<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Jobs\ProcessVideo;
use App\Jobs\ListenVideo;

class HomeController extends Controller
{
    public function getIndex()
    {
        return view('index');
    }

    public function postUpload(Request $request)
    {
        
        if (!$request->hasFile('video')) {
            return response()->json(['upload_file_not_found'], 400);
        }

        $file = $request->file('video');

        if (!$file->isValid()) {
            return response()->json(['invalid_file_upload'], 400);
        }
        
        $name = $file->hashname();
        $hashNameWithoutExt = explode(".", $name)[0];
        $ext = explode(".", $name)[1];
        
        $path = public_path() . '/uploads/' . $hashNameWithoutExt . '/';

        $file->move($path, "orig." . $ext);

        //CreateApplication::dispatch($value, $value1, $value2, $value3)->onQueue('processing');
        $videoConvertJob = new ProcessVideo($path, $name, $ext, $hashNameWithoutExt);
        $videoListenJob = new ListenVideo($path, $name, $ext, $hashNameWithoutExt);

        dispatch($videoConvertJob)->onQueue('video');
        dispatch($videoListenJob)->onQueue('listenVideo');

        return response()->json(compact('path','name'));
    }
}
