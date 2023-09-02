<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Contacts, User, Properties, Categories, Cities};
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class api extends Controller
{
    public function login(Request $request)
    {
        $validator = validator($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 401);
        } else {
            $user = User::where('email', $request->email)->first();

            if (!password_verify($request->password, $user->password)) {
                return response()->json(['errors' => 'Email or password is incorrect'], 401);
            } else {
                return response()->json(
                    [
                        'success' => 'Login successfully',
                        'token' => $user->createToken('auth_token')->plainTextToken,
                        'user' => $user
                    ],
                    200
                );
            }
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['success' => 'Logout successfully'], 200);
    }

    public function register(Request $request)
    {
        $validator = validator($request->all(), [
            'full_name' => 'required|min:3|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 401);
        } else {

            $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'email_verified_at' => null,
            ]);

            event(new Registered($user));

            if (!$user) {
                return response()->json(['errors' => 'Register faild!'], 401);
            } else {
                return response()->json([
                    'success' => 'Register successfully',
                    'token' => $user->createToken('auth_token')->plainTextToken,
                    'user' => $user
                ], 200);
            }
        }
    }

    public function verifyNotification(Request $request)
    {
        $user = User::findOrFail($request->id);

        if ($user->hasVerifiedEmail()) {
            return response()->json(['errors' => 'Email already verified!'], 401);
        } else {
            if (!hash_equals((string) $request->hash, sha1($user->getEmailForVerification()))) {
                return response()->json(['errors' => 'Email verification faild!'], 401);
            } else {
                if ($user->markEmailAsVerified()) {
                    event(new Verified($user));
                    return response()->json(['success' => 'Email verification successfully'], 200);
                } else {
                    return response()->json(['errors' => 'Email not access to verify!'], 401);
                }
            }
        }
    }

    public function resendVerificationEmail(Request $request)
    {
        $validator = validator($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 401);
        } else {
            $user = User::where('email', $request->email)->first();

            if ($user->hasVerifiedEmail()) {
                return response()->json(['errors' => 'Email already verified!'], 401);
            } else {
                $user->sendEmailVerificationNotification();
                return response()->json(['success' => 'Email verification sent successfully'], 200);
            }
        }
    }

    public function forgot(Request $request)
    {
        $validator = validator($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 401);
        } else {
            $status = Password::sendResetLink($request->only('email'));

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json(['success' => 'Password reset link sent successfully'], 200);
            } else {
                return response()->json(['errors' => __('passwords.throttled')], 401);
            }
        }
    }

    public function passwordReset(Request $request)
    {
        $validator = validator($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 401);
        } else {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user) use ($request) {
                    $user->forceFill([
                        'password' => bcrypt($request->password),
                        'remember_token' => $request->token,
                    ])->save();

                    $user->tokens()->delete();
                    event(new PasswordReset($user));
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return response()->json(['success' => 'Password reset successfully'], 200);
            } else {
                return response()->json(['errors' => __('passwords.token')], 401);
            }
        }
    }

    public function home(Request $request)
    {
        return [
            'categories' => Categories::latest()->get(),
            'cities' => Cities::latest()->get(),
            'properties' => Properties::OfUser($request->user_id)
                ->OfCategory($request->category_id)
                ->OfCity($request->city_id)
                ->OfPrice($request->price[0], $request->price[1])
                ->OfArea($request->area[0], $request->area[1])
                ->OfSearch($request->search)
                ->latest()
                ->take(7)
                ->get(),
            'users' => User::latest()->take(7)->get(),
            'less_price' => Properties::latest()->orderBy('price', 'asc')->take(7)->get(),
        ];
    }

    public function properties(Request $request)
    {
        return Properties::OfUser($request->user_id)
            ->OfCategory($request->category_id)
            ->OfCity($request->city_id)
            ->OfSearch($request->search)
            ->latest()
            ->paginate(10);
    }

    public function property($id)
    {
        return Properties::with(['category', 'city', 'user'])->findOrFail($id);
    }

    public function users()
    {
        return User::latest()->paginate(10);
    }

    public function user($id)
    {
        return Properties::OfUser($id)->latest()->paginate(10);
    }

    public function contact(Request $request)
    {
        $validator = validator($request->all(), [
            'full_name' => 'required|min:3|max:100',
            'email' => 'required|email',
            'phone' => 'required|min:10|max:11',
            'message' => 'required|min:10|max:400'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 401);
        } else {

            $contact = Contacts::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message
            ]);

            if (!$contact) {
                return response()->json(['errors' => 'Message sent faild!'], 401);
            } else {
                return response()->json(['success' => 'Message sent successfully'], 200);
            }
        }
    }
}
