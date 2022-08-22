<?php

namespace App\Http\Controllers;

use App\Events\GsExtendEvent;
use App\Events\GsRestartEvent;
use App\Models\GivenSubject;
use App\Models\ProfAttendance;
use App\Models\Semester;
use App\Models\StdAttendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

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


    public function pythonGivenSubjects()
    {
        $semester = Semester::getLatest();
        if (!$semester) return response(["message" => "no semester"]);
        $gs = GivenSubject::query()
            ->where('day', now()->dayOfWeek)
            ->where('semester_id', $semester->id)
            ->whereHas('cam')
            ->with(['cam', 'professor', 'professor.images'])->get();

        $split = $gs->filter(function ($givenSubject) {
            $endTime = Carbon::parse($givenSubject->time)->addMinutes($givenSubject->attendance_post + $givenSubject->attendance_present + ($givenSubject->attendance_extend));
            if (Carbon::now()->isAfter($endTime)) { // previous
                if ($givenSubject->restart_start_time !== null) {
                    if (!Carbon::now()->isAfter(Carbon::parse($givenSubject->restart_start_time)->addMinutes($givenSubject->restart_duration))) { // after restart as well
                        return $givenSubject;
                    }
                }
            } else {
                return $givenSubject;
            }
        });



        foreach ($split as $givenSubject) {
            $givenSubject->std_attendances = $givenSubject->activeWeekAttendance()->with(['takenSubject', 'takenSubject.student', 'takenSubject.student.images'])->get();
            $givenSubject->prof_attendances = $givenSubject->activeWeekAttendanceProfessor()->first('id');
        }
        return response($split);
    }

    public function attendancePython(Request $request, GivenSubject $givenSubject)
    {
        // return response($request->hasFile("frame") ? "true" : "false", 500);
        // return response($request->all(), 500);
        $destinationPath = storage_path('app/public/attendance');
        $imageLink = "";
        if ($request->hasFile('frame')) {
            $image = $request->file('frame');
            $name = Carbon::now()->timestamp . '.' . $image->extension();
            $img = Image::make($image->path());
            $img->save($destinationPath . '/' . $name);
            $imageLink = asset('storage/attendance/' . $name);
        }
        if ($request->get('profAttId') !== "-1") {
            ProfAttendance::find($request->get('profAttId'))->update(['attended' => 1, 'verification_img' => $imageLink]);
        }
        if ($request->get('isAttended')) {
            foreach ($request->get('recognitions') as $stdAttendanceId) {
                StdAttendance::query()->find($stdAttendanceId)->update([
                    'verification_img' => $imageLink,
                    'attended' => 1,
                ]);
            }
        } else {
            foreach ($request->get('recognitions') as $stdAttendanceId) {
                StdAttendance::query()->find($stdAttendanceId)->update([
                    'verification_img' => $imageLink,
                    'present' => 1,
                ]);
            }
        }
    }

    public function visitWeek(Request $request, GivenSubject $givenSubject)
    {
        $givenSubject->activeWeekAttendanceProfessor()->update(['visited' => 1]);

        $attIds = $request->get('attIds');
        StdAttendance::whereIn('id', $attIds)->update(['visited' => 1]);
    }

    public function skipStdWeek(Request $request, GivenSubject $givenSubject)
    {
        $attIds = $request->get('attIds');
        StdAttendance::whereIn('id', $attIds)->update(['skipped' => 1]);
    }
    public function skipWeek(Request $request, GivenSubject $givenSubject)
    {
        $givenSubject->activeWeekAttendanceProfessor()->update(['skipped' => 1]);
        $givenSubject->activeWeekAttendance()->update(['skipped' => 1]);
        // $attIds = $request->get('attIds');
        // StdAttendance::whereIn('id', $attIds)->update(['skipped' => 1]);
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


    public function extend(Request $request, GivenSubject $givenSubject)
    {
        $givenSubject->update(['attendance_extend' => $request->get('extend_duration')]);
        broadcast(new GsExtendEvent($givenSubject->id, $request->get('extend_duration')));
        return response(['status' => 'ok', 'message' => 'تم تمديد الحضور بنجاح']);
    }
    public function resetExtension(Request $request, GivenSubject $givenSubject)
    {
        $givenSubject->update(['attendance_extend' => 0]);
    }

    public function restart(Request $request, GivenSubject $givenSubject)
    {
        $givenSubject->update(['restart_start_time' => $request->get('restart_start_time'), 'restart_duration' => $request->get('restart_duration')]);
        broadcast(new GsRestartEvent($givenSubject->id, $request->get('restart_start_time'), $request->get('restart_duration')));

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
    public function studentsDetailedTh(Request $request, GivenSubject $givenSubject)
    {

        return response($givenSubject->takenSubjectsTh()->where('semester_id', '=', auth()->user()->semester_id)->with(["attendancesTh", 'student'])->paginate($request->get('perPage')));
        // return response(Student::query()
        //     ->whereHas('takenSubjects', function ($query) use ($subject) {
        //         $query->where('subject_id', $subject->id)->where('semester_id', '=', auth()->user()->semester_id);
        //     })
        //     ->with('takenSubjects', function ($query) use ($subject) {
        //         $query->where('subject_id', $subject->id)->with(['attendancesTh', 'attendancesPr']);
        //     })->paginate($request->get('perPage')));
    }
    public function studentsDetailedPr(Request $request, GivenSubject $givenSubject)
    {

        return response($givenSubject->takenSubjectsPr()->where('semester_id', '=', auth()->user()->semester_id)->with(["attendancesPr", 'student'])->paginate($request->get('perPage')));
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
