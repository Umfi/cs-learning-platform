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

                    <h3>{{ __('My courses') }}</h3>

                    <div class="row">
                    @forelse($myCourses as $course)

                            <div class="card m-2" style="width: 18rem;">
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
                            <p class="col-12">{{ __("No courses found.") }}</p>
                    @endforelse
                    </div>


                    <br><br>

                    <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#myModal">{{__("Create course")}}</button>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- The Modal -->
<div class="modal" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">{{ __("Create course") }}</h4>
                <button type="button" class="close" data-dismiss="modal" onclick="document.getElementById('createForm').reset();">&times;</button>
            </div>

            <form id="createForm" method="POST" action="{{ route('teacher-createCourse') }}">
            {{csrf_field()}}
            <!-- Modal body -->
            <div class="modal-body">

                <div class="form-group">
                    <label for="name">{{ __("Name") }}</label>
                    <input type="text" class="form-control" placeholder="{{ __("Enter the course name") }}" name="name" required>
                </div>

                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="shared" value="1">{{ __("Shared") }}
                    </label>
                </div>

                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="active" value="1">{{ __("Active") }}
                    </label>
                </div>

            </div>


            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="document.getElementById('createForm').reset();">{{ __("Cancle") }}</button>
                <button type="submit" class="btn btn-primary">{{ __("Create") }}</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>


    </script>
@endsection
