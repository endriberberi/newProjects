<?
function GuideMapNEM_eventHandler($event) 
{
	global $session,$event;
	extract($event->args);
}
function loadConfigurationsTypes($prop_arr=array(),$userRoles,$confType="", $logOperator="", $butNot="", $confId){
	$CIids = array();
	$logOperator = $prop_arr[$logOperator][$confId];

	if(isset($prop_arr[$butNot][$confId]) && $prop_arr[$butNot][$confId]>0){
		$hasButNotRoles = "false";
		foreach($prop_arr[$butNot][$confId] as $bnKey => $bnVal){
			if(in_array($bnVal,$userRoles)){
				$hasButNotRoles = "true";
				break;
			}
		}

		if($hasButNotRoles === "false"){
			
			if(isset($prop_arr[$confType][$confId]) && $prop_arr[$confType][$confId]>0){
					
					if($logOperator == "or"){
						foreach ($prop_arr[$confType][$confId] as $keyR => $valR) {
							
							if(in_array($valR, $userRoles)){
								$CIids[''.$confId] = $prop_arr["ci_to_include"][$confId];
								break;
							}
						}

					}elseif($logOperator == "and"){

						$countRoles = count($prop_arr[$confType][$confId]);
						$newRoleCounter=0;
					
						foreach($prop_arr[$confType][$confId] as $keyR => $valR){
							if(in_array($valR, $userRoles)){
								$newRoleCounter++;
							}
						}
						if($countRoles == $newRoleCounter){
							$CIids[''.$confId] = $prop_arr["ci_to_include"][$confId];					
						}
					}
				
				}
			}

		}else{
			if(isset($prop_arr[$confType][$confId]) && $prop_arr[$confType][$confId]>0){
				
				if($logOperator == "or"){
					foreach ($prop_arr[$confType][$confId] as $keyR => $valR) {
						
						if(in_array($valR, $userRoles)){
							$CIids[''.$confId] = $prop_arr["ci_to_include"][$confId];
							break;
						}
					}

				}elseif($logOperator == "and"){

					$countRoles = count($prop_arr[$confType][$confId]);
					$newRoleCounter=0;
				
					foreach($prop_arr[$confType][$confId] as $keyR => $valR){
						if(in_array($valR, $userRoles)){
							$newRoleCounter++;
						}
					}
					if($countRoles == $newRoleCounter){
						$CIids[''.$confId] = $prop_arr["ci_to_include"][$confId];					
					}
				}
			
			}
			
		}

	return $CIids;
}

function GuideMapNEM_onRender() 
{
	global $session;
//$starts = WebApp::get_formatted_microtime();

	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {
		$prop_arr = unserialize(base64_decode(WebApp::findNemProp($session->Vars["idstemp"])));

		$templateTypeSelected = 'default_template.html';
		if (isset($prop_arr["templateType"]) && $prop_arr["templateType"] != "") {

			//selektohet template ----------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$prop_arr["templateType"]."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF()) {
				$templateTypeSelected = $rs->Field("template_box");
			}
			//------------------------------------------------------------------------------------------------------------------------
		}

		WebApp::addVar("GuideMapNEM_TEMPLATE","<Include SRC=\"{{NEMODULES_PATH}}GuideMapNEM/".$templateTypeSelected."\"/>");
	}

	/*echo'<textarea>';
		print_r($prop_arr);
	echo'</textarea>';
*/
	if(count($prop_arr) > 0)
			foreach($prop_arr as $k => $v)
				if(!is_array($v))
					WebApp::addVar($k, $v);
	
	$userId = $session->Vars["ses_userid"];
	
	$userFullName = "";
	$getUserNameSql = "SELECT FirstName, SecondName, usr_title FROM users WHERE UserId='".$userId."'";
	$rsName = WebApp::execQuery($getUserNameSql);
	if(!$rsName->EOF()){
		$firstName  = $rsName->Field("FirstName"); 
		$secondName = $rsName->Field("SecondName"); 
		$usr_title  = $rsName->Field("usr_title");
		$userFullName = $firstName." ".$secondName; 
	}



	WebApp::addVar("UserName",$userFullName);

/*	echo'<textarea>';
		print_r($prop_arr);
	echo'</textarea>';*/

		$userRoles =array();
		$selectUserRoles = "SELECT profil_id FROM user_profile WHERE UserId='".$userId."'";
		$rsUserRoles = WebApp::execQuery($selectUserRoles);
		while(!$rsUserRoles->EOF()){
			$userRoles[] = $rsUserRoles->Field("profil_id");
			$rsUserRoles->MoveNext();
		}

	
	$CIids=array();

	if($session->Vars["ses_userid"] == "1" || $session->Vars["tip"] == "1"){
		if(isset($prop_arr["ci_to_include"]) && $prop_arr["ci_to_include"]>0){
			foreach ($prop_arr["ci_to_include"] as $key => $value) {
				$CIids[$key] = $value;
			}
		}
	}elseif($session->Vars["typeOfUser"] == "BO"){


		//$prop_arr["internalSelectedRoles"][1] = 1;
		if(isset($prop_arr["internalSelectedRoles"]) && $prop_arr["internalSelectedRoles"] > 0){
			foreach($prop_arr["internalSelectedRoles"] as $keyIr => $valIr){
				if(in_array($valIr, $userRoles)){
					if(isset($prop_arr["ci_to_include"]) && $prop_arr["ci_to_include"]>0){
						foreach ($prop_arr["ci_to_include"] as $key => $value) {
							$CIids[$key] = $value;
						}
					}
					break;
				}
			}

		}

	}else{

		$getUserRegisteredAcountIds="SELECT reg_account_ids FROM user_registered_accounts WHERE UserId='".$userId."'";
		$rsRegAcount = WebApp::execQuery($getUserRegisteredAcountIds);
		$userRegAcontIds = "";
		if(!$rsRegAcount->EOF()){
			$userRegAcontIds = $rsRegAcount->Field("reg_account_ids");
		}
		$userRegAcontIdsArray = explode(",", $userRegAcontIds);

		
		$copyuserRegAcontIdsArray = $userRegAcontIdsArray;
		//sort($userRegAcontIdsArray);

		$regProfilesids = array();
		if(isset($prop_arr["ci_to_include"]) && $prop_arr["ci_to_include"]>0){
			foreach ($prop_arr["ci_to_include"] as $key => $value) {
				
				$userRegAcontIdsArray = $copyuserRegAcontIdsArray;

				$key = ''.$key;
				$configurationType = $prop_arr["configuration_by_type"][$key];
			
				if($configurationType == "profiles"){ // congiguratio with Profiles
					$logOperator = $prop_arr["logOperator"][$key];
					if(isset($prop_arr["selectedProfiles"][$key]) && $prop_arr["selectedProfiles"][$key] != ""){

						if($logOperator == "or"){
							foreach($prop_arr["selectedProfiles"][$key] as $keyR => $valR){
								if(in_array(''.$valR, $userRegAcontIdsArray)){
									$CIids[''.$key] = $prop_arr["ci_to_include"][$key];
									break;
								}
						
							}

						}elseif($logOperator == "and"){
							$countProfiles= count($prop_arr["selectedProfiles"][$key]);
							$newCounter=0;
							foreach($prop_arr["selectedProfiles"][$key] as $keyR => $valR){
								if(in_array(''.$valR, $userRegAcontIdsArray)){
									$newCounter++;
								}
							}
							if($countProfiles == $newCounter){
								$CIids[''.$key] = $prop_arr["ci_to_include"][$key];					
							}				
						}elseif($logOperator == "equal"){
							$equalRoles = implode(",",$prop_arr["selectedProfiles"][$key]);
												
							/*Change string as is needed to match profiles*/
									
							if (($keyArr = array_search('main_profile', $userRegAcontIdsArray)) !== false) {
								if(count($userRegAcontIdsArray) > 1){
							    	unset($userRegAcontIdsArray[$keyArr]);									
							   		sort($userRegAcontIdsArray);
									$newRegUserProfArray = "main_profile,".implode(",",$userRegAcontIdsArray);
								}else{
									$newRegUserProfArray = implode(",",$userRegAcontIdsArray);
								}
							  
							}
							else{
								sort($userRegAcontIdsArray);
								$newRegUserProfArray = implode(",",$userRegAcontIdsArray);
							}

							if($equalRoles == $newRegUserProfArray){
								$CIids[$key] = $prop_arr["ci_to_include"][$key];
							}
						}
					}

				}elseif($configurationType == "subscR"){ //configuration with roles

					$CIids = loadConfigurationsTypes($prop_arr,$userRoles,"registration_roles", "logOperatorRoles", "but_not_roles", $key);


				}elseif($configurationType == "statusR"){

					$CIids = loadConfigurationsTypes($prop_arr,$userRoles,"status_roles", "statusRolesLogOp", "butNotStatusRoles", $key);

				}
				elseif($configurationType == "allR"){

					$CIids = loadConfigurationsTypes($prop_arr,$userRoles,"all_roles", "allRolesLogOp", "butNotAllRoles", $key);

				}
				
			}
		}
	}


	$CIidsRes = array_unique($CIids);
	$CiGrid = array("data" => array(), "AllRecs" => "0"); 
	$ind=0;
	$CIArray = array();
	foreach ($CIidsRes as $keyCI => $valueCI) {

			$state_var	 = WebApp::generalizeCoordinates($valueCI);
			if ($state_var!="") {
				$koord = explode (",",$state_var);
				$arr_values = WebApp::check_cords($koord);

				if ($arr_values!="") {
					$CIArray["data"][$ind]["CID_IN_LIST"] = $valueCI;
				}
			} 

		
		
		
		
		$ind++;
	}

	$CiGrid["data"]      = $CIArray["data"];
    $CiGrid["AllRecs"]   = count($CIArray["data"]);
	WebApp::addVar("CiGrid", $CiGrid);


	/*echo'<textarea>';
		print_r($CiGrid);
	echo'</textarea>';*/




	$headline = "Welcome";
	if(isset($prop_arr["headline"]) && $prop_arr["headline"] != ""){
		$headline = $prop_arr["headline"];
	}
	WebApp::addVar("headline", $headline);

	if(isset($prop_arr["enable_users_name_show"]) && $prop_arr["enable_users_name_show"] == 'y'){
		WebApp::addVar("enable_user_to_show", "yes");
	}else{
		WebApp::addVar("enable_user_to_show", "no");
	}
	$backgroundUrl = APP_URL."graphics/home-cover.png";
	if(isset($prop_arr["slogan_icon_show"]) && $prop_arr["slogan_icon_show"] == "image"){
		if(isset($prop_arr["blockImage"]) && $prop_arr["blockImage"]>0)
			$backgroundUrl = APP_URL."show_image.php?file_id=".$prop_arr["blockImage"];
	}

	WebApp::addVar("backgroundUrl",$backgroundUrl);


//$totals = WebApp::get_formatted_microtime() - $starts;
//echo $debugTime = "inside GuideMapNEM END->".round($totals, 2).":totals";

	require_once(INC_PHP_AJAX."NemsManager.class.php");
	$grn = NemsManager::getFrontEndGeneralProperties($prop_arr);





}