<?php

namespace App\Http\Controllers;

use App\Models\Cam;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ScheduleController extends Controller
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Schedule $schedule
     * @return \Illuminate\Http\Response
     */
    public function show(Schedule $schedule)
    {
        return response($schedule);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Schedule $schedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Schedule $schedule)
    {
        $cam = Cam::query()->find($schedule->cam_id);
        $count = $cam->schedule()
            ->where('day', $request->get('day'))
            ->where('id', '<>', $schedule->id)->where(function ($query1) use ($request) {
                $query1->where(function ($query) use ($request) {
                    $query->where('start', '<=', $request->get('start'))
                        ->where('end', '>=', $request->get('start'));
                })->orWhere(function (Builder $query) use ($request) {
                    $query->where('start', '<=', $request->get('end'))
                        ->where('end', '>=', $request->get('end'));
                });
            })->count();
        if ($count !== 0) {
            return response(['status' => 'not ok', 'message' => 'يرجى الانتباه! تواريخ متداخلة']);
        }
        $schedule->update([
            'day' => $request->get('day'),
            'start' => $request->get('start'),
            'end' => $request->get('end'),
        ]);
        return response(['status' => 'ok', 'message' => 'تم تعديل توقيت العمل']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Schedule $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return response(['status' => 'ok', 'message' => 'تم إزالة توقيت العمل']);
    }
}
