<?php

namespace Gila;

class UserNotification
{
  public static function send(int $user_id, $type, $details = '', $url = '')
  {
    DB::query("INSERT INTO user_notification(`user_id`,`type`,details,`url`,unread) VALUES(?,?,?,?,1)", [$user_id, $type, $details, $url]);
  }

  public static function unread($type = null)
  {
    $user_id = Session::userId();
    if (empty($type)) {
      return DB::get("SELECT * FROM user_notification WHERE user_id=? AND unread=1 ORDER BY created DESC", [$user_id]);
    }
    return DB::get("SELECT * FROM user_notification WHERE user_id=? AND unread=1 AND type=? ORDER BY created DESC", [$user_id, $type]);
  }

  public static function countNew($type = null)
  {
    $user_id = Session::userId();
    if (empty($type)) {
      return DB::value("SELECT COUNT(*) FROM user_notification WHERE user_id=? AND unread=1 ORDER BY created DESC", [$user_id]);
    }
    return DB::value("SELECT COUNT(*) FROM user_notification WHERE user_id=? AND unread=1 AND type=? ORDER BY created DESC", [$user_id, $type]);
  }

  public static function countNewByType()
  {
    $user_id = Session::userId();
    return DB::getAssoc("SELECT type,COUNT(*) AS new FROM user_notification WHERE user_id=? AND unread=1 ORDER BY created DESC", [$user_id]);
  }

  public static function setRead($id)
  {
    return DB::query("UPDATE user_notification SET unread=0 WHERE id=?", [$id]);
  }
}
