<?php
/**
 *  DBConn class
 *
 * author     woox
 * version    1.0
 */

use Monolog\Logger as Logger;
use Monolog\Formatter\LineFormatter;

class DBConn {
	private $host;
	private $user;
	private $password;
	private $database;

	private static $connect = null; //mysqli
	private static $ref_cnt = 0;
	private static $transaction_cnt = 0;

	private $logger;
	private $loggingUsed= "Y";

	private $debugLogger;

	public $query_analyze;

	//Construct
	function __construct() {
		global $DB;

		if (is_array($DB)) {
			$this->host = $DB['host'];
			$this->user	= $DB['dbuser'];
			$this->password = $DB['dbpass'];
			$this->database = $DB['dbname'];
		} else {
			echo "Undefined connect information";
			exit;
		}

		//Logger Init..
		$loggerFormat = "%datetime% [[^^]] %level_name% [[^^]] %message% [[^^]] %context% [[^^]] %extra%\n";
		$loggerTimeFormat = "Y-m-d H:i:s";
		$formatter = new LineFormatter($loggerFormat, $loggerTimeFormat);

		$this->logger = new Logger('DB.Log');
		$loggerHandler = new \Monolog\Handler\StreamHandler(DY_LOG_PATH . "/DB_" . date('Y-m-d') . ".log", Logger::ERROR);
		$loggerHandler->setFormatter($formatter);
		$this->logger->pushHandler($loggerHandler);

		$this->debugLogger = new Logger('Debug.Log');
		$debugHandler = new \Monolog\Handler\StreamHandler(DY_LOG_PATH . "/DEBUG_" . date('Y-m-d') . ".log", Logger::DEBUG);
		$debugHandler->setFormatter($formatter);
		$this->debugLogger->pushHandler($debugHandler);

		self::$ref_cnt++;
	}

	// Destruct class
	function __destruct() {
		self::$ref_cnt--;
		$this->db_close();
	}

	// Set database name
	function setDatabase($database) {
		$this->database = $database;
	}

	// Get database name
	function getDatabase() {
		return $this->database;
	}

	// Database connect
	public function db_connect() {
		if (self::$connect == null) {
			self::$connect = new mysqli($this->host, $this->user, $this->password, $this->database);
			if (self::$connect->connect_errno) {
				echo "Failed to connect to MySQL : (" . self::$connect->connect_errno . ") " . self::$connect->connect_error;
				exit;
			}
		}
	}

	// Connection close
	public function db_close ($isForce = false) {
		if (!$isForce) {
			if (self::$ref_cnt > 0) return;
		}

		if (self::$connect) {
			mysqli_close(self::$connect);
			self::$connect = null;
		}
	}

	public function isConnected() {
		return self::$connect != null;
	}

	// Get MicroTime
	public function getMicroTime () {
		list($uSec, $sec) = explode(" ", microtime());
		return ((float)$uSec + (float)$sec);
	}

	// Log write to text file
	public function writeLog ($msg, $log_info_ary = array()) {
		if($this -> loggingUsed != "Y") return;
		$this->logger->addError($msg, $log_info_ary);
	}

	//MSSQL Transaction Begin
	public function sqlTransactionBegin() {
		self::$transaction_cnt++;

		if(self::$transaction_cnt > 1) {
			return true;
		}

		if (!mysqli_begin_transaction(self::$connect, MYSQLI_TRANS_START_READ_WRITE)) {
			echo "Failed to begin transaction to MySQL(" . self::$connect->errno . "): " . self::$connect->error;
			exit;
		}

		return true;
	}

	//MySQL commit
	public function sqlTransactionCommit() {
		self::$transaction_cnt--;

		if(self::$transaction_cnt > 0) {
			return true;
		} elseif(self::$transaction_cnt < 0) {
			$this->writeLog("transaction count can't be minus value.");
		}

		return mysqli_commit(self::$connect);
	}

	//MySQL rollback
	public function sqlTransactionRollback() {
		self::$transaction_cnt--;

		if(self::$transaction_cnt > 0) {
			return true;
		} elseif(self::$transaction_cnt < 0) {
			$this->writeLog("transaction count can't be minus value.");
		}

		return mysqli_rollback(self::$connect);
	}

	public function query($query) {
		$rst = mysqli_query(self::$connect, $query);
		$msg = "Error message(" . self::$connect->errno . "): " . self::$connect->error;
		if (!$rst) {
			$this->writeLog($msg);
		}
		return $rst;
	}

	public function multiQuery($query) {
		$rst = mysqli_multi_query(self::$connect, $query);
		$msg = "Error message(" . self::$connect->errno . "): " . self::$connect->error;
		if (!$rst) {
			$this->writeLog($msg);
		}
		return $rst;
	}

	// Print error massage
	private function sql_error ($query, $location, $errObj = []) {
		global $_POST;

		$error_ary                = array();
		$error_ary["path"]        = $_SERVER["SCRIPT_NAME"];
		$error_ary["querystring"] = $_SERVER["QUERY_STRING"];
		$error_ary["postdata"]    = $_POST;
		$error_ary["SQLSTATE"]    = array();
		$error_ary["CODE"]        = array();
		$error_ary["MESSAGE"]     = array();

		foreach( $errObj as $error ) {
			$error_ary["SQLSTATE"][] = $error['SQLSTATE'];
			$error_ary["CODE"][]     = $error['code'];
			$error_ary["MESSAGE"][]  = $error['message'];
		}

		$this->writeLog($location . " => " . $query, $error_ary);

		exit();
	}

	// Get result rows count
	protected function  execSqlNumRow($query) {
		$result = $this -> execQry ($query, 'select');

		if (!$result){
			$this->sql_error($query , 'execSqlNumRow', self::$connect->error);
			return 0;
		}
		else {
			if (mysqli_num_rows($result)) {
				$rst = mysqli_num_rows($result);

				$row = $result->fetch_all();
				$result->close();

				return $rst;
			} else {
				return 0;
			}
		}
	}

	// Get one column data
	public function  execSqlOneCol ($query) {
		$row = null;
		$result = $this->query($query);

		if (!$result) {
			$this->sql_error($query , 'execSqlOneCol', self::$connect->error);
		} else {
			$row = $result->fetch_array();
		}

		$result->close();

		return $row[0];
	}

	// Get one row data
	public function execSqlOneRow($query) {
		$row = null;
		$result = $this->query($query);

		if (!$result){
			$this->sql_error($query , 'execSqlOneRow', self::$connect->error);
		} else {
			$row = $result->fetch_assoc();
		}

		$result->close();

		return $row;
	}

	// Get list data
	public function execSqlList($query) {
		$result = $this->query($query);

		if (!$result) {
			$this->sql_error($query , 'execSqlList', self::$connect->error);
		}

		$idx = 0;
		$data = array();

		while($row = $result->fetch_assoc()){
			$data[$idx++] = $row;
		}

		$this->freeResult($result);

		return $data;
	}


	// execute Update, Delete
	public function execSqlUpdate ($query) {
		$result = $this->query($query);

		if (!$result) {
			$this->sql_error($query , 'execSqlUpdate', self::$connect->error);
			return 0;
		} else {
			return $result;
		}
	}

	// execute Insert
	public function execSqlInsert ($query) {
		$result = $this->query($query);

		if (!$result){
			$this->sql_error($query , 'execSqlInsert', self::$connect->error);
			return 0;
		} else {
			return self::$connect->insert_id;
		}
	}

	// execute query
	private function execQry ($query, $exec_type = '') {
		if (!$query) return false;

		$time_start = $this -> getMicrotime();
		$result = $this->query($query);
		$time_end = $this -> getMicrotime();
		$time = ((float)$time_end - (float)$time_start);

		$this -> devQueryString($query, number_format($time, 5));

		$getNow = date("YmdHms");
		$detail_rst = $getNow . "||" . $time . "||" . $query;
		$detail_rst2 = $query . ' ('. number_format($time,5) . ' Sec)';
		$add_detail = "";

		$add_detail .= ($add_detail)? '<br>' : '';
		$this->query_analyze .= $detail_rst2 . '<br>' . $add_detail;

		return $result;
	}

	//For Developper
	private function devQueryString($qry, $time)
	{
		global $_dev_query_table;

		$_dev_query_table .= "<tr>";
		$_dev_query_table .= "<td>" . $qry . "</td>";
		$_dev_query_table .= "<td>" . $time . "</td>";
		$_dev_query_table .= "</tr>";
	}

	/**
	 * 디버깅 로그
	 * @param $msg
	 * @param $log_info_ary
	 */
	public function addDebugLog($msg, $log_info_ary = array())
	{
		$this->debugLogger->addDebug($msg, $log_info_ary);
	}

	public function storeResult() {
		if ($this->isConnected()) {
			return self::$connect->store_result();
		}

		return false;
	}

	public function nextResult() {
		if ($this->isConnected()) {
			return self::$connect->next_result();
		}

		return false;
	}

	public function moreResults() {
		if ($this->isConnected()) {
			return self::$connect->more_results();
		}

		return false;
	}

	public function freeResult($result) {
		mysqli_free_result($result);
	}

	public function getError() {
		if ($this->isConnected()) {
			return self::$connect->error;
		}

		return null;
	}

	public function getErrorNo() {
		if ($this->isConnected()) {
			return self::$connect->errno;
		}

		return null;
	}

	// TODO: 테이블 PK가 NN, UQ, AI 모두 사용한다고 판단. 예외처리는 개발자에게 맡긴다.
	public function insertFromArray($array, $table_name, $table_pk = "", $require_db_connection = true) {
		if(!count($array)) return false;

		$for_update = $table_pk != "";
		if($for_update) {
			if(!array_key_exists($table_pk, $array)) return false;
		}

		$qry_start = "INSERT INTO ".$table_name."(";
		$qry_middle = ") VALUES (";
		$qry_end = ")";

		$columns = [];
		$values = [];
		$added_values = [];

		foreach($array as $key => $val) {
			$column = "".$key;
			if($val == "NOW()") {
				$value = $val;
			} else {
				if ($val != null) {
					$value = "N'".$val."'";
				} else {
					$value = "NULL";
				}
			}

			$columns[] = $column;
			$values[] = $value;

			if($for_update) {
				$added_values[] = $column." = ".$value;
			}
		}

		$qry_columns = join("\t, ", $columns);
		$qry_values = join("\t, ", $values);

		$qry = $qry_start."\n\t".$qry_columns."\n".$qry_middle."\n\t".$qry_values."\n".$qry_end;

		if($for_update) {
			$qry_added_end = join("\t, ", $added_values);
			$qry .= "\nON DUPLICATE KEY UPDATE"."\n\t".$qry_added_end;
		}

		if($require_db_connection) $this->db_connect();
		$inserted_pk = $this->execSqlInsert($qry);
		if($require_db_connection) $this->db_close();

		return $inserted_pk;
	}

	public function test() {
		return self::$transaction_cnt;
}
}
?>
