<?php

namespace Newelement\DmpKodi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Newelement\DmpKodi\Services\KodiMediaSyncService;

class DmpKodiController extends Controller
{
	public function install(KodiMediaSyncService $service)
	{
		return $service->install();
	}

	public function update()
	{
		//
	}

	public function getSettings(KodiMediaSyncService $service)
	{
		return $service->getSettings();
	}

	public function updateSettings(Request $request, KodiMediaSyncService $service)
	{
		$service->updateSettings($request);
		return redirect()->back()->with('success', 'Kodi settings updated');
	}

	public function getNowPlaying(KodiMediaSyncService $service)
	{
		return $service->nowPlaying();
	}
}
