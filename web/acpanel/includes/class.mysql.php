<?php

if(!defined('IN_ACP')) die("Hacking attempt!");

class MySQL {

	// SET THESE VALUES TO MATCH YOUR DATA CONNECTION
	private $db_host    = "";  // server name
	private $db_user    = "";       // user name
	private $db_pass    = "";           // password
	private $db_dbname  = "";           // database name
	private $db_charset = "";           // optional character set (i.e. utf8)
	private $db_pcon    = false;        // use persistent connection?

	// class-internal variables - do not change
	private $error_desc     = "";       // mysql error string
	private $error_number   = 0;        // mysql error number
	private $mysql_link     = 0;        // mysql link resource
	private $count_query	= 0;        // mysql count query
	private $delta			= 0;        // mysql time query
	private $sql            = "";       // mysql query
	private $result;                    // mysql query result
	private $data = array();		// placeholders
	private $debug_info = array();		// debug_info

	/**
	* Determines if an error throws an exception
	*
	* @var boolean Set to true to throw error exceptions
	*/
	public $ThrowExceptions = true;

	/**
	* Constructor: Opens the connection to the database
	*
	* @param boolean $connect (Optional) Auto-connect when object is created
	* @param string $database (Optional) Database name
	* @param string $server   (Optional) Host address
	* @param string $username (Optional) User name
	* @param string $password (Optional) Password
	* @param string $charset  (Optional) Character set
	*/

	function __construct($server="", $username="", $password="", $database="", $charset="", $pcon=false) {

		if ($pcon)                 $this->db_pcon    = true;
		if (strlen($server)   > 0) $this->db_host    = $server;
		if (strlen($username) > 0) $this->db_user    = $username;
		if (strlen($password) > 0) $this->db_pass    = $password;
		if (strlen($database) > 0) $this->db_dbname  = $database;
		if (strlen($charset)  > 0) $this->db_charset = $charset;

		//
		if (strlen($this->db_host) > 0 && strlen($this->db_user) > 0)
		{
			$this->Open();
		}
	}

	/**
	* Connect to specified MySQL server
	*
	* @return boolean Returns TRUE on success or FALSE on error
	*/

	private function Open() {
		$this->ResetError();

		// Open persistent or normal connection
		if ($this->db_pcon) {
			$this->mysql_link = @mysql_pconnect($this->db_host, $this->db_user, $this->db_pass);
		} else {
			$this->mysql_link = @mysql_connect ($this->db_host, $this->db_user, $this->db_pass);
		}

		// Connect to mysql server failed?
		if (! $this->IsConnected()) {
			$this->SetError();
			return false;
		}
			else // Connected to mysql server
		{
			// Select a database (if specified)
			if (strlen($this->db_dbname) > 0) {
				if (strlen($this->db_charset) == 0) {
					if (! $this->SelectDatabase($this->db_dbname)) {
						return false;
					} else {
						return true;
					}
				} else {
					if (! $this->SelectDatabase($this->db_dbname, $this->db_charset)) {
						return false;
					} else {
						return true;
					}
				}
			} else {
				return true;
			}
		}
	}

	/**
	* Executes the given SQL query and returns the result
	*
	* @param string $sql The query string
	* @return (boolean, string, object, array with objects) result
	*/

	public function Query($sql, $data = array(), $debug = false)
	{
		if( !empty($data) )
			$sql = $this->query_process($sql, $data);
		$this->count_query++;
		$this->ResetError();
		$this->sql = $sql;
		$xtime = microtime();
		$this->result = @mysql_query($this->sql, $this->mysql_link);
		// show debug info
		if($debug) self::SaveDebugInfo("sql=".$this->sql);
		// start the analysis
		if (TRUE === $this->result) {   // simply result
			$return = TRUE;         // successfully (for example: INSERT INTO ...)
		} else if (FALSE === $this->result) {
			$this->SetError();
			if($debug) {
				self::SaveDebugInfo("error=".$this->error_desc);
				self::SaveDebugInfo("number=".$this->error_number);
			}
			$return = FALSE;        // error occured (for example: syntax error)
		}
		else // complex result
		{
			$num_result = mysql_num_rows($this->result);

			switch( $num_result )
			{
				case 0:
					$return = NULL; // return NULL rows
					break;
				case 1: // return one row ...
					if(1 != mysql_num_fields( $this->result))
					{
						$return = array();
						while( $obj = mysql_fetch_object($this->result)) array_push($return, $obj);
					}
					else
					{
						$row    = mysql_fetch_row($this->result);       // or as single value
						$return = $row[0];
					}
					break;
				default:
					$return = array();
					while( $obj = mysql_fetch_object($this->result)) array_push($return, $obj);
			}
		}
		$ytime = microtime();
		$this->delta += array_sum(explode(' ',$ytime)) - array_sum(explode(' ',$xtime));

		return $return;
	}

	/**
	*  Show count query
	*/

	public function CountQuery() {
		return $this->count_query;
	}

	/**
	*  Show time query
	*/

	public function DeltaQuery() {
		return $this->delta;
	}

	/**
	*  Show number of affected rows in previous operation
	*/

	public function Affected() {
		return mysql_affected_rows($this->mysql_link);
	}

	/**
	*  Show last insert ID
	*/

	public function LastInsertID() {
		return mysql_insert_id($this->mysql_link);
	}

	/**
	* Determines if a valid connection to the database exists
	*
	* @return boolean TRUE idf connectect or FALSE if not connected
	*/

	public function IsConnected() {
		if(isset($this->mysql_link))
		{
			if (gettype($this->mysql_link) == "resource") {
				return true;
			}
		}

		return false;
	}

	/**
	* Selects a different database and character set
	*
	* @param string $database Database name
	* @param string $charset (Optional) Character set (i.e. utf8)
	* @return boolean Returns TRUE on success or FALSE on error
	*/

	public function SelectDatabase($database, $charset = "") {
		$return_value = true;
		if (! $charset) $charset = $this->db_charset;
		$this->ResetError();
		if (! (mysql_select_db($database))) {
			$this->SetError();
			$return_value = false;
		} else {
			if ((strlen($charset) > 0)) {
				if (! (mysql_query("SET NAMES {$charset}", $this->mysql_link))) {
					$this->SetError();
					$return_value = false;
				}
			}
		}
		return $return_value;
	}

	/**
	* Clears the internal variables from any error information
	*
	*/

	private function ResetError() {
		$this->error_desc = '';
		$this->error_number = 0;
	}

	/**
	*  Save debug info
	*/
	private function SaveDebugInfo($string="")
	{
		$this->debug_info[] = gmdate("H:i:s").": ---".$string."---<br />";
	}

	/**
	*  Show debug info
	*/
	public function ShowDebugInfo() {
		return $this->debug_info;
	}

	/**
	* Sets the local variables with the last error information
	*
	* @param string $errorMessage The error description
	* @param integer $errorNumber The error number
	*/

	private function SetError($errorMessage = '', $errorNumber = 0) {
		try {
			// get/set error message
			if (strlen($errorMessage) > 0) {
				$this->error_desc = $errorMessage;
			} else {
				if ($this->IsConnected()) {
					$this->error_desc = mysql_error($this->mysql_link);
				} else {
					$this->error_desc = mysql_error();
				}
			}

			// get/set error number
			if ($errorNumber <> 0) {
				$this->error_number = $errorNumber;
			} else {
				if ($this->IsConnected()) {
					$this->error_number = @mysql_errno($this->mysql_link);
				} else {
					$this->error_number = @mysql_errno();
				}
			}
		} catch(Exception $e) {
			$this->error_desc = $e->getMessage();
			$this->error_number = -999;
		}

		if ($this->ThrowExceptions && $this->error_number <> 0) {
			throw new Exception($this->error_desc);
		}
	}

	/**
	* Destructor: Closes the connection to the database
	*
	*/

	public function __destruct() {
		$this->Close();
	}

	/**
	* Close current MySQL connection
	*
	* @return object Returns TRUE on success or FALSE on error
	*/

	public function Close() {
		$this->ResetError();
		$success = $this->Release();
		if ($success) {
			$success = @mysql_close($this->mysql_link);
			if (!$success) {
				$this->SetError();
			} else {
				unset($this->sql);
				unset($this->result);
				unset($this->mysql_link);
			}
		}
		return $success;
	}

	/**
	* Frees memory used by the query results and returns the function result
	*
	* @return boolean Returns TRUE on success or FALSE on failure
	*/

	public function Release() {
		$this->ResetError();
		if (!isset($this->result)) {
			$success = true;
		} else {
			if (!$this->result) {
				$success = true;
			} else {
				$success = @mysql_free_result($this->result);
				if (!$success) $this->SetError();
			}
		}
		return $success;
	}

	private function holders_replace($matches) {
		$placeholder = $matches[1];

		if(is_array($this->data[$placeholder])) {
			$data = array();
			foreach($this->data[$placeholder] as $v) $data[] = mysql_real_escape_string($v);
			$value = implode("', '", $data);
		} else {
			$value = mysql_real_escape_string($this->data[$placeholder]);
		}
		return "{$value}";
	}
	
	private function query_process($query, $data) {
		$this->data = $data;

		$query = preg_replace_callback("#{([^}{]+)}#sUi", array($this, 'holders_replace'), $query);
		return $query;
	}
}

?>