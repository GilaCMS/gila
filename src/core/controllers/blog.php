<?php



class blog extends controller
{
    public static $page;

    function __construct ()
    {
        self::$page=(router::get('page',1))?:1;
    }

    function indexAction()
    {
        global $db;
        if ($id=router::get('post_id',1)) {
            $res = $db->query("SELECT id,title,post,updated,user_id FROM post WHERE post.id=? OR post.slug=?;",[$id,$id]);
            if ($res) if ($r = mysqli_fetch_array($res)) {
                $id = $r['id'];
                $user_id = $r['user_id'];

                view::set('title',$r['title']);
                view::set('text',$r['post']);
                view::set('updated',$r['updated']);

                $res = $db->query("SELECT `value` as img FROM post,postmeta WHERE vartype='thumbnail' AND post_id=$id;");
                if ($res) {
                    $r = mysqli_fetch_array($res);
                    view::set('img',$r['img']);
                } else view::set('img',$r['img']);

                $res = $db->query("SELECT name FROM user WHERE id='$user_id';");
                if ($res) {
                    $r = mysqli_fetch_array($res);
                    view::set('author',$r['name']);
                } else view::set('author','unknown');

                view::render('single-post.php');
            }
            else {
                view::render('404.phtml');
            }
            return;
        }
        $this->pageAction();
        //include __DIR__.'/../../../themes/'.$GLOBALS['config']['theme']."/frontpage.php";
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
        $ppp = 8;
        $start_from = (self::$page-1)*$ppp+3;

        $res = $db->query("SELECT id,title,SUBSTRING(post,1,300) as post,
            (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img
            FROM post ORDER BY id DESC LIMIT $start_from,$ppp");

        if ($res) while ($r = mysqli_fetch_array($res)) {
            yield $r;
        }
    }
}
