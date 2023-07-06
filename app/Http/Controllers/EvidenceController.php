<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Storage;
use Carbon\Carbon;
use App\Models\User;
use App\Models\File;
use App\Models\Office;
use App\Models\Evidence;
use App\Models\Directory;
use App\Models\AuditPlan;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DirectoryRepository;

class EvidenceController extends Controller
{
    private $parent = 'Evidences';
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

        return view('evidences.create', compact('directories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if(Auth::user()->role->role_name == 'Process Owner') {
            $directories = $this->dr->getDirectoriesAssignedByGrandParent($this->parent);
            $directory = $directories->where('id', $request->directory)->firstOrFail();
        }else{
            $parent_directory = Directory::where('name', $this->parent)->whereNull('parent_id')->firstOrFail();

            $user = Auth::user();
            $dir = $this->dr->makeAreaRootDirectories($user->assigned_area, $parent_directory->id);
            $year = Carbon::parse($request->date)->format('Y');
            $directory = $this->dr->getDirectory($year, $dir->id);    
        }
        
        $file_id = null;
        if ($request->hasFile('file_attachment')) {
            $now = Carbon::now();
            $file = $request->file('file_attachment');
            $hash_name = md5($file->getClientOriginalName() . uniqid());
            $target_path = sprintf('attachments/%s/%s/%s/%s', $now->year, $now->month, $now->day, $hash_name);
            $path = Storage::put($target_path, $file);
            $file_name = $request->name.".".$file->getClientOriginalExtension();

            $file = File::create([
                'directory_id' => $directory->id,
                'user_id' => $user->id,
                'file_name' => $file_name,
                'file_mime' => $file->getClientMimeType(),
                'container_path' => $path,
                'description' => $request->description,
                'type' => 'evidences'
            ]);
            $file_id = $file->id;
        }
        

        Evidence::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => $user->id,
            'directory_id' => $directory->id,
            'date' => $request->date,
            'file_id' => $file_id
        ]);

        $users = User::whereHas('role', function($q){ $q->whereIn('role_name', \FileRoles::EVIDENCES); })->get();
        \Notification::notify($users, 'Submitted Evidence');
        
        return back()->withMessage('Evidence created successfully');
    }
}
