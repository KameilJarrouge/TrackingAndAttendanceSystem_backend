<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TakenSubject;
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

    public function takenSubjects(Request $request, Student $student){
        return response($student->subjects()->withPivot(['id','given_subject_id_th','given_subject_id_pr','attendance_warning','suspended'])->wherePivot('semester_id' ,'=',auth()->user()->semester_id)->paginate($request->get('perPage')));
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

    public function addSubject(Request $request, Student $student){
        $attributes = array();
        $latestSemesterId = Semester::getLatest();
        if ($latestSemesterId === null){
            return response(['status' => 'not ok', 'message'=>'يرجى إضافة فصل من الإعدادات']);
        }
        $attributes['semester_id'] = $latestSemesterId->id;
        if($request->get('theory_id') !== "null"){
            $attributes['given_subject_id_th'] = $request->get('theory_id');
        }
        if($request->get('practical_id') !== "null"){
            $attributes['given_subject_id_pr'] = $request->get('practical_id');
        }
        $student->subjects()->attach($request->get('subject_id'),$attributes);
        return response(['status'=> 'ok', 'message' => 'تم إضافة المقرر بنجاح']);
    }

    public function updateSubject(Request $request, TakenSubject $takenSubject){
        $attributes = array();
        if($request->get('theory_id') !== "null"){
            $attributes['given_subject_id_th'] = $request->get('theory_id');
        }else{
            $attributes['given_subject_id_th'] = null;

        }
        if($request->get('practical_id') !== "null"){
            $attributes['given_subject_id_pr'] = $request->get('practical_id');
        }else{
            $attributes['given_subject_id_pr'] = null;
        }
        $takenSubject->update($attributes);

        return response(['status'=> 'ok', 'message' => 'تم تغيير المقرر بنجاح']);
    }

    public function removeSubject(Request $request, TakenSubject $takenSubject){
        $takenSubject->delete();
        return response(['status'=> 'ok', 'message' => 'تم إزالة المقرر بنجاح']);

    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        return response($student);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        //
    }
}
