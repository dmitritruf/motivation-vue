<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\TaskListController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\ExampleTaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OverviewController;
use App\Http\Controllers\VillageController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Middleware for admin

Route::post('/login', [AuthenticationController::class, 'authenticate']);
Route::post('/logout', [AuthenticationController::class, 'logout']);
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::group(['middleware' => ['auth']], function () {
    Route::resource('/tasks', TaskController::class)->only([
        'store', 'show', 'update', 'destroy'
    ]);
    Route::put('/tasks/complete/{task}', [TaskController::class, 'complete']);

    Route::resource('/tasklists', TaskListController::class)->only([
        'store', 'show', 'update', 'destroy'
    ]);
    Route::post('/tasks/merge/{tasklist}', [TaskListController::class, 'mergeTasks']);

    Route::resource('/character', CharacterController::class)->only([
        'store', 'update', 'destroy',
    ]);

    Route::get('/character/find', [CharacterController::class, 'fetchCharacterIfExists']);
    Route::get('/character/active', [CharacterController::class, 'fetchActiveCharacter']);
    Route::get('/character/all', [CharacterController::class, 'fetchAllCharactersByUser']);

    Route::get('/village/all', [VillageController::class, 'fetchAllVillagesByUser']);

    
    Route::get('/notifications', [NotificationController::class, 'show']);
    Route::get('/notifications/unread', [NotificationController::class, 'hasUnreadNotifications']);
    Route::post('/notifications/all', [NotificationController::class, 'sendNotificationToAll']);
    Route::get('/profile/{user}', [UserController::class, 'show']);
  
    Route::post('/friend/request/{user}', [FriendController::class, 'sendFriendRequest']);
    Route::get('/friend/requests/all', [FriendController::class, 'getAllRequests']);
    Route::post('/friend/request/{friend}/accept', [FriendController::class, 'acceptFriendRequest']);
    Route::post('/friend/request/{friend}/deny', [FriendController::class, 'denyFriendRequest']);
    Route::delete('/friend/remove/{friend}', [FriendController::class, 'destroy']);

    Route::put('/user/settings/email', [UserController::class, 'updateEmail']);
    Route::put('/user/settings/password', [UserController::class, 'updatePassword']);
    Route::put('/user/settings', [UserController::class, 'updateSettings']);
    Route::put('/user/settings/rewards', [UserController::class, 'updateRewardsType']);

    Route::get('/isadmin', [UserController::class, 'isAdmin']);

    Route::post('/search', [UserController::class, 'searchUser']);
    Route::post('/register/confirm', [RegisteredUserController::class, 'confirmRegister']);

    Route::get('/dashboard', [DashboardController::class, 'getDashboard']);
    Route::get('/overview', [OverviewController::class, 'getOverview']);
});

    Route::get('/achievements', [AchievementController::class, 'showAll']);
    Route::get('/achievements/triggers', [AchievementController::class, 'showTriggers']);
    Route::resource('/achievements', AchievementController::class)->only([
        'store', 'update',
    ]);

    Route::get('/examples/tasks', [ExampleTaskController::class, 'fetchExampleTasks']);
//Route::group(['middleware' => ['admin']], function () {

//});

Route::get('/experience', [CharacterController::class, 'getExperienceTable']);