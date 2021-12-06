<?php
/**
 * Created by PhpStorm.
 * User: zwb
 * Date: 2021/12/6
 * Time: 14:42
 */

namespace Zwb\Mywgsdk\Facades;


use Illuminate\Support\Facades\Facade;

class Mywgsdk extends Facade
{
    protected static function getFacadeAccessor()
    {
       return 'mywgsdk';
    }

}