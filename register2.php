<?
session_start();

include "config.php";

function Register($RallyeID, $CarClass, $CarNumber, $DriverName, $NavgtrName, $PasngrName, $DClub, $NClub, $PClub, $DEmail, $NEmail, $PEmail, $dadd, $nadd, $padd)
{
	global $oDB;
	
	$DriverName   = addslashes($DriverName);
	$NavgtrName   = addslashes($NavgtrName); 
	$PasngrName   = addslashes($PasngrName); 
	$DClub        = addslashes($DClub); 
	$NClub        = addslashes($NClub); 
	$PClub        = addslashes($PClub); 
	$DEmail       = addslashes($DEmail); 
	$NEmail       = addslashes($NEmail);
	$PEmail       = addslashes($PEmail);

	$strSQL = "replace INTO `ars_scoresheet` (`RallyeID`, `CarNumber`, `CarClass`, `Driver`, `Navigator`, `Passenger`, `DClub`, `NClub`, `PClub`, `DEmail`, `NEmail`, `PEmail`, `dadd`, `nadd`, `padd`) VALUES ($RallyeID, $CarNumber, $CarClass, '$DriverName', '$NavgtrName', '$PasngrName', '$DClub', '$NClub', '$PClub', '$DEmail', '$NEmail', '$PEmail', $dadd, $nadd, $padd)";
	$oDB->Query($strSQL);

	$strSQL = "replace into ars_rallyist (`RallyistID`, `Name`, `Email`, `Address`, `City`, `ZIP`, `Club`, `Class`) values
	(".intval($_SESSION['DriverID']).", '$DriverName', '".addslashes($_SESSION['DriverEmail'])."','".addslashes($_SESSION['DriverAddress'])."','".addslashes($_SESSION['DriverCity'])."','".addslashes($_SESSION['DriverZIP'])."', '$DClub', $CarClass),
	(".intval($_SESSION['NavigatorID']).", '$NavgtrName', '".addslashes($_SESSION['NavigatorEmail'])."','".addslashes($_SESSION['NavigatorAddress'])."','".addslashes($_SESSION['NavigatorCity'])."','".addslashes($_SESSION['NavigatorZIP'])."', '$NClub', $CarClass),
	(".intval($_SESSION['PassengerID']).", '$PasngrName', '".addslashes($_SESSION['PassengerEmail'])."','".addslashes($_SESSION['PassengerAddress'])."','".addslashes($_SESSION['PassengerCity'])."','".addslashes($_SESSION['PassengerZIP'])."', '$PClub', $CarClass)
	";
	$oDB->Query($strSQL);
}


include "config.php";

$ThisStep=48;
// this should really be databased
$arrClasses[1] = "First Timer";
$arrClasses[2] = "Beginner";
$arrClasses[3] = "Novice";
$arrClasses[4] = "Senior";
$arrClasses[5] = "Expert";
$arrClasses[6] = "Master";

if(isset($_REQUEST['startover']))
{
	session_destroy();
	session_start();
}

if(isset($_REQUEST['back']))
{
	unset($_REQUEST);
	if(isset($_SESSION['PassengerName']))
	{
		unset($_SESSION['PassengerName']       ); 
		unset($_SESSION['PassengerEmail']      ); 
		unset($_SESSION['PassengerID']         ); 
		unset($_SESSION['PassengerAddress']         ); 
		unset($_SESSION['PassengerCity']         ); 
		unset($_SESSION['PassengerZIP']         );
		unset($_SESSION['PassengerClub']);		
		unset($_SESSION['PassengerTRCClub']    ); 
		unset($_SESSION['PassengerOtherClub']  ); 
		unset($_SESSION['PassengerJoinList']   ); 
	}
	elseif(isset($_SESSION['NavigatorName']))
	{
		unset($_SESSION['PassengerName']       ); 
		unset($_SESSION['PassengerEmail']      ); 
		unset($_SESSION['PassengerID']         ); 
		unset($_SESSION['PassengerAddress']         ); 
		unset($_SESSION['PassengerCity']         ); 
		unset($_SESSION['PassengerZIP']         ); 
		unset($_SESSION['PassengerClub']);		
		unset($_SESSION['PassengerTRCClub']    ); 
		unset($_SESSION['PassengerOtherClub']  ); 
		unset($_SESSION['PassengerJoinList']   ); 
		unset($_SESSION['NavigatorName']       ); 
		unset($_SESSION['NavigatorEmail']      ); 
		unset($_SESSION['NavigatorID']         ); 
		unset($_SESSION['NavigatorAddress']         ); 
		unset($_SESSION['NavigatorCity']         ); 
		unset($_SESSION['NavigatorZIP']         ); 
		unset($_SESSION['NavigatorTRCClub']    ); 
		unset($_SESSION['NavigatorClub']);		
		unset($_SESSION['NavigatorOtherClub']  ); 
		unset($_SESSION['NavigatorJoinList']   ); 
	}
	elseif(isset($_SESSION['DriverName']))
	{
		unset($_SESSION['PassengerName']       ); 
		unset($_SESSION['PassengerEmail']      ); 
		unset($_SESSION['PassengerID']         ); 
		unset($_SESSION['PassengerAddress']         ); 
		unset($_SESSION['PassengerCity']         ); 
		unset($_SESSION['PassengerZIP']         ); 
		unset($_SESSION['PassengerClub']);		
		unset($_SESSION['PassengerTRCClub']    ); 
		unset($_SESSION['PassengerOtherClub']  ); 
		unset($_SESSION['PassengerJoinList']   ); 
		unset($_SESSION['NavigatorName']       ); 
		unset($_SESSION['NavigatorEmail']      ); 
		unset($_SESSION['NavigatorID']         ); 
		unset($_SESSION['NavigatorCity']         ); 
		unset($_SESSION['NavigatorZIP']         ); 
		unset($_SESSION['NavigatorAddress']         ); 
		unset($_SESSION['NavigatorClub']);		
		unset($_SESSION['NavigatorTRCClub']    ); 
		unset($_SESSION['NavigatorOtherClub']  ); 
		unset($_SESSION['NavigatorJoinList']   ); 
		unset($_SESSION['DriverName']       ); 
		unset($_SESSION['DriverEmail']      ); 
		unset($_SESSION['DriverID']         ); 
		unset($_SESSION['DriverAddress']         ); 
		unset($_SESSION['DriverTRCClub']    ); 
		unset($_SESSION['DriverOtherClub']  ); 
		unset($_SESSION['DriverJoinList']   ); 
	}
	elseif(isset($_SESSION['CarClass']))
	{
		unset($_SESSION['PassengerName']       ); 
		unset($_SESSION['PassengerEmail']      ); 
		unset($_SESSION['PassengerID']         ); 
		unset($_SESSION['PassengerAddress']         ); 
		unset($_SESSION['PassengerCity']         ); 
		unset($_SESSION['PassengerZIP']         ); 
		unset($_SESSION['PassengerTRCClub']    ); 
		unset($_SESSION['PassengerOtherClub']  ); 
		unset($_SESSION['PassengerJoinList']   ); 
		unset($_SESSION['NavigatorName']       ); 
		unset($_SESSION['NavigatorEmail']      ); 
		unset($_SESSION['NavigatorID']         ); 
		unset($_SESSION['NavigatorID']         ); 
		unset($_SESSION['NavigatorCity']         ); 
		unset($_SESSION['NavigatorZIP']         ); 
		unset($_SESSION['NavigatorTRCClub']    ); 
		unset($_SESSION['NavigatorOtherClub']  ); 
		unset($_SESSION['NavigatorJoinList']   ); 
		unset($_SESSION['DriverName']       ); 
		unset($_SESSION['DriverEmail']      ); 
		unset($_SESSION['DriverID']         ); 
		unset($_SESSION['DriverAddress']         ); 
		unset($_SESSION['DriverCity']         ); 
		unset($_SESSION['DriverZIP']         ); 
		unset($_SESSION['DriverClub']    ); 
		unset($_SESSION['DriverTRCClub']    ); 
		unset($_SESSION['DriverOtherClub']  ); 
		unset($_SESSION['DriverJoinList']   ); 
		unset($_SESSION['CarClass']);
	}
	elseif(isset($_SESSION['CarNumber']))
	{
		unset($_SESSION['PassengerName']       ); 
		unset($_SESSION['PassengerEmail']      ); 
		unset($_SESSION['PassengerID']         ); 
		unset($_SESSION['PassengerAddress']         ); 
		unset($_SESSION['PassengerTRCClub']    ); 
		unset($_SESSION['PassengerOtherClub']  ); 
		unset($_SESSION['PassengerJoinList']   ); 
		unset($_SESSION['NavigatorName']       ); 
		unset($_SESSION['NavigatorEmail']      ); 
		unset($_SESSION['NavigatorID']         ); 
		unset($_SESSION['NavigatorAddress']         ); 
		unset($_SESSION['NavigatorTRCClub']    ); 
		unset($_SESSION['NavigatorOtherClub']  ); 
		unset($_SESSION['NavigatorJoinList']   ); 
		unset($_SESSION['DriverName']       ); 
		unset($_SESSION['DriverEmail']      ); 
		unset($_SESSION['DriverID']         ); 
		unset($_SESSION['DriverAddress']         ); 
		unset($_SESSION['DriverTRCClub']    ); 
		unset($_SESSION['DriverOtherClub']  ); 
		unset($_SESSION['DriverJoinList']   ); 
		unset($_SESSION['CarClass']);
		unset($_SESSION['CarNumber']);
	}
}

if(isset($_REQUEST['CarNumber']))
{
	if(intval($_REQUEST['CarNumber']))
	{
		$_SESSION['CarNumber'] = $_REQUEST['CarNumber'];
	}
	else
	{
		unset($_REQUEST);
	}
}
else if(isset($_REQUEST['CarClass']))
{
	$_SESSION['CarClass'] =$_REQUEST['CarClass'];
}
elseif(isset($_REQUEST['DriverName']))
{
	$_SESSION['DriverName']       = $_REQUEST['DriverName'];
	$_SESSION['DriverEmail']      = $_REQUEST['DriverEmail'];
	$_SESSION['DriverID']         = $_REQUEST['DriverID'];
	$_SESSION['DriverAddress']    = $_REQUEST['DriverAddress'];
	$_SESSION['DriverCity']    = $_REQUEST['DriverCity'];
	$_SESSION['DriverZIP']    = $_REQUEST['DriverZIP'];
	$_SESSION['DriverTRCClub']    = intval($_REQUEST['DriverTRCClub']);
	$_SESSION['DriverOtherClub']  = trim($_REQUEST['DriverOtherClub']);
	$_SESSION['DriverJoinList']   = intval($_REQUEST['DriverJoinList']);

	$sep="";
	if($_SESSION['DriverTRCClub'])
	{
		$_SESSION['DriverClub'] = "TRC";
		$sep="/";
	}
	if($_SESSION['DriverOtherClub'] != "")
	{
		$_SESSION['DriverClub'] .= $sep.$_SESSION['DriverOtherClub'];
	}
}
elseif(isset($_REQUEST['NavigatorName']))
{
	$_SESSION['NavigatorName']       = $_REQUEST['NavigatorName'];
	$_SESSION['NavigatorEmail']      = $_REQUEST['NavigatorEmail'];
	$_SESSION['NavigatorID']         = $_REQUEST['NavigatorID'];
	$_SESSION['NavigatorAddress']    = $_REQUEST['NavigatorAddress'];
	$_SESSION['NavigatorCity']    = $_REQUEST['NavigatorCity'];
	$_SESSION['NavigatorZIP']    = $_REQUEST['NavigatorZIP'];
	$_SESSION['NavigatorTRCClub']    = intval($_REQUEST['NavigatorTRCClub']);
	$_SESSION['NavigatorOtherClub']  = trim($_REQUEST['NavigatorOtherClub']);
	$_SESSION['NavigatorJoinList']   = intval($_REQUEST['NavigatorJoinList']);

	$sep="";
	if($_SESSION['NavigatorTRCClub'])
	{
		$_SESSION['NavigatorClub'] = "TRC";
		$sep="/";
	}
	if($_SESSION['NavigatorOtherClub'] != "")
	{
		$_SESSION['NavigatorClub'] .= $sep.$_SESSION['NavigatorOtherClub'];
	}
}
elseif(isset($_REQUEST['PassengerName']))
{
	$_SESSION['PassengerName']       = $_REQUEST['PassengerName'];
	$_SESSION['PassengerEmail']      = $_REQUEST['PassengerEmail'];
	$_SESSION['PassengerID']         = $_REQUEST['PassengerID'];
	$_SESSION['PassengerAddress']    = $_REQUEST['PassengerAddress'];
	$_SESSION['PassengerCity']    = $_REQUEST['PassengerCity'];
	$_SESSION['PassengerZIP']    = $_REQUEST['PassengerZIP'];
	$_SESSION['PassengerTRCClub']    = intval($_REQUEST['PassengerTRCClub']);
	$_SESSION['PassengerOtherClub']  = trim($_REQUEST['PassengerOtherClub']);
	$_SESSION['PassengerJoinList']   = intval($_REQUEST['PassengerJoinList']);
	
	$sep="";
	if($_SESSION['PassengerTRCClub'])
	{
		$_SESSION['PassengerClub'] = "TRC";
		$sep="/";
	}
	if($_SESSION['PassengerOtherClub'] != "")
	{
		$_SESSION['PassengerClub'] .= $sep.$_SESSION['PassengerOtherClub'];
	}
}
elseif(isset($_REQUEST['NavigatorName']))
{
	$_SESSION['NavigatorName'] =$_REQUEST['NavigatorName'];
}
elseif(isset($_REQUEST['PassengerName']))
{
	$_SESSION['PassengerName'] =$_REQUEST['PassengerName'];
}

?>
<HTML>
	<HEAD>
		<TITLE>ARS - Score</TITLE>
		<link rel="stylesheet" href="bigstylesheet.css">
		<script language="Javascript1.2">
			<!--

			function printpage() 
			{
				var tmp;
				
				tmp = document.getElementById('printbutton');
				tmp.style.visibility="hidden";
				
				tmp = document.getElementById('startover');
				tmp.style.visibility="hidden";
				
				tmp = document.getElementById('back');
				tmp.style.visibility="hidden";
				
				window.print();

				tmp = document.getElementById('printbutton');
				tmp.style.visibility="visible";

				tmp = document.getElementById('startover');
				tmp.style.visibility="visible";

				tmp = document.getElementById('back');
				tmp.style.visibility="visible";
				
			}
			//-->
		</script>
		
	</HEAD>
	<BODY>
	<form method=get>
<?

if(isset($_SESSION['RallyeID']))
{
	$RallyeID = $_SESSION['RallyeID'];
	$TestMode = $_SESSION['TestMode'];

	$RallyeName    =  $_SESSION['RallyeName']  ;  
	$RallyeDate    =  $_SESSION['RallyeDate']  ;
	$RallyeMaster  =  $_SESSION['RallyeMaster'];
	$TopX          =  $_SESSION['TopX']        ;
	$TopY          =  $_SESSION['TopY']        ;
	$CMX           =  $_SESSION['CMX']         ;
	$CMY           =  $_SESSION['CMY']         ;
	$CPX           =  $_SESSION['CPX']         ;
	$CPY           =  $_SESSION['CPY']         ;
	$BotX          =  $_SESSION['BotX']        ;
	$BotY          =  $_SESSION['BotY']        ;
	$CPLoc         =  $_SESSION['CPLoc']       ;
	
	if(isset($_SESSION['CarNumber']))
	{
		$RallyeID = $_SESSION['RallyeID'];
		if(isset($_SESSION['CarClass']))
		{
			if(isset($_SESSION['DriverName']))
			{
				if(isset($_SESSION['NavigatorName']))
				{
					if(isset($_SESSION['PassengerName']))
					{
						// Thank you print
						print("<BR>
						<table width=100%>
							<tr>
								<td align=left class=small>
									Car: $_SESSION[CarNumber]  
								</td>
								<td align=rigth class=small>Class: ".$arrClasses[$_SESSION['CarClass']]."
								</td>
							</tr>
						</table>
						<table width=100% class=small>
							<tr class=small>
								<td width=1% class=small>
									<B>
									D<BR>
									R<BR>
									I<BR>
									V<BR>
									E<BR>
									R
									</B>
								</td>

								<td width=98% class=small>
									<table width=100% class=small>
										<tr class=small>
											<td class=small>
												$_SESSION[DriverName]
											</td>

											<td class=small>
											<B>Name</B>
											</td>

											<td class=small>
												$_SESSION[NavigatorName]
											</td>
										</tr>
										<tr class=small>
											<td class=small>
											$_SESSION[DriverAddress]
											</td>

											<td class=small>
											<B>Address</B>
											</td>

											<td class=small>
											$_SESSION[NavigatorAddress]
											</td>
										</tr>
										<tr class=small>
											<td class=small>
											$_SESSION[DriverCity], $_SESSION[DriverZIP]
											</td>

											<td class=small>
											<B>City, ZIP</B>
											</td>

											<td class=small>
											$_SESSION[NavigatorCity], $_SESSION[NavigatorZIP]
											</td>
										</tr>
										<tr>
											<td class=small>
												$_SESSION[DriverID], $_SESSION[DriverClub]
											</td>

											<td class=small>
											<B>Phone, Club</B>
											</td>

											<td class=small>
												$_SESSION[NavigatorID], $_SESSION[NavigatorClub]
											</td>
										</tr>
										<tr class=small>
											<td class=small>
												$_SESSION[DriverEmail]
											</td>

											<td class=small>
											<B>Email</B>
											</td>

											<td class=small>
												$_SESSION[NavigatorEmail]
											</td>
										</tr>
										<tr class=small>
											<td class=small>
												".($_SESSION['DriverJoinList']?"Yes":"No")."
											</td>

											<td class=small>
											<B>Join TRC Events</B>
											</td>

											<td class=small>
												".($_SESSION['NavigatorJoinList']?"Yes":"No")."
											</td>
										</tr>
									</table>
								</td>

								<td width=1% class=small>
									<B>
									N<BR>
									A<BR>
									V<BR>
									G<BR>
									T<BR>
									R<BR>
									</B>
								</td>
							</tr>
						
							<tr class=small>
								<td colspan=3 align=left class=small>
									Passenger: $_SESSION[PassengerName]
								</td>
							</tr>
						</table>
						");			
						
						print('<br><br>');
						print('<center>
						
						<table width=100%>
							<tr>
								<td align=center>
						<input class=button class=button type=submit id=back value=Back name=back>
								</td>
								<td align=center>'.
/**						<input class=button type=button id=printbutton value=Print onClick=printpage();> **/ 
								'</td>
								<td align=center>
						<input class=button type=submit id=startover name=startover value="Done">
								</td>
							</tr>
						</table>
						</center>');
						
						Register($_SESSION['RallyeID'], $_SESSION['CarClass'], $_SESSION['CarNumber'], $_SESSION['DriverName'], $_SESSION['NavigatorName'], $_SESSION['PassengerName'], $_SESSION['DriverClub'], $_SESSION['NavigatorClub'], $_SESSION['PassengerClub'], $_SESSION['DriverEmail'], $_SESSION['NavigatorEmail'], $_SESSION['PassengerEmail'], $_SESSION['DriverJoinList'], $_SESSION['NavigatorJoinList'], $_SESSION['PassengerJoinList']);
					}
					else
					{
?>
	<center>
	<H1>Passenger Information</H1>
	</center>
	<hr><br>
	Last 4 digits of personal Phone:<input class=text type=text name=PassengerID size=4 maxlength=4><BR>
	Name: <input type=text class=text name=PassengerName size=30><BR>
	Email: <input type=text class=text name=PassengerEmail size=29><BR>
	Address: <input type=text class=text name=PassengerAddress size=27><BR>
	City: <input type=text class=text name=PassengerCity size=18>&nbsp;ZIP: <input type=text class=text name=PassengerZIP size=5 maxlength=5><BR>
	Club: TRC:<input style=checkbox type=checkbox name=PassengerTRCClub value=1> Other:<input type=text class=text name=PassengerOtherClub size=18><BR>
	Join TRC Events Email List? <input style=checkbox type=checkbox name=PassengerJoinList value=1><BR>
	<br>
	
	<table width=100%>
		<tr>
			<td align=center>
	<input class=button type=submit value=Back name=back>
			</td>
			<td align=center>
	<input class=button type=submit value=Next>
			</td>
		</tr>
	</table>
<?
			}
				}
				else
				{
?>
	<center>
	<H1>Navigator Information</H1>
	</center>
	<hr><br>
	
	Last 4 digits of personal Phone:<input type=text class=text name=NavigatorID size=4 maxlength=4><BR>
	Name: <input type=text class=text name=NavigatorName size=30><BR>
	Email: <input type=text class=text name=NavigatorEmail size=29><BR>
	Address: <input type=text class=text name=NavigatorAddress size=27><BR>
	City: <input type=text class=text name=NavigatorCity size=18>&nbsp;ZIP: <input type=text class=text name=NavigatorZIP size=5 maxlength=5><BR>
	Club: TRC:<input style=checkbox type=checkbox name=NavigatorTRCClub value=1> Other:<input type=text class=text name=NavigatorOtherClub size=18><BR>
	Join TRC Events Email List? <input style=checkbox type=checkbox name=NavigatorJoinList value=1><BR>
	<BR>
	<table width=100%>
		<tr>
			<td align=center>
	<input class=button type=submit value=Back name=back>
			</td>
			<td align=center>
	<input class=button type=submit value=Next>
			</td>
		</tr>
	</table>
<?
				}
			}
			else
			{
?>
	<center>
	<H1>Driver Information</H1>
	</center>
	<hr><br>
	Last 4 digits of personal Phone:<input type=text class=text name=DriverID size=4 maxlength=4><BR>
	Name: <input type=text class=text name=DriverName size=30><BR>
	Email: <input type=text class=text name=DriverEmail size=29><BR>
	Address: <input type=text class=text name=DriverAddress size=27><BR>
	City: <input type=text class=text name=DriverCity size=18>&nbsp;ZIP: <input type=text class=text name=DriverZIP size=5 maxlength=5><BR>
	Club: TRC:<input style=checkbox type=checkbox name=DriverTRCClub value=1> Other:<input type=text class=text name=DriverOtherClub size=18><BR>
	Join TRC Events Email List? <input style=checkbox type=checkbox name=DriverJoinList value=1><BR>
	<BR>
	<table width=100%>
		<tr>
			<td align=center>
	<input class=button type=submit value=Back name=back>
			</td>
			<td align=center>
	<input class=button type=submit value=Next>
			</td>
		</tr>
	</table>
<?
			}
		}
		else
		{
?>
	<CENTER>
	<H1>Car Class, The highest class of all the people in the car</H1>
	<HR><BR>
	<table border=1 style="padding:10px;">
		<tr>
			<td>
	<label><input type=radio name=CarClass value=1>First Timer</label><BR>
			</td>
			<td>
	<label><input type=radio name=CarClass value=2>Beginner</label><BR>
			</td>
			<td>
	<label><input type=radio name=CarClass value=3>Novice</label><BR>
			</td>
		</tr>
		<tr>
			<td>
	<label><input type=radio name=CarClass value=4>Senior</label><BR>
			</td>
			<td>
	<label><input type=radio name=CarClass value=5>Expert</label><BR>
			</td>
			<td>
	<label><input type=radio name=CarClass value=6>Master</label><BR>
			</td>
		</tr>
	</table>
	<BR>
	<BR>
	<BR>
	<table width=100%>
		<TR>
			<td align=center>
	<input class=button type=submit value=Back name=back>
			</TD>
			<td align=center>
	<input class=button type=submit value=Next>
			</TD>
		</TR>
	<TABLE>
	</CENTER>
<?
		}
	}
	else
	{
?>
	<center>
	<H1>Car Number</H1>
	<hr>
	<BR><input style="border: 1px solid black;" type=text class=text name=CarNumber size=3 maxlength=2><BR><BR><BR>
	<input class=button type=submit value=Next>
	</center>
<?
	}
	
}	
else
{
	$RallyeID = CHTTPVars::GetValue("RallyeID");
	$_SESSION['RallyeID'] = $RallyeID;
	$TestMode = intval(CHTTPVars::GetValue("TestMode"));
	$_SESSION['TestMode'] = $TestMode;
	
	// must load this information regardless of weather we are are recording or scoring
	$strSQL="SELECT * FROM ars_rallye_base WHERE RallyeID = $RallyeID";
	$oDB->Query($strSQL);
	$arr = $oDB->GetRecordArray("RallyeName, RallyeDate, RallyeMaster, TopX, TopY, CMX, CMY, CPX, CPY, BotX, BotY, CPLoc");
	if(count($arr))
	{
		list($RallyeName, $RallyeDate, $RallyeMaster, $TopX, $TopY, $CMX, $CMY, $CPX, $CPY, $BotX, $BotY, $CPLoc) = $arr[0];
		
		$_SESSION['RallyeName']    = $RallyeName  ;  
		$_SESSION['RallyeDate']    = $RallyeDate  ;
		$_SESSION['RallyeMaster']  = $RallyeMaster;
		$_SESSION['TopX']          = $TopX        ;
		$_SESSION['TopY']          = $TopY        ;
		$_SESSION['CMX']           = $CMX         ;
		$_SESSION['CMY']           = $CMY         ;
		$_SESSION['CPX']           = $CPX         ;
		$_SESSION['CPY']           = $CPY         ;
		$_SESSION['BotX']          = $BotX        ;
		$_SESSION['BotY']          = $BotY        ;
		$_SESSION['CPLoc']         = $CPLoc       ;

	}

	// Car number
?>
<center>
<H1>Car Number</H1>
<hr>
<BR><input class=text type=text name=CarNumber size=3 maxlength=2><br><BR><BR>
<input class=button type=submit value=Next>
</center>
<?
}

	
?>
	<input type=hidden name=RallyeID value=<?=$RallyeID?>
	</form>
	</BODY>
</HTML>