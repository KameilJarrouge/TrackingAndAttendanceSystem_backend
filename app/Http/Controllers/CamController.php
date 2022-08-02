<?php

namespace App\Http\Controllers;

use App\Models\Cam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CamController extends Controller
{
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
        if ($request->get('type') === "4") {
            return response(Cam::query()->where('location', 'like', '%' . $identifier . '%')
                ->where(function ($query) {
                    $query->where('type', 1)->orWhere('type', 2);
                })
                ->paginate($request->get('perPage'))
            );
        } else if ($request->get('type') !== "-1") {
            return response(Cam::query()->where('location', 'like', '%' . $identifier . '%')
                ->where('type', $request->get('type'))
                ->paginate($request->get('perPage')));
        } else {
            return response(Cam::query()->where('location', 'like', '%' . $identifier . '%')
                ->paginate($request->get('perPage')));
        }
    }

    public function log(Request $request, Cam $cam)
    {
        $logQuery = $cam->log();
        if ($request->get('start') !== "null" && $request->get('end') !== "null") {
            $logQuery = $logQuery->whereBetween('timestamp', [$request->get('start'), $request->get('end')]);
        }

        if ($request->get('withImage') !== "-1") {
            $operator = ($request->get('withImage') === "1") ? "<>" : "=";
            $logQuery = $logQuery->where('verification_img', $operator, "");
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
//            return response('Fukckkjl');
            $logQuery = $logQuery->with('person')
                ->whereHas('person', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->get('identifier') . '%')
                        ->orWhere('id_number', 'like', '%' . $request->get('identifier') . '%');
                });
        } else {
            $logQuery = $logQuery->with('person');
        }
        $logs = $logQuery->paginate($request->get('perPage'));
        return response($logs);
    }

    public function options()
    {
        return response(Cam::query()->select('location')->distinct()->get());
    }

    public function camOptions()
    {
        return response(Cam::query()->where('type', 0)->get()); // classroom only
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Cam::create([
            'cam_url' => $request->get('cam_url'),
            'location' => $request->get('location'),
            'type' => $request->get('type')
        ]);
        return response(['status' => 'ok', 'message' => 'تم إضافة الكاميرا بنجاح']);

    }

    public function schedule(Request $request, Cam $cam)
    {
        return response($cam->schedule()->orderBy('day')->orderBy('start')->paginate($request->get('perPage')));
    }

    private function addScheduleAllDays(Request $request, Cam $cam)
    {
        DB::beginTransaction();
        for ($i = 0; $i < 7; $i++) {
            $count = $cam->schedule()->where('day', '=',$i)
                ->where(function ($query1) use ($request) {
                    $query1->where(function ($query) use ($request) {
                        $query->where('start', '<=', $request->get('start'))
                            ->where('end', '>=', $request->get('start'));
                    })->orWhere(function (Builder $query) use ($request) {
                        $query->where('start', '<=', $request->get('end'))
                            ->where('end', '>=', $request->get('end'));
                    });
                })
                ->count();
            if ($count !== 0) {
                DB::rollBack();
                return response(['status' => 'not ok', 'message' => 'يرجى الانتباه! تواريخ متداخلة']);
            }
            $cam->schedule()->create([
                'day' => $i,
                'start' => $request->get('start'),
                'end' => $request->get('end'),
            ]);
        }
        DB::commit();
        return response(['status' => 'ok', 'message' => 'تم إضافة توقبت عمل']);
    }


    public function addSchedule(Request $request, Cam $cam)
    {
        if ($request->get('day') === "-1") {

            return $this->addScheduleAllDays($request, $cam);
        }

        $count = $cam->schedule()->where('day', '=', $request->get('day'))
            ->where(function ($query1) use ($request) {
                $query1->where(function ($query) use ($request) {
                    $query->where('start', '<=', $request->get('start'))
                        ->where('end', '>=', $request->get('start'));
                })->orWhere(function (Builder $query) use ($request) {
                    $query->where('start', '<=', $request->get('end'))
                        ->where('end', '>=', $request->get('end'));
                });
            })
            ->count();
        if ($count !== 0) {
            return response(['status' => 'not ok', 'message' => 'يرجى الانتباه! تواريخ متداخلة']);
        }
        $cam->schedule()->create([
            'day' => $request->get('day'),
            'start' => $request->get('start'),
            'end' => $request->get('end'),
        ]);
        return response(['status' => 'ok', 'message' => 'تم إضافة توقبت عمل']);

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Cam $cam
     * @return \Illuminate\Http\Response
     */
    public function show(Cam $cam)
    {
        return response($cam);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Cam $cam
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cam $cam)
    {
        $cam->update([
            'cam_url' => $request->get('cam_url'),
            'location' => $request->get('location'),
            'type' => $request->get('type')
        ]);
        return response(['status' => 'ok', 'message' => 'تم تعديل الكاميرا بنجاح']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Cam $cam
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cam $cam)
    {
        $cam->delete();
        return response(['status' => 'ok', 'message' => 'تم تعديل الكاميرا بنجاح']);

    }
}
