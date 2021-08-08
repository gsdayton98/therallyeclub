<?php
// STEP 1
// Present the user with a list of existing rallyes or create a new one
//
// Get the name and date of the rallye
// create an empty entry in ars_rallye_base

include "config.php";

$_SESSION['Password'] = "";
$RallyeID = CHTTPVars::GetValue("RallyeID");


if(!CHTTPVars::IsEmpty("action"))
{
	$action = CHTTPVars::GetValue("action");
	
	switch(strtolower($action))
	{
		case "scroll board":
			redirect("scrollerscore.php?RallyeID=$RallyeID");
			break;

		case "score board":
			redirect("scoreboard.php?RallyeID=$RallyeID");
			break;

		case "import":
			// store the file in a temp and then call import
			$TmpFile = tempnam(@$_APPENV['tmpdir'], "ARS");
			if(move_uploaded_file($_FILES['import']['tmp_name'], $TmpFile)) 
			{
				redirect("import.php?import=$TmpFile");
			}
			break;

		case "score":
			$RallyeID = CHTTPVars::GetValue("RallyeID");
			$Password = CHTTPVars::GetValue("Password");
			$_SESSION['Password'] = trim($Password);
			redirect("score.php?RallyeID=$RallyeID");
			break;

		case "configure":
			$RallyeID = CHTTPVars::GetValue("RallyeID");
			$Password = CHTTPVars::GetValue("Password");
			$_SESSION['Password'] = trim($Password);
			
			redirect("configure.php?RallyeID=$RallyeID");
			break;

		case "register":
			$RallyeID = CHTTPVars::GetValue("RallyeID");
			
			redirect("register2.php?RallyeID=$RallyeID");
			break;

		case "create":
			$Password1 = ""; // ensure they are empty
			$Password2 = "";

			$RallyeName = CHTTPVars::GetValue("RallyeName");
			$RallyeName = trim($RallyeName);
			
			$RallyeDate = CHTTPVars::GetValue("RallyeDate");
			$RallyeDate = Date("Y-m-d",strtotime($RallyeDate)); // convert to ISO format for database

			$RallyeMaster = CHTTPVars::GetValue("RallyeMaster");
			$RallyeMaster = trim($RallyeMaster);


			if($RallyeName == "" || $RallyeMaster == "" || trim($Password1) != trim($Password2))
			{
				break;
 			}

			$_SESSION['Password'] = trim($Password1);
 			
			$strSQL="INSERT INTO ars_rallye_base (Password, RallyeName, RallyeDate, RallyeMaster) Values(PASSWORD('$Password1'), '$RallyeName', '$RallyeDate', '$RallyeMaster')";
			$oDB->Query($strSQL);
			$RallyeID = $oDB->GetLastInsertID();
			
			redirect("configure.php?RallyeID=$RallyeID");
			break;
			
		default:
			redirect("error.php");
	}
}


$strSQL="SELECT * FROM ars_rallye_base ORDER BY RallyeDate DESC, RallyeName";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray("RallyeID, RallyeName, RallyeDate, RallyeMaster");


foreach($arr as $data)
{
	list($lRallyeID, $lRallyeName, $lRallyeDate, $lRallyeMaster) = $data;
	$arrExistingRallyes[$lRallyeID] = "$lRallyeName - $lRallyeDate ($lRallyeMaster)";
}


?>
<HTML>
	<HEAD>
		<TITLE>ARS - Login</TITLE>
		<link rel="stylesheet" href="stylesheet.css">
	</HEAD>
	
	<BODY>
		<CENTER>
		<FONT SIZE=+2>
			<B>Advanced Rallye Score</B><BR><BR>
		</FONT>
		</CENTER>
		<FORM METHOD=POST ENCTYPE="multipart/form-data">
		<FONT SIZE=+1><B>Load an existing rallye:</B></FONT><BR>
		&nbsp;&nbsp;&nbsp;<B>Rallye:</B> 
<?
		SelectOption("RallyeID",$arrExistingRallyes,$RallyeID);
?>
		<BR>
		&nbsp;&nbsp;&nbsp;<B>Password:</B> <INPUT TYPE=PASSWORD NAME=Password SIZE=20><BR>
		<INPUT NAME=action TYPE=SUBMIT VALUE="Score"> 
		<INPUT NAME=action TYPE=SUBMIT VALUE="Configure">
		<INPUT NAME=action TYPE=SUBMIT VALUE="Register">

		<INPUT NAME=action TYPE=SUBMIT VALUE="Score Board">
		<INPUT NAME=action TYPE=SUBMIT VALUE="Scroll Board">
<BR><BR>
		<FONT SIZE=+1><B>or create a new one:</B></FONT><BR>
		&nbsp;&nbsp;&nbsp;<B>Rallye Name:</B><INPUT NAME=RallyeName VALUE="<?=HTMLEscape($RallyeName);?>" TYPE=TEXT><BR>
		&nbsp;&nbsp;&nbsp;<B>Rallye Date:</B><INPUT NAME=RallyeDate VALUE="<?=HTMLEscape($RallyeDate);?>" TYPE=TEXT><BR>
		&nbsp;&nbsp;&nbsp;<B>Rallye Mstr:</B><INPUT NAME=RallyeMaster VALUE="<?=HTMLEscape($RallyeMaster);?>" TYPE=TEXT><BR>
		<INPUT NAME=action TYPE=SUBMIT VALUE=Create>
		<BR>
		<BR>
		<FONT SIZE=+1><B>or import an ARS file:</B></FONT><BR>
		<INPUT TYPE="HIDDEN" NAME="MAX_FILE_SIZE" VALUE="1048576">
		<INPUT NAME=import TYPE=FILE><BR>
		<INPUT NAME=action TYPE=SUBMIT VALUE=Import>
		</FORM>

		<BR><BR><BR><BR><BR>
		<FONT SIZE=-2>This software is opensource and public domain.<BR>
		Send email to <A HREF=mailto:nick@stefanisko.net>nick@stefanisko.net</A> for a current copy.<BR>
		*AMP environment required for use.</FONT>
	</BODY>
</HTML>
