@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
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

                        <h3>{{ __('Join a course') }}</h3>
                        <div class="row">
                            <form id="joinCourseForm" class="form-inline" method="POST" action="{{ route('student-joinCourse') }}">
                                {{csrf_field()}}
                                <div class="form-group">
                                    <input type="text" class="form-control mr-1" name="code" placeholder="{{ __("Enter a code to join") }}">
                                    <button type="submit" class="btn btn-primary"><i class="far fa-paper-plane"></i></button>
                                </div>
                            </form>
                        </div>

                        <br>

                        <h3>{{ __('My courses') }}</h3>

                        <div class="row">
                            @forelse($myCourses as $course)

                                <div class="card m-2" style="width: 18rem;">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $course->name }}</h5>
                                        <p class="card-text">
                                            <i class="fas fa-users"></i> {{ $course->participants->count() }} <br>
                                            <i class="fas fa-history"></i> {{ $course->updated_at->format('d.m.Y H:i') }} <br>
                                        </p>
                                        <a href="{{ route('teacher-showCourse', $course->_id) }}" class="btn btn-primary">{{ __('Start course') }}</a>
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
