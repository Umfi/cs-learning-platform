<?php

namespace App\Http\Controllers;

use App\Course;
use App\Topic;
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

        session()->flash('status', 'User "' . $user->name . '" has been activated.');

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

        session()->flash('status', 'User "' . $user->name . '" has been deactivated.');

        return back();
    }

    /**
     * Change a user role in admin area
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeRoleForUser()
    {
        $user = User::find(request()->id);
        $user->role = request()->role;
        $user->save();

        session()->flash('status', 'User "' . $user->name . '" role has been updated to ' . $user->role . '.');

        return back();
    }

    /**
     * Show the courses.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function courses()
    {
        $courses = Course::all();

        return view('admin/courses', compact('courses'));
    }

    /**
     * Activate a course in admin area
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activateCourse()
    {
        $course = Course::find(request()->id);
        $course->active = true;
        $course->save();

        session()->flash('status', 'Course "' . $course->name . '" has been activated.');

        return back();
    }

    /**
     * Deactivate a curse in admin area
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivateCourse()
    {
        $course = Course::find(request()->id);
        $course->active = false;
        $course->save();

        session()->flash('status', 'Course "' . $course->name . '" has been deactivated.');

        return back();
    }

    /**
     * Get the course participants to render in modal
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCourseParticipants(Request $request, $id)
    {
        $course = Course::find($id);

        return response()->json([
            'participants' => $course->participants,
        ], \Illuminate\Http\Response::HTTP_OK);
    }


    /**
     * Show the topics.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function topics()
    {
        $topics = Topic::all();

        return view('admin/topics', compact('topics'));
    }

    /**
     * Activate a topic in admin area
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activateTopic()
    {
        $topic = Topic::find(request()->id);
        $topic->active = true;
        $topic->save();

        session()->flash('status', 'Topic "' . $topic->name . '" has been activated.');

        return back();
    }

    /**
     * Deactivate a topic in admin area
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivateTopic()
    {
        $topic = Topic::find(request()->id);
        $topic->active = false;
        $topic->save();

        session()->flash('status', 'Topic "' . $topic->name . '" has been deactivated.');

        return back();
    }


}
