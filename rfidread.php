<?
include "config.php";

$ThisStep=48;

$RallyeID = CHTTPVars::GetValue("RallyeID");
$TestMode = intval(CHTTPVars::GetValue("TestMode"));

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
		
		<OBJECT CLASSID="clsid:50484945-4745-5453-3000-000000000007" ID=RFID1></OBJECT>   
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
	
		<H2>Registration: <?=$RallyeName?></H2>
		<table width=100%>
			<tr>
				<td id=instructions>
				</td>
			</tr>
		</table>
		<IMG ID="sound" SRC="ding.wav" STYLE="Display: none"> <!-- this is really a sound file -->
		<BGSOUND ID="play" SRC=""  LOOP="1">

		<center>
		<FORM ID=theform METHOD=POST ACTION=rfidreg.php>
			<input type=hidden name=RallyeID value=<?=$RallyeID?>>
			<INPUT TYPE=hidden readonly NAME=cardnumber ID=cardnumber  VALUE="<?=@$CardNumber?>">
			<input type=submit name=store value="register without keyfob">
		</FORM>
		</center>
	</BODY>
</HTML>

		<SCRIPT language="VBScript">
		//Global variables
		DIM LastRF
		DIM NoDup
		DIM iTimerID
		DIM SerialNumber
		DIM inst

		NoDup = FALSE
		LastRF = ""

		RFID1.Open()
		RFID1.waitForAttachment(1000)

		set inst=document.getElementById("instructions")

		If Not RFID1.IsAttached Then
		   //msgbox "RFID Reader Not Attached - quitting"
		   inst.innerText = "RFID Reader Not Attached"
		   Stop
		else
			inst.innerHTML = "<center>Wave your TRC keyfob over the RFID pad to autoload.<br><BR>-- OR --</center>"
		End If

		SerialNumber = RFID1.SerialNumber
		//msgbox SerialNumber
		RFID1.AntennaOn = "True"


		// When a tag is swiped past the RFID reader, this event fires
		SUB RFID1_OnTag(ByRef TagNumber)
				RFID1.AntennaOn = "False" // turn off the field so that we don't read while we are processing 

				RFID1.LEDOn = "True"                       // turn on the light
				RFID1.OutputState(1) = "True"
				play.src=sound.src                             // play a sound
				
				theform.cardnumber.value = TagNumber

				RFID1.AntennaOn = "True" // turn on the field so that we can read again
		END SUB

		Sub RFID1_OnTagLost(ByVal TagNumber)
				RFID1.LEDOn = "False"                      // turn off the light
				RFID1.OutputState(1) = "False"
				theform.submit
		End Sub

		</SCRIPT>

