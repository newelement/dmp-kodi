<?php

use Newelement\DmpKodi\Http\Controllers\DmpKodiController;

Route::put('/dmp-kodi/settings', [DmpKodiController::class, 'updateSettings'])->name('dmp-kodi.settings');
