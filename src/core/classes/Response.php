<?php
/**
* A simple class for a mysqli connection
*/
namespace Gila;

class Response
{
  static public function json($args, $code=200)
  {
    @http_response_code($code);
    echo json_encode($args);
    exit;
  }

  static public function success($args=[], $code=200)
  {
    @http_response_code($code);
    echo json_encode(array_merge(['success'=>true], $args));
    exit;
  }

  static public function error($message='', $code=401)
  {
    @http_response_code($code);
    echo json_encode(['success'=>false, 'error'=>$message]);
    exit;
  }

}
