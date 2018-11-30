<?php

/***************************************************************************
* ONLINE LIBRARY APPLICATION (OLA)               http://ola.sourceforge.net/
* (c) 2002 S. Rawlinson and N. Flear    Licenced under GPL (see licence.txt)
****************************************************************************
* add2.php - version 2.0
* - a form to add a new resource to the library (database commit)
***************************************************************************/


require_once ("standard.inc.php");

// check variables and url parameters
//PN check_security ();
check_param_empty ();
check_param ("title");
check_param ("subject");

if (empty ($errormsg)) {
  // loan ADD_RESOURCE session variable
  // session_register ("ADD_RESOURCE");
  $_SESSION["ADD_RESOURCE"]="ADD_RESOURCE";
  $ADD_RESOURCE = 1;
  // check that first time adding resource from ADD_RESOURCE status
  if ($ADD_RESOURCE != 1) {
    $errormsg .= "Error: Cannot add resource more than once.<br>";
  }
}

// add record to database
if (empty ($errormsg)) {
  $sql = "INSERT INTO resource ";
  $sql .= "SET title = \"" . $_GET["title"] . "\"";
  $sql .= ", subject = \"" . $_GET["subject"] . "\"";

  if (exists_param ("location"))
    $sql .= ", location = \"" . $_GET["location"] . "\"";
  if (exists_param ("media"))
    $sql .= ", media = \"" . $_GET["media"] . "\"";
  if (exists_param ("status"))
    $sql .= ", status = \"" . $_GET["status"] . "\"";
  if (exists_param ("author"))
    $sql .= ", author = \"" . $_GET["author"] . "\"";
  if (exists_param ("year"))
    $sql .= ", year= \"" . $_GET["year"] . "\"";
  if (exists_param ("isbn"))
    $sql .= ", isbn = \"" . $_GET["isbn"] . "\"";
  if (exists_param ("comments"))
    $sql .= ", comments = \"" . $_GET["comments"] . "\"";
  if (exists_param ("date_acquired"))
    $sql .= ", date_acquired = \"" . $_GET["date_acquired"] . "\"";
  if (exists_param ("donated_by"))
    $sql .= ", donated_by = \"" . $_GET["donated_by"] . "\"";
  if (exists_param ("keywords"))
    $sql .= ", keywords = \"" . $_GET["keywords"] . "\"";

  $rs = get_recordset ($sql);
}

// print page
if (empty ($errormsg)) {
  $ADD_RESOURCE = 0;
  $list = array ("EMPTY" => "");
  $output = simple_tpl ("add2.tpl", $list);
}
output_html ("Add Resource", $output);

?>
