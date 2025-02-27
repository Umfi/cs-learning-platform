@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

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

                        <div class="container">
                            <h3 class="text-center">{{ __('Join a course') }}</h3>
                            <div class="d-flex flex-row justify-content-center align-items-center">
                                <form id="joinCourseForm" class="form-inline" method="POST" action="{{ route('student-joinCourse') }}">
                                    {{csrf_field()}}
                                    <div class="form-group">
                                        <input type="text" class="form-control mr-1" name="code" placeholder="{{ __("Enter a code to join") }}">
                                        <button type="submit" class="btn btn-primary"><i class="far fa-paper-plane"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <hr>
                        <br>

                        <h3>{{ __('My courses') }}</h3>

                        <div class="row">
                            @forelse($myCourses as $course)

                                <div class="card m-2 bg-course" style="max-width: 18rem;">
                                    <div class="card-header">{{ $course->name }}</div>
                                    <div class="card-body">
                                        <p class="card-text text-center">
                                            <a href="{{ route('student-showCourse', $course->_id) }}" class="btn btn-lg btn-primary"><i class="fas fa-play"></i> {{ __('Start course') }}</a>
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="col-12">{{ __("No courses found.") }}</p>
                            @endforelse
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
