<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HeroController;
use App\Http\Controllers\Api\MissionController;

// Rutas para Héroes
Route::apiResource('heroes', HeroController::class);
Route::get('heroes/search/query', [HeroController::class, 'search']);
Route::get('heroes/{id}/missions', [HeroController::class, 'missions']);
Route::post('heroes/{id}/missions', [HeroController::class, 'assignMissions']);
Route::put('heroes/{heroId}/missions/{missionId}', [HeroController::class, 'updateMissionStatus']);

// Rutas para Misiones
Route::get('missions/groups/all', [MissionController::class, 'getAllGroups']);
Route::apiResource('missions', MissionController::class);
Route::get('missions/search/query', [MissionController::class, 'search']);
Route::get('missions/{id}/heroes', [MissionController::class, 'heroes']);
Route::post('missions/{id}/heroes', [MissionController::class, 'assignHeroes']);
Route::get('missions/{missionId}/groups/{groupName}', [MissionController::class, 'getGroupStatus']);
Route::put('missions/{missionId}/groups/status', [MissionController::class, 'updateGroupStatus']);
Route::delete('missions/{missionId}/groups/{groupName}', [MissionController::class, 'deleteGroup']);