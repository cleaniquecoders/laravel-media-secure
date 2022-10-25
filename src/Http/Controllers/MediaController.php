<?php

namespace Bekwoh\LaravelMediaSecure\Http\Controllers;

use Bekwoh\LaravelMediaSecure\Enums\MediaAccess;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $type
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, string $type, string $uuid)
    {
        abort_if(! MediaAccess::acceptable($type), 422, 'Invalid request type of '.$type);

        $media = config('media-library.media_model')::whereUuid($uuid)->firstOrFail();

        abort_if($request->user()->cannot($type, $media), 403, 'Unauthorized Access.');

        if($type == MediaAccess::view()->value || $type == MediaAccess::stream()->value) {
            return response()->make(file_get_contents($media->getPath()), 200, [
                'Content-Type' => $media->mime_type,
                'Content-Disposition' => 'inline; filename="'.$media->file_name.'"'
            ]);
        }

        if($type == MediaAccess::download()->value) {
            return response()->download($media->getPath());
        }

        return abort(422, 'Invalid media request');
    }
}
