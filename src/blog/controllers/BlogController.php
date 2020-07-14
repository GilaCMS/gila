<?php

use Gila\Post;
use Gila\Page;
use Gila\User;
use Gila\Config;
use Gila\View;
use Gila\Session;
use Gila\Router;

/**
* The blog controller, get calls for display of posts
*/
class BlogController extends \Gila\Controller
{
  public static $page; /** The page number */
  public static $totalPosts;
  public static $totalPages;
  public static $ppp; /** Posts per page */

  public function __construct()
  {
    self::$page = intval(@$_GET['page'])?:1;
    self::$ppp = 12;
    self::$totalPosts = null;
    View::set('page_title', Config::config('title'));
  }

  /**
  * The default action.
  * First checks if there is parameter for a post and calls postShow()
  * Then check if there is search parameter and renders blog-search.php
  * If none, will render homepage.php or frontpage.php
  * @see postShow()
  */
  public function indexAction()
  {
    if ($id = Router::path()) {
      if ($id !== "blog" && $id !== "blog/") {
        $this->postShow($id);
        return;
      }
    }
    if ($id=Router::param('p')) {
      $this->postShow($id);
      return;
    }
    if ($s=Router::param('search')) {
      $s = strip_tags($s);
      View::set('search', $s);
      View::set('posts', Post::search($s));
      View::render('blog-search.php');
      return;
    }

    if ($_GET['url']!='' || View::getViewFile('homepage.php')==false) {
      if ($r = Page::getByIdSlug('')) {
        View::set('title', $r['title']);
        View::set('text', $r['page']);
        View::render('blog-homepage.php', 'blog');
        return;
      }
      View::set('page', self::$page);
      View::set('posts', Post::getPosts(['posts'=>self::$ppp,'page'=>self::$page]));
      View::render('frontpage.php');
    } else {
      View::render('homepage.php');
    }
  }

  /**
  * Displays new posts in xml feed
  */
  public function feedAction()
  {
    $title = Config::config('title');
    $link = Config::config('base');
    $description = Config::config('slogan');
    $items = self::latestposts(20);
    include 'src/core/views/blog-feed.php';
  }

  /**
  * Displays posts with a specific tag
  */
  public function tagAction($tag)
  {
    $tag = htmlentities($tag);
    Config::canonical('tag/'.$tag);
    View::set('page_title', '#'.$tag.' | '.Config::config('title'));
    View::set('tag', $tag);
    View::set('page', self::$page);
    View::set('posts', Post::getPosts(['posts'=>self::$ppp,'tag'=>$tag,'page'=>self::$page]));
    View::render('blog-tag.php');
  }

  /**
  * Display a list with all post tags
  */
  public function tagsAction()
  {
    Config::canonical('tags');
    View::set('page_title', __('Tags').' | '.Config::config('title'));
    View::set('tags', Post::getMeta('tag'));
    View::render('blog-tags.php');
  }

  /**
  * Display posts by a category
  */
  public function categoryAction($category)
  {
    global $db;
    if (!is_numeric($category)) {
      $category = $db->value('SELECT id FROM postcategory WHERE slug=?', $category);
    }
    $name = $db->value("SELECT title from postcategory WHERE id=?", $category);
    Config::canonical('blog/category/'.$category.'/'.$name.'/');
    self::$totalPosts = Post::total(['category'=>$category]);
    View::set('categoryName', $name);
    View::set('page_title', $name);
    View::set('page', self::$page);
    View::set('posts', Post::getPosts(['posts'=>self::$ppp,'category'=>$category,'page'=>self::$page]));
    View::render('blog-category.php');
  }

  /**
  * Display posts by author
  */
  public function authorAction()
  {
    global $db;
    $user_id = Router::param('author', 1);
    Config::canonical('author/'.$user_id);
    $res = $db->get("SELECT username,id from user WHERE id=? OR username=?", [$user_id,$user_id]);
    if ($res) {
      View::set('author', $res[0][0]);
      View::set('page_title', $res[0][0].' | '.Config::config('title'));
      View::set('posts', Post::getPosts(['posts'=>self::$ppp,'user_id'=>$res[0][1]]));
    } else {
      View::set('author', __('unknown'));
      View::set('posts', []);
    }
    View::render('blog-author.php');
  }


  /**
  * Display a post
  */
  public function postShow($id=null)
  {
    global $db;
    $cacheTime = Config::option('blog.cache');
    Config::canonical('blog/'.$id);
    if (Session::userId()==0 && $cacheTime > 0) {
      Cache::page('blog/post'.$id, $cacheTime, [Config::mt('post')]);
    }
    Router::$action = "post";

    $args = explode('/', $id);
    $postId = $args[0]==='blog'? $args[1]: $args[0];

    if (($r = Post::getByIdSlug($postId)) && ($r['publish']==1)) {
      $id = $r['id'];
      if (!$r['user_id']) {
        $r['user_id'] = $db->value("SELECT user_id FROM post WHERE id=? OR slug=?", [$id,$id]);
      }
      $user_id = $r['user_id'];
      View::set('author_id', $user_id);
      View::set('title', $r['title']);
      View::set('page_title', $r['title']);
      View::set('slug', $r['slug']);
      View::set('text', $r['post']);
      View::set('id', $r['id']);
      View::set('updated', $r['updated']);

      Config::canonical('blog/'.$r['id'].'/'.$r['slug'].'/');
      View::meta('og:title', $r['title']);
      View::meta('og:type', 'website');
      View::meta('og:url', View::$canonical);
      View::meta('og:description', $r['description']);

      if ($r['img']) {
        View::set('img', $r['img']);
        View::meta('og:image', $r['img']);
        View::meta('twitter:image:src', Config::base_url($r['img']));
      } elseif (Config::config('og-image')) {
        View::meta('og:image', Config::config('og-image'));
        View::meta('twitter:image:src', Config::base_url(Config::config('og-image')));
      } else {
        View::set('img', '');
      }

      if ($r['tags']) {
        View::meta('keywords', $r['tags']);
      }

      if ($value = Config::option('blog.twitter-card')) {
        View::meta('twitter:card', $value);
      }
      if ($value = Config::option('blog.twitter-site')) {
        View::meta('twitter:site', '@'.$value);
      }

      if ($r = User::getById($user_id)) {
        View::set('author', $r['username']);
        View::meta('author', $r['username']);
        if ($creator = User::meta($user_id, 'twitter_account')) {
          View::meta('twitter:creator', '@'.$creator);
        }
      } else {
        View::set('author', __('unknown'));
      }

      View::render('single-post.php');
    } else {
      if ($category = $db->value('SELECT id FROM postcategory WHERE slug=?', $id)) {
        $this->categoryAction($category);
        return;
      }
  
      if (($r = Page::getByIdSlug($id)) && ($r['publish']==1)) {
        View::set('title', $r['title']);
        View::set('text', $r['page']);
        if ($r['template']==''||$r['template']===null) {
          View::render('page.php');
        } else {
          View::renderFile('page--'.$r['template'].'.php');
        }
      } else {
        http_response_code(404);
        View::render('404.php');
      }
    }
  }

  /**
  * Display posts by a search query
  */
  public function searchAction()
  {
    if ($s=Router::param('search', 1)) {
      $s = strip_tags($s);
      View::set('search', $s);
      View::set('page_title', $s.' | '.Config::config('title'));
      View::set('posts', Post::search($s));
      View::render('blog-search.php');
      return;
    }
    View::set('page', self::$page);
    View::set('posts', self::post(['posts'=>(self::$ppp)]));
    View::render('frontpage.php');
  }

  public static function post($args = [])
  {
    $args['page'] = self::$page;
    return Post::getPosts($args);
  }

  public static function latestposts($n = 10)
  {
    return Post::getLatest($n);
  }

  public static function posts($args = [])
  {
    $args['page'] = self::$page;
    return Post::getPosts($args);
  }

  public static function totalposts($args = [])
  {
    if (self::$totalPosts == null) {
      return Post::total($args);
    }
    return self::$totalPosts;
  }

  public static function totalpages($args = [])
  {
    $totalPosts = self::totalposts($args);
    self::$totalPages = floor(($totalPosts+self::$ppp)/self::$ppp);
    return self::$totalPages;
  }

  public static function get_url($id, $slug=null) // DEPRECATED
  {
    if ($slug==null) {
      return Config::make_url('blog', '', ['p'=>$id]);
    }
    return Config::make_url('blog', '', ['p'=>$id,'slug'=>$slug]);
  }

  public static function thumb_sm($img, $id) // DEPRECATED
  {
    $target = 'post_sm/'.str_replace(["://",":\\\\","\\","/",":"], "_", $img);
    return View::thumb_sm($img, $target);
  }
}

class_alias('BlogController', 'blog'); // DEPRECATED
