<?
include "config.php";

$ThisStep=48;

$RallyeID = CHTTPVars::GetValue("RallyeID");
$TestMode = intval(CHTTPVars::GetValue("TestMode"));

$CardNumber = CHTTPVars::GetValue("cardnumber");

$CarNumber =  CHTTPVars::GetValue("carnumber");
$CarClass =  CHTTPVars::GetValue("carclass");

$DName = CHTTPVars::GetValue("dname");
$DEmail = CHTTPVars::GetValue("demail");
$DClub = CHTTPVars::GetValue("dclub");
$DTRC = intval(CHTTPVars::GetValue("dtrc"));

$NName = CHTTPVars::GetValue("nname");
$NEmail = CHTTPVars::GetValue("nemail");
$NClub = CHTTPVars::GetValue("nclub");
$NTRC = intval(CHTTPVars::GetValue("ntrc"));

$PName = CHTTPVars::GetValue("pname");
$PEmail = CHTTPVars::GetValue("pemail");
$PClub = CHTTPVars::GetValue("pclub");
$PTRC = intval(CHTTPVars::GetValue("ptrc"));


//print($CardNumber." ".intval($CarNumber));
if($CardNumber != "" && intval($CarNumber))
{
	$strSQL="replace into rfidcard (CardNumber,CarClass,DName,DEmail,DClub,NName,NEmail,NClub,PName,PEmail,PClub) values('$CardNumber','$CarClass','$DName','$DEmail','$DClub','$NName','$NEmail','$NClub','$PName','$PEmail','$PClub')";
	$oDB->Query($strSQL);

	$strSQL = "replace INTO `ars_scoresheet` (`RallyeID`, `CarNumber`, `CarClass`, `Driver`, `Navigator`, `Passenger`, `DClub`, `NClub`, `PClub`, `DEmail`, `NEmail`, `PEmail`) VALUES ($RallyeID, $CarNumber, $CarClass, '$DName', '$NName', '$PName', '$DClub', '$NClub', '$PClub', '$DEmail', '$NEmail', '$PEmail')";
	$oDB->Query($strSQL);

	// this should really be databased
	$arrClasses[1] = "First Timer";
	$arrClasses[2] = "Beginner";
	$arrClasses[3] = "Novice";
	$arrClasses[4] = "Senior";
	$arrClasses[5] = "Expert";
	$arrClasses[6] = "Master";

	// must load this information regardless of weather we are are recording or scoring
	$strSQL="SELECT * FROM ars_rallye_base WHERE RallyeID = $RallyeID";
	$oDB->Query($strSQL);
	$arr = $oDB->GetRecordArray("RallyeName, RallyeDate, RallyeMaster, TopX, TopY, CMX, CMY, CPX, CPY, BotX, BotY, CPLoc");
	if(count($arr))
	{
		list($RallyeName, $RallyeDate, $RallyeMaster, $TopX, $TopY, $CMX, $CMY, $CPX, $CPY, $BotX, $BotY, $CPLoc) = $arr[0];
	}

?>
	<HTML>
		<HEAD>
			<TITLE>ARS - Register</TITLE>
			<link rel="stylesheet" href="bigstylesheet.css">
			</SCRIPT>

		</HEAD>
		
		<BODY>
			<TABLE WIDTH="100%">
				<TR>
					<TD WIDTH="30%" ALIGN=LEFT VALIGN=TOP>
						<B><?=$RallyeMaster?></B>
					</TD>

					<TD WIDTH="40%" ALIGN=CENTER VALIGN=TOP>
						<B><?=$RallyeName?></B>
					</TD>

					<TD WIDTH="30%" ALIGN=RIGHT VALIGN=TOP>
						<B><?=$RallyeDate?></B>
					</TD>
				</TR>
			</TABLE>
			<BR>
			<H2>Registration Complete: <?=$RallyeName?></H2>

			<FORM ID=theform METHOD=POST ACTION=rfidread.php>
				<input type=hidden name=RallyeID value=<?=$RallyeID?>>
				<table border=1>
					<tr>
						<td colspan=2>
							Car: <?=@$CarNumber?><BR>
							Class: <?=@$arrClasses[$CarClass]?><BR>
							Driver: <?=@$DName?><BR>
							Navigator: <?=@$NName?><BR>
							Passengers: <?=@$PName?><BR>
						</td>
					</tr>
				<table>
				<input  type=submit name=store value=DONE>
			</FORM>

		</BODY>
	</HTML>
<?
}
else
{
?>
			<FORM ID=theform METHOD=POST ACTION=rfidread.php>
				<input type=hidden name=RallyeID value=<?=$RallyeID?>>
				<table border=1>
					<tr>
						<td colspan=2>
							Serious problem.
						</td>
					</tr>
				<table>
				<input type=submit name=store value=RESET>
			</FORM>
<?
}
?>