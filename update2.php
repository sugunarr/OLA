<?php

/***************************************************************************
* ONLINE LIBRARY APPLICATION (OLA)               http://ola.sourceforge.net/
* (c) 2002 S. Rawlinson and N. Flear    Licenced under GPL (see licence.txt)
****************************************************************************
* update2.php - version 2.0
* - a form to update resource in the library (database commit)
***************************************************************************/


require_once ("standard.inc.php");

// check variables and url parameters
check_security ();
check_param_empty ();
check_param ("id");
check_param ("title");
check_param ("subject");

// update record in database
if (empty ($errormsg)) {

  $sql = "UPDATE resource ";
  $sql .= "SET title = \"" . $HTTP_GET_VARS["title"] . "\"";
  $sql .= ", subject = \"" . $HTTP_GET_VARS["subject"] . "\"";

  if (exists_param ("location"))
    $sql .= ", location = \"" . $HTTP_GET_VARS["location"] . "\"";
  if (exists_param ("media"))
    $sql .= ", media = \"" . $HTTP_GET_VARS["media"] . "\"";
  if (exists_param ("status"))
    $sql .= ", status = \"" . $HTTP_GET_VARS["status"] . "\"";
  if (exists_param ("author"))
    $sql .= ", author = \"" . $HTTP_GET_VARS["author"] . "\"";
  if (exists_param ("year"))
    $sql .= ", year= \"" . $HTTP_GET_VARS["year"] . "\"";
  if (exists_param ("isbn"))
    $sql .= ", isbn = \"" . $HTTP_GET_VARS["isbn"] . "\"";
  if (exists_param ("comments"))
    $sql .= ", comments = \"" . $HTTP_GET_VARS["comments"] . "\"";
  if (exists_param ("date_acquired"))
    $sql .= ", date_acquired = \"" . $HTTP_GET_VARS["date_acquired"] . "\"";
  if (exists_param ("donated_by"))
    $sql .= ", donated_by = \"" . $HTTP_GET_VARS["donated_by"] . "\"";

  $sql .= " WHERE resource_id = " . $HTTP_GET_VARS["id"];
  $rs = get_recordset ($sql);
}

// print page
if (empty ($errormsg)) {
  $list = array ("ID" => $HTTP_GET_VARS["id"]);
  $output = simple_tpl ("update2.tpl", $list);
}
output_html ("Update Resource", $output);

?>
