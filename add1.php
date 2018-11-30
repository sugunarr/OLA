<?php

/***************************************************************************
* ONLINE LIBRARY APPLICATION (OLA)               http://ola.sourceforge.net/
* (c) 2002 S. Rawlinson and N. Flear    Licenced under GPL (see licence.txt)
****************************************************************************
* add1.php - version 2.0
* - a form to add a new resource to the library (data entry)
***************************************************************************/


require_once ("standard.inc.php");

// check variables and url parameters
is_admin();
//PN check_security ();

// register state information;
// prevents a resource from being added multiple times
// when the user presses the back button or reloads the page
if (empty ($errormsg)) {
  // session_register ("ADD_RESOURCE");
  $_SESSION["ADD_RESOURCE"]="ADD_RESOURCE";
  $ADD_RESOURCE = 1;
}

// print page
if (empty ($errormsg)) {
  $location_list = get_list ("location", "location", "", "Main St");
  $media_list = get_list ("media", "media", "", "Book");
  $status_list = get_list ("status", "status", "", "On Shelf");
  $subject_list = get_list ("subject", "subject", "", "uncategorized");

  $list = array (
    "LOCATIONLIST" => $location_list,
    "MEDIALIST" => $media_list,
    "STATUSLIST" => $status_list,
    "SUBJECTLIST" => $subject_list
  );

  $output = simple_tpl ("add1.tpl", $list);
}
output_html ("Add Resource", $output);

?>
