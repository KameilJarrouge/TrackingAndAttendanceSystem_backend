<?php

namespace App\Http\Controllers;

use App\Models\GivenSubject;
use App\Models\ProfAttendance;
use App\Models\Professor;
use App\Models\Semester;
use App\Models\StdAttendance;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TakenSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

//use Illuminate\Support\Carbon;

class SubjectController extends Controller
{

    public function professors(Request $request, Subject $subject)
    {
//        return response($subject->professors()->withPivot([
//            'id', 'cam_id', 'semester_id',
//            'time', 'day', 'group', 'is_theory',
//            'attendance_pre', 'attendance_post', 'attendance_present'])
//            ->wherePivot('semester_id', '=', auth()->user()->semester_id)
//            ->with('givenSubjects', function ($query) use ($subject) {
//                $query->where('subject_id', $subject->id)->where('id','pivot.id')->with('cam');
//            })
//            ->paginate($request->get('perPage')));
        return response($subject->givenSubjects()->where('semester_id', '=',auth()->user()->semester_id)->with(['cam','professor'])->paginate($request->get('perPage')));

    }

    public function students(Request $request, Subject $subject)
    {
        return response($subject->students()->withPivot([
            'id', 'semester_id',
            'attendance_warning', 'suspended', 'given_subject_id_pr', 'given_subject_id_th'])
            ->wherePivot('semester_id', '=', auth()->user()->semester_id)
//                ->with('givenSubjects',function ($query) use($subject){
//                    $query->where('subject_id',$subject->id)->with('cam');
//                })
            ->paginate($request->get('perPage')));

    }

    public function studentsDetailed(Request $request, Subject $subject)
    {
        return response(Student::query()
            ->whereHas('takenSubjects', function ($query) use ($subject) {
                $query->where('subject_id', $subject->id)->where('semester_id', '=', auth()->user()->semester_id);
            })
            ->with('takenSubjects', function ($query) use ($subject) {
                $query->where('subject_id', $subject->id)->with(['attendancesTh', 'attendancesPr']);
            })->paginate($request->get('perPage')));
    }

    public function professorsDetailed(Request $request, Subject $subject)
    {
        return response(Professor::query()
            ->whereHas('givenSubjects', function ($query) use ($subject) {
                $query->where('subject_id', $subject->id)->where('semester_id', '=', auth()->user()->semester_id);
            })
            ->with(['givenSubjects'=>function ($query) use ($subject) {
                $query->where('subject_id', $subject->id);
            },'givenSubjects.attendances'])->paginate($request->get('perPage')));
    }

    public function addProfessor(Request $request, Subject $subject)
    {
        $semester = Semester::getLatest();
        if ($semester === null) {
            return response(['status' => 'not ok', 'message' => 'الرجاء إدخال فصل من الإعدادات']);
        }
        // the same person can't give two subjects at the same time
        $count = GivenSubject::query()->where('time', $request->get('time'))
            ->where('day', $request->get('day'))
            ->where('semester_id', $semester->id)
            ->where('person_id', $request->get('person_id'))
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
        $attr['person_id'] = $request->get('person_id');
        $attr['subject_id'] = $subject->id;
//        $subject->professors()->attach($request->get('person_id'), $attr);
        $gs = new GivenSubject($attr);
        $gs->save();
//        $gs = GivenSubject::query()->where('subject_id', $subject->id)->where('person_id', $request->get('person_id'))->first();
        $att = array();
        for ($i = 1; $i <= $semester->number_of_weeks; $i++) {

            array_push($att, ['given_subject_id' => $gs->id, 'week' => $i]);


        }
        ProfAttendance::query()->insert($att);

        return response(['status' => 'ok', 'message' => 'تم إضافة المقرر بنجاح']);
    }


    public function addStudent(Request $request, Subject $subject)
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
        $subject->students()->attach($request->get('person_id'), $attributes);

        // create the attendance list
        $ts = TakenSubject::query()->where('subject_id', $subject->id)->where('person_id', $request->get('person_id'))->first();
        $att = array();
        for ($i = 1; $i <= $latestSemester->number_of_weeks; $i++) {

            if ($request->get('theory_id') !== "null") {
                array_push($att, ['taken_subject_id' => $ts->id, 'week' => $i, 'theory' => 1]);
            }
            if ($request->get('practical_id') !== "null") {
                array_push($att, ['taken_subject_id' => $ts->id, 'week' => $i, 'theory' => 0]);

            }
        }
        StdAttendance::query()->insert($att);

        return response(['status' => 'ok', 'message' => 'تم إضافة الطالب بنجاح']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $identifier = "";
        if ($request->get('identifier') !== null) {
            $identifier = $request->get('identifier');
        }
        return response(Subject::query()->where(function ($query) use ($identifier, $request) {
            $query->where('name', 'like', '%' . $identifier . '%')
                ->orWhere('department', 'like', '%' . $identifier . '%');
        })->paginate($request->get('perPage')));
    }

    public function dashboard(Request $request)
    {
        $all = GivenSubject::query()->where('day', '=', now()->dayOfWeek)->with(['professor', 'subject'])->get();
        $split = $all->mapToGroups(function ($givenSubject) {
            $startTime = Carbon::parse($givenSubject->time)->subtract('minutes', $givenSubject->attendance_pre);
            $endTime = Carbon::parse($givenSubject->time)->addMinutes($givenSubject->attendance_post + $givenSubject->attendance_present + ($givenSubject->attendance_extend * 2));
            if (Carbon::now()->isBefore($startTime)) { // future
                return ['future' => $givenSubject];
            }
            if (Carbon::now()->isAfter($endTime)) { // previous
                if ($givenSubject->restart_start_time !== null) {
                    if (Carbon::now()->isAfter(Carbon::parse($givenSubject->restart_start_time)->addMinutes($givenSubject->restart_duration))) { // after restart as well
                        return ['previous' => $givenSubject];
                    }
                } else {
                    return ['previous' => $givenSubject];
                }
            }
            // current
            return ['current' => $givenSubject];
        });
        return response($split);

    }

    public function subjectOptionsStd(Request $request, Student $student)
    {

        return response(Subject::query()->whereDoesntHave('students', function ($query) use ($student) {
            $query->where('people.id', '=', $student->id);
        })->get());
    }

    public function studentOptions(Request $request, Subject $subject)
    {

        return response(Student::query()->where('on_blacklist', '<>', 1)->whereDoesntHave('subjects', function ($query) use ($subject) {
            $query->where('subjects.id', '=', $subject->id);
        })->get());
    }

    public function subjectOptionsProf(Request $request)
    {
        return response(Subject::all());
//        return response(Subject::query()->whereDoesntHave('professors', function ($query) use($professor) {
//            $query->where('people.id', '=', $professor->id);
//        })->get());
    }

    public function givenSubjectOptionsTh(Subject $subject)
    {
        return response($subject->givenSubjects()->with('professor')->where('is_theory', '=', 1)->get());
    }

    public function givenSubjectOptionsPr(Subject $subject)
    {
        return response($subject->givenSubjects()->with('professor')->where('is_theory', '=', 0)->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Subject::query()->create([
            'name' => $request->get('name'),
            'department' => $request->get('department'),
        ]);
        return response(['status' => 'ok', 'message' => 'تم إضافة المقرر']);

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Subject $subject
     * @return \Illuminate\Http\Response
     */
    public function show(Subject $subject)
    {
        return response($subject);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Subject $subject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subject $subject)
    {
        $subject->update([
            'name' => $request->get('name'),
            'department' => $request->get('department'),
        ]);
        return response(['status' => 'ok', 'message' => 'تم تعديل المقرر']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Subject $subject
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();
        return response(['status' => 'ok', 'message' => 'تم إزالة المقرر']);

    }
}
