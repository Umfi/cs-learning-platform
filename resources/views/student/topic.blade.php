@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header"><a href="{{ route('home') }}">{{ __("Dashboard") }}</a> / <a href="{{ route('student-showCourse', $topic->course->_id) }}">{{ $topic->course->name }}</a> / {{ $topic->name }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif


                            @if($topic->changed || empty($topic->learningpath))
                                <div class="alert alert-danger" role="alert">
                                    {{ __("Learning path is not setup yet. Contact your teacher.") }}
                                </div>
                            @else
                                <h4>{{ __('Current task') }}</h4>
                                <div class="row row-cols-1 row-cols-lg-3 row-cols-md-2 row-cols-sm-1">
                                    @if(!is_null($currentTask))
                                        @if($currentTask->active)
                                            <div class="col">
                                                <!-- Task is available if user already has passed it once. If it's the first task. Or the previous task is done and the current one has no rating. -->
                                                <div class="card m-2" style="width: 15rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title">{{ $currentTask->name }}</h5>
                                                        <p class="card-text">
                                                            <!-- render difficulty stars -->
                                                            @for($i = 0; $i < 5; $i++)
                                                                @if ($i < $currentTask->difficulty)
                                                                    <i class="fas fa-star"></i>
                                                                @else
                                                                    <i class="far fa-star"></i>
                                                                @endif
                                                            @endfor
                                                        </p>
                                                        <br>
                                                        <!-- Task -->
                                                        <task taskid="{{ $currentTask->_id }}" taskmodule="{{ $currentTask->module }}"></task>
                                                     </div>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <p class="col-12">{{ __("No tasks found.") }}</p>
                                    @endif
                                </div>

                                <hr>

                                <h4>{{ __('Finished tasks') }}</h4>
                                <div class="row row row-cols-1 row-cols-lg-3 row-cols-md-2 row-cols-sm-1">
                                    @forelse($remainingTasks as $task)
                                        @if($task->active && $task->userRating)
                                            <div class="col">
                                                <!-- Task is available if user already has passed it once. If it's the first task. Or the previous task is done and the current one has no rating. -->
                                                <div class="card m-2" style="width: 15rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title">{{ $task->name }}</h5>
                                                        <p class="card-text">
                                                            <!-- render difficulty stars -->
                                                            @for($i = 0; $i < 5; $i++)
                                                                @if ($i < $task->difficulty)
                                                                    <i class="fas fa-star"></i>
                                                                @else
                                                                    <i class="far fa-star"></i>
                                                                @endif
                                                            @endfor
                                                        </p>
                                                        <br>
                                                        <!-- Task -->
                                                        <task taskid="{{ $task->_id }}" taskmodule="{{ $task->module }}"></task>

                                                        @if ($task->userRating)

                                                            <task-solution taskid="{{ $task->_id }}" taskmodule="{{ $task->module }}"></task-solution>

                                                            <div class="float-right">
                                                                <button type="button" title="{{ __("Rating") }}" class="btn">
                                                                    <i class="fas fa-trophy fa-2x text-gold"></i> {{ $task->userRating->score . "|" . $task->userRating->score_max }}
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @empty
                                        <p class="col-12">{{ __("No tasks found.") }}</p>
                                    @endforelse
                                </div>
                            @endif


                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

