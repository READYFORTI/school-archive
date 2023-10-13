<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Storage;
use Carbon\Carbon;
use App\Constants\Roles;
use App\Constants\FileRoles;

use App\Models\User;
use App\Models\File;
use App\Models\Office;
use App\Models\Manual;
use App\Models\FileItem;
use App\Models\Directory;
use App\Models\FileHistory;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DirectoryRepository;

class ManualController extends Controller
{
    private $parent = 'Manuals';
    private $dr;

    public function __construct() 
    {
        $this->dr = new DirectoryRepository;
    }

    public function index(Request $request)
    {
        $data = $this->dr->getDirectoriesAndFiles($this->parent, $request->directory ?? null);
        
        $data['route'] = strtolower($this->parent);
        $data['page_title'] = $this->parent;

        return view('archives.index', $data);
    }

    public function create()
    {
        $directories = [];
        if(Auth::user()->role->role_name == 'Process Owner') {
            $directories = $this->dr->getDirectoriesAssignedByGrandParent($this->parent);
        }
        return view('manuals.create', compact('directories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if(Auth::user()->role->role_name == 'Process Owner') {
            $directories = $this->dr->getDirectoriesAssignedByGrandParent($this->parent, false);
            $directory = $directories->where('id', $request->directory)->firstOrFail();
        }else{
            $parent_directory = Directory::where('name', $this->parent)->whereNull('parent_id')->firstOrFail();

            $user = Auth::user();
            $dir = $this->dr->makeAreaRootDirectories($user->assigned_area, $parent_directory->id);
            $year = Carbon::parse($request->date)->format('Y');
            $directory = $this->dr->getDirectory($year, $dir->id);    
        }
        
        $file_id = null;
        if ($request->hasFile('file_attachments')) {
            $file = $this->dr->storeFile(
                        $request->name, 
                        $request->description, 
                        $request->file('file_attachments'), 
                        $directory->id, 
                        'manuals'
            );
            $file_id = $file->id;
        }

        Manual::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => $user->id,
            'directory_id' => $directory->id,
            'date' => $request->date,
            'file_id' => $file_id
        ]);

        $users = User::whereHas('role', function($q){ $q->where('role_name', Roles::QUALITY_ASSURANCE_DIRECTOR); })->get();
        \Notification::notify($users, 'Submitted Process Manuals', route('archives-show-file', $file_id));
       
        
        return back()->withMessage('Manual created successfully');
    }

    public function allManuals(Request $request)
    {
        $status = Auth::user()->role->role_name == Roles::QUALITY_ASSURANCE_DIRECTOR ? ['pending-cmt', 'pending-update-cmt'] : ['pending', 'pending-update'];
        $manuals = Manual::whereNotIn('status', $status)->get();

        return view('manuals.index', compact('manuals'));
    }

    public function pendingManuals(Request $request)
    {
        $title = 'Pending Manuals';
        $status = Auth::user()->role->role_name == Roles::QUALITY_ASSURANCE_DIRECTOR ? 'pending' : 'pending-cmt';
        $manuals = Manual::where('status', $status)->get();

        return view('manuals.index', compact('manuals', 'title'));
    }

    public function pendingUpdateManuals(Request $request)
    {
        $title = 'Manuals - Pending Updates';
        $status = Auth::user()->role->role_name == Roles::QUALITY_ASSURANCE_DIRECTOR ? 'pending-update' : 'pending-update-cmt';
        $manuals = Manual::where('status', $status)->get();


        return view('manuals.index', compact('manuals', 'title'));
    }

    public function rejectManuals(Request $request, $id)
    {
        $current_status = Auth::user()->role->role_name == Roles::QUALITY_ASSURANCE_DIRECTOR ? ['pending', 'pending-update'] : ['pending-cmt', 'pending-update-cmt'];
        $manual = Manual::whereIn('status', $current_status)->where('id', $id)->firstOrFail();
        $manual->status = 'rejected';
        $manual->save();

        $file_id = $manual->file_id;
        $user = User::where('id', $manual->user_id)->get();
        \Notification::notify($user, 'Rejected Process Manual', route('archives-show-file', $file_id));

        return redirect()->back()->with('message', 'You have successfully rejected manual');
    }

    public function approveManuals(Request $request, $id)
    {
        $current_status = Auth::user()->role->role_name == Roles::QUALITY_ASSURANCE_DIRECTOR ? ['pending', 'pending-update'] : ['pending-cmt', 'pending-update-cmt'];
        $manual = Manual::whereIn('status', $current_status)->where('id', $id)->firstOrFail();

        if(!empty($manual->parent_manual_id)) {// Update the parent manual with child
            $parent_manual = Manual::findOrFail($manual->parent_manual_id);
            $parent_file = File::findOrFail($parent_manual->file_id);
            $status = Auth::user()->role->role_name == Roles::QUALITY_ASSURANCE_DIRECTOR ? 'pending-update-cmt' : 'approved';
        }else{
            $status = Auth::user()->role->role_name == Roles::QUALITY_ASSURANCE_DIRECTOR ? 'pending-cmt' : 'approved';
        }

        $manual->status = $status;
        $manual->save();

        $file_id = $manual->file_id;
        if($status == 'approved') {
            if(!empty($parent_manual)) {// Update the parent manual with child
                // Create File History for Manual
                $fileHistory = FileHistory::create([
                    'file_id' => $parent_manual->file_id,
                    'file_name' => $parent_manual->name,
                    'description' => $parent_manual->description,
                ]);
                
                FileItem::where('file_id', $parent_manual->file_id)
                            ->whereNull('file_history_id')
                            ->update(['file_history_id' => $fileHistory->id]);

                // Update Manual Updates File Items
                FileItem::where('file_id', $manual->file_id)
                            ->whereNull('file_history_id')
                            ->update(['file_id' => $parent_manual->file_id]);
                
                $parent_manual->name = $manual->name;
                $parent_manual->description = $manual->description;
                $parent_manual->save();

                $parent_file->file_name = $manual->name;
                $parent_file->description = $manual->description;
                $parent_file->save();

                // Send notification to user for approval
                $users = User::whereHas('role', function($q){ $q->where('role_name', FileRoles::MANUALS); })
                            ->orWhere('id', $manual->user_id)->get();
                            \Notification::notify($users, 'Approved Process Manual Updates', route('archives-show-file', $parent_manual->file_id));

            }else{
                // Send notification to user for approval
                $users = User::whereHas('role', function($q){ $q->where('role_name', FileRoles::MANUALS); })
                            ->orWhere('id', $manual->user_id)->get();
                \Notification::notify($users, 'Approved Process Manual', route('archives-show-file', $file_id));
            }
        }else{
            // Send notification to CMT Users for approval
            $users = User::whereHas('role', function($q){ $q->where('role_name', Roles::COLLEGE_MANAGEMENT_TEAM); })->get();
            \Notification::notify($users, 'Pre-approved Process Manual', route('archives-show-file', $file_id));
        }

        return redirect()->back()->with('message', 'You have successfully approved manual');
    }
}