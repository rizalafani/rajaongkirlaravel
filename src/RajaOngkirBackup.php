<?php

namespace rizalafani\rajaongkirlaravel;

use Exception;
use Input;

class RajaOngkir {
	public $method;
	public $overWriteOptions = [];
	public $endPointAPI = 'http://rajaongkir.com/api/starter';
	public $apiKey = '8effcbab899a19e5534ed2772f27c2e8';

	public function GetProvinsi(){
		$this->method = "province".$this->BuildParams();
		$result = $this->GetData();
		$code = $result['rajaongkir']['status']['code'];
		if($code == 400){
			throw new Exception($result['rajaongkir']['status']['description'], 1);			
		}else{
			$data = $result['rajaongkir']['results'];
			return $this->ParseDataLokasi($data, 'province');
		}
	}

	public function GetKota(){
		$this->method = "city".$this->BuildParams();
		$result = $this->GetData();
		$code = $result['rajaongkir']['status']['code'];
		if($code == 400){
			throw new Exception($result['rajaongkir']['status']['description'], 1);	
		}else{
			$data = $result['rajaongkir']['results'];
			return $this->ParseDataLokasi($data, 'city_name');
		}
	}

	public function GetOngkir(){
		if( Input::get('origin') && Input::get('destination') && Input::get('weight') && Input::get('courier') ){
			$this->method = "cost";
			$this->overWriteOptions = [
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $this->BuildParams(),
				CURLOPT_HTTPHEADER => [
					"content-type: application/x-www-form-urlencoded",
	    			"key: ".$this->apiKey
				]
			];

			$result = $this->GetData();
			$code = $result['rajaongkir']['status']['code'];
			if($code == 400){
				throw new Exception($result['rajaongkir']['status']['description'], 1);	
			}else{
				return [
					'info' => [
						'origin' => $result['rajaongkir']['origin_details'],
						'destination' => $result['rajaongkir']['destination_details'],
					],
					'courier' => $result['rajaongkir']['results'],
				];
			}
		}else{
			echo "Input tidak lengkap !!";
			exit(0);
		}
	}

	protected function BuildParams(){
		$params = Input::except(['f', 's']);

		if(isset($params['weight'])){
			$params['weight'] = $params['weight']*1000;
		}

		$data_ret = ( !empty($params) ? '?'.http_build_query($params) : '');
		$data_ret = isset($params['weight']) ? substr($data_ret, 1) : $data_ret;
		return $data_ret;
	}

	protected function ParseDataLokasi($data, $columnSearch = "province"){
		if( Input::get('s') ){
			$rowColumn = array_column($data, $columnSearch);
			$s = preg_quote(ucwords(Input::get('s')), '~');
			$res = preg_grep('~' . $s . '~', $rowColumn);
			$resKey = array_keys($res);
			$temp = [];
			foreach($data as $key => $val){
				if(in_array($key, $resKey)){
					array_push($temp, $val);
				}
			}

			return $temp;
		}

		return $data;
	}

	protected function GetData(){
		$curl = curl_init();

		$options = [
			CURLOPT_URL => $this->endPointAPI."/".$this->method,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => [
				"key: ".$this->apiKey
			],
		];

		foreach( $this->overWriteOptions as $key => $val){
			$options[$key] = $val;
		}

		//echo "<pre>", print_r($options), "</pre>";

		curl_setopt_array($curl, $options);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		 	throw new Exception($err, 1);		 	
		} else {
			return json_decode($response, true);
		}
	}
}