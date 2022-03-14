<?php

namespace Gila;

class UserMessage
{
  public static function send(int $receiver_id, string $message, int $sender_id)
  {
    global $db;
    $db->query("INSERT INTO user_message(`sender_id`,`receiver_id`,`message`,unread)
    VALUES(?,?,?,1)", [$sender_id??Session::userId(), $user_id, $message]);
  }

  public static function unread()
  {
    global $db;
    $receiver_id = Session::userId();
    return $db->get("SELECT * FROM user_message WHERE receiver_id=? AND unread=1 ORDER BY created DESC", [$receiver_id]);
  }

  public static function countNew($type = null)
  {
    global $db;
    $receiver_id = Session::userId();
    return $db->value("SELECT COUNT(*) FROM user_message WHERE receiver_id=? AND unread=1 ORDER BY created DESC", [$receiver_id]);
  }

  public static function setRead($id)
  {
    global $db;
    return $db->query("UPDATE user_message SET unread=0 WHERE id=?", [$id]);
  }
}
