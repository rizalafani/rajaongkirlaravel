<?php

namespace rizalafani\rajaongkirlaravel;

use rizalafani\rajaongkirlaravel\app\Provinsi;
use rizalafani\rajaongkirlaravel\app\Kota;
use rizalafani\rajaongkirlaravel\app\Cost;

class RajaOngkir {
	public function Provinsi(){
		return new Provinsi;
	}

	public function Kota(){
		return new Kota;
	}

	public function Cost($attributes){
		return new Cost($attributes);
	}
}