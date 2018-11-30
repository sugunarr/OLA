<?php

/***************************************************************************
* ONLINE LIBRARY APPLICATION (OLA)               http://ola.sourceforge.net/
* (c) 2002 S. Rawlinson and N. Flear    Licenced under GPL (see licence.txt)
****************************************************************************
* checkin.php - version 2.0
* - signs-in a returned resource to the library (database commit)
***************************************************************************/


require_once ("standard.inc.php");
// check variables and url parameters
 
check_security ();
check_param_empty ();
check_param ("id");
check_param ("status");

// check status of loan to ensure it can be returned
if (empty ($errormsg)) {
  if (($HTTP_GET_VARS["status"] != "lost") && ($HTTP_GET_VARS["status"] != "returned")) {
    $errormsg .= "Error: Status can only be set to lost or returned using this link.<br>";
  }
}

// consult database
if (empty ($errormsg)) {
  $sql = "SELECT resource_id, (is_returned + is_lost) AS status FROM loan
    WHERE loan_id=" . $HTTP_GET_VARS["id"];
  $rs1 = get_recordset ($sql);
}
if (empty ($errormsg)) {
  $loan = db_make_1D_array ($rs1);
}

if (empty ($errormsg)) {
  $resource_id = $loan["resource_id"];
  if (0 != $loan["status"]) {
    $errormsg = "Error: This resource has been recorded as returned/lost already.";
  }
}

// update records in database
if (empty ($errormsg)) {
  $sql = "UPDATE resource SET status = ";
  if ($HTTP_GET_VARS["status"] == "lost")
    $sql .= "\"Lost\" ";
  else
    $sql .= "\"On Shelf\" ";
  $sql .= "WHERE resource_id = " . $resource_id;
  $rs2 = get_recordset ($sql);
}

if (empty ($errormsg)) {
  $sql = "UPDATE loan SET ";
  if ($HTTP_GET_VARS["status"] == "lost") {
    $sql .= "is_lost = 1 ";
    $return_type = "Lost";
  }
  else {
    $sql .= "is_returned = 1 ";
    $return_type = "Returned";
  }
  $sql .= "WHERE loan_id = " . $HTTP_GET_VARS["id"];
  $rs3 = get_recordset ($sql);
}

// print page
if (empty ($errormsg)) {
  $list = array ("RETURNTYPE" => $return_type);
  $output = simple_tpl ("checkin.tpl", $list);
}
output_html ("Return Resource", $output);

?>
