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
    $session->message("No Photograph ID Was Provided");
    redirect_to('index.php');
}

$photo = Photograph::find_by_id($_GET['id']);
if($photo && $photo->destroy()){
    $session->message("The Photo Was Deleted");
    redirect_to("list_photos.php");
}else{
    $session->message("The Photo Could Not Be Deleted.");
    redirect_to("list_photos.php");
}
?>

<?php
    if(isset($database)){
        $database->close_connection();
    }
?>