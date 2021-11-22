<?php
    require_once('../../include/initialize.php');


    if(!$session->is_logged_in()){
        redirect_to("login.php");
    }
?>

    <?php include_layout_template('admin_header.php'); ?>

        <h2>
            Menu
        </h2>
        <?php 
  if(isset($message)){
    echo output_message($message);
  }
?>
    <ul>
        <li><a href="list_photos.php">List Photos</a></li>
        <li><a href="log_file.php">View Log File</a></li>
        <li><a href="logout.php">logout</a></li>
    </ul>

    <?php include_layout_template('admin_footer.php'); ?>

    