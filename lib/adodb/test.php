<?php
/* 
V0.95 13 Mar 2001 (c) 2000, 2001 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under Lesser GPL library license. See License.txt. 
  Set tabs to 8 for best viewing.
    
  Latest version is available at http://php.weblogs.com/
*/
if (isset($HTTP_SERVER_VARS['DOCUMENT_ROOT'])) {
	$zdir = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];
	if ($zdir[strlen($zdir)-1] == '/') $zdir = substr($zdir,0,strlen($zdir)-1);
	$ADODB_DIR = $HTTP_SERVER_VARS['DOCUMENT_ROOT'].'/php/adodb';
} else
	$ADODB_DIR = 'd:\inetpub\wwwroot\php\adodb';
	
?><html>
<title>ADODB TEST</title>
<body bgcolor=white>
<H1>ADODB Test</H1>

This script tests the following databases: Interbase, Oracle, Visual FoxPro, Microsoft Access (ODBC and ADO), MySQL, MSSQL (ODBC, native, ADO). 
There is also support for Sybase, PostgreSQL.</p>
For the latest version of ADODB, visit <a href=http://php.weblogs.com/ADODB>php.weblogs.com</a>.</p>
<?php

// Set the following control flags to true/false to enable testing for a particular database.

$testoracle = true;
/*
$testibase = true;
$testpostgres = true;
*/
$testmysql = true;

//$testmssql = true;
//$testvfp = true;
//$testaccess = true;
//$testado = true;

error_reporting(63);

set_time_limit(240); // increase timeout

include("./tohtml.inc.php");
include("./adodb.inc.php");		

// the table creation code is specific to the database, so we allow the user 
// to define their own table creation stuff
function testdb(&$db,$createtab="create table ADOXYZ (id int, firstname char(24), lastname char(24), created date)")
{
GLOBAL $ADODB_version;
?>	<form>
	</p>
	<table width=100% ><tr><td bgcolor=beige>&nbsp;</td></tr></table>
	</p>
<?php  
	$create =false;
      	$db->debug = false;
	
	$phpv = phpversion();
	print "<h3>ADODB Version: $ADODB_version Host: <i>$db->host</i> &nbsp; Database: <i>$db->database</i> &nbsp; PHP: $phpv</h3>";
	$e = error_reporting(63-E_WARNING);
	print "<p>Testing bad connection. Ignore following error msgs:<br>";
	$db2 = ADONewConnection();
	$rez = $db2->Connect("bad connection");
	$err = $db2->ErrorMsg();
	error_reporting($e);
	
	print "<i>Error='$err'</i></p>";
	if ($rez) print "<b>Cannot check if connection failed.</b> The Connect() function returned true.</p>";
	
	$rs=$db->Execute('select * from adoxyz');
	if($rs === false) $create = true;
	else $rs->Close();
	
	//if ($db->databaseType !='vfp') $db->Execute("drop table ADOXYZ");
        
	if ($create) {
                if ($db->databaseType == 'ibase') {
                        print "<b>Please create the following table for testing:</b></p>$createtab</p>";
                        return;
                } else
                        $db->Execute($createtab);
        }
	
	$rs = &$db->Execute("delete from ADOXYZ"); // some ODBC drivers will fail the drop so we delete
	if ($rs) {
		if(! $rs->EOF)print "<b>Error: </b>RecordSet returned by Execute('delete...') should show EOF</p>";
		$rs->Close();
	} else print "err=".$db->ErrorMsg();
	
	print "<p>Test select on empty table</p>";
	$rs = &$db->Execute("select * from ADOXYZ where id=9999");
	if ($rs && !$rs->EOF) print "<b>Error: </b>RecordSet returned by Execute(select...') on empty table should show EOF</p>";
	if ($rs) $rs->Close();
	
	
	$db->debug=false;	
	print "<p>Testing Commit: ";
	$time = $db->DBDate(time());
	if (!$db->BeginTrans()) print '<b>Transactions not supported</b></p>';
	else { /* COMMIT */
		$rs = $db->Execute("insert into ADOXYZ values (99,'Should Not','Exist (Commit)',$time)");
		if ($rs && $db->CommitTrans()) {
			$rs->Close();
			$rs = &$db->Execute("select * from ADOXYZ where id=99");
			if ($rs === false || $rs->EOF) {
				print '<b>Data not saved</b></p>';
				$rs = &$db->Execute("select * from ADOXYZ where id=99");
				print_r($rs);
				die();
			} else print 'OK</p>';
			if ($rs) $rs->Close();
		} else
			print "<b>Commit failed</b></p>";
		
		/* ROLLBACK */	
		if (!$db->BeginTrans()) print "<p><b>Error in BeginTrans</b>()</p>";
		print "<p>Testing Rollback: ";
		$db->Execute("insert into ADOXYZ values (100,'Should Not','Exist (Rollback)',$time)");
		if ($db->RollbackTrans()) {
			$rs = $db->Execute("select * from ADOXYZ where id=100");
			if ($rs && !$rs->EOF) print '<b>Fail: Data should rollback</b></p>';
			else print 'OK</p>';
			if ($rs) $rs->Close();
		} else
			print "<b>Commit failed</b></p>";
			
		$rs = &$db->Execute('delete from ADOXYZ where id>50');
		if ($rs) $rs->Close();
	}
	
	print "<p>Testing MetaTables() and MetaColumns()</p>";
	$a = $db->MetaTables();
	if ($a===false) print "<b>MetaTables not supported</b></p>";
	else {
		print "Array of tables: "; 
		foreach($a as $v) print " ($v) ";
		print '</p>';
	}
	$a = $db->MetaColumns('ADOXYZ');
	if ($a===false) print "<b>MetaColumns not supported</b></p>";
	else {
		print "<p>Columns of ADOXYZ: ";
		foreach($a as $v) print " ($v->name $v->type $v->max_length) ";
	}
	$rs = &$db->Execute('delete from ADOXYZ');
	if ($rs) $rs->Close();
	
	$db->debug = false;
	
	print "<p>Inserting 50 rows</p>";

	for ($i = 0; $i < 5; $i++) {	
	
	$time = $db->DBDate(time());
	switch($db->dataProvider){
	case 'ado':
	default:

		$arr = array(0=>'Caroline',1=>'Miranda ');
		$rs = $db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+0,?,?,$time)",$arr);
		if ($rs === false) print '<b>Error inserting with parameters</b><br>';
                else $rs->Close();
		break;

	case 'odbc':
	// currently there are bugs using parameters with ODBC with PHP 4
		$rs=$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+0,'Caroline','Miranda',$time)");
		if ($rs === false) print $rs->ErrorMsg.'<b>Error inserting Caroline</b><br>';
                else $rs->Close();
		break;
	}
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+1,'John','Lim',$time)");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+2,'Mary','Lamb',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+3,'George','Washington',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+4,'Mr. Alan','Tam',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+5,'Alan','Turing',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created)values ($i*10+6,'Serena','Williams',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+7,'Yat Sun','Sun',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+8,'Wai Hun','See',$time )");
	$db->Execute("insert into ADOXYZ (id,firstname,lastname,created) values ($i*10+9,'Steven','Oey',$time )");
	}
        
        $db->Execute('update ADOXYZ set id=id+1');
        $nrows = $db->Affected_Rows();
        if ($nrows === false) print "<p><b>Affected_Rows() not supported</b></p>";
        else if ($nrows != 50)  print "<p><b>Affected_Rows() Error: $nrows returned (should be 50) </b></p>";
        else print "<p>Affected_Rows() passed</p>";
	$db->debug = true;

 ///////////////////////////////
 	
	$rs = &$db->Execute("select * from ADOXYZ order by id");
	if ($rs) {
		if ($rs->RecordCount() != 50) print "<p><b>RecordCount returns -1</b></p>";
		if (isset($rs->fields['firstname'])) print '<p>The fields columns can be indexed by column name.</p>';
		else print '<p>The fields columns <i>cannot</i> be indexed by column name.</p>';
		rs2html($rs);
	}
	else print "<b>Error in Execute of SELECT</b></p>";
	
	print "<p>SelectLimit Test 1: Should see Caroline, John and Mary</p>";
	$rs = &$db->SelectLimit('select * from ADOXYZ order by id',3);
	if ($rs && !$rs->EOF) rs2html($rs);
	else "<p><b>Failed SelectLimit Test 1</b></p>";
	print "<p>SelectLimit Test 2: Should see Mary, George and Mr. Alan</p>";
	$rs = &$db->SelectLimit('select * from ADOXYZ order by id',3,2);
	if ($rs && !$rs->EOF) rs2html($rs);
	else "<p><b>Failed SelectLimit Test 2</b></p>";
	
	print "<p>SelectLimit Test 3: Should see Wai Hun and Steven</p>";
	$rs = &$db->SelectLimit('select * from ADOXYZ order by id',-1,48);
	if ($rs && !$rs->EOF) rs2html($rs);
	else "<p><b>Failed SelectLimit Test 3</b></p>";
	
        $rs = &$db->Execute("select * from ADOXYZ order by id");
	print "<p>Testing Move()</p>";	
	if (!$rs)print "<b>Failed Move SELECT</b></p>";
	else {
		if (!$rs->Move(2)) {
			if (!$rs->canSeek) print "<p>$db->databaseType: <b>Move(), MoveFirst() nor MoveLast() not supported.</b></p>";
			else print '<p><b>RecordSet->canSeek property should be set to false</b></p>';
		} else {
			$rs->MoveFirst();
			if (trim($rs->Fields("firstname")) != 'Caroline') {
				print "<p><b>$db->databaseType: MoveFirst failed -- probably cannot scroll backwards</b></p>";
			}
			else print "MoveFirst() OK<BR>";
                        
                        // Move(3) tests error handling -- MoveFirst should not move cursor
			$rs->Move(3);
			if (trim($rs->Fields("firstname")) != 'George') {
				print '<p>'.$rs->Fields("id")."<b>$db->databaseType: Move(3) failed</b></p>";
				print_r($rs);
			} else print "Move(3) OK<BR>";
                        
            		$rs->Move(7);
			if (trim($rs->Fields("firstname")) != 'Yat Sun') {
				print '<p>'.$rs->Fields("id")."<b>$db->databaseType: Move(7) failed</b></p>";
				print_r($rs);
			} else print "Move(7) OK<BR>";

			$rs->MoveLast();
			if (trim($rs->Fields("firstname")) != 'Steven'){
				 print '<p>'.$rs->Fields("id")."<b>$db->databaseType: MoveLast() failed</b></p>";
				 print_r($rs);
			}else print "MoveLast() OK<BR>";
		}
	}
	
 
	print "<p>Testing concat: concat firstname and lastname</p>";
	
	if ($db->databaseType == 'postgres')
		$rs = &$db->Execute("select distinct ".$db->Concat('(firstname',$db->qstr(' ').')','lastname')." from ADOXYZ");
	else
		$rs = &$db->Execute("select distinct ".$db->Concat('firstname',$db->qstr(' '),'lastname')." from ADOXYZ");
	if ($rs) {
		rs2html($rs);
	} else print "<b>Failed Concat</b></p>";
	
	print "<hr>Testing GetArray() ";
	$rs = &$db->Execute("select * from ADOXYZ order by id");
	if ($rs) {
		$arr = $rs->GetArray(10);
		if (sizeof($arr) != 10 || trim($arr[1][1]) != 'John' || trim($arr[1][2]) != 'Lim') print $arr[1][1].' '.$arr[1][2]."<b> &nbsp; ERROR</b><br>";
		else print " OK<BR>";
	}
	
	print "Testing GetAssoc() ";
	$rs = &$db->Execute("select distinct lastname,firstname from ADOXYZ");
	if ($rs) {
		$arr = $rs->GetAssoc();
		if (trim($arr['See']) != 'Wai Hun') print $arr['See']." &nbsp; <b>ERROR</b><br>";
		else print " OK<BR>";
	}
	
	print "Testing GetMenu() <BR>";
	$rs = &$db->Execute("select distinct firstname,lastname from ADOXYZ");
	if ($rs) print 'With blanks, Steven selected:'. $rs->GetMenu('menu','Steven'); 
	else print " Fail<BR>";
	$rs = &$db->Execute("select distinct firstname,lastname from ADOXYZ");
	if ($rs) print ' No blanks, Steven selected: '. $rs->GetMenu('menu','Steven',false);
	else print " Fail<BR>";
	
	$rs = &$db->Execute("select distinct firstname,lastname from ADOXYZ");
	if ($rs) print ' Multiple, Alan selected: '. $rs->GetMenu('menu','Alan',false,true);
	else print " Fail<BR>";
	print '</p><hr>';
	
	$db->debug = false;
	$rs1 = &$db->Execute("select id from ADOXYZ where id = 2 or id = 1 order by 1");
	$rs2 = &$db->Execute("select id from ADOXYZ where id = 3 or id = 4 order by 1");
	
	if ($rs1) $rs1->MoveLast();
	if ($rs2) $rs2->MoveLast();
	

	if (empty($rs1) || empty($rs2) || $rs1->fields[0] != 2 || $rs2->fields[0] != 4) {
		$a = $rs1->fields[0];
		$b = $rs2->fields[0];
		print "<p><b>Error in multiple recordset test rs1=$a rs2=%b (should be rs1=2 rs2=4)</b></p>";
	} else
		print "<p>Testing multiple recordsets</p>";
	$sql = "seleckt zcol from NoSuchTable_xyz";
	print "<p>Testing execution of illegal statement: <i>$sql</i></p>";
	if ($db->Execute($sql) === false) {
		print "<p>This returns the following ErrorMsg(): <i>".$db->ErrorMsg()."</i> and ErrorNo(): ".$db->ErrorNo().'</p>';
	} else 
		print "<p><b>Error in error handling -- Execute() should return false</b></p>";
?>
	</p>
	<table width=100% ><tr><td bgcolor=beige>&nbsp;</td></tr></table>
	</p></form>
<?php
	if ($rs1) $rs1->Close();
	if ($rs2) $rs2->Close();
        if ($rs) $rs->Close();
        $db->Close();
}

include('./testdatabases.inc.php');

?>
<p><i>ADODB Database Library  (c) 2000, 2001 John Lim. All rights reserved. Released under Lesser GPL.</i></p>
</body>
</html>
