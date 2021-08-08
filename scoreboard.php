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

$strSQL="SELECT CarNumber, CarClass, Driver, Navigator, Passenger, BaseScore, ProtestScore, DClub, NClub, PClub FROM ars_scoresheet WHERE RallyeID = $RallyeID ORDER By CarClass, CarNumber";
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

if(!@$sub)
{
?>

<HTML>
	<HEAD>
		<TITLE>ARS - Scoreboard</TITLE>
		<link rel="stylesheet" href="scorestyle.css">
<?
if(!@$sub)
{
		print("<meta http-equiv=refresh content=30>");
}
?>
	</HEAD>
	
	<BODY>
<?
}
?>
		<TABLE BORDER=1 width=100%>
			<TR>
<?
				foreach($ScoreBoard as $CarClass => $data)
				{
?>
				<TD VALIGN=TOP>
					<TABLE BORDER=1 width=100%>
						<TR>
							<TD WIDTH=100% align=center>
								<B><?=$arrClasses[intval($CarClass)]?></B>
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
?>
							<TR>
								<TD>
									<TABLE WIDTH=100%>
										<TR>
											<TD WIDTH=50% style="text-align: left;">
												<B><?=Ordinal(++$Ord);?></B>
											</TD>
											
											<TD WIDTH=50% style="text-align: right;">
												<B><?=$CarNumber?></B>
											</TD>
										</TR>
		
										<TR>
											<TD COLSPAN=2>
												<?=(trim(@$Driver[$CarNumber]) != "")?$Driver[$CarNumber]:"&nbsp;"?>
											</TD>
											
										</TR>
		
										<TR>
											<TD COLSPAN=2>
												<?=(trim(@$Navigator[$CarNumber]) != "")?$Navigator[$CarNumber]:"&nbsp;"?>
											</TD>
											
										</TR>

										<TR>
											<TD COLSPAN=2 style="text-align: center;">
<?
	if($Protest[$CarNumber] != 0) print("<FONT COLOR=RED>");
?>
												<FONT SIZE=+2<B><?=intval($Score)?intval($Score):"";?><? // the PHP begins here so that there is no space between the score and the aserisk

												// if the intval of the next score in line is the same as mine and my floatval != my intval then mark me with an asterisk
												if(isset($sorted[$i+1]) &&
												   (floatval(intval($Score)) != floatval($Score)) &&
												   (intval($Score) == intval($sorted[$i+1][1]))) // [$i+1][1] is the score of the next car in line
												{
													print("<SUP>*</SUP>");
												}
	if($Protest[$CarNumber] != 0) print("</FONT>");
?>
												</B></FONT>
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
		</TABLE>

<?
if(!@$sub)
{
?>
		<FORM METHOD=POST>
		<INPUT TYPE=HIDDEN NAME=RallyeID VALUE=<?=$RallyeID?>>
		</FORM>
	</BODY>
</HTML>
<?
}
?>