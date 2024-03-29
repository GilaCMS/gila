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
class BlogController extends Gila\Controller
{
  public static $page; /** The page number */
  public static $totalPosts;
  public static $totalPages;
  public static $ppp; /** Posts per page */

  public function __construct()
  {
    self::$page = (int)($_GET['page']??1);
    self::$ppp = 12;
    self::$totalPosts = null;
    $page_title = __('Blog');
    if (Config::lang()!==Config::get('language')) {
      $page_title .= ' ('.strtoupper(Config::lang()).')';
    }
    if (isset($_GET['page'])) {
      Config::addLang('core/lang/');
      $page_title .= ' - '.__('Page').' '.(int)$_GET['page'];
    }
    $page_title .= ' | '.Config::get('title');
    View::set('page_title', $page_title);
    View::set('page', self::$page);
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
    if ($id = Router::path() ?? Router::param('p')) {
      $id = explode('?', $id)[0];
      if ($id !== "blog" && $id !== "blog/") {
        $this->postShow($id);
        return;
      }
    }

    if ($s=Router::param('search')) {
      $s = strip_tags($s);
      View::set('search', $s);
      View::set('posts', Post::search($s));
      View::render('blog-search.php');
      if (http_response_code()==200) {
        Logger::stat();
      }
      return;
    }

    $path = Router::getPath();
    Config::canonical('blog');
    if (self::$page>1) {
      Config::canonical('blog?page='.self::$page);
    }
    if ($path=='' && Page::inCachedList('')) {
      $this->postShow('');
      return;
    }
    if ($path!='' || View::getViewFile('homepage.php')==false) {
      View::set('page', self::$page);
      $args = ['posts'=>self::$ppp, 'page'=>self::$page, 'language'=>Config::lang()];
      View::set('posts', Post::getPosts($args));
      $totalpages = ceil((Post::total($args))/12);
      View::set('totalpages', $totalpages);
      if ($totalpages<self::$page && self::$page>1) {
        http_response_code(404);
      }
      View::render('frontpage.php');
    } else {
      View::render('homepage.php');
    }
    if (http_response_code()==200) {
      Logger::stat();
    }
  }

  /**
  * Displays new posts in xml feed
  */
  public function feedAction()
  {
    View::set('title', Config::get('title'));
    View::set('link', Config::get('base'));
    View::set('description', Config::get('slogan'));
    View::set('items', Post::getLatest(1000));
    View::renderFile('blog-feed.php');
  }

  /**
  * Displays posts with a specific tag
  */
  public function tagAction($tag='')
  {
    $tag = htmlentities($tag);
    Config::canonical('tag/'.$tag);
    $args = [
      'posts'=>self::$ppp, 'tag'=>$tag, 'language'=>Config::lang(), 'page'=>self::$page
    ];
    $totalpages = ceil((Post::total($args))/12);
    $posts = Post::getPosts($args);
    if (self::$page<1 || self::$page>self::totalPages()) {
      View::render('404.php');
      return;
    }
    View::set('page_title', '#'.$tag.' | '.Config::get('title'));
    View::set('tag', $tag);
    View::set('page', self::$page);
    View::set('posts', $posts);
    View::set('totalpages', $totalpages);
    if ($totalpages<self::$page && self::$page>1) {
      http_response_code(404);
    }
    View::render('blog-tag.php');
  }

  /**
  * Display a list with all post tags
  */
  public function tagsAction()
  {
    Config::canonical('tags');
    View::set('page_title', __('Tags').' | '.Config::get('title'));
    View::set('tags', Post::getMeta('tag'));
    View::render('blog-tags.php');
  }

  /**
  * Display posts by a category
  */
  public function categoryAction($category)
  {
    if (!is_numeric($category)) {
      $category = DB::value('SELECT id FROM postcategory WHERE slug=?', $category);
    }
    $name = DB::value("SELECT title from postcategory WHERE id=?", $category);
    Config::canonical('blog/category/'.$category.'/'.$name.'?page='.self::$page);
    $args = [
      'posts'=>self::$ppp, 'category'=>$category, 'publish'=>1,
      'language'=>Config::lang(), 'page'=>self::$page
    ];
    $totalpages = ceil((Post::total($args))/12);
    $posts = Post::getPosts($args);
    if (self::$page<1 || self::$page>self::totalPages()) {
      View::render('404.php');
      return;
    }
    View::set('categoryName', $name);
    View::set('page_title', $name.' | '.Config::get('title'));
    View::set('page', self::$page);
    View::set('posts', $posts);
    View::set('totalpages', $totalpages);
    if ($totalpages<self::$page) {
      http_response_code(404);
    }
    View::render('blog-category.php');
  }

  /**
  * Display posts by author
  */
  public function authorAction()
  {
    $user_id = Router::param('author', 1);
    Config::canonical('author/'.$user_id);
    $res = DB::get("SELECT username,id from user WHERE id=? OR username=?", [$user_id,$user_id]);
    if ($res) {
      View::set('author', $res[0][0]);
      View::set('page_title', $res[0][0].' | '.Config::get('title'));
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
    $cacheTime = Config::option('blog.cache');
    Config::canonical('blog/'.$id);
    if (Session::userId()==0 && $cacheTime > 0) {
      $uniques = [Config::mt('post'), Config::mt('postcategory'), Config::mt('page')];
      Cache::page('blog_'.Config::lang().$id, $cacheTime, $uniques);
    }
    Router::$action = "post";

    $args = explode('/', $id);
    $postId = $args[0]==='blog'? $args[1]: $args[0];

    if (Post::inCachedList($postId) && ($r = Post::getByIdSlug($postId)) && ($r['publish']==1)) {
      $id = $r['id'];
      if (!$r['user_id']) {
        $r['user_id'] = DB::value("SELECT user_id FROM post WHERE id=? OR slug=?", [$id,$id]);
      }
      $user_id = $r['user_id'];
      View::set('author_id', $user_id);
      View::set('title', $r['title']);
      View::set('page_title', $r['title']);
      View::set('slug', $r['slug']);
      View::set('text', $r['post']);
      View::set('id', $r['id']);
      View::set('updated', $r['updated']);
      View::set('created', $r['created']);
      View::set('description', $r['description']);
      View::set('post', $r);

      View::meta('og:title', $r['title']);
      View::meta('og:type', 'website');
      View::meta('og:url', View::$canonical);
      View::meta('og:description', $r['description']);
      View::meta('description', $r['description']);

      if (!empty($r['language'])) {
        Config::lang($r['language']);
      }
      Config::canonical('blog/'.$r['id'].'/'.$r['slug']);

      if ($r['img']) {
        View::set('img', $r['img']);
        View::meta('og:image', $r['img']);
        $twitterImgSrc = substr($r['img'], 0, 7)=='assets/'?Config::get('base').$r['img']:$r['img'];
        View::meta('twitter:image:src', $twitterImgSrc);
      } elseif (Config::get('og-image')) {
        View::meta('og:image', Config::get('og-image'));
        $twitterImgSrc = substr($r['img'], 0, 7)=='assets/'?Config::get('base').$r['og-img']:$r['og-img'];
        View::meta('twitter:image:src', $twitterImgSrc);
      } else {
        View::set('img', '');
      }

      if ($r['tags']) {
        View::meta('keywords', $r['tags']);
        View::set('keywords', $r['tags']);
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

      if (View::getViewFile('blog-post.php')) {
        View::renderFile('blog-post.php');
      } else {
        View::render('single-post.php');
      }
    } else {
      if (Page::inCachedList($id) && $r = Page::getByIdSlug($id)) {
        View::set('title', $r['title']);
        View::set('text', $r['page']);
        View::meta('description', $r['description']);
        if (!empty($r['language'])) {
          Config::lang($r['language']);
        }
        View::set('page_title', $r['title'].' | '.Config::get('title'));
        Config::canonical($r['slug']);
        if ($r['template']==''||$r['template']===null) {
          View::render('page.php');
        } else {
          View::renderFile('page--'.$r['template'].'.php');
        }
      } else {
        if ($to = Gila\Page::redirect($id)) {
          http_response_code(301);
          header('Location: '.Config::base($to));
          exit;
        }
        http_response_code(404);
        Cache::page('404_blog'.Config::lang(), max(86400, $cacheTime));
        View::render('404.php');
      }

      if ($category = DB::value('SELECT id FROM postcategory WHERE slug=?;', $postId)) {
        $this->categoryAction($category);
        return;
      }
    }
    if (http_response_code()==200) {
      Logger::stat();
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
      View::set('page_title', $s.' | '.Config::get('title'));
      View::set('posts', Post::search($s));
      View::render('blog-search.php');
      return;
    }
    View::set('page', self::$page);
    View::set('posts', self::post(['posts'=>(self::$ppp)]));
    if (View::getViewFile('blog.php')) {
      View::render('blog.php');
    } else {
      View::render('frontpage.php');
    }
  }

  public static function post($args = [])
  {
    $args['page'] = self::$page;
    return Post::getPosts($args);
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
    self::$totalPages = ceil(($totalPosts)/self::$ppp);
    return self::$totalPages;
  }
}
