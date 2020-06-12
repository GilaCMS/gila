<?php

class blocks extends Controller
{
  static private $draft = false;

  public function __construct ()
  {
      self::admin();
      Gila::addLang('core/lang/admin/');
  }

  function indexAction ()
  {
    $table = Router::request('t');
    $id = Router::get('id',2);
    $widgets = self::readBlocks($table, $id);
    View::set('contentType', $table);
    View::set('id', $id);
    View::set('isDraft', self::$draft);
    View::set('widgets', $widgets);
    View::renderAdmin("content-block.php","blocks");
  }

  function editAction ()
  {
    global $db;
    if ($id = Router::get('id', 2)) {
      $idArray = explode('_', $id);
      View::set('widget_id', $id);
      View::set('contentType', $idArray[0]);
      View::set('id', $idArray[1]);
      View::set('type', $_GET['type']);
      View::set('pos', $idArray[2]);
      View::set('widgets', self::readBlocks($idArray[0], $idArray[1]));
      View::renderFile('edit_block.php','blocks');
    }
  }

  function updateAction ()
  {
    global $db;
    $id = $_POST['widget_id'];
    $idArray = explode('_', $id);
    $content = $idArray[0];
    $id = (int)$idArray[1];
    $pos = (int)$idArray[2];
    $widgets = self::readBlocks($content, $id);
    if($type = $widgets[$pos]['_type']) {
      $widget_folder = 'src/'.Gila::$widget[$type];
      $fields = include $widget_folder.'/widget.php';
      $widget_data = $_POST['option'] ?? [];

      foreach($widget_data as $key=>$value) {
        $allowed = $fields[$key]['allow_tags'] ?? false;
        if($allowed==false) {
          if(!json_decode($widget_data[$key])) {
            $widget_data[$key] = strip_tags($widget_data[$key]);
          }
        } else if($allowed!==true) {
          $widget_data[$key] = strip_tags($widget_data[$key], $allowed);
          $widget_data[$key] = str_replace('="javascript:', '="', $widget_data[$key]);
        }
      }
      $widget_data['_type'] = $type;
      $widgets[$pos] = $widget_data;
      self::updateBlocks($content, $id, $widgets);
      echo json_encode($widgets);
    }
  }

  function posAction ()
  {
    global $db;
    $rid = $_POST['id'];
    $idArray = explode('_',$rid);
    $content = $idArray[0];
    $id = (int)$idArray[1];
    $pos = (int)$idArray[2];
    $newpos = (int)$_POST['pos'];
    $widgets = self::readBlocks($content, $id);

    if($newpos<0 || $newpos>count($widgets)-1) {
      echo json_encode($widgets);
      return;
    }

    for($i=$pos; $i!=$newpos; $i+=$newpos<=>$pos) {
      // swap blocks
      $nexti = $i+($newpos<=>$pos);
      $tmp = $widgets[$i];
      $widgets[$i] = $widgets[$nexti];
      $widgets[$nexti] = $tmp;
      $nextrid = $content.'_'.$id.'_'.$nexti;
      $wfile = SITE_PATH.'tmp/stacked-wdgt'.$rid.'.jpg';
      $nextwfile = SITE_PATH.'tmp/stacked-wdgt'.$nextrid.'.jpg';
      if(file_exists($wfile)) {
        rename($wfile.'.json', SITE_PATH.'tmp/tmp_wgtjpgjson');
        rename($wfile, SITE_PATH.'tmp/tmp_wgtjpg');
      }
      if(file_exists($nextwfile)) {
        rename($nextwfile.'.json', $wfile.'.json');
        rename($nextwfile, $wfile);
      }
      if(file_exists(SITE_PATH.'tmp/tmp_wgtjpg')) {
        rename(SITE_PATH.'tmp/tmp_wgtjpgjson', $nextwfile.'.json');
        rename(SITE_PATH.'tmp/tmp_wgtjpg', $nextwfile);
      }
    }

    self::updateBlocks($content, $id, $widgets);
    echo json_encode($widgets);
  }


  function createAction ()
  {
    global $db;
    $rid = $_POST['id'];
    $idArray = explode('_',$rid);
    $content = $idArray[0];
    $id = $idArray[1];
    $pos = (int)$idArray[2];
    $widgets = self::readBlocks($content, $id)??[];
    $new = ['_type'=>$_POST['type']];
    $widget_folder = 'src/'.Gila::$widget[$_POST['type']];
    $fields = include $widget_folder.'/widget.php';
    foreach($fields as $key=>$field) {
      if(isset($field['default'])) $new[$key] = $field['default'];
    }
    array_splice( $widgets, $pos, 0, [$new] );
    self::updateBlocks($content, $id, $widgets);
    echo json_encode($widgets);
  }

  function deleteAction ()
  {
    global $db;
    $rid = $_POST['id'];
    $idArray = explode('_',$rid);
    $content = $idArray[0];
    $id = $idArray[1];
    $pos = (int)$idArray[2];
    $widgets = self::readBlocks($content, $id);
    array_splice($widgets, $pos, 1);
    self::updateBlocks($content, $id, $widgets);
    echo json_encode($widgets);
  }

  function displayAction ()
  {
    $content = Router::get('t',1);
    $id = Router::get('id',2);
    $blocks = self::readBlocks($content, $id);
    View::renderFile('blocks-display-head.php', 'blocks');
    echo '<body><article style="transform: scale(0.8);transform-origin: 50% 0%;" id="'.$content.'">';
    Event::fire('body');
    View::blocks($blocks);
    echo '</article></body>';
    Event::fire('foot');
  }

  function saveAction ()
  {
    $rid = $_POST['id'];
    $idArray = explode('_',$rid);
    $content = $idArray[0];
    $id = $idArray[1];
    $widgets = self::readBlocks($content, $id);
    self::saveBlocks($content, $id, $widgets);
  }

  function discardAction ()
  {
    $rid = $_POST['id'];
    $idArray = explode('_',$rid);
    $content = $idArray[0];
    $id = $idArray[1];
    $draftFile = LOG_PATH.'/blocks/'.$content.$id.'.json';
    unlink($draftFile);
    $widgets = self::readBlocks($content, $id);
    echo json_encode($widgets);
  }

  static function readBlocks ($content, $id) {
    global $db;
    $draftFile = LOG_PATH.'/blocks/'.$content.$id.'.json';
    if(file_exists($draftFile)) {
      $json = file_get_contents($draftFile);
      self::$draft = true;
    } else {
      $content = $db->res($content);
      $json = $db->value("SELECT blocks FROM `$content` WHERE id=?;",[$id]);
      self::$draft = false;
    }
    return json_decode($json, true)??[];
  }

  static function updateBlocks ($content, $id, $blocks) {
    $draftFile = LOG_PATH.'/blocks/'.$content.$id.'.json';
    $json = json_encode($blocks);
    Gila::dir(LOG_PATH.'/blocks/');
    file_put_contents($draftFile, $json);
  }

  static function saveBlocks ($content, $id, $blocks) {
    global $db;
    $return = [];
    foreach($blocks as $w) if($w!==null) $return[] = $w;
    $db->query("UPDATE $content SET `blocks`=? WHERE id=?;",[json_encode($return), $id]);
    $draftFile = LOG_PATH.'/blocks/'.$content.$id.'.json';
    unlink($draftFile);
  }

}
