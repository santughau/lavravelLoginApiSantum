<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
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

       
        $response = [
            'data' => $user,
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 200);
    }
    
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

    public function last_seen(){
        if(auth()->user()->role == 1){ 
            $users = User::orderBy('last_seen','desc')->get();

            $users->each(function ($user) {
                $lastSeen = Carbon::parse($user->last_seen)->diffForHumans();
                $user->last_seen_ago = $lastSeen;

                if($user->last_seen >= now()->subMinutes(1)){
                    $user->last_seen_status = 'Online';
                }else{
                    $user->last_seen_status = 'Offline';
                }            
            });

            if(count($users) > 0){
                $response = [
                    'data' => $users,
                    'success' => true,
                    'status' => 200,
                ];
                return response()->json($response, 200);
            }else{
                $response = [
                    'message' => 'No user is Online Now',
                    'success' => false,
                    'status' => 500,
                ];
                return response()->json($response, 200);
            }  
        }      

    }


    public function filterDates(Request $request){

        if(auth()->user()->role == 1){ 
            $validator = Validator::make($request->all(), [
                'start_date' => 'required',
                'end_date' => 'required'
            ]);
            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'message' => 'Validation Error.', $validator->errors(),
                    'status' => 500
                ];
                return response()->json($response, 404);
            }    
    
            $start_date = $request->start_date;
            $end_date = $request->end_date;
    
            $Users = User::whereDate('dob','>=', $start_date)
                    ->whereDate('dob','<=', $end_date)->get();


            if(count($Users) > 0){
                $response = [
                    'data' => $Users,
                    'success' => true,
                    'status' => 200,
                ];
                return response()->json($response, 200);
            }else{
                $response = [
                    'message' => 'No user Found',
                    'success' => false,
                    'status' => 500,
                ];
                return response()->json($response, 200);
            }  
        }

        
    }

     public function sortDates(Request $request){
        $query = User::query();
        $date = $request->date_filter;

        switch ($date) {
            case 'today':
                $query->whereDate('dob',Carbon::today());
                break;
            case 'yesterday':
                $query->whereDate('dob',Carbon::yesterday());
                break;
            case 'this_week':
                $query->whereBetween('dob',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]);
                break;
            case 'last_week':
                $query->whereBetween('dob',[Carbon::now()->subWeek(),Carbon::now()]);
                break;
            case 'this_month':
                $query->whereMonth('dob',Carbon::now()->month);
                break;
            case 'last_month':
                $query->whereMonth('dob',Carbon::now()->subMonth()->month);
                break;
            case 'this_year':
                $query->whereYear('dob',Carbon::now()->year);
                break;
            case 'last_year':
                $query->whereYear('dob',Carbon::now()->subYear()->year);
                break;
        }

        $users = $query->get();

        if(count($users) > 0){
            $response = [
                'data' => $users,
                'success' => true,
                'status' => 200,
            ];
            return response()->json($response, 200);
        }else{
            $response = [
                'message' => 'No user is Online Now',
                'success' => false,
                'status' => 500,
            ];
            return response()->json($response, 200);
        } 
    }

    public function makeActive(Request $request){
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
                'IsActive'=>1          
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

    public function removeActive(Request $request){
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
                'IsActive'=>0          
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
}
