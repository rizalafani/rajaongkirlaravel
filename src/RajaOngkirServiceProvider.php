<?php

namespace rizalafani\rajaongkirlaravel;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class RajaOngkirServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
                __DIR__.'/config/rajaongkir.php' => config_path().'/rajaongkir.php',
            ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('rajaOngkir', function()
        {
            return new RajaOngkir;
        });
    }
}