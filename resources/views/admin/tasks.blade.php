@extends('layouts.app')

@section('content')



    <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header"><a href="{{ route('admin') }}">Dashboard</a> / {{ __('Tasks') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <table id="taskTable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Module</th>
                                <th>Topic</th>
                                <th>Description</th>
                                <th>Difficulty</th>
                                <th>Active</th>
                                <th>Created on</th>
                                <th>Updated on</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($tasks as $task)
                                <tr>
                                    <td>{{ $task->name }}</td>
                                    <td>{{ $task->module }}</td>
                                    <td>{{ $task->topic->name }}</td>
                                    <td>{{ $task->description }}</td>
                                    <td>{{ $task->difficulty }}</td>
                                    <td>
                                        @if( $task->active )
                                            <form method="POST" action="{{ route('admin-deactivateTask') }}">
                                                {{csrf_field()}}
                                                <input type="hidden" name="id" value="{{ $task->_id }}">
                                                <button class="btn btn-sm btn-success" title="Deactivate task">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin-activateTask') }}">
                                                {{csrf_field()}}
                                                <input type="hidden" name="id" value="{{ $task->_id }}">
                                                <button class="btn btn-sm btn-danger" title="Activate task">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td>{{ $task->created_at->format('d. m. Y') }}</td>
                                    <td>{{ $task->updated_at->format('d. m. Y') }}</td>
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
            $('#taskTable').DataTable();
        });
    </script>
@endsection
