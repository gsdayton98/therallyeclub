
<OBJECT CLASSID="clsid:50484945-4745-5453-3000-000000000002" ID=PhidgetManager></OBJECT>   
<OBJECT CLASSID="clsid:50484945-4745-5453-3000-000000000007" ID=RF></OBJECT>   


<SCRIPT language="VBScript">
//Global variables

//RF.Open()
//RF.waitForAttachment(1000)

//If Not RF.IsAttached Then
//   msgbox "RFID Reader Not Attached - quitting"
//   Stop
//End If

//SerialNumber = RF.SerialNumber
//msgbox SerialNumber
//RF.AntennaOn = "True"

PhidgetManager.Open

// This event occurs at startup if any phidgets are present and when an new phidget is attached.
SUB PhidgetManager_OnAttach(ByVal deviceType, ByVal deviceName, ByVal serialNumber, ByVal deviceVersion, ByVal deviceLabel)

//	msgbox deviceType
//	msgbox serialNumber
//	
	if(deviceType = "PhidgetRFID") Then
		RF.Open serialNumber
		RF.waitForAttachment 2000 

		msgbox RF.serialNumber

		RF.AntennaOn = "True"
	else
		msgbox "What?"
	end if
	
END SUB

// When a Phidget is detached this event fires.
SUB PhidgetManager_OnDetach(ByVal deviceType, ByVal deviceName, ByVal serialNumber, ByVal deviceVersion, ByVal deviceLabel)

	msgbox deviceType
	msgbox serialNumber

	
END SUB

// When a tag is swiped past the RFID reader, this event fires
SUB RF_OnTag(ByVal tagNumber)

	msgbox tagNumber
	
END SUB

</SCRIPT>


