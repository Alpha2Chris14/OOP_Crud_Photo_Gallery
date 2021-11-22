<?php
require_once(LIB_PATH.DS.'config.php');

class MySQLDatabase {
	
	private $connection;
    public $last_query;
	private $magic_quotes_active;
    private $real_escape_string_exist;
  function __construct() {
    $this->open_connection();
    
	$this->magic_quotes_active = get_magic_quotes_gpc();
	$this->real_escape_string_exist = function_exists( "mysql_real_escape_string" ); // i.e. PHP >= v4.3.0
  }

	public function open_connection() {
		$this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS);
		if (!$this->connection) {
			die("Database connection failed: " . mysqli_error());
		} else {
			$db_select = mysqli_select_db($this->connection,DB_NAME);
			if (!$db_select) {
				die("Database selection failed: " . mysqli_error());
			}
		}
	}

	public function close_connection() {
		if(isset($this->connection)) {
			mysqli_close($this->connection);
			unset($this->connection);
		}
	}

	public function query($sql) {
        $this->last_query = $sql;
		$result = mysqli_query($this->connection,$sql);
		$this->confirm_query($result);
		return $result;
	}
	
	public function escape_value($value) {
		if($this->real_escape_string_exist) { // PHP v4.3.0 or higher
			// undo any magic quote effects so mysql_real_escape_string can do the work
			if( $this->magic_quotes_active ) { $value = stripslashes( $value ); }
			$value = mysqli_real_escape_string($this->connection,$value);
		} else { // before PHP v4.3.0
			// if magic quotes aren't already on then add slashes manually
			if( !$this->magic_quotes_active ) { $value = addslashes( $value ); }
			// if magic quotes are active, then the slashes already exist
		}
		return $value;
	}
	
    public function fetch_array($result_set){
        return mysqli_fetch_array($result_set);
    }

    public function num_rows($result_set){
        //returns how many rows are in a result set
        return mysqli_num_rows($result_set);
    }

    public function insert_id(){
        //get the last id inserted over the current db connection
        return mysqli_insert_id($this->connection);
    }

    public function affected_rows(){
        return mysqli_affected_rows($this->connection);
    }

	private function confirm_query($result) {
		if (!$result) {
            $output = "Database query failed: " . mysqli_error($this->connection)."<br><br>";
            $output.= "The Last Query Was ".$this->last_query;
			die($output);
		}
	}
	
}

$database = new MySQLDatabase();
$db =& $database;

?>