<?php

namespace App\Http\Controllers;

use App\Events\TerminateEvent;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function shutdownPython()
    {
        broadcast(new TerminateEvent());
    }

    public function login(Request $request)
    {
        if (Auth::attempt([
            'username' => $request->get('username'),
            'password' => $request->get('password')
        ])) {
            $token = Auth::user()->createToken('myToken' . Auth::user()->id)->plainTextToken;
            $user = Auth::user();

            $currentSemester = Semester::getLatest();
            if ($currentSemester !== null) {
                $user->semester_id = $currentSemester->id;
            } else {
                $user->semester_id = null;
            }
            $user->save();
            if ($currentSemester !== null) {
                $user->semester = $currentSemester;
            }
            return \response(['user' => $user, 'token' => $token]);
        } else {
            return \response('error with the username or password', 402);
        }
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        //        \auth()->logout();
        Auth::logout();
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $identifier = "";
        if ($request->get('username') !== null) {
            $identifier = $request->get('username');
        }
        $query = User::query()->where('username', 'like', '%' . $identifier . '%');
        if ($request->get('isAdmin') !== "-1") {
            $query = $query->where('isAdmin', $request->get('isAdmin'));
        }
        $query = $query->where('id', '<>', \auth()->user()->id)->where('python', '<>', 1);
        return response($query->paginate($request->get('perPage')));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //        $request->validate([
        //            'username' => 'unique:users,username',
        //        ]);
        $head = 0;
        if (User::query()->where('head', 1)->count() === 0) {
            $head = 1;
        }

        $user = User::query()->create([
            'username' => $request->get('username'),
            'password' => bcrypt($request->get('password')),
            'isAdmin' => $request->get('isAdmin'),
            'head' => $head,
        ]);


        return response(['status' => 'ok', 'message' => 'تم إضافة المستخدم بنجاح']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response($user);
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

        if ($request->get('password') !== "-1") {

            $user->password = bcrypt($request->get('password'));
        }
        $user->username = $request->get('username');
        $user->save();
        return response(['status' => 'ok', 'message' => 'تم تعديل المستخدم بنجاح', 'user' => $user]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->head || $user->python) {
            return response(['status' => 'not ok', 'message' => 'لا يمكن حذف المستخدم الرئيسي']);
        } else {
            $user->delete();
            return response(['status' => 'ok', 'message' => 'تم إزالة المستخدم بنجاح']);
        }
    }
}
