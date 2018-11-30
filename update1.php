<?php

/***************************************************************************
* ONLINE LIBRARY APPLICATION (OLA)               http://ola.sourceforge.net/
* (c) 2002 S. Rawlinson and N. Flear    Licenced under GPL (see licence.txt)
****************************************************************************
* update1.php - version 2.0
* - a form to update a resource in the library (data entry)
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

  // get subject select menu with correct entry highlighted
  $location_list = get_list ("location", "location", "", $resource["location"]);
  $media_list = get_list ("media", "media", "", $resource["media"]);
  $status_list = get_list ("status", "status", "", $resource["status"]);
  $subject_list = get_list ("subject", "subject", "", $resource["subject"]);
  $list = array (
    "RESID" => $HTTP_GET_VARS["id"],
    "LOCATIONLIST" => $location_list,
    "MEDIALIST" => $media_list,
    "STATUSLIST" => $status_list,
    "TITLE" => htmlspecialchars ($resource["title"]),
    "AUTHOR" => htmlspecialchars ($resource["author"]),
    "SUBJECTLIST" => $subject_list,
    "ISBN" => htmlspecialchars ($resource["isbn"]),
    "YEAR" => htmlspecialchars ($resource["year"]),
    "COMMENTS" => htmlspecialchars ($resource["comments"]),
    "ACQUIRED" => htmlspecialchars ($resource["date_acquired"]),
    "DONATED" => htmlspecialchars ($resource["donated_by"])
  );
  $output = simple_tpl ("update1.tpl", $list);
}
output_html ("Update Resource", $output);

?>
