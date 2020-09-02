@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header"><a href="{{ route('home') }}">{{ __("Dashboard") }}</a> / <a href="{{ route('teacher-showCourse', $topic->course->_id) }}">{{ $topic->course->name }}</a> /  {{ $topic->name }}</div>

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

                        <div class="row row-cols-1 row-cols-lg-3 row-cols-md-2 row-cols-sm-1">
                            @forelse($topic->tasks as $task)
                                <div class="col">
                                    <div class="card m-2 {{ $task->active ? "" : "inactive" }}" style="width: 15rem;">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $task->name }} {{ $task->active ? "" : __('(Inactive)') }}</h5>
                                            <p class="card-text">

                                            </p>
                                            <button type="button" class="btn btn-primary" onclick="editTask('{{ $task->_id }}')">{{ __('Edit task') }}</button>
                                            <!-- Task Module Config -->
                                            <taskmoduleconfig taskid="{{ $task->_id }}" taskmodule="{{ $task->module }}"></taskmoduleconfig>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="col-12">{{ __("No tasks found.") }}</p>
                            @endforelse
                        </div>


                        <br><br>


                        <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#createTaskModal">{{__("Create task")}}</button>

                        <a href="{{ route('teacher-learningPath', $topic->_id) }}" class="btn btn-outline-primary float-right mr-2">
                            <i class="fas fa-route"></i> {{__("Define learning path")}}
                            @if($topic->changed || (empty($topic->learningpath) && count($topic->tasks) > 0))<i class="text-danger blink fas fa-exclamation-triangle"></i>@endif
                        </a>


                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Task Modal -->
    <div class="modal" id="createTaskModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __("Create task") }}</h4>
                    <button type="button" class="close" data-dismiss="modal" onclick="document.getElementById('createForm').reset();">&times;</button>
                </div>

                <form id="createForm" method="POST" enctype="multipart/form-data" action="{{ route('teacher-createTask') }}">
                {{csrf_field()}}
                <!-- Modal body -->
                    <div class="modal-body">

                        <input type="hidden" name="topic_id" value="{{ $topic->_id }}">

                        <div class="form-group">
                            <label for="name">{{ __("Name") }}</label>
                            <input type="text" class="form-control" placeholder="{{ __("Enter the task name") }}" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="module">{{ __("Module") }}</label>
                            <select class="form-control" name="module">
                                @foreach($modules as $module_key => $module_name)
                                <option value="{{ $module_key }}">{{ $module_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description">{{ __("Description") }}</label>
                            <textarea class="form-control" rows="5" name="description"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="difficulty">{{ __("Difficulty") }}</label>
                            <select class="form-control" name="difficulty">
                                <option value="1">{{ __("1 - very easy") }}</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">{{ __("5 - very difficulty") }}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="introTab">{{ __("Intro") }}</label>
                            <i class="fa-info-circle fas ml-1" data-toggle="tooltip" title="{{ __('Can only be a local file or a external file or a text.') }}"></i>

                            <ul class="nav nav-tabs" id="introTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="localeintro-tab" data-toggle="tab" href="#localeintro" role="tab" aria-controls="localeintro" aria-selected="true"> {{ __("Local file") }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="externalintro-tab" data-toggle="tab" href="#externalintro" role="tab" aria-controls="externalintro" aria-selected="false">{{ __("External file") }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="textintro-tab" data-toggle="tab" href="#textintro" role="tab" aria-controls="textintro" aria-selected="false">{{ __("Text") }}</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="introTabContent">
                                <div class="tab-pane fade show active" id="localeintro" role="tabpanel" aria-labelledby="localeintro-tab">
                                    <input type="file" class="form-control-file" name="intro_local" accept="image/*,video/*">
                                </div>
                                <div class="tab-pane fade" id="externalintro" role="tabpanel" aria-labelledby="externalintro-tab">
                                    <input type="text" class="form-control" name="intro_external" placeholder="{{ __('Link to video or picture') }}">
                                </div>
                                <div class="tab-pane fade" id="textintro" role="tabpanel" aria-labelledby="textintro-tab">
                                    <textarea class="form-control" rows="5" name="intro_text"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="extroTab">{{ __("Extro") }}</label>
                            <i class="fa-info-circle fas ml-1" data-toggle="tooltip" title="{{ __('Can only be a local file or a external file or a text.') }}"></i>

                            <ul class="nav nav-tabs" id="extroTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="locale-tab" data-toggle="tab" href="#locale" role="tab" aria-controls="locale" aria-selected="true"> {{ __("Local file") }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="external-tab" data-toggle="tab" href="#external" role="tab" aria-controls="external" aria-selected="false">{{ __("External file") }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="text-tab" data-toggle="tab" href="#text" role="tab" aria-controls="text" aria-selected="false">{{ __("Text") }}</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="extroTabContent">
                                <div class="tab-pane fade show active" id="locale" role="tabpanel" aria-labelledby="locale-tab">
                                    <input type="file" class="form-control-file" name="extro_local" accept="image/*,video/*">
                                </div>
                                <div class="tab-pane fade" id="external" role="tabpanel" aria-labelledby="external-tab">
                                    <input type="text" class="form-control" name="extro_external" placeholder="{{ __('URL zu Video oder Bild einfügen') }}">
                                </div>
                                <div class="tab-pane fade" id="text" role="tabpanel" aria-labelledby="text-tab">
                                    <textarea class="form-control" rows="5" name="extro_text"></textarea>
                                </div>
                            </div>
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

    <!-- Edit Task Modal -->
    <div class="modal" id="editTaskModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __("Edit task") }}</h4>
                    <button type="button" class="close" data-dismiss="modal" onclick="document.getElementById('editForm').reset();">&times;</button>
                </div>

                <form id="editForm" method="POST" enctype="multipart/form-data" action="{{ route('teacher-editTask') }}">
                {{csrf_field()}}
                <!-- Modal body -->
                    <div class="modal-body">

                        <!-- Topic ID -->
                        <input type="hidden" name="id" value="">

                        <div class="form-group">
                            <label for="name">{{ __("Name") }}</label>
                            <input type="text" class="form-control" placeholder="{{ __("Enter the task name") }}" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="module">{{ __("Module") }}</label>
                            <select class="form-control" name="module">
                                @foreach($modules as $module_key => $module_name)
                                    <option value="{{ $module_key }}">{{ $module_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description">{{ __("Description") }}</label>
                            <textarea class="form-control" rows="5" name="description"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="difficulty">{{ __("Difficulty") }}</label>
                            <select class="form-control" name="difficulty">
                                <option value="1">{{ __("1 - very easy") }}</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">{{ __("5 - very difficulty") }}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="introTabEdit">{{ __("Intro") }}</label>
                            <i class="fa-info-circle fas ml-1" data-toggle="tooltip" title="{{ __('Can only be a local file or a external file or a text.') }}"></i>

                            <ul class="nav nav-tabs" id="introTabEdit" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="localeintro-tabedit" data-toggle="tab" href="#localeintroedit" role="tab" aria-controls="localeintroedit" aria-selected="true"> {{ __("Local file") }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="externalintro-tabedit" data-toggle="tab" href="#externalintroedit" role="tab" aria-controls="externalintroedit" aria-selected="false">{{ __("External file") }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="textintro-tabedit" data-toggle="tab" href="#textintroedit" role="tab" aria-controls="textintroedit" aria-selected="false">{{ __("Text") }}</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="introTabContentEdit">
                                <div class="tab-pane fade show active" id="localeintroedit" role="tabpanel" aria-labelledby="localeintro-tabedit">
                                    <input type="file" class="form-control-file" name="intro_local" accept="image/*,video/*">
                                </div>
                                <div class="tab-pane fade" id="externalintroedit" role="tabpanel" aria-labelledby="externalintro-tabedit">
                                    <input type="text" class="form-control" name="intro_external" placeholder="{{ __('Link to video or picture') }}">
                                </div>
                                <div class="tab-pane fade" id="textintroedit" role="tabpanel" aria-labelledby="textintro-tabedit">
                                    <textarea class="form-control" rows="5" name="intro_text"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="extroTabEdit">{{ __("Extro") }}</label>
                            <i class="fa-info-circle fas ml-1" data-toggle="tooltip" title="{{ __('Can only be a local file or a external file or a text.') }}"></i>

                            <ul class="nav nav-tabs" id="extroTabEdit" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="locale-tabedit" data-toggle="tab" href="#localeedit" role="tab" aria-controls="localeedit" aria-selected="true"> {{ __("Local file") }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="external-tabedit" data-toggle="tab" href="#externaledit" role="tab" aria-controls="externaledit" aria-selected="false">{{ __("External file") }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="text-tabedit" data-toggle="tab" href="#textedit" role="tab" aria-controls="textedit" aria-selected="false">{{ __("Text") }}</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="extroTabContentEdit">
                                <div class="tab-pane fade show active" id="localeedit" role="tabpanel" aria-labelledby="locale-tabedit">
                                    <input type="file" class="form-control-file" name="extro_local" accept="image/*,video/*">
                                </div>
                                <div class="tab-pane fade" id="externaledit" role="tabpanel" aria-labelledby="external-tabedit">
                                    <input type="text" class="form-control" name="extro_external" placeholder="{{ __('URL zu Video oder Bild einfügen') }}">
                                </div>
                                <div class="tab-pane fade" id="textedit" role="tabpanel" aria-labelledby="text-tabedit">
                                    <textarea class="form-control" rows="5" name="extro_text"></textarea>
                                </div>
                            </div>
                        </div>


                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="active" value="1">{{ __("Active") }}
                            </label>
                        </div>

                    </div>


                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger mr-auto" data-dismiss="modal" onclick="deleteTask()"><i class="fas fa-trash"></i></button>

                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="document.getElementById('editForm').reset();">{{ __("Cancle") }}</button>
                        <button type="submit" class="btn btn-primary">{{ __("Update") }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            $('[data-toggle="tooltip"]').tooltip();
        });

        function editTask(id) {

            $.ajax({
                url: '{{ route('teacher-getTask', '') }}/' + id,
                type: 'get',
                success: function( data, textStatus, jQxhr ){

                    if (data.task != null) {

                        $("#editForm input[name='id']").val(data.task._id);
                        $("#editForm input[name='name']").val(data.task.name);
                        $("#editForm select[name='module']").val(data.task.module);
                        $("#editForm textarea[name='description']").val(data.task.description);
                        $("#editForm select[name='difficulty']").val(data.task.difficulty);
                        $("#editForm input[name='active']").prop('checked', data.task.active);


                        $("#editForm input[name='intro_external']").val("");
                        $("#editForm textarea[name='intro_text']").val("");
                        if (data.task.intro_type === "EXTERNAL") {
                            $("#editForm input[name='intro_external']").val(data.task.intro);
                        } else if (data.task.intro_type === "TEXT") {
                            $("#editForm textarea[name='intro_text']").val(data.task.intro);
                        }

                        $("#editForm input[name='extro_external']").val("");
                        $("#editForm textarea[name='extro_text']").val("");
                        if (data.task.extro_type === "EXTERNAL") {
                            $("#editForm input[name='extro_external']").val(data.task.extro);
                        } else if (data.task.extro_type === "TEXT") {
                            $("#editForm textarea[name='extro_text']").val(data.task.extro);
                        }

                        $('#editTaskModal').modal('show');
                    }

                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

        }

        function deleteTask() {
            var taskID = $("#editForm input[name='id']").val();

            if (taskID) {
                Swal.fire({
                    title: '{{ __("Delete task?") }}',
                    html: "{{ __("You won't be able to revert this!") }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ __("Yes, delete it!") }}',
                }).then((result) => {
                    if (result.value) {

                        $.ajax({
                            url: '{{ route('teacher-deleteTask', '') }}/' + taskID,
                            type: 'delete',
                            data: {"_token": "{{ csrf_token() }}"},
                            success: function (data, textStatus, jQxhr) {

                                if (data.status) {

                                    Swal.fire({
                                        title: '{{ __("Deleted!") }}',
                                        html: "{{ __("The task has been deleted.") }}",
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
    </script>
@endsection
