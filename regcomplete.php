<?
include "config.php";

$RallyeID = CHTTPVars::GetValue("RallyeID");
$TestMode = intval(CHTTPVars::GetValue("TestMode"));
/**
if(!ScorePasswordCheck($RallyeID,@$_SESSION["Password"])) 
{
	print("redirect"); exit;
	redirect("index.php?RallyeID=$RallyeID");
}
**/

if(!IsSetStep(16,$RallyeID)) // have I completed the previous step?
	redirect("configdone.php?RallyeID=$RallyeID");

function myprint($str)
{
	print($str."\n");
	//print($str);
}


//TODO: this should really be databased
global $arrClasses;
$arrClasses[1] = "First Timer";
$arrClasses[2] = "Beginner";
$arrClasses[3] = "Novice";
$arrClasses[4] = "Senior";
$arrClasses[5] = "Expert";
$arrClasses[6] = "Master";


if(!CHTTPVars::IsEmpty("action"))
{
	$action = CHTTPVars::GetValue("action");

	switch(strtolower($action))
	{
		case "register":
			// Extract scoresheet from POST
			
			$CarNumber = intval(CHTTPVars::GetValue("CarNumber"));
			$CarClass = intval(CHTTPVars::GetValue("CarClass"));
			$DriverName = CHTTPVars::GetValue("DriverName");
			$NavgtrName = CHTTPVars::GetValue("NavgtrName");
			$PasngrName = CHTTPVars::GetValue("PasngrName");
			if(IntVal(CHTTPVars::GetValue("DClubCB")))
				$DClub = "TRC";
			else
				$DClub = trim(CHTTPVars::GetValue("DClub"));

			if(IntVal(CHTTPVars::GetValue("NClubCB")))
				$NClub = "TRC";
			else
				$NClub = trim(CHTTPVars::GetValue("NClub"));

			if(IntVal(CHTTPVars::GetValue("PClubCB")))
				$PClub = "TRC";
			else
				$PClub = trim(CHTTPVars::GetValue("PClub"));

			$DEmail = trim(CHTTPVars::GetValue("DEmail"));
			$NEmail = trim(CHTTPVars::GetValue("NEmail"));
			$PEmail = trim(CHTTPVars::GetValue("PEmail"));

			$dadd = intval(CHTTPVars::GetValue("DADD"));
			$nadd = intval(CHTTPVars::GetValue("NADD"));
			$padd = intval(CHTTPVars::GetValue("PADD"));
			//var_dump($PasngrName);
			
			
			$arrBreakDown = Register($RallyeID, $CarClass, $CarNumber, $DriverName, $NavgtrName, $PasngrName, $DClub, $NClub, $PClub, $DEmail, $NEmail, $PEmail, $dadd, $nadd, $padd);

			break;
		
	}
}

function Register($RallyeID, $CarClass, $CarNumber, $DriverName, $NavgtrName, $PasngrName, $DClub, $NClub, $PClub, $DEmail, $NEmail, $PEmail, $dadd, $nadd, $padd)
{
	global $oDB;

	$strSQL = "replace INTO `ars_scoresheet` (`RallyeID`, `CarNumber`, `CarClass`, `Driver`, `Navigator`, `Passenger`, `DClub`, `NClub`, `PClub`, `DEmail`, `NEmail`, `PEmail`, `dadd`, `nadd`, `padd`) VALUES ($RallyeID, $CarNumber, $CarClass, '$DriverName', '$NavgtrName', '$PasngrName', '$DClub', '$NClub', '$PClub', '$DEmail', '$NEmail', '$PEmail', $dadd, $nadd, $padd)";
	$oDB->Query($strSQL);


}

?>
<HTML>
	<HEAD>
		<TITLE>ARS Scoring</TITLE>
		<link rel="stylesheet" href="stylesheet.css">
	</HEAD>
	
	<BODY>
		<TABLE BORDER=0 WIDTH=100% border=0>
			<TR>
				<TD WIDTH=30% ALIGN=LEFT VALIGN=TOP>
					<B><?=$RallyeMaster?></B>
				</TD>

				<TD WIDTH=40% ALIGN=CENTER VALIGN=TOP>
					<B><?=$RallyeName?></B>
				</TD>

				<TD WIDTH=30% ALIGN=RIGHT VALIGN=TOP>
					<B><?=$RallyeDate?></B>
				</TD>
			<TR>
		</TABLE>
		<BR>
		<h1>Thank you, you have completed the computer station.</h1>
		<h1>Please collect your scoresheet on the way out.</h1>
		<TABLE WIDTH=100%>
			<TR>
				<TD WIDTH=50%>
					Car #: <?=$CarNumber?><BR>
					Class: <?=$arrClasses[$CarClass]?><BR>
				</TD>

				<TD WIDTH=50%>
					Driver Name: <?=$DriverName?> <?=(trim($DClub)!=""?"($DClub)":"")?><BR>
					Navgtr Name: <?=$NavgtrName?> <?=(trim($NClub)!=""?"($NClub)":"")?><BR>
					Pasngr Name: <?=$PasngrName?> <?=(trim($PClub)!=""?"($PClub)":"")?><BR>
				</TD>
			<TR>
		</TABLE>
		
	<BR>

	<FORM METHOD=POST ACTION=register.php>
	<INPUT TYPE=HIDDEN NAME=RallyeID VALUE=<?=$RallyeID?>>
	<INPUT TYPE=HIDDEN NAME=TestMode VALUE=<?=$TestMode?>>

		<TABLE WIDTH=100%>
			<TR>
				<TD WIDTH=33% ALIGN=LEFT>
					<INPUT TYPE=SUBMIT NAME=action VALUE="Next Car">
				</TD>
			</TR>
		</TABLE>
	</FORM>
	<BR><BR>
<?php include("regmsg.php"); ?>
	</BODY>
</HTML>
