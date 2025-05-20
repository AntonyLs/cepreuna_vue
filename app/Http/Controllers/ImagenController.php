<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Image;

class ImagenController extends Controller
{
    public function upload( Request $request){
        return($request->all());
            // $name = time();
            // $img = $request->image;
            // $folderPath = "images/";

            // $file = base64_decode($img);
            // $folderName = 'public/images/';
            // $safeName = $name.'.'.'png';
            // $destinationPath = public_path() . $folderName;
            // $success = file_put_contents(public_path().'/images/'.$safeName, $file);
            // print $success;
    
    }
}
