<?php
/*
 V0.95 13 Mar 2001 (c) 2000, 2001 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under Lesser GPL library license. See License.txt.
  Set tabs to 8.
  
  Postgres7 support.
  28 Feb 2001: Currently indicate that we support LIMIT
*/

include_once("$ADODB_DIR/adodb-postgres.inc.php");

class ADODB_postgres7 extends ADODB_postgres {
	var $databaseType = 'postgres7';	
	var $hasLimit = true;	// set to true for pgsql 6.5+ only. support pgsql/mysql SELECT * FROM TABLE LIMIT 10

	function ADODB_postgres7() 
	{
	}

	function &SelectLimit($sql,$nrows=-1,$offset=-1)
	{
		$offsetStr = ($offset >= 0) ? " OFFSET $offset" : '';
		return $this->Execute($sql." LIMIT $nrows$offsetStr");
	}
}
	
/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordSet_postgres7 extends ADORecordSet_postgres{

	var $databaseType = "postgres7";

	function ADORecordSet_postgres7($queryID) {
		$res=$this->ADORecordSet_postgres($queryID);
                return $res;
	}

}
?>
