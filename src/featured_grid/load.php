<?php

if(gila::option('featured_grid.height')=='400px') {
    view::stylesheet('src/featured_grid/assets/style400.css');
} else view::stylesheet('src/featured_grid/assets/style.css');


event::listen('slide',function(){
    if(router::controller()=='blog'){
        echo '<div class="featured-posts row">';
        $params=['posts'=>4];
        if(gila::option('featured_grid.category')!='') $params['category']=gila::option('featured_grid.category');
        foreach (blog::posts($params) as $p) {
            $srcset = view::thumb_srcset($p['img'],[600,240]);
            echo "<div>";
            echo "<a href=\"".blog::get_url($p['id'],$p['slug'])."\">";
            echo "<div class=\"img\" style=\"background-image: url('{$srcset[0]}');";
            echo "background-image: -webkit-image-set(url({$srcset[0]}) 1x, url({$srcset[1]}) 2x);\"></div>";
            echo "<div class=\"featured-title\">{$p['title']}</div>";
            echo "</a></div>";
        }
        echo '</div>';
    }
});
