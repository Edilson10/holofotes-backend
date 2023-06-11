<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFlavorsAirRequest;
use App\Http\Requests\UpdateFlavorsAirRequest;
use App\Http\Resources\FlavorsAirResource;
use App\Models\FlavorsAir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FlavorsAirController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @return mixed
     */
    public function index(Request $request)
    {
        // $user = $request->user();
        // return FlavorsAirResource::collection(FlavorsAir::orderBy("id", "DESC")->paginate(2));

        return FlavorsAirResource::collection(FlavorsAir::when(request('search'), function ($query) {
            $query->where('title', 'like', '%' . request('search') . '%');
        })->orderBy("id", "DESC")->paginate(2));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFlavorsAirRequest $request)
    {
        $data = $request->validated();

        // Check if image was given and save on local file system
        if (isset($data['image'])) {
            $relativePath  = $this->saveImage($data['image']);
            $data['image'] = $relativePath;
        }

        $flavorsAir = FlavorsAir::create($data);

        return new FlavorsAirResource($flavorsAir);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(FlavorsAir $flavorsAir, Request $request)
    {
        $user = $request->user();
        if ($user->id !== $flavorsAir->user_id) {
            return abort(403, 'Unauthorized action.');
        }
        return new FlavorsAirResource($flavorsAir);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFlavorsAirRequest $request, FlavorsAir $flavorsAir)
    {
        $data = $request->validated();

        // Check if image was given and save on local file system
        if (isset($data['image'])) {
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;

            //If there is an old image, delete it
            if ($flavorsAir->image) {
                $absolutePath = public_path($flavorsAir->image);
                File::delete($absolutePath);
            }
        }

        // Update culture in the database
        $flavorsAir->update($data);

        return new FlavorsAirResource($flavorsAir);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(FlavorsAir $flavorsAir, Request $request)
    {
        $user = $request->user();
        if ($user->id !== $flavorsAir->user_id) {
            return abort(403, 'Unauthorized action.');
        }

        $flavorsAir->delete();
        return response('', 204);
    }

    private function saveImage($image)
    {
        // Check if image is valid base64 string
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
            // Take out the base64 encoded text without mime type
            $image = substr($image, strpos($image, ',') + 1);
            // Get file extension
            $type = strtolower($type[1]); // jpg, png, gif

            // Check if file is an image
            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new \Exception('invalid image type');
            }
            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);

            if ($image === false) {
                throw new \Exception('base64_decode failed');
            }
        } else {
            throw new \Exception('did not match data URI with image data');
        }

        $dir = 'images/flavorAir/';
        $file = Str::random() . '.' . $type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;
        if (!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }
        file_put_contents($relativePath, $image);

        return $relativePath;
    }
}
