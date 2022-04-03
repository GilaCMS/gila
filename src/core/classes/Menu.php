<?php

namespace Gila;

class Menu
{
  private static $active = false;
  public static $editableLinks = false;
  public static $liClass = '';
  public static $liActive = '';
  public static $ulClass = 'dropdown-menu';
  public static $aClass = '';
  public static $iClass = '';
  public static $ddIcon = '';
  public static $span = true;

  public static function getContents($menu)
  {
    if ($data = Cache::get('menu--'.$menu, 86400, [Config::mt('menu')])) {
      return $data;
    }
    if ($data = DB::value("SELECT `data` FROM menu WHERE `menu`=?;", [$menu])) {
      Cache::set('menu--'.$menu, $data, [Config::mt('menu')]);
      return $data;
    }

    return "{type:\"menu\",children:[]}";
  }

  public static function setContents($menu, $data)
  {
    Config::setMt('menu');
    DB::query("REPLACE INTO menu(`menu`,`data`) VALUES(?,?);", [$menu, $data]);
    $menuLN = $menu.'.'.Config::lang();
    Cache::remove($menuLN.'_data');
  }

  public static function getData($menu)
  {
    $menuLN = $menu.'.'.Config::lang();
    $data =  Cache::remember($menuLN.'_data', 100, function ($u) {
      return DB::value(
        "SELECT `data` FROM menu WHERE `menu`=?
        UNION SELECT `data` FROM menu WHERE `menu`=?;",
        [$u[1], $u[2]]
      );
    }, [Config::mt('menu'),$menuLN, $menu]);

    if ($data) {
      return json_decode($data, true);
    }
    return self::defaultData();
  }

  public static function remove($menu)
  {
    Cache::set('menu--'.$menu);
  }

  public static function defaultData()
  {
    $widget_data = (object) array('type'=>'menu','children' => []);
    $widget_data->children[] = ['type'=>'link','url'=>'','name'=>__('Home')];
    $ql = "SELECT id,title FROM postcategory;";
    $pages = DB::get($ql);
    foreach ($pages as $p) {
      $widget_data->children[] = ['type'=>"postcategory",'id'=>$p[0]];
    }
    foreach (Page::genPublished() as $p) {
      if (count($widget_data->children)<6 && !empty($p['slug'])) {
        $widget_data->children[] = ['type'=>'page','id'=>$p[0]];
      }
    }
    return (array) $widget_data;
  }

  public static function convert($data)
  {
    $items = [];
    if ($type = $data['type']) {
      if (isset($data['children'])) {
        $children = [];
        foreach ($data['children'] as $mi) {
          $children[] = self::convert($mi);
        }
      }
      if ($type=='menu') {
        return $children;
      }
      if ($type=='dir') {
        return ['name'=>$data['title']??($data['name']??''), 'url'=>'#',
        'children'=>$children];
      }
      if ($type=='page') {
        if ($r=Page::getById(@$data['id'])) {
          $url = $r['slug'];
          if (Config::lang()!==Config::get('language')) {
            $url = Config::lang().'/'.$url;
          }
          if (self::$editableLinks) {
            $url = self::$editableLinks.'/'.$r['id'];
          }
          $name = $r['title'];
          return ['name'=>$name, 'url'=>$url];
        }
        if (is_string($data['id']) && !is_numeric($data['id'])) {
          $prefix = (Config::lang()!==Config::get('language'))? '/'.Config::lang(): '';
          return ['name'=>__(ucfirst($data['id'])), 'url'=>$prefix.'/'.$data['id']];
        }
      }
      if ($type=='postcategory') {
        $ql = "SELECT id,title,slug FROM postcategory WHERE id=?;";
        $res = DB::query($ql, @$data['id']);
        while ($r=mysqli_fetch_array($res)) {
          $url = "category/".$r[0].'/'.$r[2];
          $name = $r[1];
        }
        return ['name'=>$name, 'url'=>$url];
      }
      if ($res = MenuItemTypes::get($data)) {
        list($url, $name) = $res;
        return ['name'=>$name, 'url'=>$url];
      }
    }
    if (self::$editableLinks && $r=Page::getBySlug($data['url'])) {
      return ['type'=>$data['type'], 'name'=>$data['title']??$data['name'], 'url'=>self::$editableLinks.'/'.$r['id']];
    } else {
      return ['type'=>$data['type'], 'name'=>$data['title']??$data['name'], 'url'=>$data['url']??'#'];
    }
  }

  public static function getHtml($items, $base='')
  {
    $html = "";
    self::$active = false;
    foreach ($items as $key => $item) {
      $url = $item['url'] ?? $item[1];
      $label = $item['title'] ?? ($item['name']??$item[0]);
      if (isset($item['access'])) {
        if (!Session::hasPrivilege($item['access'])) {
          continue;
        }
      }
      $icon = self::$iClass;
      if (isset($item['icon'])) {
        $icon .= ' fa-'.$item['icon'];
      }
      $url = ($url=='#')? 'javascript:void(0)': htmlentities($url);
      $badge = "";
      if (isset($item['counter'])) {
        $c = is_callable($item['counter'])? $item['counter'](): $item['counter'];
        if ($c>0) {
          $badge = " <span class=\"g-badge\">$c</span>";
        }
      }
      $liClass = self::$liClass;
      $aClass = self::$aClass;
      if (isset($item['children'])) {
        $liClass .= ' dropdown';
        $childrenHtml = '<ul class="'.self::$ulClass.'">';
        $childrenHtml .= self::getHtml($item['children'], $base);
        $childrenHtml .= "</ul>";
        $ddIcon = self::$ddIcon;

        if (self::$active===true) {
          self::$active = false;
          $liClass .= ' '.self::$liActive;
        }
      } else {
        $childrenHtml = '';
        $ddIcon = '';
      }
      $current = !empty($base)? $base: $_SERVER['REQUEST_URI'];
      if (trim($url, '/')==trim($current, '/')) {
        self::$active = true;
        $liClass .= ' active';
        $aClass .= ' active';
      }
      if ($item['type']=='btn') $aClass .= ' g-btn btn btn-primary';
      if ($item['type']=='btn2') $aClass .= ' g-btn btn btn-secondary';

      $liClass = $liClass!==''? ' class="'.$liClass.'"': '';
      $aClass = $aClass!==''? ' class="'.$aClass.'"': '';
      $html .= "<li$liClass><a$aClass href='".$url."'>";
      if (!empty($icon)) {
        $html .= "<i class='fa {$icon}'></i>";
      }
      if (self::$span) {
        $html .= '<span>'.Config::tr($label).$badge.$ddIcon.'</span></a>';
      } else {
        $html .= '<p>'.Config::tr($label).$badge.$ddIcon.'</p></a>';
      }
      $html .= $childrenHtml.'</li>';
    }
    return $html;
  }

  public static function getList()
  {
    return DB::getList("SELECT `menu` FROM `menu`;");
  }

  public static function bootstrap()
  {
    self::$liClass = 'nav-item';
    self::$liActive = 'menu-open';
    self::$ulClass = 'nav nav-treeview';
    self::$aClass = 'nav-link';
    self::$iClass = 'nav-icon fas';
    self::$ddIcon = '<i class="fa fa-angle-left right"></i>';
    self::$span = false;
  }
}
