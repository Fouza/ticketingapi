<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Models\Ticket;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => ['cors', 'json.response']], function () {
    // Public routes
    Route::post('/login', [ApiAuthController::class, 'login'])->name('login.api');//
    Route::post('/register',[ApiAuthController::class, 'register'])->name('register.api');

    Route::middleware('auth:api')->group(function () {
        // Protected routes
        // Route::get('/user', function(Request $request){
        //     return $request->user();
        // });
        Route::post('/logout', [ApiAuthController::class, 'logout'])->name('logout.api');//
        Route::get('/tickets',[TicketController::class, 'getTickets']);//
        Route::post('/create-agent',[UserController::class,'createAgent']);
        Route::post('/create-ticket',[TicketController::class,'create']);//
        Route::post('/assign-ticket',[TicketController::class, 'assignTicket']);
        Route::post('/finish-ticket',[MessageController::class, 'finishTicket']);
        Route::get('/my-messages',[MessageController::class, 'getMyMessages']);
        Route::post('/read-message',[MessageController::class, 'readMessage']);
        Route::get('/my-tickets',[TicketController::class, 'myTickets']);
        Route::get('/finished-tickets',[TicketController::class, 'finishedTickets']);
    });

});
