<?php 

/** 
 * @version V0.93 18 Jan 2001
 * @author John Lim (jlim@natsoft.com.my).  (c) 2000, 2001 All rights reserved.
 * Released under Lesser GPL library license. See License.txt. 
 *
 * Set tabs to 8 for best viewing.
 * 
 * Latest version is available at http://php.weblogs.com
 * 
 * This is the main include file for ADODB.
 * It has all the generic functionality of ADODB. 
 * Database specific drivers are stored in the adodb-*.inc.php files.
 *
 * Requires PHP4.01pl2 because it uses include_once
*/

 if (!defined('_ADODB_LAYER')) {
 	define('_ADODB_LAYER',1);

	GLOBAL $ADODB_vers, $ADODB_Database, $ADODB_DIR, $ADODB_COUNTRECS;

	/** 
	 * SET THE VALUE BELOW TO THE DIRECTORY WHERE THIS FILE RESIDES
	 * ADODB_RootPath has been renamed ADODB_DIR 
	 */
	if (!isset($ADODB_DIR))
	    $ADODB_DIR = 'lib/adodb';

	//==============================================================================================	
	// CHANGE NOTHING BELOW UNLESS YOU ARE CODING
	//==============================================================================================	

	// using PHPCONFIG protocol overrides ADODB_DIR
	if (isset($PHPCONFIG_DIR)) $ADODB_DIR=$PHPCONFIG_DIR.'/../adodb';
	
	/**
	 * Name of last database driver loaded into memory.
	 */
	$ADODB_Database = '';
	
	/**
	 * ADODB version as a string.
	 */
	$ADODB_vers = 'V0.95 13 Mar 2001 (c) 2000, 2001 John Lim (jlim@natsoft.com.my). All rights reserved. Released LGPL.';

	/**
	 * Determines whether recordset->RecordCount() is used. 
	 * Set to false for highest performance -- RecordCount() will always return -1 then.
	 */
	$ADODB_COUNTRECS = true; 

	/**
	 * Helper class for FetchFields -- holds info on a column
	 */
	class ADODBFieldObject { 
		var $name = '';
		var $max_length=0;
		var $type="";
	}

	/**
	 * Connection object. For connecting to databases, and executing queries.
	 */ 
	class ADODBConnection {
	/*
	 * PUBLIC VARS 
	 */
	var $dataProvider = 'native';
	
	var $databaseType = '';	// RDBMS currently in use, eg. odbc, mysql, mssql
								
	var $database = '';	// Name of database to be used.	
	
	var $host = ''; 	//The hostname of the database server
							
	var $user = ''; 	// The username which is used to connect to the database server. 
	
	var $password = ''; 	// Password for the username

	var $debug = false; 	// if set to true will output sql statements
	
	var $maxblobsize = 8000; // maximum size of blobs or large text fields -- some databases die otherwise like foxpro
	
	var $concat_operator = '+'; 	// default concat operator -- change to || for Oracle/Interbase	
	var $fmtDate = "'Y-m-d'"; 	// used by DBDate() as the default date format used by the database
	var $fmtTimeStamp = "'Y-m-d, h:i:s A'"; // used by DBTimeStamp as the default timestamp fmt.
	var $true = '1'; 		// string that represents TRUE for a database
	var $false = '0'; 		// string that represents FALSE for a database
	var $replaceQuote = "\\'"; 	// string to use to replace quotes
        var $hasInsertID = false; 	// supports autoincrement ID?
        var $hasAffectedRows = false; 	// supports affected rows for update/delete?
        var $autoCommit = true; 
	var $charSet=false; 		// character set to use - only for interbase
	var $metaTablesSQL = '';
	var $hasTop = false;		// support mssql/access SELECT TOP 10 * FROM TABLE
	var $hasLimit = false;		// support pgsql/mysql SELECT * FROM TABLE LIMIT 10
	var $readOnly = false; 		// this is a readonly database ?
	
	/*
	 * PRIVATE VARS
	 */
	var $_connectionID	= -1;	// The returned link identifier whenever a successful database connection is made.	*/
		
	var $_errorMsg = '';		// A variable which was used to keep the returned last error message.  The value will
					//then returned by the errorMsg() function	
						
	var $_queryID = -1;		// This variable keeps the last created result link identifier.		*/
	
	var $_isPersistentConnection = false;	// A boolean variable to state whether its a persistent connection or normal connection.	*/
	
	var $_bindInputArray = false; // set to true if ADODBConnection.Execute() permits binding of array parameters.
	
	/**
	 * Constructor
	 */
	function ADODBConnection()			
	{
		die('Virtual Class -- cannot instantiate');
	}
	
	/**
	 * Connect to database
	 *
	 * @param [argHostname]		Host to connect to
	 * @param [argUsername]		Userid to login
	 * @param [argPassword]		Associated password
	 * @param [argDatabaseName]	database
	 *
	 * @return true or false
	 */	  
	function Connect($argHostname = "", $argUsername = "", $argPassword = "", $argDatabaseName = "") {
		if ($argHostname != "") $this->host = $argHostname;
		if ($argUsername != "") $this->user = $argUsername;
		if ($argPassword != "") $this->password = $argPassword; // not stored for security reasons
		if ($argDatabaseName != "") $this->database = $argDatabaseName;		
		
		return $this->_connect($argHostname,$argUsername,$argPassword,$argDatabaseName);
	}	
	
	/**
	 * Establish persistent connect to database
	 *
	 * @param [argHostname]		Host to connect to
	 * @param [argUsername]		Userid to login
	 * @param [argPassword]		Associated password
	 * @param [argDatabaseName]	database
	 *
	 * @return return true or false
	 */	
	function PConnect($argHostname = "", $argUsername = "", $argPassword = "", $argDatabaseName = "")
	{
		if ($argHostname != "") $this->host = $argHostname;
		if ($argUsername != "") $this->user = $argUsername;
		if ($argPassword != "") $this->password = $argPassword;
		if ($argDatabaseName != "") $this->database = $argDatabaseName;			
			
		if ( $this->_pconnect($argHostname, $argUsername, $argPassword, $argDatabaseName)) {
			$this->_isPersistentConnection = true;	
			return true;			
		}
		
		return false;
	}
	
	/**
	 * Should actually only prepare the sql statement and return a dummy recordset
	 * prepare is left for legacy applications, but should not be used because
	 * too many databases implement prepare differently.
	 *
	 * @param sql	SQL to send to database
	 *
	 * @return return TRUE or FALSE
	 *
	 * @deprecated
	 */	
	function Prepare($sql)
	{
		return $this->Execute($sql);
	}
	
	/**
	 * @return  the last inserted ID. Not all databases support this.
	 */ 
        function Insert_ID()
        {
                if ($this->hasInsertID) return $this->_insertid();
                if ($this->debug) print '<p>Insert_ID error</p>';
                return false;
        }
	
        /**
	 * @return  # rows affected by UPDATE/DELETE
	 */ 
        function Affected_Rows()
        {
                if ($this->hasAffectedRows) {
                       $val = $this->_affectedrows();
                       return ($val < 0) ? false : $val;
                }
                        
                if ($this->debug) print '<p>Affected_Rows error</p>';
                return false;
        }
	
        /**
	 * @return  the last error message
	 */
	function ErrorMsg()
	{
		return '!! '.strtoupper($this->dataProvider.' '.$this->databaseType).': '.$this->_errorMsg;
	}
	
	
	/**
	 * @return the last error number. Normally 0 means no error.
	 */
	function ErrorNo() 
	{
		return ($this->_errorMsg) ? -1 : 0;
	}
	
	/**
	 * Choose a database to connect to. Many databases do not support this.
	 *
	 * @param dbName 	is the name of the database to select
	 * @return 		true or false
	 */
	function SelectDB($dbName) 
	{return false;}
	
	
	/**
	* Will select, getting rows from $offset (1-based), for $nrows. 
	* This simulates the MySQL "select * from table limit $offset,$nrows" , and
	* the PostgreSQL "select * from table limit $nrows offset $offset". Note that
	* MySQL and PostgreSQL parameter ordering is the opposite of the other.
	* eg. 
	*  SelectLimit('select * from table',3); will return rows 1 to 3 (1-based)
	*  SelectLimit('select * from table',3,2); will return rows 3 to 5 (1-based)
	*
	* @param offset		is the row to start calculations from (1-based)
	* @param nrows		is the number of rows to get
	* @return		the recordset ($rs->databaseType == 'array')
 	*/
	function &SelectLimit($sql,$nrows=-1,$offset=-1)
	{
		if ($this->hasTop && $offset <= 0) {
			$sql = eregi_replace('^select','select top '.$nrows,$sql);
			return $this->Execute($sql);
		}
		
		$rs = &$this->Execute($sql);
		if ($rs && !$rs->EOF) {
			$arr = &$rs->GetArrayLimit($nrows,$offset);
			$flds = array();
			for ($i=0, $max=$rs->FieldCount(); $i < $max; $i++)
				$flds[] = &$rs->FetchField($i);
			
			$rs->Close();
			
			$rs = new ADORecordSet_array();
			
			$rs->InitArrayFields($arr,$flds);
		}
		//print_r($rs);
		return $rs;
	}
	
	/**
	 * Execute SQL
	 *
	 * @param sql		SQL statement to execute
	 * @param [inputarr]	holds the input data  to bind to - not used yet
	 * @param [arg3]	reserved for john lim for future use
	 * @return 		RecordSet or false
	 */
	function &Execute($sql,$inputarr=false,$arg3=false) 
	{
		if (!$this->_bindInputArray && $inputarr) {
			$sqlarr = explode("?",$sql);
			$sql = "";
			for ($i=0, $zmax = sizeof($inputarr); $i < $zmax; $i++) {
				$sql .= $sqlarr[$i];
				
				// from Ron Baldwin <ron.baldwin@sourceprose.com>
				// Only quote string types
				if (gettype($inputarr[$i]) == 'string')
					$sql .= "'".$inputarr[$i]."'";
				else
					$sql .= $inputarr[$i];
			}
			$sql .= $sqlarr[$i];
			if ($zmax+1 != sizeof($sqlarr))	print "Input Array does not match ?: ".htmlspecialchars($sql);
			$inputarr = false;
		}
		
		if ($this->debug) {
			print "<hr>($this->databaseType): ".htmlspecialchars($sql)."<hr>";
			$this->_queryID = $this->_query($sql,$inputarr,$arg3);
		} else 
			$this->_queryID =@$this->_query($sql,$inputarr,$arg3);
		
		if ($this->_queryID === false) {
			return false;
		}
		$rsclass = "ADORecordSet_".$this->databaseType;
		
		$rs = new $rsclass($this->_queryID);
		$rs->Init();
		//$this->_insertQuery(&$rs); PHP4 handles closing automatically
		$rs->sql = $sql;
		return $rs;
	}
	
	function BlankRecordSet($id=false)
	{
		$rsclass = "ADORecordSet_".$this->databaseType;
		return new $rsclass($id);
	}
	
 	
	/**
	 * Close Connection
	 */
	function Close() 
	{
		if ($this->_isPersistentConnection != true) $this->_close();
		else return true;	
	}
	
	/**
	 * Begin a Transaction. Must be followed by CommitTrans() or RollbackTrans().
	 *
	 * @return true if succeeded or false if database does not support transactions
	 */
	function BeginTrans() {return false;}
	
	/**
	 * If database does not support transactions, always return true as data always commited
	 *
	 * @return true/false.
	 */
	function CommitTrans() 
	{ return true;}
	
	/**
	 * If database does not support transactions, rollbacks always fail, so return false
	 *
	 * @return true/false.
	 */
	function RollbackTrans() 
	{ return false;}


        /**
	 * return the databases that the driver can connect to. 
	 * Some databases will return an empty array.
	 *
	 * @return an array of database names.
	 */
        function &MetaDatabases() {return false;}
        
	/**
	 * @return  array of tables for current database.
	 */ 
        function &MetaTables() 
	{
		if ($this->metaTablesSQL) {
			$rs = $this->Execute($this->metaTablesSQL);
			if ($rs === false) return false;
			$arr = $rs->GetArray();
			$arr2 = array();
			for ($i=0; $i < sizeof($arr); $i++) {
				$arr2[] = $arr[$i][0];
			}
			$rs->Close();
			return $arr2;
		}
		return false;
	}
	
	/**
	 * List columns in a database as an array of ADODBFieldObjects. 
	 * See top of file for definition of object.
	 *
	 * @params table	table name to query
	 *
	 * @return  array of ADODBFieldObjects for current table.
	 */ 
        function &MetaColumns($table) 
	{
	
		if (!empty($this->metaColumnsSQL)) {
		
			$rs = $this->Execute(sprintf($this->metaColumnsSQL,strtoupper($table)));
			if ($rs === false) return false;

			$retarr = array();
			while (!$rs->EOF) { //print_r($rs->fields);
				$fld = new ADODBFieldObject();
				$fld->name = $rs->fields[0];
				$fld->type = $rs->fields[1];
				$fld->max_length = $rs->fields[2];
				$retarr[strtoupper($fld->name)] = $fld;	
				
				$rs->MoveNext();
			}
			$rs->Close();
			return $retarr;	
		}
		return false;
	}
      
        	
	/**
	 * Different SQL databases used different methods to combine strings together.
	 * This function provides a wrapper. 
	 * 
	 * @param s	variable number of string parameters
	 *
	 * Usage: $db->Concat($str1,$str2);
	 * 
	 * @return concatenated string
	 */ 	 
	function Concat()
	{
		$first = true;
		$s = "";
		$arr = func_get_args();
		$concat = $this->concat_operator;
		foreach($arr as $a) {
			if ($first) {
				$s = (string) $a;
				$first = false;
			} else $s .= $concat.$a;
		}
		return $s;
	}
	
	/**
	 * Converts a date "d" to a string that the database can understand.
	 *
	 * @param d	a date in Unix date time format.
	 *
	 * @return  date string in database date format
	 */
	function DBDate($d)
	{
	// note that we are limited to 1970 to 2038
		return date($this->fmtDate,$d);
	}
	
	/**
	 * Converts a timestamp "ts" to a string that the database can understand.
	 *
	 * @param ts	a timestamp in Unix date time format.
	 *
	 * @return  timestamp string in database timestamp format
	 */
	function DBTimeStamp($ts)
	{
		return date($this->fmtTimeStamp,$ts);
	}
	
	/**
	 * Converts a timestamp "ts" to a string that the database can understand.
	 * An example is  $db->qstr("Don't bother",magic_quotes_runtime());
	 * 
	 * @param s			the string to quote
	 * @param [magic_quotes]	if $s is GET/POST var, set to get_magic_quotes_gpc().
	 *				This undoes the stupidity of magic quotes for GPC.
	 *
	 * @return  quoted string to be sent back to database
	 */
	function qstr($s,$magic_quotes=false)
	{	
	$nofixquotes=false;
		if (!$magic_quotes) return  "'".str_replace("'",$this->replaceQuote,$s)."'";
		
		// undo magic quotes for "
		$s = str_replace('\\"','"',$s);
		
		if ($this->replaceQuote == "\\'")  // ' already quoted, no need to change anything
			return "'$s'";
		else // change \' to '' for sybase/mssql
			return "'".str_replace("\\'",$this->replaceQuote,$s)."'";
	}
	
	} // end class ADODB
	
	
	
	//==============================================================================================	
	
	/**
	* Used by ADORecordSet->FetchObj()
	*/
	class ADOFetchObj {
	};
	
	/**
	 * RecordSet class that represents the dataset returned by the database.
	 * To keep memory overhead low, this class holds only the current row in memory.
	 * No prefetching of data is done, so the RecordCount() can return -1 (not known).
	 */
	class ADORecordSet {
	/*
	 * public variables	
	 */
	var $dataProvider = "native";
	var $fields = false; // holds the current row data
	var $blobSize = 64; 	// any varchar/char field this size or greater is treated as a blob
				// in other words, we use a text area for editting.
	var $canSeek = false; 	// indicates that seek is supported
	var $sql; 		// sql text
	var $EOF = false;	/* Indicates that the current record position is after the last record in a Recordset object. */
	
	var $emptyTimeStamp = '&nbsp;'; // what to display when $time==0
	var $emptyDate = '&nbsp;'; // what to display when $time==0
	var $debug = false;
	/*
	 *	private variables	
	 */
	var $_numOfRows = -1;	
	var $_numOfFields = -1;	
	var $_queryID = -1;	/* This variable keeps the result link identifier.	*/
	var $_currentRow = -1;	/* This variable keeps the current row in the Recordset.	*/
	var $_closed = false; 	/* has recordset been closed */
	var $_inited = false; 	/* Init() should only be called once */
	var $_obj; 		/* Used by FetchObj */
	var $_names;
	/**
	 * Constructor
	 *
	 * @param queryID  	this is the queryID returned by ADOConnection->_query()
	 *
	 */
	function ADORecordSet(&$queryID) 
	{
		$this->_queryID = $queryID;
	}
	
	function Init()
	{
		if ($this->_inited) return;
		$this->_inited = true;
		
		if ($this->_queryID) @$this->_initrs();
		else {
			$this->_numOfRows = 0;
			$this->_numOfFields = 0;
		}
		if ($this->_numOfRows != 0 && $this->_numOfFields && $this->_currentRow == -1) {
			$this->_currentRow = 0;
			$this->EOF = ($this->_fetch() === false);
		} else 
			$this->EOF = true;
	}
	/**
	 * Generate a <SELECT> string from a recordset, and return the string
	 *
	 * @param name  		name of <SELECT>
	 * @param [defstr]		the value to hilite
	 * @param [firstItem]	        HTML <OPTION> text to enter in first item, or empty
	 * @param [multiple]		true for listbox, false for popup
	 * @param [size]		#rows to show for listbox. not used by popup
	 * @param [selectAttr]		additional attributes to defined for <SELECT>.
	 *				useful for holding javascript onChange='...' handlers.
	 *
	 * @return HTML
	 */
	function GetMenu($name,$defstr='',$firstItem='',$multiple=false,$size=0, $selectAttr='')
	{
		$hasvalue = false;

		if ($multiple) {
			if ($size==0) $size=5;
			$attr = " multiple size=$size";
			if (!strpos($name,'[]')) $name .= '[]';
		} else if ($size) $attr = " size=$size";
		else $attr ='';
		
		$s = "<select class=\"data\" name=\"$name\"$attr $selectAttr>";
		if ('' != $firstItem) {
			$s .= "\n$firstItem";
		}
		if ($this->FieldCount() > 1) $hasvalue=true;
		while(!$this->EOF) {
			$zval = trim($this->fields[0]);
			if (($firstItem!='') && ($zval=="")) {
				$this->MoveNext();
				continue;
			}
			if ($hasvalue) {
				$value = 'value="'.htmlspecialchars(trim($this->fields[1])).'"';
			}
			
			if (strcasecmp($zval,$defstr)==0) $s .= "<option selected $value>".htmlspecialchars($zval);
			else $s .= "\n<option ".$value.'>'.htmlspecialchars($zval).'</option>';
			$this->MoveNext();
		}
		
		return $s ."\n</select>\n";
	}
	/**
	 * return recordset as a 2-dimensional array.
	 *
	 * @param [nRows]  is the number of rows to return. -1 means every row.
	 *
	 * @return an array indexed by the rows (0-based) from the recordset
	 */
	function &GetArray($nRows = -1) 
	{
		$results = array();
		$cnt = 0;
		while (!$this->EOF && $nRows != $cnt) {
			$results[$cnt++] = $this->fields;
			$this->MoveNext();
		}
		
		return $results;
	}
	/**
	 * return recordset as a 2-dimensional array. 
	 * Helper function for ADODBConnection->SelectLimit()
	 *
	 * @param offset	is the row to start calculations from (1-based)
	 * @param [nrows]	is the number of rows to return
	 *
	 * @return an array indexed by the rows (0-based) from the recordset
	 */
	function &GetArrayLimit($nrows,$offset=-1) 
	{
		if ($offset <= -1) return $this->GetArray($nrows);
		$this->Move($offset);
		
		$results = array();
		$cnt = 0;
		while (!$this->EOF && $nrows != $cnt) {
			$results[$cnt++] = $this->fields;
			$this->MoveNext();
		}
		
		return $results;
	}
	
	/**
	 * Synonym for GetArray() for compatibility with ADO.
	 *
	 * @param [nRows]  is the number of rows to return. -1 means every row.
	 *
	 * @return an array indexed by the rows (0-based) from the recordset
	 */
	function &GetRows($nRows = -1) 
	{
		return $this->GetArray($nRows);
	}
	
	/**
	 * return whole recordset as a 2-dimensional associative array if there are more than 2 columns. 
	 * The first column is treated as the key and is not included in the array. 
	 * If there is only 2 columns, it will return a 1 dimensional array of key-value pairs unless
	 * $force_array == true.
	 *
	 * @param [force_array] has only meaning if we have 2 data columns. If false, a 1 dimensional
	 * 	array is returned, otherwise a 2 dimensional array is returned. If this sounds confusing,
	 * 	read the source.
	 *
	 * @return an associative array indexed by the first column of the array, 
	 * 	or false if the  data has less than 2 cols.
	 */
	function &GetAssoc($force_array = false) {
		$cols = $this->_numOfFields;
		if ($cols < 2) {
			return false;
		}
		$results = array();
		if ($cols > 2 || $force_array) {
			while (!$this->EOF) {
				$results[trim($this->fields[0])] = array_slice($this->fields, 1);
				$this->MoveNext();
			}
		} else {
			// return scalar values
			while (!$this->EOF) {
			// some bug in mssql PHP 4.02 -- doesn't handle references properly so we FORCE creating a new string
				$val = ''.$this->fields[1]; 
				$results[trim($this->fields[0])] = $val;
				$this->MoveNext();
			}
		}
		return $results; 
	}
	
	/**
	 *
	 * @param v  	is the character timestamp in YYYY-MM-DD hh:mm:ss format
	 * @param fmt 	is the format to apply to it, using date()
	 *
	 * @return a timestamp formated as user desires
	 */
	function UserTimeStamp($v,$fmt='Y-m-d H:i:s')
	{
		$tt = $this->UnixTimeStamp($v);
		// $tt == -1 if pre 1970
		if (($tt === false || $tt == -1) && $v != false) return $v;
		if ($tt == 0) return $this->emptyTimeStamp;
		
		return date($fmt,$tt);
	}
	
       /**
	 * @param v  	is the character date in YYYY-MM-DD format
	 * @param fmt 	is the format to apply to it, using date()
	 *
	 * @return a date formated as user desires
	 */
	function UserDate($v,$fmt='Y-m-d')
	{
		$tt = $this->UnixDate($v);
		// $tt == -1 if pre 1970
		if (($tt === false || $tt == -1) && $v != false) return $v;
		else if ($tt == 0) return $this->emptyDate;
		else if ($tt == -1) { // pre-1970
		}
		return date($fmt,$tt);
	
	}
	
	/**
	 * @param $v is a date string in YYYY-MM-DD format
	 *
	 * @return date in unix timestamp format, or 0 if before 1970, or false if invalid date format
	 */
	function UnixDate($v)
	{
		if (!ereg( "([0-9]{4})[-/\.]?([0-9]{1,2})[-/\.]?([0-9]{1,2})", 
			$v, $rr)) return false;
			
		if ($rr[1] <= 1970) return 0;
		// h-m-s-MM-DD-YY
		return mktime(0,0,0,$rr[2],$rr[3],$rr[1]);
	}
	

	
	/**
	 * @param $v is a timestamp string in YYYY-MM-DD HH-NN-SS format
	 *
	 * @return date in unix timestamp format, or 0 if before 1970, or false if invalid date format
	 */
	function UnixTimeStamp($v)
	{
		if (!ereg( "([0-9]{4})[-/\.]([0-9]{1,2})[-/\.]?([0-9]{1,2}) ?([0-9]{1,2}):?([0-9]{1,2}):?([0-9]{1,2})", 
			$v, $rr)) return false;
		
		if ($rr[1] <= 1970 && $rr[2]<= 1) return 0;
		// h-m-s-MM-DD-YY
		return  mktime($rr[4],$rr[5],$rr[6],$rr[2],$rr[3],$rr[1]);
	}
	
	
	/**
	 * Move to the first row in the recordset. Many databases do NOT support this.
	 *
	 * @return true or false
	 */
	function MoveFirst() 
	{
		if ($this->_currentRow == 0) return true;
		return $this->Move(0);			
	}			

	/**
	 * Move to the last row in the recordset. 
	 *
	 * @return true or false
	 */
	function MoveLast() 
	{
		if ($this->_numOfRows >= 0) return $this->Move($this->_numOfRows-1);
                while (!$this->EOF) $this->MoveNext();
		return true;
	}
	
	/**
	 * Move to next record in the recordset.
	 *
	 * @return true if there still rows available, or false if there are no more rows (EOF).
	 */
	function MoveNext($ignore_fields=false) 
	{
		if ($this->_numOfRows != 0 && !$this->EOF) {		
			$this->_currentRow++;
			if ($this->_fetch($ignore_fields)) return true;
		}
		$this->EOF = true;
		return false;
	}	
	
	/**
	 * Random access to a specific row in the recordset. Some databases do not support
	 * access to previous rows in the databases (no scrolling backwards).
	 *
	 * @param rowNumber is the row to move to (0-based)
	 *
	 * @return true if there still rows available, or false if there are no more rows (EOF).
	 */
	function Move($rowNumber = 0) 
	{
		if ($rowNumber == $this->_currentRow) return true;
                
                if ($rowNumber > $this->_numOfRows)
                        if ($this->_numOfRows != -1)
                                $rowNumber = $this->_numOfRows-1;
   
                   if ($this->canSeek) {
                        if ($this->_seek($rowNumber)) {
				$this->_currentRow = $rowNumber;
				if ($this->_fetch()) {
					$this->EOF = false;	
                                      //  $this->_currentRow += 1;			
					return true;
				}
			} else 
				return false;
                } else {
                        if ($rowNumber < $this->_currentRow) return false;
                        while (! $this->EOF && $this->_currentRow < $rowNumber) {
				$this->_currentRow++;
                                if (!$this->_fetch()) $this->EOF = true;
			}
                        if ($this->EOF) return false;
                        return true;
                }
		
		$this->fields = null;	
		$this->EOF = true;
		return false;
	}
		
	/**
	 * Get the value of a field in the current row by column name.
	 * 
	 * @param colname  is the field to access
	 *
	 * @return the value of $colname column
	 */
	function Fields($colname)
	{
		return $this->fields[$colname];
	}
	
	/**
	 * Clean up 
	 *
	 * @return true or false
	 */
	function Close() 
	{
		if (!$this->_closed) {
			$this->_closed = true;
			return $this->_close();		
		} else
			return true;
	}
	
	/**
	 * synonyms RecordCount and RowCount	
	 *
	 * @return the number of rows or -1 if this is not supported
	 */
	function RecordCount() {return $this->_numOfRows;}
	
	/**
	 * synonyms RecordCount and RowCount	
	 *
	 * @return the number of rows or -1 if this is not supported
	 */
	function RowCount() {return $this->_numOfRows;} 
	
	
	/**
	 * @return the current row in the recordset. If at EOF, will return the last row. 0-based.
	 */
	function CurrentRow() {return $this->_currentRow;}
	
	/**
	 * synonym for CurrentRow -- for ADO compat
	 *
	 * @return the current row in the recordset. If at EOF, will return the last row. 0-based.
	 */
	function AbsolutePosition() {return $this->_currentRow;}
	
	/**
	 * @return the number of columns in the recordset. Some databases will set this to 0
	 * if no records are returned, others will return the number of columns in the query.
	 */
	function FieldCount() {return $this->_numOfFields;}   


	/**
	 * Get the ADOFieldObject of a specific column.
	 *
	 * @param fieldoffset	is the column position to access(0-based).
	 *
	 * @return the ADOFieldObject for that column, or false.
	 */
	function &FetchField($fieldoffset) 
	{
		// must be defined by child class
	}	
	
	/**
	* Return the fields array of the current row as an object for convenience.
	* 
	* @param $isupper to set the object property names to uppercase
	*
	* @return the object with the properties set to the fields of the current row
	*/
	function &FetchObject($isupper=true)
	{
		if (empty($this->_obj)) {
			$this->_obj = new ADOFetchObj();
			$this->_names = array();
			for ($i=0; $i <$this->_numOfFields; $i++) {
				$f = $this->FetchField($i);
				$this->_names[] = ($isupper) ? strtoupper($f->name) : $f->name;
			}
		}
		$i = 0;
		$o = &$this->_obj;
		foreach ($this->fields as $v) {
			$name = $this->_names[$i];
			$o->$name = $v;
			$i += 1;
		}
		return $o;
	}
	
	/**
	 * Get the metatype of the column. This is used for formatting. This is because
	 * many databases use different names for the same type, so we transform the original
	 * type to our standardised version which uses 1 character codes:
	 *
	 * @param t  is the type passed in. Normally is ADOFieldObject->type.
	 * @param len is the maximum length of that field. This is because we treat character
	 * 	fields bigger than a certain size as a 'B' (blob).
	 * @param fieldobj is the field object returned by the database driver. Can hold
	 *	additional info (eg. primary_key for mysql).
	 * 
	 * @return the general type of the data: 
	 *	C for character < 200 chars
	 *	X for teXt (>= 200 chars)
	 *	B for Binary
	 * 	N for numeric floating point
	 *	D for date
	 *	T for timestamp
	 * 	L for logical/Boolean
	 *	I for integer
	 *	R for autoincrement counter/integer
	 * 
	 *
	*/
	function MetaType($t,$len=-1,$fieldobj=false)
	{
		switch (strtoupper($t)) {
		case 'VARCHAR':
		case 'CHAR':
		case 'STRING':
		case 'C':
		case 'NCHAR':
		case 'NVARCHAR':
			if ($len <= $this->blobSize) return 'C';
		
		case 'LONGCHAR':
		case 'TEXT':
		case 'M':
			return 'X';
		
		case 'B':
		case 'BLOB':
		case 'NTEXT':
		case 'BINARY':
		case 'VARBINARY':
		case 'LONGBINARY':
			return 'B';
			
		case 'DATE':
		case 'D':
			return 'D';
		
		
		case 'TIME':
		case 'TIMESTAMP':
		case 'DATETIME':
		case 'T':
			return 'T';
		
		case 'BOOLEAN': 
		case 'BIT':
		case 'L':
			return 'L';
			
		case 'COUNTER':
			return 'R';
			
		case 'INT':
		case 'INTEGER':
		case 'SHORT':
		case 'TINYINT':
		case 'SMALLINT':
		case 'I':
			return 'I';
			
		default: return 'N';
		}
	}
	} // end class ADORecordSet
	
	//==============================================================================================	
	
	/**
	 * This class encapsulates the concept of a recordset created in memory
	 * as an array. This is useful for the creation of cached recordsets.
	 * 
	 * Note that the constructor is different from the standard.
	 */
	
	class ADORecordSet_array extends ADORecordSet
	{
		var $databaseType = "array";
	
		var $_array; 	// holds the 2-dimensional data array
		var $_types;	// the array of types of each column (C B I L M)
		var $_colnames;	// names of each column in array
		var $_skiprow1;	// skip 1st row because it holds column names
		var $_fieldarr; // holds array of field objects
		var $canSeek = true;
		
		/**
		 * Constructor
		 *
		 */
		function ADORecordSet_array($fakeid=1)
		{
			$this->ADORecordSet($fakeid); // fake queryID
		}
		/**
		 * Setup the Array. Later we will have XML-Data and CSV handlers
		 *
		 * @param array		is a 2-dimensional array holding the data.
		 *			The first row should hold the column names 
		 *			unless paramter $colnames is used.
		 * @param typearr	holds an array of types. These are the same types 
		 *			used in MetaTypes (C,B,L,I,N).
		 * @param [colnames]	array of column names. If set, then the first row of
		 *			$array should not hold the column names.
		 */
		function InitArray(&$array,$typearr,$colnames=false)
		{
			$this->_array = $array;
			$this->_types = &$typearr;	
			if ($colnames) {
				$this->_skiprow1 = false;
				$this->_colnames = $colnames;
			} else $this->_colnames = $array[0];
			
			$this->Init();
		}
		/**
		 * Setup the Array and datatype file objects
		 *
		 * @param array		is a 2-dimensional array holding the data.
		 *			The first row should hold the column names 
		 *			unless paramter $colnames is used.
		 * @param fieldarr	holds an array of ADODBFieldObject's.
		 */
		function InitArrayFields(&$array,&$fieldarr)
		{
			$this->_array = $array;
			$this->_skiprow1= false;
			if ($fieldarr) {
				$this->_fieldobjects = &$fieldarr;
			} 
			
			$this->Init();
		}
		
		function _initrs()
		{
			$this->_numOfRows =  sizeof($this->_array);
			if ($this->_skiprow1) $this->_numOfRows -= 1;
		
			$this->_numOfFields =(isset($this->_fieldobjects)) ?
				 sizeof($this->_fieldobjects):sizeof($this->_types);
		}
		
	
		function &FetchField($fieldOffset = -1) 
		{
			if (isset($this->_fieldobjects)) {
				return $this->_fieldobjects[$fieldOffset];
			}
			$o =  new ADODBFieldObject();
			$o->name = $this->_colnames[$fieldOffset];
			$o->type =  $this->_types[$fieldOffset];
			$o->max_length = -1; // length not known
			
			return $o;
		}
			
		function _seek($row)
		{
			return true;
		}
		
		function _fetch($ignore_fields=false)
		{
			$pos = $this->_currentRow;
			
			if ($this->_skiprow1) {
				if ($this->_numOfRows <= $pos-1) return false;
				$pos += 1;
			} else {
				if ($this->_numOfRows <= $pos) return false;
			}
			
			$this->fields = $this->_array[$pos];
			return true;
		}
		
		function _close() 
		{
			return true;	
		}
		
		
	
	} // ADORecordSet_array

	//==============================================================================================	
	// HELPER FUNCTIONS
	//==============================================================================================			
	
        /**
	 * Synonym for ADOLoadCode.
	 *
	 * @deprecated
	 */
	function ADOLoadDB($dbType) { return ADOLoadCode($dbType);}
        
        /**
	 * Load the code for a specific database driver
	 */
        /* NF - SCRIPT MODIFIED TO HANDLE FILE NOT FOUND ERROR */

        function ADOLoadCode($dbType) 
	{
	GLOBAL $ADODB_DIR, $ADODB_Database;
	
		if (!$dbType)
                  return false;
		$ADODB_Database = strtolower($dbType);
		if ($ADODB_Database == 'maxsql')
                  $ADODB_Database = 'mysqlt';
		if (!(include_once("$ADODB_DIR/adodb-$ADODB_Database.inc.php")))
                  return false;
		return true;		
	}

	/**
	 * synonym for ADONewConnection for people who cannot remember the correct name
	 */
	function &NewADOConnection($db='')
	{
		return ADONewConnection($db);
	}
	
	/**
	 * Instantiate a new Connection class for a specific database driver.
	 *
	 * @param [db]  is the database Connection object to create. If undefined,
	 * 	use the last database driver that was loaded by ADOLoadCode().
	 *
	 * @return the freshly created instance of the Connection class.
	 */
	function &ADONewConnection($db='')
	{
	GLOBAL $ADODB_Database;
		if (!$db) $db = $ADODB_Database;
		
		$cls = 'ADODB_'.$db;
		return new $cls();
	}

} // defined
?>
