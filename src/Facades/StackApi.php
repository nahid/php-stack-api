<?php
namespace Nahid\StackApis\Facades;
use Illuminate\Support\Facades\Facade;
class StackApi extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'StackApi';
    }
}