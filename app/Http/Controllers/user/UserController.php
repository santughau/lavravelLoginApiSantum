<?php

namespace App\Http\Controllers\user;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required | email | unique:users,email',
            'password' => 'required | confirmed'
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error.', $validator->errors(),
                'status' => 500
            ];
            return response()->json($response, 404);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 2,
            'IsActive' => true,
        ]);

        if (is_null($user)) {
            $response = [
                'data' => $user,
                'message' => 'error',
                'status' => 500,
            ];
            return response()->json($response, 200);
        }

        $token = $user->createToken($request->email)->plainTextToken;
        $response = [
            'data' => $user,
            'token' => $token,
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 200);
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required | email ',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error.', $validator->errors(),
                'status' => 500
            ];
            return response()->json($response, 404);
        }

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {

            $token = $user->createToken($request->email)->plainTextToken;
            $response = [
                'data' => $user,
                'token' => $token,
                'success' => true,
                'status' => 200,
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'message' => 'The provided Credentials are incorrect',
                'success' => false,
                'status' => 401,
            ];
            return response()->json($response, 401);
        }
    }


    public function logOut()
    {
        $user = Auth::user();

        if ($user) {
            $user->tokens()->delete();
        }

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function loggedUser()
    {
        $loggedUser = auth()->user();

        $response = [
            'user' => $loggedUser,
            'message' => 'Logged user Data',
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 401);
    }

    public function changepassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required | confirmed'
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error.', $validator->errors(),
                'status' => 500
            ];
            return response()->json($response, 404);
        }

        $loggedUser = auth()->user();
        $loggedUser->password = Hash::make($request->password);
        $loggedUser->save();

        $response = [
            'user' => $loggedUser,
            'message' => 'password successfully changed',
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 401);
    }

    public function profileUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'gender' => 'required',
            'mobile' => 'required',
            'dob' => 'required',
        ]);
        
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error.', $validator->errors(),
                'status' => 500
            ];
            return response()->json($response, 404);
        }

        if ($request->photo == '') {

            if ($request->gender == 'male') {
                $request->photo = 'male.jpg';
            } else {
                $request->photo = 'female.jpg';
            }
        }
        
        
        $data = User::find($request->id)->update([
            'name'=>$request->name,            
            'photo'=> $request->photo,            
            'gender'=> $request->gender,            
            'mobile'=> $request->mobile,            
            'dob'=> $request->dob            
        ]);


        if (is_null($data)) {
            $response = [
                'success' => false,
                'message' => 'Data not found.',
                'status' => 404
            ];
            return response()->json($response, 404);
        }
        $response = [
            'data' => $data,
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 200);
    }
}
