<?
	// must be included
	$strSQL = "SELECT Steps FROM ars_rallye_base WHERE RallyeID = $RallyeID";
	$oDB->Query($strSQL);
	$arr = $oDB->GetRecordArray();
	if(count($arr))
	{
		$Steps = intval($arr[0][0]);
		
		$arrSteps[1]  = "&nbsp;1.&nbsp;Layout&nbsp;";
		$arrSteps[2]  = "&nbsp;2.&nbsp;Names&nbsp;";
		$arrSteps[4]  = "&nbsp;3.&nbsp;Values&nbsp;";
		$arrSteps[8]  = "&nbsp;4.&nbsp;Combos&nbsp;";
		$arrSteps[16] = "&nbsp;5.&nbsp;Config&nbsp;Done&nbsp;";
		$arrSteps[48] = "&nbsp;6.&nbsp;Test&nbsp; Score &nbsp;";
		
		$arrLinks[1]  = "configure.php?RallyeID=$RallyeID";
		$arrLinks[2]  = "name.php?RallyeID=$RallyeID";
		$arrLinks[4]  = "value.php?RallyeID=$RallyeID";
		$arrLinks[8]  = "impcombo.php?RallyeID=$RallyeID";
		$arrLinks[16] = "configdone.php?RallyeID=$RallyeID";
		$arrLinks[48] = "score.php?RallyeID=$RallyeID&TestMode=1";


		print("<TABLE BORDER=0>");
			print("<TR>");
			
/**
			print("<TD>");
				print("<B>Configuration&nbsp;Steps:</B>");
			print("</TD>");
/**/

			foreach($arrSteps as $key => $val)
			{
				print("<TD STYLE=\"border-bottom: 1px solid black; border-right: 1px solid black; background-image : url(tab.gif); background-repeat: no-repeat;\">");
					if(!($Steps & $key))
					{
						$Color="gray";
						$ShowLink=false;
					}
					else
					{
						$Color="black";
						$ShowLink=true;
					}
	
					if($ThisStep == $key)
					{
						$Weight="B";
						$Color="dimgray";
					}
					else
						$Weight="N"; // doesn't do anything
					
	
					
					print("<FONT COLOR=$Color>");
						print("<$Weight>");
							if($ShowLink)
							{
								print("<A HREF=\"".$arrLinks[$key]."\">");
							}
							print($val);
							if($ShowLink)
							{
								print("</A>");
							}
						print("</$Weight>");
					print("</FONT>");
				print("</TD>");
			}		

				print("<TD>");
					print("&nbsp;&nbsp;&nbsp;&nbsp;");
				print("</TD>");
				print("<TD STYLE=\"border-bottom: 1px solid black; border-right: 1px solid black; background-image : url(tab.gif); background-repeat: no-repeat;\">");
					print("<A HREF=\"export.php?RallyeID=${RallyeID}\">");
					print("&nbsp;Export&nbsp;Rallye&nbsp;");
					print("</A>");
				print("</TD>");

				if(!@$LogoutButton)
				{
					print("<TD>");
						print("&nbsp;&nbsp;&nbsp;&nbsp;");
					print("</TD>");
	
					print("<TD STYLE=\"border-bottom: 1px solid black; border-right: 1px solid black; background-image : url(tab.gif); background-repeat: no-repeat;\">");
						print("<A HREF=index.php>");
						print("&nbsp;Logout&nbsp;");
						print("</A>");
						$LogoutButton=true;
					print("</TD>");
				}
			print("</TR>");
		print("</TABLE>");
	}
?>
