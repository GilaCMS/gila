<?php



class blog extends controller
{
    public static $page;
    public static $totalPosts;

    function __construct ()
    {
        self::$page = (router::get('page',1))?:1;
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
    function tagAction()
    {
          view::set('posts_by_tag',blog::postByTag(['posts'=>12,'tag'=>router::get('tag',1)]));
          view::render('blog-tag.php');
    }
    static function postByTag ($args = []) {
        global $db;
        $ppp = isset($args['posts'])?$args['posts']:8;
        $tag = isset($args['tag'])?$args['tag']:'';
        $start_from = (self::$page-1)*$ppp;
        $where = '';

        $ql="SELECT id,title,SUBSTRING(post,1,300) as post,
            (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img
            FROM post WHERE publish=1
            AND id IN(SELECT post_id from postmeta where vartype='tag' and value='$tag')
            ORDER BY id DESC LIMIT $start_from,$ppp";
        $res = $db->query($ql);
        echo $ql;

        if ($res) while ($r = mysqli_fetch_assoc($res)) {
            yield $r;
        }
    }


    function postShow($id=null)
    {
        global $db;

        $res = $db->query("SELECT id,title,post,updated,user_id FROM post WHERE post.id=? OR post.slug=?;",[$id,$id]);
        if ($res) if ($r = mysqli_fetch_array($res)) {
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

            $res = $db->query("SELECT name FROM user WHERE id='$user_id';");
            if ($res) {
                $r = mysqli_fetch_array($res);
                view::set('author',$r['name']);
            } else view::set('author','unknown');

            view::render('single-post.php');
        }
        else {
            $res = $db->query("SELECT id,title,page,updated FROM page WHERE page.id=? OR page.slug=?;",[$id,$id]);
            if ($res) if ($r = mysqli_fetch_array($res)) {
                view::set('title',$r['title']);
                view::set('text',$r['page']);
                view::render('page.php');
            }else view::render('404.phtml');
        }
    }

    function pageAction()
    {
        global $db;
        $ppp = 8;
        //$page=(router::get('page',1))?:1;
        view::set('page',blog::$page);
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

        $res = $db->query("SELECT id,title,SUBSTRING(post,1,300) as post,
            (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img
            FROM post WHERE publish=1 ORDER BY id DESC LIMIT $start_from,$ppp");

        if ($res) while ($r = mysqli_fetch_array($res)) {
            yield $r;
        }
    }
    static function latestposts ($n) {
        global $db;

        $res = $db->query("SELECT id,title,SUBSTRING(post,1,300) as post,
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

        $res = $db->query("SELECT id,title,SUBSTRING(post,1,300) as post,
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

}
