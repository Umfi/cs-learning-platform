<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the users profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        return view('profile/index')->with('user', $user);
    }

    /**
     * Update the users profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users','email')->ignore(Auth::id(), '_id') ],
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    return $fail(__('The current password is incorrect.'));
                }
            }],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            session()->flash('error', 'User has not been updated.');
        } else {
            $user = User::findOrFail(Auth::id());

            $user->email = $request->get('email');

            if (!empty($request->get('password'))) {
                $user->password = Hash::make($request->get('password'));
            }

            $user->save();
            session()->flash('status', 'User has been updated.');
        }

        return Redirect::back();
    }
}
