<?php
    require_once("../include/initialize.php");
?>

<?php
    //1. The Current Page Number($current_page)
    $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
    //2. Records Per Page($per_page)
    $per_page = 3;
    //3. Total Record Count($total_count)
    $total_count = Photograph::count_all();

    //Find All Photos
    //$photos = Photograph::find_all();
    $pagination = new Pagination($page,$per_page,$total_count);

    $sql = "SELECT * FROM photographs ";
    $sql .= "LIMIT {$per_page} ";
    $sql .= "OFFSET {$pagination->offset()}";

    $photos = Photograph::find_by_sql($sql);

    //Need To Add ?php=$page to all links we want to
    //maintain the current (or store $page in $session)

?>

<?php  include_layout_template('header.php') ?>

<?php
    foreach($photos as $photo):
?>
        <div style="float:left;margin-left:20px;">
            <a href="photo.php?id=<?php echo $photo->id; ?>">
                <img src="<?php echo $photo->image_path(); ?>" width="200" alt="<?php echo $photo->caption; ?>"/>
            </a>
            <p><?php echo $photo->caption; ?></p>
        </div>    


<?php
    endforeach;
?>

<div id="pagination" style="clear: both;">
    <?php 
        if($pagination->total_pages() > 1 ){
            if($pagination->has_previous_page()){
                echo " <a href=\"index.php?page={$pagination->previous_page()}\">";
                echo "&laquo; Previous</a> ";
            }
            for($i = 1; $i <= $pagination->total_pages();$i++){
                if($i == $page){
                    echo " <span class=\"selected\">{$i}</span>";
                }else{
                    echo " <a href=\"index.php?page={$i}\">{$i}</a> ";
                }
            }
            if($pagination->has_next_page()){
                echo " <a href=\"index.php?page={$pagination->next_page()}\">";
                echo "Next &raquo;</a> ";
            }
        }
    ?>
</div>
<?php  include_layout_template('footer.php') ?>