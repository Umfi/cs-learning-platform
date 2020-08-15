@extends('layouts.app')

@section('content')



    <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header"><a href="{{ route('admin') }}">Dashboard</a> / {{ __('Topics') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <table id="topicTable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Image</th>
                                <th>Course</th>
                                <th>Active</th>
                                <th>Created on</th>
                                <th>Updated on</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($topics as $topic)
                                <tr>
                                    <td>{{ $topic->name }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($topic->description, 150) }}</td>
                                    <td>{{ $topic->image ? "Yes": "No" }}</td>
                                    <td>{{ $topic->course->name }}</td>
                                    <td>
                                        @if( $topic->active )
                                            <form method="POST" action="{{ route('admin-deactivateTopic') }}">
                                                {{csrf_field()}}
                                                <input type="hidden" name="id" value="{{ $topic->_id }}">
                                                <button class="btn btn-sm btn-success" title="Deactivate topic">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin-activateTopic') }}">
                                                {{csrf_field()}}
                                                <input type="hidden" name="id" value="{{ $topic->_id }}">
                                                <button class="btn btn-sm btn-danger" title="Activate topic">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td>{{ $topic->created_at->format('d. m. Y') }}</td>
                                    <td>{{ $topic->updated_at->format('d. m. Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            $('#topicTable').DataTable( {
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/{{ config('app.locale') == "de" ? "German" : "English" }}.json"
                }
            } );
        });
    </script>
@endsection
