<?php
    require_once(LIB_PATH.DS.'database.php');
class User{
    protected static $table_name = "users";
    protected static $db_fields = array('id','username','password','first_name','last_name');
    public $id;
    public $username;
    public $password;
    public $first_name;
    public $last_name;

    

    // public static function authenticate($username="", $password="") {
    //     global $database;
    //     $username = $database->escape_value($username);
    //     $password = $database->escape_value($password);
    
    //     $sql  = "SELECT * FROM users ";
    //     $sql .= "WHERE username = '{$username}' ";
    //     $sql .= "AND password = '{$password}' ";
    //     $sql .= "LIMIT 1";
    //     $result_array = self::find_by_sql($sql);
    //         return !empty($result_array) ? array_shift($result_array) : false;
    //     }

    public static function find_all(){
        $result_set = self::find_by_sql("SELECT * FROM ".self::$table_name);
        return $result_set;
    }

    public static function find_by_id($id = 0){
        global $database;
        $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE id = {$id} LIMIT 1");
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
        $object = new User();
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

    public function save(){
        return isset($this->id) ? $this->update() : $this->create();
    }

    // public function create(){
    //     global $database;
    //     //Don't Forget Your Sql Syntax
    //     //INSERT INTO table(key,key)VALUES('value','value')
    //     // Single-Quote Around All Values
    //     //Escape All Values To Prevent Sql Injection

    //     $sql = "INSERT INTO ".self::$table_name."(";
    //     $sql .= "username,password,first_name,last_name";
    //     $sql .= ")VALUES('";
    //     $sql .= $database->escape_value($this->username)."','";
    //     $sql .= $database->escape_value($this->password)."','";
    //     $sql .= $database->escape_value($this->first_name)."','";
    //     $sql .= $database->escape_value($this->last_name)."')";
    //     if($database->query($sql)){
    //         $this->id = $database->insert_id();
    //         return true;
    //     }else{
    //         return false;
    //     }
    // }

    public function create(){
        global $database;
        //Don't Forget Your Sql Syntax
        //INSERT INTO table(key,key)VALUES('value','value')
        // Single-Quote Around All Values
        //Escape All Values To Prevent Sql Injection
        $attribute = $this->sanitized_attribute();
        $sql = "INSERT INTO ".self::$table_name."(";
        $sql .= join(',',array_keys($attribute));
        $sql .= ")VALUES('";
        $sql.=join("', '",array_values($attribute));
        $sql.= "')";
        if($database->query($sql)){
            $this->id = $database->insert_id();
            return true;
        }else{
            return false;
        }
    }

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
        $sql = "DELETE FROM ".self::$table_name;
        $sql .= " WHERE id = ".$database->escape_value($this->id);
        $sql .= " LIMIT 1";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

}

?>