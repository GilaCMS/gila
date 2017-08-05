<?php

//namespace core\controllers;

class blog  //extends controller
{
    public static $page;
    public static $totalPosts;

    function __construct ()
    {
        self::$page = (router::get('page',1))?:1;
    }

    function bbAction(){
      global $db;
      $data = '{"menu":"[\\\r\\\t{\\\"title\\\":\\\"Home\\\",\\\"url\\\":\\\"\\\"},\\\r\\\t{\\\"title\\\":\\\"Page\\\",\\\"url\\\":\\\"page\\\"}\\\r]"}';
      $db->query("INSERT INTO widget VALUES(5,'menu','head',1,1,'$data');");

    }
    function indexAction()
    {
        if ($id=router::get('page_id',1)) {
            $this->postShow($id);
            return;
        }
        if ($id=router::get('p',1)) {
            $this->postShow($id);
            return;
        }
        $this->pageAction();
        //include __DIR__.'/../../../themes/'.$GLOBALS['config']['theme']."/frontpage.php";
    }

    function feedAction()
    {
        $title=gila::config('title');
        $link=gila::config('base');
        $description=gila::config('slogan');
        $items=blog::latestposts();
        include 'src/core/views/rss.php';
    }

    function readerAction()
    {
	$rss = new DOMDocument();
	$rss->load('http://gilacms.com/blog/feed/');
	$feed = array();
	foreach ($rss->getElementsByTagName('item') as $node) {
		$item = array (
			'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
			'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
			'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
			'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
			);
		array_push($feed, $item);
	}
	$limit = 5;
/*	for($x=0;$x<$limit;$x++) {
		$title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
		$link = $feed[$x]['link'];
		$description = $feed[$x]['desc'];
		$date = date('l F d, Y', strtotime($feed[$x]['date']));
		echo '<p><h2><a href="'.$link.'" title="'.$title.'">'.$title.'</a></h2><br />';
		echo '<small><em>Posted on '.$date.'</em></small></p>';
		echo '<p>'.$description.'</p>';
	}*/
  $posts =[];
  	for($x=0;$x<$limit;$x++) {
      $posts[$x]=[];
  		$posts[$x]['title'] = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
  		$posts[$x]['slug'] = $feed[$x]['link'];
      $posts[$x]['post'] = $feed[$x]['desc'];
      $posts[$x]['img'] = '';
      $posts[$x]['id'] = '';
  		$posts[$x]['date'] = date('l F d, Y', strtotime($feed[$x]['date']));
  	}
  view::set('posts',$posts);
  view::render('blog-list.php');
}

    function tagAction()
    {
          view::set('posts',blog::postByTag(['posts'=>12,'tag'=>router::get('tag',1)]));
          view::render('blog-tag.php');
    }

    function cAction()
    {
          //view::set('posts',blog::postByCategory(['posts'=>12,'cat'=>router::get('cat',1)]));
          //view::render('blog-tag.php');
    }

    static function postByTag ($args = []) {
        global $db;
        $ppp = isset($args['posts'])?$args['posts']:8;
        $tag = isset($args['tag'])?$args['tag']:'';
        $start_from = (self::$page-1)*$ppp;
        $where = '';

        $ql="SELECT id,title,SUBSTRING(post,1,300) as post,slug,updated,
            (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img
            FROM post WHERE publish=1
            AND id IN(SELECT post_id from postmeta where vartype='tag' and value='$tag')
            ORDER BY id DESC LIMIT $start_from,$ppp";
        $res = $db->query($ql);


        if ($res) while ($r = mysqli_fetch_assoc($res)) {
            yield $r;
        }
    }


    static function searchposts ($s) {
        global $db;

        $res = $db->query("SELECT id,title,slug,SUBSTRING(post,1,300) as post,
            (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img
            FROM post WHERE match(title,post) AGAINST('$s' IN NATURAL LANGUAGE MODE) ORDER BY id DESC");
        if ($res) while ($r = mysqli_fetch_array($res)) {
            yield $r;
        }
    }


    function postShow($id=null)
    {
        global $db;

        $res = $db->query("SELECT id,title,post,updated,user_id FROM post WHERE publish=1 AND (id=? OR slug=?);",[$id,$id]);
        f ($res && $r = mysqli_fetch_array($res)) {
            $id = $r['id'];
            $user_id = $r['user_id'];

            view::set('title',$r['title']);
            view::set('text',$r['post']);
            view::set('updated',$r['updated']);

            view::set('og_url',gila::config('base').$r['id']);

            $res = $db->query("SELECT `value` as img FROM post,postmeta WHERE vartype='thumbnail' AND post_id=$id;");
            if ($res) {
                $r = mysqli_fetch_array($res);
                view::set('img',$r['img']);
                view::set('og_image',$r['img']);
            } else view::set('img',$r['img']);

            $res = $db->query("SELECT username FROM user WHERE id='$user_id';");
            if ($res) {
                $r = mysqli_fetch_array($res);
                view::set('author',$r['username']);
            } else view::set('author','unknown');

            view::render('single-post.php');
        }
        else {
            $res = $db->query("SELECT id,title,page,updated FROM page WHERE publish=1 AND (id=? OR slug=?);",[$id,$id]);
            f ($res && $r = mysqli_fetch_array($res)) {
                view::set('title',$r['title']);
                view::set('text',$r['page']);
                view::render('page.php');
            }else view::render('404.phtml');
        }
    }

    function searchAction()
    {
        global $db;
        $ppp = 8;
        if ($s=router::get('search',1)) {
		      view::set('posts',blog::searchposts($s));
		      view::render('blog-search.php');
            return;
        }
        view::set('page',blog::$page);
        view::set('posts',blog::post(['posts'=>12]));
        view::render('frontpage.php');
    }

    static function ppp () {
        return 8;
    }

    static function post () {
        global $db;
        $ppp = isset($args['posts'])?$args['posts']:8;
        $start_from = (self::$page-1)*$ppp;
        $where = '';

        $res = $db->query("SELECT id,title,slug,SUBSTRING(post,1,300) as post,
            (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img
            FROM post WHERE publish=1 ORDER BY id DESC LIMIT $start_from,$ppp");

        if ($res) while ($r = mysqli_fetch_array($res)) {
            yield $r;
        }
    }
    static function latestposts ($n=12) {
        global $db;

        $res = $db->query("SELECT id,title,slug,SUBSTRING(post,1,300) as post,updated,
            (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img
            FROM post ORDER BY id DESC LIMIT 0,$n");

        if ($res) while ($r = mysqli_fetch_object($res)) {
            yield $r;
        }
    }
    static function posts ($args = []) {
        global $db;
        $ppp = isset($args['posts'])?$args['posts']:8;
        $start_from = (self::$page-1)*$ppp;
        $where = '';

        $res = $db->query("SELECT id,title,slug,SUBSTRING(post,1,300) as post,
            (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img
            FROM post WHERE publish=1 ORDER BY id DESC LIMIT $start_from,$ppp");

        if ($res) while ($r = mysqli_fetch_object($res)) {
            yield $r;
        }
    }

    static function totalposts ($args = []) {
        global $db;
        $res = $db->query("SELECT * FROM post WHERE publish=1;");

        if ($r = mysqli_fetch_array($res)) {
            return $r[0];
        } else return 0;
    }

    static function get_url($id,$slug=NULL)
    {
        return gila::make_url('blog','',['p'=>$id,'slug'=>$slug]);
    }
}
