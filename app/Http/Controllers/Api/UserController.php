<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function authMe(Request $request){
        $user = User::where('api_token', $request->token)->first()->toArray();
        
        if(count($user) != 0){
            return response([
                'fullname' => $user['fullname'],
                'email'=> $user['email'],
                'status'=>"true",
                'image'=> 'p1.jpg'
            ]);
        }
        else{
            return response([
                'status'=>"false"
            ]);
        }
    }
    public function register(Request $request){
        // return response([
        //     'response'=> $request->email
        // ]);
        $val = $request->validate([
            'fullname'=>'required',
            'email' => 'required|email',
            'password'=>'required|confirmed'
        ]);
        $data = [
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ];
        
        $emailToken = User::where('email', $request->email)->first();
        if($emailToken){
            return response([
                'message'=> 'Email already exists',
                'status' => 'failed'
            ]);
        }
        // $token = $emailToken->createToken($request->email)->plainTextToken;
        $response = User::create([
            'fullname'=>$request->fullname,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
            'image'=>'p1.jpg'
            // 'api_token'=> $token
        ]);
        $emailToken = User::where('email', $request->email)->first();
        $token = $emailToken->createToken($request->email)->plainTextToken;
        User::where("email", $request->email)->update(['api_token'=> $token]);
        if($response){
            return response([
                'message'=>"User created successfully",
                'fullname'=>$request->fullname,
                'email'=>$request->email,
                'image'=>'p1.jpg',
                'api_token' => $token
                // 'token'=>$token,
            ], 200);
        }
    }

    public function login(Request $request){
        $val = $request->validate([
            'email' => 'required|email|',
            'password' => 'required',
        ]);
        // $data = [
        //     "email" => $request->email,
        //     'password'=> bcrypt($request->password)
        // ];
        // if (!Auth::attempt($data)) {
        //     return response()->json([
        //         'message' => 'Invalid login details'
        //                    ], 401);
        // }
        $emailToken = User::where('email', $request->email)->first();
        if($emailToken && User::where('password', bcrypt($request->password))){
            $fullname = User::where('email', $request->email)->first();
        $token = $emailToken->createToken($request->email)->plainTextToken;
        User::where('email',$request->email)->update(['api_token'=> $token]);
            return response([
                'message'=> 'User Successfully login',
                'status'=> 'true',
                'api_token' => $token,
                'fullname'=>$fullname->fullname,
                'image' => 'p1.jpg'
            ],201);
        }
        else{
            return response([
                'message'=> "Login failed",
                'status' => 'false',
            ], 200);
        }
    }


    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
