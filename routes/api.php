<?php

use App\Http\Controllers\Api\APIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['apikey']], function(){
    Route::get('allCategories', [APIController::class, 'allCategories']);
    Route::get('allNews', [APIController::class, 'allNews']);
    Route::get('filterNews/{id}', [APIController::class, 'filterNews']);

    Route::get('allZoos', [APIController::class, 'allZoos']);
    Route::get('filterZoo/{id}', [APIController::class, 'filterZoo']);
    Route::get('zoo/{id}', [APIController::class, 'ShowZoo']);

    Route::post('addUser', [APIController::class, 'addUser']);
    Route::post('updateUser', [APIController::class, 'updateUser']);

    Route::post('profileUser', [APIController::class, 'profileUser']);

    Route::get('users', [APIController::class, 'allUsers']);
});


Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::get('user-profile', [APIController::class, 'userProfile']);
    Route::post('logout', [APIController::class, 'logout']);

});
