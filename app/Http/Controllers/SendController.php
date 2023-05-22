<?php

namespace App\Http\Controllers;

use App\Mail\ClientSend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;

class SendController extends Controller
{
    public function send(Request $request){
        $image = $request->img;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $uuid = Uuid::uuid4();
        $imageName = $uuid.'.'.'png';
        $url=env('APP_URL').'email/'.$imageName;
        \File::put(public_path(). '/email/' . $imageName, base64_decode($image));

        $email=$request->email;
        try {
            Mail::to($request->email)->send(new ClientSend($email,$url));
            return response()->json(['suc'=>1]);
        } catch (Exception $e) {

        } finally {
        }


    }
}
