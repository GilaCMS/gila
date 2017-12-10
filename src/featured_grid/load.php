<?php

if(gila::option('featured_grid.category')!='400px') {
    view::stylesheet('src/featured_grid/assets/style400.css');
} else view::stylesheet('src/featured_grid/assets/style.css');


event::listen('slide',function(){
    if(router::controller()=='blog'){
        echo '<div class="featured-posts row">';
        $params=['posts'=>4];
        if(gila::option('featured_grid.category')!='') $params['category']=gila::option('featured_grid.category');
        foreach (blog::posts($params) as $p) {
            echo "<div class=\"img\" style=\"background-image: url('".view::thumb_lg($p['img'],"featured_img_{$p['id']}.jpg")."')\">";
            echo "<a href=\"".blog::get_url($p['id'],$p['slug'])."\">";
            echo "<div class=\"featured-title\">{$p['title']}</div>";
            echo "</a></div>";
        }
        echo '</div>';
    }
});
