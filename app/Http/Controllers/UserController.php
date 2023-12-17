<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerification;
use App\Models\AuditPlanFile;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Ramsey\Uuid\Uuid;

use App\Models\Area;
use App\Models\Role;
use App\Models\User;
use App\Models\File;
use App\Models\FileRemark;
use App\Models\ProcessUser;
use App\Models\Notification;
use App\Models\Announcement;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function dashboard()
    {
        $announcements = Announcement::latest()->get();
        $user_type = Auth::user()->role->role_name;
        if(in_array(Auth::user()->role->role_name, ['Administrator', 'Human Resources'])){
            $users = User::get();
        }elseif(Auth::user()->role->role_name == 'Internal Lead Auditor'){
            $users = User::whereHas('role', function($q) { $q->where('role_name', 'Internal Auditor'); })->get();
            $user_type = 'Internal Auditors';
        }else{
            $users = User::where('role_id', Auth::user()->role_id)->get();
        }
        $data = [
            'files' => File::where('user_id', Auth::user()->id)->count(),
            'users' => $users,
            'user_type' => Str::plural($user_type),
            'notifications' => Auth::user()->notifications,
            'announcements' => $announcements,
            'audit_file' => AuditPlanFile::query()->orderByDesc('audit_plan_files.id')
            ->select('name','date','audit_plans.id')
            ->join('audit_plans','audit_plans.id','audit_plan_files.audit_plan_id')
            ->get()
        ];

        return view('user.dashboard', $data);
    }

    public function __construct()
    {
        $this->middleware('auth')->except(['create','store']);
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('register');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'firstname'=>['required','max:255'],
            'middlename'=>['nullable','max:255'],
            'surname'=>['required','max:255'],
            'suffix'=>['nullable','max:255'],
            'username'=>['required','email','max:255','unique:users,username'],
            'password'=>['required','confirmed','max:255', Password::min(8)->mixedCase()->numbers()->symbols()],
            'img'=>['nullable', 'file','mimes:jpg,jpeg,png','max:10000']
        ]);
        $validatedData['password'] = Hash::make($validatedData['password']);
        $file_name = Uuid::uuid4()->toString();

        if($request->hasFile('img')) {
            $path = Storage::putFileAs('public/profiles',$request->file('img'),$file_name.'.'.$request->file('img')->extension());
            $validatedData['img'] = $path;
        }

        $user = User::create($validatedData);
        $code = Uuid::uuid4()->toString();

        Otp::insert([
            'user_id'=>$user->id,
            'code'=>$code,
            'created'=>time(),
            'expiration'=>time()+300
        ]);

        Mail::send(new EmailVerification($validatedData['username'],$code));
        return redirect()->route('login-page')->with('success', 'Account has been registered successfully');
    }

        public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $originalData = $user->toArray(); // Get the original user data

        $validatedData = $request->validate([
            'firstname' => ['required', 'max:255'],
            'middlename' => ['nullable', 'max:255'],
            'surname' => ['required', 'max:255'],
            'suffix' => ['nullable', 'max:255'],
            'username' => ['required', 'max:255', 'unique:users,username,' . $id],
            'password' => ['nullable', 'confirmed', 'max:255'],
            'img' => ['nullable', 'image', 'max:10000'],
        ]);

        // Check if a new password is provided in the request
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $user->update($validatedData);

        // Calculate the difference between two arrays
        $dataDiff = array_diff_assoc($validatedData, $originalData);

        // Check if there are any differences
        if (!empty($dataDiff)) {
            // Data has been updated
            $message = 'Updated Successfully';
        } else {
            // No data changes
            $message = 'No data changes were made';
        }

        // Handle image upload separately, similar to the previous example
        $imgPath = $this->handleImageUpload($request);

        if ($imgPath) {
            $user->img = $imgPath;
            $user->save();
        }

        return redirect()->route('users.update', $user->id)->with('success', $message);
    }









   











    
   
    


    public function handleImageUpload(Request $request)
{
    if ($request->hasFile('img')) {
        $file = $request->file('img');
        $fileName = Uuid::uuid4()->toString() . '.' . $file->extension();
        $filePath = $file->storeAs('public/profiles', $fileName);
        return $filePath;
    }
    
    return null; // No file was uploaded
}



    public function show($id)
{
    // Retrieve the user by ID from the database
    $user = User::find($id);

    // Check if the user was found
    if (!$user) {
        return redirect()->route('login-page')->with('error', 'User not found');
    }

    // If the user was found, return a view with the user's details
    return view('user.show', compact('user'));
}

public function pshow($id)
{
    // Retrieve the user by ID from the database
    $user = User::find($id);

    // Check if the user was found
    if (!$user) {
        return redirect()->route('login-page')->with('error', 'User not found');
    }

    // If the user was found, return a view with the user's details
    return view('user.pshow', compact('user'));
}







    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function saveRemarks(Request $request, $file_id)
    {
        $file = File::findOrFail($file_id);

        if(!in_array(Auth::user()->role->role_name, [
            'Staff',
            'Internal Lead Auditor',
            'Internal Auditor',
            'Document Control Custodian',
            'College Management Team',
            'Quality Assurance Director'
        ])){
            return redirect()->back()->with('error', 'You are not authorized');
        }

        FileRemark::updateOrCreate(
            ['file_id' => $file_id, 'user_id' => Auth::user()->id],
            ['type' => $request->type, 'comments' => $request->comments]
        );

        if($file->user_id !== Auth::user()->id) {
            $user = User::find($file->user_id);
            \Notification::notify($user, 'Submitted Remarks', route('archives-show-file', $file_id));
        }
        
        return redirect()->back()->with('success', 'Your remarks has been saved successfully');
    }

    public function notifications()
    {
        $notifications = Auth::user()->notifications;
        $notifications->markAsRead();
        return view('user.notifications', compact('notifications'));
    }
}