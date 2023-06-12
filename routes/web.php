<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentsController;

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

Route::group(['prefix' => 'students', 'as' => 'students.'], function () {

    Route::get('/dashboard', function () {
        return view('students.dashboard');
    })->name('dashboard');

    Route::post('/file-upload', [StudentsController::class, 'fileUpload'])->name('file-upload');

    Route::get('/template-download', [StudentsController::class, 'templateDownload'])->name('template-download');

    Route::get('/students-records', [StudentsController::class, 'fetchStudentsRecords'])->name('students-records');

});
