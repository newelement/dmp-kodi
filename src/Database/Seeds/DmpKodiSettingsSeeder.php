<?php

use Illuminate\Database\Seeder;
use Newelement\DmpKodi\Models\DmpKodiSetting;

class DmpKodiSettingsSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DmpKodiSetting::firstOrCreate(
			[ 'setting_name' => 'enable' ],
			[ 'bool_value' => 0 ]
		);
	}
}
