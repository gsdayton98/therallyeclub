<?
include "config.php";

$ThisStep=48;

$RallyeID = CHTTPVars::GetValue("RallyeID");
$TestMode = intval(CHTTPVars::GetValue("TestMode"));
//if(!ScorePasswordCheck($RallyeID,@$_SESSION["Password"])) redirect("index.php?RallyeID=$RallyeID");

if(!IsSetStep(16,$RallyeID)) // have I completed the previous step?
	redirect("configdone.php?RallyeID=$RallyeID");


function myprint($str)
{
	print($str."\n");
	//print($str);
}


// Since in HTML rows come first, we have a funky order

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
		<TITLE>ARS - Score</TITLE>
		<link rel="stylesheet" href="bigstylesheet.css">
		
	</HEAD>
	
	<BODY>
		<FORM ID=scoreform METHOD=POST ACTION=regcomplete.php>
		<INPUT TYPE=HIDDEN NAME=RallyeID VALUE=<?=$RallyeID?>>
		<INPUT TYPE=HIDDEN NAME=TestMode VALUE=<?=$TestMode?>>

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

		<TABLE WIDTH="100%">
			<TR>
				<TD VALIGN=TOP>
					<B>Car #:</b> <INPUT TYPE=TEXT NAME=CarNumber ID=CarNumber VALUE="<?=@$CarNumber?>" SIZE=4><BR>
					<B>Class:</b> <?SelectOption("CarClass",$arrClasses,@$CarClass);?><BR>
				</TD>

			</TR>
			<TR>
				<TD VALIGN=TOP>
<BR>
<table border=0 width=100%>
	<tr>
		<td valign=top>
			<b>Driver</b> 
			<hr>
			<table border=0 width=100%>
				<tr>
					<td width=100>
					</td>
					<td valign=top>
						Join TRC Events: <INPUT TYPE=CHECKBOX VALUE=1 <?=intval($dadd)?"CHECKED":""?> NAME=DADD><BR>
						Name: <INPUT TYPE=TEXT NAME=DriverName VALUE="<?=$DriverName?>" SIZE=25><BR>
						Club: TRC<INPUT TYPE=CHECKBOX VALUE=1 <?=$CHECKED?> NAME=DClubCB> / Other:<INPUT TYPE=TEXT NAME=DClub VALUE="<?=$DClub?>" SIZE=10><BR>
						Email: <INPUT TYPE=TEXT NAME=DEmail VALUE="<?=$DEmail?>" SIZE=30><BR><BR><BR>
					</td>
				</tr/>
			</table>
		</td>

		<td valign=top>
			<b>Navigator</b>
			<hr>
			<table border=0 width=100%>
				<tr>
					<td  width=100>
					</td>
					<td valign=top>
						Join TRC Events: <INPUT TYPE=CHECKBOX VALUE=1 <?=intval($nadd)?"CHECKED":""?> NAME=NADD><BR>
						Name: <INPUT TYPE=TEXT NAME=NavgtrName VALUE="<?=$NavgtrName?>" SIZE=25><BR>
						TRC:<INPUT TYPE=CHECKBOX VALUE=1 <?=$CHECKED?> NAME=NClubCB> Other:<INPUT TYPE=TEXT NAME=NClub VALUE="<?=$NClub?>" SIZE=10><BR>
						Email: <INPUT TYPE=TEXT NAME=NEmail VALUE="<?=$NEmail?>" SIZE=30><BR><br><br>

					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 valign=top>
			<B>Passenger</b> (if any)
			<hr>
			<table width=100%>
				<tr>
					<td>
					</td>
					<td>
						Join TRC Events: <INPUT TYPE=CHECKBOX VALUE=1 <?=intval($padd)?"CHECKED":""?> NAME=PADD><BR>
						Name: <INPUT TYPE=TEXT NAME=PasngrName VALUE="<?=$PasngrName?>" SIZE=25><br>
						TRC:<INPUT TYPE=CHECKBOX VALUE=1 <?=$CHECKED?> NAME=PClubCB><br>Other:<INPUT TYPE=TEXT NAME=PClub VALUE="<?=$PClub?>" SIZE=10><br>
						Email: <INPUT TYPE=TEXT NAME=PEmail VALUE="<?=$PEmail?>" SIZE=30><br><br><BR>
					</td>
				</tr>
			</table>
		</TD>
	</TR>
</TABLE>

		<TABLE>
			<TR>
				<TD  VALIGN=TOP>
					<CENTER>
						<INPUT TYPE=SUBMIT NAME=action ID=register VALUE=Register>
					</CENTER>
				</TD>
			</TR>
		</TABLE>
		</FORM>

	</BODY>
</HTML>
