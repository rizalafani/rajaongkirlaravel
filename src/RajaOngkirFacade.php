<?php

namespace rizalafani\rajaongkirlaravel;

use Illuminate\Support\Facades\Facade;

class RajaOngkirFacade extends Facade{
	protected static function getFacadeAccessor() { return 'rajaOngkir'; }
}