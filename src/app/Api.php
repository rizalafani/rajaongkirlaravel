<?php

namespace rizalafani\rajaongkirlaravel\app;

use Exception;

abstract class Api {
	protected $method;
	protected $parameters;
	protected $data;
	protected $endPointAPI;
	protected $overWriteOptions = [];
	protected $apiKey;

	public function __construct(){
		$this->endPointAPI = config('rajaongkir.end_point_api', 'http://rajaongkir.com/api/starter');
		$this->apiKey = config('rajaongkir.api_key');
	}

	public function all(){
		return $this->GetData()->data;
	}

	public function find($id){
		$this->parameters = "?id=".$id;
		return $this->GetData()->data;
	}

	public function search($column, $searchKey){
		$data = ( empty($this->data) ) ? $this->GetData()->data : $this->data;

		$rowColumn = array_column($data, $column);
		$s = preg_quote(ucwords($searchKey), '~');
		$res = preg_grep('~' . $s . '~', $rowColumn);
		$resKey = array_keys($res);
		$temp = [];
		foreach($data as $key => $val){
			if(in_array($key, $resKey)){
				array_push($temp, $val);
			}
		}

		$this->data = $temp;

		return $this;
	}

	public function get(){
		return $this->data;
	}

	public function count(){
		( empty($this->data) ) ? $this->GetData()->data : $this->data;
		return count($this->data);
	}

	protected function GetData(){
		$curl = curl_init();

		$options = [
			CURLOPT_URL => $this->endPointAPI."/".$this->method.$this->parameters,
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

		curl_setopt_array($curl, $options);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		 	throw new Exception($err, 1);	
		} else {
			$data = json_decode($response, true);
			$code = $data['rajaongkir']['status']['code'];
			if($code == 400){
				throw new Exception($data['rajaongkir']['status']['description'], 1);		
			}else{
				$this->data = $data['rajaongkir']['results'];
				return $this;
			}
		}
	}
}
