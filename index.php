<?php

/***************************************************************************
* ONLINE LIBRARY APPLICATION (OLA)               http://ola.sourceforge.net/
* (c) 2002 S. Rawlinson and N. Flear    Licenced under GPL (see licence.txt)
****************************************************************************
* index.php - version 2.0
* - the main entry point for the program
***************************************************************************/


require_once ("standard.inc.php");

// check variables and url parameters

// decide which (help/welcome) page to serve by default
// based on the type of user

if (is_admin ()) {
  $page = "help_librarian.tpl";
  $title = "Volunteer Help";
}
else {
  $page = "help_welcome.tpl";
  $title = "Welcome";
}

// override which page to serve
// based on the 'action' parameter, if present

if (exists_param ("action")) {
  if ($_GET["action"] == "welcome") {
    $page = "help_welcome.tpl";
    $title = "Welcome";
  }
  else if ($_GET["action"] == "help") {
    $page = "help_general.tpl";
    $title = "Help";
  }
  else if ($_GET["action"] == "login") {
    $page = "login.tpl";
    $title = "Login";
  }
  else if ($_GET["action"] == "lib") {
    $page = "help_librarian.tpl";
    $title = "Volunteer Help";
  }
}

// print page
$list = array ("EMPTY" => "");
$output = simple_tpl ($page, $list);
output_html ($title, $output);

?>
