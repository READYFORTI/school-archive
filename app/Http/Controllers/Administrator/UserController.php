<?php

namespace App\Http\Controllers\Administrator;

use App\Models\Area;
use App\Models\User;
use App\Models\Role;
use App\Models\AreaUser;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Repositories\DirectoryRepository;


class UserController extends Controller
{
    //
    private $dr;

    public function __construct() 
    {
        $this->dr = new DirectoryRepository;
    }

    public function index(Request $request)
    {
        $roles = Role::get();
        $request_role = $request->role ?? '';
        $users = User::withTrashed()->with('role');
        if(!empty($request_role)) {
            $users = $users->whereHas('role', function($q) use($request_role){
                $q->where('role_name', $request_role);
            });
        }
        $users = $users->get();

        return view('administrators.user', compact('users', 'roles' ,'request_role'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User has been disabled successfully');
    }

    public function enable($id)
    {
        $user = User::withTrashed()->findOrFail($id)->restore();

        return redirect()->back()->with('success', 'User has been enabled successfully');
    }

    public function pending()
    {
        $data = User::whereNull('role_id')->get();
        return view('administrators.pending',[
            'data' => $data,
            'data2'=>Role::get()
        ]);
    }

    public function approve(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::where('id',$request->user_id)
        ->withTrashed()
        ->update([
            'role_id'=>$request->role_id,
            'deleted_at'=>null
        ]);

        $user = User::find($request->user_id);
        \Notification::notify($user, 'Approved your account');

        return redirect(URL::previous())->with('success', 'User approved successfully');
    }

    public function rejected()
    {
        $data = User::onlyTrashed()->get();
        return view('administrators.rejected',[
            'data' => $data,
            'data2'=>Role::get()
        ]);
    }
    

    public function assignUserList()
    {
        $all_areas = Area::get();
        $main_areas = Area::with(['children'])->whereNull('parent_area')->get();
        
        $areas = Area::with(['children'])->get();
        $data = User::query()
                ->whereHas('role', function($q){
                    $q->whereIn('role_name', ['Process Owner', 'Document Control Custodian']);
                })
                ->join('roles','roles.id','users.role_id')
                ->select('users.*','roles.role_name')
                ->get();

        $tree_areas = $this->dr->getAreaFamilyTree(null, 'process');
                
        return view('administrators.assign', compact('data', 'areas', 'main_areas', 'tree_areas'));
    }

    public function assignUser(Request $request)
    {
        $area_id = $request->assign_area;
        $area = Area::findOrFail($area_id);
        AreaUser::where('user_id', $request->user_id)->delete();
        
        AreaUser::firstOrCreate([
            'user_id' => $request->user_id,
            'area_id' => $area->id,
        ]);

        $user = User::find($request->user_id);
        \Notification::notify($user, 'Assigned you to area '.$area->area_name, route('user.dashboard'));

        return redirect(URL::previous())->with('success', 'User has been assigned successfully');
    }

    public function assignPOUser(Request $request)
    {
        $selected_areas = explode(',',$request->areas);
        $areas = Area::whereIn('id', $selected_areas)->where('type', 'process')->get();
        AreaUser::where('user_id', $request->user_id)->delete();
        
        foreach($areas as $area) {
            AreaUser::firstOrCreate([
                'user_id' => $request->user_id,
                'area_id' => $area->id,
            ]);
            $user = User::find($request->user_id);
            \Notification::notify($user, 'Assigned you to area '.$area->area_name);    
        }

        return redirect(URL::previous())->with('success', 'User has been assigned successfully')->with('role', 'Process Owner');
    }
    
}
