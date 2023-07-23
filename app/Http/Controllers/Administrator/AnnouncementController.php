<?php

namespace App\Http\Controllers\Administrator;

use App\Models\User;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AnnouncementController extends Controller
{
    //
    public function index()
    {
        $announcements = Announcement::get();
        return view('administrators.announcements.index', compact('announcements'));
    }

    public function create(Request $request)
    {
        return view('administrators.announcements.create');
    }

    public function store(Request $request)
    {
        Announcement::create([
            'name' => $request->name,
            'date' => $request->date,
            'description' => $request->description,
        ]);
        
        $users = User::whereHas('role', function($q){ $q->where('role_name', '!=', 'Quality Assurance Director');})->get();
        \Notification::notify($users, 'post an announcement', route('user.dashboard'));

        return redirect()->back()->with('message', 'You have successfully created announcement');
    }

    public function edit(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);
        return view('administrators.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, $id)
    {
        
        $announcement = Announcement::findOrFail($id);
        $announcement->name = $request->name;
        $announcement->date = $request->date;
        $announcement->description = $request->description;
        $announcement->save();

        return redirect()->back()->with('message', 'You have successfully updated the announcement');
    }

    public function delete(Request $request, $id)
    {
        
        $announcement = announcement::findOrFail($id);
        $announcement->delete();

        return redirect()->back()->with('message', 'You have successfully removed the announcement');
    }
}
