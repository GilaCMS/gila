<?php
/**
* A simple class for a mysqli connection
*/
namespace Gila;

class Request
{
  static private $errors = []; 

  static public function validate($args, $autoexit=false)
  {
    $data = [];
    if (empty($_POST)) {
      $_POST = json_decode(file_get_contents("php://input"));
    }

    foreach ($args as $key=>$rules) {
      $data[$key] = self::validateParam($key, $rules);
    }

    if ($autoexit && !empty(self::$errors)) {
      Response::json([
        'success'=>false,
        'error'=>self::$errors[0]
      ]);
    }
    return $data;
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
    if (is_string($rules)) {
      $rules = explode('|', $rules);
    }
    $value = $_REQUEST[$key] ?? ($_POST[$key] ?? null);
    foreach ($rules as $rule) {
      if ($rule==='required' && $value===null) {
        self::$errors[] = __("Field $key is required");
      }
      if ($rule==='email' && filter_var($value, FILTER_VALIDATE_EMAIL)) {
        self::$errors[] = __("Field $key is not an email");
      }
      if ($rule==='numeric' && !is_numeric($value)) {
        self::$errors[] = __("Field $key is not a number");
      }
    }
    return $value;
  }
}
