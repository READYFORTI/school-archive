<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Storage;
use Carbon\Carbon;
use App\Models\Area;
use App\Models\Role;
use App\Models\User;
use App\Models\File;
use App\Models\Template;
use App\Models\Directory;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DirectoryRepository;

class TemplateController extends Controller
{
    private $parent = 'Templates';
    private $dr;

    public function __construct() 
    {
        $this->dr = new DirectoryRepository;
    }

    public function index(Request $request, $directory_name = '')
    {
        $user = Auth::user();
        if(in_array($user->role->role_name, ['Quality Assurance Director', 'Staff']) || !empty($request->directory)) {
            $data = $this->dr->getDirectoriesAndFiles($this->parent, $request->directory ?? null);
        }else{
            $template_dir = $this->dr->getDirectory('Templates');
            $directory = $this->dr->getDirectory($user->role->role_name, $template_dir->id);
            $data = $this->dr->getDirectoriesAndFiles($this->parent, $directory->id ?? null);
        }
        
        $data['route'] = strtolower($this->parent);
        $data['page_title'] = $this->parent;
        
        return view('archives.index', $data);
    }

    public function create()
    {
        $roles = Role::get();

        $tree_process = $this->dr->getAreaFamilyTree(null, 'process');
        $areas = Area::whereIn('type', ['institute', 'office'])->get()->groupBy('parent.area_name');

        return view('templates.create', compact('tree_process', 'areas', 'roles'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $dir = Directory::findOrFail($request->current_directory);
        $year = Carbon::parse($request->date)->format('Y') ?? date('Y');
        $directory = $this->dr->getDirectory($year, $dir->id);  

        $file_id = null;
        if ($request->hasFile('file_attachments')) {
            $file = $this->dr->storeFile(
                        $request->name, 
                        $request->description, 
                        $request->file('file_attachments'), 
                        $directory->id, 
                        'templates'
            );
            $file_id = $file->id;
        }

        Template::create([
            'name' => $request->name,
            'description' => $request->description ?? '',
            'user_id' => $user->id,
            'date' => $request->date,
            'file_id' => $file->id ?? null
        ]);

        
        return back()->withMessage('Template created successfully');
    }
}
