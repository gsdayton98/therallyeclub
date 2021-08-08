<?

function _get_browser()
{
  $browser = array ( //reversed array
   "OPERA",
   "MSIE",            // parent
   "NETSCAPE",
   "FIREFOX",
   "SAFARI",
   "KONQUEROR",
   "MOZILLA"        // parent
  );
  
  $info['browser'] = "OTHER";
   
  foreach ($browser as $parent)  
  {
   if ( ($s = strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), $parent)) !== FALSE )
   {            
     $f = $s + strlen($parent);
     $version = substr($_SERVER['HTTP_USER_AGENT'], $f, 5);
     $version = preg_replace('/[^0-9,.]/','',$version);
               
     $info['browser'] = $parent;
     $info['version'] = $version;
     break; // first match wins
   }
  }
  
  return $info;
}


	$ShowCheckAll = false;
	print("
	<SCRIPT  LANGUAGE=JavaScript>
	function CheckAllHaves(mode)
	{
		myform = document.getElementById('scoreform');
		
		for(element in myform.elements)
		{
			if(element != \"\" && element != null)
			{
	");

	$BrowserInfo = _get_browser();
	$Browser = $BrowserInfo["browser"];
	$BVersion = $BrowserInfo["version"];
	
	if(in_array($Browser,array("MSIE")))
	{
		$ShowCheckAll = true;
		// this works in IE
		print("
			if(element.substr(0, 4) == 'Have')
			{
				e = document.getElementById(element);
				e.checked = mode;	
			}
		");
	}
	else if(in_array($Browser,array("FIREFOX","MOZILLA","NETSCAPE")))
	{
		$ShowCheckAll = true;
		// this works with firefox
		print("
			if(myform[element].type == 'checkbox')
			{
				if(myform[element].name.substr(0, 4) == 'Have') 
				myform[element].checked = mode;
			}
		");
	}
	else
	{
		print("alert(element);");
	}
	
	print("
			}
		}
	}
	</SCRIPT>
	");
	
	if(!CHTTPVars::IsEmpty("checkall"))
	{
		$ShowCheckAll = intval(CHTTPVars::GetValue("checkall"));
	}

	// must be included
	$strSQL = "SELECT Steps FROM ars_rallye_base WHERE RallyeID = $RallyeID";
	$oDB->Query($strSQL);
	$arr = $oDB->GetRecordArray();
	if(count($arr))
	{
		$Steps = intval($arr[0][0]);
		
		$arrOption[1]  =  "&nbsp;Register&nbsp;";
		$arrOption[2]  = "&nbsp;Rescore&nbsp;All&nbsp;";
		$arrOption[3]  = "&nbsp;Protest&nbsp;";
		$arrOption[4]  = "&nbsp;Score&nbsp;";
		$arrOption[5]  = "&nbsp;Results&nbsp;";
		$arrOption[6]  = "&nbsp;Stats&nbsp;";
		if($ShowCheckAll)
		{
			$arrOption[7]  = "&nbsp;Check&nbsp;All&nbsp;";
			$arrOption[8]  = "&nbsp;Uncheck&nbsp;All&nbsp;";
		}
		/**
		$arrOption[4]  = "&nbsp;4. Combos&nbsp;";
		$arrOption[5]  = "&nbsp;5. Config Done&nbsp;";
		$arrOption[6]  = "&nbsp;6. Test Score&nbsp;";
		/**/
		
		$arrOpLink[1]  = "rfidread.php?RallyeID=$RallyeID&TestMode=".@$TestMode;
		$arrOpLink[2]  = "store.php?RallyeID=$RallyeID&action=rescoreall&TestMode=".@$TestMode;
		$arrOpLink[3]  = "protest.php?RallyeID=$RallyeID&checkall=0&TestMode=".@$TestMode;
		$arrOpLink[4]  = "score.php?RallyeID=$RallyeID&TestMode=".@$TestMode;
		$arrOpLink[5]  = "resultsboard2.php?RallyeID=$RallyeID&TestMode=".@$TestMode;
		$arrOpLink[6]  = "statst.php?RallyeID=$RallyeID&TestMode=".@$TestMode;
		if($ShowCheckAll)
		{
			$arrOpLink[7]  = "# OnClick=CheckAllHaves(true);";
			$arrOpLink[8]  = "# OnClick=CheckAllHaves(false);";
		}
		/**
		$arrOpText[4]  = "impcombo.php?RallyeID=$RallyeID";
		$arrOpText[5]  = "configdone.php?RallyeID=$RallyeID";
		$arrOpText[6]  = "score.php?RallyeID=$RallyeID&TestMode=1";
		/**/


		print("<TABLE BORDER=0>");
			print("<TR>");
			
/**
			print("<TD>");
				print("<B>Scoring Functions:</B>");
			print("</TD>");
/**/
			foreach($arrOption as $key => $val)
			{
				print("<TD STYLE=\"border-bottom: 1px solid black; border-right: 1px solid black; background-image : url(tab.gif); background-repeat: no-repeat;\">");
						print("<A HREF=".$arrOpLink[$key].">");
							print($val);
						print("</A>");
				print("</TD>");
			}		

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

/***
		print("<TABLE>");
			print("<TR>");
				print("<TD>");
					print("<B>Browser type:</B> $Browser $BVersion");
				print("</TD>");
			print("</TR>");
			print("<TR>");
				print("<TD>");
					print("<B>JavaScript capable:</B> <span id=javascriptison>NO</span>");
				print("</TD>");
			print("</TR>");
		print("</TABLE>");
		
		print("<SCRIPT  LANGUAGE=JavaScript>");
		print("element = document.getElementById('javascriptison');");
		print("element.innerHTML = 'YES';");
		print("</SCRIPT>");
/***/
	}
?>
