<?php

/***************************************************************************
* ONLINE LIBRARY APPLICATION (OLA)               http://ola.sourceforge.net/
* (c) 2002 S. Rawlinson and N. Flear    Licenced under GPL (see licence.txt)
****************************************************************************
* view.php - version 2.0
* - displays full record of one resource
***************************************************************************/


require_once ("standard.inc.php");

// check variables and url parameters
check_param_empty ();
check_param ("id");

// consult database
if (empty ($errormsg)) {
  $sql = "SELECT * FROM resource WHERE resource_id = " . $_GET["id"];
  $rs = get_recordset ($sql);
}
if (empty ($errormsg)) {
  $resource = db_make_1D_array ($rs);
}

// print page
if (empty ($errormsg)) {
  $admin_menu = "";
  $checkout = "";
  $update_link = "";

  // Display extra fields if Admin
  if (is_admin ()) {
    $acquired = $resource["date_acquired"];
    $donated_by = $resource["donated_by"];
    if ($acquired == "")
      $acquired = "&nbsp;";
    if ($donated_by == "")
      $donated_by = "&nbsp;";

    // only allow checkout for books on shelf
    if ("On Shelf" == $resource["status"]) {
      $checkout_link = "<a href=\"checkout1.php?id=" . $id . "\">Check-out Resource</a>";
    }
    else {
      $checkout_link = "You can only check-out books that have an 'on shelf' status.";
    }

    $update_link = "<a href=\"update1.php?id=" . $id . "\">Update Record</a>";

    $list = array (
      "ACQUIRED" => $acquired,
      "DONATED" => $donated_by
    );
    $admin_menu = simple_tpl ("view_admin.tpl", $list);
  }

  // -- should replace blank with &nbsp;
  $list = array (
    "RESID" => $_GET["id"],
    "LOCATION" => $resource["location"],
    "MEDIA" => $resource["media"],
    "STATUS" => $resource["status"],
    "SUBJECT" => $resource["subject"],
    "TITLE" => $resource["title"],
    "AUTHOR" => $resource["author"],
    "YEAR" => $resource["year"],
    "ISBN" => $resource["isbn"],
    "COMMENTS" => $resource["comments"],
    "KEYWORDS" => $resource["keywords"],
    "ADMINITEMS" => $admin_menu,
    "UPDATE" => $update_link,
    "CHECKOUT" => $checkout_link
  );
  $output = simple_tpl ("view.tpl", $list);
}
output_html ("View Resource", $output);

?>
