<?php

/***************************************************************************
* ONLINE LIBRARY APPLICATION (OLA)               http://ola.sourceforge.net/
* (c) 2002 S. Rawlinson and N. Flear    Licenced under GPL (see licence.txt)
****************************************************************************
* standard.inc.php - version 2.0
* - provides functions central to the program
***************************************************************************/

include "class.FastTemplate.php";

/***************************************************************************
* GLOBAL VARIABLES AND INITIAL SETTINGS
****************************************************************************
* Preferences
***************************************************************************/

  // number of days patrons can borrow materials
  define ("LOAN_PERIOD", 21);

  // number of rows per page in search results
  define ("ROWS_PER_PAGE", 25);

  // user login
  define ("ADMIN_USR", "admin");

  // encrypted user password (pick a new one using set_password.php);
  // currently set to "password"
  define ("ADMIN_PWD", "4b86bf0ec1640d7d9ebf798e9fc8e794");

  // turn off system-generated errors
  error_reporting (1);

/***************************************************************************
* Include files
***************************************************************************/

  // Allows Database sublayer to be abstracted from the main code
  require_once ("/home/v-excel/www/ola/lib/db.inc.php");

  // Allows HTML pages to be stored as templates seperate from the code
  // require_once ("lib/tpl.inc.php");

/***************************************************************************
* Initial settings
***************************************************************************/

  // Define the session variable ADMIN
  // (remembered across pages)
  session_start ();
  session_register ("ADMIN");

  // Enable gzip compression of page
  ob_start ("ob_gzhandler");

  // Initialize the global error handler to null
  $errormsg = "";

  // Check if the parameter list contains a flag to login or logout
  check_login ();
  check_logout ();

/***************************************************************************
* FUNCTIONS
****************************************************************************
* Functions that check variables and url parameters
***************************************************************************/

function check_login () {
  global $HTTP_POST_VARS, $ADMIN, $errormsg;

  if (isset ($HTTP_POST_VARS) && !empty ($HTTP_POST_VARS)) {

    // check username and password
    if (($HTTP_POST_VARS["login_user"] == ADMIN_USR) &&
        ($HTTP_POST_VARS["login_password"] == ADMIN_PWD)) {

      $ADMIN = 1; // basic admin rights
    }
    else {
      $errormsg .= "Error: Invalid userid or password.<br>\n";
      $ADMIN = 0;
      session_destroy ();
      unset ($ADMIN);
    }
  }
}

function check_logout () {
  global $HTTP_GET_VARS, $ADMIN;

  if (isset ($HTTP_GET_VARS) && !empty ($HTTP_GET_VARS)) {
    if (exists_param ("action") && $HTTP_GET_VARS["action"]=="logout") {
      $ADMIN = 0;
      session_destroy ();
      unset ($ADMIN);
    }
  }
}

function check_security () {
  global $ADMIN, $errormsg;

  if ((!isset ($ADMIN)) || (!$ADMIN > 0))
    $errormsg .= "Error: You must be logged in to access this feature.<br>";
}

function is_admin () {
  global $ADMIN;

  if ((isset ($ADMIN)) && ($ADMIN > 0)) {
    $errormsg .= "You are the admin, baby<br>";
    return TRUE;
  }
  else {
    return FALSE;
  }
}

function check_param_empty () {
  global $HTTP_GET_VARS, $errormsg;

  if ((!isset ($HTTP_GET_VARS)) || (empty ($HTTP_GET_VARS)))
    $errormsg .= "Error: The specified url is missing parameters.<br>";
}

function check_param ($name) {
  global $HTTP_GET_VARS, $errormsg;

  if ((!isset ($HTTP_GET_VARS[$name]) || empty ($HTTP_GET_VARS[$name])))
    $errormsg .= "Error: \"" . $name . "\" cannot be blank. It is a required field.<br>";
}

function exists_param ($name) {
  global $HTTP_GET_VARS;

  if ((isset ($HTTP_GET_VARS[$name]) && !empty ($HTTP_GET_VARS[$name])))
    return TRUE;
  else
    return FALSE;
}

/***************************************************************************
* Functions that perform database functions
***************************************************************************/

function get_recordset ($sql, $num=-1, $loc=-1) {
	echo "GETTING THE SQL RECORDSET $sql";
  global $errormsg;

  $dbc = db_connect ();
  if ($dbc == 0) {
    $errormsg .= "Error: Cannot connect to database.<br>";
    return FALSE;
  }

  $rs = db_query ($dbc, $sql, $num, $loc);
  if ($rs == 0) {
    $errormsg .= "Error: Query was unsucessful.<br>";
    return FALSE;
  }

    // only warn of empty result set with SELECT statements;
    // other SQL statements return nothing by default
  if (substr ($sql, 0, 6) == "SELECT" && db_rows_returned ($rs) == 0) {
    $errormsg .= "There are no records to display.<br>";
    return FALSE;
  }

  return $rs;
}

function get_list ($field, $name, $firstItem, $selected) {
  $sql = "SELECT DISTINCT " . $field . ", " . $field . " FROM resource ORDER BY " . $field . " ASC";
  $rs = get_recordset ($sql);
  if ($rs != FALSE)
    return db_get_menu ($rs, $name, $firstItem, $selected);
  else
    return FALSE;
}

/***************************************************************************
* Functions that generate HTML and print the page
***************************************************************************/

// create a HTML string, $tpl, from a HTML template, $filename,
// and substitute all $variables specified in the template
function simple_tpl ($filename, $variables) {
  echo "INTO THE FAST TEMPLATE with $filename AND $variables";
  $tpl = new FastTemplate("/home/v-excel/www/ola/tpl/");
  # $tpl = new FastTemplate ("/ola/tpl/");
  echo "dddd";
  $tpl->define (array ("file_list" => $filename));
  $tpl->assign ($variables);
  $tpl->parse ("parsed", "file_list");
  return $tpl->fetch ("parsed");
}

// build the HTML page from the header, menubar, viewport, and footer;
// substitue an error for the viewport if encountered
function output_html ($title, $viewport) {
  global $errormsg;

  $DOC_TITLE = $title;
  include ("tpl/header.tpl");

  include ("menubar.inc.php");

  // display main viewport or error message
  echo ("              <td valign='top'>\n");
  if (!empty ($errormsg))
    echo ("<p class=\"error\">$errormsg</p>\n");
  else
    echo ($viewport);
  echo ("              </td>\n");

  include ("tpl/footer.tpl");
}

?>
