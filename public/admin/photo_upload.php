<?php
    require_once('../../include/initialize.php');


    if(!$session->is_logged_in()){
        redirect_to("login.php");
    }
?>
<?php
    $max_file_size = 1048576; //expressed in bytes
                              //10240 = 10kb
                              //102400 = 100kb
                              //1048576 = 1mb
                              //10485760 = 10mb

    if(isset($_POST['submit'])){
        $photo = new Photograph();
        $photo->caption = $_POST['caption'];
        $photo->attach_file($_FILES['file_upload']);
        if($photo->save()){
            //success
            $session->message("Photograph Was Successfully Uploaded");
            redirect_to('list_photos.php');
        }else{
            //failure
            $message = join("<br>",$photo->errors);
        }
    }
?>

<?php
    
?>
<?php include_layout_template('admin_header.php'); ?>

<h2>Photo Upload</h2>
<?php echo output_message($message); ?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size; ?>"/>
    <p><input type="file" name="file_upload" id=""></p>
    <p>Caption: <input type="text" name="caption" value=""/></p>
    <input type="submit" name="submit" value="Upload">
</form>

<?php include_layout_template('admin_footer.php'); ?>

    