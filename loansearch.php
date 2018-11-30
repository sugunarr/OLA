<?php

/***************************************************************************
* ONLINE LIBRARY APPLICATION (OLA)               http://ola.sourceforge.net/
* (c) 2002 S. Rawlinson and N. Flear    Licenced under GPL (see licence.txt)
****************************************************************************
* loansearch.php - version 2.0
* - displays search results of the loan table
***************************************************************************/


require_once ("standard.inc.php");

// check variables and url parameters
check_security ();
check_param_empty ();
check_param ("order");

// consult database
if (empty ($errormsg)) {
  $sql = "SELECT loan.loan_id,
    loan.person_name,
    DATE_FORMAT(loan.date_time, '%M %e, %Y') AS loan_date,
    DATE_FORMAT(DATE_ADD(loan.date_time, INTERVAL " . LOAN_PERIOD . " DAY),'%M %e, %Y') AS due_date,
    resource.title
    FROM loan, resource
    WHERE loan.resource_id = resource.resource_id AND
    NOT loan.is_returned  AND
    NOT loan.is_lost ";
  if ("date" == $HTTP_GET_VARS["order"]) {
    $sql .= "ORDER BY loan.date_time ASC";
  }
  else if ("title" == $HTTP_GET_VARS["order"]) {
    $sql .= "ORDER BY resource.title ASC";
  }
  else {
    $sql .= "ORDER BY loan.person_name";
  }
  $rs = get_recordset ($sql);
}
if (empty ($errormsg)) {
  $result = db_make_2D_array ($rs);
}

// print page
if (empty ($errormsg)) {

  $tpl = new FastTemplate ("tpl");
  $tpl->define (array (
    "row" => "loansearch_row.tpl",
    "table" => "loansearch.tpl"));

  while (list ($key, $val) = each ($result)) {

    $name = $val["person_name"];
    $loandate = $val["loan_date"];
    $duedate = $val["due_date"];
    $title = $val["title"];
    $view = "HREF=\"loanview.php?id=" . $val["loan_id"] . "\"";

    // alternate colour in table
    if (0 == ($key % 2)) {
      $row_colour = "type2";  // light grey
    }
    else {
      $row_colour = "type1";  // white
    }

    $tpl->assign (array (
      "NAME" => $name,
      "LOANDATE" => $loandate,
      "DUEDATE" => $duedate,
      "TITLE" => $title,
      "VIEW" => $view,
      "COLOUR" => $row_colour
    ));

    $tpl->parse ("ROWS", ".row");
  }
  $tpl->parse ("CONTENT", "table");
  $output = $tpl->fetch ("CONTENT");
}
output_html ("Resources on Loan", $output);

?>
