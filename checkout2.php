<?php

/***************************************************************************
* ONLINE LIBRARY APPLICATION (OLA)               http://ola.sourceforge.net/
* (c) 2002 S. Rawlinson and N. Flear    Licenced under GPL (see licence.txt)
****************************************************************************
* checkout2.php - version 2.0
* - a form to sign-out a resource from the library (database commit)
***************************************************************************/


require_once ("standard.inc.php");

// check variables and url parameters
check_security ();
check_param_empty ();
check_param ("id");
check_param ("person_name");
check_param ("person_contact_info");

// consult database;
// check status of resource to ensure it can be checked out
if (empty ($errormsg)) {
  $sql = "SELECT status FROM resource WHERE resource_id=" . $HTTP_GET_VARS["id"];
  $rs1 = get_recordset ($sql);
}
if (empty ($errormsg)) {
  $status = db_make_1D_array ($rs1);
}

if (empty ($errormsg)) {
  if ($status["status"] != "On Shelf") {
    $errormsg = "Error: This resource cannot be checked out.<br>";
  }
}

// update record in database
if (empty ($errormsg)) {
  $sql = "UPDATE resource SET status = \"On Loan\"
    WHERE resource_id = " . $HTTP_GET_VARS["id"];
  $rs2 = get_recordset ($sql);
}

// create record in database
if (empty ($errormsg)) {
  $sql = "INSERT INTO loan
    (resource_id, date_time, person_name, person_contact_info, comments)
    VALUES ( ";
  $sql .= $HTTP_GET_VARS["id"] . ", ";
  $sql .= "NOW(), ";
  $sql .= "\"" . $HTTP_GET_VARS["person_name"] . "\", ";
  $sql .= "\"" . $HTPP_GET_VARS["person_contact_info"] . "\", ";
  $sql .= "\"" . $HTTP_GET_VARS["comments"] . "\")";	
  $rs3 = get_recordset ($sql);
}

// print page
if (empty ($errormsg)) {
  $due_date = date ("F j, Y", mktime (0, 0, 0, date ("m"), date ("d") + LOAN_PERIOD, date ("Y")));
  $list = array (
    "LOANPERIOD" => LOAN_PERIOD,
    "RETURNDATE" => $due_date
  );
  $output = simple_tpl ("checkout2.tpl", $list);
}
output_html ("Checkout Resource", $output);

?>
