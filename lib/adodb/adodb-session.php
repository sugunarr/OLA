<?php
/*
V0.95 13 Mar 2001 (c) 2000, 2001 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under Lesser GPL library license. See License.txt. 
  Set tabs to 8 for best viewing.
  
  Latest version of ADODB is available at http://php.weblogs.com/adodb
  ======================================================================
  
 This file provides PHP4 session management using the ADODB database wrapper library.
 
 Example
 =======
 
 	GLOBAL $HTTP_SESSION_VARS;
	include('adodb.inc.php');
	include('adodb-session.php');
	session_start();
	session_register('AVAR');
	$HTTP_SESSION_VARS['AVAR'] += 1;
	print "<p>\$HTTP_SESSION_VARS['AVAR']={$HTTP_SESSION_VARS['AVAR']}</p>";

 
 Installation
 ============
 1. Create a new database in MySQL or Access "sessions" like so:
 
  create table sessions (
       SESSKEY char(32) not null,
       EXPIRY int(11) unsigned not null,
       DATA text not null,
      primary key (sesskey)
  );
  
  2. Then define the following parameters in this file:
  	$ADODB_SESSION_DRIVER='database driver, eg. mysql or ibase';
	$ADODB_SESSION_CONNECT='server to connect to';
	$ADODB_SESSION_USER ='user';
	$ADODB_SESSION_PWD ='password';
	$ADODB_SESSION_DB ='database';
	
  3. Recommended is PHP 4.0.2 or later. There are documented session bugs in 
     earlier versions of PHP.

*/

if (!defined('_ADODB_LAYER')) {
	include ('adodb.inc.php');
}



if (!defined('ADODB_SESSION')) {

 define('ADODB_SESSION',1);
 
GLOBAL 	$ADODB_SESSION_CONNECT, 
	$ADODB_SESSION_DRIVER,
	$ADODB_SESSION_USER,
	$ADODB_SESSION_PWD,
	$ADODB_SESSION_DB,
	$ADODB_SESS_CONN,
	$ADODB_SESS_LIFE,
	$ADODB_SESS_INSERT; 

	/* SET THE FOLLOWING PARAMETERS */
if (empty($ADODB_SESSION_DRIVER)) {
	$ADODB_SESSION_DRIVER='ado_access';
	$ADODB_SESSION_CONNECT='PROVIDER=Microsoft.Jet.OLEDB.4.0;DATA SOURCE=d:\inetpub\wwwroot\php\phplens\adodb.mdb;';
	$ADODB_SESSION_USER ='';
	$ADODB_SESSION_PWD ='';
	$ADODB_SESSION_DB ='';
}

$ADODB_SESS_LIFE = get_cfg_var('session.gc_maxlifetime');
if ($ADODB_SESS_LIFE <= 1) {
	// bug in PHP 4.0.3 pl 1  -- how about other versions?
	//print "<h3>Session Error: PHP.INI setting <i>session.gc_maxlifetime</i> not set: $ADODB_SESS_LIFE</h3>";
	$ADODB_SESS_LIFE=1440;
}

function adodb_sess_open($save_path, $session_name) 
{
GLOBAL 	$ADODB_SESSION_CONNECT, 
	$ADODB_SESSION_DRIVER,
	$ADODB_SESSION_USER,
	$ADODB_SESSION_PWD,
	$ADODB_SESSION_DB,
	$ADODB_SESS_CONN,
	$ADODB_SESS_DEBUG;
	
	$ADODB_SESS_INSERT = false;
	
	if (isset($ADODB_SESS_CONN)) return true;
	ADOLoadCode($ADODB_SESSION_DRIVER);
	
	$ADODB_SESS_CONN = ADONewConnection($ADODB_SESSION_DRIVER);
	if (!empty($ADODB_SESS_DEBUG)) {
		$ADODB_SESS_CONN->debug = true;
		print" conn=$ADODB_SESSION_CONNECT user=$ADODB_SESSION_USER pwd=$ADODB_SESSION_PWD db=$ADODB_SESSION_DB ";
	}
	return $ADODB_SESS_CONN->PConnect($ADODB_SESSION_CONNECT,
			$ADODB_SESSION_USER,$ADODB_SESSION_PWD,$ADODB_SESSION_DB);
	
}

function adodb_sess_close() 
{
global $ADODB_SESS_CONN;

	if ($ADODB_SESS_CONN) $ADODB_SESS_CONN->Close();
	return true;
}

function adodb_sess_read($key) 
{
global $ADODB_SESS_CONN,$ADODB_SESS_INSERT;
	
	$rs = $ADODB_SESS_CONN->Execute("SELECT data FROM sessions WHERE sesskey = '$key' AND expiry >= " . time());
	if ($rs) {
		if ($rs->EOF) {
			$ADODB_SESS_INSERT = true;
			$v = '';
		} else 
			$v = rawurldecode($rs->fields[0]);
			
		$rs->Close();
		return $v;
	}
	else $ADODB_SESS_INSERT = true;
	
	return false;
}

function adodb_sess_write($key, $val) 
{
	global $ADODB_SESS_INSERT,$ADODB_SESS_CONN, $ADODB_SESS_LIFE;

	$expiry = time() + $ADODB_SESS_LIFE;
	
	$val = rawurlencode($val);
	$qry = "UPDATE sessions SET expiry=$expiry,data='$val' WHERE sesskey='$key'";
	$rs = $ADODB_SESS_CONN->Execute($qry);
	if ($rs) $rs->Close();
	else print '<p>Session Update: '.$ADODB_SESS_CONN->ErrorMsg().'</p>';
	
	if ($ADODB_SESS_INSERT || $rs === false) {
		$qry = "INSERT INTO sessions VALUES ('$key',$expiry,'$val')";
		$rs = $ADODB_SESS_CONN->Execute($qry);
		if ($rs) $rs->Close();
		else print '<p>Session Insert: '.$ADODB_SESS_CONN->ErrorMsg().'</p>';
	}
	// bug in access driver (could be odbc?) means that info is not commited
	// properly unless select statement executed in Win2000
	if ($ADODB_SESS_CONN->databaseType == 'access') $rs = $ADODB_SESS_CONN->Execute("select sesskey from sessions WHERE sesskey='$key'");

	return isset($rs);
}

function adodb_sess_destroy($key) 
{
	global $ADODB_SESS_CONN;

	$qry = "DELETE FROM sessions WHERE sesskey = '$key'";
	$rs = $ADODB_SESS_CONN->Execute($qry);
	if ($rs) $rs->Close();
	return $rs;
}

function adodb_sess_gc($maxlifetime) {
	global $ADODB_SESS_CONN;

	$qry = "DELETE FROM sessions WHERE expiry < " . time();
	$rs = $ADODB_SESS_CONN->Execute($qry);
	if ($rs) $rs->Close();
	return true;
}

session_module_name('user'); 
session_set_save_handler(
	"adodb_sess_open",
	"adodb_sess_close",
	"adodb_sess_read",
	"adodb_sess_write",
	"adodb_sess_destroy",
	"adodb_sess_gc");
}

/*  TEST SCRIPT -- UNCOMMENT */

if (0) {
GLOBAL $HTTP_SESSION_VARS;

	session_start();
	session_register('AVAR');
	$HTTP_SESSION_VARS['AVAR'] += 1;
	print "<p>\$HTTP_SESSION_VARS['AVAR']={$HTTP_SESSION_VARS['AVAR']}</p>";
}

?>