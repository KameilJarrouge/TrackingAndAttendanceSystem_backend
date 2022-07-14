<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
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
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return response(['settings'=>Setting::first(), 'currentSemester'=>Semester::query()->latest()->first()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function updateAttendance(Request $request)
    {
        $setting = Setting::first();
        $setting->attendance_pre = $request->get('attendance_pre');
        $setting->attendance_post = $request->get('attendance_post');
        $setting->attendance_present = $request->get('attendance_present');
        $setting->save();
        return response(['status'=>'ok','message'=>'تم تعديل مواعيد تسجيل الحضور']);


    }
    public function updateThreshold(Request $request)
    {
        $setting = Setting::first();
        $setting->warning_thresh = $request->get('warning_thresh');
        $setting->suspension_thresh = $request->get('suspension_thresh');
        $setting->save();
        return response(['status'=>'ok','message'=>'تم تعديل حدود الحرمانات']);
    }
    public function updateSms(Request $request)
    {
        $setting = Setting::first();
        $setting->sms_number = $request->get('sms_number');
        $setting->should_send_sms = $request->get('should_send_sms')?1:0;
        $setting->save();
        return response(['status'=>'ok','message'=>'تم تعديل إعدادات الإنذارات']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
