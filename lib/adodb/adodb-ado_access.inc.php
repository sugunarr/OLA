<?php
/* 
V0.95 13 Mar 2001 (c) 2000, 2001 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under Lesser GPL library license. See License.txt. 
Set tabs to 8 for best viewing.
  
  Latest version is available at http://php.weblogs.com/
  
    Microsoft Access ADO data driver. Requires ADO and ODBC. Works only on MS Windows.
*/

if (!defined('_ADODB_ADO_LAYER')) {
	include("$ADODB_DIR/adodb-ado.inc.php");
}

class  ADODB_ado_access extends ADODB_ado {	
var $databaseType = 'ado_access';
var $hasTop = true;		// support mssql SELECT TOP 10 * FROM TABLE
var $fmtDate = "#Y-m-d#";
var $fmtTimeStamp = "#Y-m-d h:i:sA#";// note no comma

	function BeginTrans() { return false;}

}

 
class  ADORecordSet_ado_access extends ADORecordSet_ado {	
	
	var $databaseType = "ado_access";		
	
	function ADORecordSet_ado_access(&$id)
	{
		return $this->ADORecordSet_ado($id);
	}
}
?>