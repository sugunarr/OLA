<?php
/* 
V0.95 13 Mar 2001 (c) 2000, 2001 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under Lesser GPL library license. See License.txt. 
Set tabs to 8 for best viewing.
  
  Latest version is available at http://php.weblogs.com/
  
  Requires ODBC. Works on Windows and Unix.
*/
  define("_ADODB_ODBC_LAYER", 1 );
	 
/*--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------*/

  
class ADODB_odbc extends ADODBConnection {
	var $databaseType = "odbc";	
	var $fmtDate = "'Y-m-d'";
	var $fmtTimeStamp = "'Y-m-d, h:i:sA'";
	var $replaceQuote = "''"; // string to use to replace quotes
	var $dataProvider = "odbc";
	var $hasAffectedRows = true;
	var $binmode = ODBC_BINMODE_RETURN;
	//var $longreadlen = 8000; // default number of chars to return for a Blob/Long field
	var $_bindInputArray = true;      
	var $_haserrorfunctions = false;
	
	function ADODB_odbc() 
	{ 	
		$v = explode('.',PHP_VERSION);
		$this->haserrorfunctions = ((integer)$v[2])>4; // > PHP 4.0.4
	}

	function ErrorMsg()
	{
		if ($this->haserrorfunctions) return odbc_errormsg($this->_connectionID);
		else return parent::ErrorMsg();
	}
	function ErrorNo()
	{
		if ($this->haserrorfunctions) return odbc_error($this->_connectionID);
		else return parent::ErrorNo();
	}
	
	
	// returns true or false
	function _connect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
	global $php_errormsg;
	
		$php_errormsg = '';
		$this->_connectionID = odbc_connect($argHostname,$argUsername,$argPassword);
		$this->_errorMsg = $php_errormsg;

		//if ($this->_connectionID) odbc_autocommit($this->_connectionID,true);
		return $this->_connectionID != false;
	}
	// returns true or false
	function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
	{
	global $php_errormsg;
		$php_errormsg = '';
		$this->_connectionID = odbc_pconnect($argHostname,$argUsername,$argPassword);
		$this->_errorMsg = $php_errormsg;
		
		//if ($this->_connectionID) odbc_autocommit($this->_connectionID,true);
		return $this->_connectionID != false;
	}

	function BeginTrans()
	{       
                return odbc_autocommit($this->_connectionID,false);
	}
	
	function CommitTrans()
	{
                $ret = odbc_commit($this->_connectionID);
		odbc_autocommit($this->_connectionID,true);
		return $ret;
	}
	
	function RollbackTrans()
	{
                $ret = odbc_rollback($this->_connectionID);
		odbc_autocommit($this->_connectionID,true);
		return $ret;
	}
	
	/*	returns true or false */
	function SelectDB($dbName) {
		/*	No database selected	*/
		return false;	
	}
	
	function &MetaDatabases()
	{
		$arr = array();
		return $arr;
	}
	
	function &MetaTables()
	{
		$qid = odbc_tables($this->_connectionID);
		$rs = new ADORecordSet_odbc($qid);
		//print_r($rs);
		$arr = &$rs->GetArray();
		
		$arr2 = array();
		for ($i=0; $i < sizeof($arr); $i++) {
			if ($arr[$i][2]) $arr2[] = $arr[$i][2];
		}
		return $arr2;
	}
	
/*
/ SQL data type codes /
#define	SQL_UNKNOWN_TYPE	0
#define SQL_CHAR            1
#define SQL_NUMERIC         2
#define SQL_DECIMAL         3
#define SQL_INTEGER         4
#define SQL_SMALLINT        5
#define SQL_FLOAT           6
#define SQL_REAL            7
#define SQL_DOUBLE          8
#if (ODBCVER >= 0x0300)
#define SQL_DATETIME        9
#endif
#define SQL_VARCHAR        12

/ One-parameter shortcuts for date/time data types /
#if (ODBCVER >= 0x0300)
#define SQL_TYPE_DATE      91
#define SQL_TYPE_TIME      92
#define SQL_TYPE_TIMESTAMP 93
*/
	function ODBCTypes($t)
	{
		switch ((integer)$t) {
		case 1:	
		case 12:
		case 0:
			return 'C';
		case -1: //text
			return 'X';
		case -4: //image
			return 'B';
				
		case 91:
		case 11:
			return 'D';
			
		case 92:
		case 93:
		case 9:	return 'T';
		case 4:
		case 5:
		case -6:
			return 'I';
			
		case -11: // uniqidentifier
			return 'R';
		case -7: //bit
			return 'L';
		
		default:
			return 'N';
		}
	}
	
	function &MetaColumns($table)
	{
		$table = strtoupper($table);
		
	/* // for some reason, cannot view only 1 table with odbc_columns -- bug?
		$qid = odbc_tables($this->_connectionID);
		$rs = new ADORecordSet_odbc($qid);
		if (!$rs) return false;
		while (!$rs->EOF) {
			if ($table == strtoupper($rs->fields[2])) {
				$q = $rs->fields[0];
				$o = $rs->fields[1];
				break;
			}
			$rs->MoveNext();
		}
		$rs->Close();
		
		$qid = odbc_columns($this->_connectionID,$q,$o,strtoupper($table),'%');
	*/
		$qid = odbc_columns($this->_connectionID);
		$rs = new ADORecordSet_odbc($qid);
		if (!$rs) return false;
		
		$retarr = array();
		while (!$rs->EOF) {
			if (strtoupper($rs->fields[2]) == $table) {
				$fld = new ADODBFieldObject();
				$fld->name = $rs->fields[3];
				$fld->type = $this->ODBCTypes($rs->fields[4]);
				$fld->max_length = $rs->fields[7];
				$retarr[strtoupper($fld->name)] = $fld;	
			} else if (sizeof($retarr)>0)
				break;
			$rs->MoveNext();
		}
		//$rs->Close(); -- crashes 4.03pl1 -- why?
		
		return $retarr;
	}
		

	/* returns queryID or false */
	function _query($sql,$inputarr=false) 
	{
	GLOBAL $php_errormsg;
		$php_errormsg = '';
		$this->_error = '';
		if ($inputarr) {
			$stmtid = odbc_prepare($this->_connectionID,$sql);
			if ($stmtid == false) {
				$this->_errorMsg = $php_errormsg;
				return false;
			}
			//print_r($inputarr);
			$rez = odbc_execute($stmtid,$inputarr);
		} else
			$rez = odbc_exec($this->_connectionID,$sql);
		
		if ($rez) {
			odbc_binmode($rez,$this->binmode);
			odbc_longreadlen($rez,$this->maxblobsize);
		}
		$this->_errorMsg = $php_errormsg;
		return $rez;
	}


	// returns true or false
	function _close()
	{
		return @odbc_close($this->_connectionID);
	}
	
        function _affectedrows()
        {
                return  odbc_num_rows($this->_queryID);
        }
	
}
	
/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordSet_odbc extends ADORecordSet {	
	
	var $bind = false;
	var $databaseType = "odbc";		
	var $dataProvider = "odbc";
	
	function ADORecordSet_odbc($id)
	{
		return $this->ADORecordSet($id);
	}


	// returns the field object
	function &FetchField($fieldOffset = -1) {
		
		$off=$fieldOffset+1; // offsets begin at 1
		
		$o= new ADODBFieldObject();
		$o->name = @odbc_field_name($this->_queryID,$off);
		$o->type = @odbc_field_type($this->_queryID,$off);
		$o->max_length = @odbc_field_len($this->_queryID,$off);
		
		return $o;
	}
	
	/* Use associative array to get fields array */
	function Fields($colname)
	{
		if (!$this->bind) {
			$this->bind = array();
			for ($i=0; $i < $this->_numOfFields; $i++) {
				$o = $this->FetchField($i);
				$this->bind[strtoupper($o->name)] = $i;
			}
		}
		
		 return $this->fields[$this->bind[strtoupper($colname)]];
		
	}
	
		
	function _initrs()
	{
		$this->_numOfRows = @odbc_num_rows($this->_queryID);
		$this->_numOfFields = @odbc_num_fields($this->_queryID);
	}	
	
	function _seek($row)
	{
		return false;
	}
	
	function _fetch($ignore_fields=false)
	{
		return odbc_fetch_into($this->_queryID,0,$this->fields);
	}
	
	function _close() {
		
		return @odbc_free_result($this->_queryID);		
	}
	


}

?>