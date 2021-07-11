<?php

use App\Http\Controllers\CompornentTestController;
use App\Http\Controllers\LifeCycleTestController;
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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');


Route::get('/component-test1', [CompornentTestController::class, 'showComportnent1']);
Route::get('/component-test2', [CompornentTestController::class, 'showComportnent2']);
Route::get('/servicecontainertest', [LifeCycleTestController::class, 'showServiceContainerTest']);
Route::get('/serviceprovidertest', [LifeCycleTestController::class, 'serviceprovidertest']);

require __DIR__.'/auth.php';
