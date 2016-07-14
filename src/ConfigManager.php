<?php

namespace Nahid\StackApis;

use Illuminate\Config\Repository;

class ConfigManager
{
    public $config;

    public function __construct($config)
    {
    	$data = '';
    	if(!is_null($config)) {
    		$data = $config;
    	}else {
    		$data = include __DIR__.'/../config/stackapi.php';
    	}
       
        $this->config = new Repository($data);
    }
}
