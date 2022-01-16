<?php
/**
* A simple class for a mysqli connection
*/
namespace Gila;

class Request
{
  static public function validate($args)
  {
    if (empty($_POST)) {
      $_POST = json_decode(file_get_contents("php://input"));
    }

    foreach ($args as $key=>$rules) {
      self::validateParam($key, $rules);
    }
  }

  static public function get($key)
  {
    return $_GET[$key] ?? null;
  }

  static public function post($key)
  {
    return $_POST[$key] ?? null;
  }

  static public function validateParam($key, $rules) {
    $rules = explode('|', $rules);
    $value = self::post($key);
    foreach ($rules as $rule) {
      if ($rule==='required' && $value===null) Response::json([
        'success' => false,
        'message' => __("Field $key is required")
      ]);
      if ($rule==='email' && filter_var($value, FILTER_VALIDATE_EMAIL)) Response::json([
        'success' => false,
        'message' => __("Field $key is not an email format")
      ]);
    }
  }
}
