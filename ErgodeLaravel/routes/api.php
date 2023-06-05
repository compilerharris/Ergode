
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/login',[TestController::class, 'login']);

Route::post('/getUser',[TestController::class, 'getUser']);

Route::post('/getOneUser',[TestController::class, 'getOneUser']);

Route::post('/addUser',[TestController::class, 'addUser']);

Route::post('/editUser',[TestController::class, 'editUser']);

Route::post('/deleteUser',[TestController::class, 'deleteUser']);