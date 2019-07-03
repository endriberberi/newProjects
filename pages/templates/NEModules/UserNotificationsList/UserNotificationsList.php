<?
function UserNotificationsList_onRender() 
{
	global $session,$event;
	extract($event->args);

	global $session,$global_cache_dynamic,$cacheDyn;
	
	$NEM_TEMPLATE = "";	
	
	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {
	
		$NEM_FILENAME = 'NotificationFullPage.html';
		$prop_arr = WebApp::clearNemAtributes($session->Vars["idstemp"]);

		require_once(INC_PHP_AJAX."NemsManager.class.php");
		$grn = NemsManager::getFrontEndGeneralProperties($prop_arr);
		
		if (isset($grn["NEM_FILENAME"]) && $grn["NEM_FILENAME"]!='') {
			$NEM_FILENAME = $grn["NEM_FILENAME"];
		}


		
		
		$defaultProperties = array();
		
		$defaultProperties = $grn;
		$defaultProperties["more_link"] 			= "{{_more_link}}";
		$defaultProperties["badge_text"] 			= "{{_badge_text}}";
		$defaultProperties["notification_empty"]	= "{{_notification_empty}}";
		
		$defaultProperties["badge_text_empty"]		= "{{_badge_text_empty}}";
		$defaultProperties["badge_text_one"]		= "{{_badge_text_one}}";
		$defaultProperties["badge_text_multi"]		= "{{_badge_text_multi}}";

		$defaultProperties["close_lbl"]						= "{{_close_lbl}}";
		$defaultProperties["delete_notification_confirm"]	= "{{_delete_notification_confirm}}";
		$defaultProperties["modal_title_details"]			= "{{_modal_title_details}}";
		$defaultProperties["delete_item"]					= "{{_delete_item}}";	
		$defaultProperties["delete_all_items"]				= "{{_delete_all_items}}";	
		$defaultProperties["delete_all_notifications_confirm"]	= "{{_delete_all_notifications_confirm}}";
		
		if (isset($prop_arr)) { 
			$prop_nem_arr = $prop_arr;
			while (list($key,$value)=each($prop_nem_arr)) {
				if (isset($defaultProperties[$key]))
					$defaultProperties[$key] = $prop_nem_arr[$key];
			}
		}		
		
		require_once(INC_PATH.'generalFunctionalityClass.php');
		$defaultProperties["badge_text"] 		= generalFunctionality::getPlaceholdersToReplace($defaultProperties["badge_text"]);
		$defaultProperties["badge_text_empty"] 	= generalFunctionality::getPlaceholdersToReplace($defaultProperties["badge_text_empty"]);
		$defaultProperties["badge_text_one"] 	= generalFunctionality::getPlaceholdersToReplace($defaultProperties["badge_text_one"]);
		$defaultProperties["badge_text_multi"] 	= generalFunctionality::getPlaceholdersToReplace($defaultProperties["badge_text_multi"]);

		/*reset($defaultProperties);
		while (list($key,$value)=each($defaultProperties)) {
			WebApp::addVar("$key", "$value");
		}	*/	
				
		/* notification list data*/
		

		$objUsr = new UserFullFunctionality();	
		$readdedMessage = ""; //y:only readed|n:onlyUnreaded
		$fullList = "yes"; //yes:inlcude List|no:onlyTotals
		$dtToRtrn = $objUsr->getUserAlertInSystem($session->Vars["ses_userid"],$readdedMessage,$fullList);   

		$totals["nr_total"] = "0";
		$totals["nr_new"] = "0";
		$totals["nr_readed"] = "0";
		$tmpConf["data"][0] = $defaultProperties;

		if (isset($dtToRtrn["totals"]) && count($dtToRtrn["totals"])>0) {
			while (list($key,$value)=each($dtToRtrn["totals"])) {
				//WebApp::addVar("$key", "".$value);
				$totals[$key] = "".$value;
			}
			$tmpConf["data"][0] =array_merge($totals, $tmpConf["data"][0]);
		}

		$tmpConf["AllRecs"] = count($tmpConf["data"]);
		WebApp::addVar("notif_".$session->Vars["idstemp"], $tmpConf);  	
		
		
		/*echo "<textarea>";
		print_r($prop_arr);
		print_r($tmpConf);
		echo "</textarea>";*/
		
		WebApp::addVar("modal_title_details", $defaultProperties["modal_title_details"]);  	
		WebApp::addVar("delete_notification_confirm", $defaultProperties["delete_notification_confirm"]);  	
		WebApp::addVar("delete_all_notifications_confirm", $defaultProperties["delete_all_notifications_confirm"]);  	
		
		
		
		
		$NEM_TEMPLATE = '<Include SRC="{{NEMODULES_PATH}}UserNotificationsList/'.$NEM_FILENAME.'"/>';	
		
		//$NEM_FILENAME
		
		
		if ($_REQUEST["apprcss"] == "notifications") {
			if ($_REQUEST["action"] == "refreshBadge") {
				$NEM_TEMPLATE = '<Include SRC="{{NEMODULES_PATH}}UserNotificationsList/NotificationIconPageWithAjaxBadgeInside.html"/>';	
			}
			if ($_REQUEST["action"] == "viewItem") {
				$dtToRtrn = $objUsr->getUserAlertInfoInSystem($session->Vars["ses_userid"],$_REQUEST["itemId"]);   
				$NEM_TEMPLATE = '<Include SRC="{{NEMODULES_PATH}}UserNotificationsList/NotificationItemDetails.html"/>';	
			}	
			if ($_REQUEST["action"] == "refreshNotList") {
				$NEM_TEMPLATE = '<Include SRC="{{NEMODULES_PATH}}UserNotificationsList/list_of_notifications.html"/>';	
			}			
		}		
	}	
 	WebApp::addVar("inc_TEMPLATE",$NEM_TEMPLATE);
}
?>