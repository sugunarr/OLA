<?php

/***************************************************************************
* ONLINE LIBRARY APPLICATION (OLA)               http://ola.sourceforge.net/
* (c) 2002 S. Rawlinson and N. Flear    Licenced under GPL (see licence.txt)
****************************************************************************
* search.php - version 2.0
* - displays search results of the resource table
***************************************************************************/


require_once ("standard.inc.php");

// check variables and url parameters
check_param_empty ();
check_param ("submit");

if (empty ($errormsg)) {
  if ($_GET["submit"] == "Search") {
    check_param ("search_type");
    check_param ("search_text");
  }
  else if ($_GET["submit"] == "Browse") {
    check_param ("browse_media");
    check_param ("browse_subject");
  }
  else {
    $errormsg .= "Error: Bad url format. Incorrect submit=xxx.<br>";
  }
}

// consult database
if (empty ($errormsg)) {

  // set $pos to form
  $pos = 0;
  if (exists_param ("pos") && $_GET["pos"] % ROWS_PER_PAGE == 0) {
    $pos = (int) $_GET["pos"];
  }

  $sql = "SELECT * FROM resource ";

  // if Serach
  if ($_GET["submit"] == "Search") {
    $sql .= "WHERE ";
    if ($_GET["search_type"] == "title") {
      $sql .= "title LIKE '%" . $_GET["search_text"] . "%' ";
      $sql .= "ORDER BY title ASC";
    }
    else if ($_GET["search_type"] == "author") {
      $sql .= "author LIKE '%" . $_GET["search_text"] . "%' ";
      $sql .= "ORDER BY title ASC";
    }

  // if Browse
  }
  else if ($_GET["submit"] == "Browse") {
    if ($_GET["browse_media"] != "All") {
      $sql .= "WHERE media = '" . $_GET["browse_media"] . "' ";
      $sql .= "ORDER BY title ASC";
    }
    else if ($_GET["browse_subject"] != "All") {
      $sql .= "WHERE subject = '" . $_GET["browse_subject"] . "' ";
      $sql .= "ORDER BY title ASC";
    }
    else {
      $sql .= "ORDER BY subject, title ASC";
    }
  }

  $rs = get_recordset ($sql, ROWS_PER_PAGE, $pos);
}
if (empty ($errormsg)) {
  $result = db_make_2D_array ($rs);
}


// print page
if (empty ($errormsg)) {
  global $pos;

  $tpl = new FastTemplate ("tpl");
  $tpl->define (array (
      "row" => "search_row.tpl",
      "table" => "search.tpl"));

  while (list ($key, $val) = each ($result)) {

    // define output keys
    $id = $val["resource_id"];
    $media = $val["media"];
    $subject = $val["subject"];
    $title =  $val["title"];
    $author = $val["author"];
    $year = $val["year"];

    // alternate colour in table
    if (0 == ($key % 2)) {
      $row_colour = "type2";  // light grey
    }
    else {
      $row_colour = "type1";  // white
    }

    if ($media == "") $media = "&nbsp;";
    if ($subject == "") $subject = "&nbsp;";
    if ($title == "") $title = "&nbsp;";
    if ($author == "") $author = "&nbsp;";
    if ($year == "") $year = "&nbsp;";

    // url to view
    $view = "href=\"view.php?id=" . $id . "\"";

    $tpl->assign (array (
      "MEDIA" => $media,
      "SUBJECT" => $subject,
      "TITLE" => $title,
      "AUTHOR" => $author,
      "YEAR" => $year,
      "VIEW" => $view,
      "COLOUR" => $row_colour));
    $tpl->parse ("ROWS", ".row");
  }

  // remove the old &pos= from the query string
  // (assumes it is at the end and nothing after it!!)
  if (intval (strpos (getenv ("QUERY_STRING"), "&pos")) != 0) {
    $query = substr (getenv ("QUERY_STRING"), 0,
        strpos (getenv ("QUERY_STRING"), "&pos"));
  }
  else {
    $query = getenv ("QUERY_STRING");
  }

  if ($pos >= ROWS_PER_PAGE) {
    $prev = "<a href=\"search.php?" . $query . "&pos=" . ($pos - ROWS_PER_PAGE) . "\" class=\"rvts5\"><<< Previous</a>";
  }
  else {
    $prev = "&nbsp;";
  }

  if (count ($result) == ROWS_PER_PAGE) {
    $next = "<a href=\"search.php?" . $query . "&pos=" . ($pos + ROWS_PER_PAGE) . "\" class=\"rvts5\">Next >>></a>";
  }
  else {
    $next = "&nbsp;";
  }

  $tpl->assign (array ("NEXT" => $next, "PREV" => $prev));
  $tpl->parse ("CONTENT", "table");

  $output = $tpl->fetch ("CONTENT");
}
output_html ("Search", $output);

?>
