<?php

namespace App\Http\Controllers;

use App\Models\TakenSubject;
use Illuminate\Http\Request;

class TakenSubjectController extends Controller
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

    public function info(Request $request,TakenSubject $takenSubject){

        return response([
            'subject' => $takenSubject->subject,
            'student' => $takenSubject->student,
            'theory' => $takenSubject->thSubjectGiven()->with('professor')->first(),
            'practical' => $takenSubject->prSubjectGiven()->with('professor')->first(),
        ]);
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
     * @param  \App\Models\TakenSubject  $takenSubject
     * @return \Illuminate\Http\Response
     */
    public function show(TakenSubject $takenSubject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TakenSubject  $takenSubject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TakenSubject $takenSubject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TakenSubject  $takenSubject
     * @return \Illuminate\Http\Response
     */
    public function destroy(TakenSubject $takenSubject)
    {
        //
    }
}
