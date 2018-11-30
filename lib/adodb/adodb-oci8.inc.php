<?php
/*
V0.95 13 Mar 2001 (c) 2000, 2001 John Lim. All rights reserved.
  Released under Lesser GPL library license. See License.txt.

  Latest version is available at http://php.weblogs.com/
  
  Code contributed by George Fourlanos <fou@infomap.gr>
  
  13 Nov 2000 jlim - removed all ora_* references.
*/

/*
NLS_Date_Format
Allows you to use a date format other than the Oracle Lite default. When a literal
character string appears where a date value is expected, the Oracle Lite database
tests the string to see if it matches the formats of Oracle, SQL-92, or the value
specified for this parameter in the POLITE.INI file. Setting this parameter also
defines the default format used in the TO_CHAR or TO_DATE functions when no
other format string is supplied.
For Oracle the default is dd-mon-yy or dd-mon-yyyy, and for SQL-92 the default is
yy-mm-dd or yyyy-mm-dd.
Using 'RR' in the format forces two-digit years less than or equal to 49 to be
interpreted as years in the 21st century (2000–2049), and years over 50 as years in
the 20th century (1950–1999). Setting the RR format as the default for all two-digit
year entries allows you to become year-2000 compliant. For example:
NLS_DATE_FORMAT='RR-MM-DD'
You can also modify the date format using the ALTER SESSION command. See the
Oracle Lite SQL Reference for more information.
*/
class ADODB_oci8 extends ADODBConnection {
        var $databaseType = 'oci8';
        var $replaceQuote = "\'"; // string to use to replace quotes
        var $concat_operator='||';
	var $_stmt;
	var $_commit = OCI_COMMIT_ON_SUCCESS;
	var $_initdate = true; // init date to YYYY-MM-DD
	var $metaTablesSQL = 'select table_name from cat';
	var $metaColumnsSQL = "select cname,coltype,width from col where tname='%s'";
	
        function ADODB_oci8() {
        }
	
	function Affected_Rows()
	{
		return OCIRowCount($this->_stmt);
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
             	$this->_commit = OCI_DEFAULT;
               return true;
	}
	
	function CommitTrans()
	{
               $ret = OCIcommit($this->_connectionID);
	       $this->_commit = OCI_COMMIT_ON_SUCCESS;
	       $this->autoCommit = true;
	       return $ret;
	}
	
	function RollbackTrans()
	{
                $ret = OCIrollback($this->_connectionID);
		$this->_commit = OCI_COMMIT_ON_SUCCESS;
	       	$this->autoCommit = true;
		return $ret;
	}
        
        function SelectDB($dbName) {
               return false;
        }

	/* there seems to be a bug in the oracle extension -- always returns ORA-00000 - no error */
        function ErrorMsg() {
		$arr = @OCIerror($this->_stmt);
		if ($arr === false) {
			$arr = @OCIerror($this->_connectionID);
			if ($arr === false) return '';
		}
                $this->_errorMsg = $arr['message'];
                return $this->_errorMsg;
        }
	
	function ErrorNo() {
		$arr = @OCIerror($this->_stmt);
		if ($arr === false) {
			$arr = @OCIerror($this->_connectionID);
			if ($arr === false) return '';
		}
                return $arr['code'];
        }
	

        // returns true or false
        function _connect($argHostname, $argUsername, $argPassword, $argDatabasename)
        {
 		if ($argHostname) print "<p>Connect: 1st argument should be left blank for $this->databaseType</p>";
               $this->_connectionID = OCIlogon($argUsername,$argPassword, $argDatabasename);
                if ($this->_connectionID === false) return false;
		if ($this->_initdate) $this->Execute("ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD'");
                return true;
        }
        // returns true or false
        function _pconnect($argHostname, $argUsername, $argPassword, $argDatabasename)
        {
		if ($argHostname) print "<p>PConnect: 1st argument should be left blank for $this->databaseType</p>";
                 $this->_connectionID = OCIplogon($argUsername,$argPassword, $argDatabasename);
                if ($this->_connectionID === false) return false;
		if ($this->_initdate) $this->Execute("ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD'");
                return true;
        }

        // returns query ID if successful, otherwise false
        function _query($sql,$inputarr)
        {
		$stmt=OCIparse($this->_connectionID,$sql);
		$this->_stmt = $stmt;
		if (!$stmt) return false;
		if (OCIexecute($stmt,$this->_commit)) return $stmt;
                 return false;
        }

        // returns true or false
        function _close()
        {
		if (!$this->autoCommit) OCIRollback($this->_connectionID);
		OCILogoff($this->_connectionID);
        }


}

/*--------------------------------------------------------------------------------------
         Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordset_oci8 extends ADORecordSet {

        var $databaseType = 'oci8';
	var $bind=false;
	
        function ADORecordset_oci8($queryID)
        {
		$this->_inited = true;
		$this->_queryID = $queryID;
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

        function &FetchField($fieldOffset = -1)
        {
                 $fld = new ADODBFieldObject;
		 $fieldOffset += 1;
                 $fld->name =OCIcolumnname($this->_queryID, $fieldOffset);
                 $fld->type = OCIcolumntype($this->_queryID, $fieldOffset);
                 $fld->max_length = OCIcolumnsize($this->_queryID, $fieldOffset);
                
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
                $this->_numOfFields = OCInumcols($this->_queryID);
        }

	
        function _seek($row)
        {
                return false;
        }

        function _fetch($ignore_fields=false) {
                return @OCIfetchinto($this->_queryID,$this->fields);
        }

        /*        close() only needs to be called if you are worried about using too much memory while your script
                is running. All associated result memory for the specified result identifier will automatically be freed.        */

        function _close() {
              OCIFreeStatement($this->_queryID);
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
