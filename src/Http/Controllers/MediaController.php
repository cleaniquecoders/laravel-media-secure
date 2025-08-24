<?php

namespace CleaniqueCoders\LaravelMediaSecure\Http\Controllers;

use CleaniqueCoders\LaravelMediaSecure\Enums\MediaAccess;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, string $type, string $uuid)
    {
        // Media has been fetched and authorized in the ValidateMediaAccess middleware
        $media = $request->attributes->get('media');

        if ($type == MediaAccess::VIEW->value || $type == MediaAccess::STREAM->value) {
            return response()->make(file_get_contents($media->getPath()), 200, [
                'Content-Type' => $media->mime_type,
                'Content-Disposition' => 'inline; filename="'.$media->file_name.'"',
            ]);
        }

        if ($type == MediaAccess::DOWNLOAD->value) {
            return response()->download($media->getPath());
        }

        return abort(422, 'Invalid media request');
    }
}
