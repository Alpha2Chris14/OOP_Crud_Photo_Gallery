<?php
    require_once(LIB_PATH.DS.'database.php');
    class Photograph extends DatabaseObject{
        protected static $table_name = "photographs";
        protected static $db_fields = array('id','filename','type','size','caption');
        public $id;
        public $filename;
        public $type;
        public $size;
        public $caption;

        private $temp_path;
        protected $upload_dir = "image";

        public $errors = array();
        protected $upload_errors = array(
            UPLOAD_ERR_OK => "No errors.",
            UPLOAD_ERR_INI_SIZE => "Larger Than Upload_Max_Filesize.",
            UPLOAD_ERR_FORM_SIZE => "Larger Than form MAX_FILE_SIZE.",
            UPLOAD_ERR_PARTIAL => "Partial Upload.",
            UPLOAD_ERR_NO_FILE => "No File.",
            UPLOAD_ERR_NO_TMP_DIR => "No Temporary Directory.",
            UPLOAD_ERR_CANT_WRITE => "Ca't Write To Disk.",
            UPLOAD_ERR_EXTENSION => "File Upload Stopped By Extension.",
        );


        //Pass in $_FILES['uploaded_file'] as an arguement
        public function attach_file($file){
            // Perform error checking on the for parameters
            if(!$file || empty($file) || !is_array($file)){
                //error: nothing uploaded or wrong argument usage
                $this->errors[] = "No File Was Uploaded";
                return false;
            }elseif($file['error'] != 0){
                //error:report what php says went wrong
                $this->errors[] = $this->upload_errors[$file['error']];
                return false;
            }else{
            //Set Object attributes to form parameters
                $this->temp_path = $file['tmp_name'];
                $this->filename = basename($file['name']);
                $this->type = $file['type'];
                $this->size = $file['size'];
                //Don't Worry About Anything To The Database
                return true;
            }

        }

        public function save(){
            //A New Record Won't Have An ID Yet
            if(isset($this->id)){
                $this->update();
            }else{
                // Make sure there are no errors
                if(!empty($this->errors)){
                    return false;
                }
                //make sure caption is not too long
                if(strlen($this->caption) > 255){
                    $this->errors[] = "The Caption Can Only Be 255 Long.";
                    return false;
                }

                //Cam't save filename and temp location
                if(empty($this->filename) || empty($this->temp_path)){
                    $this->errors[] = "The File Location Is Not Available.";
                    return false;
                }

                //Determine The Target_Path
                $target_path = SITE_ROOT.DS.'public'.DS.$this->upload_dir.DS.$this->filename;

                //Make Sure File Doesn't Already Exist
                if(file_exists($target_path)){
                    $this->errors[] = "The file {$this->filename} already exists.";
                    return false;
                }
                // Attempt to move the file
                if(move_uploaded_file($this->temp_path,$target_path)){
                    // Save a corresponding entry to the db
                    if($this->create()){
                        //We are done with tem_path the file has been moved
                        unset($this->temp_path);
                        return true;
                    }
                }else{
                    //File Was Not Moved
                    $this->errors[] = "The file upload failed, Possibly Due To Incorrect Permissions On The Upload Folder.";
                    return false;
                }
                
            }
        }

        public function destroy(){
            //first remove the databse entry
            if($this->delete()){
                //remove the file from the folder
                $target_path = SITE_ROOT.DS.'public'.DS.$this->image_path();
                return unlink($target_path) ? true : false;
            }else{
                //database delete failed
                return false;
            }
            
        }

        public function image_path(){
            return $this->upload_dir.DS.$this->filename;
        }

        public function size_as_text(){
            if($this->size < 1024){
                return "{$this->size} bytes";
            }elseif($this->size < 1048576){
                $size_kb = round($this->size/1024);
                return "{$size_kb} KB";
            }else{
                $size_mb = round($this->size/1048576,1);
                return "{$size_mb} MB";
            }
        }

        public function comments(){
            return Comment::find_comments_on($this->id);
        }

        public static function find_all(){
            $result_set = self::find_by_sql("SELECT * FROM ".self::$table_name);
            return $result_set;
        }
    
        public static function find_by_id($id = 0){
            global $database;
            $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE id = {$database->escape_value($id)} LIMIT 1");
            // $found = $database->fetch_array($result_set);
            // return $found;
            return !empty($result_array) ? array_shift($result_array) : false;
        }
    
        public static function find_by_sql($sql =""){
            global $database;
            $result_set = $database->query($sql);
            $object_array = array();
            while($row = $database->fetch_array($result_set)){
                $object_array[] = self::instantiate($row);
            }
            return $object_array;
        }

        public static function count_all(){
            global $database;
            $sql = "SELECT COUNT(*) FROM ".self::$table_name;
            $result_set = $database->query($sql);
            $row = $database->fetch_array($result_set);
            return array_shift($row);
        }
    
        
        private function has_attribute($attribute){
            //get_object_vars returns an associative array with all attributes
            //(incl. private ones!) as the keys and their current values as the valee
            $object_vars = $this->attribute();
            //we don't care about the value we just want to know if the key exists will return true or false
            return array_key_exists($attribute,$object_vars);
        }
    
        private static function instantiate($record){
    
            //$class_name = get_called_class();
            $object = new Photograph();
            // $object->id = $record['id'];
            // $object->username = $record['username'];
            // $object->paswword = $record['password'];
            // $object->first_name = $record['first_name'];
            // $object->last_name = $record['last_name'];
            
            //A More Dynamic Short Form Approach
            foreach ($record as $attribute => $value){
                if($object->has_attribute($attribute)){
                    $object->$attribute = $value;
                }
            }
            return $object;
        }
        
    
        public function full_name(){
            if(isset($this->first_name) && isset($this->last_name)){
                return $this->first_name." ".$this->last_name;
            }else{
                return "";
            }
        }
    
        public static function authenticate($username="",$password=""){
            global $database;
            $username = $database->escape_value($username);
            $password = $database->escape_value($password); 
    
            $sql = "SELECT * FROM ".self::$table_name." WHERE username = '{$username}' AND password = '{$password}' LIMIT 1 ";
            $result_array = self::find_by_sql($sql);
            return !empty($result_array) ? array_shift($result_array) : false;
        }
    
        protected function attribute(){
            //returns an associative array of keys and their value
            //return get_object_vars($this);
            $attributes = array();
            foreach(self::$db_fields as $field){
                if(property_exists($this,$field)){
                    $attributes[$field] = $this->$field;
                }
            }
            return $attributes;
        }
    
        protected function sanitized_attribute(){
            global $database;
            $clean_attributes = array();
            //Sanitize value before submitting
            //it's doesn't alter its value
            foreach($this->attribute() as $key => $value){
                $clean_attributes[$key] = $database->escape_value($value);
            }
            return $clean_attributes;
        }
        //replaced by a custom function
        // public function save(){
        //     return isset($this->id) ? $this->update() : $this->create();
        // }
    

        public function create(){
            global $database;
            //Don't Forget Your Sql Syntax
            //INSERT INTO table(key,key)VALUES('value','value')
            // Single-Quote Around All Values
            //Escape All Values To Prevent Sql Injection

            $sql = "INSERT INTO ".self::$table_name."(";
            $sql .= "filename,type,size,caption";
            $sql .= ")VALUES('";
            $sql .= $database->escape_value($this->filename)."','";
            $sql .= $database->escape_value($this->type)."','";
            $sql .= $database->escape_value($this->size)."','";
            $sql .= $database->escape_value($this->caption)."')";
            if($database->query($sql)){
                $this->id = $database->insert_id();
                return true;    
            }else{
                return false;
            }
        }
        
    //     public function create(){
    //         global $database;
    //         //Don't Forget Your Sql Syntax
    //         //INSERT INTO table(key,key)VALUES('value','value')
    //         // Single-Quote Around All Values
    //         //Escape All Values To Prevent Sql Injection
    //         $attribute = $this->sanitized_attribute();
    //         $sql = "INSERT INTO ".self::$table_name." (";
	// 	$sql .= join(", ", array_keys($attribute));
	//   $sql .= ") VALUES ('";
	// 	$sql .= join("', '", array_values($attribute));
	// 	$sql .= "')";
    //         if($database->query($sql)){
    //             $this->id = $database->insert_id();
    //             return true;
    //         }else{
    //             return false;
    //         }
    //     }
    
        public function update(){
            global $database;
            //Don't Forget Your Sql Syntax
            //UPDATE table SET (key='value',key='value') WHERE CONDITION
            // Single-Quote Around All Values
            //Escape All Values To Prevent Sql Injection
    
            $sql = "UPDATE ".self::$table_name." SET ";
            $attribute = $this->sanitized_attribute();
            $attribute_pairs = array();
            foreach($attribute as $key => $value){
                $attribute_pairs[] = "{$key} = '{$value}'";
            }
            $sql .= join(",",$attribute_pairs);
            $sql .= "WHERE id = ".$database->escape_value($this->id);
            $database->query($sql);
            return ($database->affected_rows() == 1) ? true : false;
    
        }
    
        public function delete(){
            global $database;
            $sql = "DELETE FROM ".self::$table_name;
            $sql .= " WHERE id = ".$database->escape_value($this->id);
            $sql .= " LIMIT 1";
            $database->query($sql);
            return ($database->affected_rows() == 1) ? true : false;
        }

    }
?>