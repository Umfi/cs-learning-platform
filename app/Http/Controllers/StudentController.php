<?php

namespace App\Http\Controllers;

use App\Course;
use App\Rating;
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
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|min:3|max:10',
        ]);

        if ($validator->fails()) {
            session()->flash('error', __('Invalid code send.'));
        } else {

            $course = Course::where('code', trim($request->get('code')))->first();

            if ($course) {

                if ($course->active == false) {
                    session()->flash('error', __('Course is currently not active.'));
                } else {
                    $course->participants()->attach(Auth::user());
                    $course->save();

                    session()->flash('status', __('Course successful joined.'));
                }
            } else {
                session()->flash('error', __('No course found with given code.'));
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

        if (!isset($topic->learningpath)) {
            $currentTask = null;
            $remainingTasks = null;
            return view('student/topic', compact('topic', 'currentTask', 'remainingTasks'));
        } else {
            $currentTask = Task::getCurrentTask($topic);

            if (is_null($currentTask)) { // all tasks done
                $remainingTasks = Task::with(array('ratings' => function ($query) {
                    $query->where('student_id', Auth::id());
                }))->where("topic_id", $id)->get();
            } else {
                $remainingTasks = Task::with(array('ratings' => function ($query) {
                    $query->where('student_id', Auth::id());
                }))->where("topic_id", $id)
                    ->where("_id", "!=", $currentTask->_id)
                    ->get();
            }
        }

        return view('student/topic', compact('topic', 'currentTask', 'remainingTasks'));
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

            // Export property to json
            $task->intro_filetype = $task->intro_filetype;
            if ($task->intro_type == Task::LOCAL) {
                $task->intro = \Illuminate\Support\Facades\Storage::url($task->intro);
            } else if ($task->intro_type == Task::EXTERNAL) {
                preprocessExternalFile($task, "INTRO");
            }

            // Export property to json
            $task->extro_filetype = $task->extro_filetype;
            if ($task->extro_type == Task::LOCAL) {
                $task->extro = \Illuminate\Support\Facades\Storage::url($task->extro);
            } else if ($task->extro_type == Task::EXTERNAL) {
                preprocessExternalFile($task, "EXTRO");
            }

            return response()->json([
                'task' => $task,
            ], \Illuminate\Http\Response::HTTP_OK);
        }
    }

    /**
     * Get Task Details
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTaskDataWithUserSolution($id)
    {
        $task = Task::find($id);

        if ($task) {
            // Remove teacher default solution
            unset($task->solution);

            // Export property to json
            $task->intro_filetype = $task->intro_filetype;
            $task->extro_filetype = $task->extro_filetype;

            // Add user solution
            $rating = Rating::where('task_id', $task->_id)->where('student_id', Auth::id())->first();
            $task->solution = json_decode($rating->solution_data);

            return response()->json([
                'task' => $task,
            ], \Illuminate\Http\Response::HTTP_OK);
        }
    }

    /**
     * Send a solution and check if it's correct
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function solveTask(Request $request)
    {
        $status = false;

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'module' => 'required|string',
            'data' => 'required|json',
            'required_time' => 'required|numeric',
            'used_tips' => 'required|numeric',
            'solve_attempts' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $message = __('Invalid data send.');
        } else {

            $task = Task::find($request->get('id'));

            if ($task) {
                if ($task->checkSolution($request)) {
                    $status = true;
                    $message = __('Task :name has been solved.', ['name' => $task->name]);

                    // Create rating
                    $rating = Rating::where('task_id', $task->_id)->where('student_id', Auth::id())->first();

                    if ($rating === null) {
                        $rating = Rating::create();
                        $rating->student()->associate(Auth::user());
                        $rating->task()->associate($task);
                    }

                    $rating->calculateScore($request->get('required_time'), $request->get('used_tips'), count($task->tips), $request->get('solve_attempts'));

                    // Store optional additional info (example code for programming task, ...)
                    $rating->solution_data = $request->get('data');

                    $rating->save();

                } else {
                    $message = __('This was not the correct solution. Try again.');
                }
            } else {
                $message = __('Task not found.');
            }
        }

        return response()->json([
            'result' => $status,
            'message' => $message
        ], \Illuminate\Http\Response::HTTP_OK);
    }
}
