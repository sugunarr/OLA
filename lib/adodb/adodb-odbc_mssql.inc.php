<?php
/* 
V0.95 13 Mar 2001 (c) 2000, 2001 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under Lesser GPL library license. See License.txt. 
Set tabs to 8 for best viewing.
  
  Latest version is available at http://php.weblogs.com/
  
  MSSQL support via ODBC. Requires ODBC. Works on Windows and Unix. 
  For Unix configuration, see http://phpbuilder.com/columns/alberto20000919.php3
*/

if (!defined('_ADODB_ODBC_LAYER')) {
	include("$ADODB_DIR/adodb-odbc.inc.php");
}

 
class  ADODB_odbc_mssql extends ADODB_odbc {	
	var $databaseType = 'odbc_mssql';
	var $fmtDate = "'Y-m-d'";
	var $fmtTimeStamp = "'Y-m-d h:i:sA'";
	var $hasTop = true;		// support mssql/interbase SELECT TOP 10 * FROM TABLE

} 
 
class  ADORecordSet_odbc_mssql extends ADORecordSet_odbc {	
	
	var $databaseType = 'odbc_mssql';
	
	function ADORecordSet_odbc_mssql($id)
	{
		return $this->ADORecordSet_odbc($id);
	}
}
?>