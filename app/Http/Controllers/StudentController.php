<?php

namespace App\Http\Controllers;

use App\Course;
use App\Task;
use App\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
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
     * Join a course by a given code
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function joinCourse(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'code' => 'required|string|min:3|max:10',
        ]);

        if ($validator->fails()) {
            session()->flash('error', 'Invalid code send.');
        } else {

            $course = Course::where('code', trim($request->get('code')))->first();

            if ($course) {

                if ($course->active == false) {
                    session()->flash('error', 'Course is currently not active.');
                } else {
                    $course->participants()->attach(Auth::user());
                    $course->save();

                    session()->flash('status', 'Course "' . $course->name . '" successful joined.');
                }
            } else {
                session()->flash('error', 'No course found with given code.');
            }
        }

        return Redirect::back();
    }

    /**
     * Show the topics of a course.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showCourse($id)
    {
        $course = Course::find($id);

        return view('student/course', compact('course'));
    }

    /**
     * Show the tasks of a topic.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showTopic($id)
    {
        $topic = Topic::find($id);

        // get all tasks of the topic with rating if one exists for the student
        $tasks = Task::with(array('ratings'=>function($query){
            $query->where('student_id', Auth::id());
        }))->where("topic_id", $id)->get();

        return view('student/topic', compact('topic', 'tasks'));
    }

    /**
     * Get Task Details
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTaskData($id)
    {
        $task = Task::find($id);

        if ($task) {
                unset($task->solution);

                $task->introType = $task->intro_type;
                $task->intro = \Illuminate\Support\Facades\Storage::url($task->intro);

                $task->extroType = $task->extro_type;
                $task->extro = \Illuminate\Support\Facades\Storage::url($task->extro);


                return response()->json([
                    'task' => $task,
                ], \Illuminate\Http\Response::HTTP_OK);
        }
    }
}
