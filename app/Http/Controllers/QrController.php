<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;

//use Endroid\QrCode\Label\Label;
//use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

//use Endroid\QrCode\Writer\ValidationException;
use Ramsey\Uuid\Uuid;

class QrController extends Controller
{
    public function default_qr()
    {
        $writer = new PngWriter();

        $qrCode = QrCode::create('QR codes')
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(380);
//            ->setMargin(10)
//            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
//            ->setForegroundColor(new Color(0, 0, 0))
//            ->setBackgroundColor(new Color(255, 255, 255));
        $result = $writer->write($qrCode);
        $defaulf=public_path().'/qrcode/default.png';
        $result->saveToFile($defaulf);
    }

    public function  text_qr(Request $request){
        $text=$request->text;
        $writer = new PngWriter();
        $uuid = Uuid::uuid4();
        $qrCode = QrCode::create($text)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(380);
        $result = $writer->write($qrCode);
        $url='/qrcode/'.$uuid.'.png';
        $patch=public_path().$url;

        $result->saveToFile($patch);
        return response()->json([
            'qr'=>$url
        ]);
    }
}
