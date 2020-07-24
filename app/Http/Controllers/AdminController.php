<?php

namespace App\Http\Controllers;

use App\Course;
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

        /*
        $user = User::find('5f199739ce0834782b024f42');

        $course = Course::create([
            'name' => "Test 1234",
            'code' => "ABCDE",
            'shared' => false,
            'active' => true
        ]);

        $course->owner()->associate($user);
        $course->save();

        $user1 = User::find('5f1adde3a11d6f2489c1142f');
        $user2 = User::find('5f1addeea11d6f2489c11430');
        $user3 = User::find('5f1addfea11d6f2489c11431');

        $course->participants()->save($user1);
        $course->participants()->save($user2);
        $course->participants()->save($user3);
        //$course->save();

        */

        $courses = Course::all();


        return view('admin/courses', compact('courses'));
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




}
