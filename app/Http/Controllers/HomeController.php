<?php

namespace App\Http\Controllers;

use App\Course;
use App\User;
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
            $allCopyableCourses = Course::where('owner_id', Auth::id())->orWhere('shared', true)->get();
            return view('teacher/home')->with('myCourses', $myCourses)->with('allCourses', $allCopyableCourses);
        } else {
            $user = User::find(Auth::id());
            $studentCourses = $user->courses()->where('active', true)->get();
            return view('student/home')->with('myCourses', $studentCourses);
        }

    }
}
