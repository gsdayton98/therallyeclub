<?
include "config.php";

$ThisStep = 1;
$RallyeID = CHTTPVars::GetValue("RallyeID");
if(!PasswordCheck($RallyeID,@$_SESSION["Password"])) redirect("index.php?RallyeID=$RallyeID");

if(!IsSetStep(0,$RallyeID)) // have I completed the previous step?
	redirect("index.php");

if(!CHTTPVars::IsEmpty("action"))
{
	$action = CHTTPVars::GetValue("action");
	
	switch(strtolower($action))
	{
		case "delete this rallye":
			// delete all references to this rallye
			$strSQL="DELETE FROM ars_protest WHERE RallyeID = $RallyeID";
			$oDB->Query($strSQL);
			$strSQL="DELETE FROM ars_protest_elements WHERE RallyeID = $RallyeID"; // doesn't really work yet, because this table doesn't use RallyeID
			$oDB->Query($strSQL);
			$strSQL="DELETE FROM ars_rallye_base WHERE RallyeID = $RallyeID";
			$oDB->Query($strSQL);
			$strSQL="DELETE FROM ars_rallye_cells WHERE RallyeID = $RallyeID";
			$oDB->Query($strSQL);
			$strSQL="DELETE FROM ars_rallye_impcombo WHERE RallyeID = $RallyeID";
			$oDB->Query($strSQL);
			$strSQL="DELETE FROM ars_rallye_impcombo_elements WHERE RallyeID = $RallyeID";
			$oDB->Query($strSQL);
			$strSQL="DELETE FROM ars_scoresheet WHERE RallyeID = $RallyeID";
			$oDB->Query($strSQL);
			$strSQL="DELETE FROM ars_scoresheet_elements WHERE RallyeID = $RallyeID";
			$oDB->Query($strSQL);

			redirect("index.php");
			break;

		case "done":
			$TopX =  intval(CHTTPVars::GetValue("TopX"));
			$TopY=   intval(CHTTPVars::GetValue("TopY"));
			$CMX =   intval(CHTTPVars::GetValue("CMX"));
			$CMY =   intval(CHTTPVars::GetValue("CMY"));
			$ABChoices =   CHTTPVars::GetValue("CMZ");
			$CPX =   intval(CHTTPVars::GetValue("CPX"));
			$CPY =   intval(CHTTPVars::GetValue("CPY"));
			$BotX =  intval(CHTTPVars::GetValue("BotX"));
			$BotY =  intval(CHTTPVars::GetValue("BotY"));
			$CPLoc = CHTTPVars::GetValue("CPLoc");
			$RallyeMaster = (HTMLEscape(CHTTPVars::GetValue("RallyeMaster")));
			$RallyeName = (HTMLEscape(CHTTPVars::GetValue("RallyeName")));
			$RallyeDate = Date("Y-m-d",strtotime(CHTTPVars::GetValue("RallyeDate")));
			
			
			$ConfigurePassword1 = trim(CHTTPVars::GetValue("ConfigurePassword1"));
			$ConfigurePassword2 = trim(CHTTPVars::GetValue("ConfigurePassword2"));
			
			$ScorePassword1 = trim(CHTTPVars::GetValue("ScorePassword1"));
			$ScorePassword2 = trim(CHTTPVars::GetValue("ScorePassword2"));
			
			//print("$ConfigurePassword1 $ConfigurePassword2<BR>");
			//print("$ScorePassword1 $ScorePassword2<BR>");
			
			$strSQL="UPDATE ars_rallye_base SET RallyeMaster = '$RallyeMaster', RallyeName = '".DBEscape($RallyeName)."', RallyeDate = '$RallyeDate', TopX = $TopX, TopY = $TopY, CMX = $CMX, CMY = $CMY, CPX = $CPX, CPY = $CPY, BotX = $BotX, BotY = $BotY, CPLoc = '$CPLoc' WHERE RallyeID = $RallyeID";
			$oDB->Query($strSQL);
			//print("$strSQL<BR>");
			
			if($ScorePassword1 != $ScorePassword2 || $ConfigurePassword1 != $ConfigurePassword2)
			{
				$nopassmatch=true;
				break;
			}
			
			//print("CONSIDER<BR>");
			//print("if($ConfigurePassword1 != '' && $ConfigurePassword1 == $ConfigurePassword2)<BR>");
			if($ConfigurePassword1 != "" && $ConfigurePassword1 == $ConfigurePassword2)
			{
			//print("SET CONFIGURE<BR>");
				if($ConfigurePassword1 == "REMOVE") $ConfigurePassword1 = "";
				$strSQL="UPDATE ars_rallye_base SET Password = PASSWORD('".DBEscape($ConfigurePassword1)."') WHERE RallyeID = $RallyeID";
				$oDB->Query($strSQL);
				//print("$strSQL<BR>");
				$_SESSION["Password"] = $ConfigurePassword1;
			}
			
			if($ScorePassword1 != "" && $ScorePassword1 == $ScorePassword2)
			{
			//print("SET SCORE<BR>");
				if($ScorePassword1 == "REMOVE") $ScorePassword1 = "";
				$strSQL="UPDATE ars_rallye_base SET ScoreOnlyPassword = PASSWORD('".DBEscape($ScorePassword1)."') WHERE RallyeID = $RallyeID";
				$oDB->Query($strSQL);
				//print("$strSQL<BR>");
			}
			
			SetStep($ThisStep,$RallyeID);

			redirect("name.php?ABChoices=3&RallyeID=$RallyeID");	// RallyeID=$RallyeID&ABChoices=$CMZ

			break;

			
		default:
			redirect("error.php");
	}
}


$strSQL="SELECT * FROM ars_rallye_base WHERE RallyeID = $RallyeID";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray("RallyeID, RallyeName, RallyeDate, RallyeMaster, TopX, TopY, CMX, CMY, CPX, CPY, BotX, BotY, CPLoc");
if(count($arr))
{
	list($RallyeID, $RallyeName, $RallyeDate, $RallyeMaster, $TopX, $TopY, $CMX, $CMY, $CPX, $CPY, $BotX, $BotY, $CPLoc) = $arr[0];
}


?>
<HTML>
	<HEAD>
		<TITLE>ARS - Layout</TITLE>
		<link rel="stylesheet" href="stylesheet.css">
		
		<SCRIPT LANGUAGE=JavaScript>
/*		function ConfigDiv(sectionname, edivname, exname, eyname, ezname="")
		{
			ediv = document.getElementById(edivname);
			eX = document.getElementById(exname);
			eY = document.getElementById(eyname);
			if (ezname == "") {
			    eZ = 1
			} else {
				eZ = document.getElementById(ezname);
			}
			
			ediv.innerHTML="";
			
			if(isNaN(parseInt(eX.value)) || isNaN(parseInt(eY.value)))
			{
				//alert(sectionname);
				return;
			}
			else
			{
				html ="<B>"+sectionname+"</B><BR>";
				html += "<TABLE WIDTH=100% BORDER=1>";
				for(i = 0; i < parseInt(eY.value); i++)
				{
					html += "<TR>";
						for(j = 0; j < parseInt(eX.value); j++)
							for(k = 0; k < parseInt(eZ.value); k++)
							{
								html += "<TD>";
								html += "&nbsp;";
								html += "</TD>";
							}					
					html += "</TR>"
				}
				html += "</TABLE>";
				ediv.innerHTML = html;
			}
		}	
*/
		
		function ConfigDiv(sectionname, edivname, exname, eyname, ezname="")
		{
			ediv = document.getElementById(edivname);
			eX = document.getElementById(exname);
			eY = document.getElementById(eyname);
			if (ezname == "") {
			    kmax = 1
			} else {
				kmax = parseInt(document.getElementById(ezname).value);
			}
			
			ediv.innerHTML="";
			
			if(isNaN(parseInt(eX.value)) || isNaN(parseInt(eY.value)))
			{
				//alert(sectionname);
				return;
			}
			else
			{
				html ="<B>"+sectionname+"</B><BR>";
				html += "<TABLE WIDTH=100% BORDER=1>";
				xmax = parseInt(eX.value);
				ymax = parseInt(eY.value);
				for(i = 0; i < ymax; i++)
				{
					html += "<TR>";
						for(j = 0; j < xmax; j++)
							for(k = 0; k < kmax; k++)
							{
								html += "<TD>";
								html += "&nbsp;";
								html += "</TD>";
							}					
					html += "</TR>"
				}
				html += "</TABLE>";
				ediv.innerHTML = html;
			}
		}	
		
		function ConfigCPDiv()
		{
			eCPLocR = document.getElementById("CPLocR");
			eCPLocB = document.getElementById("CPLocB");
			
			ecprdiv = document.getElementById("cprdiv");
			ecpbdiv = document.getElementById("cpbdiv");
			
			ecprdiv.innerHTML = "";
			ecpbdiv.innerHTML = "";
			
			if(eCPLocR.checked)
				ConfigDiv('CPs','cprdiv','CPX','CPY');
			
			if(eCPLocB.checked)
				ConfigDiv('CPs','cpbdiv','CPX','CPY');
		}
		</SCRIPT>
	</HEAD>
	
	<BODY>
<?
		include("configmenu.php");
?>

		<FORM METHOD=POST>
		<INPUT TYPE=HIDDEN NAME=RallyeID VALUE=<?=$RallyeID?>>
		<TABLE WIDTH=100% border=0>
			<TR>
				<TD WIDTH=30% ALIGN=LEFT>
					<B>Rallyemaster:</B><INPUT TYPE=TEXT NAME=RallyeMaster VALUE="<?=$RallyeMaster?>">
				</TD>

				<TD WIDTH=40% ALIGN=CENTER>
					<B>Rallye Name:</B><INPUT TYPE=TEXT NAME=RallyeName VALUE="<?=$RallyeName?>">
				</TD>

				<TD WIDTH=30% ALIGN=RIGHT>
					<B>Rallye Date:</B><INPUT TYPE=TEXT NAME=RallyeDate VALUE="<?=$RallyeDate?>">
				</TD>
			</TR>
		</TABLE>
		<BR>

		<FIELDSET>
			<LEGEND><B>Instructions</B></LEGEND>
		This step allows you to define the structure of your score sheet. 
		A standard score sheet is divided in to four sections: The header, the CM section, the 
		CP section, and the footer. Sometimes the CP section is to the right of the CM section 
		and sometimes it is below the CM section. Enter information about the number of columns 
		and rows in each section and the location of your CP section below. When you have closely
		aproximated the layout of your scoresheet click the 'Done' button.<BR>
		<BR>
		This section also allows you to set two passwords. The first password is your configuration password.
		The configuration password allows you to access these configuration pages as well as the scoring pages.
		If left blank, your rallye will not be password protected.
		The second password is the score only password. It is often desireable to allow others to score for you,
		but not allow them to access the configuration pages. This second password allows you to rest asurred
		that your configuration is safe from foreign scorers. If left blank the scoring pages will not be password
		protected. Once a password is set, it cannot be removed by blanking out the password field. A blank password
		does not actually remove the password, it simply leaves it alone, so that you do not have to reset the
		password every time you visit this page. To remove a password enter REMOVE into the password field and its
		confirmation field.
		</FIELDSET>
		<BR>

<?
	if(@$nopassmatch)
	{
?>
		<TABLE BORDER=1>
			<TR>
				<TD>
					<FONT COLOR=RED>
					One or more the new passwords you entered did not match its confirmation.
					The passwords for this rallye were not set or changed. Please try again.
					</FONT>
				</TD>
			</TR>
		</TABLE>
<?
	}	
?>
		<TABLE border=0 WIDTH=100%>
			<TR>
				<TD>
					<TABLE border=0>
						<TR>
							<TD ALIGN=RIGHT>
								<B>Configure Password:</B>
							</TD>
			
							<TD ALIGN=LEFT>
								<INPUT TYPE=PASSWORD NAME=ConfigurePassword1>
							</TD>
						</TR>

						<TR>
							<TD ALIGN=RIGHT>
								<B>Confirm Password:</B>
							</TD>
			
							<TD ALIGN=LEFT>
								<INPUT TYPE=PASSWORD NAME=ConfigurePassword2>
							</TD>
						</TR>
					</TABLE>
				</TD>

				<TD ALIGN=LEFT WIDTH=40>
					&nbsp;
				</TD>
				
				<TD>
					<TABLE border=0 WIDTH=100%>
						<TR>
							<TD  ALIGN=RIGHT>
								<B>Score Password:</B>
							</TD>
			
							<TD  ALIGN=LEFT>
								<INPUT TYPE=PASSWORD NAME=ScorePassword1>
							</TD>
						</TR>
			
						<TR>
							<TD ALIGN=RIGHT>
								<B>Confirm Password:</B>
							</TD>
			
							<TD ALIGN=LEFT>
								<INPUT TYPE=PASSWORD NAME=ScorePassword2>
							</TD>
						</TR>
					</TABLE>
				</TD>
			
			</TR>
		</TABLE>
		<BR>
		<BR>
		<TABLE WIDTH=100% border=0>
			<TR>
				<TD>
					<B>Header section:</B> (If you have no gimmicks in this section you may leave it blank)<BR>
					&nbsp;&nbsp;Enter the number of Columns in your header section: <INPUT SIZE=2 NAME=TopX ID=TopX VALUE="<?=$TopX?>" TYPE=TEXT OnKeyUp="ConfigDiv('Header','topdiv','TopX','TopY');"><BR>
					&nbsp;&nbsp;Enter the number of Rows in your header section: <INPUT SIZE=2 NAME=TopY ID=TopY VALUE="<?=$TopY?>" TYPE=TEXT OnKeyUp="ConfigDiv('Header','topdiv','TopX','TopY');"><BR>
				</TD>
			</TR>
		</TABLE>
		
		<TABLE WIDTH=100% border=0>
			<TR>
				<TD>
					<DIV ID=topdiv></DIV>
				</TD>
			</TR>
		</TABLE>

		<TABLE WIDTH=100% border=0>
			<TR>
				<TD VALIGN=TOP>
					<B>CM (or A/B RI) section:</B><BR>
					&nbsp;&nbsp;Enter the number of Columns in your CM section: <INPUT SIZE=2 NAME=CMX ID=CMX VALUE="<?=$CMX?>" TYPE=TEXT OnKeyUp="ConfigDiv('CMs','cmdiv','CMX','CMY','CMZ');"><BR>
					&nbsp;&nbsp;Enter the number of Rows in your CM section: <INPUT SIZE=2 NAME=CMY ID=CMY VALUE="<?=$CMY?>" TYPE=TEXT OnKeyUp="ConfigDiv('CMs','cmdiv','CMX','CMY','CMZ');"><BR>
					&nbsp;&nbsp;For an A/B rallye, typical choices per RI: 3? <INPUT SIZE=1 NAME=CMZ ID=CMZ VALUE="<?=$CMZ?>" TYPE=TEXT OnKeyUp="ConfigDiv('CMs','cmdiv','CMX','CMY','CMZ');"><BR>
				</TD>

				<TD VALIGN=TOP>
					<B>CP section:</B><BR>
					&nbsp;&nbsp;My CP Section is:<BR>
					&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE=RADIO NAME=CPLoc ID=CPLocR CHECKED VALUE="R" <?=($CPLoc=="R"?"CHECKED":"")?> OnClick="ConfigCPDiv();">to the right of the CM Section<BR>
					&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE=RADIO NAME=CPLoc ID=CPLocB VALUE="B" <?=($CPLoc=="B"?"CHECKED":"")?> OnClick="ConfigCPDiv();">below the CM Section<BR>
					&nbsp;&nbsp;Enter the number of Columns in your CP section: <INPUT SIZE=2 NAME=CPX ID=CPX VALUE="<?=$CPX?>" TYPE=TEXT OnKeyUp="ConfigCPDiv();"><BR>
					&nbsp;&nbsp;Enter the number of Rows in your CP section: <INPUT SIZE=2 NAME=CPY ID=CPY VALUE="<?=$CPY?>" TYPE=TEXT OnKeyUp="ConfigCPDiv();"><BR>
				</TD>
			</TR>
		</TABLE>

		<TABLE WIDTH=100% border=0>
			<TR>
				<TD VALIGN=TOP>
					<DIV ID=cmdiv></DIV>
				</TD>
				<TD VALIGN=TOP WIDTH=15%>
					<DIV ID=cprdiv></DIV>
				</TD>
			</TR>

			<TR>
				<TD VALIGN=TOP COLSPAN=2>
					<DIV ID=cpbdiv></DIV>
				</TD>
			</TR>
		</TABLE>

		<TABLE WIDTH=100% border=0>
			<TR>
				<TD VALIGN=TOP>
					<B>Footer section:</B> (If you have no gimmicks in this section you may leave it blank, though it is an excelent area for tie brakers)<BR>
					&nbsp;&nbsp;Enter the number of Columns in your footer section: <INPUT SIZE=2 NAME=BotX ID=BotX VALUE="<?=$BotX?>" TYPE=TEXT OnKeyUp="ConfigDiv('Footer','botdiv','BotX','BotY');"><BR>
					&nbsp;&nbsp;Enter the number of Rows in your footer section: <INPUT SIZE=2 NAME=BotY ID=BotY VALUE="<?=$BotY?>" TYPE=TEXT OnKeyUp="ConfigDiv('Footer','botdiv','BotX','BotY');"><BR>
				</TD>
			</TR>
		</TABLE>

		<TABLE WIDTH=100% border=0>
			<TR>
				<TD VALIGN=TOP COLSPAN=2>
					<DIV ID=botdiv></DIV>
				</TD>
			</TR>
		</TABLE>
		
		
		<TABLE WIDTH=100% border=0>
			<TR>
				<TD VALIGN=TOP ALIGN=CENTER>
					<INPUT TYPE=SUBMIT NAME=action VALUE=Done>
				</TD>
				<TD VALIGN=TOP ALIGN=RIGHT>
					<INPUT TYPE=SUBMIT NAME=action VALUE="Delete This Rallye">
				</TD>
			</TR>
		</TABLE>
		
		<SCRIPT LANGUAGE=JavaScript>
			ConfigDiv('Header','topdiv','TopX','TopY');
			ConfigDiv('CMs','cmdiv','CMX','CMY','CMZ');
			ConfigCPDiv();
			ConfigDiv('Footer','botdiv','BotX','BotY');
		</SCRIPT>
		</FORM>
	</BODY>
</HTML>
