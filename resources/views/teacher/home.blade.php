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

                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">{{ __("General") }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="users-tab" data-toggle="tab" href="#users" role="tab" aria-controls="users" aria-selected="false">{{ __("Participants") }}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
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
                        <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
                            <br>
                            <ul id="usersList">

                            </ul>
                        </div>
                   </div>



                </div>


                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger mr-auto" data-dismiss="modal" onclick="deleteCourse()"><i class="fas fa-trash"></i></button>

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

                        $("#usersList").empty();
                        for (var i = 0; i < data.course.participants.length; i++) {
                            $("#usersList").append('<li>' + data.course.participants[i].name + ' - <a href="#" onclick="resetUserPassword(\'' + data.course.participants[i]._id + '\')"><i class="fas fa-user-cog"></i> {{ __("Reset Password") }}</a></li>');
                        }


                        $('#editCourseModal').modal('show');
                    }

                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });
        }


        function deleteCourse() {
            var courseID = $("#editForm input[name='id']").val();

            if (courseID) {
                Swal.fire({
                    title: '{{ __("Delete course?") }}',
                    html: "{{ __("You won't be able to revert this!") }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ __("Yes, delete it!") }}',
                }).then((result) => {
                    if (result.value) {

                        $.ajax({
                            url: '{{ route('teacher-deleteCourse', '') }}/' + courseID,
                            type: 'delete',
                            data: {"_token": "{{ csrf_token() }}"},
                            success: function (data, textStatus, jQxhr) {

                                if (data.status) {

                                    Swal.fire({
                                        title: '{{ __("Deleted!") }}',
                                        html: "{{ __("The course has been deleted.") }}",
                                        icon: 'success',
                                        showCancelButton: false,
                                        confirmButtonText: '{{ __("Close") }}',
                                    }).then((result) => {
                                        location.reload();
                                    });

                                }
                            },
                            error: function (jqXhr, textStatus, errorThrown) {
                                console.log(errorThrown);
                            }
                        });


                    }
                });
            }
        }

        function resetUserPassword(id) {
            $.ajax({
                url: '{{ route('teacher-resetUserPassword', '') }}/' + id,
                type: 'get',
                success: function (data, textStatus, jQxhr) {

                    if (data.status) {
                        Swal.fire({
                            title: '{{ __("Done!") }}',
                            html: "{{ __("The user should have received a email to reset its password.") }}",
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonText: '{{ __("Close") }}',
                        });
                    } else {
                        Swal.fire({
                            title: '{{ __("Oops...") }}',
                            html: "{{ __("Could not send password reset email to user.") }}",
                            icon: 'error',
                            showCancelButton: false,
                            confirmButtonText: '{{ __("Close") }}',
                        });
                    }
                },
                error: function (jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });

        }
    </script>
@endsection
