<?php

namespace App\Http\Controllers;

use App\Course;
use App\Rating;
use App\Task;
use App\Topic;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
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
        $course = Course::with('participants')->get()->find($id);

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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
        ]);

        if ($validator->fails()) {
            session()->flash('error', __('Course :name has not been created. Invalid name.', ['name' => $request->get('name')]));
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

                session()->flash('status', __('Course :name has been created.', ['name' => $request->get('name')]));
            } else {
                session()->flash('error', __('Course :name has not been created.', ['name' => $request->get('name')]));
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
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required|string|min:3|max:255',
        ]);

        if ($validator->fails()) {
            session()->flash('error', __('Course :name has not been updated. Invalid data send.', ['name' => $request->get('name')]));
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

                        session()->flash('status', __('Course :name has been updated.', ['name' => $request->get('name')]));
                    } else {
                        session()->flash('error', __('Course :name has not been updated. Not authorized.', ['name' => $request->get('name')]));
                    }
                }
            } else {
                session()->flash('error', __('Course :name has not been updated.', ['name' => $request->get('name')]));
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
        $topic = Topic::with('tasks')->get()->find($id);

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
        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
            'name' => 'required|string|min:3|max:255',
            'description' => 'string|max:2048',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            session()->flash('error', __('Topic :name has not been created. Invalid data send.', ['name' => $request->get('name')]));
        } else {
            $course = Course::find($request->get('course_id'));

            if ($course) {
                if ($course->owner_id == Auth::id()) {

                    if ($request->hasFile('image')) {
                        $image = Storage::disk('public')->putFile('uploads', request()->file('image'));
                    } else {
                        $image = "";
                    }

                    $topic = Topic::create([
                        'name' => $request->get('name'),
                        'description' => $request->get('description'),
                        'image' => $image,
                        'learningpath' => '',
                        'changed' => false,
                        'active' => $request->get('active') == "1"
                    ]);
                    $topic->course()->associate($course);
                    $topic->save();

                    session()->flash('status', __('Topic :name has been created.', ['name' => $request->get('name')]));
                } else {
                    session()->flash('error', __('Topic :name has not been created. Not authorized.', ['name' => $request->get('name')]));
                }
            } else {
                session()->flash('error', __('Topic :name has not been created.', ['name' => $request->get('name')]));
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
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required|string|min:3|max:255',
            'description' => 'string|max:2048',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            session()->flash('error', __('Topic :name has not been updated. Invalid data send.', ['name' => $request->get('name')]));
        } else {

            $topic = Topic::find($request->get('id'));

            if ($topic) {

                if ($topic->course->owner_id == Auth::id()) {
                    $topic->name = $request->get('name');
                    $topic->description = $request->get('description');

                    if ($request->hasFile('image')) {
                        //delete old file
                        if (!empty($topic->image)) {
                            Storage::disk('public')->delete($topic->image);
                        }

                        $image = Storage::disk('public')->putFile('uploads', request()->file('image'));
                        $topic->image = $image;
                    }

                    $topic->active = $request->get('active') == "1";
                    $topic->save();

                    session()->flash('status', __('Topic :name has been updated.', ['name' => $request->get('name')]));
                } else {
                    session()->flash('error', __('Topic :name has not been updated. Not authorized.', ['name' => $request->get('name')]));
                }
            } else {
                session()->flash('error', __('Topic :name has not been updated.', ['name' => $request->get('name')]));
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
     * Show the learning path of a topic.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showLearningPath($id)
    {
        $topic = Topic::find($id);

        return view('teacher/learningpath', compact('topic'));
    }

    /**
     * Store the learning path of a topic
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeLearningPath(Request $request)
    {
        $status = false;

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'data' => 'required|json',
        ]);

        if ($validator->fails()) {
            $message = __("Learning path has not been saved. Invalid data send.");
        } else {

            $topic = Topic::find($request->get('id'));

            if ($topic) {
                if ($topic->course->owner_id == Auth::id()) {

                    $topic->learningpath = json_decode($request->get('data'));
                    $topic->changed = false;
                    $topic->save();
                    $status = true;
                    $message = __('Learning path has been successfully saved.');
                } else {
                    $message = __('Learning path has not been saved. Not authorized.');
                }
            } else {
                $message = __('Learning path has not been saved. Topic not found.');
            }
        }

        return response()->json([
            'result' => $status,
            'message' => $message
        ], \Illuminate\Http\Response::HTTP_OK);
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
        $validator = Validator::make($request->all(), [
            'topic_id' => 'required',
            'name' => 'required|string|min:3|max:255',
            'description' => 'required|string|max:4096',
            'module' => 'required',
            'difficulty' => 'numeric',
            'intro_local' => 'mimes:jpeg,png,jpg,gif,svg,mp4,avi,mov,ogg|max:51200',
            'intro_external' => 'url|nullable',
            'extro_local' => 'mimes:jpeg,png,jpg,gif,svg,mp4,avi,mov,ogg|max:51200',
            'extro_external' => 'url|nullable',
        ]);

        if ($validator->fails()) {
            session()->flash('error', __('Task :name has not been created. Invalid data send.', ['name' => $request->get('name')]));
        } else {
            $topic = Topic::find($request->get('topic_id'));

            if ($topic) {
                if ($topic->course->owner_id == Auth::id()) {

                    // Process intro
                    if ($request->hasFile('intro_local')) {
                        $intro = Storage::disk('public')->putFile('uploads', request()->file('intro_local'));
                        $intro_type = Task::LOCAL;
                    } else if (!empty($request->get('intro_external'))) {
                        $intro = $request->get('intro_external');
                        $intro_type = Task::EXTERNAL;
                    } else if (!empty($request->get('intro_text'))) {
                        $intro = $request->get('intro_text');
                        $intro_type = Task::TEXT;
                    } else {
                        $intro = "";
                        $intro_type = Task::NONE;
                    }

                    // Process extro
                    if ($request->hasFile('extro_local')) {
                        $extro = Storage::disk('public')->putFile('uploads', request()->file('extro_local'));
                        $extro_type = Task::LOCAL;
                    } else if (!empty($request->get('extro_external'))) {
                        $extro = $request->get('extro_external');
                        $extro_type = Task::EXTERNAL;
                    } else if (!empty($request->get('extro_text'))) {
                        $extro = $request->get('extro_text');
                        $extro_type = Task::TEXT;
                    } else {
                        $extro = "";
                        $extro_type = Task::NONE;
                    }


                    $task = Task::create([
                        'name' => $request->get('name'),
                        'description' => $request->get('description'),
                        'module' => $request->get('module'),
                        'difficulty' => $request->get('difficulty'),
                        'intro' => $intro,
                        'intro_type' => $intro_type,
                        'extro' => $extro,
                        'extro_type' => $extro_type,
                        'active' => $request->get('active') == "1",
                        'tips' => array(),
                    ]);
                    $task->topic()->associate($topic);
                    $task->save();

                    //Indicate that the learning path has to be adopted
                    if ($request->get('active') == "1") {
                        $topic->changed = true;
                        $topic->save();
                    }

                    session()->flash('status', __('Task :name has been created.', ['name' => $request->get('name')]));
                } else {
                    session()->flash('error', __('Task :name has not been created. Not authorized.', ['name' => $request->get('name')]));
                }
            } else {
                session()->flash('error', __('Task :name has not been created.', ['name' => $request->get('name')]));
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
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required|string|min:3|max:255',
            'description' => 'required|string|max:4096',
            'module' => 'required',
            'difficulty' => 'numeric',
            'intro' => 'mimes:jpeg,png,jpg,gif,svg,mp4,avi,mov,ogg|max:51200',
            'intro_external' => 'url|nullable',
            'extro' => 'mimes:jpeg,png,jpg,gif,svg,mp4,avi,mov,ogg|max:51200',
            'extro_external' => 'url|nullable',
        ]);

        if ($validator->fails()) {
            session()->flash('error', __('Task :name has not been updated. Invalid data send.', ['name' => $request->get('name')]));
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

                    if ($request->hasFile('intro_local')) {
                        //delete old file
                        if (!empty($task->intro)) {
                            Storage::disk('public')->delete($task->intro);
                        }

                        $path = Storage::disk('public')->putFile('uploads', request()->file('intro_local'));
                        $task->intro = $path;
                        $task->intro_type = Task::LOCAL;
                    } else if (!empty($request->get('intro_external'))) {
                        $task->intro = $request->get('intro_external');
                        $task->intro_type = Task::EXTERNAL;
                    } else if (!empty($request->get('intro_text'))) {
                        $task->intro = $request->get('intro_text');
                        $task->intro_type = Task::TEXT;
                    }

                    if ($request->hasFile('extro_local')) {
                        //delete old file
                        if (!empty($task->extro)) {
                            Storage::disk('public')->delete($task->extro);
                        }

                        $path = Storage::disk('public')->putFile('uploads', request()->file('extro_local'));
                        $task->extro = $path;
                        $task->extro_type = Task::LOCAL;
                    } else if (!empty($request->get('extro_external'))) {
                        $task->extro = $request->get('extro_external');
                        $task->extro_type = Task::EXTERNAL;
                    } else if (!empty($request->get('extro_text'))) {
                        $task->extro = $request->get('extro_text');
                        $task->extro_type = Task::TEXT;
                    }

                    //Indicate that the learning path has to be adopted
                    $newActiveState = $request->get('active') == "1";
                    if ($task->active !== $newActiveState) {
                        $topic = Topic::find($task->topic->_id);
                        $topic->changed = true;
                        $topic->save();
                    }

                    $task->active = $newActiveState;
                    $task->save();



                    session()->flash('status', __('Task :name has been updated.', ['name' => $request->get('name')]));
                } else {
                    session()->flash('error', __('Task :name has not been updated. Not authorized.', ['name' => $request->get('name')]));
                }
            } else {
                session()->flash('error', __('Task :name has not been updated.', ['name' => $request->get('name')]));
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

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'module' => 'required|string',
            'data' => 'required|json',
        ]);

        if ($validator->fails()) {
            $message = __("Tasks module config has not been updated. Invalid data send.");
        } else {

            $task = Task::find($request->get('id'));

            if ($task) {
                if ($task->topic->course->owner_id == Auth::id()) {

                    if ($task->storeModuleConfig($request)) {
                        $task->save();
                        $status = true;
                        $message = __('Task :name has been updated.', ['name' => $task->name]);
                    } else {
                        $message = __('Task :name has not been updated.', ['name' => $task->name]);
                    }
                } else {
                    $message = __('Task :name has not been updated. Not authorized.', ['name' => $task->name]);
                }
            } else {
                $message = __('Task has not been updated. Task not found.');
            }
        }

        return response()->json([
            'result' => $status,
            'message' => $message
        ], \Illuminate\Http\Response::HTTP_OK);
    }

    /**
     * Copy a course
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function copyCourse(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'course' => 'required'
        ]);

        if ($validator->fails()) {
            session()->flash('error', __('Can not copy course. Invalid data send.'));
        } else {

            $course = Course::find($request->get('course'));

            if ($course) {

                $user = User::find(Auth::id());

                // Create new course
                $newCourse = Course::create([
                    'name' => $course->name . " (Copy)",
                    'code' => Str::random(6),
                    'shared' => false,
                    'active' => true
                ]);
                $newCourse->owner()->associate($user);
                $newCourse->save();

                // Copy all topics from course
                foreach ($course->topics as $topic) {

                    $newTopic = Topic::create([
                        'name' => $topic->name,
                        'description' => $topic->description,
                        'image' => $topic->image,
                        'active' => $topic->active,
                        'learningpath' => '',
                        'changed' => true,
                    ]);
                    $newTopic->course()->associate($newCourse);
                    $newTopic->save();

                    // Copy all tasks from topic
                    foreach ($topic->tasks as $task) {

                        $newTask = Task::create([
                            'name' => $task->name,
                            'description' => $task->description,
                            'module' => $task->module,
                            'difficulty' => $task->difficulty,
                            'intro' => $task->intro,
                            'extro' => $task->extro,
                            'active' => $task->active,
                            'specification' => $task->specification,
                            'solution' => $task->solution,
                            'tips' => $task->tips,
                        ]);
                        $newTask->topic()->associate($newTopic);
                        $newTask->save();
                    }
                }

                session()->flash('status', __('Course :name has been copied.', ['name' => $course->name]));
            } else {
                session()->flash('error', __('Can not copy course. Course not found.'));
            }
        }

        return Redirect::back();
    }

    /**
     * Get all ratings of a course, grouped by tasks
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllRatingsFromCourse($id)
    {
        $course = Course::find($id);
        $ratings = array();
        $allRatingsFromCourse = array();

        if ($course) {

            foreach ($course->topics as $topic) {
                foreach ($topic->tasks as $task) {
                    foreach ($task->ratings as $rating) {

                        $rating->topic = $topic->name;
                        $rating->task = $task->name;
                        $rating->studentName = $rating->student->name;
                        array_push($allRatingsFromCourse, $rating);

                        $rating->participantsCount = count($course->participants);
                        unset($rating->student);

                        $ratings[$rating->task_id][$rating->score][] = $rating;
                    }
                }
            }
        }

        return response()->json([
            'ratings' => $ratings,
            'all' => $allRatingsFromCourse
        ], \Illuminate\Http\Response::HTTP_OK);
    }


    /**
     * Delete course
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCourse($id)
    {
        $course = Course::find($id);

        if ($course) {
            foreach ($course->topics as $topic) {
                foreach ($topic->tasks as $task) {
                    foreach ($task->ratings as $rating) {
                        $rating->delete();
                    }
                    $task->delete();
                }
                $topic->delete();
            }
            $course->delete();
        }

        return response()->json([
            'status' => true,
        ], \Illuminate\Http\Response::HTTP_OK);
    }

    /**
     * Delete topic
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteTopic($id)
    {
        $topic = Topic::find($id);

        if ($topic) {
            foreach ($topic->tasks as $task) {
                foreach ($task->ratings as $rating) {
                    $rating->delete();
                }
                $task->delete();
            }
            $topic->delete();
        }

        return response()->json([
            'status' => true,
        ], \Illuminate\Http\Response::HTTP_OK);
    }

    /**
     * Delete task
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteTask($id)
    {
        $task = Task::find($id);

        if ($task) {
            foreach ($task->ratings as $rating) {
                $rating->delete();
            }

            // set topic changes -> update learning path
            $topic = Topic::find($task->topic->_id);
            $topic->changed = true;
            $topic->save();

            $task->delete();
        }

        return response()->json([
            'status' => true,
        ], \Illuminate\Http\Response::HTTP_OK);
    }

    /**
     * Reset the password of a user
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetUserPassword($id)
    {
        $user = User::find($id);
        $status = false;

        if ($user) {
            try {
                Password::broker()->sendResetLink(['email' => $user->email]);
                $status = true;
            } catch (\Throwable $e) {
                report($e);
                $status = false;
            }
        }

        return response()->json([
            'status' => $status,
        ], \Illuminate\Http\Response::HTTP_OK);
    }

}
