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

        $media = config('laravel-media-secure.model')::whereUuid($uuid)->firstOrFail();

        abort_if($request->user()->cannot($type, $media), 403, 'Unauthorized Access.');

        return match ($type) {
            MediaAccess::stream()->value => response()->streamDownload(function () use ($media) {
                echo file_get_contents($media->getPath());
            }),
            MediaAccess::view()->value => response()->file($media->getPath()),
            MediaAccess::download()->value => response()->download($media->getPath()),
            default => abort(422, 'Invalid media request')
        };
    }
}
