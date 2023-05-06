<?php

namespace App\Http\Controllers;

use App\Models\ProfAttendance;
use Illuminate\Http\Request;

class ProfAttendanceController extends Controller
{
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProfAttendance  $profAttendance
     * @return \Illuminate\Http\Response
     */
    public function show(ProfAttendance $profAttendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProfAttendance  $profAttendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProfAttendance $profAttendance)
    {
        if ($profAttendance->skipped === 1){
            $profAttendance->attended = 0;
            $profAttendance->skipped = 0;



        }elseif ($profAttendance->attended === 0){
            $profAttendance->attended = 0;
            $profAttendance->skipped = 1;
        }

        $profAttendance->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProfAttendance  $profAttendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProfAttendance $profAttendance)
    {
        //
    }
}
