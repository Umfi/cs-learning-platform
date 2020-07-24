@extends('layouts.app')

@section('content')



    <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Courses') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <table id="courseTable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Owner</th>
                                <th>Code</th>
                                <th>Shared</th>
                                <th>Active</th>
                                <th>Participants</th>
                                <th>Created on</th>
                                <th>Updated on</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($courses as $course)
                                <tr>
                                    <td>{{ $course->name }}</td>
                                    <td>{{ $course->owner->name }}</td>
                                    <td>{{ $course->code }}</td>
                                    <td>
                                        @if( $course->shared )
                                            <i class="fas fa-lock-open" title="This course is public"></i>
                                        @else
                                            <i class="fas fa-lock" title="This course is private"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if( $course->active )
                                            <form method="POST" action="{{ route('admin-deactivateUser') }}">
                                                {{csrf_field()}}
                                                <input type="hidden" name="id" value="{{ $course->_id }}">
                                                <button class="btn btn-sm btn-success" title="Deactivate course">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin-activateUser') }}">
                                                {{csrf_field()}}
                                                <input type="hidden" name="id" value="{{ $course->_id }}">
                                                <button class="btn btn-sm btn-danger" title="Activate course">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button"
                                                class="bg-white btn btn-outline-secondary btn-sm text-dark"
                                                title="Show participants"
                                                onclick="showParticipants('{{ $course->_id }}')">
                                            <i class="fas fa-users"></i>
                                        </button>
                                    </td>
                                    <td>{{ $course->created_at->format('d. m. Y') }}</td>
                                    <td>{{ $course->updated_at->format('d. m. Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

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
                <h4 class="modal-title">Course Participants</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body" id="participants-area">
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            $('#courseTable').DataTable();
        });

        function showParticipants(id) {

            $.ajax({
                url: '{{ route('admin-getCourseParticipants', '') }}/' + id,
                type: 'get',
                success: function( data, textStatus, jQxhr ){

                    var html = "<ul>";

                    for(var i in data.participants) {
                        html += "<li>" + data.participants[i].name + "</li>";
                    }

                    html += "</ul>";
                    $("#participants-area").html(html);

                    $('#myModal').modal('show');
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });
        }
    </script>
@endsection
