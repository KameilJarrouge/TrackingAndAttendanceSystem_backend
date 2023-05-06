<?php

namespace App\Http\Controllers;

use App\Models\StdAttendance;
use Illuminate\Http\Request;

class StdAttendanceController extends Controller
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
     * @param  \App\Models\StdAttendance  $stdAttendance
     * @return \Illuminate\Http\Response
     */
    public function show(StdAttendance $stdAttendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StdAttendance  $stdAttendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StdAttendance $stdAttendance)
    {
        if ($stdAttendance->skipped === 1){
            $stdAttendance->attended = 0;
            $stdAttendance->skipped = 0;
            $stdAttendance->present = 0;

        }elseif ($stdAttendance->attended === 1){
            $stdAttendance->attended = 0;
            $stdAttendance->skipped = 0;
            $stdAttendance->present = 1;

        }elseif($stdAttendance->present === 1){
            $stdAttendance->attended = 1;
            $stdAttendance->skipped = 0;
            $stdAttendance->present = 0;


        }elseif ($stdAttendance->attended === 0){
            $stdAttendance->attended = 0;
            $stdAttendance->skipped = 1;
            $stdAttendance->present = 0;
        }

        $stdAttendance->save();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StdAttendance  $stdAttendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(StdAttendance $stdAttendance)
    {
        //
    }
}
