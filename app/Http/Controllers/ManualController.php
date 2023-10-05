<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Storage;
use Carbon\Carbon;
use App\Models\User;
use App\Models\File;
use App\Models\Office;
use App\Models\Manual;
use App\Models\Directory;
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
            dd($directories);
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

        $users = User::whereHas('role', function($q){ $q->whereIn('role_name', \FileRoles::MANUALS); })->get();
        foreach($users as $user) {
            if($this->dr->allowedDirectory($directory, $user)) {
                \Notification::notify([$user], 'Submitted Process Manuals', route('archives-show-file', $file_id));
            }
        }

        
        return back()->withMessage('Manual created successfully');
    }
}
