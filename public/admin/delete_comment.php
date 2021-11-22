<?php
    require_once('../../include/initialize.php');
?>

<?php
    if(!$session->is_logged_in()){
        redirect_to("login.php");
    }
?>

<?php
//must have an id
if(empty($_GET['id'])){
    $session->message("No Comment ID Was Provided");
    redirect_to('index.php');
}

$comment = Comment::find_by_id($_GET['id']);
if($comment && $comment->delete()){
    $session->message("The Comment Was Deleted");
    redirect_to("comments.php?id={$comment->photograph_id}");
}else{
    $session->message("The Comment Could Not Be Deleted.");
    redirect_to("list_photos.php");
}
?>

<?php
    if(isset($database)){
        $database->close_connection();
    }
?>