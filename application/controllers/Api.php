<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function saveOrUpdate()
	{
		$this->load->database();
		$this->load->model("mapModel");

		$title = $this->input->post("title");
		$type = $this->input->post("type");
		$pointers = ($this->input->post("pointers"));
		$ip = get_ip();

		// if($this->input->post("key")){
		// 	$this->mapModel->update();
		// }else{
		header("Access-Control-Allow-Origin: *");
		header('Content-Type: application/json');

		$key = uniqid("mk");

		$id = $this->mapModel->savePoints($key,$title,$pointers,$ip,$type);
		// }

		if($id == null ){
			die(json_encode([
				"isSuccess" => false
			]));
		}

		die(json_encode([
			"isSuccess" => true,
			"data" => ["key"=>$key],
		]));
		
		session_write_close();
	}

	public function marker($key,$type="fiddle"){
		$this->load->database();
		$this->load->model("mapModel");
		$item = $this->mapModel->get_fiddle($key);
		header("Access-Control-Allow-Origin: *");
		header('Content-Type: application/json');

		if($item ==null){
			http_response_code(404);
			die(json_encode([
				"isSuccess" => false,
				"message" => "key not exist "
			]));
		}	


		if($type == "fiddle"){
			die(json_encode($this->_render_fiddle_format([$item])));
		}else if($type =="geojson"){
			die(json_encode($this->_render_geojson_format([$item])));
		}
	}

	public function _render_geojson_format($fiddles){
		$features = [];
		foreach($fiddles as $fiddle){
			$feature = [
				"type"=>"Feature",
				"properties" => [],
				"geometry" => [

				]
			];

			if($fiddle->type == 0 ){
				$feature["geometry"]["type"] = "Point";

				$point = json_decode($fiddle->points)[0];

				$feature["geometry"]["coordinates"] = $this->_point_to_coordinate($point);
			}else if($fiddle->type == 1){
				$feature["geometry"]["type"] = "LineString";
				$coordinates= [];

				$points = json_decode($fiddle->points);
				foreach($points as $point){
					$coordinates[] = $this->_point_to_coordinate($point);
				}

				$feature["geometry"]["coordinates"] = $coordinates;
			}else if($fiddle->type == 2){
				$feature["geometry"]["type"] = "Polygon";
				$coordinates= [];

				$points = json_decode($fiddle->points);
				foreach($points as $point){
					$coordinates[] = $this->_point_to_coordinate($point);
				}

				//geojson require first and last have to be same
				$coordinates[] = $this->_point_to_coordinate($points[0]);

				$feature["geometry"]["coordinates"] = [$coordinates];

			}
			$features[] = $feature;
		}
		return [
			"type" => "FeatureCollection",
			"features" => $features
		];
		
	}

	public function _point_to_coordinate($point){
		return [
			$point->latlng->lng,
			$point->latlng->lat
		];
	} 
	public function _render_fiddle_format($fiddles){
		$types = [0 => "Point",1=>"Line",2=>"Area"];
		$res = [];
		foreach($fiddles as $item){
			$res[$item->key] = ["key" => $item->key,
				"type" => $item->type,
				"title" => $item->title,
				"type_name" => $types[$item->type],
				"latlngs" => json_decode($item->points)];
		}
		return [
			"isSuccess" => true,
			"data" => $res
		];
	}

}
