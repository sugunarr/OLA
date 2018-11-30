<?php
/*
V0.95 13 Mar 2001 (c) 2000, 2001 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under Lesser GPL library license. See License.txt.
  Set tabs to 8.
  
  MySQL code that does not support transactions. Use mysqlt if you need transactions.
  Requires mysql client. Works on Windows and Unix.
  
 28 Feb 2001: MetaColumns bug fix - suggested by  Freek Dijkstra (phpeverywhere@macfreek.com)
*/ 

if (! defined("_ADODB_MYSQL_LAYER")) {
 define("_ADODB_MYSQL_LAYER", 1 );

class ADODB_mysql extends ADODBConnection {
	var $databaseType = 'mysql';
        var $hasInsertID = true;
        var $hasAffectedRows = true;	
	var $metaTablesSQL = "SHOW TABLES";	
	var $metaColumnsSQL = "SHOW COLUMNS FROM %s";
	var $fmtTimeStamp = "'Y-m-d H:i:s'";
	var $hasLimit = true;
	
	function ADODB_mysql() 
	{			
	}
	
        function _insertid()
        {
                return mysql_insert_id($this->_connectionID);
        }
        
        function _affectedrows()
        {
                return mysql_affected_rows($this->_connectionID);
        }
  
  	function &MetaDatabases()
	{
		$qid = mysql_list_dbs($this->_connectionID);
		$arr = array();
		$i = 0;
		$max = mysql_num_rows($qid);
		while ($i < $max) {
			$arr[] = mysql_tablename($qid,$i);
			$i += 1;
		}
		return $arr;
	}

	// returns concatenated string
	function Concat()
	{
		$s = "";
		$arr = func_get_args();
		$first = true;

		foreach($arr as $a) {
			if ($first) {
				$s = $a;
				$first = false;
			} else $s .= ','.$a;
		}
		if (sizeof($s) > 0) return "CONCAT($s)";
		else return '';
	}
	
	// returns true or false
	function _connect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		$this->_connectionID = mysql_connect($argHostname,$argUsername,$argPassword);
		if ($this->_connectionID === false) return false;
		if ($argDatabasename) return $this->SelectDB($argDatabasename);
		return true;	
	}
	
	// returns true or false
	function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
		$this->_connectionID = mysql_pconnect($argHostname,$argUsername,$argPassword);
		if ($this->_connectionID === false) return false;
		if ($argDatabasename) return $this->SelectDB($argDatabasename);
		return true;	
	}
	
 	function &MetaColumns($table) 
	{
		if ($this->metaColumnsSQL) {
		
			$rs = $this->Execute(sprintf($this->metaColumnsSQL,$table));
			
			if ($rs === false) return false;
			
			$retarr = array();
			while (!$rs->EOF){
				$fld = new ADODBFieldObject();
				$fld->name = $rs->fields[0];
				$fld->type = $rs->fields[1];
					
				// split type into type(length):
				if (preg_match("/^(.+)\((\d+)\)$/", $fld->type, $query_array)) {
					$fld->type = $query_array[1];
					$fld->max_length = $query_array[2];
				} else {
					$fld->max_length = -1;
				}
				$fld->not_null = ($rs->fields[2] != 'YES');
				$fld->primary_key = ($rs->fields[3] == 'PRI');
				$fld->auto_increment = (strpos($rs->fields[5], 'auto_increment') !== false);
				$fld->binary = (strpos($fld->type,'blob') !== false);
				
				$retarr[strtoupper($fld->name)] = $fld;	
				$rs->MoveNext();
			}
			$rs->Close();
			return $retarr;	
		}
		return false;
	}
		
	// returns true or false
	function SelectDB($dbName) 
	{
		$this->databaseName = $dbName;
		if ($this->_connectionID) {
			return @mysql_select_db($dbName,$this->_connectionID);		
		}
		else return false;	
	}
	
	// parameters use PostgreSQL convention, not MySQL
	function &SelectLimit($sql,$nrows=-1,$offset=-1)
	{
		$offsetStr =($offset>=0) ? "$offset," : '';
		
		return $this->Execute($sql." LIMIT $offsetStr$nrows");
		
	}
	
	// returns queryID or false
	function _query($sql,$inputarr)
	{
		return mysql_query($sql,$this->_connectionID);
	}

	/*	Returns: the last error message from previous database operation	*/	
	function ErrorMsg() 
	{
		$this->_errorMsg = @mysql_error($this->_connectionID);
	    	return $this->_errorMsg;
	}
	
	/*	Returns: the last error number from previous database operation	*/	
	function ErrorNo() 
	{
		return @mysql_errno($this->_connectionID);
	}
	
	// returns true or false
	function _close()
	{
		return @mysql_close($this->_connectionID);
	}
		
}
	
/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordSet_mysql extends ADORecordSet{	
	
	var $databaseType = "mysql";
	var $canSeek = true;
	
	function ADORecordSet_mysql($queryID) {
		return $this->ADORecordSet($queryID);
	}
	
	function _initrs()
	{
	GLOBAL $ADODB_COUNTRECS;
		$this->_numOfRows = ($ADODB_COUNTRECS) ? @mysql_num_rows($this->_queryID):-1;
		$this->_numOfFields = @mysql_num_fields($this->_queryID);
	}
	


	function &FetchField($fieldOffset = -1) {
		if ($fieldOffset != -1) {
			$o =  @mysql_fetch_field($this->_queryID, $fieldOffset);
			$o->max_length = -1; // mysql returns the max length less spaces -- so it is unrealiable
			$f = @mysql_field_flags($this->_queryID,$fieldOffset);
			$o->binary = (strpos($f,'binary')!== false);
		}
		else if ($fieldOffset == -1) {	/*	The $fieldOffset argument is not provided thus its -1 	*/
			$o = @mysql_fetch_field($this->_queryID);// mysql returns the max length less spaces -- so it is unrealiable
			$o->max_length = -1;
		}
		
		return $o;
	}
		
	function _seek($row)
	{
		return @mysql_data_seek($this->_queryID,$row);
	}
	
	function _fetch($ignore_fields=false)
	{
		$this->fields = @mysql_fetch_array($this->_queryID);
		return ($this->fields == true);
	}
	
	function _close() {
		return @mysql_free_result($this->_queryID);		
	}
	
	function MetaType($t,$len=-1,$fieldobj=false)
	{
		$len = -1; // mysql max_length is not accurate
		switch (strtoupper($t)) {
		case 'STRING': 
		case 'CHAR':
		case 'VARCHAR': 
		case 'TINYBLOB': 
		case 'TINYTEXT': 
		case 'ENUM': 
		case 'SET': 
			if ($len <= $this->blobSize) return 'C';
			
		case 'TEXT':
		case 'LONGTEXT': 
		case 'MEDIUMTEXT':
			return 'X';
			
		// php_mysql extension always returns 'blob' even if 'text'
		// so we have to check whether binary...
		case 'IMAGE':
		case 'LONGBLOB': 
		case 'BLOB':
		case 'MEDIUMBLOB':
			return !empty($fieldobj->binary) ? 'B' : 'X';
			
		case 'DATE': return 'D';
		
		case 'DATETIME':
		case 'TIMESTAMP': return 'T';
		
		case 'INT': 
		case 'INTEGER':
		case 'BIGINT':
		case 'TINYINT':
		case 'MEDIUMINT':
		case 'SMALLINT': 
			
			if (!empty($fieldobj->primary_key)) return 'R';
			else return 'I';
		
		default: return 'N';
		}
	}

}
}
?>