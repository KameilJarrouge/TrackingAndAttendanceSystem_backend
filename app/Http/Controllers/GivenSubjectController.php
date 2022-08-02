<?php

namespace App\Http\Controllers;

use App\Models\GivenSubject;
use Illuminate\Http\Request;

class GivenSubjectController extends Controller
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
     * @param  \App\Models\GivenSubject  $givenSubject
     * @return \Illuminate\Http\Response
     */
    public function show(GivenSubject $givenSubject)
    {
        //
    }


    public function restart(Request $request, GivenSubject $givenSubject){
        $givenSubject->update(['attendance_extend' => $request->get('extend_duration')]);

        return response(['status' => 'ok','message' => 'تم تمديد الحضور بنجاح']);
    }

    public function extend(Request $request, GivenSubject $givenSubject){
        $givenSubject->update(['restart_start_time' => $request->get('extend_duration') , 'restart_duration' => $request->get('restart_duration')]);

        return response(['status' => 'ok','message' => 'تم إعادة تسجيل الحضور بنجاح']);
    }

    public function info(GivenSubject $givenSubject){
        return response($givenSubject->loadMissing(['professor','subject','cam']));
    }
    public function infoDashboard(GivenSubject $givenSubject){
        $givenSubject->activeWeekProfAttendanceAttended();
        return response($givenSubject->loadMissing(['professor','subject','cam'])->loadCount(['activeWeekAttendance','activeWeekAttendanceAttended']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GivenSubject  $givenSubject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GivenSubject $givenSubject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GivenSubject  $givenSubject
     * @return \Illuminate\Http\Response
     */
    public function destroy(GivenSubject $givenSubject)
    {
        //
    }
}
