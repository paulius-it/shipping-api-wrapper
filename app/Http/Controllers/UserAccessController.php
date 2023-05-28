<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * User registration and log-in functionality
 */
class UserAccessController extends Controller
{
    public function __construct(private UserRepository $userRepo)
    {
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $this->userRepo->createUser([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $tokenhash = 'token hash';

        return response()->json([
            'access_token' => $user->createToken($tokenhash)->plainTextToken,
        ], Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email);
        $userPassword = Hash::check($request->password, $user->password);

        if (!$user || !$userPassword) {
            return response()->json([
                'errors' => ['Wrong credentials. Try again'],
            ]);
        }

        $tokenhash    = 'token hash';
        $tokenValidTill = now()->addMinutes(10000);

        return response()->json([
            'access_token' => $user->createToken($tokenhash, $tokenValidTill)->plainTextToken,
        ], Response::HTTP_CREATED);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->where('id', auth()->id())->delete();

        return response()->json([
            'logout successful'
        ]);
    }

    public function setProviderCredentials(Request $request)
    {
        $providers = $request->input('providers') ?? null;

        if (!$providers) {
            return response()->json('No credentials to save');
        }

        $omnivaProvider = $providers['omniva'] ?? null;
        $lpProvider = $providers['lp'] ?? null;
        $saved = false;

        if ($omnivaProvider) {
            if (
                Cache::put('app.apiProviders.omniva.api_access_key', $omnivaProvider['api_access_key'])
                && Cache::put('app.apiProviders.omniva.api_secret_key', $omnivaProvider['api_secret_key'])
            ) {
                $saved = true;
            }
        }

        if ($lpProvider) {
            if (
                Cache::put('app.apiProviders.lp.api_access_key', $lpProvider['api_access_key'])
                && Cache::put('app.apiProviders.lp.api_secret_key', $lpProvider['api_secret_key'])
            ) {
                $saved = true;
            }
        }

        $message = $saved ? 'Settings saved successfully' : 'Failed to saved API authentication info';

        return response()->json($message);
    }
}
