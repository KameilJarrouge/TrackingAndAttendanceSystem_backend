<?php

namespace App\Http\Controllers;

use App\Models\GivenSubject;
use App\Models\ProfAttendance;
use App\Models\Professor;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Http\Request;

class ProfessorController extends Controller
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

    public function professorOptions()
    {
        return response(Professor::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Professor $professor
     * @return \Illuminate\Http\Response
     */
    public function show(Professor $professor)
    {
        return response($professor);
    }


    public function givenSubjects(Request $request, Professor $professor)
    {
//        return response($professor->subjects()->withPivot([
//            'id', 'cam_id', 'semester_id',
//            'time', 'day', 'group', 'is_theory',
//            'attendance_pre', 'attendance_post', 'attendance_present'])
//            ->wherePivot('semester_id', '=', auth()->user()->semester_id)
//            ->with('givenSubjects', function ($query) use ($professor) {
//                $query->where('person_id', $professor->id)->with('cam');
//            })
//            ->paginate($request->get('perPage')));
        return response($professor->givenSubjects()->where('semester_id', '=',auth()->user()->semester_id)->with(['cam','subject'])->paginate($request->get('perPage')));

    }

    public function givenSubjectsDetailed(Request $request, Professor $professor)
    {
        return response(Subject::query()
            ->whereHas('givenSubjects', function ($query) use ($professor) {
                $query->where('person_id', $professor->id)->where('semester_id', '=', auth()->user()->semester_id);
            })
            ->with('givenSubjects', function ($query) use ($professor) {
                $query->where('person_id', $professor->id)->with(['attendances']);
            })->paginate($request->get('perPage')));
    }

    public function addSubject(Request $request, Professor $professor)
    {
        $semester = Semester::getLatest();
        if ($semester === null) {
            return response(['status' => 'not ok', 'message' => 'الرجاء إدخال فصل من الإعدادات']);
        }
        $count = GivenSubject::query()->where('time', $request->get('time'))
            ->where('day', $request->get('day'))
            ->where('semester_id', $semester->id)
            ->where('person_id', $professor->id)
            ->count();
        if ($count !== 0) {
            return response(['status' => 'not ok', 'message' => 'يتواجد مقرر بنفس الوقت واليوم']);
        }

        $attr = array(
            'time' => $request->get('time'),
            'day' => $request->get('day'),
            'is_theory' => $request->get('is_theory'),
            'attendance_pre' => $request->get('attendance_pre'),
            'attendance_post' => $request->get('attendance_post'),
            'attendance_present' => $request->get('attendance_present'),
            'semester_id' => $semester->id);
        if ($request->get('cam_id') !== null) {
            // unique day and time per camera
            $count = GivenSubject::query()->where('time', $request->get('time'))
                ->where('day', $request->get('day'))
                ->where('semester_id', $semester->id)
                ->where('cam_id', $request->get('cam_id'))->count();
            if ($count !== 0) {
                return response(['status' => 'not ok', 'message' => 'يتواجد مقرر بنفس الوقت واليوم والموقع']);
            }
            $attr['cam_id'] = $request->get('cam_id');
        }
        if ($request->get('group') !== null) {
            $attr['group'] = $request->get('group');

        }
        $attr['subject_id'] = $request->get('subject_id');
        $attr['person_id'] = $professor->id;
//        $professor->subjects()->attach($request->get('subject_id'), $attr);
        $gs = new GivenSubject($attr);
        $gs->save();
        // create the attendance list
//        $gs = GivenSubject::query()->where('person_id',$professor->id)->where('subject_id',$request->get('subject_id'))->first();
        $att = array();
        for ($i = 1 ; $i <= $semester->number_of_weeks; $i++){
            array_push($att,['given_subject_id' => $gs->id,'week' => $i]);


        }
        ProfAttendance::query()->insert($att);
        return response(['status' => 'ok', 'message' => 'تم إضافة المقرر بنجاح']);
    }

    public function updateSubject(Request $request, GivenSubject $givenSubject)
    {
        $semester = Semester::getLatest();
        $day_time_is_same = $givenSubject->day === $request->get('day') && $givenSubject->time === $request->get('time');

        if (!$day_time_is_same) {
            $count = GivenSubject::query()->where('time', $request->get('time'))
                ->where('day', $request->get('day'))
                ->where('semester_id', $semester->id)
                ->where('person_id', $givenSubject->person_id)
                ->count();
            if ($count !== 0) {
                return response(['status' => 'not ok', 'message' => 'يتواجد مقرر بنفس الوقت واليوم']);
            }
        }

        $attr = array(
            'time' => $request->get('time'),
            'day' => $request->get('day'),
            'is_theory' => $request->get('is_theory'),
            'attendance_pre' => $request->get('attendance_pre'),
            'attendance_post' => $request->get('attendance_post'),
            'attendance_present' => $request->get('attendance_present'),
        );
        if ($request->get('cam_id') !== null) {
            // unique day and time per camera
            $count = GivenSubject::query()->where('time', $request->get('time'))
                ->where('day', $request->get('day'))
                ->where('semester_id', $semester->id)
                ->where('cam_id', $request->get('cam_id'))->count();
            if ($count !== 0) {
                return response(['status' => 'not ok', 'message' => 'يتواجد مقرر بنفس الوقت واليوم والموقع']);
            }
            $attr['cam_id'] = $request->get('cam_id');
        }else{
            $attr['cam_id'] = null;
        }
        if ($request->get('group') !== null) {
            $attr['group'] = $request->get('group');

        }
        $givenSubject->update($attr);
        return response(['status' => 'ok', 'message' => 'تم تعديل المقرر بنجاح']);

    }

    public function removeSubject(Request $request, GivenSubject $givenSubject)
    {
        $givenSubject->delete();
        return response(['status' => 'ok', 'message' => 'تم إزالة المقرر بنجاح']);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Professor $professor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Professor $professor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Professor $professor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Professor $professor)
    {
        //
    }
}
