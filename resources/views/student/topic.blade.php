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
                            @forelse($tasks as $task)
                                @if($task->active)
                                    <div class="card m-2" style="width: 15rem;">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $task->name }}</h5>
                                            <p class="card-text">
                                                <!-- render difficulty stars -->
                                                @for($i = 0; $i < 5; $i++)
                                                    @if ($i < $task->difficulty)
                                                        <i class="fas fa-star"></i>
                                                    @else
                                                        <i class="far fa-star"></i>
                                                    @endif
                                                @endfor

                                                <br>
                                                @forelse ($task->ratings as $rating)
                                                    <i class="fas fa-trophy"></i> {{ $rating->score . "/" . $rating->score_max }}
                                                @empty
                                                    <i class="fas fa-trophy"></i> 0
                                                @endforelse
                                            </p>

                                            <!-- Task -->
                                            <task taskid="{{ $task->_id }}" taskmodule="{{ $task->module }}"></task>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <p class="col-12">{{ __("No tasks found.") }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>

    </script>
@endsection