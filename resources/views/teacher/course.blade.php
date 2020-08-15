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

                        <div class="row">
                            @forelse($course->topics as $topic)

                                <div class="card m-2 {{ $topic->active ? "" : "inactive" }}" style="width: 18rem;">
                                    <img class="card-img-top" src="{{ $topic->image ? \Illuminate\Support\Facades\Storage::url($topic->image) : "https://via.placeholder.com/150" }}" alt="Topic image">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $topic->name }} {{ $topic->active ? "" : __('(Inactive)') }}</h5>
                                        <p class="card-text">
                                            {{ $topic->description }}
                                        </p>
                                        <a href="{{ route('teacher-showTopic', $topic->_id) }}" class="btn btn-primary">{{ __('View details') }}</a>
                                        <button type="button" class="btn btn-secondary" onclick="editTopic('{{ $topic->_id }}')">{{ __('Edit topic') }}</button>
                                    </div>
                                </div>
                            @empty
                                <p class="col-12">{{ __("No topics found.") }}</p>
                            @endforelse
                        </div>


                        <br><br>

                        <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#createTopicModal">{{__("Create topic")}}</button>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Topic Modal -->
    <div class="modal" id="createTopicModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __("Create topic") }}</h4>
                    <button type="button" class="close" data-dismiss="modal" onclick="document.getElementById('createForm').reset();">&times;</button>
                </div>

                <form id="createForm" method="POST" enctype="multipart/form-data" action="{{ route('teacher-createTopic') }}">
                {{csrf_field()}}
                <!-- Modal body -->
                    <div class="modal-body">

                        <input type="hidden" name="course_id" value="{{ $course->_id }}">

                        <div class="form-group">
                            <label for="name">{{ __("Name") }}</label>
                            <input type="text" class="form-control" placeholder="{{ __("Enter the topic name") }}" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="description">{{ __("Description") }}</label>
                            <textarea class="form-control" rows="5" name="description"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="image">{{ __("Image") }}</label>
                            <input type="file" class="form-control-file" name="image" accept="image/*">
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

    <!-- Edit Topic Modal -->
    <div class="modal" id="editTopicModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __("Edit topic") }}</h4>
                    <button type="button" class="close" data-dismiss="modal" onclick="document.getElementById('editForm').reset();">&times;</button>
                </div>

                <form id="editForm" method="POST" enctype="multipart/form-data" action="{{ route('teacher-editTopic') }}">
                {{csrf_field()}}
                <!-- Modal body -->
                    <div class="modal-body">

                        <!-- Topic ID -->
                        <input type="hidden" name="id" value="">

                        <div class="form-group">
                            <label for="name">{{ __("Name") }}</label>
                            <input type="text" class="form-control" placeholder="{{ __("Enter the topic name") }}" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="description">{{ __("Description") }}</label>
                            <textarea class="form-control" rows="5" name="description"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="image">{{ __("Image") }}</label>
                            <input type="file" class="form-control-file" name="image" accept="image/*">
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
        function editTopic(id) {

            $.ajax({
                url: '{{ route('teacher-getTopic', '') }}/' + id,
                type: 'get',
                success: function( data, textStatus, jQxhr ){

                    if (data.topic != null) {
                        $("#editForm input[name='id']").val(data.topic._id);
                        $("#editForm input[name='name']").val(data.topic.name);
                        $("#editForm textarea[name='description']").val(data.topic.description);
                        $("#editForm input[name='active']").prop('checked', data.topic.active);
                        $('#editTopicModal').modal('show');
                    }

                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

        }
    </script>
@endsection
