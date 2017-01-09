<div class="clearfix wrapper main_content_area">

    <div class="clearfix main_content floatleft">

<?php
if($page==1) view::widget('slider');
?>

        <div class="clearfix content">
            <div class="content_title"><h2>Latest Blog Post</h2></div>

<?php foreach (blog::post() as $r) { ?>
        <div class="clearfix single_content">
            <div class="clearfix post_date floatleft">
                <div class="date">
                    <h3>27</h3>
                    <p>January</p>
                </div>
            </div>
            <div class="clearfix post_detail">
                <h2><a href="<?=$r['id']?>"><?=$r['title']?></a></h2>
                <div class="clearfix post-meta">
                    <p><span><i class="fa fa-user"></i> Admin</span> <span><i class="fa fa-clock-o"></i> 20 Jan 2014</span> <span><i class="fa fa-comment"></i> 4 comments</span> <span><i class="fa fa-folder"></i> Category</span></p>
                </div>
                <div class="clearfix post_excerpt">
                    <img src="<?=$r['img']?>" alt=""/>
                    <p><?=nl2br(strip_tags($r['post']))?> </p>
                </div>
                <a href="<?=$r['id']?>">Continue Reading</a>
            </div>
        </div>
<?php }?>


        </div>

        <div class="pagination">
            <nav>
                <ul>
                    <!--li><a href=""> << </a></li-->
<?php
global $db;
                    $total = $db->value("SELECT COUNT(*) FROM post")?:0;
                    for ($i=1; $i<= $total/blog::ppp(); $i++) {
                        echo "<li><a href='page/$i'>$i</a></li>";
                    }
?>
                    <!--li><a href=""> >> </a></li-->
                </ul>
            </nav>
        </div>
    </div>
    <?php include 'sidebar.php'; ?>
</div>
