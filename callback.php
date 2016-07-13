<?php
require_once 'vendor/autoload.php';

use Nahid\StackApis\StackApi;

$api = new StackApi;

 var_dump($api->me()->info());
