<?php

require_once 'vendor/autoload.php';

use Nahid\StackApis\StackApi;

$api = new StackApi();

echo '<pre>';
 print_r($api->users()->moderators()->get());
 echo '</pre>';
