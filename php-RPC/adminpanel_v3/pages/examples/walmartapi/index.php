<?php
$base_url = "http://localhost/walmartapi/";
?>
<!DOCTYPE html>
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">

    <title>Create RESTful API using Slim PHP Framework</title>
    <link href='css/style.css' rel='stylesheet' type='text/css'>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="js/ajaxGetPost.js"></script>
    <script>


    $(document).ready(function()
    {
    var base_url="<?php echo $base_url; ?>";
    var url,encodedata;
    $("#update").focus();

    /* Load Updates */
    /*
    url=base_url+'api/updates';
    encodedata='';
    ajax_data('GET',url, function(data) 
    {
    $.each(data.updates, function(i,data)
    {
    var html="<div class='stbody' id='stbody"+data.update_id+"'><div class='stimg'><img src='"+data.profile_pic+"' class='stprofileimg'/><\/div>"
         +"<div class='sttext'><strong>"+data.name+"<\/strong>"+data.user_update+"<span id='"+data.update_id+"' class='stdelete'>Delete<\/span>";
         +"<\/div><\/div>";
    $(html).appendTo("#mainContent");
    });

    }); */

    /* Insert Update */
    $('body').on("click",'.stpostbutton',function()
    {
    var update=$('#update').val();
    encode=JSON.stringify({
        "user_update": update,
        "user_id": $('#user_id').val()
        });
    url=base_url+'api/updates';
    if(update.length>0)
    {
    post_ajax_data(url,encode, function(data) 
    {
    $.each(data.updates, function(i,data)
    {
    var html="<div class='stbody' id='stbody"+data.update_id+"'><div class='stimg'><img src='"+data.profile_pic+"' class='stprofileimg'/><\/div>"
         +"<div class='sttext'><strong>"+data.name+"<\/strong>"+data.user_update+"<span id='"+data.update_id+"' class='stdelete'>Delete<\/span>";
         +"<\/div><\/div>";
    $("#mainContent").prepend(html);

    $('#update').val('').focus();

    });
    });
    }

    });

    /* Delete Updates */
    $('body').on("click",'.stdelete',function()
    {
    var ID=$(this).attr("id");
    url=base_url+'api/updates/delete/'+ID;
    ajax_data('DELETE',url, function(data) 
    {
    $("#stbody"+ID).fadeOut("slow");
    });
    });





    });
    </script>
</head>

<body>
    <div style="margin:0 auto;width:1000px;">
        <h1>Wallmart API</h1>
        <!--
        <h3>TEST API URLs</h3>
        Get Users <a href="<?php echo $base_url; ?>api/users"><?php echo $base_url; ?>api/users</a><br>
        <br>
        Get Updates <a href="<?php echo $base_url; ?>api/updates"><?php echo $base_url; ?>api/updates</a><br>
        <br>
        User Search <a href="<?php echo $base_url; ?>api/users/search/s"><?php echo $base_url; ?>api/users/search/s</a><br>
        <br>
        Delete Update <a href="<?php echo $base_url; ?>api/updates/delete/1"><?php echo $base_url; ?>api/updates/delete/1</a><br>
        <br>
        Post Update <a href="<?php echo $base_url; ?>api/updates"><?php echo $base_url; ?>api/updates</a>
        <br>
        <br> -->
        <h3>Category API URLs</h3>
        Get All Categories <a href="<?php echo $base_url; ?>api/category"><?php echo $base_url; ?>api/category</a><br>
        <br>
        Parent Categories <a href="<?php echo $base_url; ?>api/category/parent"><?php echo $base_url; ?>api/category/parent</a><br>
        <br>
        Child Categories <a href="<?php echo $base_url; ?>api/category/child/1"><?php echo $base_url; ?>api/category/child/1</a><br>
        <br>
        Parent Category Search <a href="<?php echo $base_url; ?>api/category/parent/search/car"><?php echo $base_url; ?>api/category/parent/search/car</a><br>
        <br>
        Child Category Search <a href="<?php echo $base_url; ?>api/category/child/search/car"><?php echo $base_url; ?>api/category/child/search/car</a><br>
        <br>
        <br>
        <h3>Main API URLs</h3>
        Category data <a href="<?php echo $base_url; ?>api/category/data"><?php echo $base_url; ?>api/category/data</a><br>
        <br>
        Product data <a href="<?php echo $base_url; ?>api/product/data"><?php echo $base_url; ?>api/product/data</a><br>
        <br>
        <!--
        <div>
            <textarea class="stupdatebox" id="update"></textarea><br>
            <input id="user_id" type="hidden" value="1"> <input class="stpostbutton" type="submit" value="POST">
        </div>
        -->

        <div id="mainContent"></div>

        <div style="width:380px;float:right"></div>
    </div>
</body>
</html>