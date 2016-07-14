<?php

require 'vendor/autoload.php';
use Nahid\StackApis\StackApi;

$api = new StackApi();
echo $api->makeAuthLink('Login');
