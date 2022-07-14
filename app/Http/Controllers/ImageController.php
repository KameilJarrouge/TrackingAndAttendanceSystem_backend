<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
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
    public function store(Request $request, Person $person)
    {
        // save the new image
        $destinationPath = storage_path('app/public/profiles');
        $imageTemp = $request->file('image');
        $name = $person->id. '-' .$request->get('imageNumber').'.'.$imageTemp->extension();
        $img = \Intervention\Image\Facades\Image::make($imageTemp->path());
        $img->resize(220, 220)->save($destinationPath.'/'.$name);
        // create the Image instance
        $person->images()->create([
            'url' => asset( 'storage/profiles/' . $person->id.'-'. $request->get('imageNumber'). '.'.$imageTemp->extension()),
            'name' => $person->id.'-'. $request->get('imageNumber') . '.'.$imageTemp->extension(),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function show(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Image $image)
    {

        // delete old image
        Storage::delete('public/profiles/' . $image->name);
        // save new image
        $destinationPath = storage_path('app/public/profiles');
//        return response($request->hasFile('image')?"fuck":"no");
        $imageTemp = $request->file('image');
        $name = $image->person_id. '-' .$request->get('imageNumber').'.'.$imageTemp->extension();
        $img = \Intervention\Image\Facades\Image::make($imageTemp->path());
        $img->resize(220, 220)->save($destinationPath.'/'.$name);
        // update the Image instance to point at the new image file
        $image->update([
            'url' => asset( 'storage/profiles/'. $image->person_id .'-'.$request->get('imageNumber') .  '.'.$imageTemp->extension()),
            'name' => $image->person_id. '-'.$request->get('imageNumber').  '.'.$imageTemp->extension(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function destroy($imageId)
    {
        $image = Image::find($imageId);
        Storage::delete('public/profiles/' . $image->name);
        $image->delete();
    }
}
