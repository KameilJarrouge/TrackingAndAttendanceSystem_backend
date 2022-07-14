<?php

namespace App\Http\Controllers;

use App\Models\Cam;
use Illuminate\Http\Request;

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
        if($request->get('identifier') !== null){
            $identifier = $request->get('identifier');
        }
        if($request->get('type') !== "-1"){
            return response(Cam::query()->where('location', 'like', '%' . $identifier . '%')
                ->where('type', $request->get('type'))
                ->paginate($request->get('perPage')));
        }else{
            return response(Cam::query()->where('location', 'like', '%' . $identifier . '%')
                ->paginate($request->get('perPage')));
        }
    }

    public function log(Request $request, Cam $cam){
        $logQuery = $cam->log();
        if($request->get('start') !== "null" && $request->get('end') !== "null"){
            $logQuery = $logQuery->whereBetween('timestamp',[$request->get('start'), $request->get('end')]);
        }

        if($request->get('withImage') !== "-1"){
            $operator = ($request->get('withImage') === "1")? "<>":"=";
            $logQuery = $logQuery->where('verification_img',$operator,"");
        }

        if($request->get('withIgnore') !== "-1"){
            $logQuery = $logQuery->where('ignore',"=",$request->get('withIgnore'));
        }

        if($request->get('withUnknown') !== "-1"){
            $logQuery = $logQuery->where('unidentified',"=",$request->get('withUnknown') );
        }

        if($request->get('withForbid') !== "-1"){
            $logQuery = $logQuery->where('warning_flag',"=",$request->get('withForbid'));
        }

        if($request->get('identifier') !== null){
//            return response('Fukckkjl');
            $logQuery = $logQuery->with('person')
                ->whereHas('person', function ($query) use($request){
                    $query->where('name', 'like', '%' . $request->get('identifier') . '%')
                    ->orWhere('id_number', 'like', '%' . $request->get('identifier') . '%');
                });
        }else{
            $logQuery = $logQuery->with('person');
        }
        $logs = $logQuery->paginate($request->get('perPage'));
        return response( $logs);
    }

    public function options(){
        return response(Cam::query()->select('location')->distinct()->get());
    }

    public function camOptions(){
        return response(Cam::query()->where('type' , 0)->get()); // classroom only
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Cam::create([
            'cam_url' => $request->get('cam_url'),
            'location' => $request->get('location'),
            'type' => $request->get('type')
        ]);
        return response(['status'=> 'ok', 'message' => 'تم إضافة الكاميرا بنجاح']);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cam  $cam
     * @return \Illuminate\Http\Response
     */
    public function show(Cam $cam)
    {
        return response($cam);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cam  $cam
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cam $cam)
    {
        $cam->update([
            'cam_url' => $request->get('cam_url'),
            'location' => $request->get('location'),
            'type' => $request->get('type')
        ]);
        return response(['status'=> 'ok', 'message' => 'تم تعديل الكاميرا بنجاح']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cam  $cam
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cam $cam)
    {
        $cam->delete();
        return response(['status'=> 'ok', 'message' => 'تم تعديل الكاميرا بنجاح']);

    }
}
