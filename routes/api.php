<?php

use Newelement\DmpKodi\Http\Controllers\DmpKodiController;

// These are /api/* routes
Route::get('/dmp-kodi-settings', [DmpKodiController::class, 'getSettings']);
Route::get('/dmp-kodi-now-playing', [DmpKodiController::class, 'getNowPlaying']);

Route::get('/dmp-kodi-install', [DmpKodiController::class, 'install']);
Route::get('/dmp-kodi-update', [DmpKodiController::class, 'update']);
