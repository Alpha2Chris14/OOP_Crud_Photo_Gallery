<?php
    require_once("../include/initialize.php");
?>

<?php
    if(empty($_GET['id'])){
        $session->message("No Photograph ID Was Provided.");
        redirect_to('index.php');
    }
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $photo = Photograph::find_by_id($id);
        if(!$photo){
            $session->message("The Photo Could Not Be Found.");
            redirect_to('index.php');
        }
    }
    $message = "";

    if(isset($_POST['submit'])){
        $author = trim($_POST['author']);
        $body = trim($_POST['body']);

        $new_comment = Comment::make($photo->id,$author,$body);
        if($new_comment && $new_comment->save())  {
            //comment saved
            //No message nedded  
            redirect_to("photo.php?id={$photo->id}");
        }else{
            //Fail
            $message = "There Was Error THat Prevented The Comment From Being Saved";
        }
    }else{
        $author = "";
        $body = "";
    }
    $comments = $photo->comments();
?>
<?php  include_layout_template('header.php') ?>

    <a href="index.php">&laquo; Back</a><br>

    <div style=";margin-left:20px;">
        <a href="photo.php?id=<?php echo $photo->id; ?>">
            <img src="<?php echo $photo->image_path(); ?>" alt="<?php echo $photo->caption; ?>"/>
        </a>
        <p><?php echo $photo->caption; ?></p>
    </div>  
    
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
            </div>
            <br> 
        <?php endforeach; ?>
        <?php 
            if(empty($comments)){
                echo "No Comments.";
            } 
        ?>
    </div>

    <!-- list comments -->
    <div id="id">
        <h3>New Comment</h3>
        <?php echo output_message($message); ?>
        <form action="photo.php?id=<?php echo $photo->id; ?>" method="post">
            <table>
                <tr>
                    <td>Your Name</td>
                    <td><input type="text" name="author" value="<?php echo $author; ?>"></td>
                </tr>
                <tr>
                    <td>Your Comment</td>
                    <td>
                        <textarea name="body" id="" cols="40" rows="10">
                            <?php echo $body; ?>
                        </textarea>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" name="submit" value="Submit Comment"></td>
                </tr>
            </table>
        </form>
    </div>



<?php  include_layout_template('footer.php') ?>