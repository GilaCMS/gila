<?php
/**
* A simple class for a mysqli connection
*/
namespace Gila;

class Request
{
  static private $errors = [];
  static private $load;

  static public function validate($args, $autoexit=true)
  {
    self::load(); 
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

  static public function all()
  {
    self::load();
    return array_merge($_GET, $_POST);
  }

  static public function load()
  {
    if (!isset(self::$load)) {
      if (empty($_POST)) {
        $_POST = json_decode(file_get_contents("php://input"), true);
      }

    }
    return self::$load;
  }

  static public function get($key)
  {
    return $_GET[$key] ?? null;
  }

  static public function post($key)
  {
    return $_POST[$key] ?? null;
  }

  static public function key($key)
  {
    self::load();
    return $_POST[$key] ?? ($_GET[$key] ?? null);
  }

  static public function validateParam($key, $rules) {
    if (is_string($rules)) {
      $rules = explode('|', $rules);
    }
    $value = self::value($key);

    foreach ($rules as $line) {
      $array = explode(':', $line);
      $rule = $array[0];
      $p = $array[1] ?? [];
      if ($rule==='required' && empty($value)) {
        self::$errors[] = __("$key is required");
      }
      if ($rule==='date' && strtotime($value)===false) {
        self::$errors[] = __("$key is not a valid date");
      }
      if ($rule==='email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
        self::$errors[] = __("$key is not an email");
      }
      if ($rule==='url' && !filter_var($value, FILTER_VALIDATE_URL)) {
        self::$errors[] = __("$key is not a valid url");
      }
      if ($rule==='numeric' && !is_numeric($value)) {
        self::$errors[] = __("$key is not a number");
      }
      if ($rule==='unique') {
        $table = $db->res($p[0]);
        $field = $db->res($key);
        if (0 < $db->value("SELECT COUNT(*) FROM $table WHERE $field=?", [$value])) {
          self::$errors[] = __("$key value already exists");
        }
      }
      if ($rule==='int') if (!is_numeric($value)) {
        self::$errors[] = __("$key is not a number");
      } else {
        $value = (int)$value;
      }
    }
    return $value;
  }
}
