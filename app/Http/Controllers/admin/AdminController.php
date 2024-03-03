<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    
    public function loggedUser()
    {
        $loggedUser = auth()->user();

        $response = [
            'user' => $loggedUser,
            'message' => 'Logged user Data',
            'success' => true,
            'status' => 200,
            'img'=> asset('users/'.auth()->user()->photo)
        ];
        return response()->json($response, 401);
    }

    public function getAllUsers(){

        if(auth()->user()->role == 1){ 
            $users = User::all();
            if (is_null($users)) {
                $response = [
                    
                    'message' => 'error',
                    'status' => 500,
                ];
                return response()->json($response, 200);
            }else{
                $response = [
                    'data' => $users,
                    'success' => true,
                    'status' => 200,
                ];
                return response()->json($response, 200);
            }
        }else{
            $response = [
                'message' => 'You are not Admin User, please Login with Admin Account',
                'status' => 500,
            ];
            return response()->json($response, 200);
        }
    }

    public function makeAdmin(Request $request){
        if(auth()->user()->role == 1){ 
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'message' => 'Validation Error.', $validator->errors(),
                    'status' => 500
                ];
                return response()->json($response, 404);
            }

            $data = User::find($request->id)->update([
                'role'=>1          
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

    public function removeAdmin(Request $request){
        if(auth()->user()->role == 1){ 
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'message' => 'Validation Error.', $validator->errors(),
                    'status' => 500
                ];
                return response()->json($response, 404);
            }

            $data = User::find($request->id)->update([
                'role'=>2          
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

    public function deleteUser(Request $request){
        if(auth()->user()->role == 1){ 
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'message' => 'Validation Error.', $validator->errors(),
                    'status' => 500
                ];
                return response()->json($response, 404);
            }

            $data = User::find($request->id)->delete();
                    
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


    public function deleteselectedUser(Request $request){
        if(auth()->user()->role == 1){ 
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'message' => 'Validation Error.', $validator->errors(),
                    'status' => 500
                ];
                return response()->json($response, 404);
            }
            $ids = $request->id;
            
            $data = User::WhereIn('id', $ids)->delete();
                    
            if ($data == 0) {
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

  
}
