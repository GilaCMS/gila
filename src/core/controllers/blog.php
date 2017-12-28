<?php

use core\models\post as post;
use core\models\user as user;

class blog  //extends controller
{
    public static $page;
    public static $totalPosts;
    public static $totalPages;
    public static $ppp;

    function __construct ()
    {
        self::$page = (router::get('page',1))?:1;
        self::$ppp = 12;
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
        if ($s=router::get('search')) {
            view::set('posts',post::search($s));
            view::render('blog-search.php');
            return;
        }

        if($_GET['url']!='' || view::findPath('landpage.php')==false) {
            view::set('page',blog::$page);
            view::set('posts',post::getPosts(['posts'=>self::$ppp]));
            view::render('frontpage.php');
        }
        else view::render('landpage.php');
    }

    function feedAction()
    {
        $title=gila::config('title');
        $link=gila::config('base');
        $description=gila::config('slogan');
        $items=blog::latestposts();
        include 'src/core/views/rss.php';
    }

    function tagAction()
    {
        $tag = router::get('tag',1);
        view::set('tag',$tag);
        view::set('posts',post::getPosts(['posts'=>self::$ppp,'tag'=>$tag]));
        view::render('blog-tag.php');
    }

    function tagsAction()
    {
          view::set('tags',post::getMeta('tag'));
          view::render('blog-tags.php');
    }

    function categoryAction()
    {
        global $db;
        $category = router::get('category',1);
        $res = $db->get("SELECT title from postcategory WHERE id=?",$category);
        view::set('category',$res[0][0]);
        view::set('posts',post::getPosts(['posts'=>self::$ppp,'category'=>$category]));
        view::render('blog-category.php');
    }

    function authorAction()
    {
        global $db;
        $user_id = router::get('author',1);
        $res = $db->get("SELECT username from user WHERE id=?",$user_id);
        view::set('author',$res[0][0]);
        view::set('posts',post::getPosts(['posts'=>self::$ppp,'user_id'=>$user_id]));
        view::render('blog-author.php');
    }


    function postShow($id=null)
    {
        global $db;

        $res = $db->query("SELECT id,title,post,updated,user_id,slug FROM post WHERE publish=1 AND (id=? OR slug=?);",[$id,$id]);
        if ($res && $r = mysqli_fetch_array($res)) {
            $id = $r['id'];
            $user_id = $r['user_id'];

            view::set('title',$r['title']);
            view::set('text',$r['post']);
            view::set('id',$r['id']);
            view::set('updated',$r['updated']);

            view::meta('twitter:card','summary');
            //if() view::meta('twitter:site','@site');

            view::meta('og:title',$r['title']);
            view::meta('og:type','website');
            view::meta('og:url',self::get_url($r['id'],$r['slug']));

            $res = $db->query("SELECT `value` as img FROM post,postmeta WHERE vartype='thumbnail' AND post_id=$id;");
            if ($res) {
                $r = mysqli_fetch_array($res);
                view::set('img',$r['img']);
                view::meta('og:image',$r['img']);
            } else view::set('img','');

            $res = $db->query("SELECT username FROM user WHERE id='$user_id';");
            if ($res) {
                $r = mysqli_fetch_array($res);
                view::set('author',$r['username']);
                view::meta('author',$r['username']);
                if($creator = user::meta($user_id,'twitter_account'))
                    view::meta('twitter:creator','@'.$creator);
            } else view::set('author','unknown');

            // view::meta('description',$r['username']);

            view::render('single-post.php');
        }
        else {
            $res = $db->query("SELECT id,title,page,updated FROM page WHERE publish=1 AND (id=? OR slug=?);",[$id,$id]);
            if ($res && $r = mysqli_fetch_array($res)) {
                view::set('title',$r['title']);
                view::set('text',$r['page']);
                view::render('page.php');
            } else view::render('404.phtml');
        }
    }

    function searchAction()
    {
        global $db;
        $ppp = 8;
        if ($s=router::get('search',1)) {
		      view::set('posts',post::search($s));
		      view::render('blog-search.php');
            return;
        }
        view::set('page',blog::$page);
        view::set('posts',blog::post(['posts'=>(self::$ppp)]));
        view::render('frontpage.php');
    }

    static function post ($args = []) {
        return post::getPosts($args);
    }

    static function latestposts ($n) {
        return post::getLatest($n);
    }

    static function posts ($args = []) {
        return post::getPosts($args);
    }

    static function totalposts ($args = []) {
        return post::total($args);
    }

    static function totalpages ($args = []) {
        self::$totalPosts = blog::totalposts($args);
        self::$totalPages = floor((self::$totalPosts+self::$ppp)/self::$ppp);
        return self::$totalPages;
    }

    static function get_url($id,$slug=NULL)
    {
        if($slug==NULL) return gila::make_url('blog','',['p'=>$id]);
        return gila::make_url('blog','',['p'=>$id,'slug'=>$slug]);
    }

}
