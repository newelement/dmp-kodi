<?php

namespace Newelement\DmpKodi\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use App\Interfaces\MediaSyncInterface;
use App\Traits\PosterProcess;
use Illuminate\Support\Facades\Artisan;
use Plugin;

class KodiMediaSyncService implements MediaSyncInterface
{
	use PosterProcess;

	public $kodiSettings = [];

	public function __construct()
	{
		$this->setSettings();
		$this->setKodiSettings();
	}

	public function setSettings()
	{
		$this->settings = Setting::first();
	}

	/**
	 * Install the plugin
	 *
	 * @return array
	 */
	public function install(): array
	{
		Artisan::call('vendor:publish', ['--provider' => 'Newelement\DmpKodi\DmpKodiServiceProvider', '--force' => true]);

		$plugin = [
			'type' => 'media_source',
			'plugin_key' => 'dmp-kodi',
			'name' => 'Kodi Media Sync and Now Playing',
			'description' => 'Syncs movie posters and shows now playing.',
			'url' => 'https://github.com/newelement/dmp-kodi',
			'repo' => 'newelement/dmp-kodi',
			'version' => '1.0.0',
		];

		Plugin::install($plugin);

		$options = [
			[
				'type' => 'string',
				'value' => 'localhost',
				'field_name' => 'kodi_url',
				'plugin_key' => 'dmp-kodi',
			],
			[
				'type' => 'string',
				'value' => '8989',
				'field_name' => 'kodi_port',
				'plugin_key' => 'dmp-kodi',
			],
			[
				'type' => 'string',
				'value' => '9090',
				'field_name' => 'kodi_socket_port',
				'plugin_key' => 'dmp-kodi',
			],
			[
				'type' => 'string',
				'value' => null,
				'field_name' => 'kodi_username',
				'plugin_key' => 'dmp-kodi',
			],
			[
				'type' => 'string',
				'value' => null,
				'field_name' => 'kodi_password',
				'plugin_key' => 'dmp-kodi',
				'secret' => true
			]
		];

		Plugin::addOptions($options);

		Artisan::call('optimize:clear');
		Artisan::call('optimize');

		return ['success' => true];
	}

	public function update()
	{
		//
	}

	public function setKodiSettings()
	{
		// Can also call Plugin::getOptions('dmp-kodi') to get full options array
		$this->kodiSettings['kodi_url'] = Plugin::getOptionValue('kodi_url');
		$this->kodiSettings['kodi_port'] = Plugin::getOptionValue('kodi_port');
		$this->kodiSettings['kodi_socket_port'] = Plugin::getOptionValue('kodi_socket_port');
		$this->kodiSettings['kodi_username'] = Plugin::getOptionValue('kodi_username');
		$this->kodiSettings['kodi_password'] = Plugin::getOptionValue('kodi_password');
	}

	public function getSettings()
	{
		return $this->kodiSettings;
	}

	public function updateSettings($request)
	{
		Plugin::updateOption('kodi_url', $request->kodi_url);
		Plugin::updateOption('kodi_port', $request->kodi_port);
		Plugin::updateOption('kodi_socket_port', $request->kodi_socket_port);
		Plugin::updateOption('kodi_username', $request->kodi_username);
		Plugin::updateOption('kodi_password', $request->kodi_password);
	}

	/**
	 * Make API calls to media server
	 *
	 * @param string $path /path/resource
	 * @param string $method get|post
	 * @param array $params
	 *
	 * @return json
	 */
	public function apiCall($jsonRpc, $method = 'GET', $params = [])
	{
		$request = 'http://'.$this->kodiSettings['kodi_url'].':'.$this->kodiSettings['kodi_port'].'/jsonrpc?request='.$jsonRpc;

		if (strlen($this->kodiSettings['kodi_username']) && strlen($this->kodiSettings['kodi_password'])) {
			$response = Http::withBasicAuth(
				$this->kodiSettings['kodi_username'],
				$this->kodiSettings['kodi_password']
			)
				->get($request);
		} else {
			$response = Http::get($request);
		}

		return $response->json();
	}

	public function syncMedia($page = 0)
	{
		$limit = 20;
		$start = $page * $limit;
		$end = $limit * ($page+1);

		$jsonRpc = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies", "params": {"limits": { "start" : '.$start.', "end": '.$end.' }, "properties" : ["art", "rating", "mpaa", "runtime"], "sort": { "order": "ascending", "method": "label", "ignorearticle": true } }, "id": "libMovies"}';

		$json = $this->apiCall($jsonRpc);

		if (isset($json['result']) && isset($json['result']['movies'])) {
			if (count($json['result']['movies']) > 0) {
				$movies = $json['result']['movies'];
				$this->processMovies($movies);

				if ($end < $json['result']['limits']['total']) {
					$page = $page+1;
					$this->syncMedia($page);
				}
			}
		}

		return ['success' => true];
	}

	public function processMovies($movies)
	{
		foreach ($movies as $movie) {
			if (isset($movie['art']) && isset($movie['art']['poster']) && $movie['art']['poster']) {
				$imageUrl = urldecode(str_replace('image://', '', rtrim($movie['art']['poster'], '/')));

				$savedImage = $this->saveImage($movie['label'], $imageUrl);

				$params = [
					'media_type' => 'movie',
					'name' => $movie['label'],
					'file_name' => $savedImage['file_name'],
					'id' => 'kodi-'.$movie['movieid'],
					'rating' => isset($movie['mpaa']) ? str_replace('Rated ', '', $movie['mpaa']) : null,
					'audience_rating' => isset($movie['rating']) ? $movie['rating'] : 0,
					'runtime' => is_numeric($movie['runtime']) ? $movie['runtime'] / 60 : null
				];

				$this->savePoster($params);
			}
		}
	}

	public function nowPlaying()
	{
		$jsonRpc = '[{"jsonrpc":"2.0","method":"Player.GetItem","params":{"properties":["title","rating","mpaa","runtime"],"playerid":1},"id":"VideoGetItem"},{"jsonrpc":"2.0","id":1,"method":"Player.GetItem","params":{"playerid":1,"properties":["art"]}}]';

		$json = $this->apiCAll($jsonRpc);

		return $json;
	}

	private function syncTv($sections)
	{
		//
	}
}
