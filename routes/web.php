<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/send-message',[MessageController::class,'sendMessage'])->name('send-message');
Route::post('/get-message',[MessageController::class,'getMessages'])->name('get-message');
Route::post('/update-message-status',[MessageController::class,'updateMessagesStatus'])->name('update-message-status');
