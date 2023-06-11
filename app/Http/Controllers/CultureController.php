<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCultureRequest;
use App\Http\Requests\UpdateCultureRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\CultureResource;
use App\Models\Culture;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CultureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @return mixed
     */
    public function index(Request $request)
    {
        return CultureResource::collection(Culture::when(request('search'), function ($query) {
            $query->where('title', 'like', '%' . request('search') . '%');
        })->orderBy("id", "DESC")->paginate(2));

        //return CultureResource::collection(Culture::orderBy("id", "DESC")->paginate(2));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCultureRequest $request)
    {
        $data = $request->validated();

        // Check if image was given and save on local file system
        if (isset($data['image'])) {
            $relativePath  = $this->saveImage($data['image']);
            $data['image'] = $relativePath;
        }

        $culture = Culture::create($data);

        return new CultureResource($culture);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Culture $culture, Request $request)
    {
        $user = $request->user();
        if ($user->id !== $culture->user_id) {
            return abort(403, 'Unauthorized action.');
        }
        return new CultureResource($culture);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCultureRequest $request, Culture $culture)
    {
        $data = $request->validated();

        // Check if image was given and save on local file system
        if (isset($data['image'])) {
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;

            //If there is an old image, delete it
            if ($culture->image) {
                $absolutePath = public_path($culture->image);
                File::delete($absolutePath);
            }
        }

        // Update culture in the database
        $culture->update($data);

        return new CultureResource($culture);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Culture $culture, Request $request)
    {
        $user = $request->user();
        if ($user->id !== $culture->user_id) {
            return abort(403, 'Unauthorized action.');
        }

        $culture->delete();
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

        $dir = 'images/culture/';
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
