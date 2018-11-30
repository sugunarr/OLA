<?php
/* 
V0.95 13 Mar 2001 (c) 2000, 2001 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under Lesser GPL library license. See License.txt. 
Set tabs to 8 for best viewing.
  
  Latest version is available at http://php.weblogs.com/
  
  Microsoft Visual FoxPro data driver. Requires ODBC. Works only on MS Windows.
*/

if (!defined('_ADODB_ODBC_LAYER')) {
	include("$ADODB_DIR/adodb-odbc.inc.php");
}
if (!defined('ADODB_VFP')){
define('ADODB_VFP',1);
class ADODB_vfp extends ADODB_odbc {
	var $databaseType = "vfp";	
	var $fmtDate = "{^Y-m-d}";
	var $fmtTimeStamp = "{^Y-m-d, h:i:sA}";
	var $replaceQuote = "'+chr(39)+'" ;
	var $true = '.T.';
	var $false = '.F.';
	var $hasTop = true;		// support mssql SELECT TOP 10 * FROM TABLE

	function BeginTrans() { return false;}

	// quote string to be sent back to database
	function qstr($s,$nofixquotes=false)
	{
		if (!$nofixquotes) return  "'".str_replace("\r\n","'+chr(13)+'",str_replace("'",$this->replaceQuote,$s))."'";
		return "'".$s."'";
	}
	
};
 

class  ADORecordSet_vfp extends ADORecordSet_odbc {	
	
	var $databaseType = "vfp";		

	
	function ADORecordSet_vfp($id)
	{
		return $this->ADORecordSet_odbc($id);
	}

	function MetaType($t,$len=-1)
	{
		switch (strtoupper($t)) {
		case 'C':
			if ($len <= $this->blobSize) return 'C';
		case 'M':
			return 'X';
			 
		case 'D': return 'D';
		
		case 'T': return 'T';
		
		case 'L': return 'L';
		
		case 'I': return 'I';
		
		default: return 'N';
		}
	}
}

} //define
?>