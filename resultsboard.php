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
	
	
	$RallyeID = CHTTPVars::GetValue("RallyeID"); 
	$TestMode = CHTTPVars::GetValue("TestMode"); 
	
	
	if(!CHTTPVars::IsEmpty("action"))
	{
		$action = CHTTPVars::GetValue("action");
		
		switch(strtolower($action))
		{
			case "done":
				redirect("score.php?RallyeID=$RallyeID&TestMode=$TestMode");
				break;
				
			default:
				redirect("error.php");
		}
	}
}

// must load this information regardless of weather we are are recording or scoring
$strSQL="SELECT * FROM ars_rallye_base WHERE RallyeID = $RallyeID";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray("RallyeName, RallyeDate, RallyeMaster, TopX, TopY, CMX, CMY, CPX, CPY, BotX, BotY, CPLoc");
if(count($arr))
{
	list($RallyeName, $RallyeDate, $RallyeMaster, $TopX, $TopY, $CMX, $CMY, $CPX, $CPY, $BotX, $BotY, $CPLoc) = $arr[0];
}
	
$strSQL="SELECT CarNumber, CarClass, Driver, Navigator, Passenger, BaseScore, ProtestScore FROM ars_scoresheet WHERE RallyeID = $RallyeID ORDER By CarClass, CarNumber";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray(false);
$ScoreBoard = array();
foreach($arr as $data)
{
	list($CarNumber, $CarClass, $DriverName, $NavigatorName, $PassengerName, $BaseScore, $ProtestScore) = $data;
	
	$ScoreBoard[$CarClass][$CarNumber] = floatval($BaseScore) + floatval($ProtestScore);
	$Driver[$CarNumber] = $DriverName;
	$Navigator[$CarNumber] = $NavigatorName;
	if(trim($PassengerName) != "")
		$Passenger[$CarNumber] = $PassengerName;
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
		<TITLE>ARS - Results</TITLE>
		<link rel="stylesheet" href="stylesheet.css">
	</HEAD>
	
	<BODY>

		<FORM METHOD=POST>
		<INPUT TYPE=HIDDEN NAME=RallyeID VALUE=<?=$RallyeID?>>
		<INPUT TYPE=HIDDEN NAME=TestMode VALUE=<?=$TestMode?>>
		<INPUT TYPE=SUBMIT NAME=action VALUE=Done>
		</FORM>
<?
}
?>
		<TABLE WIDTH=550>
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
		T = Award Issued<BR>
                * = Tie Broken<BR>
                P = Score affected by protest<BR>
                X = NCSCC Christmas Tree Award<BR>
		<BR>
		<TABLE WIDTH=550>
			<TR>
				<TD  ALIGN=LEFT VALIGN=BOTTOM>
					<B>C<BR>
				           l<BR>
					   a<BR>
					   s<BR>
					   s</B>
				</TD>

				<TD  ALIGN=LEFT VALIGN=BOTTOM>
					<B>P<BR>
					   l<BR>
					   a<BR>
					   c<BR>
					   e</B>
				</TD>

				<TD ALIGN=LEFT  VALIGN=BOTTOM>
					<B>C<BR>
					   a<BR>
					   r</B>
				</TD>

				<TD  ALIGN=LEFT VALIGN=BOTTOM>
					<B>S<BR>
					   c<BR>
					   o<BR>
					   r<BR>
					   e</B>
				</TD>
				<TD  ALIGN=LEFT VALIGN=BOTTOM>
					<B>N<BR>
					   o<BR>
					   t<BR>
					   e<BR>
					   s</B>
				</TD>

				<TD VALIGN=BOTTOM>
					<B>Driver</B>
				</TD>

				<TD VALIGN=BOTTOM>
					<B>Navigator</B>
				</TD>

				<TD VALIGN=BOTTOM>
					<B>Passenger</B>
				</TD>
			</TR>
<?
			foreach($ScoreBoard as $CarClass => $data)
			{
?>
						
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
						<TD VALIGN=TOP STYLE="border-top: solid black 1px;">
							<?=$arrClasses[intval($CarClass)]{0}?>
						</TD>

						<TD VALIGN=TOP STYLE="border-top: solid black 1px;" ALIGN=RIGHT>
							<?=++$Ord /*Ordinal(++$Ord);*/?>
						</TD>

						<TD VALIGN=TOP STYLE="border-top: solid black 1px;" ALIGN=RIGHT>
							<?=$CarNumber?>
						</TD>

						<TD VALIGN=TOP STYLE="border-top: solid black 1px;" ALIGN=RIGHT>
							<?=intval($Score);?>
						</TD>
									
						<TD VALIGN=TOP STYLE="border-top: solid black 1px;">
<?
							// trophied
							if(in_array($CarClass, array(1,2,3)) && in_array($Ord,array(1,2,3)))
							{
								print("T");
							}
							else if(in_array($CarClass, array(4,5)) && in_array($Ord,array(1,2)))
							{
								print("T");
							}
							else if(in_array($CarClass, array(6)) && in_array($Ord,array(1)))
							{
								print("T");
							}
							else
								print("&nbsp;");

							// ties broken
							if(isset($sorted[$i+1]) &&
							  (floatval(intval($Score)) != floatval($Score)) &&
							  (intval($Score) == intval($sorted[$i+1][1]))) // [$i+1][1] is the score of the next car in line
							{
								print("*");
							}
							else
								print("&nbsp;");

							// protest
							if($Protest[$CarNumber] != 0) 
								print("P");
							else
								print("&nbsp;");

							// Xmas tree award
							print("&nbsp;");
?>
						</TD>
											
						<TD VALIGN=TOP STYLE="border-top: solid black 1px;">
							<?=(trim(@$Driver[$CarNumber]) != "")?$Driver[$CarNumber]:"&nbsp;"?>
						</TD>
										
						<TD VALIGN=TOP STYLE="border-top: solid black 1px;">
							<?=(trim(@$Navigator[$CarNumber]) != "")?$Navigator[$CarNumber]:"&nbsp;"?>
						</TD>

						<TD VALIGN=TOP STYLE="border-top: solid black 1px;">
							<?=(strlen($Passenger[$CarNumber])?str_replace(",",",<BR>",$Passenger[$CarNumber]):"&nbsp;")?>
						</TD>
					</TR>
<?
/***
					if(isset($Passenger[$CarNumber]))
					{
?>
						<TR>
							<TD>
							</TD>
							<TD>
							</TD>
							<TD>
							</TD>
							<TD ALIGN=RIGHT COLSPAN=2>
								<B>Passenger </B>
							</TD>
							<TD COLSPAN=2>
								<?=$Passenger[$CarNumber]?>
							</TD>
						</TR>
<?

					}
/***/
				}						
?>
				<TR>
					<TD>
						<BR>
					</TD>
				</TR>
<?
			}
?>
		</TABLE>

<?
if(!@$sub)
{
?>
		<FORM METHOD=POST>
		<INPUT TYPE=HIDDEN NAME=RallyeID VALUE=<?=$RallyeID?>>
		<INPUT TYPE=HIDDEN NAME=TestMode VALUE=<?=$TestMode?>>
		<INPUT TYPE=SUBMIT NAME=action VALUE=Done>
		</FORM>
	</BODY>
</HTML>
<?
}
?>
