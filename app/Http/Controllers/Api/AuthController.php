<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
     public function register(Request $request)
    {
       try {
         $validated = $request->validate([
            'name' => 'required', 'string', 'max:255',
            'email' =>'required','email','max:255','unique:users,email',
            'password' => 'required','string','min:8','confirmed',
        ]);
            $u=User::all();
         if ($u && $request->email) {
            $u->first();
            return 'user get before';
         }
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
         if ($user && hash::check($request->password,$user->password)) {
             $token =$user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'user' => $user,
            'token' => $token,
        ]);
         }
       } catch (Exception $error) {
         return response()->json([
            'massege'=>$error->getMessage(),
        ]);
       }
    }
    // login section
        public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        $token = $user->createToken('freshstock')->plainTextToken;
        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'user' => $user,
            'token' => $token,
        ]);
    }
    // logout section
        public function logout(Request $request)
    {
       try {
         $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
       } catch (Exception $error) {
          return response()->json([
            'massege'=>$error->getMessage(),
          ]);
       }
    }
}
