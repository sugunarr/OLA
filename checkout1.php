<?php

/***************************************************************************
* ONLINE LIBRARY APPLICATION (OLA)               http://ola.sourceforge.net/
* (c) 2002 S. Rawlinson and N. Flear    Licenced under GPL (see licence.txt)
****************************************************************************
* checkout1.php - version 2.0
* - a form to sign-out a resource from the library (data entry)
***************************************************************************/


require_once ("standard.inc.php");

// check variables and url parameters
check_security ();
check_param_empty ();
check_param ("id");

// consult database
if (empty ($errormsg)) {
  $sql = "SELECT * FROM resource WHERE resource_id = " . $HTTP_GET_VARS["id"];
  $rs = get_recordset ($sql);
}
if (empty ($errormsg)) {
  $resource = db_make_1D_array ($rs);
}

// print page
if (empty ($errormsg)) {
  $due_date = date ("F j, Y", mktime (0, 0, 0, date ("m"), date ("d") + LOAN_PERIOD, date ("Y")));
  $list = array (
    "RESID" => $HTTP_GET_VARS["id"],
    "TITLE" => $resource["title"],
    "AUTHOR" => $resource["author"],
    "DUEDATE" => $due_date
  );
  $output = simple_tpl ("checkout1.tpl", $list);
}
output_html ("Checkout Resource", $output);

?>
