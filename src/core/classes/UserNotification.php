<?php

namespace Gila;

class UserNotification
{

  public function send(int $user_id, $type, $details = '', $url = '')
  {
    global $db;
    $db->query("INSERT INTO user_notification(`user_id`,`type`,details,`url`,unread) VALUES(?,?,?,?,1)", [$user_id, $type, $details, $url]);
  }

  public function unread($type = null)
  {
    global $db;
    $user_id = Session::userId();
    if(empty($type)) {
      return $db->get("SELECT * FROM user_notification WHERE user_id=? AND unread=1 ORDER BY created DESC", [$user_id]);
    }
    return $db->get("SELECT * FROM user_notification WHERE user_id=? AND unread=1 AND type=? ORDER BY created DESC", [$user_id, $type]);
  }

  public function countNew($type = null)
  {
    global $db;
    $user_id = Session::userId();
    if(empty($type)) {
      return $db->value("SELECT COUNT(*) FROM user_notification WHERE user_id=? AND unread=1 ORDER BY created DESC", [$user_id]);
    }
    return $db->value("SELECT COUNT(*) FROM user_notification WHERE user_id=? AND unread=1 AND type=? ORDER BY created DESC", [$user_id, $type]);
  }

  public function setRead($id)
  {
    global $db;
    return $db->query("UPDATE user_notification SET unread=0 WHERE id=?", [$id]);
  }
}
