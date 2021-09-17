<?php

class EmbedCodeController extends Gila\Controller
{
  public function __construct()
  {
    self::admin();
    if (!Gila\Session::hasPrivilege('admin')) {
      http_response_code(403);
      exit;
    }
    Config::addLang('core/lang/admin/');
  }

  public function indexAction()
  {
    View::renderAdmin('admin/embed-code.php', 'embed-code');
  }
}
