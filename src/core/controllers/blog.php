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
            $res = $db->query("SELECT * FROM post WHERE id=?",$id);
            while ($r = mysqli_fetch_array($res)) {
                view::set('title',$r['title']);
                view::set('text',$r['post']);
                view::render('single-post.php');
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

            /*$res = $db->query("SELECT * FROM post WHERE LIMIT $start_from,$ppp");
            while ($r = mysqli_fetch_array($res)) {
                view::set('title',$r['title']);
                view::set('text',$r['post']);
                view::render('../themes/yellow-blog/single-post.php');
            }*/
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
