<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Holiday::query();
        switch ($request->get('scope')) {
            case 'current':

                $query = $query->current();
                break;

            case 'all':
                break;

            case 'passed':

                $query = $query->passed();
                break;

            case 'upcoming':

                $query = $query->upcoming();
                break;

            default:
                break;
        }
        if ($request->get('name') !== "null") {
            $query->where('name', 'like', '%' . $request->get('name') . '%');
        }
        return response($query->orderByDesc('start')->paginate($request->get('perPage')));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Holiday::create([
            'name' => $request->get('name'),
            'start' => $request->get('start'),
            'end' => $request->get('end'),
        ]);
        return response(['status' => 'ok', 'message' => 'تم إضافة عطلة بنجاح']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function show(Holiday $holiday)
    {
        return response($holiday);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Holiday $holiday)
    {
        $holiday->update([
            'name' => $request->get('name'),
            'start' => $request->get('start'),
            'end' => $request->get('end'),
        ]);
        return response(['status' => 'ok', 'message' => 'تم تعديل العطلة بنجاح']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return response(['status' => 'ok', 'message' => 'تم إزالة العطلة بنجاح']);
    }
}
