<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
    public function login(Request $request)
    {
        if (Auth::attempt([
            'username'=>$request->get('username'),
            'password'=>$request->get('password')
        ])){
//            $token=Auth::user()->createToken('myToken'.Auth::user()->id)->plainTextToken;
            $user = Auth::user();

            $currentSemester = Semester::getLatest();
            if($currentSemester !== null){
                $user->semester_id = $currentSemester->id;
            }else{
                $user->semester_id = null;
            }
            $user->save();
            return \response(['user'=> \auth()->user()]);
        }

        else {
            return \response('error with the username or password', 402);
        }
    }

    public function logout()
    {
//         auth()->user()->tokens()->delete();
//        \auth()->logout();
        Auth::logout();
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'unique:users,username',
        ]);

        $user = User::query()->create([
            'username' => $request->get('username'),
            'password' => bcrypt($request->get('password')),
            'is_admin' => $request->get('is_admin')
        ]);

        return response($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
