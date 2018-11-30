<?php

/***************************************************************************
* ONLINE LIBRARY APPLICATION (OLA)               http://ola.sourceforge.net/
* (c) 2002 S. Rawlinson and N. Flear    Licenced under GPL (see licence.txt)
****************************************************************************
* menubar.inc.php - version 2.0
* - creates the left menu that appears on every page
***************************************************************************/


## global $_GET;
global $_GET;

// print page

// Define default Login/Logout status line and admin menu

$login_status = "<a href=\"index.php?action=login\" class=\"blacklinks\">Volunteer Log In</a>";
$admin_menu = "";

if (is_admin ()) {

  // Change Login/Logout Status Line
  $login_status = "<p><a href=\"index.php?action=logout\" class=\"blacklinks\">Log out</a></p>";

  // include the admin menu
  $list = array ("EMPTY" => "");
  $admin_menu = simple_tpl ("menubar_admin.tpl", $list);
}

// For the form controls, decide which items need to be highlighted

// Browse box
$media_selected = "";
if (exists_param ("browse_media"))
  $media_selected = $_GET["browse_media"];

$subject_selected = "";
if (exists_param ("browse_subject"))
  $subject_selected = $_GET["browse_subject"];

// Search box
$search_text = "";
$title_checked = "";
$author_checked = "";
if (exists_param ("search_type")) {
  if ($_GET["search_type"] == "title")
    $title_checked = " checked";
  else if ($_GET["search_type"] == "author")
    $author_checked = " checked";
}
if (exists_param ("search_text"))
  $search_text = $_GET["search_text"];

// build drop-down menus from using database queries
$media_menu = get_list ("media", "browse_media", "<option class=\"data\" value=\"All\">All</option>", $media_selected);
$subject_menu = get_list ("subject", "browse_subject", "<option value=\"All\">All</option>", $subject_selected);

// store constants for the template
$list = array (
  "MEDIAMENU" => $media_menu,
  "SUBJECTMENU" => $subject_menu,
  "TITLECHECKED" => $title_checked,
  "AUTHORCHECKED" => $author_checked,
  "SEARCHTEXT" => $search_text,
  "ADMINSTATUS" => $login_status,
  "ADMINMENU" => $admin_menu
);

// build and output the HTML using a template
echo simple_tpl ("menubar.tpl", $list);

?>
