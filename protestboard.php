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
	
	
	$RallyeID = CHTTPVars::GetValue("RallyeID"); // any one can look at the protestboard, so there is no need for password here
	
	
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

?>

<HTML>
	<HEAD>
		<TITLE>ARS - Protests</TITLE>
		<link rel="stylesheet" href="stylesheet.css">
	</HEAD>
	
	<BODY>
<?
}

$strSQL="SELECT ProtestID,Points,Reason FROM ars_protest WHERE RallyeID = $RallyeID";
$oDB->Query($strSQL);
$arrProtests = $oDB->GetRecordArray(false);
?>
		<TABLE BORDER=1>
			<TR>
				<TD WIDTH=5%>
					&nbsp;
				</TD>

				<TD WIDTH=5%>
					<B>Pts.</B>
				</TD>

				<TD WIDTH=50%>
					<B>Reason</B>
				</TD>

				<TD WIDTH=40%>
					<B>Criteria</B>
				</TD>
			</TR>

<?
			if(count($arrProtests))
			foreach($arrProtests as $data)
			{
				list($ProtestID, $ProtestPoints, $ProtestReason) = $data;

				$strSQL="SELECT Class,CarNumber,F,X,Y,O,Have FROM ars_protest_elements WHERE ProtestID = $ProtestID";
				$arr = $oDB->Query($strSQL);
                $arr = $oDB->GetRecordArray(false);
                $arrProtestClasses = array();   
                $arrProtestCarNumbers = array();
                $arrProtestElements = array();
                foreach($arr as $data)
                {               
                        list($ProtestClass, $ProtestCarNumber, $ProtestF, $ProtestX, $ProtestY, $ProtestO, $ProtestHave) = $data;
                        $ProtestClass = intval($ProtestClass);
                        $ProtestCarNumber = intval($ProtestCarNumber);
                                        
                        if($ProtestClass > 0)
                        {
                                $arrProtestClasses[] = $ProtestClass;
                        }

                        if($ProtestCarNumber > 0)
                        {
                                $arrProtestCarNumbers[] = $ProtestCarNumber;
                        }

                        if(trim($ProtestF) != "")
                        {
                                $arrProtestElements[] = array($ProtestF, $ProtestX, $ProtestY, $ProtestO, $ProtestHave);
                        }
                }


				print("<TR>");
					print("<TD VALIGN=TOP>");
						print("<A HREF=protest.php?RallyeID=$RallyeID&action=delete&ProtestID=$ProtestID>DEL</A>");
					print("</TD>");

					print("<TD VALIGN=TOP>");
						print($ProtestPoints);
					print("</TD>");

					print("<TD VALIGN=TOP>");
						if(trim($ProtestReason) == "")
							print("&nbsp;");
						else
							print($ProtestReason);
					print("</TD>");

					print("<TD VALIGN=TOP>");
						if(count($arrProtestCarNumbers))
						{
							print("Car: ".implode(", ",$arrProtestCarNumbers)." ");
						}

						if(count($arrProtestClasses))
						{
							print("in ");
							$sep="";
							foreach($arrProtestClasses as $val)
							{
								print($sep.$arrClasses[$val]);
								$sep=", ";
							}
							print("class ");
						}

						$sep="";
						if(count($arrProtestElements))
						foreach($arrProtestElements as $data)
						{
							list($ProtestF, $ProtestX, $ProtestY, $ProtestO, $ProtestHave) = $data;
							
							// what is the real name of this element
							$strSQL="SELECT Name FROM ars_rallye_cells WHERE F = '$ProtestF' AND X=$ProtestX AND Y=$ProtestY AND O=$ProtestO AND RallyeID = $RallyeID";
							$oDB->Query($strSQL);
							$arrName = $oDB->GetRecordArray(false);
							if(count($arrName))
							{
								print($sep);
								if($ProtestHave > 0)
									print("Having ");
								else if($ProtestHave < 0)
									print("NOT Having ");
								print($arrName[0][0]);
								$sep=", ";
							}
						}
					print("</TD>");
				print("</TR>");
			}
?>
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
