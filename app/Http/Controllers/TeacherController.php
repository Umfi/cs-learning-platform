<?php

namespace App\Http\Controllers;

use App\Course;
use App\Task;
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
     * Show the topics of a course.
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

    /**
     * Show the tasks of a topic.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showTopic($id)
    {
        $topic = Topic::find($id);
        $modules = Task::MODULES;

        return view('teacher/topic', compact('topic', 'modules'));
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
            if ($task->topic->course->owner_id == Auth::id()) {
                return response()->json([
                    'task' => $task,
                ], \Illuminate\Http\Response::HTTP_OK);
            }
        }

        return response()->json([
            'task' => null,
        ], \Illuminate\Http\Response::HTTP_OK);
    }

    /**
     * Create a new task
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createTask(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'topic_id' => 'required',
            'name' => 'required|string|min:3|max:255',
            'description' => 'required|string|max:4096',
            'module'=> 'required',
            'difficulty'=> 'numeric',
            'intro' => 'mimes:jpeg,png,jpg,gif,svg,mp4,avi,mov,ogg|max:51200',
            'extro' => 'mimes:jpeg,png,jpg,gif,svg,mp4,avi,mov,ogg|max:51200',
        ]);

        if ($validator->fails()) {
            session()->flash('error', 'Task "' . $request->get('name') . '" has not been created. Invalid data.');
        } else {
            $topic = Topic::find($request->get('topic_id'));

            if ($topic) {
                if ($topic->course->owner_id == Auth::id()) {

                    if ($request->hasFile('intro')) {

                        $file = request()->file('intro');
                        $fileName = time() . '.' . $file->getClientOriginalExtension();

                        Storage::disk('public')->put('uploads/' . $fileName, file_get_contents($file));

                        $intro =  Storage::url('uploads/' . $fileName);
                    } else {
                        $intro = "";
                    }

                    if ($request->hasFile('extro')) {

                        $file = request()->file('extro');
                        $fileName = time() . '.' . $file->getClientOriginalExtension();

                        Storage::disk('public')->put('uploads/' . $fileName, file_get_contents($file));

                        $extro =  Storage::url('uploads/' . $fileName);
                    } else {
                        $extro = "";
                    }

                    $task = Task::create([
                        'name' => $request->get('name'),
                        'description' => $request->get('description'),
                        'module' => $request->get('module'),
                        'difficulty' => $request->get('difficulty'),
                        'intro' => $intro,
                        'extro' => $extro,
                        'active' => $request->get('active') == "1"
                    ]);
                    $task->topic()->associate($topic);
                    $task->save();

                    session()->flash('status', 'Task "' . $request->get('name') . '" has been created.');

                } else {
                    session()->flash('error', 'Task "' . $request->get('name') . '" has not been created. Not authorized.');
                }
            } else {
                session()->flash('error', 'Task "' . $request->get('name') . '" has not been created.');
            }
        }

        return Redirect::back();
    }

    /**
     * Edit a task
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editTask(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => 'required',
            'name' => 'required|string|min:3|max:255',
            'description' => 'required|string|max:4096',
            'module'=> 'required',
            'difficulty'=> 'numeric',
            'intro' => 'mimes:jpeg,png,jpg,gif,svg,mp4,avi,mov,ogg|max:51200',
            'extro' => 'mimes:jpeg,png,jpg,gif,svg,mp4,avi,mov,ogg|max:51200',
        ]);

        if ($validator->fails()) {
            session()->flash('error', 'Task "' . $request->get('name') . '" has not been updated. Invalid data send.');
        } else {

            $task = Task::find($request->get('id'));

            if ($task) {

                if ($task->topic->course->owner_id == Auth::id()) {

                    $task->name = $request->get('name');
                    $task->description = $request->get('description');

                    // Module has changed, remove module specific config
                    if ($task->module != $request->get('module')) {
                        $task->module = $request->get('module');
                        $task->removeModuleSpecificConfig();
                    }

                    $task->difficulty = $request->get('difficulty');

                    if ($request->hasFile('intro')) {

                        $file = request()->file('intro');
                        $fileName = time() . '.' . $file->getClientOriginalExtension();

                        Storage::disk('public')->put('uploads/' . $fileName, file_get_contents($file));

                        $task->intro =  Storage::url('uploads/' . $fileName);
                    }

                    if ($request->hasFile('extro')) {

                        $file = request()->file('extro');
                        $fileName = time() . '.' . $file->getClientOriginalExtension();

                        Storage::disk('public')->put('uploads/' . $fileName, file_get_contents($file));

                        $task->extro =  Storage::url('uploads/' . $fileName);
                    }

                    $task->active = $request->get('active') == "1";
                    $task->save();

                    session()->flash('status', 'Task "' . $request->get('name') . '" has been updated.');
                } else {
                    session()->flash('error', 'Task "' . $request->get('name') . '" has not been updated. Not authorized.');
                }
            } else {
                session()->flash('error', 'Task "' . $request->get('name') . '" has not been updated.');
            }
        }

        return Redirect::back();
    }

    /**
     * Set a task module specific config
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setTaskModuleConfig(Request $request)
    {
        $status = false;

        $validator = Validator::make($request->all(),[
            'id' => 'required',
            'module' => 'required|string',
            'data' => 'required|json',
        ]);

        if ($validator->fails()) {
            $message = "Tasks module config has not been updated. Invalid data send.";
        } else {

            $task = Task::find($request->get('id'));

            if ($task) {
                if ($task->topic->course->owner_id == Auth::id()) {

                    if ($task->storeModuleConfig($request)) {
                        $task->save();
                        $status = true;
                        $message =  'Task "' . $task->name . '" has been updated.';
                    } else {
                        $message = 'Task "' . $task->name . '" has not been updated.';
                    }
                } else {
                    $message = 'Task "' . $task->name . '" has not been updated. Not authorized.';
                }
            } else {
                $message = 'Task has not been updated. Task not found.';
            }
        }

        return response()->json([
            'result' => $status,
            'message' => $message
        ], \Illuminate\Http\Response::HTTP_OK);
    }


}
