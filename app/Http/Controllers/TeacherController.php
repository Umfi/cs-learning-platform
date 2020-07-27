<?php

namespace App\Http\Controllers;

use App\Course;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TeacherController extends Controller
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
     * Create a new course
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createCourse(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|min:3|max:255',
        ]);

        if ($validator->fails()) {
            session()->flash('error', 'Course "' . $request->get('name') . '" has not been created. Invalid name.');
        } else {
            $user = User::find(Auth::id());

            if ($user) {
                $course = Course::create([
                    'name' => $request->get('name'),
                    'code' => Str::random(6),
                    'shared' => $request->get('shared') == "1",
                    'active' => $request->get('active') == "1"
                ]);
                $course->owner()->associate($user);
                $course->save();

                session()->flash('status', 'Course "' . $request->get('name') . '" has been created.');
            } else {
                session()->flash('error', 'Course "' . $request->get('name') . '" has not been created.');
            }
        }

        return Redirect::back();
    }


}
