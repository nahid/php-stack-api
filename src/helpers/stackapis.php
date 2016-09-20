<?php

if(!function_exists('get_stack_api_auth_url')) {
    function get_stack_api_auth_url()
    {
        return app('StackApi')->makeAuthUri();
    }
}

