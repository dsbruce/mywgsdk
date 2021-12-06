<?php
/**
 * Created by PhpStorm.
 * User: zwb
 * Date: 2021/12/6
 * Time: 14:39
 */

namespace Zwb\Mywgsdk;


use Illuminate\Support\ServiceProvider;

class MywgsdkProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/mywgsdk.php' => config_path('mywgsdk.php')
        ]);
    }

    public function register()
    {
        $this->app->singleton('mywgsdk', function (){
            return new Mywgsdk();
        });
    }
}