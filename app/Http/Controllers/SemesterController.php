<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response(Semester::query()->orderByDesc('created_at')->paginate($request->get('perPage')));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $semester = Semester::query()->create([
            'name_identifier' => $request->get('name_identifier'),
            'year' => $request->get('year'),
            'semester_start' => $request->get('semester_start'),
            'number_of_weeks' => $request->get('number_of_weeks')
        ]);
        $user = auth()->user();
        $user->semester_id = $semester->id;
        $user->save();
        return response(['status'=>'ok','message'=>'تم إضافة فصل جديد','user' => $user]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function show(Semester $semester)
    {
        return response($semester);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Semester $semester)
    {
        $semester->update([
            'name_identifier' => $request->get('name_identifier'),
            'year' => $request->get('year'),
            'semester_start' => $request->get('semester_start'),
            'number_of_weeks' => $request->get('number_of_weeks')
        ]);
        return response(['status'=>'ok','message'=>'تم تعديل الفصل']);
    }

    public function preview(Request $request, Semester $semester){
        auth()->user()->semester_id = $semester->id;
        auth()->user()->save();
        return response(['status'=>'ok','message'=>'معاينة الفصل:' . '  ' . $semester->name_identifier . '  ' . $semester->year, 'user' => auth()->user()]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function destroy(Semester $semester)
    {
        $semester->delete();
//        update the semester id on the auth user
        $user = auth()->user();
        $sem = Semester::getLatest();
        if($sem === null){
            $user->semester_id = null ;
        }else{
            $user->semester_id = $sem->id;
        }
        $user->save();
        return response(['status'=>'ok','message'=>'تم إزالة الفصل','user'=>$user]);

    }
}
