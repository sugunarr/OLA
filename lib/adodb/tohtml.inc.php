<?php 
/*
V0.95 13 Mar 2001 (c) 2000, 2001 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under Lesser GPL library license. See License.txt.
*/ 
  
// specific code for tohtml
GLOBAL $gSQLMaxRows,$gSQLBlockRows;
	 
$gSQLMaxRows = 1000; // max no of rows to download
$gSQLBlockRows=20; // max no of rows per table block

// RecordSet to HTML Table
//------------------------------------------------------------
// Convert a recordset to a html table. Multiple tables are generated
// if the number of rows is > $gSQLBlockRows. This is because
// web browsers normally require the whole table to be downloaded
// before it can be rendered, so we break the output into several
// smaller faster rendering tables.
//
// $rs: the recordset
// $ztabhtml: the table tag attributes (optional)
// $zheaderarray: contains the replacement strings for the headers (optional)
//
//  USAGE:
//    include('adodb.inc.php');
//    ADOLoadCode('mysql');
//    $db = ADONewConnection();
//    $db->Connect('mysql','userid','password','database');
//    $rs = $db->Execute('select col1,col2,col3 from table');
//  rs2html($rs, 'BORDER=2', array('Title1', 'Title2', 'Title3'));
//    $rs->Close();
//
// RETURNS: number of rows displayed
function rs2html(&$rs,$ztabhtml='',$zheaderarray="")
{
$s ='';$rows=0;$docnt = false;
GLOBAL $gSQLMaxRows,$gSQLBlockRows;

	
	if (! $ztabhtml) $ztabhtml = "BORDER='1' WIDTH='98%'";
	else $docnt = true;
	$typearr = array();
        $ncols = $rs->FieldCount();
        $hdr = "<TABLE COLS=$ncols $ztabhtml>";
	for ($i=0; $i < $ncols; $i++) {	
		$field = $rs->FetchField($i);
		if ($zheaderarray) $fname = $zheaderarray[$i];
		else $fname = htmlspecialchars($field->name);	
		$typearr[$i] = $rs->MetaType($field->type,$field->max_length);
            
		if (empty($fname)) $fname = '&nbsp;';
		$hdr .= "<TH>$fname</TH>";
         }

	print $hdr;
	
       	while (!$rs->EOF) {
                $s .= "<TR>";
                for ($i=0; $i < $ncols; $i++) {
                        $type = $typearr[$i];
                        
			switch($type) {
			case 'T':
				$s .= "<TD>".$rs->UserTimeStamp($rs->fields[$i],"D d, M Y, h:i:s") ."&nbsp;</TD>";
				break;
			case 'D':
				$s .= "<TD>".$rs->UserDate($rs->fields[$i],"D d, M Y") ."&nbsp;</TD>";
				break;
			case 'I':
			case 'N':
				$s .= "<TD align=right>".htmlspecialchars(trim($rs->fields[$i])) ."&nbsp;</TD>";
				break;
			default:
				$s .= "<TD>".htmlspecialchars(trim($rs->fields[$i])) ."&nbsp;</TD>";
			}
                }
                $s .= "</TR>\n\n";
               
		$rows += 1;
		if ($rows >= $gSQLMaxRows) {
			$rows = "<p>Truncated at $gSQLMaxRows</p>";
			break;
		}

		$rs->MoveNext();
		
		// additional EOF check to prevent a widow header
	        if (!$rs->EOF && $rows % $gSQLBlockRows == 0) {
		
			//if (connection_aborted()) break;// not needed as PHP aborts script, unlike ASP
                        print $s . "</TABLE>\n\n";
                        $s = $hdr;
		}
        } // while
	
       	print $s."</TABLE>\n\n";

	if ($docnt) print "<H2>".$rows." Rows</H2>";
	
	return $rows;
 }
 

function arr2html(&$arr,$ztabhtml='',$zheaderarray='')
{
	if (!$ztabhtml) $ztabhtml = 'BORDER=1';
	
	$s = "<TABLE $ztabhtml>";//';print_r($arr);

	if ($zheaderarray) {
		$s .= '<TR>';
		for ($i=0; $i<sizeof($zheaderarray); $i++) {
			$s .= "<TD>{$zheaderarray[$i]}</TD>";
		}
		$s .= '</TR>';
	}
	
	for ($i=0; $i<sizeof($arr); $i++) {
		$s .= '<TR>';
		$a = &$arr[$i];
		if (is_array($a)) 
			for ($j=0; $j<sizeof($a); $j++) {
				$val = $a[$j];
				if (empty($val)) $val = '&nbsp;';
				$s .= "<TD>$val</TD>";
			}
		else if ($a) {
			$s .=  '<TD>'.$a.'</TD>';
		} else $s .= '<TD>&nbsp;</TD>';
		$s .= "</TR>\n";
	}
	$s .= '</TABLE>';
	print $s;
}
?>