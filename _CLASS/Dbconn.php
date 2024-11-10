<?php
/**
 *  Dbconn class
 *
 * author     woox
 * version    1.0
 */
//namespace DYFLUX;
use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
class Dbconn
{
	private $host;
	private $dbuser;
	private $dbpass;
	private $dbname;
	private $connectionInfo;
	private $dbCharset = "UTF-8";
	static 	$connect;

	private $logger;
	private $loggingUsed= "Y";
	private $logFolder = "/";
	private $logFileName = "";

	private $debugLogger;

	public $query_analyze;

	//Construct
	function __construct() {
		global $DB;

		if (is_array($DB)) {
			$this -> host 		= $DB['host'];
			$this -> dbuser  	= $DB['dbuser'];
			$this -> dbpass 	= $DB['dbpass'];
			$this -> dbname 	= $DB['dbname'];
			$this -> connectionInfo = array(
				"UID" => $DB['dbuser'],
				"PWD" => $DB['dbpass'],
				"Database" => $DB['dbname'],
				"CharacterSet" => $this->dbCharset,
				"ReturnDatesAsStrings" => true
			);
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
	}

	// Destruct class
	function __destruct()
	{
		if($this->connect) {
			sqlsrv_close($this->connect);
		}
	}

	// Set database name
	function set_dbname($dbname)
	{
		$this -> dbname = $dbname;
	}

	// Set database name
	function get_dbname()
	{
		return $this -> dbname;
	}

	// Database connect
	public function db_connect()
	{
		$this -> connect = sqlsrv_connect($this->host, $this->connectionInfo) or die(" Error ");
		//$this -> connect = sqlsrv_connect($this->host, $this->connectionInfo) or die( print_r( sqlsrv_errors(), true));
	}

	// Connection close
	public function db_close ()
	{
		@sqlsrv_close($this -> connect);
		$this -> connect = "";
	}

	// Get microtime
	public function getmicrotime ()
	{
		list($usec, $sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec);
	}

	// Log write to text file
	public function log_write ($msg, $log_info_ary = array())
	{

		if($this -> loggingUsed != "Y")
			return;

		/*
		if(! $this -> logFileName)
		{
			$today = date("Ymd");
			$this -> logFileName = $this -> logFolder."/" . $today . ".data";
		}

		$fp = @fopen($this -> logFileName , "a+") or die("Can't open ".$this -> logFileName);
		$filename = $this -> logFileName;

		@fwrite($fp, "#$msg\n");
		@fclose($fp);
		*/
		$this->logger->addError($msg, $log_info_ary);
	}

	//MSSQL Transaction Begin
	public function sqlTransactionBegin()
	{
		if ( sqlsrv_begin_transaction( $this->connect ) === false ) {
			die( print_r( sqlsrv_errors(), true ));
		}
		//sqlsrv_begin_transaction($this->connect);
	}

	//MSSQL Transaction Commit
	public function sqlTransactionCommit()
	{
		sqlsrv_commit($this->connect);
	}

	//MSSQL Transaction Rollback
	public function sqlTransactionRollback()
	{
		sqlsrv_rollback($this->connect);
	}

	// Print error massage
	private function sql_error ($query, $location, $errObj = "")
	{
		global $_POST;
		//$err_msg = $location . " ::::::::::::::::::> " . $query . "<br> : ".sqlsrv_errors();
		//echo $location . " => " . $query . "<br>";
		//$this->log_write($location . " => " . $query);
		$error_ary                = array();
		$error_ary["path"]        = $_SERVER["SCRIPT_NAME"];
		$error_ary["querystring"] = $_SERVER["QUERY_STRING"];
		$error_ary["postdata"]    = $_POST;
		$error_ary["SQLSTATE"]    = array();
		$error_ary["CODE"]        = array();
		$error_ary["MESSAGE"]     = array();
		foreach( $errObj as $error )
		{
//			$this->log_write("SQLSTATE : ".$error['SQLSTATE']);
//			$this->log_write("CODE : ".$error['code']);
//			$this->log_write("MESSAGE : ".$error['message']);

			$error_ary["SQLSTATE"][] = $error['SQLSTATE'];
			$error_ary["CODE"][]     = $error['code'];
			$error_ary["MESSAGE"][]  = $error['message'];

//			echo SQLSTATE . " : " . $error['SQLSTATE'] . "<br>";
//			echo CODE . " : " . $error['code'] . "<br>";
//			echo MESSAGE . " : " . $error['message'] . "<br>";
		}

		$this->log_write($location . " => " . $query, $error_ary);

		exit();
	}

	// Get result rows count
	protected function  execSqlNumRow ($query)
	{
		$result = $this -> execQry ($query, 'select');

		if (!$result){
			$this->sql_error($query , 'execSqlNumRow', sqlsrv_errors(SQLSRV_ERR_ERRORS));
		}
		else {
			if (sqlsrv_num_rows($result))
				return sqlsrv_num_rows($result);
			else
				return 0;
		}
	}

	// Get one colume data
	public function  execSqlOneCol ($query)
	{
		$result = $this -> execQry ($query, 'select');

		if (!$result){
			$this->sql_error($query , 'execSqlOneCol', sqlsrv_errors(SQLSRV_ERR_ERRORS));
		}

		else {
			if (@sqlsrv_num_rows($result)) {
				sqlsrv_fetch($result);
				return sqlsrv_get_field($result, 0);
			} else {
				return 0;
			}
		}
	}

	// Get one row data
	public function execSqlOneRow($query)
	{
		$result = $this -> execQry ($query, 'select');

		if (!$result){
			$this->sql_error($query , 'execSqlOneRow', sqlsrv_errors(SQLSRV_ERR_ERRORS));
		}
		else return sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC, 1);
	}

	// Get list data
	public function execSqlList($query) {
		$result = $this -> execQry ($query,'select');

		if (!$result){
			$this->sql_error($query , 'execSqlList', sqlsrv_errors(SQLSRV_ERR_ERRORS));
		}
		$count	= 0;
		$data	= array();
		while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){
			$data[$count] = $row;
			$count++;
		}
		sqlsrv_free_stmt($result);
		return $data;
	}


	// execute Update, Delete
	public function execSqlUpdate ($query)
	{
		$result = $this -> execQry ($query,'update');

		if (!$result){
			$this->sql_error($query , 'execSqlUpdate', sqlsrv_errors(SQLSRV_ERR_ERRORS));
		}
		else return $result;
	}

	// execute Insert
	public function execSqlInsert ($query)
	{
		$result = $this -> execQry ($query, 'insert');
		if (!$result){
			$this->sql_error($query , 'execSqlInsert', sqlsrv_errors(SQLSRV_ERR_ERRORS));
		} else {
			$key = $this -> sqlsrv_insert_id();
			return $key;
		}
	}

	public function sqlsrv_insert_id() {
		$id = 0;
		$res = sqlsrv_query($this->connect, "SELECT @@identity AS id");
		if ($row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC)) {
			$id = $row["id"];
		}
		return $id;
	}

	// execute query
	private function execQry ($query, $exec_type = '') {

		if (!$query) return false;

		$time_start = $this -> getmicrotime();
		if($exec_type == "select")
		{
			//$result = sqlsrv_query($this->connect, $query, array(), array( "Scrollable" => 'static' ));
			$result = sqlsrv_query($this->connect, $query, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));

		}else{
			$result = sqlsrv_query($this->connect, $query);
		}
		$time_end = $this -> getmicrotime();
		$time = ((float)$time_end - (float)$time_start);

		$this -> devQueryString($query, number_format($time, 5));

		$getNow = date("YmdHms");
		$detail_rst = $getNow . "||" . $time . "||" . $query;
		$detail_rst2 = $query . ' ('. number_format($time,5) . ' Sec)';
		$add_detail = "";
		$exp_query = "";
		// except select log
		if ($exec_type == 'insert' || $exec_type == 'update') {
			//$this->log_write($detail_rst);
		} else {
			//$exp_query .= 'EXPLAIN ' . $query;
			//$add_detail = $this -> expResult(sqlsrv_query($this->connect, $exp_query));
		}
		$add_detail .= ($add_detail)? '<br>' : '';
		$this->query_analyze .= $detail_rst2 . '<br>' . $add_detail;

		return $result;
	}

	//Explain Query Result
	private function expResult ($result) {
		if (!empty($result)) {
			$str = '<table border=\'1\' width=\'700px\'><tr>';
			$str .= '<td>ID</td>';
			$str .= '<td>SELECT TYPE</td>';
			$str .= '<td>TABLE</td>';
			$str .= '<td>TYPE</td>';
			$str .= '<td>POSSIBLE KEY</td>';
			$str .= '<td>KEY</td>';
			$str .= '<td>KEY LEN</td>';
			$str .= '<td>REF</td>';
			$str .= '<td>ROWS</td>';
			$str .= '<td>EXTRA</td>';
			$str .= '</tr>';

			while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC))
			{
				$str .= '</tr>';
				$str .= '<td>' . $row['id'] . '&nbsp;</td>';
				$str .= '<td>' . $row['select_type'] . '&nbsp;</td>';
				$str .= '<td>' . $row['table'] . '&nbsp;</td>';
				$str .= '<td>' . $row['type'] . '&nbsp;</td>';
				$str .= '<td>' . $row['possible_keys'] . '&nbsp;</td>';
				$str .= '<td>' . $row['key'] . '&nbsp;</td>';
				$str .= '<td>' . $row['key_len'] . '&nbsp;</td>';
				$str .= '<td>' . $row['ref'] . '&nbsp;</td>';
				$str .= '<td>' . $row['rows'] . '&nbsp;</td>';
				$str .= '<td>' . $row['Extra'] . '&nbsp;</td>';
				$str .= '</tr>';
			}
			$str .= '</table>';
			sqlsrv_free_stmt($result);
		}

		return $str;
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
}
?>
