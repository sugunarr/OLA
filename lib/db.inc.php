<?php

/***************************************************************************
* ONLINE LIBRARY APPLICATION (OLA)               http://ola.sourceforge.net/
* (c) 2002 S. Rawlinson and N. Flear    Licenced under GPL (see licence.txt)
****************************************************************************
* db.inc.php - version 2.0
* - provides functions that access the database
***************************************************************************/


/***************************************************************************
* Global variables (used in this file only)
***************************************************************************/

  class object {};
  $DB = new object;
  $DB->TYPE = 'mysql';           // as defined by ADODB

  // $DB->SERVER = "db550.perfora.net";     // machine database is on
  // $DB->NAME = "db199801853";          // database name
  // $DB->LOGIN = "dbo199801853";         // database user login
  // $DB->PASSWORD = "Tj59Lp23";      // database user password
  $DB->SERVER = "db.1and1.com";     // machine database is on
  $DB->NAME = "";          // database name
  $DB->LOGIN = "";         // database user login
  $DB->PASSWORD = "";      // database user password

  $DB->DEBUG = 0;   // 0=silent, 1=messages/errors, 2=errors with halt

/***************************************************************************
* Initial Settings
***************************************************************************/

// check that required global variables are set
if (!isset ($DB) || !isset ($DB->DEBUG)) {
  debug_report (2, "DB variables undefined.");
}

// provides ADODB database functions through an abstraction layer
include_once ("/ola/lib/adodb/adodb.inc.php");
if (!(ADOLoadCode ($DB->TYPE))) {
  debug_report (2, "Cannot link to ADODB code.");
}

/***************************************************************************
* Open the database
***************************************************************************/

// initiates a persistant database connection and
// returns a connection object, or 0 if connection failed;
// database is closed automatically by PHP

function db_connect () {
  global $DB;

  $db_conn = &NewADOConnection ();
  if (!($db_conn->PConnect ($DB->SERVER, $DB->LOGIN, $DB->PASSWORD, $DB->NAME))) {
    // set connection to failure value
    $db_conn = 0;
    debug_report (1, "Error connecting to server with server/name/login/password specified.");
  }

  // set error reporting (in ADODB) for debugging purposes
  if ($DB->DEBUG > 0) {
    $db_conn->debug = TRUE;
  }

  return $db_conn;
}

/***************************************************************************
* Query the database
***************************************************************************/

// takes a connection and SQL query string and returns a recordset object;
// returns 0 on failure;
// limits length of $numrows and and starting $offset rows from the top
// when these values are positive integers

function db_query (&$db_conn, $query, $numrows=-1, $offset=-1) {

  // error reporting: output the query string
  debug_report (0, htmlspecialchars ($query));

  // error reporting: bad connection
  if (!is_object ($db_conn)) {
    $db_record_set = 0;
    debug_report (1, "Bad connection object.");
  }

  if ($numrows == -1 && $offset == -1)
    $db_record_set = $db_conn->Execute ($query);
  else
    $db_record_set = $db_conn->SelectLimit ($query, $numrows, $offset);

  if ($db_conn->ErrorNo () > 0) {
    $db_record_set = 0;
    debug_report (1, "Cannot execute SQL query.");
  }

  return $db_record_set;
}

/***************************************************************************
* Error reporting routine (for debugging purposes)
***************************************************************************/

// outputs messages, depending on the specified global DEBUG level
// and the priority of the actual error

function debug_report ($priority, $output) {
  global $DB;

  // forces serious errors to halt
  if ($priority == 2) $DB->DEBUG = 2;

  // print the error message
  if ($DB->DEBUG > 0) {
    printf ("<p>db.inc.php: " . $output . "</p>");

    // only halt on errors with high priority and high debug level
    if ($DB->DEBUG > 1 && $priority > 0) {
      printf ("<p>db.inc.php: Script halted.</p>");
      die ();
    }
  }
}

/***************************************************************************
* The following simply call ADODB functions, documented in adodb.inc.php
***************************************************************************/

// takes the recordset object and returns a single row as a 1D array
function db_make_1D_array (&$db_record_set) {
  return $db_record_set->fields;
}

// takes the recordset object and returns multiple rows as a 2D array
function db_make_2D_array (&$db_record_set) {
  return $db_record_set->GetArray ();
}

// takes the recordset object and returns a formatted HTML drop-down list
function db_get_menu (&$db_record_set, $name, $firstItem, $selected) {
  return $db_record_set->GetMenu ($name, $selected, $firstItem);
}

// takes the recordset object and returns the number of records in it
function db_rows_returned ($db_record_set) {
  return $db_record_set->RecordCount ();
}

?>
