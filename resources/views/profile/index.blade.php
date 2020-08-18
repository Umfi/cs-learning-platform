@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Profile') }}</div>

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

                    <h3>{{ __("My account") }}</h3>
                    <br>


                    <form id="createForm" method="POST" action="{{ route('updateProfile') }}">
                    {{csrf_field()}}

                        <div class="form-group">
                            <label for="name"><i class="fas fa-user-circle"></i> {{ __("Name") }}</label>
                            <input id="name" type="text" readonly class="form-control" value="{{ $user->name }}" />
                        </div>

                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> {{ __("E-Mail") }}</label>
                            <input type="email" id="email" name="email" class="form-control" value="{{ $user->email }}" required />
                        </div>


                        <div class="form-group">
                            <label for="language">{{ __('Language') }}</label>
                            <select id="language" name="language" class="form-control" required>
                                @isset($user->language)
                                    <option value="de" {{ $user->language == 'de' ? 'selected' : '' }}>{{ __('German') }}</option>
                                    <option value="en" {{ $user->language == 'en' ? 'selected' : '' }}>{{ __('English') }}</option>
                                @else
                                    <option value="de" selected>{{ __('German') }}</option>
                                    <option value="en">{{ __('English') }}</option>
                                @endisset
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="password"><i class="fas fa-lock"></i> {{ __("New Password") }}</label>
                            <input type="password" id="password" name="password" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation"><i class="fas fa-lock"></i> {{ __("Confirm New Password") }}</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" />
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="password_c"><i class="fas fa-lock"></i> {{ __("Current Password") }}</label>
                            <input type="password" id="password_c" name="current_password" class="form-control" autocomplete="current-password" required />
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __("Update") }}</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
