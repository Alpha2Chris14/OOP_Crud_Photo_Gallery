<?php
require_once("../../include/initialize.php");

    if(!$session->is_logged_in()) {
        redirect_to("login.php");
    }
    $file = SITE_ROOT.DS.'log'.DS.'log.txt';
    if(isset($_GET['clear'])){
        if($handle = fopen($file,'w')){
            fwrite($handle,'');
            fclose($handle);
        }
    }
    
    if( file_exists($file) && is_readable($file) && 
              $handle = fopen($file, 'r')) {  // read
      echo "<ul class=\"log-entries\">";
          while(!feof($handle)) {
              $entry = fgets($handle);
              if(trim($entry) != "") {
                  echo "<li>{$entry}</li>";
              }
          }
          echo "</ul>";
      fclose($handle);
    } else {
      echo "Could not read from {$file}.";
    }
  
  
  

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">

        <input type="submit" value="Clear Logs" name="clear">
    </form>
</body>
</html>