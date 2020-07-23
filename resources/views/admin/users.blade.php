@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Users') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Active</th>
                                <th>Created on</th>
                                <th>Updated on</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin-changeUserRole') }}">
                                            {{csrf_field()}}
                                            <input type="hidden" name="id" value="{{ $user->_id }}">

                                            <select name="role" class="custom-select" onchange="this.form.submit()">
                                                <option value="{{ \App\User::ROLE_ADMIN }}" {{ ( $user->role == \App\User::ROLE_ADMIN) ? 'selected' : '' }}>{{ \App\User::ROLE_ADMIN }}</option>
                                                <option value="{{ \App\User::ROLE_TEACHER }}" {{ ( $user->role == \App\User::ROLE_TEACHER) ? 'selected' : '' }}>{{ \App\User::ROLE_TEACHER }}</option>
                                                <option value="{{ \App\User::ROLE_STUDENT }}" {{ ( $user->role == \App\User::ROLE_STUDENT) ? 'selected' : '' }}>{{ \App\User::ROLE_STUDENT }}</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        @if( $user->active )
                                            <form method="POST" action="{{ route('admin-deactivateUser') }}">
                                                {{csrf_field()}}
                                                <input type="hidden" name="id" value="{{ $user->_id }}">
                                                <button class="btn-success" title="Deactivate User">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin-activateUser') }}">
                                                {{csrf_field()}}
                                                <input type="hidden" name="id" value="{{ $user->_id }}">
                                                <button class="btn-danger" title="Activate User">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('d. m. Y') }}</td>
                                    <td>{{ $user->updated_at->format('d. m. Y') }}</td>
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
