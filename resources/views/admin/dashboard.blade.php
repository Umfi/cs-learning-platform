@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Admin Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif


                    <div class="row">

                        <div class="col-md-4 col-sm-6 mt-2">
                            <a href="{{ route('admin-users') }}" class="adminTileLink">
                                <div class="adminTile card-body border shadow text-center">
                                    <div>
                                        <div>
                                            <i class="fas fa-users fa-3x" aria-hidden="true"></i>
                                        </div>
                                        <h4>{{ __('Users') }}</h4>
                                    </div>
                                </div>
                            </a>

                        </div>

                        <div class="col-md-4 col-sm-6 mt-2">
                            <a href="{{ route('admin-courses') }}" class="adminTileLink">
                                <div class="adminTile card-body border shadow text-center">
                                    <div>
                                        <div>
                                            <i class="fas fa-chalkboard-teacher fa-3x" aria-hidden="true"></i>
                                        </div>
                                        <h4>{{ __('Courses') }}</h4>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-4 col-sm-6 mt-2">
                            <a href="{{ route('admin-topics') }}" class="adminTileLink">
                                <div class="adminTile card-body border shadow text-center">
                                    <div>
                                        <div>
                                            <i class="fab fa-leanpub fa-3x" aria-hidden="true"></i>
                                        </div>
                                        <h4>{{ __('Topics') }}</h4>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-4 col-sm-6 mt-2">
                            <a href="{{ route('admin-tasks') }}" class="adminTileLink">
                                <div class="adminTile card-body border shadow text-center">
                                    <div>
                                        <div>
                                            <i class="fas fa-tasks fa-3x" aria-hidden="true"></i>
                                        </div>
                                        <h4>{{ __('Tasks') }}</h4>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-4 col-sm-6 mt-2">
                            <a href="{{ route('admin-ratings') }}" class="adminTileLink">
                                <div class="adminTile card-body border shadow text-center">
                                    <div>
                                        <div>
                                            <i class="fas fa-star fa-3x" aria-hidden="true"></i>
                                        </div>
                                        <h4>{{ __('Ratings') }}</h4>
                                    </div>
                                </div>
                            </a>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
