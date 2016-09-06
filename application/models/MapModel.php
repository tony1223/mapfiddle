<?php

class MapModel extends CI_Model {

  public function __construct()
  {
    // Call the CI_Model constructor
    parent::__construct();
  }
  
  public function savePoints($key,$title,$pointers,$ip,$type){
    $this->db->insert("fiddles",["key" => $key,"title" => $title,"points" => $pointers,"ip" => $ip,"type" => $type ]);

    return $this->db->insert_id();
  }

  public function get_fiddle($key){
    return array_first_item(
      $this->db->get_where("fiddles",["key"=> $key])->result()
    );
  }

  public function get_fiddles($keys){

    $this->db->where_in("key" , $keys);
    $q = $this->db->get("fiddles");

    return array_first_item(
      $q->result()
    );
  }
}