<?php
/*
 V0.95 13 Mar 2001 (c) 2000, 2001 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under Lesser GPL library license. See License.txt.
  Set tabs to 8.
  
  Original version derived from Alberto Cerezal (acerezalp@dbnet.es) - DBNet Informatica & Comunicaciones. 
  08 Nov 2000 jlim - Minor corrections, removing mysql stuff
  09 Nov 2000 jlim - added insertid support suggested by "Christopher Kings-Lynne" <chriskl@familyhealth.com.au>
                    jlim - changed concat operator to || and data types to MetaType to match documented pgsql types 
	     	see http://www.postgresql.org/devel-corner/docs/postgres/datatype.htm  
  22 Nov 2000 jlim - added changes to FetchField() and MetaTables() contributed by "raser" <raser@mail.zen.com.tw>
  27 Nov 2000 jlim - added changes to _connect/_pconnect from ideas by "Lennie" <leen@wirehub.nl>
  15 Dec 2000 jlim - added changes suggested by Additional code changes by "Eric G. Werk" egw@netguide.dk. 
  31 Jan 2001 jlim - finally installed postgresql. testing
  01 Mar 2001 jlim - Freek Dijkstra changes, also support for text type
*/

class ADODB_postgres extends ADODBConnection{
	var $databaseType = 'postgres';
        var $hasInsertID = true;
        var $_resultid = false;
  	var $concat_operator='||';
	var $metaTablesSQL = "select tablename from pg_tables where tablename not like 'pg_%' order by 1";
	var $metaColumnsSQL = "SELECT a.attname,t.typname,a.attlen,a.atttypmod,a.attnotnull FROM pg_class c, pg_attribute a,pg_type t WHERE relkind = 'r' AND c.relname='%s' AND a.attnum > 0 AND a.atttypid = t.oid AND a.attrelid = c.oid ORDER BY a.attnum";
	// get primary key etc -- from Freek Dijkstra
	var $metaKeySQL = "SELECT ic.relname AS index_name, a.attname AS column_name,i.indisunique AS unique_key, i.indisprimary AS primary_key FROM pg_class bc, pg_class ic, pg_index i, pg_attribute a WHERE bc.oid = i.indrelid AND ic.oid = i.indexrelid AND (i.indkey[0] = a.attnum OR i.indkey[1] = a.attnum OR i.indkey[2] = a.attnum OR i.indkey[3] = a.attnum OR i.indkey[4] = a.attnum OR i.indkey[5] = a.attnum OR i.indkey[6] = a.attnum OR i.indkey[7] = a.attnum) AND a.attrelid = bc.oid AND bc.relname = '%s'";
	
	var $_hastrans = false;
	var $hasAffectedRows = false;
	var $hasTop = false;		
	var $hasLimit = false;	// set to true for pgsql 7 only. support pgsql/mysql SELECT * FROM TABLE LIMIT 10
	// below suggested by Freek Dijkstra 
	var $true = 't';		// string that represents TRUE for a database
	var $false = 'f';		// string that represents FALSE for a database
	var $fmtDate = "'Y-m-d'";	// used by DBDate() as the default date format used by the database
	var $fmtTimeStamp = "'Y-m-d h:i:s'"; // used by DBTimeStamp as the default timestamp fmt.
	// The last (fmtTimeStamp is not entirely correct: 
	// PostgreSQL also has support for time zones, 
	// and writes these time in this format: "2001-03-01 18:59:26+02". 
	// There is no code for the "+02" time zone information, so I just left that out. 
	// I'm not familiar enough with both ADODB as well as Postgres 
	// to know what the concequences are. The other values are correct (wheren't in 0.94)
	// -- Freek Dijkstra 

	function ADODB_postgres() 
	{
	}

	/* Warning from http://www.php.net/manual/function.pg-getlastoid.php:
	Using a OID as a unique identifier is not generally wise. 
	Unless you are very careful, you might end up with a tuple having 
	a different OID if a database must be reloaded. */
        function _insertid()
        {
		return pg_getlastoid($this->_resultid);
        }

	// I get this error ??? - jlim
	// Warning: This compilation does not support pg_cmdtuples() in d:/inetpub/wwwroot/php/adodb/adodb-postgres.inc.php on line 44
         function _affectedrows()
        {
		return pg_cmdtuples($this->_resultid);      
        }

	
		// returns true/false
	function BeginTrans()
	{
		$this->_hastrans = true;
		return @pg_Exec($this->_connectionID, "begin");
	}

	// returns true/false. 
	function CommitTrans()
	{
		$this->_hastrans = false;
		return @pg_Exec($this->_connectionID, "commit");
	}
	
	// returns true/false
	function RollbackTrans()
	{
		$this->_hastrans = false;
		return @pg_Exec($this->_connectionID, "rollback");
	}

	// converts table to lowercase 
         function &MetaColumns($table) 
	{
		if (!empty($this->metaColumnsSQL)) {
			// the following is the only difference -- we lowercase it
			$rs = $this->Execute(sprintf($this->metaColumnsSQL,strtolower($table)));
			if ($rs === false) return false;
			
			if (!empty($this->metaKeySQL)) {
				// If we want the primary keys, we have to issue a separate query
				// Of course, a modified version of the metaColumnsSQL query using a 
				// LEFT JOIN would have been much more elegant, but postgres does 
				// not support OUTER JOINS. So here is the clumsy way.
				$rskey = $this->Execute(sprintf($this->metaKeySQL,strtolower($table)));
				// fetch all result in once for performance.
				$keys = $rskey->GetArray();
				$rskey->Close();
				unset($rskey);
			}

			$retarr = array();
			while (!$rs->EOF) { //print_r($rs->fields);
				$fld = new ADODBFieldObject();
				$fld->name = $rs->fields[0];
				$fld->type = $rs->fields[1];
				$fld->max_length = $rs->fields[2];
				if ($fld->max_length <= 0) $fld->max_length = $rs->fields[3]-4;
				if ($fld->max_length <= 0) $fld->max_length = -1;
				
				//Freek
				if ($rs->fields[4] == $this->true) {
					$fld->not_null = true;
				}
				
				// Freek
				if (is_array($keys)) {
					reset ($keys);
					while (list($x,$key) = each($keys)) {
						if ($fld->name == $key['column_name'] AND 
								$key['primary_key'] == $this->true) $fld->primary_key = true;
						if ($fld->name == $key['column_name'] AND 
								$key['unique_key'] == $this->true) $fld->unique = true; // What name is more compatible?
					}
				}
				
				$retarr[strtoupper($fld->name)] = $fld;	
				
				$rs->MoveNext();
			}
			$rs->Close();
			return $retarr;	
		}
		return false;
	}


	// returns true or false
	//
	// examples:
	// 	$db->Connect("host=host1 user=user1 password=secret port=4341");
	// 	$db->Connect('host1','user1','secret');
	function _connect($str,$user='',$pwd='',$db='')
	{           
		if ($user || $pwd || $db) {
           		if ($str)  {
			 	$host = split(":", $str);
				if ($host[0]) $str = "host=$host[0]";
				else $str = 'localhost';
				if (isset($host[1])) $str .= " port=$host[1]";
			}
           		if ($user) $str .= " user=".$user;
           		if ($pwd)  $str .= " password=".$pwd;
			if ($db)   $str .= " dbname=".$db;
		}
		
		//if ($user) $linea = "user=$user host=$linea password=$pwd dbname=$db port=5432";
		$this->_connectionID = pg_connect($str);
		if ($this->_connectionID === false) return false;
		$this->Execute("set datestyle='ISO'");
                return true;
	}
	
	// returns true or false
	//
	// examples:
	// 	$db->PConnect("host=host1 user=user1 password=secret port=4341");
	// 	$db->PConnect('host1','user1','secret');
	function _pconnect($str,$user='',$pwd='',$db='')
	{
		if ($user || $pwd || $db) {
           		if ($str)  {
			 	$host = split(":", $str);
				if ($host[0]) $str = "host=$host[0]";
				else $str = 'localhost';
				if (isset($host[1])) $str .= " port=$host[1]";
			}
           		if ($user) $str .= " user=".$user;
           		if ($pwd)  $str .= " password=".$pwd;
			if ($db)   $str .= " dbname=".$db;
		}
		$this->_connectionID = pg_pconnect($str);
		if ($this->_connectionID === false) return false;
		$this->Execute("set datestyle='ISO'");
		return true;
	}

	// returns queryID or false
	function _query($sql,$inputarr)
	{
                $this->_resultid= pg_Exec($this->_connectionID,$sql);
                return $this->_resultid;
	}
	

	/*	Returns: the last error message from previous database operation	*/	
	function ErrorMsg() {
		$this->_errorMsg = @pg_errormessage($this->_connectionID);
	    	return $this->_errorMsg;
	}

	// returns true or false
	function _close()
	{
		if ($this->_hastrans) $this->RollbackTrans();
		return true;
	}
		
}
	
/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordSet_postgres extends ADORecordSet{

	var $databaseType = "postgres";
	var $canSeek = true;
	
	function ADORecordSet_postgres($queryID) {
		$res=$this->ADORecordSet($queryID);
                return $res;
	}

	function _initrs()
	{
	global $ADODB_COUNTRECS;
		$this->_numOfRows = ($ADODB_COUNTRECS)? @pg_numrows($this->_queryID):-1;
		$this->_numOfFields = @pg_numfields($this->_queryID);
	}



	function &FetchField($fieldOffset = 0) {
		$off=$fieldOffset; // offsets begin at 0
		
		$o= new ADODBFieldObject();
		$o->name = @pg_fieldname($this->_queryID,$off);
		$o->type = @pg_fieldtype($this->_queryID,$off);
		$o->max_length = @pg_fieldsize($this->_queryID,$off);
		//print_r($o);		
		//print "off=$off name=$o->name type=$o->type len=$o->max_length<br>";
		return $o;	
	}

	function _seek($row)
	{
		return @pg_fetch_row($this->_queryID,$row);
	}

	function _fetch($ignore_fields=false)
	{
		$this->fields = @pg_fetch_array($this->_queryID,$this->_currentRow);
		return ($this->fields == true);
	}

	function _close() {
		return @pg_freeresult($this->_queryID);
	}

	function MetaType($t,$len=-1,$fieldobj=false)
	{
		switch (strtoupper($t)) {
	            case 'CHAR':
	            case 'CHARACTER':
	            case 'VARCHAR':
	            case 'NAME':
	                if ($len <= $this->blobSize) return 'C';
				
	            case 'TEXT':
	                return 'X';
		
		    case 'IMAGE': // user defined type
		    case 'BLOB': // user defined type
	            case 'BIT':	// This is a bit string, not a single bit, so don't return 'L'
	            case 'VARBIT':
	                return 'B';
	            
	            case 'BOOL':
	            case 'BOOLEAN':
	                return 'L';
				
	            case 'DATE':
	            	return 'D';
	            
	            case 'TIME':
	            case 'DATETIME':
	            case 'TIMESTAMP':
	            	return 'T';
	            
	            case 'SMALLINT': 
	            case 'BIGINT': 
	            case 'INTEGER': 
	            case 'INT8': 
	            case 'INT4':
	            case 'INT2':
	            	if (isset($fieldobj) &&
				empty($fieldobj->primary_key) && empty($fieldobj->unique)) return 'I';
				
	            case 'OID':
	            case 'SERIAL':
	            	return 'R';
				
	             default:
	             	return 'N';
	        }
	}

}
?>
