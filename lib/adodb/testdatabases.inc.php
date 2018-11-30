<?php
  
/*
V0.95 13 Mar 2001 (c) 2000, 2001 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under Lesser GPL library license. See License.txt.
*/ 
 
 /* this file is used by the ADODB test program: test.php */
 
// cannot test databases below, but we include them anyway to check
// if they parse ok...
ADOLoadCode("sybase");
ADOLoadCode("postgres");
ADOLoadCode("postgres7");

if (!empty($testpostgres)) {
	//ADOLoadCode("postgres");
	$db = &ADONewConnection('postgres');
	print "<h1>Connecting $db->databaseType...</h1>";
	if (@$db->PConnect("susetikus","tester","test","test")) {
		testdb($db,"create table ADOXYZ (id integer, firstname char(24), lastname varchar,created date)");
	}else
		print "ERROR: PostgreSQL requires a database called test on server susetikus, user tester, password test.<BR>".$db->ErrorMsg();
}
ADOLoadCode("ibase");
if (!empty($testibase)) {
	
	$db = &ADONewConnection();
	print "<h1>Connecting $db->databaseType...</h1>";
	if (@$db->PConnect("e:\\interbase\\examples\\database\\employee.gdb", "sysdba", "masterkey", ""))
		testdb($db,"create table ADOXYZ (id integer, firstname char(24), lastname char(24),created date)");
	 else print "ERROR: Interbase test requires a database called employee.gdb".'<BR>'.$db->ErrorMsg();
	
}
ADOLoadCode("access");
// REQUIRES ODBC DSN CALLED nwind
if (!empty($testaccess)) {
	
	$db = &ADONewConnection('access');
	print "<h1>Connecting $db->databaseType...</h1>";
	
	if (@$db->PConnect("nwind", "", "", ""))
		testdb($db,"create table ADOXYZ (id int, firstname char(24), lastname char(24),created datetime)");
	else print "ERROR: Access test requires a Windows ODBC DSN=nwind, Access driver";
	
}

ADOLoadCode("ado_access");
if (!empty($testaccess) && !empty($testado)) { // ADO ACCESS
	
	$db = &ADONewConnection("ado_access");
	print "<h1>Connecting $db->databaseType...</h1>";
	
	$access = 'd:\inetpub\wwwroot\php\NWIND.MDB';
	$myDSN =  'PROVIDER=Microsoft.Jet.OLEDB.4.0;'
		. 'DATA SOURCE=' . $access . ';';
		//. 'USER ID=;PASSWORD=;';
	
	if (@$db->PConnect($myDSN, "", "", "")) {
		print "ADO version=".$db->_connectionID->version."<br>";
		testdb($db,"create table ADOXYZ (id int, firstname char(24), lastname char(24),created datetime)");
	} else print "ERROR: Access test requires a Access database $access".'<BR>'.$db->ErrorMsg();
	
}

ADOLoadCode("vfp");
if (!empty($testvfp)) { // ODBC

	
	$db = &ADONewConnection('vfp');
	print "<h1>Connecting $db->databaseType...</h1>";
	if (@$db->PConnect("logos2", "", "", ""))
		testdb($db,"create table d:\\inetpub\\wwwroot\\logos2\\data\\ADOXYZ (id int, firstname char(24), lastname char(24),created date)");
	 else print "ERROR: Visual FoxPro test requires a Windows ODBC DSN=logos2, VFP driver";
	
}


ADOLoadCode("mysqlt");
// REQUIRES MySQL server at localhost with database 'test'
if (!empty($testmysql)) { // MYSQL
	
	$db = &ADONewConnection();
	print "<h1>Connecting $db->databaseType...</h1>";
	if (@$db->PConnect("192.168.0.1", "", "", "test"))
		testdb($db);
	else print "ERROR: MySQL test requires a MySQL server on localhost, userid='admin', password='', database='test'".'<BR>'.$db->ErrorMsg();
	
}
ADOLoadCode("oci8");
if (!empty($testoracle)) { 
	
	$db = ADONewConnection();
	print "<h1>Connecting $db->databaseType...</h1>";
	if ($db->PConnect("", "scott", "tiger", ""))
		testdb($db,"create table ADOXYZ (id int, firstname varchar(24), lastname varchar(24),created date)");
	else print "ERROR: Oracle test requires an Oracle server setup with scott/tiger".'<BR>'.$db->ErrorMsg();

}
ADOLoadCode("oracle");
if (!empty($testoracle)) { 
	
	$db = ADONewConnection();
	print "<h1>Connecting $db->databaseType...</h1>";
	if ($db->PConnect("", "scott", "tiger", ""))
		testdb($db,"create table ADOXYZ (id int, firstname varchar(24), lastname varchar(24),created date)");
	else print "ERROR: Oracle test requires an Oracle server setup with scott/tiger".'<BR>'.$db->ErrorMsg();

}



ADOLoadCode("mssql");
if (!empty($testmssql)) { // MS SQL Server -- the extension is buggy -- probably better to use ODBC
	$db = ADONewConnection();
	print "<h1>Connecting $db->databaseType...</h1>";
	if (@$db->PConnect("mangrove", "sa", "", "ai"))
		testdb($db,"create table ADOXYZ (id int, firstname char(24), lastname char(24),created datetime)");
	else print "ERROR: MSSQL test 2 requires a MS SQL 7 on a server='192.168.0.1', userid='sa', password='', database='ai'".'<BR>'.$db->ErrorMsg();
	
}


ADOLoadCode("odbc_mssql");
if (!empty($testmssql)) { // MS SQL Server via ODBC
	
	$db = ADONewConnection();
	print "<h1>Connecting $db->databaseType...</h1>";
	if (@$db->PConnect("ai", "sa", "", ""))
		testdb($db,"create table ADOXYZ (id int, firstname char(24), lastname char(24),created datetime)");
	else print "ERROR: MSSQL test 1 requires a MS SQL 7 server setup with DSN='ai', userid='sa', password=''";

}

ADOLoadCode("ado_mssql");

if (!empty($testmssql) && !empty($testado) ) { // ADO ACCESS MSSQL -- thru ODBC -- DSN-less
	
	$db = &ADONewConnection("ado_mssql");
	print "<h1>Connecting DSN-less $db->databaseType...</h1>";
	
	$myDSN="PROVIDER=MSDASQL;DRIVER={SQL Server};"
		. "SERVER=mangrove;DATABASE=ai;UID=sa;PWD=;"  ;

		
	if (@$db->PConnect($myDSN, "", "", ""))
		testdb($db,"create table ADOXYZ (id int, firstname char(24), lastname char(24),created datetime)");
	else print "ERROR: MSSQL test 2 requires a MS SQL 7 on a server='mangrove', userid='sa', password='', database='ai'";
	
}


if (!empty($testmssql) && !empty($testado)) { // ADO ACCESS MSSQL with OLEDB provider

	$db = &ADONewConnection("ado_mssql");
	print "<h1>Connecting DSN-less OLEDB Provider $db->databaseType...</h1>";
	
	$myDSN="SERVER=mangrove;DATABASE=ai;";
		
	if (@$db->PConnect($myDSN, "sa", "", 'SQLOLEDB'))
		testdb($db,"create table ADOXYZ (id int, firstname char(24), lastname char(24),created datetime)");
	else print "ERROR: MSSQL test 2 requires a MS SQL 7 on a server='mangrove', userid='sa', password='', database='ai'";

}

?>