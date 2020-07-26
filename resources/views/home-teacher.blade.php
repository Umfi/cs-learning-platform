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

                    <h3>{{ __('My courses') }}</h3>

                    @forelse($myCourses as $course)

                            <div class="card" style="width: 18rem;">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $course->name }}</h5>
                                    <p class="card-text">
                                        <i class="fas fa-users"></i> {{ $course->participants->count() }} <br>
                                        <i class="fas fa-history"></i> {{ $course->updated_at->format('d.m.Y H:s') }} <br>
                                        <i class="fas fa-key"></i> {{ $course->code }} <br>
                                    </p>
                                    <a href="#" class="btn btn-primary">{{ __('View details') }}</a>
                                </div>
                            </div>
                    @empty
                            <p>{{ __("No courses found.") }}</p>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
