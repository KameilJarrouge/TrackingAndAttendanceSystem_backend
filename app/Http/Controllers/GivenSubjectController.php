<?php

namespace App\Http\Controllers;

use App\Models\GivenSubject;
use App\Models\Student;
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
        return response($givenSubject->loadMissing('subject'));
    }


    public function restart(Request $request, GivenSubject $givenSubject)
    {
        $givenSubject->update(['attendance_extend' => $request->get('extend_duration')]);

        return response(['status' => 'ok', 'message' => 'تم تمديد الحضور بنجاح']);
    }

    public function extend(Request $request, GivenSubject $givenSubject)
    {
        $givenSubject->update(['restart_start_time' => $request->get('extend_duration'), 'restart_duration' => $request->get('restart_duration')]);

        return response(['status' => 'ok', 'message' => 'تم إعادة تسجيل الحضور بنجاح']);
    }

    public function info(GivenSubject $givenSubject)
    {
        return response($givenSubject->loadMissing(['professor', 'subject', 'cam']));
    }
    public function infoDashboard(GivenSubject $givenSubject)
    {
        $givenSubject->activeWeekProfAttendanceAttended();
        return response($givenSubject->loadMissing(['professor', 'subject', 'cam'])->loadCount(['activeWeekAttendance', 'activeWeekAttendanceAttended']));
    }


    public function studentsDetailed(Request $request, GivenSubject $givenSubject)
    {
        $relation = "attendancesTh";
        if (!$givenSubject->is_thoery === 1) {
            $relation = "attendancesPr";
        }
        return response($givenSubject->takenSubjects()->where('semester_id', '=', auth()->user()->semester_id)->with([$relation, 'student'])->paginate($request->get('perPage')));
        // return response(Student::query()
        //     ->whereHas('takenSubjects', function ($query) use ($subject) {
        //         $query->where('subject_id', $subject->id)->where('semester_id', '=', auth()->user()->semester_id);
        //     })
        //     ->with('takenSubjects', function ($query) use ($subject) {
        //         $query->where('subject_id', $subject->id)->with(['attendancesTh', 'attendancesPr']);
        //     })->paginate($request->get('perPage')));
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
