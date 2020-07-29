@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">{{ $topic->name }}</div>

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

                        <div class="row">
                            @forelse($topic->tasks as $task)

                                <div class="card m-2 {{ $topic->active ? "" : "inactive" }}" style="width: 18rem;">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $task->name }} {{ $topic->active ? "" : __('(Inactive)') }}</h5>
                                        <p class="card-text">

                                        </p>
                                        <button type="button" class="btn btn-primary" onclick="editTask('{{ $task->_id }}')">{{ __('Edit task') }}</button>
                                        <button type="button" class="btn btn-secondary" title="{{ __('Edit module specific task config') }}" onclick="editTaskModuleSettings('{{ $task->_id }}')"><i class="fas fa-cog"></i></button>
                                    </div>
                                </div>
                            @empty
                                <p class="col-12">{{ __("No tasks found.") }}</p>
                            @endforelse
                        </div>


                        <br><br>

                        <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#createTaskModal">{{__("Create task")}}</button>

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
                            <label for="intro">{{ __("Intro") }}</label>
                            <input type="file" class="form-control-file" name="intro" accept="image/*,video/*">
                        </div>

                        <div class="form-group">
                            <label for="extro">{{ __("Extro") }}</label>
                            <input type="file" class="form-control-file" name="extro" accept="image/*,video/*">
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
                            <label for="intro">{{ __("Intro") }}</label>
                            <input type="file" class="form-control-file" name="intro" accept="image/*,video/*">
                        </div>

                        <div class="form-group">
                            <label for="extro">{{ __("Extro") }}</label>
                            <input type="file" class="form-control-file" name="extro" accept="image/*,video/*">
                        </div>


                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="active" value="1">{{ __("Active") }}
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
@endsection

@section('scripts')
    <script>
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
                        $('#editTaskModal').modal('show');
                    }

                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

        }
    </script>
@endsection
