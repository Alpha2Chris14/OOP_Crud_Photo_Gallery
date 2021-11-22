<?php

    function strip_zero_from_date($marked_string=""){
        //first remove the marked zeroes
        $no_zeroes = str_replace('*0','',$marked_string);

        //remove any remaining mark
        $cleaned_string = str_replace('*','',$no_zeroes);
        return $cleaned_string;
    }

    function redirect_to($location = NULL){
        if($location != null){
            header("Location: {$location}");
            exit;
        }
    }

    function output_message($message=""){
        if(!empty($message)){
            return "<p class=\"message\">{$message}<p>";
        }
        else{
            return "";
        }
    }

    function __autoload($class_name){
        $class_name = strtolower($class_name);
        $path = LIB_PATH.DS."{$class_name}.php";
        if(file_exists($path)){
            require_once($path);
        }
        else{
            die("Class {$class_name}.php Could Not Be Found ");
        }
    }

    function include_layout_template($template = ""){
        include(SITE_ROOT.DS.'public'.DS.'layouts'.DS.$template);
    }

    function log_action($action,$message = ""){
        $file = SITE_ROOT.DS.'log'.DS.'log.txt';
        //if(!file_exists($file)){
            
            $contents = read_content($file);
            if($handle = fopen($file,'w')){
                $time = time();
                $str_format = strftime("%Y-%m-%d %H:%M:%S",$time);
                $contents .= "\n{$str_format}|  {$action} : $message";
                fwrite($handle,$contents);
                fclose($handle);
            }
            else{
                echo "unable to open file";
            }
        
    }

    function read_content($file){
        
        $content = "";
        if($handle = fopen($file,'r')){
            while(!feof($handle)){
                $content.=fgetc($handle);
            }
            fclose($handle);
            
            return $content;
        }
    }

    function date_to_text($datetime=""){
        $unixdatetime = strtotime($datetime);
        return strftime("%B %d, %Y at %I:%M %p",$unixdatetime);
    }
?>