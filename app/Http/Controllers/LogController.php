<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
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


    public function tracking(Request $request)
    {
        $logQuery = Log::query()->where('verification_img', '<>', "");
        if ($request->get('start') !== "null" && $request->get('end') !== "null") {
            $logQuery = $logQuery->whereBetween('timestamp', [$request->get('start'), $request->get('end')]);
        }

        if ($request->get('withIgnore') !== "-1") {
            $logQuery = $logQuery->where('ignore', "=", $request->get('withIgnore'));
        }

        if ($request->get('withUnknown') !== "-1") {
            $logQuery = $logQuery->where('unidentified', "=", $request->get('withUnknown'));
        }

        if ($request->get('withForbid') !== "-1") {
            $logQuery = $logQuery->where('warning_flag', "=", $request->get('withForbid'));
        }

        if ($request->get('identifier') !== null) {
            $logQuery = $logQuery->with(['person', 'cam'])
                ->whereHas('person', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->get('identifier') . '%')
                        ->orWhere('id_number', 'like', '%' . $request->get('identifier') . '%');
                });
        } else {
            $logQuery = $logQuery->with(['person', 'cam']);
        }
        $logs = $logQuery->orderByDesc('timestamp')->get();
        return response($logs);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Log  $log
     * @return \Illuminate\Http\Response
     */
    public function show(Log $log)
    {
        //
    }

    public function ignore(Request $request, Log $log)
    {
        if ($log->warning_flag || $log->unidentified) {
            $log->ignore = 1;
            $log->save();
            return response(['status' => 'ok', 'message' => 'تم تجاهل السجل']);
        } else {
            return response(['status' => 'not ok', 'message' => 'لا يمكن تجاهل هذا السجل']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Log  $log
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Log $log)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Log  $log
     * @return \Illuminate\Http\Response
     */
    public function destroy(Log $log)
    {
        //
    }
}
