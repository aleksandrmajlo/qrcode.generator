<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload_file(Request $request)
    {
        $file = $request->file('file');
        foreach (file($file) as $key => $line) {
            if ($key == 0) {
                $arr = explode(';', $line);
                if (isset($arr[0])) {
                    $text_arr=[];
                    foreach ($arr as $k=>$item){
                        if($k>0){
                            $res=explode( "\n", wordwrap( $item, 3));
                            $th=[];
                            foreach ($res as $ke=>$re){
                                if(($ke & 1)){
                                    //нечетное

                                }else{
                                    $t=$re;
                                    if(isset($res[$ke+1]))$t.=' '.$res[$ke+1];
                                    $th[]=$t;
                                }
                            }
                            $text_arr[]=implode("\n",$th);
                        }
                    }
                    return response()->json(['qr' => $arr[0],'text'=>$text_arr]);
                }
            }
        }
    }

}
