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

    public function info(Request $request, TakenSubject $takenSubject)
    {

        return response([
            'subject' => $takenSubject->subject,
            'student' => $takenSubject->student,
            'theory' => $takenSubject->thSubjectGiven()->with('professor')->first(),
            'practical' => $takenSubject->prSubjectGiven()->with('professor')->first(),
        ]);
    }

    public function warnings(Request $request)
    {
        // return response($request);
        $query = TakenSubject::query()->where('semester_id', auth()->user()->semester_id)->with(['student', 'subject']);
        if ($request->get('identifier') !== "null") {
            $query = $query->whereHas('student', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->get('identifier') . '%')
                    ->orWhere('id_number', 'like', '%' . $request->get('identifier')  . '%');
            });
        }
        switch ($request->get('scope')) {
            case 'warnings':
                $query = $query->where('attendance_warning', 1)->where('suspended', 0);
                break;
            case 'suspensions':
                $query = $query->where('suspended', 1);
                break;
            default:
                $query = $query->where('attendance_warning', 1)->orWhere('suspended', 1);
                break;
        }
        return response($query->orderBy('subject_id')->get());
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
