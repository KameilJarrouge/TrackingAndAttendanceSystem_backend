<?php


use App\Http\Controllers\CamController;
use App\Http\Controllers\GivenSubjectController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\SettingController;
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


// =================================================================================================== Auth
Route::post('/login',[UserController::class,'login']);
Route::middleware('auth:sanctum')->group(function (){

    Route::post('/logout',[UserController::class,'logout'])->middleware('auth:web');

    // =================================================================================================== Semester
    Route::get('/semesters',[SemesterController::class,'index']);
    Route::post('/semesters/add',[SemesterController::class,'store']);
    Route::get('/semesters/{semester}',[SemesterController::class,'show']);
    Route::put('/semesters/{semester}/update',[SemesterController::class,'update']);
    Route::put('/semesters/{semester}/preview',[SemesterController::class,'preview']);
    Route::delete('/semesters/{semester}/delete',[SemesterController::class,'destroy']);

    // =================================================================================================== Settings
    Route::get('/settings',[SettingController::class,'show']);
    Route::put('/settings/update-attendance',[SettingController::class,'updateAttendance']);
    Route::put('/settings/update-threshold',[SettingController::class,'updateThreshold']);
    Route::put('/settings/update-sms',[SettingController::class,'updateSms']);

    // =================================================================================================== people
    Route::get('/people',[PersonController::class,'index']);
    Route::post('/people/add',[PersonController::class,'store']);
    Route::get('/people/{person}',[PersonController::class,'show']);
    Route::get('/people/{person}/show',[PersonController::class,'view']);
    Route::get('/people/{person}/logs',[PersonController::class,'logs']);
    Route::put('/people/{person}/update',[PersonController::class,'update']);
    Route::delete('/people/{person}/delete',[PersonController::class,'destroy']);

    // =================================================================================================== people images
    Route::post('/images/{person}/store',[ImageController::class,'store']);
    Route::post('/images/{image}/update',[ImageController::class,'update']);
    Route::delete('/images/{id}/delete',[ImageController::class,'destroy']);

    // =================================================================================================== Subjects
    Route::get('/subjects',[SubjectController::class,'index']);
    Route::post('/subjects/add',[SubjectController::class,'store']);
    Route::get('/subjects/{subject}',[SubjectController::class,'show']);
    Route::put('/subjects/{subject}/update',[SubjectController::class,'update']);
    Route::delete('/subjects/{subject}/delete',[SubjectController::class,'destroy']);

    Route::get('/subjects/students/{student}/options',[SubjectController::class, 'subjectOptionsStd']);
    Route::get('/subjects/professors/options',[SubjectController::class, 'subjectOptionsProf']);
    Route::post('/subjects/{subject}/add-professor',[SubjectController::class, 'addProfessor']);
    Route::post('/subjects/{subject}/add-student',[SubjectController::class, 'addStudent']);
    Route::get('/subjects/{subject}/professors',[SubjectController::class,'professors']);
    Route::get('/subjects/{subject}/students',[SubjectController::class,'students']);

    // =================================================================================================== Cams
    Route::get('/cameras',[CamController::class, 'index']);
    Route::get('/location-options',[CamController::class, 'options']);
    Route::get('/cameras/options',[CamController::class, 'camOptions']);
    Route::post('/cameras/add',[CamController::class, 'store']);
    Route::get('/cameras/{cam}',[CamController::class, 'show']);
    Route::put('/cameras/{cam}/update',[CamController::class, 'update']);
    Route::get('/cameras/{cam}/log',[CamController::class, 'log']);
    Route::delete('/cameras/{cam}/delete',[CamController::class, 'destroy']);

    // =================================================================================================== Students
    Route::get('/students/{student}',[StudentController::class,'show']);
    Route::get('/students/{student}/subjects',[StudentController::class,'takenSubjects']);
    Route::post('/students/{student}/add-subject',[StudentController::class,'addSubject']);
    Route::get('/students/subjects/{subject}/options',[SubjectController::class, 'studentOptions']);

    // =================================================================================================== Taken Subjects
    Route::put('/taken-subjects/{takenSubject}/update-subject',[StudentController::class,'updateSubject']);
    Route::delete('/taken-subjects/{takenSubject}/remove-subject',[StudentController::class,'removeSubject']);
    Route::get('/taken-subjects/{takenSubject}/info',[TakenSubjectController::class,'info']);

    // =================================================================================================== Professors
    Route::get('/professors/{professor}',[ProfessorController::class,'show']);
    Route::get('/professors/{professor}/subjects',[ProfessorController::class,'givenSubjects']);
    Route::post('/professors/{professor}/add-subject',[ProfessorController::class,'addSubject']);
    Route::get('/professors/subjects/options',[ProfessorController::class,'professorOptions']);

    // =================================================================================================== Given Subjects
    Route::get('/given-subjects/{subject}/theory',[SubjectController::class, 'givenSubjectOptionsTh']);
    Route::get('/given-subjects/{subject}/practical',[SubjectController::class, 'givenSubjectOptionsPr']);
    Route::put('/given-subjects/{givenSubject}/update-subject',[ProfessorController::class,'updateSubject']);
    Route::delete('/given-subjects/{givenSubject}/remove-subject',[ProfessorController::class,'removeSubject']);
    Route::get('/given-subjects/{givenSubject}/info',[GivenSubjectController::class,'info']);
    Route::get('/given-subjects/{givenSubject}',[GivenSubjectController::class,'show']);



});



