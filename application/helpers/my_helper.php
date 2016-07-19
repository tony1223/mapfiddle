<?php
defined('BASEPATH') OR exit('No direct script access allowed');


function array_first_item($arr){
  return count($arr) > 0 ? $arr[0] : null;
}


function h($string, $quote_style = null, $charset = null, $double_encode = null) {
  return @htmlspecialchars($string, $quote_style , $charset);
}

function uh($string){
  return html_entity_decode($string,ENT_QUOTES);
}


function db_current_date($format = "Y-m-d H:i:s"){
  return date("Y-m-d H:i:s");
}


function db_current_timestamp(){
  return time() * 1000;
}


function db_current_gmt_date(){
  return gmdate("Y-m-d\TH:i:s\Z");
}



function _date_gmt($inp_val){

  if($inp_val == null || $inp_val=="0000-00-00 00:00:00"){
    return "";
  }
  if(is_string($inp_val)){
    $val = strtotime($inp_val);
    if($val == -1){
      return "invalid date";
    }
  }else{
    $val = $inp_val;
  }
  return gmdate("Y-m-d\TH:i:s\Z",$val);
}

function _date_format($inp_val,$format = "Y-m-d H:i:s"){

  if($inp_val == null || $inp_val=="0000-00-00 00:00:00"){
    return "";
  }
  if(is_numeric($inp_val)){
    $val = intval($inp_val);
  }else if(is_string($inp_val)){
    $val = strtotime($inp_val);
    if($val == -1){
      return "invalid date";
    }
  }else{
    $val = $inp_val;
  }

  return date($format,$val);
}

function _date_format_ms($inp_val,$format = "Y-m-d H:i:s"){
  if($inp_val == null || $inp_val=="0000-00-00 00:00:00"){
    return "";
  }
  if(is_string($inp_val)){
    $val = strtotime($inp_val);
    if($val == -1){
      return "invalid date";
    }
  }else{
    $val = $inp_val/1000;
  }

  return date($format,$val);
}

function _display_date_with_fulldate_ms($inp_val,$format = null,$linebreak = "<Br />"){
  if($inp_val == null || $inp_val=="0000-00-00 00:00:00"){
    return "";
  }
  if($format == null){
   $format = "Y-m-d H:i:s";
  }
  if(is_string($inp_val)){
    $val = strtotime($inp_val);
    if($val == -1){
      return "invalid date";
    }
  }else{
    $val = intval($inp_val / 1000,10);
  }

  return _display_date_with_fulldate($val,$format,$linebreak);
}

function _display_date_with_fulldate($inp_val,$format = null,$linebreak = "<Br />"){

  if($format == null){
    $format = "Y-m-d H:i:s";
  }
  if($inp_val == null || $inp_val=="0000-00-00 00:00:00"){
    return "";
  }
  if(is_string($inp_val)){
    $val = strtotime($inp_val);
    if($val == -1){
      return "invalid date";
    }
  }else{
    $val = $inp_val;
  }
  $diff = time() - $val;
  if ($diff < 0) {
    return date($format,$val);
  } elseif ($diff < 60) {
    return date($format,$val).$linebreak.$diff . ' 秒前';
  } elseif ($diff < 3600) {
    return date($format,$val).$linebreak.floor($diff/60) . ' 分鐘前';
  } elseif ($diff < 86400) {
    return date($format,$val).$linebreak.floor($diff/3600) . ' 小時前';
  } elseif ($diff < 604800) {
    return date($format,$val).$linebreak.floor($diff/86400) . ' 天前';
  } else {
    return date($format,$val);
      //return floor($diff/604800) . '週前';
  }
}


function get_ip(){
  $ip = null;
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}
