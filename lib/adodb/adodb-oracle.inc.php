<?php
/*
V0.95 13 Mar 2001 (c) 2000, 2001 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under Lesser GPL library license. See License.txt.

  Latest version is available at http://php.weblogs.com/
  
  Oracle data driver. Requires Oracle client. Works on Windows and Unix and Oracle 7 and 8.
  
  If you are using Oracle 8, use the oci8 driver as ErrorMsg() and ErrorNo() work properly,
  
*/

// select table_name from cat -- MetaTables
// 
class ADODB_oracle extends ADODBConnection {
        var $databaseType = "oracle";
        var $replaceQuote = "\'"; // string to use to replace quotes
        var $concat_operator='||';
	var $_curs;
	var $_initdate = true; // init date to YYYY-MM-DD
	var $metaTablesSQL = 'select table_name from cat';	
	var $metaColumnsSQL = "select cname,coltype,width from col where tname='%s'";
        function ADODB_oracle() {
        }

	// format and return date string in database date format
	function DBDate($d)
	{
		return 'TO_DATE('.date($this->fmtDate,$d).",'YYYY-MM-DD')";
	}
	
	// format and return date string in database timestamp format
	function DBTimeStamp($ts)
	{
		return 'TO_DATE('.date($this->fmtTimeStamp,$d).",'YYYY-MM-DD, HH:RR:SSAM')";
	}
	
        function BeginTrans()
	{      
               $this->autoCommit = false;
               ora_commitoff($this->_connectionID);
               return true;
	}
	
	function CommitTrans()
	{
               $ret = ora_commit($this->_connectionID);
	       ora_commiton($this->_connectionID);
	       return $ret;
	}
	
	function RollbackTrans()
	{
                $ret = ora_rollback($this->_connectionID);
                ora_commiton($this->_connectionID);
		return $ret;
	}
        
        function SelectDB($dbName) {
               return false;
        }

	/* there seems to be a bug in the oracle extension -- always returns ORA-00000 - no error */
        function ErrorMsg() {
                $this->_errorMsg = @ora_error($this->_curs);
		if (!$this->_errorMsg) $this->_errorMsg = @ora_error($this->_connectionID);
                return $this->_errorMsg;
        }
	
	function ErrorNo() {
                $err = @ora_errorcode($this->_curs);
		if (!$err) return @ora_errorcode($this->_connectionID);
        }
	

        // returns true or false
        function _connect($argHostname, $argUsername, $argPassword, $argDatabasename)
        {
		if ($argHostname) print "<p>Connect: 1st argument should be left blank for $this->databaseType</p>";
                $this->_connectionID = ora_logon($argUsername,$argPassword);
                if ($this->_connectionID === false) return false;
                if ($this->autoCommit) ora_commiton($this->_connectionID);
		if ($this->_initdate) $this->Execute("ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD'");

                return true;
        }
        // returns true or false
        function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
        {
		if ($argHostname) print "<p>PConnect: 1st argument should be left blank for $this->databaseType</p>";
                $this->_connectionID = ora_plogon($argUsername,$argPassword);
                if ($this->_connectionID === false) return false;
                if ($this->autoCommit) ora_commiton($this->_connectionID);
		if ($this->_initdate) $this->Execute("ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD'");

                return true;
        }

        // returns query ID if successful, otherwise false
        function _query($sql,$inputarr)
        {
                 $curs = ora_open($this->_connectionID);
		 
		 if ($curs === false) return false;
		$this->_curs = $curs;
		if (!ora_parse($curs,$sql)) return false;
		if (ora_exec($curs)) return $curs;
		
		 @ora_close($curs);
                 return false;
        }

        // returns true or false
        function _close()
        {
		if (!$this->autoCommit) ora_rollback($this->_connectionID);
                return @ora_close($this->_connectionID);
        }


}

/*--------------------------------------------------------------------------------------
         Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordset_oracle extends ADORecordSet {

        var $databaseType = "oracle";
	var $bind = false;
	
        function ADORecordset_oracle($queryID)
        {
		$this->_queryID = $queryID;
	
		$this->_inited = true;
		$this->fields = array();
		if ($queryID) {
			$this->_currentRow = 0;
			$this->EOF = !$this->_fetch();
			@$this->_initrs();
		} else {
			$this->_numOfRows = 0;
			$this->_numOfFields = 0;
			$this->EOF = true;
		}
		
 		return $this->_queryID;
        }



        /*        Returns: an object containing field information.
                Get column information in the Recordset object. fetchField() can be used in order to obtain information about
                fields in a certain query result. If the field offset isn't specified, the next field that wasn't yet retrieved by
                fetchField() is retrieved.        */

        function FetchField($fieldOffset = -1)
        {
                 $fld = new ADODBFieldObject;
                 $fld->name = ora_columnname($this->_queryID, $fieldOffset);
                 $fld->type = ora_columntype($this->_queryID, $fieldOffset);
                 $fld->max_length = ora_columnsize($this->_queryID, $fieldOffset);
                
                 return $fld;
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
                $this->_numOfRows = -1;
                $this->_numOfFields = @ora_numcols($this->_queryID);
        }

	
        function _seek($row)
        {
                return false;
        }

        function _fetch($ignore_fields=false) {
	// should remove call by reference, but ora_fetch_into requires it in 4.0.3pl1
                return @ora_fetch_into($this->_queryID,&$this->fields);
        }

        /*        close() only needs to be called if you are worried about using too much memory while your script
                is running. All associated result memory for the specified result identifier will automatically be freed.        */

        function _close() {
                return @ora_close($this->_queryID);
        }

        function MetaType($t,$len=-1)
        {
                switch (strtoupper($t)) {
                case 'VARCHAR':
                case 'VARCHAR2':
                case 'CHAR':
		case 'VARBINARY':
		case 'BINARY':
                        if ($len <= $this->blobSize) return 'C';
                case 'LONG':
		case 'LONG VARCHAR':
			return 'X';
                case 'LONG RAW':
		case 'LONG VARBINARY':
		case 'CLOB':
                        return 'B';

                case 'DATE': return 'D';

                //case 'T': return 'T';

                case 'BIT': return 'L';
		case 'INT': 
		case 'SMALLINT':
		case 'INTEGER': return 'I';
                default: return 'N';
                }
        }
}
?>