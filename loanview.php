<?php

/***************************************************************************
* ONLINE LIBRARY APPLICATION (OLA)               http://ola.sourceforge.net/
* (c) 2002 S. Rawlinson and N. Flear    Licenced under GPL (see licence.txt)
****************************************************************************
* loanview.php - version 2.0
* - displays full record of one loan
***************************************************************************/


require_once ("standard.inc.php");

// check variables and url parameters
check_security ();
check_param_empty ();
check_param ("id");

// update record in database;
// update comment field if non-empty
if (empty ($errormsg)) {
  if (exists_param ("comments")) {
    $sql = "UPDATE loan
      SET comments = \"" . $HTTP_GET_VARS["comments"] . "\"
      WHERE loan_id = " . $HTTP_GET_VARS["id"];
    $rs = get_recordset ($sql);
  }
}

// consult database
if (empty ($errormsg)) {
  $sql = "SELECT loan.loan_id, loan.resource_id, loan.person_name, loan.person_contact_info,
    loan.comments,
    DATE_FORMAT(loan.date_time, '%M %e, %Y') AS loan_date,
    DATE_FORMAT(DATE_ADD(loan.date_time, INTERVAL " . LOAN_PERIOD . " DAY),'%M %e, %Y') AS due_date,
    resource.title, resource.author
    FROM loan, resource
    WHERE loan.resource_id = resource.resource_id AND
    loan.loan_id = " . $HTTP_GET_VARS["id"];
  $rs = get_recordset ($sql);
}
if (empty ($errormsg)) {
  $loan = db_make_1D_array ($rs);
}

// print page
if (empty ($errormsg)) {
  $list = array (
    "LOANID" => $HTTP_GET_VARS["id"],
    "TITLE" => htmlspecialchars ($loan["title"]),
    "AUTHOR" => htmlspecialchars ($loan["author"]),
    "RESID" => $loan["resource_id"],
    "LOANDATE" => $loan["loan_date"],
    "DUEDATE" => $loan["due_date"],
    "PERSONNAME" => htmlspecialchars ($loan["person_name"]),
    "PERSONCONTACT" => htmlspecialchars ($loan["person_contact_info"]),
    "COMMENTS" => htmlspecialchars ($loan["comments"])
  );
  $output = simple_tpl ("loanview.tpl", $list);
}
output_html ("View Loaned Resource", $output);

?>
