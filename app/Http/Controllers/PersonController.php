<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // return response($request->all());
        $constraints = array();
        $identifier = "";
        if ($request->get('identifier') !== "null") {
            $identifier = $request->get('identifier');
        }
        if ($request->get('identity') !== "-1") {
            $constraints['identity'] = $request->get('identity');
        }
        if ($request->get('onCampus') !== "-1") {
            $constraints['on_campus'] = $request->get('onCampus');
        }
        if ($request->get('tracked') !== "-1") {
            $constraints['track'] = $request->get('tracked');
        }
        if ($request->get('blacklist') !== "-1") {
            $constraints['on_blacklist'] = $request->get('blacklist');
        }

        if ($request->get('recognize') !== "-1") {
            $constraints['recognize'] = $request->get('recognize');
        }
        if ($constraints === []) {
            return response(Person::query()->where(function ($query) use ($identifier, $request) {
                $query->where('name', 'like', '%' . $identifier . '%')
                    ->orWhere('id_number', 'like', '%' . $identifier . '%');
            })->paginate($request->get('perPage')));
        } else {
            return response(Person::query()->where($constraints)
                ->where(function ($query) use ($identifier, $request) {
                    $query->where('name', 'like', '%' . $identifier . '%')
                        ->orWhere('id_number', 'like', '%' . $identifier . '%');
                })->paginate($request->get('perPage')));
        }
    }


    public function pythonPeople()
    {
        return response(Person::query()
            ->where('recognize', 1)
            ->with('images:id,person_id,url')
            ->whereHas('images')
            ->get(['id', 'identity']));
    }

    public function test(Request $request)
    {
        return response($request->all(), 500);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $person = Person::query()->create([
            'id_number' => $request->get('id_number'),
            'name' => $request->get('name'),
            'track' => $request->get('track'),
            'on_blacklist' => $request->get('on_blacklist'),
            'recognize' => $request->get('recognize'),
            'on_campus' => 0,
            'identity' => $request->get('identity'),
        ]);

        $destinationPath = storage_path('app/public/profiles');
        if ($request->hasFile('image1')) {
            $image = $request->file('image1');
            $name = $person->id . '-image1' . '.' . $image->extension();
            $img = Image::make($image->path());
            $img->resize(220, 220)->save($destinationPath . '/' . $name);
            $person->images()->create([
                'url' => asset('storage/profiles/' . $person->id . '-image1' .  '.' . $image->extension()),
                'name' => $person->id . '-image1' .  '.' . $image->extension(),
            ]);
        }
        if ($request->hasFile('image2')) {
            $image = $request->file('image2');
            $name = $person->id . '-image2' . '.' . $image->extension();
            $img = Image::make($image->path());
            $img->resize(220, 220)->save($destinationPath . '/' . $name);
            $person->images()->create([
                'url' => asset('storage/profiles/' . $person->id . '-image2' .  '.' . $image->extension()),
                'name' => $person->id . '-image2' .  '.' . $image->extension(),
            ]);
        }
        if ($request->hasFile('image3')) {
            $image = $request->file('image3');
            $name = $person->id . '-image3' . '.' . $image->extension();
            $img = Image::make($image->path());
            $img->resize(220, 220)->save($destinationPath . '/' . $name);
            $person->images()->create([
                'url' => asset('storage/profiles/' . $person->id . '-image3' .  '.' . $image->extension()),
                'name' => $person->id . '-image3' .  '.' . $image->extension(),
            ]);
        }

        return response(['status' => 'ok', 'message' => 'تم إضافة الشخص']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function show(Person $person)
    {
        return response($person->load('images'));
    }
    public function view(Person $person)
    {
        return response($person);
    }

    //UNIX_TIMESTAMP
    public function logs(Request $request, Person $person)
    {
        //        return $request;
        $logQuery = $person->logs();
        if ($request->get('start') !== "null" && $request->get('end') !== "null") {
            $logQuery = $logQuery->whereBetween('timestamp', [$request->get('start'), $request->get('end')]);
        }
        if ($request->get('withImage') !== "-1") {
            $operator = ($request->get('withImage') === "1") ? "<>" : "=";
            $logQuery = $logQuery->where('verification_img', $operator, "");
        }
        if ($request->get('location') !== 'all' && $request->get('location') !== 'جميع المواقع') {
            $logQuery = $logQuery->with('cam')
                ->whereHas('cam', function ($query) use ($request) {
                    $query->where('location', 'like', '%' . $request->get('location') . '%');
                });
        } else {
            $logQuery = $logQuery->with('cam');
        }
        $logs = $logQuery->orderByDesc('timestamp')->paginate($request->get('perPage'));
        return response($logs);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Person $person)
    {
        $person->update([
            'id_number' => $request->get('id_number'),
            'name' => $request->get('name'),
            'track' => $request->get('track'),
            'on_blacklist' => $request->get('on_blacklist'),
            'recognize' => $request->get('recognize'),
            'on_campus' => $person->on_campus,
            'identity' => $request->get('identity'),
        ]);
        return response(['status' => 'ok', 'message' => 'تم تعديل الشخص']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function destroy(Person $person)
    {
        $images = $person->images;
        foreach ($images as $image) {
            $image->deleteImageFile();
        }
        $person->images()->delete();
        $person->delete();
        return response(['status' => 'ok', 'message' => 'تم إزالة الشخص']);
    }
}
