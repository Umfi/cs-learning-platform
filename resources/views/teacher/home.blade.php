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

                            <div class="card m-2 {{ $course->active ? "" : "inactive" }}" style="width: 19rem;">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $course->name }} {{ $course->active ? "" : __('(Inactive)') }}</h5>
                                    <p class="card-text">
                                        <i class="fas fa-users"></i> {{ $course->participants->count() }} <br>
                                        <i class="fas fa-history"></i> {{ $course->updated_at->format('d.m.Y H:i') }} <br>
                                        <i class="fas fa-key"></i> {{ $course->code }} <br>
                                        <i class="fas {{ $course->shared ? "fa-lock-open" : "fa-lock" }}"></i> {{ $course->shared ? __('Shared') : __('Not shared') }}<br>
                                    </p>
                                    <a href="{{ route('teacher-showCourse', $course->_id) }}" class="btn btn-primary">{{ __('View details') }}</a>
                                    <button type="button" class="btn btn-secondary" onclick="editCourse('{{ $course->_id }}')">{{ __('Edit course') }}</button>
                                    <rating course="{{ $course->_id }}"></rating>
                                </div>
                            </div>
                    @empty
                            <p class="col-12">{{ __("No courses found.") }}</p>
                    @endforelse
                    </div>


                    <br><br>

                    <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#createCourseModal">{{__("Create course")}}</button>

                    <button type="button" class="btn btn-secondary float-right mr-2" data-toggle="modal" data-target="#copyCourseModal">{{__("Copy course")}}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Course Modal -->
<div class="modal" id="createCourseModal">
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
                        <i class="fa-info-circle fas ml-1" data-toggle="tooltip" title="{{ __("Other teachers can copy the course.") }}"></i>
                    </label>
                </div>

                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="active" value="1">{{ __("Active") }}
                        <i class="fa-info-circle fas ml-1" data-toggle="tooltip" title="{{ __("Students can view and join the course.") }}"></i>
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

<!-- Edit Course Modal -->
<div class="modal" id="editCourseModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">{{ __("Edit course") }}</h4>
                <button type="button" class="close" data-dismiss="modal" onclick="document.getElementById('editForm').reset();">&times;</button>
            </div>

            <form id="editForm" method="POST" action="{{ route('teacher-editCourse') }}">
            {{csrf_field()}}
            <!-- Modal body -->
                <div class="modal-body">
                    <!-- Course ID -->
                    <input type="hidden" name="id" value="">

                    <div class="form-group">
                        <label for="name">{{ __("Name") }}</label>
                        <input type="text" class="form-control" placeholder="{{ __("Enter the course name") }}" name="name" required>
                    </div>

                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="shared" value="1">{{ __("Shared") }}
                            <i class="fa-info-circle fas ml-1" data-toggle="tooltip" title="{{ __("Other teachers can copy the course.") }}"></i>
                        </label>
                    </div>

                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="active" value="1">{{ __("Active") }}
                            <i class="fa-info-circle fas ml-1" data-toggle="tooltip" title="{{ __("Students can view and join the course.") }}"></i>
                        </label>
                    </div>

                </div>


                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="document.getElementById('editForm').reset();">{{ __("Cancle") }}</button>
                    <button type="submit" class="btn btn-primary">{{ __("Update") }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Copy Course Modal -->
<div class="modal" id="copyCourseModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">{{ __("Copy course") }}</h4>
                <button type="button" class="close" data-dismiss="modal" onclick="document.getElementById('copyForm').reset();">&times;</button>
            </div>

            <form id="copyForm" method="POST" action="{{ route('teacher-copyCourse') }}">
            {{csrf_field()}}
            <!-- Modal body -->
                <div class="modal-body">

                    <p>{{ __("You can copy any shared course and all courses that belong to you.") }}</p>

                    <div class="form-group">
                        <label for="course">{{ __("Select a course:") }}</label>
                        <select class="form-control" name="course" required>
                            <option selected disabled value=""> {{ __("--- Select ---") }}</option>
                            @foreach($allCourses as $course)
                                <option value="{{$course->_id}}">{{ $course->name }} </option>
                            @endforeach
                        </select>
                    </div>

                </div>


                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="document.getElementById('copyForm').reset();">{{ __("Cancle") }}</button>
                    <button type="submit" class="btn btn-primary">{{ __("Copy") }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        function editCourse(id) {

            $.ajax({
                url: '{{ route('teacher-getCourse', '') }}/' + id,
                type: 'get',
                success: function( data, textStatus, jQxhr ){

                    if (data.course != null) {
                        $("#editForm input[name='id']").val(data.course._id);
                        $("#editForm input[name='name']").val(data.course.name);
                        $("#editForm input[name='shared']").prop('checked', data.course.shared);
                        $("#editForm input[name='active']").prop('checked', data.course.active);
                        $('#editCourseModal').modal('show');
                    }

                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

        }
    </script>
@endsection
