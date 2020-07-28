<?php

namespace App\Http\Controllers;

use App\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
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
        if (auth()->user()->isAdmin() || auth()->user()->isTeacher()) {
            $myCourses = Course::where('owner_id', Auth::id())->get();
            return view('teacher/home')->with('myCourses', $myCourses);
        } else {
            return view('home-student');
        }

    }
}
