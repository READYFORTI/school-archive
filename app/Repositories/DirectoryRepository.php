<?php

namespace App\Repositories;

use Storage;
use Carbon\Carbon;
use App\Models\Area;
use App\Models\File;
use App\Models\User;
use App\Models\FileItem;
use App\Models\FileUser;
use App\Models\Directory;
use App\Models\AuditPlan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class DirectoryRepository {

    public function getDirectoriesAndFiles($grand_parent = null, $directory_id = null)
    {
        $files = [];
        $parents = [];
        $directories = [];
        $current_directory = [];
        $users = !empty($user_id) ? User::get() : [];
        $current_user = Auth::user();
        $grand_parents = !empty($grand_parent) ? [$grand_parent] : $current_user->role->directories;
        $role = $current_user->role->role_name;
        
        if(!empty($grand_parent)) {
            $directories = Directory::whereHas('parent', function($q) use($grand_parent){
                $q->where('name', $grand_parent);
            })->get();
        }else{
            $directories = Directory::where('parent_id', null)->whereIn('name', $grand_parents)->get();
        }
        if($directory_id) {
            $current_directory = Directory::where('id', $directory_id)->firstOrFail();
            if(!in_array($this->getGrandParent($current_directory), $grand_parents)) {
                return abort(404);
            }

            $parents = $this->getRootDirectories($current_directory);
            krsort($parents);
            $parents[] = $current_directory->toArray();

            $directories = Directory::where('parent_id', $directory_id)->get();
            $files = $this->getFiles($current_user, $current_directory);
        }

        // Check if directories are assign or parent of assigned
        $directories = $directories->filter(function ($directory) use($grand_parents, $current_user) {
            return in_array($this->getGrandParent($directory), $grand_parents) && $this->allowedDirectory($directory, $current_user);
        });

        // dd(implode(',', collect($directories)->pluck('name')->toArray()));

        return compact('users', 'directories', 'current_directory', 'files', 'parents', 'current_user');
    }

    public function searchFilesAndDirectories($keyword = '', $grand_parent = null, $date_from = null, $date_to = null) {
        $files = [];
        $directories = [];
        $current_user = Auth::user();
        $role = $current_user->role->role_name;
        
        $grand_parents = !empty($grand_parent) ? [$grand_parent] : $current_user->role->directories;
        $directories = Directory::where('parent_id', '!=', null)
                        ->where('name', 'LIKE', "$keyword%")
                        ->where(function($q) use($date_from, $date_to) {
                            if(!empty($date_from) && !empty($date_to)) {
                                $q->whereBetween('created_at', [$date_from, $date_to]);
                            }
                        })->get();

        $directories = $directories->filter(function ($directory) use($grand_parents, $current_user) {
            return in_array($this->getGrandParent($directory), $grand_parents) && $this->allowedDirectory($directory, $current_user);
        });

        $files = $this->getFiles($current_user, null, $keyword);
        $files = $files->filter(function ($file) use($grand_parents, $current_user) {
            return in_array($this->getGrandParent($file->directory), $grand_parents) && $this->allowedDirectory($file->directory, $current_user);
        });

        return compact('directories', 'files');
    }

    public function searchFiles($keyword = '', $grand_parent = null, $date_from = null, $date_to = null) {
        $files = [];
        $directories = [];
        $current_user = Auth::user();
        $role = $current_user->role->role_name;
        
        $grand_parents = !empty($grand_parent) ? [$grand_parent] : $current_user->role->directories;
        $files = $this->getFiles($current_user, null, $keyword, $date_from, $date_to);
        if(!empty($date_from) && !empty($date_to)) {
            $files = $files->whereBetween('created_at', [$date_from, $date_to]);
        }
        $files = $files->filter(function ($file) use($grand_parents, $current_user) {
            return in_array($this->getGrandParent($file->directory), $grand_parents) && $this->allowedDirectory($file->directory, $current_user);
        });

        return compact('files');
    }

    public function getFiles($current_user, $current_directory = null, $keyword = null) {
        $role_file_access = [
            'Internal Auditor',
            'Internal Lead Auditor', 
            'Document Control Custodian',
            'College Management Team',
            'Quality Assurance Director'
        ];
        $files = File::with('directory')->where(function($q) use($keyword, $current_user, $current_directory, $role_file_access){
            if(!empty($keyword)) {
                $q->where('file_name','LIKE',"$keyword%");
            }
            if(!empty($current_directory)) {
                $q->where('directory_id', $current_directory->id);
            }
            if(!in_array($current_user->role->role_name, $role_file_access)) {
                $q->where(function($q) use($current_user, $current_directory) {
                    $q->where('user_id', $current_user->id);
                    if($current_user->role->role_name !== 'Staff') {
                        $q->orWhere('type', 'templates');
                    }
                });
            }
            
            $route = Route::getFacadeRoot()->current()->uri() ?? '';
            if($current_user->role->role_name == 'Internal Auditor' && $route == 'archives') {
                $q->where(function($q) use($current_user, $current_directory) {
                    $q->where('user_id', $current_user->id)
                    ->orWhereHas('remarks', function($q2) use($current_user) {
                        $q2->where('user_id', $current_user->id);
                    })->orWhere('type', 'templates');
                });
            }
        })->get();

        return $files;
    }

    public function allowedDirectory($directory, $current_user)
    {
        $allowed = true;
        $route = Route::getFacadeRoot()->current()->uri() ?? '';
        if(
            in_array($current_user->role->role_name, config('app.role_with_assigned_area'))
            || ($current_user->role->role_name == 'Internal Auditor' && (in_array($route, ['archives', 'evidences']) && (empty($directory->parent_id) && $directory->name !== 'Audit Reports')))
        ) {
            $assigned_areas = $current_user->assigned_areas->pluck('id')->toArray();
            if(!in_array($directory->area_id, $assigned_areas)) {
                $allowed = false;
                // Check from each child
                $this->getDirectoryChildBranches($directory, $assigned_areas, $allowed);

                if(!$allowed) {
                    $root_directories = $this->getRootDirectories($directory);
                    
                    foreach($root_directories as $root) {
                        if(in_array($root['area_id'], $assigned_areas)) {
                            $allowed = true;
                            break;
                        }
                    }
                }
            }
        }

        

        return $allowed;
    }


    public function allowedDirectoryForNotifications($directory, $current_user)
    {
        $allowed = true;
        $route = Route::getFacadeRoot()->current()->uri() ?? '';
        if(
            in_array($current_user->role->role_name, config('app.role_with_assigned_area'))
            || $current_user->role->role_name == 'Internal Auditor'
        ) {
            $assigned_areas = $current_user->assigned_areas->pluck('id')->toArray();
            if(!in_array($directory->area_id, $assigned_areas)) {
                $allowed = false;
                // Check from each child
                $this->getDirectoryChildBranches($directory, $assigned_areas, $allowed);

                if(!$allowed) {
                    $root_directories = $this->getRootDirectories($directory);
                    
                    foreach($root_directories as $root) {
                        if(in_array($root['area_id'], $assigned_areas)) {
                            $allowed = true;
                            break;
                        }
                    }
                }
            }
        }

        

        return $allowed;
    }
    
    public function getDirectoryChildBranches($directory, $assigned_areas = null, &$is_allowed = null)
    {
        $directories = [];
        if(!empty($directory->children)) {
            foreach($directory->children as $child) {
                if(!empty($child->children)) {
                    $child['branches'] = $this->getDirectoryChildBranches($child, $assigned_areas, $is_allowed);
                }
                if(!empty($assigned_areas) && in_array($child->area_id, $assigned_areas)) {
                    $is_allowed = true;
                }
                $directories[] = $child;
            }
        }
        return $directories;
    }

    public function getRootDirectories($directory)
    {
        $directories = [];
        if(!empty($directory->parent)) {
            $directories = [$directory->parent->toArray()];
            $directories = array_merge($directories, $this->getRootDirectories($directory->parent));
        }

        return $directories;
    }

    public function getDirectory($name, $parent_id = null, $area_id = null)
    {
        return Directory::firstOrcreate([
            'name' =>  $name,
            'parent_id' => $parent_id ?? null,
            'area_id' => $area_id ?? null
        ]);
    }

    public function getAreaTree($area)
    {
        $areas = $this->getParentArea($area);
        krsort($areas);

        return $areas;
    }

    public function getParentArea($area)
    {
        $areas = [];
        if(!empty($area->parent)) {
            $areas = [$area->parent->toArray()];
            $areas = array_merge($areas, $this->getParentArea($area->parent));
        }

        return $areas;
    }

    public function getGrandParentDirectory($directory)
    {
        if(!empty($directory->parent)) {
            return $this->getGrandParentDirectory($directory->parent);
        }else{
            return $directory;
        }
    }

    public function makeAreaRootDirectories($area, $parent_directory)
    {
        $parents = $this->getAreaTree($area);
        $last_parent = $parent_directory;
        foreach($parents as $parent) {
            $parent = $this->getDirectory($parent['area_name'], $last_parent, $parent['id']);
            $last_parent = $parent['id'];
        }

        return $this->getDirectory($area->area_name, $last_parent, $area->id);
    }

    public function getGrandParent($directory)
    {
        if(!empty($directory->parent)) {
            return $this->getGrandParent($directory->parent);
        }else{
            return $directory->name ?? '';
        }
    }

    public function getAreaFamilyTree($areas = null, $selectable_type = null, $selected_areas = []) {
        $areas = empty($areas) ? Area::whereNull('parent_area')->get() : $areas;
        return $this->getAreaGrandTree($areas, $selectable_type, $selected_areas);
    }

    private function getAreaGrandTree($areas, $selectable_type, $selected_areas)
    {
        $tree_areas = [];
        foreach($areas as $area) {
            $selectable =  !empty($area->parent_area);
            if(!empty($selectable_type)) {
               $selectable = $selectable_type == $area->type;
            }
            $tree_area = [
                'id' => $area->id,
                'text' => $area->area_name,
                'selectable' => $selectable,
                'state' => [
                    'selected' => in_array($area->id, $selected_areas),
                    'expanded' => in_array($area->id, $selected_areas),
                ]
            ];
            if(count($area->children) > 0) {
                $tree_area['nodes'] = $this->getAreaFamilyTree($area->children, $selectable_type, $selected_areas);
            }
            $tree_areas[] = $tree_area;
        }

        return $tree_areas;
    }

    public function getDirectoryByAreaAndGrandParent($area_id, $grandParent) {
        $directories = Directory::where('area_id', $area_id)->get();
        $directory = null;
        foreach($directories as $key => $dir) {
            $dir->grand_parent = $this->getGrandParent($dir);
            if($this->getGrandParent($dir) == $grandParent) {
                $directory = $dir;
            }
        }
        return $directory;
    }

    public function getDirectoriesAssignedByGrandParent($grand_parent_name)
    {
        if(in_array(Auth::user()->role->role_name, config('app.role_with_assigned_area'))) {
            $directories = Directory::whereIn('area_id', Auth::user()->assigned_areas->pluck('id'))->get();
            if(Auth::user()->role->role_name == 'Internal Auditor') {
                $audit_plan_directories = AuditPlan::whereHas('users', function($q){
                    $q->where('user_id',  Auth::user()->id);
                })->pluck('directory_id');
                $directories = $directories->merge(Directory::whereIn('id', $audit_plan_directories)->get());
            }
        }else{
            $directories = Directory::get();
        }
        
        foreach($directories as $key => $directory) {
            $directory->grand_parent = $this->getGrandParent($directory);
        }
        $directories = $directories->where('grand_parent', $grand_parent_name);
        return Directory::whereIn('parent_id', $directories->pluck('id'))->get();
    }

    public function storeFile($name, $description, $files, $parent_directory, $type)
    {
        $file = File::create([
            'directory_id' => $parent_directory ?? null,
            'user_id' => Auth::user()->id,
            'file_name' => $name,
            'description' => $description,
            'type' => $type
        ]);

        $this->storeFileItem($file, $files);
       
        return $file;
    }

    public function storeFileItem($file, $files)
    {
        foreach($files as $file_item) {
            $now = Carbon::now();
            $hash_name = md5($file_item->getClientOriginalName() . uniqid());
            $target_path = sprintf('attachments/%s/%s/%s/%s/%s', Auth::user()->id, $now->year, $now->month, $now->day, $hash_name);
            $path = Storage::put($target_path, $file_item);
            $file_name = $file_item->getClientOriginalName();

            FileItem::create([
                'file_id' => $file->id,
                'file_name' => $file_name,
                'file_mime' => $file_item->getClientMimeType(),
                'container_path' => $path
            ]);
        }
    }
}