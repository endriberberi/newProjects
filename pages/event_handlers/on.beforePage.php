<?
//this function is called before any page is constructed
function on_beforePage($event) {	//$objElearn,$objElearnAsses,
	//measure the time of page generation
	global $session,$event,$HTTPS,$VS;
	$bo_environment = "";
	
	//define("MetaCodeLNG1", "en");		//kjo do te perdoret per te mbushur selektin ne editor

	//html_lang_code
	if (defined('BO_ENVIRONMENT') && BO_ENVIRONMENT!="") 
		$bo_environment = BO_ENVIRONMENT;
	if ($bo_environment=="CONTROLLER" || $bo_environment=="CONTROLLER_REVIEW" ) {
		$VS->initZonesUserProfile();	
		$VS->initLanguagesUserProfile();	
	} elseif ($bo_environment=="APPLICATION" || $bo_environment=="APPLICATION_BO") {
		include_once ASP_FRONT_PATH."php/find.region.SI.php";
		$obj_siInRegion = new siInRegionS();
		$VS->getPageRights();
		
		if (EXECUTION_TIME_INFO)
		{
			global $timer,$timer1;
			$timer->Start("WebApp", "<font color=ff0000>Time that spends the application for constructing the page</font>");
			//$timer1->Start("WebApp", "<font color=ff0000>Time that spends the application for constructing the page</font>");
			echo "<script language='javascript'>var debug_GoTo = false; </script>";
		}		


		
		/*$zone_id = "33,15,16";
		$zone_id = "15";
		$zone_mode_right = "multi";

		$obj_siInRegion = new siInRegionS($zone_id,$zone_mode_right,"no");
		$cnt_related = $obj_siInRegion->cnt_related;
		if ($cnt_related>0) {
			WebApp::addGlobalVar("exist_right","yes");
			WebApp::addGlobalVar("class_rightsidebar"," sidebar-opposite-visible");
		} else {
			WebApp::addGlobalVar("exist_right","no");
			WebApp::addGlobalVar("class_rightsidebar","");
		}	*/

		$zone_id_left = "7";
		$zone_mode_left = "multi";

		$obj_siInRegion = new siInRegionS($zone_id_left,$zone_mode_left,"no");
		$cnt_related = $obj_siInRegion->cnt_related;
		if ($cnt_related>0) {
			WebApp::addGlobalVar("exist_left","yes");
		} else {
			WebApp::addGlobalVar("exist_left","no");
		}	

		 
		
		
		
		
	} else if ($bo_environment=="OTHER") {
		$tools_id_validation = $tools_param;
	}	
	
	//eregi_replace nuk eshte  php7 compatible
	//$session->Vars["ln"] = eregi_replace ("Lng","",$session->Vars["lang"]);
	$session->Vars["ln"] = str_replace("Lng","",$session->Vars["lang"]);
	WebApp::determine_state_general($session->Vars["level_0"].",".$session->Vars["level_1"].",".$session->Vars["level_2"].",".$session->Vars["level_3"].",".$session->Vars["level_4"].",".$session->Vars["contentId"]);

	if ($session->Vars["ses_userid"]!=2) {
		INCLUDE_ONCE INC_PATH."user.functionality.class.php";
		$objUsr = new UserFullFunctionality();
		$objUsr->getUserInfo($session->Vars["ses_userid"]);	
	}
	
  $lang_meta_code  = "MetaCodeLNG".$session->Vars["ln"]; 
  $lang_meta_code  = ""; 
  IF (DEFINED($lang_meta_code)) {
  		//if (defined('PLATFORM_MODE') && PLATFORM_MODE=="ELEARNING") { 
  		$lang_meta_code_val = CONSTANT($lang_meta_code);
  }
  WebApp::addGlobalVar("xml_lang", $lang_meta_code_val);
  

  
}