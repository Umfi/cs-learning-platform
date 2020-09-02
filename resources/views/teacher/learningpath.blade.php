@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header"><a href="{{ route('home') }}">{{ __("Dashboard") }}</a> / <a href="{{ route('teacher-showCourse', $topic->course->_id) }}">{{ $topic->course->name }}</a> /  <a href="{{ route('teacher-showTopic', $topic->_id) }}">{{ $topic->name }}</a> / {{ __("Learning Path") }} </div>

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

                        <h3>{{ $topic->name }} - {{ __('Learning Path') }}</h3>

                        <div class="row">
                            <learning-path topic="{{ $topic->_id }}"></learning-path>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

