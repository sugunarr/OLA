<?php

/***************************************************************************
* ONLINE LIBRARY APPLICATION (OLA)               http://ola.sourceforge.net/
* (c) 2002 S. Rawlinson and N. Flear    Licenced under GPL (see licence.txt)
****************************************************************************
* export.php - version 2.0
* - a tool to export the database tables in text format
***************************************************************************/


require_once ("standard.inc.php");

// check variables and url parameters
check_security ();
check_param_empty ();
check_param ("table");

// consult database and print page
if (empty ($errormsg)) {
  echo "<html><pre>\n";
	
  // export the loan table
  if ($HTTP_GET_VARS["table"] == "loan") {

    // write out field headers
    echo "loan_id, resource_id, date_time, person_name, person_contact_info, is_returned, is_lost, comments\n";
    
    $sql = "SELECT * FROM loan";
    $rs = get_recordset ($sql);

    if (empty ($errormsg)) {
      $loan = db_make_2D_array ($rs);
    }

    // build table
    if (empty ($errormsg)) {
      $tpl = new FastTemplate ("tpl");
      $tpl->define (array ("table" => "export_loan.tpl")); 
    
      while (list ($key, $val) = each ($loan)) {

        $tpl->assign (array (
          "LOANID" => $val["loan_id"],
          "RESID" => $val["resource_id"],
          "DATE" => $val["date_time"],
          "NAME" => $val["person_name"],
          "CONTACT" => $val["person_contact_info"],
          "RETURNED" => $val["is_returned"],
          "LOST" => $val["is_lost"],
          "COMMENTS" => $val["comments"]
        ));

        $tpl->parse ("CONTENT", ".table");
      }
      $tpl->FastPrint ();
    }
  }

  // export the resource table
  else if ($HTTP_GET_VARS["table"] == "resource") {

    // write out field headers
    echo "resourse_id, location, media, status, subject, title, author, year, isbn, comments, date_acquired, donated_by\n";

    $sql = "SELECT * FROM resource";
    $rs = get_recordset ($sql);

    if (empty ($errormsg)) {
      $loan = db_make_2D_array ($rs);
    }

    // build table
    if (empty ($errormsg)) {
      $tpl = new FastTemplate ("tpl");
      $tpl->define (array ("table" => "export_resource.tpl")); 
	    
      while (list ($key, $val) = each ($loan)) {

        $tpl->assign (array (
          "RESID" => $val["resource_id"],
          "LOCATION" => $val["location"],
          "MEDIA" => $val["media"],
          "STATUS" => $val["status"],
          "SUBJECT" => $val["subject"],
          "TITLE" => $val["title"],
          "AUTHOR" => $val["author"],
          "YEAR" => $val["year"],
          "ISBN" => $val["isbn"],
          "COMMENTS" => $val["comments"],
          "ACQUIRED" => $val["date_acquired"],
          "DONATED" => $val["donated_by"]
        ));

        $tpl->parse ("CONTENT", ".table");
      }
      $tpl->FastPrint ();
    }
  }

  else {
    $errormsg .= "Error: Url must contain table=loan/resource.";
  }
  echo "</pre></html>";
}

// output error message on error
if (!empty ($errormsg)) {
    echo ("<p class=\"class\">\n$errormsg\n</p>");
}

?>
