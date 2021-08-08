<?
include "config.php";


?>

<OBJECT CLASSID="clsid:50484945-4745-5453-3000-000000000007" ID=RFID1></OBJECT>   


<SCRIPT language="VBScript">
//Global variables
DIM LastRF
DIM NoDup
DIM iTimerID
DIM SerialNumber

NoDup = FALSE
LastRF = ""

RFID1.Open()
RFID1.waitForAttachment(1000)

If Not RFID1.IsAttached Then
   msgbox "RFID Reader Not Attached - quitting"
   Stop
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


<IMG ID="sound" SRC="ding.wav" STYLE="Display: none"> <!-- this is really a sound file -->
<BGSOUND ID="play" SRC=""  LOOP="1">


<FORM ID=theform METHOD=POST ACTION=rfidrecall.php>
	First Name: <INPUT TYPE=TEXT NAME=fname ID=fname VALUE="<?=@$FName?>"><BR>
	Last Name: <INPUT TYPE=TEXT NAME=lname ID=lname VALUE="<?=@$LName?>"><BR>

	EMail: <INPUT TYPE=TEXT NAME=email ID=email VALUE="<?=@$EMail?>"><BR>

	<INPUT TYPE=TEXT NAME=cardnumber ID=cardnumber  VALUE="<?=@$CardNumber?>">
</FORM>
