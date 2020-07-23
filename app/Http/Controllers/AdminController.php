<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin/dashboard');
    }

    /**
     * Show the users.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function users()
    {
        $users = User::all();
        return view('admin/users', compact('users'));
    }

    /**
     * Activate a user  in admin area
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activateUser()
    {
        $user = User::find(request()->id);
        $user->active = true;
        $user->save();

        session()->flash('message', 'User ' . $user->name . ' has been activated.');

        return back();
    }

    /**
     * Deactivate a user in admin area
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivateUser()
    {
        $user = User::find(request()->id);
        $user->active = false;
        $user->save();

        session()->flash('message', 'User ' . $user->name . ' has been deactivated.');

        return back();
    }
}
