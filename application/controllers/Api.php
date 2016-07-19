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

	public function marker($key){
		$this->load->database();
		$this->load->model("mapModel");
		$item = $this->mapModel->get_fiddle($key);
		header("Access-Control-Allow-Origin: *");
		header('Content-Type: application/json');

		if($item ==null){
			die(json_encode([
				"isSuccess" => false,
				"message" => "key not exist "
			]));
		}	

		$types = [0 => "Point",1=>"Line",2=>"Area"];

		$res = [];
		$res[$item->key] = ["key" => $item->key,
			"type" => $item->type,
			"type_name" => $types[$item->type],
			"latlngs" => json_decode($item->points)];

		die(json_encode([
			"isSuccess" => true,
			"data" => $res
		]));
	}

}
