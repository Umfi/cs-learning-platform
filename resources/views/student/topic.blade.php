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

                        <h3>{{ __('Topic Tasks') }}</h3>

                            <div class="row row row-cols-1 row-cols-lg-3 row-cols-md-2 row-cols-sm-1">
                            @forelse($tasks as $task)
                                @if($task->active)
                                    <div class="col">
                                        <!-- Task is available if user already has passed it once. If it's the first task. Or the previous task is done and the current one has no rating. -->
                                        <div class="card m-2 {{ $task->userRating || ($loop->index == 0) || ($tasks[$loop->index - 1]->userRating) ? "" : "locked" }}" style="width: 15rem;">
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

                                                @if ($task->userRating || ($loop->index == 0) || ($tasks[$loop->index - 1]->userRating))
                                                    <!-- Task -->
                                                    <task taskid="{{ $task->_id }}" taskmodule="{{ $task->module }}"></task>

                                                    @if ($task->userRating)
                                                        <div class="float-right">
                                                            <button type="button" title="{{ __("Rating") }}" class="btn">
                                                            <i class="fas fa-trophy fa-2x text-gold"></i> {{ $task->userRating->score . "|" . $task->userRating->score_max }}
                                                            </button>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <p class="col-12">{{ __("No tasks found.") }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

