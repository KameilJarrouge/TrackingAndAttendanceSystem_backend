<?php


use App\Http\Controllers\SemesterController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::post('/login',[UserController::class,'login']);
Route::post('/logout',[UserController::class,'logout']);
Route::get('/semesters',[SemesterController::class,'index']);
Route::post('/semesters/add',[SemesterController::class,'store']);
Route::get('/semesters/{semester}',[SemesterController::class,'show']);
Route::put('/semesters/{semester}/update',[SemesterController::class,'update']);
Route::put('/semesters/{semester}/preview',[SemesterController::class,'preview']);
Route::delete('/semesters/{semester}/delete',[SemesterController::class,'destroy']);

Route::middleware('auth:sanctum')->group(function (){

});



