<?php

namespace App\Http\Controllers;

use Storage;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


use App\Models\File;
use App\Models\User;
use App\Models\Manual;
use App\Models\Evidence;
use App\Models\FileItem;
use App\Models\Template;
use App\Models\FileUser;
use App\Models\Directory;
use App\Models\AuditReport;
use App\Models\FileHistory;
use App\Models\SurveyReport;
use App\Models\ConsolidatedAuditReport;


use App\Repositories\DirectoryRepository;

class ArchiveController extends Controller
{
    private $dr;

    public function __construct() 
    {
        $this->dr = new DirectoryRepository;
    }

    public function index(Request $request)
    {
        $data = $this->dr->getDirectoriesAndFiles(null, $request->directory);
        $data['page_title'] = 'Archives';

        return view('archives.index', $data);
    }

    public function indexBK(Request $request)
    {
        $user = Auth::user();
        $role_name = $user->role->role_name;
        $data = $this->dr->getArchiveDirectoryaAndFiles($request->directory);
        if(in_array($role_name,['Process Owner', 'Internal Auditor'])) {
            if(!empty($request->directory)) {
                $directory = Directory::find($request->directory);
                $data['directories'] = $this->dr->getDirectoriesAssignedByGrandParent($directory->name);
            }else{    
                if($role_name == 'Process Owner') {
                    $data['directories'] = Directory::whereIn('name', ['Evidences', 'Manuals'])->get();
                }

                if($role_name == 'Internal Auditor') {
                    $data['directories'] = Directory::whereIn('name', ['Evidences', 'Audit Reports'])->get();
                }
            }
            
        }

        return view('archives.index', $data);
    }

    public function directory(Request $request, $directory_name = '')
    {
        $directory_name = ucwords($directory_name);
        $current_user = Auth::user();
        $users = $current_user->role->role_name == 'Administrator' ? User::whereHas('role', function($q) { $q->where('role_name', '!=', 'Administrator'); })->get() : User::where('role_id', $current_user->role_id)->get();
        
        $directory_name = ucwords($directory_name);
        if(!in_array($directory_name, $current_user->role->directories)) {
            return abort(404);
        }else{
            $parent_directory = Directory::where('name', $directory_name)->first()->id ?? '';
        }

        $directory = Directory::where('parent_id', $parent_directory->id)
                        ->where('name', $current_user->assigned_area->area_name)->first();
        if(!$directory) {
            $directory = Directory::create([
                'parent_id' => $parent_directory->id,
                'name' =>  $current_user->assigned_area->area_name
            ]);
        }

        $files = File::where('directory_id', $directory->id)
                    ->where('user_id', $current_user->id)
                    ->get();
        
        return view('archives.index', compact('files', 'user', 'users'));
    }

    public function search(Request $request, $parent_name = '')
    {
        $keyword = $request->keyword;
        $parent_name = $parent_name == 'archives' ? null : $parent_name;
        
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $data = $this->dr->searchFilesAndDirectories($keyword, ucwords($parent_name), $date_from, $date_to);
        $data['route'] = strtolower($parent_name ?? 'archives');
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['page_title'] = $parent_name ?? 'archives';
        $data['keyword'] = $keyword;
        
        return view('archives.search', $data);
    }

    public function sharedWithMe(Request $request)
    {
        $current_user = Auth::user();
        $users = $current_user->role->role_name == 'Administrator' ? User::whereHas('role', function($q) { $q->where('role_name', '!=', 'Administrator'); })->get() : User::where('role_id', $current_user->role_id)->get();

        $fileSearch = $request->fileSearch;
        $files = File::where(function($q) use($current_user){
                    $q->where('user_id', $current_user->id)
                        ->orWhereHas('file_users', function($q2) use($current_user){
                            $q2->where('user_id', $current_user->id);
                        });
                });
        if(!empty($fileSearch)) {
            $files = $files->where('file_name', 'LIKE', "%$fileSearch%");
        }

        $files = $files->get();
        
        return view('archives.shared', compact('users', 'files', 'current_user', 'fileSearch'));
    }

    public function storeDirectory(Request $request)
    {
        $parent = Directory::findOrFail($request->parent_directory);
        if(Directory::where('name', $request->directory)
            ->where('parent_id', $request->parent_directory)
            ->exists()){
                return back()->withError('Directory Already Exists!');
        }

        $user = Auth::user();
        if(in_array($user->role->role_name, config('app.manage_archive'))) {
           $user = null;
        }

        $directory = Directory::create([
            'parent_id' => $request->parent_directory ?? null,
            'name' => $request->directory,
            'user_id' => $user->id ?? null,
            'area_id' => $parent->area_id ?? null,
        ]);

        $grand_parent = $this->dr->getGrandParent($parent);

        $users = User::whereHas('role', function($q){ $q->where('role_name', \Roles::PROCESS_OWNER); })->get();
        foreach($users as $user) {
            if($this->dr->allowedDirectory($parent, $user)) {
                \Notification::notify([$user], "Created ".ucfirst($grand_parent)." Folder");
            }
        }

        return back()->withMessage('Directory created successfully');
    }

    public function updateDirectory(Request $request, $id)
    {
        $directory = Directory::findOrFail($id);

        if(Directory::where('name', $request->directory)
            ->where('parent_id', $request->parent_directory)
            ->where('id', '!=', $id)
            ->exists()){
                return back()->withError('Directory Already Exists!');
        }

        $directory->name = $request->directory;
        $directory->save();
        
        return back()->withMessage('Directory updated successfully');
    }

    public function deleteDirectory(Request $request, $id)
    {
        $directory = Directory::findOrFail($id);
        $user = Auth::user();
        if($directory->user_id !== $user->id && $directory->area->type !== 'process') {
            return back()->withError("You don't have permission to delete the directory");
        }

        $directory->files()->delete();
        $directory->delete();
        return back()->withMessage('Directory deleted successfully');
    }

    public function storeFile(Request $request)
    {
        $user = Auth::user();
        if(File::where('file_name', $request->file_name)
            ->where('directory_id', $request->parent_directory)
            ->where('user_id', $user->id)
            ->exists()){
                return back()->withError('File Already Exists!');
        }
        
        if ($request->hasFile('file_attachments')) {
            $this->dr->storeFile($request->file_name, '', $request->file('file_attachments'), $request->parent_directory);
        }

        return back()->withMessage('File uploaded successfully');
    }


    public function updateFile(Request $request, $file_id)
    {
        $user = Auth::user();
        $file = File::where('user_id', $user->id)
                    ->where('id', $file_id)
                    ->firstOrFail();
        if($file->type !== 'manuals') {
            $fileHistory = FileHistory::create([
                'file_id' => $file->id,
                'file_name' => $file->file_name,
                'description' => $file->description,
            ]);
                        
            $file->file_name = $request->file_name;
            $file->description = $request->file_description;
            $file->save();

            if ($request->hasFile('file_attachments')) {
                FileItem::where('file_id', $file->id)
                            ->whereNull('file_history_id')
                            ->update(['file_history_id' => $fileHistory->id]);

                $this->dr->storeFileItem($file, $request->file('file_attachments'));            
            }
            

            return back()->withMessage('File updated successfully');
        }else{
            $parent_manual = Manual::where('file_id', $file->id)->firstOrFail();
            $file_id = null;
            if ($request->hasFile('file_attachments')) {
                $new_file = $this->dr->storeFile(
                            $request->file_name, 
                            $request->file_description, 
                            $request->file('file_attachments'), 
                            $file->directory_id,
                            'manual-updates'
                );
                $file_id = $new_file->id;
            }
            Manual::create([
                'parent_manual_id' => $parent_manual->id,
                'name' => $request->file_name,
                'description' => $request->file_description,
                'user_id' => $user->id,
                'directory_id' => $file->directory_id,
                'date' => $parent_manual->date,
                'file_id' => $file_id,
                'status' => 'pending-update'
            ]);
    
            $users = User::whereHas('role', function($q){ $q->where('role_name', 'Quality Assurance Director'); })->get();
            foreach($users as $user) {
                \Notification::notify([$user], 'Submitted Process Manuals Updates', route('archives-show-file', $file_id));
            }

            return back()->withMessage('File updated successfully and will be subject for approval.');
        }
    }

    public function downloadFile($id)
    {
        $file = FileItem::findOrFail($id);
        $user = Auth::user();

        $content = Storage::get($file->container_path);
        
        return response($content)->header('Content-Type', $file->file_mime);
    }

    public function showFile($file_id) {
        $file = File::where('id', $file_id)
                    ->firstOrFail();
        $files = [$file];
        
        return view('archives.files.show', compact('file', 'files'));
    }

    public function downloadFileHistory($id)
    {
        $file = FileHistory::findOrFail($id);
        $user = Auth::user();

        $content = Storage::get($file->container_path);
        
        return response()->download(
            storage_path('app/'.$file->container_path), 
            $file->file_name, 
            ['Content-Type' => $file->file_mime]
        );
    }

    public function deleteFile(Request $request, $id)
    {
        $file = File::findOrFail($id);
        $user = Auth::user();
        if($file->user_id !== $user->id) {
            return back()->withError("You don't have permission to delete the file");
        }

        if($file->type == 'manuals') {
            Manual::where('file_id', $file->id)->delete();
        }elseif($file->type == 'evidences') {
            Evidence::where('file_id', $file->id)->delete();
        }elseif($file->type == 'templates') {
            Template::where('file_id', $file->id)->delete();
        }elseif($file->type == 'audit_reports') {
            AuditReport::where('file_id', $file->id)->delete();
        }elseif($file->type == 'consolidated_audit_reports') {
            ConsolidatedAuditReport::where('file_id', $file->id)->delete();
        }elseif($file->type == 'survey_reports') {
            SurveyReport::where('file_id', $file->id)->delete();
        }

        $directory = $file->directory_id;
        $file->items()->delete();
        $file->histories()->delete();
        $file->delete();

        $url = url()->previous();
        $route = app('router')->getRoutes($url)->match(app('request')->create($url))->getName();

        if($route == 'archives-show-file') {
            return redirect()->route('archives-page', ['directory' => $directory])->withMessage('File deleted successfully');
        }
        
        return back()->withMessage('File deleted successfully');
    }

    public function shareFile(Request $request, $id)
    {
        $file = File::findOrFail($id);
        $user = Auth::user();
        if($file->user_id !== $user->id && !in_array($user->role->role_name, config('app.manage_archive'))) {
            return back()->withError("You don't have permission to share the file");
        }
        
        FileUser::where('file_id', $id)->delete(); // Remove exitsting
        if(!empty($request->userShare)) {
            foreach($request->userShare as $user) {
                FileUser::create([
                    'file_id' => $id,
                    'user_id' => $user
                ]);
            }
        }else {
           return back()->withMessage('File unshared successfully');
        }

        return back()->withMessage('File shared successfully');
    }
}
