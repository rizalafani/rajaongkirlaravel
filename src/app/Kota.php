<?php

namespace rizalafani\rajaongkirlaravel\app;

class Kota extends Api {
	protected $method = "city";

	public function byProvinsi($province_id){
		$this->parameters = "?province=".$province_id;
		return $this->GetData();
	}
}