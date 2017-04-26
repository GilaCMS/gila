<!-- Page Header -->
<!-- Set your background image for this header on the line below. -->

<header class="featured-posts"  style="margin-top:50px;">
<?php
foreach (blog::latestposts(4) as $p) {
    echo "<div class=\"img\" style=\"background-image: url('{$p->img}')\">";
    echo "<a href=\"{$p->id}\">";
    echo "<div class=\"featured-title\">{$p->title}</div>";
    echo "</a></div>";
}
?>
</header>
</div>

<!-- Latest posts grid -->
<div class="post-previews">
        <div class="col-md-10 col-md-offset-1">
            <?php foreach (blog::posts() as $p) { ?>
            <div class="post-preview col-md-3">
                <div><a href="<?=$p->id?>">
                    <div class="img" style="background-image: url('<?=$p->img?>')"></div>
                    <div class="post-title"><?=$p->title?></div>
                </a></div>
            </div>

            <?php } ?>
            <!-- Pager -->
            <ul class="pager">
                <li class="next">
                    <a href="#">Older Posts &rarr;</a>
                </li>
            </ul>
        </div>
</div>
