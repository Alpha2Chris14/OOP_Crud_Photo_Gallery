<?php    require_once('../../include/initialize.php'); ?>
<?php if (!$session->is_logged_in()) { redirect_to("login.php"); } ?>

<?php 
    if(empty($_GET['id'])){
        $session->message("No Photograph ID was Proveided");
        redirect_to("index.php");
    }

    $photo = Photograph::find_by_id($_GET['id']);
    if(!$photo){
        $session->message("The Photo Could Not Be Located");
        redirect_to('index.php');
    }

    $comments = $photo->comments();
?>

<?php include_layout_template('admin_header.php'); ?>

<h2>Comments on <?php echo $photo->filename; ?></h2>
<?php echo output_message($message); ?>

<div id="comments">
        <?php foreach($comments as $comment): ?>
            <div class="comment" style="margin: bottom 2em;">
                <div class="author">
                    <?php echo htmlentities($comment->author); ?> wrote:
                </div>
                <div class="body">
                    <?php  echo strip_tags($comment->body,'<strong><em><p>'); ?>
                </div>

                <div style=:font-size:0.8em; class="meta-info">
                    <?php echo date_to_text($comment->created); ?>
                </div>

                <div class="actions" style="font-size:0.8em;">
                    <a href="delete_comment.php?id=<?php echo $comment->id; ?>">Delete Comment</a>
                </div>
            </div>
            <br> 
        <?php endforeach; ?>
        <?php 
            if(empty($comments)){
                echo "No Comments.";
            } 
        ?>
    </div>

<?php include_layout_template('admin_footer.php'); ?>
