<?php

use core\models\post as post;
use core\models\page as page;
use core\models\user as user;

/**
* The blog controller, get calls for display of posts
*/
class Blog extends controller
{
    public static $page; /** The page number */
    public static $totalPosts;
    public static $totalPages;
    public static $ppp; /** Posts per page */

    function __construct ()
    {
        self::$page = @$_GET['page']?:1;
        self::$ppp = 12;
        self::$totalPosts = null;
    }

    /**
    * The default action.
    * First checks if there is parameter for a post and calls postShow()
    * Then check if there is search parameter and renders blog-search.php
    * If none, will render homepage.php or frontpage.php
    * @see postShow()
    */
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

        if($_GET['url']!='' || view::getViewFile('homepage.php')==false) {
            if ($r = page::getByIdSlug('')) {
                view::set('title',$r['title']);
                view::set('text',$r['page']);
                view::render('page.php');
                return;
            }
            view::set('page',blog::$page);
            view::set('posts',post::getPosts(['posts'=>self::$ppp,'page'=>self::$page]));
            view::render('frontpage.php');
        }
        else view::render('homepage.php');
    }

    /**
    * Displays new posts in xml feed
    */
    function feedAction()
    {
        $title = gila::config('title');
        $link = gila::config('base');
        $description = gila::config('slogan');
        $items = self::latestposts(20);
        include 'src/core/views/blog-feed.php';
    }

    /**
    * Displays posts with a specific tag
    */
    function tagAction()
    {
        $tag = router::get('tag',1);
        view::set('tag',$tag);
        view::set('page',self::$page);
        view::set('posts',post::getPosts(['posts'=>self::$ppp,'tag'=>$tag,'page'=>self::$page]));
        view::render('blog-tag.php');
    }

    /**
    * Display a list with all post tags
    */
    function tagsAction()
    {
          view::set('tags',post::getMeta('tag'));
          view::render('blog-tags.php');
    }

    /**
    * Display posts by a category
    */
    function categoryAction()
    {
        global $db;
        $category = router::get('category',1);
        $res = $db->get("SELECT title from postcategory WHERE id=?",$category);
        self::$totalPosts = post::total(['category'=>$category]);
        view::set('category',$res[0][0]);
        view::set('page',self::$page);
        view::set('posts',post::getPosts(['posts'=>self::$ppp,'category'=>$category,'page'=>self::$page]));
        view::render('blog-category.php');
    }

    /**
    * Display posts by author
    */
    function authorAction()
    {
        global $db;
        $user_id = router::get('author',1);
        $res = $db->get("SELECT username,id from user WHERE id=? OR username=?",[$user_id,$user_id]);
        if($res) {
            view::set('author',$res[0][0]);
            view::set('posts',post::getPosts(['posts'=>self::$ppp,'user_id'=>$res[0][1]]));
        } else {
            view::set('author',__('unknown'));
            view::set('posts',[]);
        }
        view::render('blog-author.php');
    }


    /**
    * Display a post
    */
    function postShow($id=null)
    {
        global $db;

        if ($r = post::getByIdSlug($id)) {
            $id = $r['id'];
            $user_id = $r['user_id'];
            view::set('author_id',$user_id);

            view::set('title',$r['title']);
            view::set('slug',$r['slug']);
            view::set('text',$r['post']);
            view::set('id',$r['id']);
            view::set('updated',$r['updated']);

            view::meta('twitter:card','summary');
            //if() view::meta('twitter:site','@site');

            view::meta('og:title',$r['title']);
            view::meta('og:type','website');
            view::meta('og:url',self::get_url($r['id'],$r['slug']));
            view::meta('og:description',$r['description']);

            gila::canonical('blog/'.$r['id'].'/'.$r['slug'].'/');

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
            } else view::set('author',__('unknown'));

            view::render('single-post.php');
        }
        else {
            if ($r = page::getByIdSlug($id)) {
                view::set('title',$r['title']);
                view::set('text',$r['page']);
                view::render('page.php');
            } else view::render('404.php');
        }
    }

    /**
    * Display posts by a search query
    */
    function searchAction()
    {
        if ($s=router::get('search',1)) {
		    view::set('posts',post::search($s));
		    view::render('blog-search.php');
            return;
        }
        view::set('page',self::$page);
        view::set('posts',self::post(['posts'=>(self::$ppp)]));
        view::render('frontpage.php');
    }

    static function post ($args = []) {
        $args['page'] = self::$page;
        return post::getPosts($args);
    }

    static function latestposts ($n = 10) {
        return post::getLatest($n);
    }

    static function posts ($args = []) {
        $args['page'] = self::$page;
        return post::getPosts($args);
    }

    static function totalposts ($args = []) {
        if(self::$totalPosts == null) return post::total($args);
        return self::$totalPosts;
    }

    static function totalpages ($args = []) {
        $totalPosts = self::totalposts($args);
        self::$totalPages = floor(($totalPosts+self::$ppp)/self::$ppp);
        return self::$totalPages;
    }

    static function get_url($id,$slug=NULL)
    {
        if($slug==NULL) return gila::make_url('blog','',['p'=>$id]);
        return gila::make_url('blog','',['p'=>$id,'slug'=>$slug]);
    }

    static function thumb_sm($img,$id)
    {
        $target = 'post_sm/'.str_replace(["://",":\\\\","\\","/",":"], "_", $img);
        return view::thumb_sm($img, $target);
    }

}
