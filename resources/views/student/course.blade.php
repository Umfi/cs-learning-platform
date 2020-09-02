@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header"><a href="{{ route('home') }}">{{ __("Dashboard") }}</a> / {{ $course->name }}</div>

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

                        <h3>{{ __('Course Topics') }}</h3>

                        <div class="row row-cols-1 row-cols-lg-3 row-cols-md-2 row-cols-sm-1">
                            @forelse($course->topics as $topic)
                                @if($topic->active)
                                    <div class="col">
                                        <div class="card m-2">
                                            <img class="card-img-top" width="100" height="150" src="{{ $topic->image ? \Illuminate\Support\Facades\Storage::url($topic->image) : "https://via.placeholder.com/100x150" }}" alt="Topic image">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $topic->name }}</h5>
                                                <p class="card-text">
                                                    {{ \Illuminate\Support\Str::limit($topic->description, 250) }}
                                                </p>
                                                <hr>
                                                <a href="{{ route('student-showTopic', $topic->_id) }}" class="btn btn-lg btn-primary d-block">{{ __('View tasks') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <p class="col-12">{{ __("No topics found.") }}</p>
                            @endforelse
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
