<?
include "config.php";


if(!@$sub)
{
	
	$arrClasses[1] = "First Timer";
	$arrClasses[2] = "Beginner";
	$arrClasses[3] = "Novice";
	$arrClasses[4] = "Senior";
	$arrClasses[5] = "Expert";
	$arrClasses[6] = "Master";
	
	$trophy[1][1]="blue";
	$trophy[1][2]="red";
	$trophy[1][3]="dimgray";
	$trophy[2][1]="blue";
	$trophy[2][2]="red";
	$trophy[2][3]="dimgray";
	$trophy[3][1]="blue";
	$trophy[3][2]="red";
	$trophy[3][3]="dimgray";
	$trophy[4][1]="blue";
	$trophy[4][2]="red";
	$trophy[5][1]="blue";
	$trophy[5][2]="red";
	$trophy[6][1]="blue";
	
	$RallyeID = CHTTPVars::GetValue("RallyeID"); // any one can look at the scoreboard, so there is no need for password here
	
	
	if(!CHTTPVars::IsEmpty("action"))
	{
		$action = CHTTPVars::GetValue("action");
		
		switch(strtolower($action))
		{
			case "done":
				redirect("score.php?RallyeID=$RallyeID");
				break;
				
			default:
				redirect("error.php");
		}
	}
}

$strSQL="SELECT CarNumber, CarClass, Driver, Navigator, Passenger, BaseScore, ProtestScore, DClub, NClub, PClub FROM ars_scoresheet WHERE CarNumber < 100 and RallyeID = $RallyeID ORDER By CarClass, CarNumber";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray(false);
$ScoreBoard = array();
foreach($arr as $data)
{
	list($CarNumber, $CarClass, $DriverName, $NavigatorName, $PassengerName, $BaseScore, $ProtestScore, $DClub, $NClub, $PClub) = $data;
	
	$ScoreBoard[$CarClass][$CarNumber] = floatval($BaseScore) + floatval($ProtestScore);
	$Driver[$CarNumber] = $DriverName.(trim($DClub) !=""?"($DClub)":"");
	$Navigator[$CarNumber] = $NavigatorName.(trim($NClub) !=""?"($NClub)":"");;
	$Protest[$CarNumber] = floatval($ProtestScore);
}

foreach($ScoreBoard as $CarClass => $data)
{
	arsort($ScoreBoard[$CarClass]);
}

?>
		<TABLE BORDER=0 WIDTH=100%>
			<TR> <TD><br><br><br><br><br><br><br><br><br><br></TD> </TR>
			<TR>
<?
				foreach($ScoreBoard as $CarClass => $data)
				{
					if($CarClass == 4)
					{
						print("</TR><TR>");
					}
?>
				<TD VALIGN=TOP WIDTH=33%>
					<TABLE WIDTH=100%>
						<TR>
							<TD style="border: 5px solid black" WIDTH=100% ALIGN=CENTER>
								<FONT SIZE=+5>
								<B><?=$arrClasses[intval($CarClass)]?></B>
								</FONT>
							</TD>
						</TR>
						
<?
						$Ord = 0;
						$sorted = array();
						foreach($ScoreBoard[$CarClass] as $CarNumber => $Score)
						{
							$sorted[] = array($CarNumber, floatval($Score)); // this looks pointless, but I need a way to peek ahead in the ordered array
						}
						
						foreach($sorted as $i => $data)
						{
							list($CarNumber, $Score) = $data;
							++$Ord;
							$placecolor = isset($trophy[intval($CarClass)][$Ord])?$trophy[intval($CarClass)][$Ord]:"black";
?>
							<TR>
								<TD>
									<TABLE style="border: 5px solid <?=$placecolor?>" WIDTH=100%>
										<TR>
											<TD WIDTH=33% style="text-align: left;">
												<FONT SIZE=+5>
												<B><?=Ordinal($Ord);?></B>
												</FONT>
											</TD>

											<TD WIDTH=33% style="text-align: center;">
												<FONT SIZE=+3>
 												<B><?=$arrClasses[intval($CarClass)]?></B>
												</FONT>
											</TD>

											
											<TD WIDTH=33% style="text-align: right;">
												<FONT SIZE=+5>
												<B><?=$CarNumber?></B>
												</FONT>
											</TD>
										</TR>
		
										<TR>
											<TD COLSPAN=3>
												<FONT SIZE=+2>
												<?=(trim(@$Driver[$CarNumber]) != "")?$Driver[$CarNumber]:"&nbsp;"?>
												</FONT>
											</TD>
											
										</TR>
		
										<TR>
											<TD COLSPAN=3>
												<FONT SIZE=+2>
												<?=(trim(@$Navigator[$CarNumber]) != "")?$Navigator[$CarNumber]:"&nbsp;"?>
												</FONT>
											</TD>
											
										</TR>

										<TR>
											<TD COLSPAN=3 style="font-size: 100px; text-align: center;">
<?
	if($Protest[$CarNumber] != 0) print("<FONT COLOR=RED>");
?>
												<B><?=intval($Score)>0?$Score:"";?><? // the PHP begins here so that there is no space between the score and the aserisk

												// if the intval of the next score in line is the same as mine and my floatval != my intval then mark me with an asterisk
												if(isset($sorted[$i+1]) &&
												   (floatval(intval($Score)) != floatval($Score)) &&
												   (intval($Score) == intval($sorted[$i+1][1]))) // [$i+1][1] is the score of the next car in line
												{
													print("<SUP>*</SUP>");
												}
	if($Protest[$CarNumber] != 0) print("</FONT>");
?>
												</B>
											</TD>
											
										</TR>
									</TABLE>
								</TD>
							</TR>
<?
						}						
?>					</TABLE>
				</TD>
<?
				}
?>
			</TR>
			<TR> <TD><br><br><br><br><br><br><br><br><br><br></TD> </TR>
		</TABLE>

