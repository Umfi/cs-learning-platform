<?php

namespace App\Http\Controllers;

use App\Course;
use App\Topic;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
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
     * Get course Details
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCourseData($id)
    {
        $course = Course::find($id);

        if ($course) {
            if ($course->owner_id == Auth::id()) {
                return response()->json([
                    'course' => $course,
                ], \Illuminate\Http\Response::HTTP_OK);
            }
        }

        return response()->json([
            'course' => null,
        ], \Illuminate\Http\Response::HTTP_OK);
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

    /**
     * Edit a course
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editCourse(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => 'required',
            'name' => 'required|string|min:3|max:255',
        ]);

        if ($validator->fails()) {
            session()->flash('error', 'Course "' . $request->get('name') . '" has not been updated. Invalid data send.');
        } else {
            $user = User::find(Auth::id());

            if ($user) {

                $course = Course::find($request->get('id'));

                if ($course) {

                    if ($course->owner_id == Auth::id()) {
                        $course->name = $request->get('name');
                        $course->shared = $request->get('shared') == "1";
                        $course->active = $request->get('active') == "1";
                        $course->save();

                        session()->flash('status', 'Course "' . $request->get('name') . '" has been updated.');

                    } else {
                        session()->flash('error', 'Course "' . $request->get('name') . '" has not been updated. Not authorized.');
                    }
                }
            } else {
                session()->flash('error', 'Course "' . $request->get('name') . '" has not been updated.');
            }
        }

        return Redirect::back();
    }


    /**
     * Show the topics.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showCourse($id)
    {
        $course = Course::find($id);

        return view('teacher/course', compact('course'));
    }

    /**
     * Get course Details
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTopicData($id)
    {
        $topic = Topic::find($id);

        if ($topic) {
            if ($topic->course->owner_id == Auth::id()) {
                return response()->json([
                    'topic' => $topic,
                ], \Illuminate\Http\Response::HTTP_OK);
            }
        }

        return response()->json([
            'topic' => null,
        ], \Illuminate\Http\Response::HTTP_OK);
    }

    /**
     * Create a new topic
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createTopic(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'course_id' => 'required',
            'name' => 'required|string|min:3|max:255',
            'description' => 'string|max:2048',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            session()->flash('error', 'Topic "' . $request->get('name') . '" has not been created. Invalid data.');
        } else {
            $course = Course::find($request->get('course_id'));

            if ($course) {
                if ($course->owner_id == Auth::id()) {

                    if ($request->hasFile('image')) {

                        $file = request()->file('image');
                        $fileName = time() . '.' . $file->getClientOriginalExtension();

                        Storage::disk('public')->put('uploads/' . $fileName, file_get_contents($file));

                        $image =  Storage::url('uploads/' . $fileName);
                    } else {
                        $image = "";
                    }

                    $topic = Topic::create([
                        'name' => $request->get('name'),
                        'description' => $request->get('description'),
                        'image' => $image,
                        'active' => $request->get('active') == "1"
                    ]);
                    $topic->course()->associate($course);
                    $topic->save();

                    session()->flash('status', 'Topic "' . $request->get('name') . '" has been created.');

                } else {
                    session()->flash('error', 'Topic "' . $request->get('name') . '" has not been created. Not authorized.');
                }
            } else {
                session()->flash('error', 'Topic "' . $request->get('name') . '" has not been created.');
            }
        }

        return Redirect::back();
    }

    /**
     * Edit a topic
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editTopic(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => 'required',
            'name' => 'required|string|min:3|max:255',
            'description' => 'string|max:2048',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            session()->flash('error', 'Topic "' . $request->get('name') . '" has not been updated. Invalid data send.');
        } else {

            $topic = Topic::find($request->get('id'));

            if ($topic) {

                if ($topic->course->owner_id == Auth::id()) {
                    $topic->name = $request->get('name');
                    $topic->description = $request->get('description');

                    if ($request->hasFile('image')) {

                        $file = request()->file('image');
                        $fileName = time() . '.' . $file->getClientOriginalExtension();

                        Storage::disk('public')->put('uploads/' . $fileName, file_get_contents($file));

                        $topic->image = Storage::url('uploads/' . $fileName);
                    }

                    $topic->active = $request->get('active') == "1";
                    $topic->save();

                    session()->flash('status', 'Topic "' . $request->get('name') . '" has been updated.');
                } else {
                    session()->flash('error', 'Topic "' . $request->get('name') . '" has not been updated. Not authorized.');
                }
            } else {
                session()->flash('error', 'Topic "' . $request->get('name') . '" has not been updated.');
            }
        }

        return Redirect::back();
    }


}
