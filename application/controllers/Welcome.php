<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

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
	public function index()
	{
		$this->load->view('welcome_message',[
				"fiddle_title" => "new Marker",
				"points" => "[]",
				"fiddle_type" => 0
		]);
		session_write_close();
	}

	public function marker($key){
		$this->load->database();
		$this->load->model("mapModel");
		$item = $this->mapModel->get_fiddle($key);

		if($item ==null){
			return show_404();
		}


		$this->load->view('welcome_message',[
				"fiddle_title" => $item->title,
				"points" => json_encode(json_decode($item->points)),
				"fiddle_type" => $item->type
		]);
	}

}
