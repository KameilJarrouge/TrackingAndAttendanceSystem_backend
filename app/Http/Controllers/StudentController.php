<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\StdAttendance;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TakenSubject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Array_;

class StudentController extends Controller
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

    public function takenSubjects(Request $request, Student $student)
    {
        return response($student->subjects()->withPivot(['id', 'given_subject_id_th', 'given_subject_id_pr', 'attendance_warning', 'suspended'])->wherePivot('semester_id', '=', auth()->user()->semester_id)->paginate($request->get('perPage')));
    }

    public function takenSubjectsDetailed(Request $request, Student $student)
    {
        return response(Subject::query()
            ->whereHas('takenSubjects', function ($query) use ($student) {
                $query->where('person_id', $student->id)->where('semester_id', '=', auth()->user()->semester_id);
            })
            ->with('takenSubjects', function ($query) use ($student) {
                $query->where('person_id', $student->id)->with(['attendancesTh', 'attendancesPr']);
            })->paginate($request->get('perPage')));
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

    public function addSubject(Request $request, Student $student)
    {
        $attributes = array();

        $latestSemester = Semester::getLatest();
        if ($latestSemester === null) {
            return response(['status' => 'not ok', 'message' => 'يرجى إضافة فصل من الإعدادات']);
        }
        $attributes['semester_id'] = $latestSemester->id;
        if ($request->get('theory_id') !== "null") {
            $attributes['given_subject_id_th'] = $request->get('theory_id');
        }
        if ($request->get('practical_id') !== "null") {
            $attributes['given_subject_id_pr'] = $request->get('practical_id');
        }
        $student->subjects()->attach($request->get('subject_id'), $attributes);

        // create the attendance list
        $ts = TakenSubject::query()->where('person_id',$student->id)->where('subject_id',$request->get('subject_id'))->first();
        $att = array();

        for ($i = 1 ; $i <= $latestSemester->number_of_weeks; $i++){

            if ($request->get('theory_id') !== "null") {
                array_push($att,['taken_subject_id' => $ts->id,'week' => $i,'theory' => 1]);
            }
            if ($request->get('practical_id') !== "null") {
                array_push($att,['taken_subject_id' => $ts->id,'week' => $i,'theory' => 0]);

            }
        }
        StdAttendance::query()->insert($att);
        return response(['status' => 'ok', 'message' => 'تم إضافة المقرر بنجاح']);
    }

    public function updateSubject(Request $request, TakenSubject $takenSubject)
    {
        $attributes = array();
        if ($request->get('theory_id') !== "null") {
            $attributes['given_subject_id_th'] = $request->get('theory_id');
        } else {
            $attributes['given_subject_id_th'] = null;

        }
        if ($request->get('practical_id') !== "null") {
            $attributes['given_subject_id_pr'] = $request->get('practical_id');
        } else {
            $attributes['given_subject_id_pr'] = null;
        }
        $takenSubject->update($attributes);

        return response(['status' => 'ok', 'message' => 'تم تغيير المقرر بنجاح']);
    }

    public function removeSubject(Request $request, TakenSubject $takenSubject)
    {
        $takenSubject->delete();
        return response(['status' => 'ok', 'message' => 'تم إزالة المقرر بنجاح']);

    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Student $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        return response($student);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Student $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Student $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        //
    }
}
