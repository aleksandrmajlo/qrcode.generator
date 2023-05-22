<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

Route::get('/', function () {
    return view('home');
});
Route::get('/test', function () {
    return view('test');
});

// генерируем  по умолчанию картинку
Route::get('default_qr', [\App\Http\Controllers\QrController::class, 'default_qr']);
// генерируем  c текста
Route::post('text_qr', [\App\Http\Controllers\QrController::class, 'text_qr']);
// загрузка файла upload_file
Route::post('upload_file', [\App\Http\Controllers\UploadController::class, 'upload_file']);
//отправка письма
Route::post('send', [\App\Http\Controllers\SendController::class, 'send']);

//Auth::routes();

//Route::get(ge'/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
