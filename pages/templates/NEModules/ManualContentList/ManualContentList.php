<?php

function ciConfigurationSettings($categKey, $itemKey, $itemId, $configuration_by_type, $prop_arr){
	global $session;

	$userRoles = array();
	$selectedRoles = array();
	$butNotRoles = array();
	if(isset($session->Vars["tip"]) && $session->Vars["tip"] !=""){
		$userRoles = explode(",",$session->Vars["tip"]);
	}

	$response = "true";

	$selRolesKey = "";
	$butNotKey 	= "";
	$logOperator = "";
	$logicalOp = "";


	if($configuration_by_type == "profiles"){
		$selRolesKey 	= "selectedProfiles";
		$logOperator 	= "logOperator";

	}elseif($configuration_by_type == "subscR"){
		$selRolesKey 	= "registration_roles";
		$butNotKey 		= "but_not_roles";
		$logOperator 	= "logOperatorRoles";

	}elseif($configuration_by_type == "statusR"){
		$selRolesKey 	= "status_roles";
		$butNotKey 		= "butNotStatusRoles";
		$logOperator 	= "statusRolesLogOp";
	}elseif($configuration_by_type == "allR"){
		$selRolesKey 	= "all_roles";
		$butNotKey 		= "butNotAllRoles";
		$logOperator 	= "allRolesLogOp";
	}

	if(isset($prop_arr[$selRolesKey][$categKey][$itemKey]) && is_array($prop_arr[$selRolesKey][$categKey][$itemKey]) && count($prop_arr[$selRolesKey][$categKey][$itemKey])>0){
		$selectedRoles = $prop_arr[$selRolesKey][$categKey][$itemKey];
	}

	if(isset($prop_arr[$logOperator][$categKey][$itemKey]))
		$logicalOp = $prop_arr[$logOperator][$categKey][$itemKey];

	if($session->Vars["ses_userid"] == "1" || $session->Vars["tip"] =="1"){
		$response = "true";
	}elseif(count($userRoles)>0 && $configuration_by_type != "profiles"){
		if(isset($prop_arr[$butNotKey][$categKey][$itemKey]) && is_array($prop_arr[$butNotKey][$categKey][$itemKey]) && count($prop_arr[$butNotKey][$categKey][$itemKey])>0){
			$butNotRoles = $prop_arr[$butNotKey][$categKey][$itemKey];
		}

		if(count($butNotRoles)>0){
			foreach ($butNotRoles as $value) {
				if(in_array($value,$userRoles)){
					$response = "false";
					break;
				}
			}
		}

		if($response == "true"){
			if(count($selectedRoles)>0){
				if($logicalOp == "and"){
					foreach ($selectedRoles as $value){
						if(!in_array($value, $userRoles)){
							$response = "false";
							break;
						}
					}
				}elseif($logicalOp == "or"){
					$response = "false";
					foreach ($selectedRoles as $value) {
						if(in_array($value, $userRoles)){
							$response = "true";
							break;
						}
					}

				}
			}
		}
	}elseif(count($userRoles)>0 && $configuration_by_type == "profiles"){
		$getUserRegisteredAcountIds="SELECT reg_account_ids FROM user_registered_accounts WHERE UserId='".$session->Vars["ses_userid"]."'";
		$rsRegAcount = WebApp::execQuery($getUserRegisteredAcountIds);
		$userRegAcontIds = "";
		if(!$rsRegAcount->EOF()){
			$userRegAcontIds = $rsRegAcount->Field("reg_account_ids");
		}
		$userRegAcontIdsArray = explode(",", $userRegAcontIds);

		if(count($userRegAcontIdsArray)>0 && count($selectedRoles)>0){
			if($logicalOp === "and"){
				foreach ($selectedRoles as $value) {
					if(!in_array($value, $userRegAcontIdsArray)){
						$response = "false";
						break;
					}
				}
			}elseif($logicalOp === "or"){
				foreach ($selectedRoles as $value) {
					if(in_array($value, $userRegAcontIdsArray)){
						$response = "true";
						break;
					}
				}
			}elseif($logicalOp === "equal"){
				if(count($selectedRoles) == count($userRegAcontIdsArray)){
					foreach ($selectedRoles as $value) {
						if(!in_array($value, $userRegAcontIdsArray)){
							$response = "false";
							break;
						}
					}
				}else{
					$response = "false";
				}
			}
		}

	}else{
		$response = "false";
	}

	return $response;
}

function ManualContentList_onRender() {
	global $session,$event;

	INCLUDE_ONCE INC_PATH."user.functionality.class.php";	
	INCLUDE_ONCE APP_PATH."templates/NEModules/ManualContentList/ManualContentList_sql.php";

	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {
		$prop_arr = unserialize(base64_decode(WebApp::findNemProp($session->Vars["idstemp"])));

		$kwConfig = array();

		if(isset($prop_arr["display_kw_atr"]) && count($prop_arr["display_kw_atr"])>0){
			foreach($prop_arr["display_kw_atr"] as $value){
				if($value != "empty"){
					$kwConfig["kw_display"][$value] = $value;
					$kwConfig["kw_display_labels"]["kwLabels_".$value] ="";
					if(isset($prop_arr["subjectTopicsLabels_".$value]) && $prop_arr["subjectTopicsLabels_".$value]!="")
						$kwConfig["kw_display_labels"]["kwLabels_".$value] =$prop_arr["subjectTopicsLabels_".$value] ; 
					
					if(isset($prop_arr["subjectTopicKwShow_".$value]))
						$kwConfig["kw_display_type"]["kw_display_type_".$value] =$prop_arr["subjectTopicKwShow_".$value];
				}
			}
		}

		/*echo 'kwConfig<textarea>';
			print_r($kwConfig);
		echo '</textarea>';*/


		$configurationMode = "";
		if(defined('ACTIVATE_ENTITY_TOOLSET_SPECIALIZATION') && ACTIVATE_ENTITY_TOOLSET_SPECIALIZATION=="Y") {	
			$getCollectorType = "SELECT data_type FROM nems WHERE nem_id = ".$prop_arr["att_nemid"]." ";
			$rs = WebApp::execQuery($getCollectorType);
			if(!$rs->EOF()){
				$configurationMode = $rs->Field("data_type");
			}
		}



		$inc_template = "ManualContentList_default.html";
		if($configurationMode == "userRelated"){
			$userId = $session->Vars["ses_userid"];

			WebApp::addVar("uId",$suserId);		
			$ObjUsr = new UserFullFunctionality($session->Vars["ses_userid"],"","","");		
			$userInfo = $ObjUsr->getUserInfo($session->Vars["ses_userid"]);

			$userCredentials = "";

			if(isset($userInfo[$userId]["FirstName"]) && $userInfo[$userId]["FirstName"] != ""){
				$userCredentials .= $userInfo[$userId]["FirstName"][0];
			}
			if(isset($userInfo[$userId]["SecondName"]) && $userInfo[$userId]["SecondName"] != ""){
				$userCredentials  .= $userInfo[$userId]["SecondName"][0];
			}
			WebApp::addVar("userCredentials",$userCredentials);		

			

			$inc_template = "UserShortcutMenu/UserShortcutMenu_default.html";
		}

		$template_id_sel=$prop_arr["templateID"];
		if ($template_id_sel!="" && $template_id_sel>0) {
			//selektohet template ----------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$template_id_sel."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF())
				$inc_template = $rs->Field("template_box");
		}  

		WebApp::addVar("include_default","<Include SRC=\"{{NEMODULES_PATH}}ManualContentList/".$inc_template."\"/>");


		$confProp=array();


		$groupingByCategory = "no";
		if( (isset($prop_arr["show_category_label"]) && $prop_arr["show_category_label"] == "yes") || 
			(isset($prop_arr["show_category_separator"]) && $prop_arr["show_category_separator"] == "yes") ){
			$groupingByCategory = "yes";
		}



		if(isset($prop_arr["categorization_name"]) && is_array($prop_arr["categorization_name"]) && count($prop_arr["categorization_name"])>0){
			$categorizationGrid = array("AllRecs"=>"", "data"=>array()); $indC=0;

			
			$categorizationItemsGrid = array("AllRecs"=>"", "data"=>array()); $indI=0;
			foreach ($prop_arr["categorization_name"] as $categKey => $categName) {
				$itemCateg = "grp";
				if($groupingByCategory == "yes")
					$itemCateg = $categKey;

				if(isset($prop_arr["ci_to_include"][$categKey]) && is_array($prop_arr["ci_to_include"][$categKey]) && count($prop_arr["ci_to_include"][$categKey])>0){
					foreach ($prop_arr["ci_to_include"][$categKey] as $itemKey => $itemValue){
		
						$itemPropDataGrid = array("AllRecs"=>"", "data"=>array());

						if(isset($prop_arr["configuration_by_type"][$categKey][$itemKey]))
							$configuration_by_type = $prop_arr["configuration_by_type"][$categKey][$itemKey];

						if($configuration_by_type != "userAccessRights"){
							$response = ciConfigurationSettings($categKey,$itemKey, $itemValue, $configuration_by_type, $prop_arr);
						}else{
							$response = "true";
						}

						if($response == "true"){
							$itemPropDataGrid["data"] = getCiPropertyData($categKey,$itemKey, $itemValue, $configuration_by_type, $prop_arr, $kwConfig);
						}



						if(count($itemPropDataGrid["data"])>0)
							$itemPropDataGrid["AllRecs"] = count($itemPropDataGrid["data"]);
						WebApp::addVar("itemPropDataGrid_".$itemCateg."_".$itemValue,$itemPropDataGrid);

						
						$categorizationItemsGrid["data"][$indI++]["itemId"] = $itemValue;
					}
				}


				if($groupingByCategory == "yes"){
					if(count($categorizationItemsGrid["data"])>0)
						$categorizationItemsGrid["AllRecs"] = count($categorizationItemsGrid["data"]);

					WebApp::addVar("categorizationItemsGrid_".$categKey,$categorizationItemsGrid);

					$categorizationItemsGrid = array("AllRecs"=>"", "data"=>array()); $indI=0;
				}
				
				$categorizationGrid["data"][$indC]["categoryId"] = "".$categKey;
				$categorizationGrid["data"][$indC++]["categoryName"] = "$categName";
				
			}

			if($groupingByCategory == "no"){
				if(count($categorizationItemsGrid["data"])>0)
					$categorizationItemsGrid["AllRecs"] = count($categorizationItemsGrid["data"]);
				WebApp::addVar("categorizationItemsGrid_grp",$categorizationItemsGrid);

				$categorizationGrid["data"][0]["categoryId"] = "grp";
				$categorizationGrid["data"][0]["categoryName"] = "";
			}

			if(count($categorizationGrid["data"])>0)
				$categorizationGrid["AllRecs"] = count($categorizationGrid["data"]);
			WebApp::addVar("categorizationGrid",$categorizationGrid);
		}



if($configurationMode == "userRelated"){
		$user_moduleGrid = array("AllRecs"=>"", "data"=>array()); $ind=0;
		if(isset($prop_arr["user_module"]) && is_array($prop_arr["user_module"]) && count($prop_arr["user_module"])>0){
			foreach($prop_arr["user_module"] as $modKey => $moduleLabel){
				if(isset($prop_arr["show_user_module"][$modKey]) &&  $prop_arr["show_user_module"][$modKey] == "yes"){
					$user_moduleGrid["data"][$ind]["modKey"] = "$modKey";
					$user_moduleGrid["data"][$ind]["label"] = "$moduleLabel";
					$user_moduleGrid["data"][$ind]["href"] = "javascript:void(0);";

					$user_moduleGrid["data"][$ind]["moduleIcon"] = "";
					if(isset($prop_arr["user_module_icon"][$modKey]) &&  $prop_arr["user_module_icon"][$modKey] != ""){
						$user_moduleGrid["data"][$ind]["moduleIcon"] = $prop_arr["user_module_icon"][$modKey];
					}

					$user_moduleGrid["data"][$ind]["moduleClass"] = "";
					if($modKey == "logOut"){
						$user_moduleGrid["data"][$ind]["moduleClass"] = "logout-link";
					}

					$ind++;
				}
			}

		}
		if(count($user_moduleGrid["data"])>0)
				$user_moduleGrid["AllRecs"] = count($user_moduleGrid["data"]);

		WebApp::addVar("user_moduleGrid",$user_moduleGrid);
	}

		if(isset($prop_arr["display_atr"]) && count($prop_arr["display_atr"])>0){
			foreach($prop_arr["display_atr"] as $atrKey => $atrVal){
				WebApp::addVar("display_".$atrVal,"yes");
			}
		}

		if(isset($prop_arr["display_manual_atr"]) && count($prop_arr["display_manual_atr"])>0){
			foreach($prop_arr["display_manual_atr"] as $atrKey => $atrVal){
				WebApp::addVar("display_".$atrVal,"yes");
			}
		}
		if(isset($prop_arr["display_link_atr"]) && count($prop_arr["display_link_atr"])>0){
			foreach($prop_arr["display_link_atr"] as $atrKey => $atrVal){
				WebApp::addVar("makeLink_".$atrVal,"yes");
			}
		}


		foreach($prop_arr as $k => $v){
			if(!is_array($v))
				WebApp::addVar($k, $v);
		
		}
	}
}

?>
