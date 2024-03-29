<?php


use App\Http\Controllers\CamController;
use App\Http\Controllers\GivenSubjectController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProfAttendanceController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StdAttendanceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TakenSubjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::get('/professors/userable-professors', [ProfessorController::class, 'userableProfessors']);


// =================================================================================================== Auth
Route::post('/login', [UserController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/python-data-gs', [GivenSubjectController::class, 'pythonGivenSubjects']);
    Route::get('/python-shutdown', [UserController::class, 'shutdownPython']);
    Route::get('/python-data-cams', [CamController::class, 'pythonCams']);
    Route::get('/python-data-people', [PersonController::class, 'pythonPeople']);

    Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:web');
    Route::get('/settings', [SettingController::class, 'show']);

    // =================================================================================================== Semester
    Route::get('/semesters', [SemesterController::class, 'index']);
    Route::post('/semesters/add', [SemesterController::class, 'store']);
    Route::put('/semesters/preview-current', [SemesterController::class, 'previewCurrent']);
    Route::get('/semesters/{semester}', [SemesterController::class, 'show']);
    Route::put('/semesters/{semester}/update', [SemesterController::class, 'update']);
    Route::put('/semesters/{semester}/preview', [SemesterController::class, 'preview']);
    Route::delete('/semesters/{semester}/delete', [SemesterController::class, 'destroy']);

    // =================================================================================================== Settings
    Route::put('/settings/update-attendance', [SettingController::class, 'updateAttendance']);
    Route::put('/settings/update-threshold', [SettingController::class, 'updateThreshold']);
    Route::put('/settings/update-sms', [SettingController::class, 'updateSms']);

    // =================================================================================================== people
    Route::get('/people', [PersonController::class, 'index']);
    Route::post('/people/add', [PersonController::class, 'store']);
    Route::get('/people/{person}', [PersonController::class, 'show']);
    Route::get('/people/{person}/show', [PersonController::class, 'view']);
    Route::get('/people/{person}/logs', [PersonController::class, 'logs']);
    Route::put('/people/{person}/update', [PersonController::class, 'update']);
    Route::delete('/people/{person}/delete', [PersonController::class, 'destroy']);

    // =================================================================================================== people images
    Route::post('/images/{person}/store', [ImageController::class, 'store']);
    Route::post('/images/{image}/update', [ImageController::class, 'update']);
    Route::delete('/images/{id}/delete', [ImageController::class, 'destroy']);

    // =================================================================================================== Subjects
    Route::get('/subjects', [SubjectController::class, 'index']);
    Route::get('/subjects/dashboard', [SubjectController::class, 'dashboard']);
    Route::post('/subjects/add', [SubjectController::class, 'store']);
    Route::get('/subjects/{subject}', [SubjectController::class, 'show']);
    Route::put('/subjects/{subject}/update', [SubjectController::class, 'update']);
    Route::delete('/subjects/{subject}/delete', [SubjectController::class, 'destroy']);

    Route::get('/subjects/students/{student}/options', [SubjectController::class, 'subjectOptionsStd']);
    Route::get('/subjects/professors/options', [SubjectController::class, 'subjectOptionsProf']);
    Route::post('/subjects/{subject}/add-professor', [SubjectController::class, 'addProfessor']);
    Route::post('/subjects/{subject}/add-student', [SubjectController::class, 'addStudent']);
    Route::get('/subjects/{subject}/professors', [SubjectController::class, 'professors']);
    Route::get('/subjects/{subject}/students', [SubjectController::class, 'students']);
    Route::get('/subjects/{subject}/students-attendance-detailed', [SubjectController::class, 'studentsDetailed']);
    Route::get('/subjects/{subject}/professors-attendance-detailed', [SubjectController::class, 'professorsDetailed']);

    // =================================================================================================== Cams
    Route::get('/cameras', [CamController::class, 'index']);
    Route::get('/location-options', [CamController::class, 'options']);
    Route::get('/cameras/options', [CamController::class, 'camOptions']);
    Route::post('/cameras/add', [CamController::class, 'store']);
    Route::get('/cameras/{cam}', [CamController::class, 'show']);
    Route::post('/cameras/{cam}/add-schedule', [CamController::class, 'addSchedule']);
    Route::put('/cameras/{cam}/update', [CamController::class, 'update']);
    Route::get('/cameras/{cam}/log', [CamController::class, 'log']);
    Route::get('/cameras/{cam}/schedule', [CamController::class, 'schedule']);
    Route::delete('/cameras/{cam}/delete', [CamController::class, 'destroy']);
    Route::post('/cameras/{cam}/log-python', [CamController::class, 'logPython']);

    // =================================================================================================== Schedule
    Route::delete('/schedules/{schedule}/delete', [ScheduleController::class, 'destroy']);
    Route::put('/schedules/{schedule}/update', [ScheduleController::class, 'update']);
    Route::get('/schedules/{schedule}', [ScheduleController::class, 'show']);

    // =================================================================================================== Students
    Route::get('/students/{student}', [StudentController::class, 'show']);
    Route::get('/students/{student}/subjects', [StudentController::class, 'takenSubjects']);
    Route::get('/students/{student}/taken-subjects', [StudentController::class, 'takenSubjectsDetailed']);
    Route::post('/students/{student}/add-subject', [StudentController::class, 'addSubject']);
    Route::get('/students/subjects/{subject}/options', [SubjectController::class, 'studentOptions']);

    // =================================================================================================== Taken Subjects
    Route::put('/taken-subjects/{takenSubject}/update-subject', [StudentController::class, 'updateSubject']);
    Route::delete('/taken-subjects/{takenSubject}/remove-subject', [StudentController::class, 'removeSubject']);
    Route::get('/taken-subjects/{takenSubject}/info', [TakenSubjectController::class, 'info']);
    Route::get('/taken-subjects/warnings', [TakenSubjectController::class, 'warnings']);
    Route::put('/taken-subjects/calculate-absence', [StudentController::class, 'calculateAbsence']);

    // =================================================================================================== Professors
    Route::get('/professors/userable-professors', [ProfessorController::class, 'userableProfessors']);
    Route::get('/professors/{professor}', [ProfessorController::class, 'show']);
    Route::get('/professors/{professor}/subjects', [ProfessorController::class, 'givenSubjects']);
    Route::get('/professors/{professor}/dashboard-subjects', [ProfessorController::class, 'dashboardSubjects']);
    Route::get('/professors/{professor}/given-subjects', [ProfessorController::class, 'givenSubjectsDetailed']);
    Route::post('/professors/{professor}/add-subject', [ProfessorController::class, 'addSubject']);
    Route::get('/professors/subjects/options', [ProfessorController::class, 'professorOptions']);


    // =================================================================================================== Given Subjects
    Route::get('/given-subjects/{subject}/theory', [SubjectController::class, 'givenSubjectOptionsTh']);
    Route::get('/given-subjects/{subject}/practical', [SubjectController::class, 'givenSubjectOptionsPr']);
    Route::put('/given-subjects/{givenSubject}/update-subject', [ProfessorController::class, 'updateSubject']);
    Route::get('/given-subjects/{givenSubject}/students-attendance-detailed', [GivenSubjectController::class, 'studentsDetailed']);
    Route::get('/given-subjects/{givenSubject}/students-attendance-detailed-theory', [GivenSubjectController::class, 'studentsDetailedTh']);
    Route::get('/given-subjects/{givenSubject}/students-attendance-detailed-practical', [GivenSubjectController::class, 'studentsDetailedPr']);

    Route::delete('/given-subjects/{givenSubject}/remove-subject', [ProfessorController::class, 'removeSubject']);
    Route::get('/given-subjects/{givenSubject}/info', [GivenSubjectController::class, 'info']);
    Route::get('/given-subjects/{givenSubject}/infoDashboard', [GivenSubjectController::class, 'infoDashboard']);
    Route::get('/given-subjects/{givenSubject}', [GivenSubjectController::class, 'show']);
    Route::put('/given-subjects/{givenSubject}/restart', [GivenSubjectController::class, 'restart']);
    Route::put('/given-subjects/{givenSubject}/extend', [GivenSubjectController::class, 'extend']);
    Route::put('/given-subjects/{givenSubject}/reset', [GivenSubjectController::class, 'reset']);

    Route::post('/given-subjects/{givenSubject}/attendance-python', [GivenSubjectController::class, 'attendancePython']);
    Route::post('/given-subjects/{givenSubject}/visit-this-week', [GivenSubjectController::class, 'visitWeek']);
    Route::put('/given-subjects/{givenSubject}/skip-this-week', [GivenSubjectController::class, 'skipWeek']);
    Route::put('/given-subjects/{givenSubject}/unskip-this-week', [GivenSubjectController::class, 'unSkipWeek']);
    Route::post('/given-subjects/{givenSubject}/skip-this-students-week', [GivenSubjectController::class, 'skipStdWeek']);
    Route::get('/given-subjects/{givenSubject}/python-subject', [GivenSubjectController::class, 'pythonSubject']);




    // =================================================================================================== Logs
    Route::put('/logs/{log}/ignore', [LogController::class, 'ignore']);
    Route::get('/logs/tracking', [LogController::class, 'tracking']);
    // =================================================================================================== Student Attendance
    Route::put('/student-attendance/{stdAttendance}/update', [StdAttendanceController::class, 'update']);
    // =================================================================================================== Professor Attendance
    Route::put('/professor-attendance/{profAttendance}/update', [ProfAttendanceController::class, 'update']);
    // =================================================================================================== Users
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users/add', [UserController::class, 'store']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}/update', [UserController::class, 'update']);
    Route::delete('/users/{user}/delete', [UserController::class, 'destroy']);
    // =================================================================================================== Holidays
    Route::get('/holidays', [HolidayController::class, 'index']);
    Route::post('/holidays/add', [HolidayController::class, 'store']);
    Route::get('/holidays/{holiday}', [HolidayController::class, 'show']);
    Route::put('/holidays/{holiday}/update', [HolidayController::class, 'update']);
    Route::delete('/holidays/{holiday}/delete', [HolidayController::class, 'destroy']);
});
