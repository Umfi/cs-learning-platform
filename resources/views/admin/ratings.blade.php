@extends('layouts.app')

@section('content')



    <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header"><a href="{{ route('admin') }}">Dashboard</a> / {{ __('Rating') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <table id="ratingTable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Task</th>
                                <th>Student</th>
                                <th>Score</th>
                                <th>Max Score</th>
                                <th>Used Tips</th>
                                <th>Time Needed</th>
                                <th>Created on</th>
                                <th>Updated on</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($ratings as $rating)
                                <tr>
                                    <td>{{ $rating->task->name }}</td>
                                    <td>{{ $rating->student->name }}</td>
                                    <td>{{ $rating->score }}</td>
                                    <td>{{ $rating->score_max }}</td>
                                    <td>{{ $rating->used_tips }}</td>
                                    <td>{{ $rating->required_time }}</td>
                                    <td>{{ $rating->created_at->format('d. m. Y') }}</td>
                                    <td>{{ $rating->updated_at->format('d. m. Y') }}</td>
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
            $('#ratingTable').DataTable();
        });
    </script>
@endsection
