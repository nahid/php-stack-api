<?php

namespace Nahid\StackApis;

use Illuminate\Config\Repository;

class ConfigManager
{
	public $config;

	function __construct()
	{
		$data = include(__DIR__ . '/../config/stackapi.php');
		$this->config = new Repository($data);
	}

}