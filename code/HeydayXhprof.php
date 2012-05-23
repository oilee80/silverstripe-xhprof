<?php

class HeydayXhprof
{

	static protected $default_flags = false;
	static protected $app_name = false;
	static protected $started = false;

	public static function start($app_name = false, $flags = false)
	{

		if (extension_loaded('xhprof')) {

			if (self::$started) {

				user_error("You have already started xhprof");

			}

			require_once dirname(__FILE__) . '/ThirdParty/xhprof_lib/utils/xhprof_lib.php';
			require_once dirname(__FILE__) . '/ThirdParty/xhprof_lib/utils/xhprof_runs.php';

			xhprof_enable($flags !== false ? $flags : self::get_default_flags());

			self::$started = true;

			if ($app_name) {

				self::set_app_name($app_name);

			}

		}

	}

	public static function end()
	{

		if (extension_loaded('xhprof')) {

			if (!self::$started) {

				user_error("You haven't started a profile");

			}

			$appName = self::get_app_name();

			$xhprof_data = xhprof_disable();

			$xhprof_runs = new XHProfRuns_Default();

			$run_id = $xhprof_runs->save_run($xhprof_data, $appName);

			$xhprofRun = new HeydayXhprofRun(array(
				'Run' => $run_id,
				'AppID' => HeydayXhprofApp::get($appName)->ID,
				'Url' => isset($_GET['url']) ? $_GET['url'] : false
			));

			$xhprofRun->write();

		}

	}

	public static function get_default_flags()
	{

		if (self::$default_flags === false) {

			self::$default_flags = XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY;

		}

		return self::$default_flags;

	}

	public static function set_default_flags($flags)
	{

		self::$default_flags = $flags;

	}

	public static function get_app_name()
	{

		if (self::$app_name == false) {

			global $project;

			self::$app_name = $project;

		}

		return self::$app_name;

	}

	public static function set_app_name($app_name)
	{

		self::$app_name = $app_name;

	}

}