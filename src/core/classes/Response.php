<?php
/**
* A simple class for a mysqli connection
*/
namespace Gila;

class Request
{
  static public function json($args, $code=200)
  {
    @http_response_code($code);
    echo json_encode($args);
    exit;
  }

  static public function success($args, $code=200)
  {
    @http_response_code($code);
    echo json_encode(merge_array(['success'=>true], $args));
    exit;
  }

  static public function error($message, $code=401)
  {
    @http_response_code($code);
    echo json_encode(['success'=>false, 'message'=>$message]);
    exit;
  }

}
