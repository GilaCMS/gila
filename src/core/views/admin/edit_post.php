<?php
use core\models\post as post;
global $db;

$p_list = ['title', 'post', 'publish' ];
if ($_SERVER['REQUEST_METHOD'] === 'POST') if (router::post('submit-btn')=='submited'){
    $title = strip_tags($_POST['p_title']);
    $description = strip_tags($_POST['p_description']);
    $text = $_POST['p_post'];
    $img = strip_tags(trim($_POST['p_img']));
    $tags = strip_tags($_POST['p_tags']);
    $categories = isset($_POST['p_categories'])?$_POST['p_categories']:[];
    $publish = isset($_POST['p_publish'])?1:0;
    $slug = strip_tags($_POST['p_slug']);
    if($slug == '') {
        $slugify = new Cocur\Slugify\Slugify();
        $slug = $slugify->slugify($title);
    }

    $args = [$title,$description,$text,$publish,$slug];
    if ($id == "new") {
        array_push($args,session::key('user_id'));
        $db->query("INSERT INTO post(title,description,post,publish,slug,user_id) VALUES(?,?,?,?,?,?)",$args);
        $id = $db->insert_id;
    }
    else {
        $db->query("UPDATE post SET title=?,description=?,post=?,publish=?,slug=? WHERE id=$id",$args);
    }

    $db->query("DELETE FROM postmeta WHERE post_id=? AND vartype IN('category','tag','thumbnail')",$id);
    if($img!='') $db->query("INSERT INTO postmeta(post_id,vartype,value) VALUES(?,'thumbnail',?);",[$id,$img]);
    if($categories) foreach($categories as $c)
        $db->query("INSERT INTO postmeta(post_id,vartype,value) VALUES(?,'category',?);",[$id,$c]);
    if($tags) foreach(explode(',',$tags) as $t) {
        $t = trim($t);
        if($t!='') $db->query("INSERT INTO postmeta(post_id,vartype,value) VALUES(?,'tag',?);",[$id,$t]);
    }

    echo "<div class='alert success'>Changes have been saved successfully!</div>";
}

if ($id == 'new') {
    $p = (object)['tags'=>'','img'=>'','slug'=>'','title'=>'','description'=>'','post'=>'','publish'=>1,'id'=>(isset($_POST['p_id'])?$_POST['p_id']:0)];
}
else {
    if (! $p = post::getById($id)) die("The post '$id' could not be found in db!");
    $p = (object)$p;

    $ql="SELECT id,title FROM postcategory";
    $categories = $db->get($ql);
    $ql="SELECT value FROM postmeta WHERE vartype='category' AND post_id=$id";
    $selectedcategories = $db->getList($ql);
}

view::script('src/core/assets/admin/media.js');
?>

<div class="row ">
<form id="postForm" method="post" class="g-form" action="admin/posts/<?=($p->id?:'new')?>">
    <input value="<?=($p->id?:'new')?>" name="p_id" type="hidden"/>

    <div class="row ">
        <label class="gm-2">Title</label>
        <input class="gm-10" value="<?=$p->title?>" name="p_title"/>
    </div>

    <div class="row">
        <label class="gm-2">Slug</label>
        <input class="gm-10" value="<?=$p->slug?>" name="p_slug" placeholder="Generate from title" />
    </div>

    <div class="row">
        <label class="gm-2">Description</label>
        <input class="gm-10" value="<?=$p->description?>" name="p_description" maxlength="200" placeholder="A small description of 200 characters" />
    </div>

    <div class="row ">
        <label class="gm-2">Thumbnail</label>
        <div class="gm-10 g-group">
          <span class="btn g-group-item" style="width:28px" onclick="open_gallery()"><i class="fa fa-image"></i></span>
          <span class="g-group-item"><input class="fullwidth" value="<?=$p->img?>" name="p_img"/><span>
        </div>
    </div>

    <div class="row ">
        <label class="gm-2">Categories</label>
        <select class="select2" multiple class="gm-10 gm-10" style="width:83.333%" name="p_categories[]">
            <?php foreach ($categories as $key => $value) {
                echo '<option value="'.$value[0].'"'.(in_array($value[0],$selectedcategories)?' selected':'').'>'.$value[1].'</option>';
            } ?>
        </select>
    </div>
    <div class="row ">
        <label class="gm-2">Tags</label>
        <input class="gm-10" placeholder="values seperated by comma" name="p_tags" value="<?=$p->tags?>"/>
    </div>

    <div class="row ">
        <label class="gm-2" for="p_publish">Public</label>
        <label class="g-switch"  for="p_publish">
            <input type="checkbox" id="p_publish" name="p_publish" <?=($p->publish==1?' checked':'')?>>
            <div class="g-slider"></div>
        </label>
        <!--label class="col-md-3">Public<input type="checkbox" name="p_publish" value="1"></label-->
    </div>

    <input type="hidden" name="submit-btn" id="submit-btn">
    <?php $textarea='p_post'; include __DIR__.'/edit_mce_editor.php'; ?>

    <br>
    <div class="btn-group ">
        <button onclick="g.el('submit-btn').value='submited'; submit()" class="btn btn-primary primary" >Submit</button>
    </div>

</form>
</div>
