<?php

class eLearningUserPlatform 
{
	var $CiAnalytics = array();
	var $totalTime   = 0;
	var $nemID		= "";
	var $objNem		= "";		
	var $idstemp 	= "x";
	var $lang		= "Lng1";
	var $lngId		= "";
	var $thisMode	= "";
	var $uniqueid	= "";
	var $tip			= "2";
	var $userSystemID	= "2";	
	var $cidFlow		= "";	
	var $tableToGetTheRights		= "profil_rights";	
	

	/*****************************************************
	*** CONSTRUCTOR OF THE CLASS ****************
	******************************************************/
	function eLearningUserPlatform ($userID="") {
		global $session,$global_cache_dynamic,$cacheDyn;
		
		$this->lngId 		= str_replace ("Lng","",$session->Vars["lang"]);
		if (isset($session->Vars["ln"]))
		$this->lngId 		= $session->Vars["ln"];
		if ($this->lngId=="")	$this->lngId = 1;
		
	 	$this->uniqueid		= $session->Vars["uni"];
		$this->constuctStructuredDefaultEcc();
		
        if ($session->Vars["thisMode"]=="_new")
        		$this->thisModeCode = 0;
        else	$this->thisModeCode = 1;
        
        $this->initUserProfile();
        $this->isSetGlobalObj = 'yes';
        
		if(defined('ZONE_AUTHORING') && ZONE_AUTHORING>=0) {
		} else {
			$PrmCnrExtr["togetherWith"] =  $session->Vars["level_0"];	
			$PrmCnrExtr["retrnRawData"] = "yes";
			$zonesLinkedDataPrmCnrExtr = SitemapObjManager::getLinkedZones("yes",$PrmCnrExtr);
			$this->avaiableZonesIds = $idsToBeChecked = implode(",",array_keys($zonesLinkedDataPrmCnrExtr));
			define("ZONE_AUTHORING", $this->avaiableZonesIds);
		}        
	}
	function PAGELOAD_PARTIAL ($starts, $message) {
        global $session;
		IF (DEFINED("PAGELOAD_PARTIAL") AND PAGELOAD_PARTIAL !="N") {
			if (PAGELOAD_PARTIAL =="ALL" || (PAGELOAD_PARTIAL =="ADMIN" && $session->Vars["ses_userid"]==1)) {
				$totals = round(WebApp::get_formatted_microtime() - $starts, 2);
				//echo $debugTime = $message.round($totals, 2).":totals";
				if (!isset($this->PAGELOAD_PARTIAL))
					$this->PAGELOAD_PARTIAL = array();
				$ind = count($this->PAGELOAD_PARTIAL);
				$this->PAGELOAD_PARTIAL[$ind]["totals"] = $totals;
				$this->PAGELOAD_PARTIAL[$ind]["idstemp"] = $session->Vars["idstemp"];
				$this->PAGELOAD_PARTIAL[$ind]["message"] = $message;
			}
		}	
	}
	function DISPLAY_PAGELOAD_PARTIAL () {
		if (isset($this->PAGELOAD_PARTIAL) && count($this->PAGELOAD_PARTIAL)>0) {
			echo "<textarea>";
			print_r($this->PAGELOAD_PARTIAL);
			echo "</textarea>";		
		}		
	}
	function initUserProfile(){
        global $session;
		//if (!isset($this->userSessionInfo)) {
			INCLUDE_ONCE INC_PATH."user.functionality.class.php";
			require_once(EASY_PATH."bo_toolsset/SubscriberTool/SubscriberTool.class.php");
			$UsrInfoToReturn = UserFullFunctionality::getUserFullInfo();
			if (isset($UsrInfoToReturn[$session->Vars["ses_userid"]])) {
				$UsrInfo = $UsrInfoToReturn[$session->Vars["ses_userid"]];
				$this->userSessionInfo["UserTitle"] 		= $UsrInfo["UserTitle"];	
				$this->userSessionInfo["UserSalutation"]	= $UsrInfo["UserSalutation"];	
				$this->userSessionInfo["UserFirstName"]		= $UsrInfo["first_name_regis"];	
				$this->userSessionInfo["UserSecondName"]	= $UsrInfo["last_name_regis"];	
				$this->userSessionInfo["UserName"] 			= $UsrInfo["MdUserName"];	
				$this->userSessionInfo["UserEmail"] 		= $UsrInfo["email_regis"];	
			}
		//}
	}
	
/*
				if (isset($event->args["idElC"]) && $event->args["idElC"]!="") {
					$idElC = $event->args["idElC"];
					$session->Vars["idElC"] = $idElC;
					$ootUserObj->initCiReferenceExtended($idElC,"idElC");
				} elseif (isset($_GET["idElC"]) && $_GET["idElC"]!="") {
					$session->Vars["idElC"] = $_GET["idElC"];
					$ootUserObj->initCiReferenceExtended($idElC,"idElC");
				} 
				if (isset($session->Vars["idElC"])) {
					$ootUserObj->initCiReferenceExtended($session->Vars["idElC"],"idElC");
				}
*/	
	
	function inicializationOfState () {	
		
		global $session,$event;
		
		require_once(INCLUDE_AJAX_PATH."/CiManagerFe.class.php");
		$workingCi = new CiManagerFe($session->Vars["contentId"],$session->Vars["lang"]);
		$tmpData = $workingCi->getGeneralProperties();
		
		
		$this->getOverallProgrammeTree();
		
		WebApp::addGlobalVar("maincategorycolor","");
		$this->getLocationPathParentAndClasses();

		if (isset($tmpData["nodeClass"])) {
			WebApp::addGlobalVar("maincategorycolor",$tmpData["nodeClass"]);
		}  		
		
		if (count($this->locationPathClasses)>0) {
		
			WebApp::addGlobalVar("maincategorycolor",implode(" ", $this->locationPathClasses));
		}
		
		if (isset($event->args["idElC"]) && $event->args["idElC"]!="") {
			$session->Vars["idElC"] = $event->args["idElC"];
		}		
		
		$this->getElearningPathNeddedInfo($cidFlow);
	
		if ($this->appRelSt["elearningInfo"]["step"] == "EL") {
			WebApp::addGlobalVar("bodyPageSetup"," full-content-width");
		} else {
			WebApp::addGlobalVar("bodyPageSetup","");
		}

		$brandpalete = 'bg-red';
		if ($session->Vars['level_0'] == '9' || $session->Vars['level_0'] == '10' ) {
			$brandpalete = 'bg-sfaricom';
		}

		WebApp::addGlobalVar("paletevalue", $brandpalete);
	}	
	
	function getLocationPathParentAndClasses () {	
		global $session;
		
		require_once(INC_PHP_AJAX."SitemapManager.class.php");
		$crd_items = array();
		$crd_items[0] = $session->Vars["level_0"];
		$crd_items[1] = $session->Vars["level_1"];
		$crd_items[2] = $session->Vars["level_2"];
		$crd_items[3] = $session->Vars["level_3"];
		$crd_items[4] = $session->Vars["level_4"];
		
		if ($session->Vars["level_4"]>0) 				$hierarchyLevel = 4;
		elseif ($session->Vars["level_3"]>0) 			$hierarchyLevel = 3;
		elseif ($session->Vars["level_2"]>0) 			$hierarchyLevel = 2;
		elseif ($session->Vars["level_1"]>0) 			$hierarchyLevel = 1;
		else											$hierarchyLevel = 0;
		
		$SiteMapObj = new SitemapObjManager();
		
		$locationPathClasses = array();
				
		$parentIds = $SiteMapObj->getParentIds($crd_items[0].",".$crd_items[1].",".$crd_items[2].",".$crd_items[3].",".$crd_items[4]);
		if(defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP=="Y") 
			$nodeBootstrapFields = "coalesce(boostrap_class,'') as boostrap_class, coalesce(boostrap_ico,'') as boostrap_ico,";

		if (count($parentIds)>0) {
		
				$actualLevel 	= array();
				$parentLevel 	= array();
				$dataLevels		= array();
				$lcpath			= array();
				
				$sql = "SELECT IF(nivel_4.description".$session->Vars["lang"]."".$session->Vars["thisMode"]." IS NULL, 
							'', nivel_4.description".$session->Vars["lang"]."".$session->Vars["thisMode"].") as NodeDescription,
								".$nodeBootstrapFields."	
							COALESCE(nivel_4.imageSm_id,      '') as imageSm_id_node, 		
							coalesce(content.imageSm_id,'') as imageSm_id,	
							coalesce(content.imageBg_id,'') as imageBg_id,	

							title".$session->Vars["lang"]." as title,
							
							nivel_4.id_zeroNivel,nivel_4.id_firstNivel,nivel_4.id_secondNivel,nivel_4.id_thirdNivel,nivel_4.id_fourthNivel

						  FROM nivel_4
						  JOIN content 
							ON nivel_4.id_zeroNivel		= content.id_zeroNivel
						   AND nivel_4.id_firstNivel	= content.id_firstNivel
						   AND nivel_4.id_secondNivel	= content.id_secondNivel
						   AND nivel_4.id_thirdNivel	= content.id_thirdNivel
						   AND nivel_4.id_fourthNivel	= content.id_fourthNivel

						 WHERE row(nivel_4.id_zeroNivel,nivel_4.id_firstNivel,nivel_4.id_secondNivel,nivel_4.id_thirdNivel,nivel_4.id_fourthNivel) 
							in (row(".implode("),row(",$parentIds)."))
						   AND nivel_4.state".$session->Vars["lang"]." != 7 
						   AND  orderContent = '0' 
						ORDER BY nivel_4.id_zeroNivel,nivel_4.id_firstNivel,nivel_4.id_secondNivel,nivel_4.id_thirdNivel,nivel_4.id_fourthNivel";
						
				$rsData = WebApp::execQuery($sql);
				while (!$rsData->EOF()) {
					
					
					$id0 = $rsData->Field("id_zeroNivel");	
					$id1 = $rsData->Field("id_firstNivel");	
					$id2 = $rsData->Field("id_secondNivel");	
					$id3 = $rsData->Field("id_thirdNivel");	
					$id4 = $rsData->Field("id_fourthNivel");	
					
					$linkCrd = $id0.",".$id1.",".$id2.",".$id3.",".$id4;	
					$linkhref  = "javascript:GoTo('thisPage?event=none.ch_state(k=".$linkCrd.")')";
					
					$nodeClass 	 ="";
					if(defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP=="Y") {
						$nodeClass 	 = $rsData->Field("boostrap_class");
						$nodeIco	 = $rsData->Field("boostrap_ico");
						
						
						
					}
									
					$nodeImageId 	= TRIM($rsData->Field("imageSm_id_node"));	
					$imageSm_id 		= TRIM($rsData->Field("imageSm_id"));	
					$imageBg_id 		= TRIM($rsData->Field("imageBg_id"));	
					

					if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") {
						//thir griden
						if ($nodeImageId>0)
								CiManagerFe::get_SL_CACHE_INDEX($nodeImageId);	
						if ($imageSm_id>0)
								CiManagerFe::get_SL_CACHE_INDEX($imageSm_id);	
						if ($imageBg_id>0)
								CiManagerFe::get_SL_CACHE_INDEX($imageBg_id);	
					}	


					$NodeDescription = TRIM($rsData->Field("NodeDescription"));	
					$CiTitle		 = TRIM($rsData->Field("title"));
					
					if ($id4>0)			$keyL = 4;
					elseif ($id3>0)		$keyL = 3;
					elseif ($id2>0)		$keyL = 2;
					elseif ($id1>0) 	$keyL = 1;
					else 				$keyL = 0;
					
					
					$locationPathClasses[$keyL] = $nodeClass;
					
					
					$tmp = array();
					$tmp["linkcrd"] 		= $linkCrd;
					$tmp["linkhref"] 		= $linkhref;
					$tmp["nodeClass"] 		= $nodeClass;
					$tmp["nodeImageId"] 	= $nodeImageId;
					$tmp["imageSm_id"] 		= $imageSm_id;
					$tmp["imageBg_id"]		= $imageBg_id;
					
					$tmp["NodeDescription"]		= $NodeDescription;
					$tmp["CiTitle"]		= $CiTitle;
					
					$parentKeyL="";
					if (($keyL-1) >0 ) {
						$parentKeyL=$keyL-1;
					}
					
					
					if ($tmp["nodeImageId"]>0) {
					
					} else {
						if (isset($dataLevels[$parentKeyL]["nodeImageId"]) && $dataLevels[$parentKeyL]["nodeImageId"]>0) {
							$tmp["nodeImageId"] = $dataLevels[$parentKeyL]["nodeImageId"];
						}
					}
					
					
					
					
					
					
					$lcpath["data"][0]["level".$keyL."_linkcrd"]  	= $linkCrd;
					$lcpath["data"][0]["level".$keyL."_linkhref"] 	= $linkhref;
					
					$lcpath["data"][0]["level".$keyL."_nodeClass"] 		= $nodeClass;
					$lcpath["data"][0]["level".$keyL."_nodeIco"] 		= $nodeIco;
					$lcpath["data"][0]["level".$keyL."_nodeImageId"]		= $nodeImageId;
					$lcpath["data"][0]["level".$keyL."_imageSm_id"]		= $imageSm_id;
					$lcpath["data"][0]["level".$keyL."_imageBg_id"]		= $imageBg_id;
					$lcpath["data"][0]["level".$keyL."_NodeDescription"]	= $NodeDescription;
					$lcpath["data"][0]["level".$keyL."_CiTitle"]	= $title;
					

					
					$lcpath["data"][0][$keyL."_exist"]	= "yes";
					if ($hierarchyLevel==$keyL) {
						$actualLevel["data"][0]  = $tmp;
					}
					
					if (($hierarchyLevel-1) >0 && ($hierarchyLevel-1) == $keyL ) {
						$parentLevel["data"][0] = $tmp;
					}
					
					$dataLevels[$keyL] = $tmp;
					$rsData->MoveNext();
				}

		}	
		
		$this->locationPathClasses = $locationPathClasses;
	}
	function getElearningPathNeddedInfo($idReferenceParam="",$nameParam="session")
	{    
    	global $session;

    	if ($idReferenceParam=="")	$idReference 		= $session->Vars["contentId"];
    	else {
    		$idReference 		= $idReferenceParam;
    		if (!isset($this->appRelSt["CiInf"][$idReference])) {
    			$this->getCiReadWriteRights($idReference);
    		}
    	}

		if (!isset($this->appRelSt["CiInf"][$idReference])) 
			$this->getCiReadWriteRights($idReference);
    	
    	if (isset($session->Vars["idElC"])) 
			$this->getCiReadWriteRights($session->Vars["idElC"]);

		$crd_items 		 = $this->appRelSt["coord"][$idReference];
		$idReferenceType = $this->appRelSt["CiInf"][$idReference]["ci_type"];
        $hierarchy_level = $this->appRelSt["hierarchy_level"][$idReference];		
		
		require_once(INC_PHP_AJAX."SitemapManager.class.php");
		$SiteMapObj = new SitemapObjManager();

		$parentIds = $SiteMapObj->getParentIds($crd_items[0].",".$crd_items[1].",".$crd_items[2].",".$crd_items[3].",".$crd_items[4]);

		$this->appRelSt["parentIds"][$idReference] = $parentIds;

		if (count($parentIds)>0) {
		
				$actualLevel 	= array();
				$parentLevel 	= array();
				$dataLevels		= array();
				$lcpath			= array();
				
				$sql = "SELECT IF(nivel_4.description".$session->Vars["lang"]."".$session->Vars["thisMode"]." IS NULL, 
							'', nivel_4.description".$session->Vars["lang"]."".$session->Vars["thisMode"].") as NodeDescription,
								".$nodeBootstrapFields."	
							COALESCE(nivel_4.imageSm_id,      '') as imageSm_id_node, 		
							coalesce(content.imageSm_id,'') as imageSm_id,	
							coalesce(content.imageBg_id,'') as imageBg_id,	
							
							content_id,ci_type,

							title".$session->Vars["lang"]." as title,
							
							nivel_4.id_zeroNivel,nivel_4.id_firstNivel,nivel_4.id_secondNivel,nivel_4.id_thirdNivel,nivel_4.id_fourthNivel

						  FROM nivel_4
						  JOIN content 
							ON nivel_4.id_zeroNivel		= content.id_zeroNivel
						   AND nivel_4.id_firstNivel	= content.id_firstNivel
						   AND nivel_4.id_secondNivel	= content.id_secondNivel
						   AND nivel_4.id_thirdNivel	= content.id_thirdNivel
						   AND nivel_4.id_fourthNivel	= content.id_fourthNivel

						 WHERE row(nivel_4.id_zeroNivel,nivel_4.id_firstNivel,nivel_4.id_secondNivel,nivel_4.id_thirdNivel,nivel_4.id_fourthNivel) 
							in (row(".implode("),row(",$parentIds)."))
						   AND nivel_4.state".$session->Vars["lang"]." != 7
						   AND  orderContent = '0' 
						   
						   GROUP BY nivel_4.id_zeroNivel,nivel_4.id_firstNivel,nivel_4.id_secondNivel,nivel_4.id_thirdNivel,nivel_4.id_fourthNivel
						ORDER BY nivel_4.id_zeroNivel,nivel_4.id_firstNivel,nivel_4.id_secondNivel,nivel_4.id_thirdNivel,nivel_4.id_fourthNivel";
						
				$rsData = WebApp::execQuery($sql);
				while (!$rsData->EOF()) {
					
					
					$content_id = $rsData->Field("content_id");	
					$id0 = $rsData->Field("id_zeroNivel");	
					$id0 = $rsData->Field("id_zeroNivel");	
					$id1 = $rsData->Field("id_firstNivel");	
					$id2 = $rsData->Field("id_secondNivel");	
					$id3 = $rsData->Field("id_thirdNivel");	
					$id4 = $rsData->Field("id_fourthNivel");	
					$ci_type = $rsData->Field("ci_type");	
					
					$linkCrd = $id0.",".$id1.",".$id2.",".$id3.",".$id4;	
					$linkhref  = "javascript:GoTo('thisPage?event=none.ch_state(k=".$linkCrd.")')";
					
					if(defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP=="Y") {
						$nodeClass 	 = $rsData->Field("boostrap_class");
						$nodeIco	 = $rsData->Field("boostrap_ico");
					}
									
					$nodeImageId 	= TRIM($rsData->Field("imageSm_id_node"));	
					$imageSm_id 		= TRIM($rsData->Field("imageSm_id"));	
					$imageBg_id 		= TRIM($rsData->Field("imageBg_id"));	
					

					if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") {
						//thir griden
						if ($nodeImageId>0)
								CiManagerFe::get_SL_CACHE_INDEX($nodeImageId);	
						if ($imageSm_id>0)
								CiManagerFe::get_SL_CACHE_INDEX($imageSm_id);	
						if ($imageBg_id>0)
								CiManagerFe::get_SL_CACHE_INDEX($imageBg_id);	
					}	


					$NodeDescription = TRIM($rsData->Field("NodeDescription"));	
					$CiTitle		 = TRIM($rsData->Field("title"));
					
					if ($id4>0)			$keyL = 4;
					elseif ($id3>0)		$keyL = 3;
					elseif ($id2>0)		$keyL = 2;
					elseif ($id1>0) 	$keyL = 1;
					else 				$keyL = 0;
					
				//	if ($this->appRelSt["coord"][$idReference][0]!=$session->Vars["level_0"] ) {
					
						$this->appRelSt["parentPath"][$idReference][$keyL]["id"]   = $content_id;
						$this->appRelSt["parentPath"][$idReference][$keyL]["type"] = $ci_type;
						$this->appRelSt["parentPathType"][$idReference][$keyL] = $ci_type;
				//	}
					
					
					//$this->appRelSt[$id]["paramCase"] 	= $nameParam;
					//$this->appRelSt[$id]["ci_type"] 		= $ci_type;					
					
					
					$tmp = array();
					$tmp["linkcrd"] 		= $linkCrd;
					$tmp["linkhref"] 		= $linkhref;
					$tmp["nodeClass"] 		= $nodeClass;
					$tmp["nodeImageId"] 	= $nodeImageId;
					$tmp["imageSm_id"] 		= $imageSm_id;
					$tmp["imageBg_id"]		= $imageBg_id;
					
					$tmp["NodeDescription"]		= $NodeDescription;
					$tmp["CiTitle"]		= $CiTitle;
					
					$this->appRelSt["nodeInfo"][$content_id] = $tmp;
					$rsData->MoveNext();
				}

		}
		$this->appRelSt["elearningInfo"]["step"] = "";
		

		
		
		$idProgram	= "";
		$idModule	= "";
		$idLecture	= "";	

		
		if ($this->appRelSt["coord"][$idReference][0]!=$session->Vars["level_0"] ) {

			/*echo "$idReference:idReference<pre>";
			print_r($this->POTS["BORRFROM"]);
			echo "</pre>";	*/	
				//ECOfEl	PROfEC	
				if (isset($this->POTS["BORRFROM"][$session->Vars["level_0"]][$idReference]) 
					&& $this->POTS["BORRFROM"][$session->Vars["level_0"]][$idReference]>0
					) {	
						
					$this->appRelSt["elearningInfo"]["step"] = $this->POTS["CI_TYPE"][$idReference];
					
					$idBorrowing	= $this->POTS["BORRFROM"][$session->Vars["level_0"]][$idReference];
					
					
					if ($this->appRelSt["elearningInfo"]["step"]=="EL")	{
						$idLecture	= $idBorrowing;
							
						if (isset($this->POTS["ECOfEl"][$idLecture])) {
						
							$idModule	= $this->POTS["ECOfEl"][$idLecture]; 
							
							if (isset($this->POTS["PROfEC"][$idModule])) {
								$idProgram = $this->POTS["PROfEC"][$idModule]; 
							}							
						}
							
					} elseif ($this->appRelSt["elearningInfo"]["step"]=="EC") {	
						
						$idModule	= $idBorrowing; 
						
						if (isset($this->POTS["PROfEC"][$idModule])) {
							$idProgram = $this->POTS["PROfEC"][$idModule]; 
						}							
					} elseif ($this->appRelSt["elearningInfo"]["step"]=="PR") {	
						$idProgram	= $idBorrowing;	
					}
					
	

					
				} else {					
					
					
					
					$lev4 = $this->appRelSt["coord"][$idReference][4];
					$lev3 = $this->appRelSt["coord"][$idReference][3];
					$lev2 = $this->appRelSt["coord"][$idReference][2];
					$lev1 = $this->appRelSt["coord"][$idReference][1];
					$lev0 = $this->appRelSt["coord"][$idReference][0];

		
					//inside Lecture items from repository
				
					if ($lev4>0)			$keyL = 3;
					elseif ($lev3>0)		$keyL = 2;
					elseif ($lev2>0)		$keyL = 4;
					elseif ($lev1>0) 		$keyL = 0;
					else 					$keyL = 0;	
					
					if ($keyL==0) 	  $lectureKeyToControl = $lev0."_0_0_0_0";
					elseif ($keyL==1) $lectureKeyToControl = $lev0."_".$lev1."_0_0_0";
					elseif ($keyL==2) $lectureKeyToControl = $lev0."_".$lev1."_".$lev2."_0_0";
					elseif ($keyL==3) $lectureKeyToControl = $lev0."_".$lev1."_".$lev2."_".$lev3."_0";
					elseif ($keyL==4) $lectureKeyToControl = $lev0."_".$lev1."_".$lev2."_".$lev3."_".$lev4."";

					
					$idLecture = $idReference;	
					if (isset($this->POTS["EL"][$lectureKeyToControl])) {
						
						$itemToCheckC = $this->POTS["EL"][$lectureKeyToControl];
						
						if (isset($this->POTS["BORRFROM"][$session->Vars["level_0"]][$itemToCheckC]) 
								&& $this->POTS["BORRFROM"][$session->Vars["level_0"]][$itemToCheckC]>0) {
								
								$idLecture = $this->POTS["BORRFROM"][$session->Vars["level_0"]][$itemToCheckC];
								$this->appRelSt["elearningInfo"]["step"] = $this->POTS["CI_TYPE"][$idLecture];

								if (isset($this->POTS["ECOfEl"][$idLecture])) {

									$idModule	= $this->POTS["ECOfEl"][$idLecture]; 

									if (isset($this->POTS["PROfEC"][$idModule])) {
										$idProgram = $this->POTS["PROfEC"][$idModule]; 
									}							
								}

								
								
								
								
								
						}						
					}
					
					
					
					
					
					
					
/*
						if (isset($this->POTS["EL"][$lectureKeyToControl])) {
							
							$itemToCheckC = $this->POTS["EL"][$lectureKeyToControl];
							 
							if (isset($this->POTS["BORRFROM"][$session->Vars["level_0"]][$itemToCheckC]) 
									&& $this->POTS["BORRFROM"][$session->Vars["level_0"]][$itemToCheckC]>0) {
							
							
								//$itemBorrowedId = $this->POTS["EL"][$itemToCheckC];
								
								//if (isset($this->POTS["eltypesB"][$itemToCheck]) && $this->POTS["eltypesB"][$itemToCheck]["borrowed_id"]>0) {



								//	$itemBorrowedId = $this->POTS["eltypesB"][$itemToCheck]["borrowed_id"];
									$itemId = $this->POTS["BORRFROM"][$session->Vars["level_0"]][$itemToCheckC];

									if (isset($this->POTS["EL_coord"][$itemId])) {
										$nodeToDisplayedItem = implode(",",explode("_",$this->POTS["EL_coord"][$itemId]));
									//	print_r($nodeToDisplayedItem);
									}	
								//}
						
							}
						
						
						
						
							echo "<pre>";
							print_r($lectureKeyToControl.":lectureKeyToControl\n");
							print_r($itemToCheckC.":itemToCheckC\n");
							print_r($this->POTS["BORRFROM"][$session->Vars["level_0"]][$itemToCheckC],":itemToCheckC\n");
							print_r($itemBorrowedId.":itemBorrowedId\n");
							print_r($this->POTS["EL_coord"][$itemBorrowedId]."-----\n");
							print_r($this->POTS["EL_coord"]);

							//print_r($itemToCheck.":itemToCheck\n");
							print_r($nodeToDisplayedItem.":nodeToDisplayedItem\n");
							print_r($itemId.":itemId\n".$itemBorrowedId.":itemBorrowedId");
							print_r($this->POTS["eltypesB"][$itemId]);
							echo "</pre>";	
							
								
							
							
						
						}
						
*/					
					
					
					
					
					

					/*echo "<textarea>";
					print_r($idReference.": idReference\n");
					print_r($keyL.": keyL\n");
					print_r($lectureKeyToControl.": lectureKeyToControl\n");
					print_r($idLecture.": idLecture\n");
					print_r($this->appRelSt["coord"][$idReference]);
					echo "</textarea>";	*/
			}
					
		
					
		
		} else {
		
/*
					} elseif ($lev0!=$session->Vars["level_0"]) { 
					
						if (isset($this->POTS["EL"][$lectureKeyToControl])) {
							
							$itemToCheckC = $this->POTS["EL"][$lectureKeyToControl];
							 
							if (isset($this->POTS["BORRFROM"][$session->Vars["level_0"]][$itemToCheckC]) 
									&& $this->POTS["BORRFROM"][$session->Vars["level_0"]][$itemToCheckC]>0) {
							
							
								//$itemBorrowedId = $this->POTS["EL"][$itemToCheckC];
								
								//if (isset($this->POTS["eltypesB"][$itemToCheck]) && $this->POTS["eltypesB"][$itemToCheck]["borrowed_id"]>0) {



								//	$itemBorrowedId = $this->POTS["eltypesB"][$itemToCheck]["borrowed_id"];
									$itemId = $this->POTS["BORRFROM"][$session->Vars["level_0"]][$itemToCheckC];

									if (isset($this->POTS["EL_coord"][$itemId])) {
										$nodeToDisplayedItem = implode(",",explode("_",$this->POTS["EL_coord"][$itemId]));
									//	print_r($nodeToDisplayedItem);
									}	
								//}
						
							}
						
*/

				if (isset($this->POTS["BORRFROM"][$session->Vars["level_0"]][$idReference]) 
					&& $this->POTS["BORRFROM"][$session->Vars["level_0"]][$idReference]>0
					) {	
						
					$this->appRelSt["elearningInfo"]["step"] = $this->POTS["CI_TYPE"][$idReference];
					
					$idItemToShow	= $this->POTS["BORRFROM"][$session->Vars["level_0"]][$idReference];
					
									if ($this->appRelSt["elearningInfo"]["step"]=="EL")	$idLecture	= $idItemToShow;
								elseif ($this->appRelSt["elearningInfo"]["step"]=="EC")	$idModule	= $idItemToShow;
								elseif ($this->appRelSt["elearningInfo"]["step"]=="PR")	$idProgram	= $idItemToShow;					
					
					
				} elseif (isset($this->POTS["BORRFROM"][$session->Vars["level_0"]][$idReference]) 
					&& $this->POTS["BORRFROM"][$session->Vars["level_0"]][$idReference]>0
					) {	
						
					//inside learning items, or tree borrowed	
						
					$this->appRelSt["elearningInfo"]["step"] = $this->POTS["CI_TYPE"][$idReference];
					
					$idItemToShow	= $this->POTS["BORRFROM"][$session->Vars["level_0"]][$idReference];
					
									if ($this->appRelSt["elearningInfo"]["step"]=="EL")	$idLecture	= $idItemToShow;
								elseif ($this->appRelSt["elearningInfo"]["step"]=="EC")	$idModule	= $idItemToShow;
								elseif ($this->appRelSt["elearningInfo"]["step"]=="PR")	$idProgram	= $idItemToShow;					
					
					
				} else {
				
				

						if (in_array("EL",$this->appRelSt["parentPathType"][$idReference])) {
							$this->appRelSt["elearningInfo"]["step"] = "EL";

						} elseif (in_array("EC",$this->appRelSt["parentPathType"][$idReference])) {
							$this->appRelSt["elearningInfo"]["step"] = "EC";

						} elseif (in_array("PR",$this->appRelSt["parentPathType"][$idReference])) {
							$this->appRelSt["elearningInfo"]["step"] = "PR";

						}				

						if ($this->appRelSt["elearningInfo"]["step"]!="" && count($this->appRelSt["parentPath"][$idReference])>0) {
							While (list($level,$dt)=each($this->appRelSt["parentPath"][$idReference])) {

									if ($dt["type"]=="EL")	$idLecture	= $dt["id"];
								elseif ($dt["type"]=="EC")	$idModule	= $dt["id"];
								elseif ($dt["type"]=="PR")	$idProgram	= $dt["id"];

							}
						}
				
				}
		}
		
		$this->appRelSt["elearningInfo"]["idProgram"] 		= $idProgram;
		$this->appRelSt["elearningInfo"]["idModule"] 		= $idModule;
		$this->appRelSt["elearningInfo"]["idLecture"]	 	= $idLecture;

			if ($this->appRelSt["elearningInfo"]["step"]=="EL" && $idLecture!="") 	$session->Vars["idElC"] = $idLecture;
		elseif ($this->appRelSt["elearningInfo"]["step"]=="EC" && $idModule!="") 	$session->Vars["idElC"] = $idModule;
		elseif ($this->appRelSt["elearningInfo"]["step"]=="PR" && $idProgram!="") 	$session->Vars["idElC"] = $idProgram;




	/*		echo "$idReference:idReference<textarea>";
			print_r($session->Vars);
			print_r($this->appRelSt);
			print_r($this->POTS);
			echo "</textarea>";	*/	





		return;

	} 
	
    function initCiReferenceExtended($ids,$nameParam="")    {
		return;
		global $session;
		
		$sql_con = "SELECT distinct content.content_id, ci_type, group_concat(`read_write`) as rights,
						   content.id_zeroNivel, content.id_firstNivel,content.id_secondNivel,content.id_thirdNivel,content.id_fourthNivel
					  FROM content
					JOIN profil_rights ON (       content.id_zeroNivel   = profil_rights.id_zeroNivel
											AND content.id_firstNivel  = profil_rights.id_firstNivel
											AND content.id_secondNivel = profil_rights.id_secondNivel
											AND content.id_thirdNivel  = profil_rights.id_thirdNivel
											AND content.id_fourthNivel = profil_rights.id_fourthNivel
											AND profil_rights.profil_id in (".$session->Vars["tip"].")
					) 
					WHERE content.content_id   = '".$ids."'";

		$rs_con = WebApp::execQuery($sql_con);
		//typeLectureParents
		IF (!$rs_con->EOF()) {

			$id 								= $rs_con->Field("content_id");
			$ci_type							= $rs_con->Field("ci_type");
			
			if (in_array($ci_type, $this->pathLectureInfo["typeLectureParents"]) 
				|| in_array($ci_type, $this->pathLectureInfo["lectureAllowedTypes"]) 
				|| in_array($ci_type, $this->pathLectureInfo["pathLectureNavRelated"])) {

				$this->paramsIsRelatedToLecture = "yes";
				$this->paramsDynamicCiId 		= $ids;
			//	if ($ci_type=="EL"  || $ci_type=="EC" || $ci_type=="PR")// || $ci_type=="EC" || $ci_type=="PR"
			//	$this->controlOwnerShipToLecture($this->paramsDynamicCiId);				
				$this->tableToGetTheRights		= "profil_rights";

				$this->appRelSt[$id]["paramCase"] 	= $nameParam;
				$this->appRelSt[$id]["ci_type"] 		= $ci_type;

				$this->appRelSt[$id]["coord"][0] = $rs_con->Field("id_zeroNivel");
				$this->appRelSt[$id]["coord"][1] = $rs_con->Field("id_firstNivel");
				$this->appRelSt[$id]["coord"][2] = $rs_con->Field("id_secondNivel");
				$this->appRelSt[$id]["coord"][3] = $rs_con->Field("id_thirdNivel");
				$this->appRelSt[$id]["coord"][4] = $rs_con->Field("id_fourthNivel");

				if ($this->appRelSt[$id]["coord"][4] > 0)		$this->appRelSt[$id]["hierarchy_level"] = 4;
				elseif ($this->appRelSt[$id]["coord"][3] > 0)	$this->appRelSt[$id]["hierarchy_level"] = 3;
				elseif ($this->appRelSt[$id]["coord"][2] > 0)	$this->appRelSt[$id]["hierarchy_level"] = 2;
				elseif ($this->appRelSt[$id]["coord"][1] > 0)	$this->appRelSt[$id]["hierarchy_level"] = 1;
				else   												$this->appRelSt[$id]["hierarchy_level"] = 0;
				//$this->initCiReference($id);
			}
		} 
		if (!isset($this->paramsIsRelatedToLecture)) {
			$sql_con = "SELECT distinct content.content_id, ci_type, group_concat(`read_write`) as rights,
							   content.id_zeroNivel, content.id_firstNivel,content.id_secondNivel,content.id_thirdNivel,content.id_fourthNivel
						  FROM content
						   JOIN profil_rights_ci ON (content.content_id = profil_rights_ci.content_id AND profil_rights_ci.profil_id in (".$session->Vars["tip"]."))
						WHERE content.content_id   = '".$ids."'";
			// A DO KONTROLLOHET E DREJTA MBI CONTENT
			$sql_con = "SELECT distinct content.content_id, ci_type, 
							   content.id_zeroNivel, content.id_firstNivel,content.id_secondNivel,content.id_thirdNivel,content.id_fourthNivel
						  FROM content
						WHERE content.content_id   = '".$ids."'";
			$rs_con = WebApp::execQuery($sql_con);
			IF (!$rs_con->EOF()) {

					$id 								= $rs_con->Field("content_id");
					$ci_type							= $rs_con->Field("ci_type");

					$this->paramsIsRelatedToLecture = "yes";
					$this->paramsDynamicCiId 		= $ids;
					$this->tableToGetTheRights		= "profil_rights_ci";
					
					$this->appRelSt[$id]["paramCase"] 	= $nameParam;
					$this->appRelSt[$id]["ci_type"] 		= $ci_type;

					$this->appRelSt[$id]["coord"][0] = $rs_con->Field("id_zeroNivel");
					$this->appRelSt[$id]["coord"][1] = $rs_con->Field("id_firstNivel");
					$this->appRelSt[$id]["coord"][2] = $rs_con->Field("id_secondNivel");
					$this->appRelSt[$id]["coord"][3] = $rs_con->Field("id_thirdNivel");
					$this->appRelSt[$id]["coord"][4] = $rs_con->Field("id_fourthNivel");

					if ($this->appRelSt[$id]["coord"][4] > 0)		$this->appRelSt[$id]["hierarchy_level"] = 4;
					elseif ($this->appRelSt[$id]["coord"][3] > 0)	$this->appRelSt[$id]["hierarchy_level"] = 3;
					elseif ($this->appRelSt[$id]["coord"][2] > 0)	$this->appRelSt[$id]["hierarchy_level"] = 2;
					elseif ($this->appRelSt[$id]["coord"][1] > 0)	$this->appRelSt[$id]["hierarchy_level"] = 1;
					else    											$this->appRelSt[$id]["hierarchy_level"] = 0;
				}
		}    
    
    }	

	function getStructuredInformationEcc($contentId="")
	{    
    	$this->getElearningPathNeddedInfo($contentId);
	} 
	function getOverallProgrammeTree () {	
		global $session;
		if (!isset($this->POTS)) {
				$starts	= WebApp::get_formatted_microtime();
				if (isset($this->useridp) && $this->useridp>0) $userId=$this->useridp;
				else										   $userId=$session->Vars["ses_userid"];
				if (isset($this->tipp) && $this->useridp>0) $tipp=$this->tipp;
				else										$tipp=$session->Vars["tip"];
				require_once(INCLUDE_AJAX_PATH."CIExtended/ElearningStructureBase.Class.php");	
				if (isset($this->dataMode))	$dataMode = $this->dataMode;
				else						$dataMode = "";
				$POTS = ElearningBase::getOverallProgrammeModelTree($userId,$tipp,$dataMode);
				$this->POTS = $POTS;
				$this->PAGELOAD_PARTIAL($starts,"getOverallProgrammeTree");
				/*echo "<br><br><br>getOverallProgrammeTree:oot.session.base<br>$userId:$tipp<br><textarea>";
				print_r($this->POTS);
				echo "</textarea>";	*/					
		}
		$this->getPartecipationInfo();
	} 
	function getPartecipationInfo ($refreshData="no") {
		return;
    	/*global $session;
		if (!isset($this->partecipationInfo) || $refreshData=="yes") {
			$starts	= WebApp::get_formatted_microtime();
			if (isset($this->useridp) && $this->useridp>0) $userId=$this->useridp;
			else										   $userId=$session->Vars["ses_userid"];

			if (isset($this->tipp) && $this->useridp>0) $tipp=$this->tipp;
			else										$tipp=$session->Vars["tip"];

			if (isset($this->dataMode))	$dataMode = $this->dataMode;
			else						$dataMode = "";
			
			$comparing_data = eLearningUserPlatformBase::getPartecipationInfo($userId,$tipp,$dataMode);

			$this->partecipationInfo 	= $comparing_data["partecipationInfo"];
			$this->partecipationStr 	= $comparing_data["partecipationStr"];
			$this->getPartecipationInfoInContext();
			
			eLearningUserPlatform::getExamsRelationWithTutorials($this->partecipationStr);
			
			eLearningUserPlatform::getPartecipationInfo($userId,$tipp,$dataMode);
			$this->PAGELOAD_PARTIAL($starts,"getPartecipationInfo");
		}*/
	}	
    function initCiReference($cid="")
    {
        global $session;
        if ($cid == "") $this->cidFlow = $session->Vars["contentId"];
        else			$this->cidFlow = $cid;
        $this->getCiReadWriteRights($this->cidFlow);
        $this->getMainCiOfNode();
    }	
    function getCiReadWriteRights($ids = "")
    {
        global $session;
        $dataToReturn = "";
		$profilRights = "JOIN profil_rights ON (       content.id_zeroNivel   = profil_rights.id_zeroNivel
														AND content.id_firstNivel  = profil_rights.id_firstNivel
														AND content.id_secondNivel = profil_rights.id_secondNivel
														AND content.id_thirdNivel  = profil_rights.id_thirdNivel
														AND content.id_fourthNivel = profil_rights.id_fourthNivel
														AND profil_rights.profil_id in (".$session->Vars["tip"]."))";	
		
		
		if ($this->tableToGetTheRights=="profil_rights_ci") {
			$profilRights = " JOIN profil_rights_ci ON (content.content_id = profil_rights_ci.content_id AND profil_rights_ci.profil_id in (".$session->Vars["tip"]."))";				
		} 

        $sql_con = "SELECT content.content_id, ci_type, group_concat(`read_write`) as rights,
							content.id_zeroNivel, content.id_firstNivel,content.id_secondNivel,content.id_thirdNivel,content.id_fourthNivel,
							titleLng1 as title, n.description".$session->Vars["lang"].$session->Vars["thisMode"]." as nodeName

                      FROM content
											JOIN nivel_4			AS n	ON (    content.id_zeroNivel   = n.id_zeroNivel
																	AND content.id_firstNivel  = n.id_firstNivel
																	AND content.id_secondNivel = n.id_secondNivel
																	AND content.id_thirdNivel  = n.id_thirdNivel
																	AND content.id_fourthNivel = n.id_fourthNivel
																	)                      

                      ".$profilRights."
                    WHERE content.content_id   = '" . $ids . "'
                 GROUP BY content.content_id";

        $rs_con = WebApp::execQuery($sql_con);
        IF (!$rs_con->EOF()) {
            //$dataToReturn["id"] 	   		= $rs_con->Field("content_id");

            $id = $rs_con->Field("content_id");
            $this->ci_type_configuration = $rs_con->Field("ci_type");
            
            
            $this->appRelSt["coord"][$id][0] = $rs_con->Field("id_zeroNivel");
            $this->appRelSt["coord"][$id][1] = $rs_con->Field("id_firstNivel");
            $this->appRelSt["coord"][$id][2] = $rs_con->Field("id_secondNivel");
            $this->appRelSt["coord"][$id][3] = $rs_con->Field("id_thirdNivel");
            $this->appRelSt["coord"][$id][4] = $rs_con->Field("id_fourthNivel");

            if ($this->appRelSt["coord"][$id][4] > 0)				$this->appRelSt["hierarchy_level"][$id] = 4;
            elseif ($this->appRelSt["coord"][$id][3] > 0)			$this->appRelSt["hierarchy_level"][$id] = 3;
            elseif ($this->appRelSt["coord"][$id][2] > 0)			$this->appRelSt["hierarchy_level"][$id] = 2;
            elseif ($this->appRelSt["coord"][$id][1] > 0)			$this->appRelSt["hierarchy_level"][$id] = 1;
            else    												$this->appRelSt["hierarchy_level"][$id] = 0;

            $dataToReturn["ci_type"] = $rs_con->Field("ci_type");

            $rights = explode(",", $rs_con->Field("rights"));
            if (is_array($rights) && in_array("W", $rights))
					$dataToReturn["read_write"] = "W";
            else    $dataToReturn["read_write"] = "R";
            
            if (!isset($this->appRelSt["CiInf"][$id])) {
            $this->appRelSt["CiInf"][$id] = $dataToReturn;
            $this->appRelSt["CiInf"][$id]["ci_type"] 			= $rs_con->Field("ci_type");
            $this->appRelSt["CiInf"][$id]["title"] 				= $rs_con->Field("title");
            $this->appRelSt["CiInf"][$id]["nodeDescription"] 	= $rs_con->Field("nodeName");
            }
        } 
    }
    function getMainCiOfNode()
    {
        global $session;
        $sql_con = "SELECT content_id, ci_type, group_concat(`read_write`) as rights,
						   coalesce(is_node,'') as is_node     
                      FROM content
					 JOIN document_types ON document_types.doctype_name = content.ci_type
                      JOIN profil_rights ON (       content.id_zeroNivel   = profil_rights.id_zeroNivel
												AND content.id_firstNivel  = profil_rights.id_firstNivel
												AND content.id_secondNivel = profil_rights.id_secondNivel
												AND content.id_thirdNivel  = profil_rights.id_thirdNivel
												AND content.id_fourthNivel = profil_rights.id_fourthNivel
												AND profil_rights.profil_id in (".$session->Vars["tip"].")
											)
                    

					WHERE content.id_zeroNivel   = '".$session->Vars["level_0"]."' 
                      AND content.id_firstNivel  = '".$session->Vars["level_1"]."' 
                      AND content.id_secondNivel = '".$session->Vars["level_2"]."' 
                      AND content.id_thirdNivel  = '".$session->Vars["level_3"]."' 
                      AND content.id_fourthNivel = '".$session->Vars["level_4"]."' 
                      AND orderContent   = '0'
                 GROUP BY content_id";

        $rs_con = WebApp::execQuery($sql_con);
        IF (!$rs_con->EOF()) {
            $id = $rs_con->Field("content_id");
			$this->appRelSt["MainNodeCiID"] = $id;
			$this->appRelSt["MainNodeCiType"] = $rs_con->Field("ci_type");
			$this->appRelSt["MainNodeCiIsCollector"] = $rs_con->Field("is_node");
			
			
			$this->appRelSt["MainNodeCi"][$id]["ci_type"] = $rs_con->Field("ci_type");
			$rights = explode(",", $rs_con->Field("rights"));
			if (is_array($rights) && in_array("W", $rights))
					$this->appRelSt["MainNodeCi"][$id]["read_write"] = "W";
			else    $this->appRelSt["MainNodeCi"][$id]["read_write"] = "R";
        }
	}	

	function getProgramExtendedDetails($cid=""){
		if ($cid == "")  $cid = $this->cidFlow;
	}
	function getCourseExtendedDetails($cid=""){
		if ($cid == "")  $cid = $this->cidFlow;
	}
	function getLectureExtendedDetails($cid=""){
	}						
	function getItemDetails($cidToInclude="",$template_id="0",$ci_target="",$my_ci_target="",$newConfigurationCiContainerParams=array()){
		global $session;
		WebApp::addVar("enroll_description", "");	

		$actualFilterStatus	="empty";
		$referenceID		="empty";
		$initialize_from_overrided="no";
		if ($newConfigurationCiContainerParams["allow_override"]=="yes") { //yes
				if (isset($this->ci_type_configuration) && 
					($this->ci_type_configuration=="PR" || $this->ci_type_configuration=="EC" || $this->ci_type_configuration=="EL"  )
				) {
						$actualFilterStatus 		= $this->ci_type_configuration;
						$referenceID				= $this->cidFlow;
						$initialize_from_overrided  = "yes";
				}
				if (isset($session->Vars["idElC"])  && isset($this->appRelSt[$session->Vars["idElC"]]) &&
					(		$this->appRelSt[$session->Vars["idElC"]]["ci_type"]=="PR" 
				  		|| $this->appRelSt[$session->Vars["idElC"]]["ci_type"]=="EC" 
				  		|| $this->appRelSt[$session->Vars["idElC"]]["ci_type"]=="EL"  )
					) {
						$actualFilterStatus 		= $this->appRelSt[$session->Vars["idElC"]]["ci_type"];
						$referenceID  				= $session->Vars["idElC"];
						$initialize_from_overrided 	= "yes";
				}	
				if ($initialize_from_overrided=="no" && isset($this->appRelSt["LECTURE_RELATED"]["EL"])) {
						$actualFilterStatus 	= "EL";
						$referenceID  			= $this->appRelSt["LECTURE_RELATED"]["EL"];
						$initialize_from_overrided 	= "yes";				
				}
				
				if ($newConfigurationCiContainerParams["docType"]==$actualFilterStatus) {
				} else {
					$initialize_from_overrided= "no";
					$referenceID		="empty";
				}
		}
		if ($initialize_from_overrided == "no") {
			if (isset($newConfigurationCiContainerParams["ci_to_include"]) && $newConfigurationCiContainerParams["ci_to_include"]!="") {
				$actualFilterStatus 	= $newConfigurationCiContainerParams["docType"];
				$referenceID  			= $newConfigurationCiContainerParams["ci_to_include"];
			} 
		}		

		$chstateParams = array();
		$chstateExtraParams = array();
		$chstateParams["k"] = "";
		$chstateParams["kc"] = "";

		if ($referenceID!="empty" && $referenceID>0) {
			
			$prop = array();
			if ($actualFilterStatus!="PR" && $actualFilterStatus!="EC" && $actualFilterStatus!="EL") {	
				
				$referenceCiForDetails = $referenceID;

				$workingCi = new CiManagerFe($referenceCiForDetails);
				$workingCi->parseDocumentToDisplayPreviewMode("",$template_id);	
				$chstateParams = array();
				$workingCi->properties_structured["DC"]["ew_link_details"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$this->referenceCiForDetails.")');";
				//$workingCi->properties_structured["DC"]["ew_link_lecure_play"] = $workingCi->properties_structured["DC"]["ew_link_details"];

				if ($workingCi->properties_structured["DC"]["idci_l0"]==$session->Vars["level_0"]) {
					$workingCi->properties_structured["DC"]["ew_link_details"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$referenceCiForDetails.")');";
					$chstateParams["k"] = $referenceCiForDetails;
				}  else {
					$workingCi->properties_structured["DC"]["ew_link_details"] = "javascript:GoTo('thisPage?event=none.ch_state(kc={{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}};k=".$referenceCiForDetails.";idElC=".$this->referenceCiForDetails.")');";
					$chstateParams["k"] 			= $referenceCiForDetails;
					//$kc = $session->Vars["level_0"]
					$chstateParams["kc"] 			= "{{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}}";
					$chstateExtraParams["idElC"] 	= $this->referenceCiForDetails;
				}

				
				$linkDynamicOrCached = $this->createDynamicOrCachedLinked($chstateParams,$chstateExtraParams,$this->lngId);				
				
				
				$workingCi->properties_structured["DC"]["ew_title_link"] = $linkDynamicOrCached;
				$workingCi->properties_structured["DC"]["ew_link_details"] = $linkDynamicOrCached;				

				if (isset($workingCi->properties_structured["DC"]) && count($workingCi->properties_structured["DC"])>0)
					$prop = array_merge($prop,$workingCi->properties_structured["DC"]);

				if (isset($workingCi->properties_structured["EX"]) && count($workingCi->properties_structured["EX"])>0)
					$prop = array_merge($prop,$workingCi->properties_structured["EX"]);	

				$gridDProp["data"][0] = $prop ;
				$gridDProp["data"][0]["idstempCiContainer"] = $this->idstemp;
				$gridDProp["data"][0]["referenceID"] = $referenceCiForDetails;
				$gridDProp["AllRecs"] = count($gridDProp["data"]);

				WebApp::addVar("DetailsOfItemPropGrid_".$referenceCiForDetails, $gridDProp);

				if (isset($workingCi->properties_structured["contentTemplateTI"])  && $workingCi->properties_structured["contentTemplateTI"]!="") {
					$gridD["data"][0]['templateHtml'] = $workingCi->properties_structured["contentTemplateTI"];
					$gridD["AllRecs"] = count($gridD["data"]);	
					WebApp::addVar("cidParam", $this->referenceCiForDetails);		
					WebApp::addVar("DetailsOfItemGrid_".$referenceCiForDetails, $gridD);		
				}	
				WebApp::addVar("ciForDetails",$referenceCiForDetails);
			
			} else {
			
					$prop = array();

					$workingCi = new CiManagerFe($referenceID);
					$workingCi->parseDocumentToDisplayPreviewMode("",$template_id);	

					if (
							$workingCi->properties_structured["DC"]["ci_type"]=="PR"
						||	$workingCi->properties_structured["DC"]["ci_type"]=="EC"
						||	$workingCi->properties_structured["DC"]["ci_type"]=="EL"

							) {

						$tmpB = $this->enrollButtonControll($workingCi->properties_structured["DC"],$my_ci_target);
						$workingCi->properties_structured["DC"]["ew_enroll"]  =  $tmpB["ew_enroll"];

						$cntCiIncl = 1;
						if (isset($this->referenceFromCiIncludeItems["ordered"])) {
							$cntCiIncl = count($this->referenceFromCiIncludeItems["ordered"])+1;
						}

						$this->referenceFromCiIncludeItems["ordered"][$cntCiIncl]["id"] 	= $referenceID;
						$this->referenceFromCiIncludeItems["ordered"][$cntCiIncl]["type"] 	= $workingCi->properties_structured["DC"]["ci_type"];

						$this->referenceFromCiIncludeItems["types"][$workingCi->properties_structured["DC"]["ci_type"]][$referenceID] = $referenceID;
					}
					$chstateParams = array();
					$workingCi->properties_structured["DC"]["ew_link_details"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$referenceID.")');";
					
					$chstateParams["k"] = $referenceID;
					if ($workingCi->properties_structured["DC"]["idci_l0"]==$session->Vars["level_0"]) {
						$chstateParams = array();
						$workingCi->properties_structured["DC"]["ew_link_details"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$referenceID.")');";
						$chstateParams["k"] = $referenceCiForDetails;
					} else {
						if ($ci_target!="") {
							$chstateParams = array();
							$workingCi->properties_structured["DC"]["ew_link_details"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$ci_target.";idElC=".$referenceID.")');";
							$chstateParams["k"] = $ci_target;
							$chstateExtraParams["idElC"] 	= $referenceID;
						} else {
							$chstateParams = array();
							$workingCi->properties_structured["DC"]["ew_link_details"] = "javascript:GoTo('thisPage?event=none.ch_state(kc={{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}};k=".COURSE_DETAILS.";idElC=".$referenceID.")');";
							
							$chstateParams["k"] 			= COURSE_DETAILS;
							$chstateParams["kc"] 			= "{{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}}";
							$chstateExtraParams["idElC"] 	= $this->referenceCiForDetails;							
							
						}
					}

					if (isset($workingCi->properties_structured["DC"]) && count($workingCi->properties_structured["DC"])>0)
						$prop = array_merge($prop,$workingCi->properties_structured["DC"]);

					if (isset($workingCi->properties_structured["EX"]) && count($workingCi->properties_structured["EX"])>0)
						$prop = array_merge($prop,$workingCi->properties_structured["EX"]);	

					$linkDynamicOrCached = $this->createDynamicOrCachedLinked($chstateParams,$chstateExtraParams,$this->lngId);				
					
					$prop["ew_link_details"] 		= $linkDynamicOrCached;
					$prop["ew_link_lecure_play"] 	= $linkDynamicOrCached;
					$prop["ew_title_link"] 			= $linkDynamicOrCached;
					
					$gridDProp["data"][0] = $prop ;
					$gridDProp["AllRecs"] = count($gridDProp["data"]);

					if ($workingCi->properties_structured["DC"]["ci_type"]=="EL")		$this->getLectureExtendedDetails($cid);
					elseif ($workingCi->properties_structured["DC"]["ci_type"]=="EC")	$this->getCourseExtendedDetails($cid);
					elseif ($workingCi->properties_structured["DC"]["ci_type"]=="PR")	$this->getProgramExtendedDetails($cid);

					if (isset($this->appRelSt["LECTURE_RELATED"]["EL"]) && $this->appRelSt["LECTURE_RELATED"]["EL"]>0)
						$propUserProg = $this->controllUserProgress($this->appRelSt["LECTURE_RELATED"]["EL"]);				

					$gridDProp["data"][0]["idstempCiContainer"] = $this->idstemp;
					$gridDProp["data"][0]["referenceID"] = $referenceID;

					WebApp::addVar("DetailsOfItemPropGrid_".$referenceID, $gridDProp);
					if (isset($workingCi->properties_structured["contentTemplateTI"])  && $workingCi->properties_structured["contentTemplateTI"]!="") {
						$gridD["data"][0]['templateHtml'] = $workingCi->properties_structured["contentTemplateTI"];
						$gridD["AllRecs"] = count($gridD["data"]);	
						WebApp::addVar("cidParam", $referenceID);		
						WebApp::addVar("DetailsOfItemGrid_".$referenceID, $gridD);		
					}			
					WebApp::addVar("ciForDetails",$referenceID);
					if ($session->Vars["ses_userid"]!=2) { //$session->Vars["thisMode"]=="" && 
						CiManagerFe::insertUserCiStatistics($referenceID, $session->Vars["ses_userid"],$this->lngId,"viewed"); //viewed_in_search,	viewed_in_list,	viewed
					}					
				} 			
		}
	}
	function getMainDocumentCiToGrid($refToCreateContent=""){
		global $session;
		
		if ($refToCreateContent=="")	$refToCreateContent = $session->Vars["contentId"];

			$workingCi = new CiManagerFe($refToCreateContent,$session->Vars["lang"]);
			$workingCi->getMainDocumentCiToGrid();	
		
			$prop = array();
			if (isset($workingCi->properties_structured["DC"]) && count($workingCi->properties_structured["DC"])>0)
				$prop = array_merge($prop,$workingCi->properties_structured["DC"]);

			if (isset($workingCi->properties_structured["EX"]) && count($workingCi->properties_structured["EX"])>0)
				$prop = array_merge($prop,$workingCi->properties_structured["EX"]);	

			$tmpB = $this->enrollButtonControll($prop,$my_ci_target);


			if ($prop["IsMeine"]==$refToCreateContent) {
				$prop["ew_link_lecure_play"] = "{{APP_URL}}?uni={{uni}}&idElC={{CID}}&mode=play";
			}

			$prop["my_ew_link_details"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$referenceID.")');";
			if ($prop["idci_l0"]==$session->Vars["level_0"])
				$prop["my_ew_link_details"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$referenceID.")');";
			else {
					$prop["my_ew_link_details"] = "javascript:GoTo('thisPage?event=none.ch_state(kc={{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}};k=".MY_COURSE_DETAILS.";idElC=".$referenceID.")');";
			}

			$prop = array_merge($prop,$tmpB);	

			$gridDProp["data"][0] = $prop ;
			$gridDProp["AllRecs"] = count($gridDProp["data"]);				
		
			WebApp::addVar("CiPropGrid".$session->Vars["contentId"], $gridDProp);
	}	
	function controllUserProgress($lectureIDs) {
        global $session;
		$dataToReturn = array();
		if ($session->Vars["typeOfUser"] =="FE") {
			$getRelated = "SELECT item_id, coalesce(lastContentId, '') as lastContentId, 
									coalesce(cme_points_received, '') as cme_points_received,
									progress_state, results_state
							 FROM z_elearning_user_progress 
							WHERE user_id = '".$session->Vars["ses_userid"]."' AND item_id IN (".$lectureIDs.")";
			$rsgetRelated = WebApp::execQuery($getRelated);	
			while (!$rsgetRelated->EOF()) {
				$lecture_id 		 							  = $rsgetRelated->Field("item_id");
				$dataToReturn[$lecture_id]["cme_points_received"] = $rsgetRelated->Field("cme_points_received");
				$dataToReturn[$lecture_id]["progressState"]		  = $rsgetRelated->Field("progress_state");
				$dataToReturn[$lecture_id]["results_state"]		  = $rsgetRelated->Field("results_state");
				$dataToReturn[$lecture_id]["lastContentId"]		  = $rsgetRelated->Field("lastContentId");
				
				if (isset($this->appRelSt["CiInf"][$dataToReturn[$lecture_id]["lastContentId"]]["title"]))
					$dataToReturn[$lecture_id]["lastContentTitle"]		  = $this->appRelSt["CiInf"][$dataToReturn[$lecture_id]["lastContentId"]]["title"];
				if (isset($this->appRelSt["CiInf"][$dataToReturn[$lecture_id]["lastContentId"]]["nodeDescription"]))
					$dataToReturn[$lecture_id]["lastContentNodeDescription"]		  = $this->appRelSt["CiInf"][$dataToReturn[$lecture_id]["lastContentId"]]["nodeDescription"];
			
				$gridDProp["data"][0] = $dataToReturn[$lecture_id];
				$gridDProp["AllRecs"] = count($gridDProp["data"]);	
				WebApp::addVar("DetailsOfUserProgressGrid_".$lecture_id, $gridDProp);	
				
				$rsgetRelated->MoveNext();
			}
		}
		return $dataToReturn;
	}
	function getElearningStructure()
	{
        $pathInsideLectureTypes = array();

        $pathInsideLectureTypes["SL"] = "yes";    //Lecture guide and Prelearning
        $pathInsideLectureTypes["CQ"] = "yes";    //Elearning Quiz Container -or during lecture quiz
        $pathInsideLectureTypes["SC"] = "yes";    //Lecture Presentation (Player)
        $pathInsideLectureTypes["SM"] = "yes";    //Summary  of the lecture
        $pathInsideLectureTypes["ES"] = "yes";    //Final Examination Container
        
        $pathRelatedLevelTypes = array();       
        
        if ($this->thisModeCode=="0") { //a paaprovuar
        	$pathRelatedLevelTypes["RQ"] = "no";    //Additional Knowledge Resources - mund te publikohet
        }
        
        $pathRelatedLevelTypes["DC"] = "no";    //DC (Event Documents Collector) - mund te publikohet
        $pathRelatedLevelTypes["TC"] = "yes";    //TC (Tutorial Collector) - mund te publikohet - shfaqe te pagrupuar
        $pathRelatedLevelTypes["RA"] = "no";    //Additional Knowledge Resources - mund te publikohet
        $pathRelatedLevelTypes["NC"] = "yes";    //News Collector
        $pathRelatedLevelTypes["CT"] = "yes";    //Forum Topic Item
        $pathRelatedLevelTypes["US"] = "no";    //User Satisfaction - Survey
        $pathRelatedLevelTypes["UF"] = "no";    //User Satisfaction - Feedback
        $pathRelatedLevelTypes["UE"] = "no";    //User Experience - Lecture
        $pathRelatedLevelTypes["LR"] = "no";    //Lecture Level Reports
        $pathRelatedLevelTypes["SS"] = "no";    //subscribers
        $pathRelatedLevelTypes["DI"] = "no";    //subscribers
                
        $pathRepository = array();
        $pathRepository["DC"] = "DC";    //DC (Event Documents Collector)
        $pathRepository["RA"] = "RA";    //Additional Knowledge Resources
        $pathRepository["RQ"] = "RQ";    //Quizes & Examninations Repository
        $pathRepository["SC"] = "SC";    //Lecture Presentation (Player)

        $pathLectureSubTypes = array();
        $pathLectureSubTypes["EQ"] = "EQ";    //Question Item
        $pathLectureSubTypes["RI"] = "RI";    //Additional Knowledge Item
        $pathLectureSubTypes["NI"] = "NI";    //News Item 
        $pathLectureSubTypes["FQ"] = "FQ";    //Faq Item 
        $pathLectureSubTypes["SP"] = "SP";    //Presantation Slide

        $this->navElearning["typeLecture"] 				= $pathInsideLectureTypes;
        $this->navElearning["typeRepository"] 			= $pathRepository;
        $this->navElearning["pathRelatedLevelTypes"] 	= $pathRelatedLevelTypes;

        $this->navElearning["mainParents"]["PR"]["EC"] = "yes";
        $this->navElearning["mainParents"]["EC"]["EL"] = "yes";
		$this->navElearning["mainParents"]["EL"] = $pathInsideLectureTypes;
	}

    function getProgressConfigurationVars($prgCnf,$referenceIDsCOF,$distinctsIdsInCollector,$typeOfProgress="usr",$authoringProgressInfo = array())
    {
        global $session;

		if ($typeOfProgress!='usr') {
		} else {
		
			if ($this->typeOfCatalogItems=="modules") {
				if (!isset($this->partecipationInfo)) {
					$this->getPartecipationInfo();
				}
				
				$calculateTimeActivity="no";
				if (isset($prgCnf["usr"]["ovll"]["grp3"]) && $prgCnf["usr"]["ovll"]["grp3"]["enableGroup"]=="yes") {
					$calculateTimeActivity="yes";
				}
				$calculateTimeActivity="yes";				
				
				$prgDtCnf = $this->userOverallProgressProgramLevel($referenceIDsCOF,$distinctsIdsInCollector,$calculateTimeActivity, "yes");
				//MAIN USER OVEROLL PROGRESS
				if (isset($prgCnf["usr"]["ovll"]) && $prgCnf["usr"]["ovll"]["enableBlock"]=="yes") {
					if (
							(isset($prgCnf["usr"]["ovll"]["grp1"]) && $prgCnf["usr"]["ovll"]["grp1"]["enableGroup"]=="yes")
						OR 	(isset($prgCnf["usr"]["ovll"]["grp2"]) && $prgCnf["usr"]["ovll"]["grp2"]["enableGroup"]=="yes")
						OR 	(isset($prgCnf["usr"]["ovll"]["grp3"]) && $prgCnf["usr"]["ovll"]["grp3"]["enableGroup"]=="yes")
						) {

						WebApp::addVar("enable_progress_usr_ovll","yes");
						$usr_ovll_data = array("data" => array(), "AllRecs" => "0");$ind=0;

						if (isset($prgCnf["usr"]["headline"]) && $prgCnf["usr"]["ovll"]["headline"]!="") {
							$usr_ovll_data["data"][0]["display_headline"] 	= "yes";
							$usr_ovll_data["data"][0]["headline"] 			= $prgCnf["usr"]["headline"];

						}
						if (isset($prgCnf["usr"]["enableTooltip"]) && $prgCnf["usr"]["ovll"]["enableTooltip"]=="yes") {
							$usr_ovll_data["data"][0]["enableTooltip"]		= "yes";
						}
						if (isset($prgCnf["usr"]["ovll"]["grp1"]) && $prgCnf["usr"]["ovll"]["grp1"]["enableGroup"]=="yes") {

							$usr_ovll_data["data"][0]["enableBlock1"]		= "yes";

							$usr_ovll_data["data"][0]["headline_1"]		= $prgCnf["usr"]["ovll"]["grp1"]["headline"];
							$usr_ovll_data["data"][0]["title_1"]		= $prgCnf["usr"]["ovll"]["grp1"]["title"];
							
							$usr_ovll_data["data"][0]["InProgress"]		= $prgCnf["usr"]["ovll"]["grp1"]["InProgress"];
							$usr_ovll_data["data"][0]["Remaining"]		= $prgCnf["usr"]["ovll"]["grp1"]["Remaining"];
							$usr_ovll_data["data"][0]["Failed"]			= $prgCnf["usr"]["ovll"]["grp1"]["Failed"];
							$usr_ovll_data["data"][0]["Finished"]		= $prgCnf["usr"]["ovll"]["grp1"]["Finished"];
							$usr_ovll_data["data"][0]["Total"]			= $prgCnf["usr"]["ovll"]["grp1"]["Total"];

							if (isset($prgDtCnf["totalsInfo"]["modules"]["tot"]))
									$usr_ovll_data["data"][0]["Total_nr"]			= "".$prgDtCnf["totalsInfo"]["modules"]["tot"];
							else	$usr_ovll_data["data"][0]["Total_nr"]			= "0";	
							if (isset($prgDtCnf["totalsInfo"]["modules"]["failed"]))
									$usr_ovll_data["data"][0]["Failed_nr"]			= "".$prgDtCnf["totalsInfo"]["modules"]["failed"];
							else	$usr_ovll_data["data"][0]["Failed_nr"]			= "0";	
							if (isset($prgDtCnf["totalsInfo"]["modules"]["passed"]))
									$usr_ovll_data["data"][0]["Finished_nr"]		= "".$prgDtCnf["totalsInfo"]["modules"]["passed"];
							else	$usr_ovll_data["data"][0]["Finished_nr"]			= "0";	
							if (isset($prgDtCnf["totalsInfo"]["modules"]["progress"]))
									$usr_ovll_data["data"][0]["InProgress_nr"]		= "".$prgDtCnf["totalsInfo"]["modules"]["progress"];
							else	$usr_ovll_data["data"][0]["InProgress_nr"]			= "0";	
							if (isset($prgDtCnf["totalsInfo"]["modules"]["remaining"]))
									$usr_ovll_data["data"][0]["Remaining_nr"]		= "".$prgDtCnf["totalsInfo"]["modules"]["remaining"];
							else	$usr_ovll_data["data"][0]["Remaining_nr"]			= "0";	

							if ($usr_ovll_data["data"][0]["Total_nr"] > 0) {
								$usr_ovll_data["data"][0]["Finished_percent"] = ($usr_ovll_data["data"][0]["Finished_nr"] / $usr_ovll_data["data"][0]["Total_nr"]) * 100;
								$usr_ovll_data["data"][0]["Finished_percent"] = "".round($usr_ovll_data["data"][0]["Finished_percent"]);

								$usr_ovll_data["data"][0]["Failed_percent"] = ($usr_ovll_data["data"][0]["Failed_nr"] / $usr_ovll_data["data"][0]["Total_nr"]) * 100;
								$usr_ovll_data["data"][0]["Failed_percent"] = "".round($usr_ovll_data["data"][0]["Failed_percent"]);

								$usr_ovll_data["data"][0]["InProgress_percent"] = ($usr_ovll_data["data"][0]["InProgress_nr"] / $usr_ovll_data["data"][0]["Total_nr"]) * 100;
								$usr_ovll_data["data"][0]["InProgress_percent"] = "".round($usr_ovll_data["data"][0]["InProgress_percent"]);

								$usr_ovll_data["data"][0]["Remaining_percent"] = ($usr_ovll_data["data"][0]["Remaining_nr"] / $usr_ovll_data["data"][0]["Total_nr"]) * 100;
								$usr_ovll_data["data"][0]["Remaining_percent"] = "".round($usr_ovll_data["data"][0]["Remaining_percent"]);
							}else{
								$usr_ovll_data["data"][0]["Finished_percent"] = "0";
								$usr_ovll_data["data"][0]["Failed_percent"] = "0";
								$usr_ovll_data["data"][0]["InProgress_percent"] = "0";
								$usr_ovll_data["data"][0]["Remaining_percent"] = "0";
							}
							
							if (isset($prgDtCnf["totalsInfo"]["lectures"]["tot"]))
									$usr_ovll_data["data"][0]["lectures_Total_nr"]			= "".$prgDtCnf["totalsInfo"]["lectures"]["tot"];
							else	$usr_ovll_data["data"][0]["lectures_Total_nr"]			= "0";	
							if (isset($prgDtCnf["totalsInfo"]["lectures"]["failed"]))
									$usr_ovll_data["data"][0]["lectures_Failed_nr"]			= "".$prgDtCnf["totalsInfo"]["lectures"]["failed"];
							else	$usr_ovll_data["data"][0]["lectures_Failed_nr"]			= "0";	
							if (isset($prgDtCnf["totalsInfo"]["lectures"]["passed"]))
									$usr_ovll_data["data"][0]["lectures_Finished_nr"]		= "".$prgDtCnf["totalsInfo"]["lectures"]["passed"];
							else	$usr_ovll_data["data"][0]["lectures_Finished_nr"]			= "0";	
							if (isset($prgDtCnf["totalsInfo"]["lectures"]["progress"]))
									$usr_ovll_data["data"][0]["lectures_InProgress_nr"]		= "".$prgDtCnf["totalsInfo"]["lectures"]["progress"];
							else	$usr_ovll_data["data"][0]["lectures_InProgress_nr"]			= "0";	
							if (isset($prgDtCnf["totalsInfo"]["lectures"]["remaining"]))
									$usr_ovll_data["data"][0]["lectures_Remaining_nr"]		= "".$prgDtCnf["totalsInfo"]["lectures"]["remaining"];
							else	$usr_ovll_data["data"][0]["lectures_Remaining_nr"]			= "0";	

							if ($usr_ovll_data["data"][0]["lectures_Total_nr"] > 0) {
								$usr_ovll_data["data"][0]["lectures_Finished_percent"] = ($usr_ovll_data["data"][0]["lectures_Finished_nr"] / $usr_ovll_data["data"][0]["lectures_Total_nr"]) * 100;
								$usr_ovll_data["data"][0]["lectures_Finished_percent"] = "".round($usr_ovll_data["data"][0]["lectures_Finished_percent"]);

								$usr_ovll_data["data"][0]["lectures_Failed_percent"] = ($usr_ovll_data["data"][0]["lectures_Failed_nr"] / $usr_ovll_data["data"][0]["lectures_Total_nr"]) * 100;
								$usr_ovll_data["data"][0]["lectures_Failed_percent"] = "".round($usr_ovll_data["data"][0]["lectures_Failed_percent"]);

								$usr_ovll_data["data"][0]["lectures_InProgress_percent"] = ($usr_ovll_data["data"][0]["lectures_InProgress_nr"] / $usr_ovll_data["data"][0]["lectures_Total_nr"]) * 100;
								$usr_ovll_data["data"][0]["lectures_InProgress_percent"] = "".round($usr_ovll_data["data"][0]["lectures_InProgress_percent"]);

								$usr_ovll_data["data"][0]["lectures_Remaining_percent"] = ($usr_ovll_data["data"][0]["lectures_Remaining_nr"] / $usr_ovll_data["data"][0]["lectures_Total_nr"]) * 100;
								$usr_ovll_data["data"][0]["lectures_Remaining_percent"] = "".round($usr_ovll_data["data"][0]["lectures_Remaining_percent"]);
							}else{
								$usr_ovll_data["data"][0]["lectures_Finished_percent"] = "0";
								$usr_ovll_data["data"][0]["lectures_Failed_percent"] = "0";
								$usr_ovll_data["data"][0]["lectures_InProgress_percent"] = "0";
								$usr_ovll_data["data"][0]["lectures_Remaining_percent"] = "0";
							}							
							
						}

						if (isset($prgCnf["usr"]["ovll"]["grp2"]) && $prgCnf["usr"]["ovll"]["grp2"]["enableGroup"]=="yes") {

							$usr_ovll_data["data"][0]["enableBlock2"]		= "yes";
							$usr_ovll_data["data"][0]["headline_2"]		= $prgCnf["usr"]["ovll"]["grp2"]["headline"];
							$usr_ovll_data["data"][0]["title_2"]		= $prgCnf["usr"]["ovll"]["grp2"]["title"];
							$usr_ovll_data["data"][0]["CreditsEarned"]		= $prgCnf["usr"]["ovll"]["grp2"]["CreditsEarned"];
							$usr_ovll_data["data"][0]["CreditsRemaining"]	= $prgCnf["usr"]["ovll"]["grp2"]["CreditsRemaining"];
							$usr_ovll_data["data"][0]["CreditsTotal"]		= $prgCnf["usr"]["ovll"]["grp2"]["CreditsTotal"];

							if (isset($prgDtCnf["totalsInfo"]["cme_credits"]["tot"]))
									$usr_ovll_data["data"][0]["CreditsTotal_nr"]		= "".$prgDtCnf["totalsInfo"]["cme_credits"]["tot"];
							else	$usr_ovll_data["data"][0]["CreditsTotal_nr"]			= "0";	
							if (isset($prgDtCnf["totalsInfo"]["cme_credits"]["earned"]))
									$usr_ovll_data["data"][0]["CreditsEarned_nr"]		= "".$prgDtCnf["totalsInfo"]["cme_credits"]["earned"];
							else	$usr_ovll_data["data"][0]["CreditsEarned_nr"]			= "0";	
							if (isset($prgDtCnf["totalsInfo"]["cme_credits"]["remaining"]))
									$usr_ovll_data["data"][0]["CreditsRemaining_nr"]		= "".$prgDtCnf["totalsInfo"]["cme_credits"]["remaining"];
							else	$usr_ovll_data["data"][0]["CreditsRemaining_nr"]			= "0";

							if ($usr_ovll_data["data"][0]["CreditsTotal_nr"] > 0) {
								$usr_ovll_data["data"][0]["CreditsEarned_percent"] = ($usr_ovll_data["data"][0]["CreditsEarned_nr"] / $usr_ovll_data["data"][0]["CreditsTotal_nr"]) * 100;
							}else{
								$usr_ovll_data["data"][0]["CreditsEarned_percent"] = "0";
							}
						}

						if (isset($prgCnf["usr"]["ovll"]["grp3"]) && $prgCnf["usr"]["ovll"]["grp3"]["enableGroup"]=="yes") {

							$usr_ovll_data["data"][0]["enableBlock3"]		= "yes";
							$usr_ovll_data["data"][0]["headline_3"]		= $prgCnf["usr"]["ovll"]["grp3"]["headline"];
							$usr_ovll_data["data"][0]["title_3"]		= $prgCnf["usr"]["ovll"]["grp3"]["title"];

							$usr_ovll_data["data"][0]["Additional"]		= $prgCnf["usr"]["ovll"]["grp3"]["Additional"];
							$usr_ovll_data["data"][0]["Forums"]			= $prgCnf["usr"]["ovll"]["grp3"]["Forums"];
							$usr_ovll_data["data"][0]["Communications"]	= $prgCnf["usr"]["ovll"]["grp3"]["Communications"];
							$usr_ovll_data["data"][0]["SpentTime"]		= $prgCnf["usr"]["ovll"]["grp3"]["SpentTime"];
							$usr_ovll_data["data"][0]["Items"]			= $prgCnf["usr"]["ovll"]["grp3"]["Items"];

							if (isset($prgDtCnf["totalsInfo"]["activity"]["ALL"]))
									$usr_ovll_data["data"][0]["SpentTime_nr"]		= "".$prgDtCnf["totalsInfo"]["activity"]["ALL"];
							else	$usr_ovll_data["data"][0]["SpentTime_nr"]		= "0";	

							if (isset($prgDtCnf["totalsInfo"]["activity"]["RA"]))
									$usr_ovll_data["data"][0]["Additional_nr"]		= "".$prgDtCnf["totalsInfo"]["activity"]["RA"];
							else	$usr_ovll_data["data"][0]["Additional_nr"]		= "0";	

							if (isset($prgDtCnf["totalsInfo"]["activity"]["SC"]))
									$usr_ovll_data["data"][0]["Items_nr"]			= "".$prgDtCnf["totalsInfo"]["activity"]["SC"];
							else	$usr_ovll_data["data"][0]["Items_nr"]			= "0";	

							if (isset($prgDtCnf["totalsInfo"]["activity"]["CT"]))
									$usr_ovll_data["data"][0]["Forums_nr"]			= "".$prgDtCnf["totalsInfo"]["activity"]["CT"];
							else	$usr_ovll_data["data"][0]["Forums_nr"]			= "0";	

							if (isset($prgDtCnf["totalsInfo"]["activity"]["OTHER"]))
									$usr_ovll_data["data"][0]["Communications_nr"]	= "".$prgDtCnf["totalsInfo"]["activity"]["OTHER"];
							else	$usr_ovll_data["data"][0]["Communications_nr"]	= "0";	
						}	
						$usr_ovll_data["AllRecs"] = count($usr_ovll_data["data"]);
						WebApp::addVar("progress_usr_ovll_grid", $usr_ovll_data);  	
					}
				}   
				
				
		/*echo "MAIN USER OVEROLL PROGRESS<textarea>";
		//print_r($referenceIDsCOF);
		//print_r($distinctsIdsInCollector);
		print_r($prgDtCnf);
		print_r($usr_ovll_data);
		echo "</textarea>";		*/				

				//MAIN USER ITEMS PROGRESS
				if (isset($prgCnf["usr"]["itms"]) && $prgCnf["usr"]["itms"]["enableBlock"]=="yes") {

					if (
							(isset($prgCnf["usr"]["itms"]["grp1"]) && $prgCnf["usr"]["itms"]["grp1"]["enableGroup"]=="yes")
						OR 	(isset($prgCnf["usr"]["itms"]["grp2"]) && $prgCnf["usr"]["itms"]["grp2"]["enableGroup"]=="yes")
						OR 	(isset($prgCnf["usr"]["itms"]["grp3"]) && $prgCnf["usr"]["itms"]["grp3"]["enableGroup"]=="yes")
						) {

						$this->configureElearningStatusLabel("modules", $prgCnf["usr"]["itms"]);
						WebApp::addVar("enable_progress_usr_itms","yes");
						$usr_itms_data = array("data" => array(), "AllRecs" => "0");$ind=0;

						if(isset($prgCnf["usr"]["itms"]["text"]) && count($prgCnf["usr"]["itms"]["text"])>0){
							$usr_itms_data["data"][0]["module_status_overall"] 	= $prgCnf["usr"]["itms"]["text"]["stInProgress"];								
						}

						if (isset($prgCnf["usr"]["headline"]) && $prgCnf["usr"]["itms"]["headline"]!="") {
							$usr_itms_data["data"][0]["display_headline"] 	= "yes";
							$usr_itms_data["data"][0]["headline"] 			= $prgCnf["usr"]["headline"];

						}
						if (isset($prgCnf["usr"]["enableTooltip"]) && $prgCnf["usr"]["itms"]["enableTooltip"]=="yes") {
							$usr_itms_data["data"][0]["enableTooltip"]		= "yes";
						}

						if (isset($prgCnf["usr"]["itms"]["grp1"]) && $prgCnf["usr"]["itms"]["grp1"]["enableGroup"]=="yes") {

							$usr_itms_data["data"][0]["enableBlock1"]		= "yes";

							$usr_itms_data["data"][0]["headline_1"]		= $prgCnf["usr"]["itms"]["grp1"]["headline"];
							$usr_itms_data["data"][0]["title_1"]		= $prgCnf["usr"]["itms"]["grp1"]["title"];
							$usr_itms_data["data"][0]["InProgress"]		= $prgCnf["usr"]["itms"]["grp1"]["InProgress"];
							$usr_itms_data["data"][0]["Remaining"]		= $prgCnf["usr"]["itms"]["grp1"]["Remaining"];
							$usr_itms_data["data"][0]["Failed"]			= $prgCnf["usr"]["itms"]["grp1"]["Failed"];
							$usr_itms_data["data"][0]["Finished"]		= $prgCnf["usr"]["itms"]["grp1"]["Finished"];
							$usr_itms_data["data"][0]["Total"]			= $prgCnf["usr"]["itms"]["grp1"]["Total"];
						}

						if (isset($prgCnf["usr"]["itms"]["grp2"]) && $prgCnf["usr"]["itms"]["grp2"]["enableGroup"]=="yes") {

							$usr_itms_data["data"][0]["enableBlock2"]		= "yes";
							$usr_itms_data["data"][0]["headline_2"]		= $prgCnf["usr"]["itms"]["grp2"]["headline"];
							$usr_itms_data["data"][0]["title_2"]		= $prgCnf["usr"]["itms"]["grp2"]["title"];
							$usr_itms_data["data"][0]["CreditsEarned"]		= $prgCnf["usr"]["itms"]["grp2"]["CreditsEarned"];
							$usr_itms_data["data"][0]["CreditsRemaining"]	= $prgCnf["usr"]["itms"]["grp2"]["CreditsRemaining"];
							$usr_itms_data["data"][0]["CreditsTotal"]		= $prgCnf["usr"]["itms"]["grp2"]["CreditsTotal"];
						}			

						if (isset($prgCnf["usr"]["itms"]["grp3"]) && $prgCnf["usr"]["itms"]["grp3"]["enableGroup"]=="yes") {

							$usr_itms_data["data"][0]["enableBlock3"]		= "yes";
							$usr_itms_data["data"][0]["headline_3"]		= $prgCnf["usr"]["itms"]["grp3"]["headline"];
							$usr_itms_data["data"][0]["title_3"]		= $prgCnf["usr"]["itms"]["grp3"]["title"];

							$usr_itms_data["data"][0]["Additional"]		= $prgCnf["usr"]["itms"]["grp3"]["Additional"];
							$usr_itms_data["data"][0]["Forums"]			= $prgCnf["usr"]["itms"]["grp3"]["Forums"];
							$usr_itms_data["data"][0]["Communications"]	= $prgCnf["usr"]["itms"]["grp3"]["Communications"];
							$usr_itms_data["data"][0]["SpentTime"]		= $prgCnf["usr"]["itms"]["grp3"]["SpentTime"];
							$usr_itms_data["data"][0]["Items"]			= $prgCnf["usr"]["itms"]["grp3"]["Items"];
						}	

						if (isset($prgDtCnf["detailsInfo"]) && count($prgDtCnf["detailsInfo"])>0) {
							
							reset($prgDtCnf["detailsInfo"]);
							while (list($moduleId,$prgDtCnfDet)=each($prgDtCnf["detailsInfo"])) {
								$tmp = $usr_itms_data;

								if (isset($prgDtCnfDet["modules"]["class"]))
										$tmp["data"][0]["class_item"]			= "".$prgDtCnfDet["modules"]["class"];
								else	$tmp["data"][0]["class_item"]			= "";		

								if ($usr_itms_data["data"][0]["enableBlock1"] == "yes") {
									if (isset($prgDtCnfDet["modules"]["tot"]))
											$tmp["data"][0]["Total_nr"]			= "".$prgDtCnfDet["modules"]["tot"];
									else	$tmp["data"][0]["Total_nr"]			= "0";	
									if (isset($prgDtCnfDet["modules"]["failed"]))
											$tmp["data"][0]["Failed_nr"]		= "".$prgDtCnfDet["modules"]["failed"];
									else	$tmp["data"][0]["Failed_nr"]		= "0";	
									if (isset($prgDtCnfDet["modules"]["passed"]))
											$tmp["data"][0]["Finished_nr"]		= "".$prgDtCnfDet["modules"]["passed"];
									else	$tmp["data"][0]["Finished_nr"]		= "0";	
									if (isset($prgDtCnfDet["modules"]["progress"]))
											$tmp["data"][0]["InProgress_nr"]	= "".$prgDtCnfDet["modules"]["progress"];
									else	$tmp["data"][0]["InProgress_nr"]	= "0";	
									if (isset($prgDtCnfDet["modules"]["remaining"]))
											$tmp["data"][0]["Remaining_nr"]		= "".$prgDtCnfDet["modules"]["remaining"];
									else	$tmp["data"][0]["Remaining_nr"]		= "0";	

									if ($tmp["data"][0]["Total_nr"] > 0) {
										$tmp["data"][0]["Finished_percent"] = ($tmp["data"][0]["Finished_nr"] / $tmp["data"][0]["Total_nr"]) * 100;
										$tmp["data"][0]["Finished_percent"] = "".round($tmp["data"][0]["Finished_percent"]);

										$tmp["data"][0]["Failed_percent"] = ($tmp["data"][0]["Failed_nr"] / $tmp["data"][0]["Total_nr"]) * 100;
										$tmp["data"][0]["Failed_percent"] = "".round($tmp["data"][0]["Failed_percent"]);

										$tmp["data"][0]["InProgress_percent"] = ($tmp["data"][0]["InProgress_nr"] / $tmp["data"][0]["Total_nr"]) * 100;
										$tmp["data"][0]["InProgress_percent"] = "".round($tmp["data"][0]["InProgress_percent"]);

										$tmp["data"][0]["Remaining_percent"] = ($tmp["data"][0]["Remaining_nr"] / $tmp["data"][0]["Total_nr"]) * 100;
										$tmp["data"][0]["Remaining_percent"] = "".round($tmp["data"][0]["Remaining_percent"]);
									}else{
										$tmp["data"][0]["Finished_percent"] = "0";
										$tmp["data"][0]["Failed_percent"] = "0";
										$tmp["data"][0]["InProgress_percent"] = "0";
										$tmp["data"][0]["Remaining_percent"] = "0";
									}
								}

								if ($usr_itms_data["data"][0]["enableBlock2"] == "yes") {

									if (isset($prgDtCnfDet["cme_credits"]["tot"]))
											$tmp["data"][0]["CreditsTotal_nr"]		= "".$prgDtCnfDet["cme_credits"]["tot"];
									else	$tmp["data"][0]["CreditsTotal_nr"]		= "0";	
									if (isset($prgDtCnfDet["cme_credits"]["earned"]))
											$tmp["data"][0]["CreditsEarned_nr"]		= "".$prgDtCnfDet["cme_credits"]["earned"];
									else	$tmp["data"][0]["CreditsEarned_nr"]		= "0";	
									if (isset($prgDtCnfDet["cme_credits"]["remaining"]))
											$tmp["data"][0]["CreditsRemaining_nr"]	= "".$prgDtCnfDet["cme_credits"]["remaining"];
									else	$tmp["data"][0]["CreditsRemaining_nr"]	= "0";	
									
									if ($tmp["data"][0]["CreditsTotal_nr"] > 0) {
										$tmp["data"][0]["CreditsEarned_percent"] = ($tmp["data"][0]["CreditsEarned_nr"] / $tmp["data"][0]["CreditsTotal_nr"]) * 100;
									}else{
										$tmp["data"][0]["CreditsEarned_percent"] = "0";
									}									
								}
								if ($usr_itms_data["data"][0]["enableBlock3"] == "yes") {

									if (isset($prgDtCnfDet["activity"]["ALL"]))
											$tmp["data"][0]["SpentTime_nr"]		= "".$prgDtCnfDet["activity"]["ALL"];
									else	$tmp["data"][0]["SpentTime_nr"]		= "0";	

									if (isset($prgDtCnfDet["activity"]["RA"]))
											$tmp["data"][0]["Additional_nr"]		= "".$prgDtCnfDet["activity"]["RA"];
									else	$tmp["data"][0]["Additional_nr"]		= "0";	

									if (isset($prgDtCnfDet["activity"]["SC"]))
											$tmp["data"][0]["Items_nr"]			= "".$prgDtCnfDet["activity"]["SC"];
									else	$tmp["data"][0]["Items_nr"]			= "0";	

									if (isset($prgDtCnfDet["activity"]["CT"]))
											$tmp["data"][0]["Forums_nr"]			= "".$prgDtCnfDet["activity"]["CT"];
									else	$tmp["data"][0]["Forums_nr"]			= "0";	

									if (isset($prgDtCnfDet["activity"]["OTHER"]))
											$tmp["data"][0]["Communications_nr"]	= "".$prgDtCnfDet["activity"]["OTHER"];
									else	$tmp["data"][0]["Communications_nr"]	= "0";								

								}
								$tmp["AllRecs"] = count($tmp["data"]);
								WebApp::addVar("progress_usr_itms_grid_$moduleId", $tmp);  
							}
						}
						$usr_itms_data["AllRecs"] = count($usr_itms_data["data"]);
						WebApp::addVar("progress_usr_itms_grid", $usr_itms_data); 
					}
				}        
				//MAIN USER TUTORIALS PROGRESS
				if (isset($prgCnf["usr"]["tut"]) && $prgCnf["usr"]["tut"]["enableBlock"]=="yes") {
						$showUserProgressInTutorial = "yes";
						$typeOfCatalogItems =$this->typeOfCatalogItems;
				}  	
			}

			if ($this->typeOfCatalogItems=="currentWorkingLectures") {
				$referenceIDsCOF = array();
				if (count($distinctsIdsInCollector)>0) {
					while (list($lectureID,$tmp)=each($distinctsIdsInCollector)) {
						if (isset($this->POTS["EL_coord"][$lectureID])) {
							$referenceIDsCOF[$lectureID] = $this->POTS["EL_coord"][$lectureID];
						}
					}
				} 
				//MAIN USER OVEROLL PROGRESS
				$prgDtCnf = $this->userOverallProgressModuleLevel($referenceIDsCOF,$distinctsIdsInCollector,$this->typeOfCatalogItems, "yes");
				if (isset($prgCnf["usr"]["ovll"]) && $prgCnf["usr"]["ovll"]["enableBlock"]=="yes") {

				}
				//MAIN USER ITEMS PROGRESS
				if (isset($prgCnf["usr"]["itms"]) && $prgCnf["usr"]["itms"]["enableBlock"]=="yes") {
					
					WebApp::addVar("enable_progress_usr_itms","yes");					
					$this->configureElearningStatusLabel("lectures", $prgCnf["usr"]["itms"]);
				}	
			}
			if ($this->typeOfCatalogItems=="lectures") {
				//MAIN USER OVEROLL PROGRESS
				$prgDtCnf = $this->userOverallProgressModuleLevel($referenceIDsCOF,$distinctsIdsInCollector);


				/*echo "MAIN USER OVEROLL PROGRESS<textarea>";
				print_r($referenceIDsCOF);
				print_r($distinctsIdsInCollector);
				echo "</textarea>";			
				echo "prgDtCnf<textarea>";
				print_r($prgDtCnf);
				echo "</textarea>";	*/
				
				if (isset($prgCnf["usr"]["ovll"]) && $prgCnf["usr"]["ovll"]["enableBlock"]=="yes") {

					if (
							(isset($prgCnf["usr"]["ovll"]["grp1"]) && $prgCnf["usr"]["ovll"]["grp1"]["enableGroup"]=="yes")
						OR 	(isset($prgCnf["usr"]["ovll"]["grp2"]) && $prgCnf["usr"]["ovll"]["grp2"]["enableGroup"]=="yes")
						OR 	(isset($prgCnf["usr"]["ovll"]["grp3"]) && $prgCnf["usr"]["ovll"]["grp3"]["enableGroup"]=="yes")
						) {

						WebApp::addVar("enable_progress_usr_ovll","yes");
						$usr_ovll_data = array("data" => array(), "AllRecs" => "0");$ind=0;

						if (isset($prgCnf["usr"]["headline"]) && $prgCnf["usr"]["ovll"]["headline"]!="") {
							$usr_ovll_data["data"][0]["display_headline"] 	= "yes";
							$usr_ovll_data["data"][0]["headline"] 			= $prgCnf["usr"]["headline"];

						}
						if (isset($prgCnf["usr"]["enableTooltip"]) && $prgCnf["usr"]["ovll"]["enableTooltip"]=="yes") {
							$usr_ovll_data["data"][0]["enableTooltip"]		= "yes";
						}

						if (isset($prgCnf["usr"]["ovll"]["grp1"]) && $prgCnf["usr"]["ovll"]["grp1"]["enableGroup"]=="yes") {

							$usr_ovll_data["data"][0]["enableBlock1"]		= "yes";
							$usr_ovll_data["data"][0]["headline_1"]		= $prgCnf["usr"]["ovll"]["grp1"]["headline"];
							$usr_ovll_data["data"][0]["title_1"]		= $prgCnf["usr"]["ovll"]["grp1"]["title"];

							$usr_ovll_data["data"][0]["InProgress"]		= $prgCnf["usr"]["ovll"]["grp1"]["InProgress"];
							$usr_ovll_data["data"][0]["Remaining"]		= $prgCnf["usr"]["ovll"]["grp1"]["Remaining"];
							$usr_ovll_data["data"][0]["Failed"]			= $prgCnf["usr"]["ovll"]["grp1"]["Failed"];
							$usr_ovll_data["data"][0]["Finished"]		= $prgCnf["usr"]["ovll"]["grp1"]["Finished"];
							
							$usr_ovll_data["data"][0]["Total"]			= $prgCnf["usr"]["ovll"]["grp1"]["Total"];

							if (isset($prgDtCnf["lectures"]["tot"])) {
								$usr_ovll_data["data"][0]["value_1"]		= "".$prgDtCnf["lectures"]["tot"];
								$usr_ovll_data["data"][0]["Total_nr"]		= "".$prgDtCnf["lectures"]["tot"];
							} else {
								$usr_ovll_data["data"][0]["value_1"]		= "0";
								$usr_ovll_data["data"][0]["Total_nr"]		= "0";
							}

							if (isset($prgDtCnf["lectures"]["failed"])) 
									$usr_ovll_data["data"][0]["Failed_nr"]			= "".$prgDtCnf["lectures"]["failed"];
							else	$usr_ovll_data["data"][0]["Failed_nr"]			= "0";

							if (isset($prgDtCnf["lectures"]["passed"])) 
									$usr_ovll_data["data"][0]["Finished_nr"]		= "".$prgDtCnf["lectures"]["passed"];
							else	$usr_ovll_data["data"][0]["Finished_nr"]		= "0";

							if (isset($prgDtCnf["lectures"]["progress"])) 
									$usr_ovll_data["data"][0]["InProgress_nr"]		= "".$prgDtCnf["lectures"]["progress"];
							else	$usr_ovll_data["data"][0]["InProgress_nr"]		= "0";

							if (isset($prgDtCnf["lectures"]["remaining"])) 
									$usr_ovll_data["data"][0]["Remaining_nr"]		= "".$prgDtCnf["lectures"]["remaining"];
							else	$usr_ovll_data["data"][0]["Remaining_nr"]		= "0";
							
							if ($usr_ovll_data["data"][0]["Total_nr"] > 0) {
								$usr_ovll_data["data"][0]["Finished_percent"] = ($usr_ovll_data["data"][0]["Finished_nr"] / $usr_ovll_data["data"][0]["Total_nr"]) * 100;
								$usr_ovll_data["data"][0]["Finished_percent"] = "".round($usr_ovll_data["data"][0]["Finished_percent"]);

								$usr_ovll_data["data"][0]["Failed_percent"] = ($usr_ovll_data["data"][0]["Failed_nr"] / $usr_ovll_data["data"][0]["Total_nr"]) * 100;
								$usr_ovll_data["data"][0]["Failed_percent"] = "".round($usr_ovll_data["data"][0]["Failed_percent"]);

								$usr_ovll_data["data"][0]["InProgress_percent"] = ($usr_ovll_data["data"][0]["InProgress_nr"] / $usr_ovll_data["data"][0]["Total_nr"]) * 100;
								$usr_ovll_data["data"][0]["InProgress_percent"] = "".round($usr_ovll_data["data"][0]["InProgress_percent"]);

								$usr_ovll_data["data"][0]["Remaining_percent"] = ($usr_ovll_data["data"][0]["Remaining_nr"] / $usr_ovll_data["data"][0]["Total_nr"]) * 100;
								$usr_ovll_data["data"][0]["Remaining_percent"] = "".round($usr_ovll_data["data"][0]["Remaining_percent"]);
							}else{
								$usr_ovll_data["data"][0]["Finished_percent"] = "0";
								$usr_ovll_data["data"][0]["Failed_percent"] = "0";
								$usr_ovll_data["data"][0]["InProgress_percent"] = "0";
								$usr_ovll_data["data"][0]["Remaining_percent"] = "0";
							}							
						}

						if (isset($prgCnf["usr"]["ovll"]["grp2"]) && $prgCnf["usr"]["ovll"]["grp2"]["enableGroup"]=="yes") {

							$usr_ovll_data["data"][0]["enableBlock2"]		= "yes";
							$usr_ovll_data["data"][0]["headline_2"]		= $prgCnf["usr"]["ovll"]["grp2"]["headline"];
							$usr_ovll_data["data"][0]["title_2"]		= $prgCnf["usr"]["ovll"]["grp2"]["title"];
							$usr_ovll_data["data"][0]["CreditsEarned"]		= $prgCnf["usr"]["ovll"]["grp2"]["CreditsEarned"];
							$usr_ovll_data["data"][0]["CreditsRemaining"]	= $prgCnf["usr"]["ovll"]["grp2"]["CreditsRemaining"];
							$usr_ovll_data["data"][0]["CreditsTotal"]		= $prgCnf["usr"]["ovll"]["grp2"]["CreditsTotal"];

							if (isset($prgDtCnf["cme_credits"]["tot"])) {
								$usr_ovll_data["data"][0]["value_2"]		= "".$prgDtCnf["cme_credits"]["tot"];
								$usr_ovll_data["data"][0]["CreditsTotal_nr"]	= "".$prgDtCnf["cme_credits"]["tot"];
							} else {
								$usr_ovll_data["data"][0]["value_2"]		= "0";
								$usr_ovll_data["data"][0]["CreditsTotal_nr"]	= "0";
							}

							if (isset($prgDtCnf["cme_credits"]["earned"])) 
									$usr_ovll_data["data"][0]["CreditsEarned_nr"]		= "".$prgDtCnf["cme_credits"]["earned"];
							else	$usr_ovll_data["data"][0]["CreditsEarned_nr"]		= "0";						

							if (isset($prgDtCnf["cme_credits"]["remaining"])) 
									$usr_ovll_data["data"][0]["CreditsRemaining_nr"]	= "".$prgDtCnf["cme_credits"]["remaining"];
							else	$usr_ovll_data["data"][0]["CreditsRemaining_nr"]	= "0";			
							
							
							
							
							if ($usr_ovll_data["data"][0]["CreditsTotal_nr"] > 0) {
								$usr_ovll_data["data"][0]["CreditsEarned_percent"] = ($usr_ovll_data["data"][0]["CreditsEarned_nr"] / $usr_ovll_data["data"][0]["CreditsTotal_nr"]) * 100;
							}else{
								$usr_ovll_data["data"][0]["CreditsEarned_percent"] = "0";
							}							

						}			

						if (isset($prgCnf["usr"]["ovll"]["grp3"]) && $prgCnf["usr"]["ovll"]["grp3"]["enableGroup"]=="yes") {

							$usr_ovll_data["data"][0]["enableBlock3"]		= "yes";
							$usr_ovll_data["data"][0]["headline_3"]		= $prgCnf["usr"]["ovll"]["grp3"]["headline"];
							$usr_ovll_data["data"][0]["title_3"]		= $prgCnf["usr"]["ovll"]["grp3"]["title"];
							$usr_ovll_data["data"][0]["value_3"] 		= "0";

							$usr_ovll_data["data"][0]["Additional"]		= $prgCnf["usr"]["ovll"]["grp3"]["Additional"];
							$usr_ovll_data["data"][0]["Forums"]			= $prgCnf["usr"]["ovll"]["grp3"]["Forums"];
							$usr_ovll_data["data"][0]["Communications"]	= $prgCnf["usr"]["ovll"]["grp3"]["Communications"];
							$usr_ovll_data["data"][0]["SpentTime"]		= $prgCnf["usr"]["ovll"]["grp3"]["SpentTime"];
							$usr_ovll_data["data"][0]["Items"]			= $prgCnf["usr"]["ovll"]["grp3"]["Items"];

							$usr_ovll_data["data"][0]["Additional_nr"]		= "0";
							$usr_ovll_data["data"][0]["Forums_nr"]			= "0";
							$usr_ovll_data["data"][0]["Communications_nr"]	= "0";
							$usr_ovll_data["data"][0]["SpentTime_nr"]		= "0";
							$usr_ovll_data["data"][0]["Items_nr"]			= "0";						

							if (isset($prgDtCnf["time"])) {
								if (isset($prgDtCnf["time"]["all"])) {
									$usr_ovll_data["data"][0]["value_3"] 			= "".$prgDtCnf["time"]["all"];
									$usr_ovll_data["data"][0]["SpentTime_nr"]		= "".$prgDtCnf["time"]["all"];
								}	
								if (isset($prgDtCnf["time"]["SC"])) 	
									$usr_ovll_data["data"][0]["Items_nr"]		= "".$prgDtCnf["time"]["SC"];
								if (isset($prgDtCnf["time"]["RA"])) 	
									$usr_ovll_data["data"][0]["Additional_nr"]		= "".$prgDtCnf["time"]["RA"];
								if (isset($prgDtCnf["time"]["CT"])) 	
									$usr_ovll_data["data"][0]["Forums_nr"]			= "".$prgDtCnf["time"]["CT"];								
							}
						}	
						$usr_ovll_data["AllRecs"] = count($usr_ovll_data["data"]);
						WebApp::addVar("progress_usr_ovll_grid", $usr_ovll_data);  	
					}
				}   
				//MAIN USER ITEMS PROGRESS
				if (isset($prgCnf["usr"]["itms"]) && $prgCnf["usr"]["itms"]["enableBlock"]=="yes") {
					$this->configureElearningStatusLabel("lectures", $prgCnf["usr"]["itms"]);
					WebApp::addVar("enable_progress_usr_itms","yes");
				}        
			}
		}
		//$totals = WebApp::get_formatted_microtime() - $starts;
		//echo $debugTime = "<br>inside ProgressConfiguration END->".round($totals, 2).":totals";
    }
    function configureElearningStatusLabel($typeOfCatalogItems="lectures",$nemLabels) {
		$defaultLectureStatusLabel = array();
		if ($typeOfCatalogItems=="lectures") {
										
					$defaultLectureStatusLabel["statusLabelMesg"] 							= "{{_statusfault_label}}";
					$defaultLectureStatusLabel["_st_lectures_label_new"] 					= "{{_stdefault_lectures_label_new}}";
					$defaultLectureStatusLabel["_st_lectures_label_passed"] 				= "{{_stdefault_lectures_label_passed}}";
					$defaultLectureStatusLabel["_st_lectures_label_passed_with_survey"] 	= "{{_stdefault_lectures_label_passed_with_survey}}";
					$defaultLectureStatusLabel["_st_lectures_label_notpassed"] 				= "{{_stdefault_lectures_label_notpassed}}";
					$defaultLectureStatusLabel["_st_lectures_label_inprogress"] 			= "{{_stdefault_lectures_label_inprogress}}";

					if (isset($nemLabels["text"]["statusLabel"]) && $nemLabels["text"]["statusLabel"]!="") 
						$defaultLectureStatusLabel["statusLabelMesg"] = $nemLabels["text"]["statusLabel"];

					if (isset($nemLabels["text"]["stNotStarted"]) && $nemLabels["text"]["stNotStarted"]!="") 
						$defaultLectureStatusLabel["_st_lectures_label_new"] = $nemLabels["text"]["stNotStarted"];

					if (isset($nemLabels["text"]["stPassedFeedbackReq"]) && $nemLabels["text"]["stPassedFeedbackReq"]!="") 
						$defaultLectureStatusLabel["_st_lectures_label_passed"] = $nemLabels["text"]["stPassedFeedbackReq"];
					if (isset($nemLabels["text"]["stPassedCmeAwarded"]) && $nemLabels["text"]["stPassedCmeAwarded"]!="") 
						$defaultLectureStatusLabel["_st_lectures_label_passed_with_survey"] = $nemLabels["text"]["stPassedCmeAwarded"];

					if (isset($nemLabels["text"]["stAssessmentFailed"]) && $nemLabels["text"]["stAssessmentFailed"]!="") 
						$defaultLectureStatusLabel["_st_lectures_label_notpassed"] = $nemLabels["text"]["stAssessmentFailed"];
					if (isset($nemLabels["text"]["stInProgress"]) && $nemLabels["text"]["stInProgress"]!="") 
						$defaultLectureStatusLabel["_st_lectures_label_inprogress"] = $nemLabels["text"]["stInProgress"];

					reset($defaultLectureStatusLabel);
					WHILE (LIST($keyLbl, $valueLbl) =each($defaultLectureStatusLabel)) {
						WebApp::addVar("$keyLbl","$valueLbl");
					}				
					
					
					if (isset($nemLabels["headline"]) && $nemLabels["headline"]!="") {
						WebApp::addVar("headline_progress_usr_itms",$nemLabels["headline"]);
					}

					$testProgress 					= "";   
					if (isset($this->NEM_PROP["testProgress"]) && $this->NEM_PROP["testProgress"]!="") 
						$testProgress	= generalFunctionality::getPlaceholdersToReplace($this->NEM_PROP["testProgress"]);
					WebApp::addVar("__testProgress","$testProgress");

					$LecturePresentationProgress 					= "";   
					if (isset($this->NEM_PROP["LecturePresentationProgress"]) && $this->NEM_PROP["LecturePresentationProgress"]!="") 
						$LecturePresentationProgress	= generalFunctionality::getPlaceholdersToReplace($this->NEM_PROP["LecturePresentationProgress"]);
					WebApp::addVar("__LecturePresentationProgress","$LecturePresentationProgress");

					$addionalResourceProgress 					= "";   
					if (isset($this->NEM_PROP["addionalResourceProgress"]) && $this->NEM_PROP["addionalResourceProgress"]!="") 
						$addionalResourceProgress	= generalFunctionality::getPlaceholdersToReplace($this->NEM_PROP["addionalResourceProgress"]);
					WebApp::addVar("__addionalResourceProgress","$addionalResourceProgress");

					$total_time_spended 					= "";   
					if (isset($this->NEM_PROP["total_time_spended"]) && $this->NEM_PROP["total_time_spended"]!="") 
						$total_time_spended	= generalFunctionality::getPlaceholdersToReplace($this->NEM_PROP["total_time_spended"]);
					WebApp::addVar("__total_time_spended","$total_time_spended");

					$min_sec 					= "";   
					if (isset($this->NEM_PROP["min_sec"]) && $this->NEM_PROP["min_sec"]!="") 
						$min_sec	= $this->NEM_PROP["min_sec"];
					WebApp::addVar("__min_sec","$min_sec");

					WebApp::addVar("show_modules_details","no");
					if (isset($this->NEM_PROP["show_modules_details"]) && $this->NEM_PROP["show_modules_details"]==0) {
						WebApp::addVar("show_modules_details","yes");
					}

					if (isset($this->NEM_PROP["view_result"]) && $this->NEM_PROP["view_result"]!="") {
						WebApp::addVar("__view_result",$this->NEM_PROP["view_result"]);
					} else {
						WebApp::addVar("__view_result","{{___view_result_default}}");
					}

					if (isset($this->NEM_PROP["show_certificate"]) && $this->NEM_PROP["show_certificate"]==0) {
						WebApp::addVar("show_certificate","yes");
						if (isset($this->NEM_PROP["view_certificate"]) && $this->NEM_PROP["view_certificate"]!="") {
							WebApp::addVar("__view_certificate",$this->NEM_PROP["view_certificate"]);
						} else {
							WebApp::addVar("__view_certificate","{{___view_certificate_default}}");
						}	
					}					
    	} else {
 					$defaultLectureStatusLabel["statusLabelMesg"] 						= "{{_statusfault_label}}";
					$defaultLectureStatusLabel["_st_modules_label_new"] 				= "{{_stdefault_modules_label_new}}";
					$defaultLectureStatusLabel["_st_modules_label_passed"] 				= "{{_stdefault_modules_label_passed}}";
					
					$defaultLectureStatusLabel["_st_modules_label_completed"] 			= "{{_stdefault_modules_label_completed}}";
					$defaultLectureStatusLabel["_st_modules_label_notpassed"] 			= "{{_stdefault_modules_label_notpassed}}";
					$defaultLectureStatusLabel["_st_modules_label_inprogress"] 			= "{{_stdefault_modules_label_inprogress}}";

					if (isset($nemLabels["text"]["statusLabel"]) && $nemLabels["text"]["statusLabel"]!="") 
						$defaultLectureStatusLabel["statusLabelMesg"] = $nemLabels["text"]["statusLabel"];

					if (isset($nemLabels["text"]["stNotStarted"]) && $nemLabels["text"]["stNotStarted"]!="") 
						$defaultLectureStatusLabel["_st_modules_label_new"] = $nemLabels["text"]["stNotStarted"];

					if (isset($nemLabels["text"]["stPassedFeedbackReq"]) && $nemLabels["text"]["stPassedFeedbackReq"]!="") 
						$defaultLectureStatusLabel["_st_modules_label_completed"] = $nemLabels["text"]["stPassedFeedbackReq"];
					
					if (isset($nemLabels["text"]["stPassedCmeAwarded"]) && $nemLabels["text"]["stPassedCmeAwarded"]!="") 
						$defaultLectureStatusLabel["_st_modules_label_passed"] = $nemLabels["text"]["stPassedCmeAwarded"];

					if (isset($nemLabels["text"]["stAssessmentFailed"]) && $nemLabels["text"]["stAssessmentFailed"]!="") 
						$defaultLectureStatusLabel["_st_modules_label_notpassed"] = $nemLabels["text"]["stAssessmentFailed"];
					
					if (isset($nemLabels["text"]["stInProgress"]) && $nemLabels["text"]["stInProgress"]!="") 
						$defaultLectureStatusLabel["_st_modules_label_inprogress"] = $nemLabels["text"]["stInProgress"];

					reset($defaultLectureStatusLabel);
					WHILE (LIST($keyLbl, $valueLbl) =each($defaultLectureStatusLabel)) {
						WebApp::addVar("$keyLbl","$valueLbl");
					}				
    	}
    }
	function getPartialStructuredInformationEcc($relatedInfo="lectures_level", $referenceIDsCOF,$typeOfResults="progress",$nodeFilterKey="",$calculateTimeActivity="yes")
	{    
    	global $session;
			
		if (!isset($this->partecipationInfo)) {
			$this->getPartecipationInfo();
		}
    	
		$StructuredInformationEcc = array();
		$BoCondition = "";
		
		/*echo "partecipationInfo<textarea>";
		print_r($this->partecipationInfo);
		echo "</textarea>";	*/	
		if (isset($this->dataMode) && $this->dataMode=="FE") {
				$md= "";
				$BoCondition = "
								AND n.active".$session->Vars["lang"]." != 1
								AND n.state".$session->Vars["lang"]." != 7
								AND content.state".$session->Vars["lang"]." not in (0,5,7)
								AND content.published".$session->Vars["lang"]." = 'Y'		
								AND n.description".$session->Vars["lang"].$md." is not null";
		
		
		} else {
			$md= $session->Vars["thisMode"];
			if ($this->thisModeCode=="0") { //a paaprovuar
				$BoCondition = "AND n.state".$session->Vars["lang"]." != 7
								AND content.state".$session->Vars["lang"]." not in (0,5,7)";
			} else {
				$BoCondition = "
								AND n.active".$session->Vars["lang"]." != 1
								AND n.state".$session->Vars["lang"]." != 7
								AND content.state".$session->Vars["lang"]." not in (0,5,7)
								AND content.published".$session->Vars["lang"]." = 'Y'		
								AND n.description".$session->Vars["lang"].$md." is not null";
			}
		}		
		
		$lectureTypes = array();
		if (isset($this->navElearning["typeLecture"]))
		$lectureTypes = $this->navElearning["typeLecture"];
		if (isset($this->navElearning["typeRepository"]))
		$lectureTypes = array_merge($lectureTypes, $this->navElearning["typeRepository"]);
       
        if ($relatedInfo=="insideLecture") {
							

						if ($nodeFilterKey!="") {
							$crdItems = explode("_",$nodeFilterKey);
							if ($crdItems[4]>0) {
								$hierarchyLevel = 4;
								return;

							} elseif ($crdItems[3]>0) {			
								$hierarchyLevel = 3;
								$nivel_condition[$nodeFilterKey] = " content.id_zeroNivel = '".$crdItems[0]."'
														  AND content.id_firstNivel = '".$crdItems[1]."'
														  AND content.id_secondNivel = '".$crdItems[2]."'
														  AND content.id_thirdNivel  = '".$crdItems[3]."'
														  AND content.id_fourthNivel > 0";								
							} elseif ($crdItems[2]>0) {			
								$hierarchyLevel = 2;
								$nivel_condition[$nodeFilterKey] = "  content.id_zeroNivel = '".$crdItems[0]."'
														  AND content.id_firstNivel = '".$crdItems[1]."'
														  AND content.id_secondNivel = '".$crdItems[2]."'
														  AND content.id_thirdNivel  > 0
														  AND content.id_fourthNivel = 0";								
							} elseif ($crdItems[1]>0) { 			
								$hierarchyLevel = 1;
								$nivel_condition[$nodeFilterKey] = "  content.id_zeroNivel = '".$crdItems[0]."'
														  AND content.id_zeroNivel = '".$crdItems[0]."'
														  AND content.id_firstNivel = '".$crdItems[1]."'
														  AND content.id_secondNivel > 0
														  AND content.id_thirdNivel  = 0
														  AND content.id_fourthNivel = 0";			
							} else {			
								$hierarchyLevel = 0;	
								$nivel_condition[$nodeFilterKey] = "  content.id_zeroNivel = '".$crdItems[0]."'
														  AND content.id_zeroNivel = '".$crdItems[0]."'
														  AND content.id_firstNivel > 0
														  AND content.id_secondNivel = 0
														  AND content.id_thirdNivel  = 0
														  AND content.id_fourthNivel = 0 ";			
							}	
						} else {
								return;
						}
							
        } else {
        
					if ($typeOfResults=="progress") {
						$pathInsideLectureTypes = array();
						if ($calculateTimeActivity=="yes") {
							$pathInsideLectureTypes["SL"] = "SL";   
							$pathInsideLectureTypes["CQ"] = "CQ";    
							$pathInsideLectureTypes["SC"] = "SC";    
							$pathInsideLectureTypes["SM"] = "SM";   
							$pathInsideLectureTypes["ES"] = "ES";  
							$pathInsideLectureTypes["RA"] = "RA";  
							$pathInsideLectureTypes["RQ"] = "RQ";   
							$pathInsideLectureTypes["CT"] = "CT";   
							$pathInsideLectureTypes["RA"] = "RA"; 				
						} else {
							//$pathInsideLectureTypes["SL"] = "SL";   
							$pathInsideLectureTypes["CQ"] = "CQ";    
							//$pathInsideLectureTypes["SC"] = "SC";    
							//$pathInsideLectureTypes["SM"] = "SM";   
							$pathInsideLectureTypes["ES"] = "ES";  
							//$pathInsideLectureTypes["RA"] = "RA";  
							//$pathInsideLectureTypes["RQ"] = "RQ";   
							//$pathInsideLectureTypes["CT"] = "CT";   
							//$pathInsideLectureTypes["RA"] = "RA"; 				
						}
						if ($relatedInfo=="modules_level") {
							$pathInsideLectureTypes["MS"] = "MS";
						}			

					} else {
						if ($relatedInfo=="lectures_level") {
							$pathInsideLectureTypes["ES"] = "ES";  
							$pathInsideLectureTypes["CQ"] = "CQ";
						} else { //
							$pathInsideLectureTypes["MS"] = "MS";
						}
					}

					$typeCondition = " AND ci_type in ('".implode("','",$pathInsideLectureTypes)."')";
					  //kap hierarkine
						$l_condition = array();
						if ($nodeFilterKey!="") {

							$tmpL = explode("_",$nodeFilterKey);
							if (count($tmpL)>0) {
								while (list($dd,$idsl)=each($tmpL)) {
									if ($idsl>0) {
										if ($dd==0) 	$levCond[$nodeFilterKey] = " content.id_zeroNivel 	= '".$idsl."' "; 
										elseif ($dd==1) $levCond[$nodeFilterKey] .= " AND content.id_firstNivel 	= '".$idsl."' "; 
										elseif ($dd==2) $levCond[$nodeFilterKey] .= " AND content.id_secondNivel 	= '".$idsl."' "; 
										elseif ($dd==3) $levCond[$nodeFilterKey] .= " AND content.id_thirdNivel 	= '".$idsl."' "; 
										elseif ($dd==4) $levCond[$nodeFilterKey] .= " AND content.id_fourthNivel 	= '".$idsl."' "; 
									}
								}
							} 

						} elseif ($relatedInfo=="lectures_level" || $relatedInfo=="modules_level" || $relatedInfo=="currentWorkingLectures") {
							if (isset($referenceIDsCOF) && count($referenceIDsCOF)>0) {
								$levCond = array();
								reset($referenceIDsCOF);
								while (list($itemId,$koord)=each($referenceIDsCOF)) {
									$tmpL = explode("_",$koord);
									if (count($tmpL)>0) {
										while (list($dd,$idsl)=each($tmpL)) {
											if ($idsl>0) {
												if ($dd==0) 	$levCond[$itemId] = " content.id_zeroNivel 	= '".$idsl."' "; 
												elseif ($dd==1) $levCond[$itemId] .= " AND content.id_firstNivel 	= '".$idsl."' "; 
												elseif ($dd==2) $levCond[$itemId] .= " AND content.id_secondNivel 	= '".$idsl."' "; 
												elseif ($dd==3) $levCond[$itemId] .= " AND content.id_thirdNivel 	= '".$idsl."' "; 
												elseif ($dd==4) $levCond[$itemId] .= " AND content.id_fourthNivel 	= '".$idsl."' "; 
											}
										}
									}
								}
							}
						} 
						if (count($levCond)>0) {
							$tmp = implode(" ) OR ( ",$levCond);
							$nivel_condition['1'] .= " (".$tmp.") ";
						} else {
							return;
						}
						if ($relatedInfo=="modules_level") {
							if (count($this->partecipationInfo)>0) {
								$lecturesPartecipationKeys = array_keys($this->partecipationInfo);
							//	$lecturesPartecipationKeyss  = array_distinct($lecturesPartecipationKeys);
								$lecturesPartecipationKeysIds = "'".implode("','",$lecturesPartecipationKeys)."'";

								$typeCondition .= " AND concat(content.id_zeroNivel,'_',content.id_firstNivel,'_',content.id_secondNivel,'_',content.id_thirdNivel) in (".$lecturesPartecipationKeysIds.")";
							}			

						}
			}

							
							
           if (count($nivel_condition) > 0) {
                $kushtSql = " (".implode(" ) AND ( ", $nivel_condition).") ";
                $sql_con = "SELECT content.content_id, ci_type, 
                							n.description".$session->Vars["lang"].$session->Vars["thisMode"]." as nodeName,
											content.id_zeroNivel, content.id_firstNivel,content.id_secondNivel,content.id_thirdNivel,content.id_fourthNivel,
											titleLng1 as title, n.orderMenu as nodeOrder
									  FROM content
										JOIN nivel_4			AS n	ON (    content.id_zeroNivel   = n.id_zeroNivel
																AND content.id_firstNivel  = n.id_firstNivel
																AND content.id_secondNivel = n.id_secondNivel
																AND content.id_thirdNivel  = n.id_thirdNivel
																AND content.id_fourthNivel = n.id_fourthNivel
																)								  
										JOIN profil_rights ON (       content.id_zeroNivel   = profil_rights.id_zeroNivel
																AND content.id_firstNivel  = profil_rights.id_firstNivel
																AND content.id_secondNivel = profil_rights.id_secondNivel
																AND content.id_thirdNivel  = profil_rights.id_thirdNivel
																AND content.id_fourthNivel = profil_rights.id_fourthNivel
																AND profil_rights.profil_id in (".$session->Vars["tip"].")
															)
								WHERE ".$kushtSql." AND orderContent = '0' ".$BoCondition.$typeCondition."
							GROUP BY content.id_zeroNivel, content.id_firstNivel,content.id_secondNivel,content.id_thirdNivel,content.id_fourthNivel, content.content_id
							ORDER BY nivel_4.orderMenu";
                	$rs_con = WebApp::execQuery($sql_con);
					$order 		= 1;
					$orderR 	= 1;
					$orderEc 	= 1;
					$orderGN 	= 1;
					while (!$rs_con->EOF()) {

						$ci_type 	= $rs_con->Field("ci_type");
						$relid 		= $rs_con->Field("content_id");
						$title 		= $rs_con->Field("title");
						$nodeName 	= $rs_con->Field("nodeName");	
						$nodeOrder 	= $rs_con->Field("nodeOrder");
						$id_zeroNivel 		= $rs_con->Field("id_zeroNivel");
						$id_firstNivel 		= $rs_con->Field("id_firstNivel");
						$id_secondNivel 	= $rs_con->Field("id_secondNivel");
						$id_thirdNivel 		= $rs_con->Field("id_thirdNivel");
						$id_fourthNivel 	= $rs_con->Field("id_fourthNivel");
						
						$keyItem = $id_zeroNivel."_".$id_firstNivel."_".$id_secondNivel."_".$id_thirdNivel."_".$id_fourthNivel;
						if ($typeOfResults=="progress") {

							if ($relatedInfo=="lectures_level" || $relatedInfo=="currentWorkingLectures") {

								$keyModul = $id_zeroNivel."_".$id_firstNivel."_".$id_secondNivel;
								$keyLecture = $id_zeroNivel."_".$id_firstNivel."_".$id_secondNivel."_".$id_thirdNivel;

								$StructuredInformationEcc["el"][$keyLecture][$ci_type][$keyItem] 	= $relid;
								$StructuredInformationEcc["in"][$relid] 	= $keyModul;
							} else {
								$keyModul = $id_zeroNivel."_".$id_firstNivel."_".$id_secondNivel;
								$keyLecture = $id_zeroNivel."_".$id_firstNivel."_".$id_secondNivel."_".$id_thirdNivel;
								$StructuredInformationEcc["ec"][$keyModul][$ci_type][$keyItem] 	= $relid;	 //[$keyLecture]					
								$StructuredInformationEcc["in"][$relid] 	= $keyLecture;
							}
							$StructuredInformationEcc["dt"][$relid]	= $nodeName;
						} else {
							$keyModul = $id_zeroNivel."_".$id_firstNivel."_".$id_secondNivel;
							$keyLecture = $id_zeroNivel."_".$id_firstNivel."_".$id_secondNivel."_".$id_thirdNivel;
							if ($relatedInfo=="lectures_level" )
									$StructuredInformationEcc[$ci_type][$keyLecture]	= $relid;
							else	$StructuredInformationEcc[$ci_type][$keyModul]	= $relid;
						}
						$rs_con->MoveNext();
					}
            } 
            return $StructuredInformationEcc;
	}    
	function findPartecipationInfoInContextOfEnrlollement($referenceIDsCOF,$distinctsIdsInCollector) {
	}	
	function userOverallProgressProgramLevel ($referenceIDsCOF,$distinctsIdsInCollector,$calculateTimeActivity="yes",$getModuleExaminationState="no") {
		global $session;

		$starts	= WebApp::get_formatted_microtime();

		$StructuredInformationEcc = $this->getPartialStructuredInformationEcc("modules_level",$referenceIDsCOF,"progress","",$calculateTimeActivity);
									     //getPartialStructuredInformationEcc($relatedInfo, $referenceIDsCOF,$typeOfResults="progress",$nodeFilterKey="",$calculateTimeActivity="yes")

		$tot = array();
		$det = array();
		
		$validationData	= array();
		
		$tmp["modules"]["tot"] 				= 0;
		$tmp["modules"]["failed"] 			= 0;
		$tmp["modules"]["passed"] 			= 0;
		$tmp["modules"]["progress"] 		= 0;
		$tmp["modules"]["remaining"] 		= 0;
		
		
		$tmp["modules"]["mod_failed"] 			= 0;
		$tmp["modules"]["mod_passed"] 			= 0;
		$tmp["modules"]["mod_progress"] 		= 0;
		$tmp["modules"]["mod_remaining"] 		= 0;

		if ($getModuleExaminationState=="no") {
			$tmp["modules"]["remaining"] 		= 0;
		
		}
		
		$tmp["cme_credits"]["tot"] 			= 0;
		$tmp["cme_credits"]["earned"] 		= 0;
		$tmp["cme_credits"]["remaining"]	= 0;

		$tmp["activity"]["RA"]		= 0;
		$tmp["activity"]["SC"]		= 0;
		$tmp["activity"]["RA"]		= 0;
		$tmp["activity"]["CT"]		= 0;
		$tmp["activity"]["OTHER"]	= 0;
		$tmp["activity"]["ALL"]		= 0;
		
		$moduleTot=0;
		$CmeCredits=0;
		$tot = $tmp;

		$tot["lectures"] = $tot["modules"];
		//if ($getModuleExaminationState=="yes") {
		//	INCLUDE_ONCE INC_PATH."elearning.user.progress.class.php";
		//	$moduleExamResults = eLearningUserPlatform::getUsersResultsInModuleAssessment($session->Vars["ses_userid"]);
		//}
		


/*
Status: Module Not Started
Status: Module In Progress

Status: Module Lectures completed, Tutorial and Exam Pending
Status: Module Passed

Status: Module Examination Failed, Resit Required

Module progress colour
- Grey/white when Module not started or opened - when any Lecture inside the Module has not started yet)
- Orange when any Lecture in that Module has been started, if at least one Lecture has started
- Green when Module Examination Passed 
- Red when Module Examination Failed (ie resit required)
*/

		$tot["modules"]["tot"]=count($distinctsIdsInCollector);
		
		if (isset($StructuredInformationEcc["ec"]) && count($StructuredInformationEcc["ec"])>0) {
			reset($StructuredInformationEcc["ec"]);
			while (list($moduleKey,$moduleData)=each($StructuredInformationEcc["ec"])) {
				
				if (isset($this->POTS["EC"][$moduleKey])) {
							
							$moduleId = $this->POTS["EC"][$moduleKey];	
							if (isset($this->POTS["TCInEC"][$moduleKey])) {
								while (list($tutId, $ddd)=each($this->POTS["TCInEC"][$moduleKey])) {
									if (isset($this->POTS["TC_coord"][$tutId])) {
										$filterByNodesTeEvents = array();
										//$filterByNodesTeEvents[$moduleKey] = $this->POTS["TC_coord"][$tutId];
										$filterByNodesTeEvents[$moduleKey] = $tutId;
											 //findEventsExtendedInformation($filterByNodes,$filterByTime="",$gridKey="",$orderBy="",$sortBy="",$ci_target="",$isMeine="no",$my_ci_target="",$limit="") {	
										$this->findEventsExtendedInformation($filterByNodesTeEvents,"",		"",			"lastUserContext",			"DESC",		"",			"",				"",1,$moduleId,$tutId);
									}
								}
							}						
							
							$itemId = $moduleId;
							if (!isset($det[$moduleId])) {
								$det[$moduleId] = $tmp;
							}
							
							
							//	INCLUDE_ONCE INC_PATH."elearning.user.progress.class.php";
							//	$moduleExamResults = eLearningUserPlatform::getUsersResultsInModuleAssessment($session->Vars["ses_userid"]);

							/*echo "<textarea> $moduleKey:moduleKey\n";
							echo "passed \n";
							print_r($moduleExamResults["inContext"]["passed"][$moduleKey]);
							echo "not_passed \n";
							print_r($moduleExamResults["inContext"]["not_passed"][$moduleKey]);
							echo "</textarea>";	*/	
								
							$exam_status = "empty";
							if (isset($moduleExamResults["inContext"]["passed"][$moduleKey]))
								$exam_status = "passed";
							elseif (isset($moduleExamResults["inContext"]["not_passed"][$moduleKey]))
								$exam_status = "not_passed";
							$det[$moduleId]["modules"]["exam_status"] = $exam_status;
							
							
							/*if ($getModuleExaminationState=="yes") {
								echo "$moduleId:moduleId<br>";
								echo "$moduleKey:moduleKey<br>";
								echo "$exam_status:exam_status<br><br>";
							}*/								
							
								
							if (count($this->POTS["ELInEC"][$moduleKey])>0) {
							
									$getProgressData="yes";	
									if ($this->apply_catalog_enrollement_state=="yes" && $this->enable_catalog_personalization=="yes") {

										if (is_array($this->partecipationStr['ec']) && in_array($moduleKey,$this->partecipationStr['ec'])) {
											$getProgressData="yes";
										} else {
											$getProgressData="no";
										}
									}						
									
									if ($getProgressData=="no") {
										//mos e agrego
									} else {
									
										
										$lecturesToBeTraited = $this->POTS["ELInEC"][$moduleKey];
						
									
										if ($this->apply_catalog_enrollement_state=="yes" && $this->enable_catalog_personalization=="yes") {
											//kontrollo qe referencat te jene enrolled
											$referenceIDsCOFToBeControled = $lecturesToBeTraited;
											$lecturesToBeTraited = array();
											reset($referenceIDsCOFToBeControled);
											WHILE (list($liID1,$liID)=each($referenceIDsCOFToBeControled)) {
												if (isset($this->POTS["EL_coord"][$liID]) ) {
													$liKey = $this->POTS["EL_coord"][$liID];
													if (is_array($this->partecipationStr['el']) && in_array($liKey,$this->partecipationStr['el'])) {
														$lecturesToBeTraited[$liID] = $liID;
													}
												}												
											}
										}	

									
										//echo $moduleKey.":moduleKey";

										$det[$moduleId]["modules"]["tot"] = count($lecturesToBeTraited);
										$ciflowsel = implode(",",$lecturesToBeTraited);
										$cmeCredits	= $this->getCmeCredits($ciflowsel);
										if (count($cmeCredits)>0) {
											while (list($lectureID,$cmeCreditsInLecture)=each($cmeCredits)) {
												$det[$moduleId]["cme_credits"]["tot"] +=$cmeCreditsInLecture;
											}
										}

										$lecturesPathRelated = $moduleData;
										if (isset($lecturesPathRelated["ES"])) {
											$ciflows = implode(",",$lecturesPathRelated["ES"]);
											$dataReturnedM = $this->userTestResultMulti($ciflows);
											if (count($dataReturnedM)>0) {
												while (list($ciflowEs,$dataReturned)=each($dataReturnedM)) {
													if ($dataReturned["nr_test_tot"] && $dataReturned["nr_test_tot"]>0) {

														$coordItem = $StructuredInformationEcc["in"]["$ciflowEs"];

														if ($dataReturned["passed"]>0) { 
															$det[$moduleId]["modules"]["passed"] +=$det[$moduleId]["modules"]["passed"]; 
															$validationData["$moduleKey"][$coordItem]["passed"]	+= 1;
															if (isset($this->POTS["EL"][$coordItem])) {
																$det[$moduleId]["cme_credits"]["earned"] +=$cmeCredits[$this->POTS["EL"][$coordItem]];
															}

															//$CmeCredits+=5;
														} elseif ($dataReturned["not_passed"]>0) {
															$det[$moduleId]["modules"]["failed"] +=$det[$moduleId]["modules"]["failed"]; 
															$validationData["$moduleKey"][$coordItem]["failed"]	+= 1;	
														} else {
															if (!isset($validationData["$moduleKey"][$coordItem]))
																$validationData["$moduleKey"][$coordItem]["progress"]	+= 1;	
														}
													}
												}
											}
										}	

										if (isset($lecturesPathRelated["CQ"])) {
											$ciflows = implode(",",$lecturesPathRelated["CQ"]);
											$dataReturnedM = $this->userTestResultMulti($ciflows);

											if (count($dataReturnedM)>0) {
												while (list($ciflowEs,$dataReturned)=each($dataReturnedM)) {
													if ($dataReturned["nr_test_tot"] && $dataReturned["nr_test_tot"]>0) {
														$coordItem = $StructuredInformationEcc["in"]["$ciflowEs"];
														if (!isset($validationData["$moduleKey"][$coordItem]))
															$validationData["$moduleKey"][$coordItem]["progress"]	+= 1;												
													}								
												}
											}
										}


						if ($calculateTimeActivity=="yes") {
										
										if (isset($lecturesPathRelated["RA"])) {
											while (list($coordItem,$RAID)=each($lecturesPathRelated["RA"])) {
												$koorTmp = explode("_",$coordItem);
												$coordItemLec = $StructuredInformationEcc["in"]["$RAID"];
												$raid_cis = array_merge($this->getRAcategory($RAID,$koorTmp));
												if (isset($raid_cis["all"])) {
													$ciflows = implode(",",$raid_cis["all"]);
													$dataToReturn 	= $this->getRAAnalyticsCat ($ciflows);
													if (isset($dataToReturn["duration"]) && $dataToReturn["duration"]>0) {
														$det[$moduleId]["activity"]["RA"] += $dataToReturn["duration"];	
														//$det[$moduleId]["activity"]["ALL"] += $dataToReturn["duration"];	
														if (!isset($validationData["$moduleKey"][$coordItemLec]))
															$validationData["$moduleKey"][$coordItemLec]["progress"]	+= 1;	
													}
												}
											}
										}

										if (isset($lecturesPathRelated["SC"])) {
											while (list($coordItem,$SCID)=each($lecturesPathRelated["SC"])) {
												$koorTmp = explode("_",$coordItem);
												$coordItemLec = $StructuredInformationEcc["in"]["$SCID"];
												$slides_cis = $this->getListOfSlides($SCID,$koorTmp);
												$ids1 = implode(",",$slides_cis);
												$dataToReturn 	= $this->getRAAnalyticsCat ($ids1);
												if (isset($dataToReturn["duration"]) && $dataToReturn["duration"]>0) {
														//$det[$moduleId]["activity"]["SC"] += $dataToReturn["duration"];	
														//$det[$moduleId]["activity"]["ALL"] += $dataToReturn["duration"];	
														if (!isset($validationData["$moduleKey"][$coordItemLec]))
															$validationData["$moduleKey"][$coordItemLec]["progress"]	+= 1;	
												}											
											}
										}
										$idsA = array();
										reset($lecturesPathRelated);
										while (list($docType,$ids)=each($lecturesPathRelated)) {
											while (list($coordItem,$idC)=each($ids)) {
												$idsA[$idC] = $idC;
											}
											$ciflows = implode(",",$idsA);
											$this->getCiAnalytics($ciflows);							
										}
						}

										reset($lecturesPathRelated);
										while (list($docType,$ids)=each($lecturesPathRelated)) {
											if ( $docType!="RA") { //$docType!="RA" && SC $docType!="RQ" && 
												while (list($coordItem,$idC)=each($ids)) {
													$coordItemLec = $StructuredInformationEcc["in"]["$idC"];

													if (isset($this->CiAnalytics[$idC]["ci_duration"]) && $this->CiAnalytics[$idC]["ci_duration"]>0) {
															$det[$moduleId]["activity"][$docType] 	+= $this->CiAnalytics[$idC]["ci_duration"];	
															$det[$moduleId]["activity"]["ALL"] 		+= $this->CiAnalytics[$idC]["ci_duration"];		
															if (!isset($validationData["$moduleKey"][$coordItemLec]))
																$validationData["$moduleKey"][$coordItemLec]["progress"]	+= 1;	
													}											
												}
											}
										}
							}
						}
						
						//exam_status[empty|passed|not_passed)
						if (isset($validationData["$moduleKey"]) && count($validationData["$moduleKey"])>0) {
							while (list($keyID,$progResType)=each($validationData["$moduleKey"])) {
								if (isset($progResType["failed"])) {
									$det[$moduleId]["modules"]["failed"] +=$progResType["failed"];	
								}
								if (isset($progResType["passed"])) {
									$det[$moduleId]["modules"]["passed"] +=$progResType["passed"];	
								}
								
								if ( isset($progResType["progress"]))  
									$det[$moduleId]["modules"]["progress"] +=$progResType["progress"];
									
								$tot["lectures"]["tot"]						+= $det[$moduleId]["modules"]["tot"];
								$tot["lectures"]["failed"]			+= $det[$moduleId]["modules"]["failed"];
								$tot["lectures"]["passed"]			+= $det[$moduleId]["modules"]["passed"];
								$tot["lectures"]["progress"] 		+= $det[$moduleId]["modules"]["progress"];
							}
						}
				
						/*$det[$moduleId]["modules"]["remaining"]  = $det[$moduleId]["modules"]["tot"];		
						$det[$moduleId]["modules"]["remaining"] -= $det[$moduleId]["modules"]["progress"];
						$det[$moduleId]["modules"]["remaining"] -= $det[$moduleId]["modules"]["failed"];
						$det[$moduleId]["modules"]["remaining"] -= $det[$moduleId]["modules"]["passed"];*/
				
						$det[$moduleId]["modules"]["remaining"]  = $det[$moduleId]["modules"]["tot"];		
						$det[$moduleId]["modules"]["remaining"] -= $det[$moduleId]["modules"]["progress"];
						$det[$moduleId]["modules"]["remaining"] -= $det[$moduleId]["modules"]["failed"];
						$det[$moduleId]["modules"]["remaining"] -= $det[$moduleId]["modules"]["passed"];						
						
						$tot["lectures"]["remaining"] 		+= $det[$moduleId]["modules"]["remaining"];
						
						$det[$moduleId]["cme_credits"]["tot"]  = $det[$moduleId]["cme_credits"]["tot"];		
						$det[$moduleId]["cme_credits"]["remaining"] =$det[$moduleId]["cme_credits"]["tot"];
						$det[$moduleId]["cme_credits"]["remaining"] -= $det[$moduleId]["cme_credits"]["earned"];
						
						$det[$moduleId]["modules"]["class"] = "item-future-bg";
						
						$exam_status = $det[$moduleId]["modules"]["exam_status"];
						
						if ($exam_status=="passed") {
							$det[$moduleId]["modules"]["class"] = "item-complete-bg";
						} elseif ($exam_status=="not_passed") {
							$det[$moduleId]["modules"]["class"] = "item-failed-bg";
						/*
						} elseif ($det[$moduleId]["modules"]["passed"]>0 && $det[$moduleId]["modules"]["passed"]==$det[$moduleId]["modules"]["tot"]) {
							$det[$moduleId]["modules"]["class"] = "item-complete-bg";
						} elseif ($det[$moduleId]["modules"]["failed"]>0 && $det[$moduleId]["modules"]["passed"]==$det[$moduleId]["modules"]["tot"]) {
							$det[$moduleId]["modules"]["class"] = "item-failed-bg";
							
							*/
						} elseif ($det[$moduleId]["modules"]["progress"]>0 || $det[$moduleId]["modules"]["passed"]>0 || $det[$moduleId]["modules"]["failed"]>0) {
							$det[$moduleId]["modules"]["class"] = "item-current-bg";
						}
						/*
						                    [exam_status] => not_passed
						                    [class] => item-failed-bg
						                    [statusToReport] => passed
						                    [statusToReportLabel] => passed

						*/
						// status[new|passed|passed_with_survey|notpassed|inprogress]
						$statusToReport = "new";
						if ($exam_status == "passed") {
							$statusToReport = "passed";
						} elseif ($exam_status == "not_passed") {
							$statusToReport = "notpassed";
						} elseif ($det[$moduleId]["modules"]["passed"] == $det[$moduleId]["modules"]["tot"] && $det[$moduleId]["modules"]["passed"]>0) {
							$statusToReport = "completed";
						} elseif ($det[$moduleId]["modules"]["progress"]>0 || $det[$moduleId]["modules"]["passed"]>0 || $det[$moduleId]["modules"]["failed"]>0) {
							$statusToReport = "inprogress";
						}
						$statusToReportLabel = "{{_st_modules_label_".$statusToReport."}}";
								
						$det[$moduleId]["modules"]["statusToReport"]		= $statusToReport;
						$det[$moduleId]["modules"]["statusToReportLabel"]	= $statusToReportLabel;
						
						$statusToReportTmp["data"][0] = array();
						$statusToReportTmp["data"][0]["statusToReport"] = $statusToReport;
						$statusToReportTmp["data"][0]["statusToReportLabel"] = $statusToReportLabel;
						$statusToReportTmp["AllRecs"] = 1;

						WebApp::addVar("ElearningItemStatusLabelToGrid_".$moduleId, $statusToReportTmp);	
						
						
				}
			}
		}
		
/*if ($getModuleExaminationState=="yes") {		
		echo "<textarea>DET";
		print_r($moduleExamResults);
		print_r($det);
		echo "</textarea>";
}*/
		

		
		
		reset($det);
		while (list($moduleId,$progResType)=each($det)) {
		
		//	$tot["modules"]["tot"] 			+= 1;
			if ($progResType["modules"]["failed"]>0 && $progResType["modules"]["failed"]==$progResType["modules"]["tot"]) 
				$tot["modules"]["failed"] 			+= 1;
			if ($progResType["modules"]["passed"]>0 && $progResType["modules"]["passed"]==$progResType["modules"]["tot"])
				$tot["modules"]["passed"] 			+= 1;
			
			if ($progResType["modules"]["progress"]>0 || $progResType["modules"]["failed"]>0 || $progResType["modules"]["passed"]>0)
				$tot["modules"]["progress"] 			+= 1;
			
			$tot["cme_credits"]["tot"] 			+= $progResType["cme_credits"]["tot"];
			$tot["cme_credits"]["earned"] 		+= $progResType["cme_credits"]["earned"];
			$tot["cme_credits"]["remaining"]	+= $progResType["cme_credits"]["remaining"];

			if ($calculateTimeActivity=="yes") {
			
				$tot["activity"]["RA"]	+= $progResType["activity"]["RA"];
				$tot["activity"]["SC"]	+= $progResType["activity"]["SC"];
				$tot["activity"]["CT"]	+= $progResType["activity"]["CT"];
				$tot["activity"]["OTHER"]	+= $progResType["activity"]["OTHER"];
				$tot["activity"]["ALL"]	+= $progResType["activity"]["ALL"];		

				$det[$moduleId]["activity"]["RA"] = "".$this->roundSecToHours($progResType["activity"]["RA"]);
				$det[$moduleId]["activity"]["SC"] = "".$this->roundSecToHours($progResType["activity"]["SC"]);
				$det[$moduleId]["activity"]["CT"] = "".$this->roundSecToHours($progResType["activity"]["CT"]);
				$det[$moduleId]["activity"]["OTHER"] = "".$this->roundSecToHours($progResType["activity"]["OTHER"]);
				$det[$moduleId]["activity"]["ALL"] 	 = "".$this->roundSecToHours($progResType["activity"]["ALL"]);
			}
		}
		
		$tot["modules"]["remaining"]  = $tot["modules"]["tot"];		
		$tot["modules"]["remaining"] -= $tot["modules"]["progress"];
		$tot["modules"]["remaining"] -= $tot["modules"]["failed"];
		$tot["modules"]["remaining"] -= $tot["modules"]["passed"];
		
		if ($calculateTimeActivity=="yes") {
			$tot["activity"]["RA"]  	= "".$this->roundSecToHours($tot["activity"]["RA"]);
			$tot["activity"]["SC"]  	= "".$this->roundSecToHours($tot["activity"]["SC"]);
			$tot["activity"]["CT"]  	= "".$this->roundSecToHours($tot["activity"]["CT"]);
			$tot["activity"]["OTHER"]  	= "".$this->roundSecToHours($tot["activity"]["OTHER"]);
			$tot["activity"]["ALL"]  	= "".$this->roundSecToHours($tot["activity"]["ALL"]);
		}
		
		$tot["cme_credits"]["remaining"]  = $tot["cme_credits"]["tot"];
		$tot["cme_credits"]["remaining"] -=  $tot["cme_credits"]["earned"];

		$progresInfo["totalsInfo"]=$tot;
		$progresInfo["detailsInfo"]=$det;
/*if ($getModuleExaminationState=="yes") {		
		
		echo "<textarea>";
	//	print_r($det);
	//	print_r($cmeCredits);
	//	print_r($tot);
		print_r($progresInfo);
	//	print_r($this->POTS);
		echo "</textarea>";
}*/
	$this->PAGELOAD_PARTIAL($starts,"inside userOverallProgressProgramLevel $calculateTimeActivity:calculateTimeActivity->");
		
		return $progresInfo;
	}
	function roundSecToHours ($seconds,$nrDec=1) {

		return round($seconds/3600,1);
	}
	function getCmeCredits ($distinctsIdsInCollector) {
		$cmes =array();
		if ($distinctsIdsInCollector!="") {

			if (isset($this->dataMode) && $this->dataMode=="FE") {
				$thisModeCode = 1;
			} else {
				$thisModeCode = $this->thisModeCode;
			}

			$getCmeCredits = "SELECT contentId, coalesce(cme_fees_value, '0') as cme_fees_value,coalesce(cme_credits_value, '0') as cme_credits_value 
									  FROM z_EccE_availability_information	
									 WHERE contentId in (".$distinctsIdsInCollector.")	
									   AND z_EccE_availability_information.lng_id 		= '".$this->lngId."'
									   AND z_EccE_availability_information.statusInfo 	= '".$thisModeCode."'";			
			$rs=WebApp::execQuery($getCmeCredits);
			WHILE (!$rs->EOF()) {	

				$cid		= $rs->Field("contentId");
				//$cmes[$cid]	= $rs->Field("cme_fees_value");
				$cmes[$cid]	= $rs->Field("cme_credits_value");
				$rs->MoveNext();
			}
		}
		return $cmes;
	}
	function userOverallProgressModuleLevel ($referenceIDsCOF,$distinctsIdsInCollector,$relatedInfo="lectures_level",$getModuleExaminationState="no") {
		global $session;
		

	/*	echo "<textarea>userOverallProgressModuleLevel:$relatedInfo";
		print_r($referenceIDsCOF);
		print_r($distinctsIdsInCollector);
		echo "</textarea><br>\n";	*/

		$starts	= WebApp::get_formatted_microtime();

		$StructuredInformationEcc = $this->getPartialStructuredInformationEcc($relatedInfo,$referenceIDsCOF);
		$ciflows	= implode(",",$distinctsIdsInCollector);
		$cmeCredits	= $this->getCmeCredits($ciflows);
		
		$surveyFilled = array();
			require_once(INC_PATH.'surveyExam.base.class.php');
			$surveyFilled = surveyBaseFuncionality::controlIfUserHasFilledLecturesSurvey($session->Vars["ses_userid"], $ciflows);

	/*	if ($getModuleExaminationState=="yes") {


echo "<textarea> ";
echo "$getModuleExaminationState";
print_r($surveyFilled);

echo "</textarea>";

		}*/



		$tot = array();
		$det = array();
		
		if (!isset($this->navElearning))
			$this->getElearningStructure();
		
		$this->getRAcategory = array();
		$this->CiAnalytics = array();
		//$this->CiAnalyticMain = array();
		$debugTestInfo = array();

		$tot["lectures"]["tot"] 		= 0;			//		$tmp["modules"]["tot"]=count($distinctsIdsInCollector);

		$tot["lectures"]["userTot"]		= 0;
		$tot["lectures"]["failed"] 		= 0;
		$tot["lectures"]["passed"] 		= 0;
		$tot["lectures"]["progress"] 	= 0;
		$tot["lectures"]["remaining"] 	= 0;
		
		$tot["cme_credits"]["tot"] 		= 0;
		$tot["cme_credits"]["earned"] 	= 0;
		$tot["cme_credits"]["remaining"]	= 0;

		reset($distinctsIdsInCollector);
		While (list($key,$lectureID)=each($distinctsIdsInCollector)) {
			$cmeCreditsInLecture = 0;
			if (isset($cmeCredits[$lectureID]))
				$cmeCreditsInLecture = $cmeCredits[$lectureID];
			$tot["cme_credits"]["tot"]	+=$cmeCreditsInLecture;
			$this->lectureAggregation["cme_total"][$lectureID] = $cmeCreditsInLecture;
		}
		
		/*echo "\n<br>USERoVERALLpROGRESSmODULElEVEL<br>\n<textarea>\n";
		echo "\n distinctsIdsInCollector \n ";
		print_r($distinctsIdsInCollector);
		echo "\n cmeCredits \n ";
		print_r($cmeCredits);
		echo "\n cme_total \n ";
		print_r($this->lectureAggregation["cme_total"]);
		echo "</textarea><br>\n";*/			
	
		if (isset($StructuredInformationEcc["el"]) && count($StructuredInformationEcc["el"])>0) {
			reset($StructuredInformationEcc["el"]);
			while (list($key,$keyData)=each($StructuredInformationEcc["el"])) {
				if (isset($this->POTS["EL"][$key])) {
					
					
					
					$itemId = $this->POTS["EL"][$key];
					
					//echo "\n$itemId-";
					if (in_array($itemId,$distinctsIdsInCollector)) {
							
							
							$raid_cis = array();
							$lecturesPathRelated = $keyData;
							if (isset($lecturesPathRelated["RA"])) {
								while (list($coordItem,$RAID)=each($lecturesPathRelated["RA"])) {
									$koorTmp = explode("_",$coordItem);
									$raid_cis = array_merge($this->getRAcategory($RAID,$koorTmp));
									if (isset($raid_cis["all"])) {
										$det[$itemId]["RA"]["all"] = count($raid_cis["all"]);
										$ciflows = implode(",",$raid_cis["all"]);
										$returnRAAnalyticsCat = $this->getRAAnalyticsCat($ciflows);									
									}
									if (isset($raid_cis["Compulsory"])) 	$det[$itemId]["RA"]["Compulsory"] = count($raid_cis["Compulsory"]);
									if (isset($raid_cis["Recommended"])) 	$det[$itemId]["RA"]["Recommended"] = count($raid_cis["Recommended"]);
									if (isset($raid_cis["Supporting"])) 	$det[$itemId]["RA"]["Supporting"] = count($raid_cis["Supporting"]);
									$det[$itemId]["RA_INFO"] = $raid_cis;
									$det[$itemId]["RA_ID"] = $RAID;
								}
							}
							
							if (isset($lecturesPathRelated["SL"]) && count($lecturesPathRelated["SL"])>0) {
								$ciflows = implode(",",$lecturesPathRelated["SL"]);
								$this->getCiRelatedWithRa($ciflows,$raid_cis);
							}
							if (isset($lecturesPathRelated["SM"]) && count($lecturesPathRelated["SM"])>0) {
								$ciflows = implode(",",$lecturesPathRelated["SM"]);
								$this->getCiRelatedWithSM($ciflows,$raid_cis);
								//$this->getCiRelatedWithRa($ciflows,$raid_cis);
							}	
							
							while (list($docType,$ids)=each($lecturesPathRelated)) {
								$idsA = array();
								while (list($coordItem,$idC)=each($ids)) {
									if (!isset($det[$itemId]["path"][$docType]))
										$det[$itemId]["path"][$docType][$idC] = array();
									$koorTmp = explode("_",$coordItem);
									$det[$itemId]["coor"][$idC] = $koorTmp;
									$idsA[$idC] = $idC;
								}
								$ciflows = implode(",",$idsA);
								if ($docType!="CQ" && $docType!="ES" && $docType!="RQ") {
									$this->getCiAnalytics($ciflows);
								}  
							}
						
							while (list($docType,$ids)=each($det[$itemId]["path"])) {
								while (list($idC,$ttt)=each($ids)) {
									if (isset($this->CiAnalytics[$idC])) {
										$det[$itemId]["path"][$docType][$idC] = $this->CiAnalytics[$idC];
									}
								}
							}
					}
				}
			}
		}
		
		
		$pathNAV = $this->navElearning["typeLecture"];
		$pathNAV["CT"] = "CT";

		$tot["lectures"]["tot"] 		= count($distinctsIdsInCollector);
		$this->lectureDetails	= $det;

		$this->PAGELOAD_PARTIAL($starts,"calculating activity END->");
		$starts	= WebApp::get_formatted_microtime();

		if (isset($det) && count($det)>0) {
			reset($det);
			while (list($lectureID,$dataPath)=each($det)) {
				
				$tot["lectures"]["userTot"]++;
				
				$tmpPrg 	= array();
				$tmpPrgTot 	= array();
				$index = 0;
				reset($pathNAV);
				
				$lectureAggregation[$lectureID]["time"] 		= 0;
				$lectureAggregation[$lectureID]["statusToReport"] 			= "new";
				$lectureAggregation[$lectureID]["statusToReportLabel"] 		= "";
				$lectureAggregation[$lectureID]["status"] 		= 0;
				$lectureAggregation[$lectureID]["is_filled_related_survey"] 		= "no";
				$lectureAggregation[$lectureID]["quiz"] 		= "no";
				$lectureAggregation[$lectureID]["examination"] 	= "no";
				$lectureAggregation[$lectureID]["passed"] 		= "";

				$cmeCreditsInLecture = 0;
				if (isset($cmeCredits[$lectureID]))
					$cmeCreditsInLecture = $cmeCredits[$lectureID];
				
				reset($pathNAV);
				while (list($ciType,$ddd)=each($pathNAV)) {
					
						if (!isset($tot["time"][$ciType]))	$tot["time"][$ciType] = 0;

						if (isset($dataPath["path"][$ciType]) && count($dataPath["path"][$ciType])>0) {
							while (list($ciflow,$infoToProgress)=each($dataPath["path"][$ciType])) {
								
								if ($ciType=="CT" ) {
								//	if (isset($this->CiAnalytics[$ciflow]["ci_duration"]) && $this->CiAnalytics[$ciflow]["ci_duration"]>0)
								//		$tot["time"][$ciType]	+= $this->CiAnalytics[$ciflow]["ci_duration"];
								} else {
							
									$generalInfo = $StructuredInformationEcc["dt"][$ciflow];
									$tmpPrg[$index]["ciflow"] 				= $ciflow;
									$tmpPrg[$index]["nodeDescription"] 		= $generalInfo;
									$tmpPrg[$index]["ci_duration"]			= "0";
									
									if ($ciType!="CQ" && $ciType!="ES") {
										if (isset($this->CiAnalytics[$ciflow]["ci_duration"])&& $this->CiAnalytics[$ciflow]["ci_duration"]>0) {
												$tmpPrg[$index]["ci_duration"]			 = "".$this->CiAnalytics[$ciflow]["ci_duration"];
												$lectureAggregation[$lectureID]["time"] += $tmpPrg[$index]["ci_duration"];
												$tot["time"][$ciType]					+= $tmpPrg[$index]["ci_duration"];
										} 
									} 
								
									$tmpPrg[$index]["colorRelatedToType"]	= "grey";
									$tmpPrg[$index]["progresRelatedToType"] = 1;
									$tmpPrg[$index]["ciType"] = $ciType;

									if ($ciType=="SL" || $ciType=="SM") {
										
										$nrItemsOpened = 0;
										$nrItemsTotal = 0;
										/*$nrItemsTotal = 1;
										if (isset($this->CiAnalytics[$ciflow]["ci_duration"]) && $this->CiAnalytics[$ciflow]["ci_duration"]>0) {
											$nrItemsOpened = 1;
										}*/
										
										if (isset($this->nrItems[$ciflow]["total"])) {
											$nrItemsTotal 	= $this->nrItems[$ciflow]["total"];
											$nrItemsOpened 	= $this->nrItems[$ciflow]["viewd"]+$nrItemsOpened;
										} 
										
										$tmpPrg[$index]["nrItemsTotal"] 		= "".$nrItemsTotal;
										$tmpPrg[$index]["nrItemsOpened"] 		= "".$nrItemsOpened;
										if ($nrItemsOpened>0)
											$tmpPrg[$index]["progresRelatedToType"]	= "".round(($nrItemsOpened/$nrItemsTotal)*100,0);										
										
										if ($tmpPrg[$index]["ci_duration"]>0) {
											$tmpPrg[$index]["statusRelatedToType"] = "";
											if ($tmpPrg[$index]["progresRelatedToType"]=="100") 
													$tmpPrg[$index]["colorRelatedToType"]	= "green";
											else	$tmpPrg[$index]["colorRelatedToType"]	= "amber";
										}										
										
									} elseif ($ciType=="CQ" || $ciType=="ES") {
										
										$tmpPrg[$index]["CertificateRelatedTag"] = "";
										$tmpPrg[$index]["resultExist"] = "no";
										
										$tmp = $this->userTestResult($ciflow);
										$debugTestInfo[$ciflow] = $tmp;
										//Info lastAttempt
										if (isset($tmp["lastAttempt"]) && Count($tmp["lastAttempt"])>0) {
												$examInfo = $tmp["lastAttempt"];
												$tmpPrg[$index]["ci_duration"]			 = "".$examInfo["time_spended_by_the_user_in_seconds"];
												$lectureAggregation[$lectureID]["time"] += $tmpPrg[$index]["ci_duration"];
												$tot["time"][$ciType]					+= $tmpPrg[$index]["ci_duration"];											
												
												$tmpPrg[$index]["statusRelatedToType"] = "";
												if ($examInfo["test_state_flag"]=='in_progress') {
													$tmpPrg[$index]["resultExist"] = "in_progress";
													$tmpPrg[$index]["colorRelatedToType"]	= "amber";
												}
												if ($examInfo["test_state_flag"]=='Evaluated') {
													$tmpPrg[$index]["resultExist"] = "yes";
													$tmpPrg[$index]["statusRelatedToType"] = $examInfo["trafficLightCase"];
													$tmpPrg[$index]["colorRelatedToType"] = $tmpPrg[$index]["statusRelatedToType"];													
												}
												$tmpPrg[$index] = array_merge($tmpPrg[$index],$examInfo);
												if ($tmpPrg[$index]["user_points_perqindje"]==0)
													$tmpPrg[$index]["user_points_perqindje"] =1;
										} 
										
										
										if (isset($tmp["totals"]) && Count($tmp["totals"])>0) {
												$tmpPrg[$index]["nr_certificate"] = $tmp["totals"]["nr_certificate"];
										} 
										
										if ($ciType=="CQ") $lectureAggregation[$lectureID]["quiz"]  = $tmpPrg[$index]["resultExist"];
										if ($ciType=="ES") {
											
											$lectureAggregation[$lectureID]["examination"]  = $tmpPrg[$index]["resultExist"];
											$lectureAggregation[$lectureID]["passed"]  		= $tmpPrg[$index]["statusRelatedToType"];
											if (isset($tmpPrg[$index]["nr_certificate"]) && $tmpPrg[$index]["nr_certificate"]>0)  {
												$tot["cme_credits"]["earned"]	+=$cmeCreditsInLecture;
												$this->lectureAggregation["cme_earned"][$lectureID] = $cmeCreditsInLecture;
											}
										}							
										
										$this->lectureAggregation["lectureDetails"][$lectureID][$ciType] = $tmp;
									} elseif ($ciType=="SC") {						
											
											$SCID = $ciflow;
											$slides_cis = $this->getListOfSlides($SCID,$det[$lectureID]["coor"][$SCID]);
											$ids1 = implode(",",$slides_cis);
											$nrItemsTotal= count($slides_cis);
											$tmpPrg[$index]["nrItemsTotal"] 		= "".$nrItemsTotal;		
											
										/*	$this->getCiAnalyticsInside($SCID,"","",$ids1);	
											if (isset($this->CiAnalytics[$SCID]["ci_duration"])&& $this->CiAnalytics[$SCID]["ci_duration"]>0) {
												$tmpPrg[$index]["ci_duration"]			 = "".$this->CiAnalytics[$SCID]["ci_duration"];
												$lectureAggregation[$lectureID]["time"] += $tmpPrg[$index]["ci_duration"];
												$tot["time"][$ciType]					+= $tmpPrg[$index]["ci_duration"];
											} */											
											
						
											if ($tmpPrg[$index]["ci_duration"]>0) {

												$dataToReturn 	= $this->getRAAnalyticsCat ($ids1);
												$nrItemsOpened	= $dataToReturn["nrOpened"];										
												$tmpPrg[$index]["nrItemsOpened"] 		= "".$nrItemsOpened;
												$tmpPrg[$index]["statusRelatedToType"] = "".$progresInfo[$ciflow]["status"];
												if ($nrItemsOpened>0)
												$tmpPrg[$index]["progresRelatedToType"]	= "".round(($nrItemsOpened/$nrItemsTotal)*100,0);

												if ($tmpPrg[$index]["progresRelatedToType"]=="100") {
													$tmpPrg[$index]["colorRelatedToType"]	= "green";
												} elseif ($tmpPrg[$index]["progresRelatedToType"]=="0") {
													$tmpPrg[$index]["colorRelatedToType"]	= "red";
													$tmpPrg[$index]["progresRelatedToType"] = 1;
												} else 
													$tmpPrg[$index]["colorRelatedToType"]	= "amber";
											} else 
												$tmpPrg[$index]["nrItemsOpened"] 		= "0";
									} elseif ($ciType=="RA") {	
											$SCID = $ciflow;
											$ids1 = implode(",",$det[$lectureID]["RA_INFO"]["all"]);
											$nrItemsTotal= $det[$lectureID]["RA"]["all"];
											$tmpPrg[$index]["nrItemsTotal"]	= "".$nrItemsTotal;
											$tmpPrg[$index]["ci_duration"]	= "0";
											//$tmpPrg[$index]["ci_duration"]	= "".$progresInfo[$ciflow]["ci_duration"];
											if ($tmpPrg[$index]["ci_duration"]>0) {
												$dataToReturn 	= $this->getRAAnalyticsCat ($ids1);
												$nrItemsOpened	= $dataToReturn["nrOpened"];										
												$tmpPrg[$index]["nrItemsOpened"] 		= "".$nrItemsOpened;
												$tmpPrg[$index]["statusRelatedToType"] = "".$progresInfo[$ciflow]["status"];
												if ($nrItemsOpened>0)
												$tmpPrg[$index]["progresRelatedToType"]	= "".round(($nrItemsOpened/$nrItemsTotal)*100,0);
												if ($tmpPrg[$index]["progresRelatedToType"]=="100") {
													$tmpPrg[$index]["colorRelatedToType"]	= "green";
												} elseif ($tmpPrg[$index]["progresRelatedToType"]=="0") {
													$tmpPrg[$index]["colorRelatedToType"]	= "red";
													$tmpPrg[$index]["progresRelatedToType"] = 1;
												} else
													$tmpPrg[$index]["colorRelatedToType"]	= "amber";
											} else 
												$tmpPrg[$index]["nrItemsOpened"] 		= "0";
									}

									$tmpPrg[$index]["ci_duration_formated"]	= "".generalFunctionality::secondsToTime($tmpPrg[$index]["ci_duration"],"yes");
									$tmpPrgExt = $tmpPrg;
									$index++;
								}
							}
						}
				}
				
				
		//		echo "$lectureID:lectureID";


				
				$tot["cme_credits"]["remaining"]	=$tot["cme_credits"]["tot"]-$tot["cme_credits"]["earned"];

				$tmpA["data"] = array();
				$lectureAggregation[$lectureID]["statusItemClass"] 		= "item-future-bg";		
				if ($lectureAggregation[$lectureID]["passed"]=="green") {
					$lectureAggregation[$lectureID]["statusItemClass"] 		= "item-complete-bg";
					$tot["lectures"]["passed"]++;
				} elseif ($lectureAggregation[$lectureID]["passed"]=="red") {
					$lectureAggregation[$lectureID]["statusItemClass"] 		= "item-failed-bg";
					$tot["lectures"]["failed"]++;	
				} else {
					if ($lectureAggregation[$lectureID]["time"]>0 || $lectureAggregation[$lectureID]["quiz"]=='yes' || $lectureAggregation[$lectureID]["examination"]=='yes') {
						$lectureAggregation[$lectureID]["statusItemClass"] 		= "item-current-bg";
						$tot["lectures"]["progress"]++;	
					}
				}
				if ($lectureAggregation[$lectureID]["time"]>0) {
					$lectureAggregation[$lectureID]["total_time_formated"] = "".generalFunctionality::secondsToTime($lectureAggregation[$lectureID]["time"],"yes");
				} else {
					$lectureAggregation[$lectureID]["total_time_formated"] = ".";
				}
				
				
				if ($lectureAggregation[$lectureID]["examination"]=="yes" && $lectureAggregation[$lectureID]["passed"]=="green") {
					$lectureAggregation[$lectureID]["statusToReport"] 		= "passed";
					if (isset($surveyFilled[$lectureID])) {
						$lectureAggregation[$lectureID]["statusToReport"]			= "passed_with_survey";
						$lectureAggregation[$lectureID]["is_filled_related_survey"]	= "yes";
					}
				} elseif ($lectureAggregation[$lectureID]["examination"]=="yes" && $lectureAggregation[$lectureID]["passed"]=="red") {
					$lectureAggregation[$lectureID]["statusToReport"] 		= "notpassed";
				} elseif ($lectureAggregation[$lectureID]["quiz"]=="yes") {
					$lectureAggregation[$lectureID]["statusToReport"] 		= "inprogress";
				} elseif ($lectureAggregation[$lectureID]["time"]>0) {
					$lectureAggregation[$lectureID]["statusToReport"] 		= "inprogress";
				}
				


				$lectureAggregation[$lectureID]["statusToReportLabel"] 		= "{{_st_lectures_label_".$lectureAggregation[$lectureID]["statusToReport"]."}}";	
					
				$tmpA["data"][0] = $lectureAggregation[$lectureID];
				$tmpA["AllRecs"] = 1;
				WebApp::addVar("lectureAggregationToGrid_$lectureID", $tmpA);	
				
				
				$statusToReportTmp["data"][0] = array();						
				$statusToReportTmp["data"][0]["statusToReport"] = $lectureAggregation[$lectureID]["statusToReport"];
				$statusToReportTmp["data"][0]["statusToReportLabel"] = $lectureAggregation[$lectureID]["statusToReportLabel"];
				$statusToReportTmp["AllRecs"] = 1;
				WebApp::addVar("ElearningItemStatusLabelToGrid_$lectureID", $statusToReportTmp);	
				
				$GridSessionProp["data"] = $tmpPrg;
				$GridSessionProp["AllRecs"] = count($GridSessionProp["data"]);
				WebApp::addVar("UserLectureProgressToGrid_$lectureID", $GridSessionProp);
				
				/*echo "<textarea> - $lectureID - ";
				echo "lectureAggregationToGrid_";
				print_r($tmpA);
				echo "UserLectureProgressToGrid_";
				print_r($GridSessionProp);
				echo "lectureAggregation";
				print_r($lectureAggregation[$lectureID]);
				echo "</textarea>";*/
								
				
				
				
		/*echo "<br>UserLectureProgressToGrid_".$lectureID."<br><textarea>";
		print_r($GridSessionProp);
		echo "</textarea><br>";	*/	
				//*******************
				$indexExt = count($tmpPrgExt);
			//	$indexExt=0;
			//	$tmpPrgExt = array();						
						if (isset($det[$lectureID]["RA_INFO"]) && count($det[$lectureID]["RA_INFO"])>0) {
								reset($det[$lectureID]["RA_INFO"]);
								while (list($categoies,$cids)=each($det[$lectureID]["RA_INFO"])) {
									if ($categoies!="other") {	
										$ciflow = $det[$lectureID]["RA_ID"];
										
										$generalInfo = $StructuredInformationEcc["dt"][$ciflow];
										$tmpPrgExt[$indexExt]["ciflow"]	= $ciflow;
										$tmpPrgExt[$indexExt]["title"]	= $generalInfo;
										
										$tmpPrgExt[$indexExt]["colorRelatedToType"]	= "grey";
										$tmpPrgExt[$indexExt]["progresRelatedToType"] = 1;
										$tmpPrgExt[$indexExt]["ciType"] = "RA";									

										$ids1 = implode(",",$cids);
										if ($categoies=="all") {
												$catLabel = $generalInfo;	
										} elseif ($categoies=="other")
												$catLabel = "{{_other}}";
										else	$catLabel = $categoies;		
										
										$tmpPrgExt[$indexExt]["nodeDescription"] 		= $catLabel;
										$tmpPrgExt[$indexExt]["title"] 					= $catLabel;
										
										$nrItemsTotal= count($cids);
										$dataToReturn 	= $this->getRAAnalyticsCat ($ids1);
										$nrItemsOpened	= $dataToReturn["nrOpened"];
										if (isset($dataToReturn["duration"]) && $dataToReturn["duration"]>0)
												$duration		= $dataToReturn["duration"];
										else	$duration		= 0;
									
										$tmpPrgExt[$indexExt]["ci_duration"]			= "".$duration;
										$tmpPrgExt[$indexExt]["ci_duration_formated"]	= "".generalFunctionality::secondsToTime($duration,"yes");
										$tmpPrgExt[$indexExt]["nrItemsTotal"] 		= "".$nrItemsTotal;
										$tmpPrgExt[$indexExt]["nrItemsOpened"] 		= "".$nrItemsOpened;
										$tmpPrgExt[$indexExt]["statusRelatedToType"] = $progresInfo[$ciflow]["status"];
										$tmpPrgExt[$indexExt]["progresRelatedToType"]	= round(($nrItemsOpened/$nrItemsTotal)*100,0);
										if ($tmpPrgExt[$indexExt]["progresRelatedToType"]=="100") {
											$tmpPrgExt[$indexExt]["colorRelatedToType"]	= "green";
										} elseif ($tmpPrgExt[$indexExt]["progresRelatedToType"]=="0") {
											$tmpPrgExt[$indexExt]["colorRelatedToType"]	= "red";
											$tmpPrgExt[$indexExt]["progresRelatedToType"] = 1;
										} else {
											$tmpPrgExt[$indexExt]["colorRelatedToType"]	= "amber";
										}
										
										
										
										if ($categoies=="all") {
										//	$lectureAggregation[$lectureID]["time"] += $tmpPrgExt[$indexExt]["ci_duration"];
											$tot["time"]["RA"]			+= $tmpPrgExt[$indexExt]["ci_duration"];	
										}
										$indexExt++;	
									}
								}			
						}									
				//*******************				
				$GridSessionProp["data"] = $tmpPrgExt;
				$GridSessionProp["AllRecs"] = count($GridSessionProp["data"]);
				WebApp::addVar("UserLectureProgressExtToGrid_$lectureID", $GridSessionProp);	
			}
		}
		WebApp::addVar("UserLectureProgressToGrid", $GridSessionProp);	

		if (count($tot["time"])>0) {
			$totalTime = 0;
			while (list($ciType,$duration)=each($tot["time"])) {
				if ($ciType!="RA" && $ciType!="CT")
				$totalTime +=$duration;
				$prgDtCnf["time"][$ciType] = $this->roundSecToHours($duration);
			}
			$prgDtCnf["time"]["all"] = $this->roundSecToHours($totalTime);
		}
		$tot["lectures"]["remaining"] 			= $tot["lectures"]["tot"]-($tot["lectures"]["failed"]+$tot["lectures"]["passed"]+$tot["lectures"]["progress"]) ;
		$tot["cme_credits"]["remaining"]		= $tot["cme_credits"]["tot"]-$tot["cme_credits"]["earned"] ;
		$prgDtCnf["lectures"] 	= $tot["lectures"];
		$prgDtCnf["cme_credits"] = $tot["cme_credits"];

		$this->lectureAggregation["lectureAggregation"]	= $lectureAggregation;

		/*echo "<textarea>\n";
		print_r($lectureAggregation);
		print_r($this->CiAnalytics);
		print_r($lecturesPathRelated);
		print_r($det);
		print_r($this->nrItems);
		echo "</textarea><br>\n";*/	
	
		$this->PAGELOAD_PARTIAL($starts,"inside userOverallProgressModuleLevel END->");

	/*	echo "\nuserOverallProgressModuleLevel\n<textarea>\n";
		print_r($lectureAggregation);
		print_r($this->lectureAggregation);
		print_r($this->lectureDetails);
		echo "</textarea>\n";*/
		
		/*if ($session->Vars["uni"]=="20161205194803192168120254552065" || $session->Vars["uni"]=="20161206100501192168120632005657" ) {
			echo "userOverallProgressModuleLevel<textarea>";
			
			
			
		if (isset($this->useridp) && $this->useridp>0) $userId=$this->useridp;
		else										   $userId=$session->Vars["ses_userid"];

		if (isset($this->tipp) && $this->useridp>0) $tipp=$this->tipp;
		else										$tipp=$session->Vars["tip"];
			
			
			//print_r($referenceIDsCOF);
			//print_r($distinctsIdsInCollector);
			//print_r($cmeCredits);
			
			print_r($userId."-userId- \n ");
			print_r($tipp."-tipp- \n ");
			//print_r($this->getRAcategory);
			
			
			
			
			//print_r($this->getRAcategory);
			//print_r($this->CiAnalytics);
			//print_r($debugTestInfo);
			print_r($prgDtCnf);
			echo "</textarea>";	
        }*/


		return $prgDtCnf;
	}   

	function eLearningAuthoring($conf) {
		global $session;
		
		$data = $this->initReferenceByStateOrConf($conf["allow_override"], $conf["programe_reference_ids"]);
		require_once(INC_PHP_AJAX."authoring.completeness.class.php");
		$idGrid = $this->NEM_PROP["att_nemid"]."_".$this->NEM_PROP["att_objid"];	
		
		
		if (isset($data["referenceIDsCOF"]) && count($data["referenceIDsCOF"])>0) {
			while (list($cis,$nodeKey)=each($data["referenceIDsCOF"])) {
    			$authoringObj = new AuthoringCompleteness();
    			$authoringObj->initCiReference($cis);	
    			$authoringObj->AuthoringProgressInFront($idGrid);	
			}
		}
		WebApp::addVar("authoringProgressType", $data["actualFilterStatus"]); 	
		WebApp::addVar("AuthoringProgressID", $idGrid); 
			
		$tmpG = array("data" => array(), "AllRecs" => "0");$ind=0;
		$tmpG["data"][0]["type"] = $data["actualFilterStatus"];
		$tmpG["AllRecs"] = count($tmpG["data"]);
		WebApp::addVar("AuthoringProgress_".$idGrid, $tmpG); 
	}	
	function eLearningTypeActualStatus($conf=array()) {
		global $session;

		//$starts = WebApp::get_formatted_microtime();

		$data = $this->initReferenceByStateOrConf($conf["allow_override"], $conf["programe_reference_ids"]);
		WebApp::addVar("userResultType", $data["actualFilterStatus"]); 	
		
		$idGrid = $this->NEM_PROP["att_nemid"]."_".$this->NEM_PROP["att_objid"];	
		
		if ($data["actualFilterStatus"]=="EL") {
			
			$this->userRelatedResult();
			
		} else {
			
			//**** LECTURE CONFIGURATION*************************************************************
			$lectureConf = array();$tmp=array();
			$lectureConf["el_cme_display"] 	= "yes";
			$lectureConf["el_cme_display"] 	= "no";
			$lectureConf["el_cme_title"] 	= "CME Credits";
			$lectureConf["el_cme_label"] 	= "CME Credits: {{ec_cme_earned}}";

			$lectureConf["el_quiz_display"] 	= "yes";
			$lectureConf["el_quiz_label"] 		= "Points taken: {{el_quiz_earned}} from a total of {{el_quiz_total}}";
		
			$lectureConf["el_test_display"] 	= "yes";
			$lectureConf["el_test_label"] 		= "Points taken: {{el_test_earned}} from a total of {{el_test_total}}";
			
			$lectureConf["el_test_link_display"] 	= "yes";
			$lectureConf["el_test_title"] 			= "Lecture Assessment";			
			$lectureConf["el_test_link_display"] 	= "yes";
			$lectureConf["el_test_link_details"] 	= "View details";
			$lectureConf["el_test_certificate_display"] 	= "yes";
			$lectureConf["el_test_certificate_title"] 	= "Lecture Certificate of Achievement";
			
			$lectureConf["el_quiz_link_display"] 	= "yes";
			$lectureConf["el_quiz_title"] 			= "Pre-learning Quiz";			
			$lectureConf["el_quiz_link_display"] 	= "yes";
			$lectureConf["el_quiz_link_details"] 	= "View details";			
			
			$tmp["data"][0] = $lectureConf;
			$tmp["AllRecs"] = count($tmp["data"]);
			WebApp::addVar("ExtendedItemsResultsConf_EL_".$idGrid, $tmp); 	
			$this->eLearningTypeActualConf["EL"] = $lectureConf;
			//*****************************************************************		
			
			$ECconf = array();$tmp=array();

			$ECconf["ec_el_nr_display"] = "yes";
			$ECconf["ec_el_nr_title"] = "Lectures Completed";
			$ECconf["ec_el_nr_label"] = "Lectures Completed: {{ec_el_nr_completed}} from {{ec_el_nr_total}} available";

			$ECconf["ec_cme_display"] 	= "yes";
			$ECconf["ec_cme_display"] 	= "no";
			$ECconf["ec_cme_title"] 	= "CME Credits Earned";
			$ECconf["ec_cme_label"] 	= "CME Credits Earned: {{ec_cme_earned}} from {{ec_cme_total}} available";

			$ECconf["ec_test_display"] 			= "yes";
			$ECconf["ec_test_link_display"] 	= "yes";
			$ECconf["ec_test_link_label"] 		= "View details";
			$ECconf["ec_test_title"]	= "Module Assessment";
			$ECconf["ec_test_label"]	= "Points taken: {{ec_test_earned}} from a total of {{ec_test_total}}";

			$ECconf["ec_test_certificate_display"]	= "yes";
			$ECconf["ec_test_certificate_title"]	= "Module Certificate of Achievement";

			$ECconf["ec_tutorial_display"] 		= "yes";
			$ECconf["ec_tutorial_link_display"] 	= "yes";

			$ECconf["ec_tutorial_link_label"] 	= "View event details";
			$ECconf["ec_tutorial_title"]		= "Tutorial Event Participation";
			$ECconf["ec_tutorial_tc_title"] 	= "Module 2: Degenerative disorders of the cervical spine, Rome, 15 Janar 2016";

			$ECconf["ec_tutorial_certificate_display"] 	= "yes";
			$ECconf["ec_tutorial_certificate"] 			= "Tutorial Certificate";
			$ECconf["ec_tutorial_certificate_missing"] 	= "Not available";

			$ECconf["ec_details_display"] 				= "yes";
			$ECconf["ec_details_title"] 					= "Module Lectures Results";			

			$tmp["data"][0] = $ECconf;
			$tmp["AllRecs"] = count($tmp["data"]);
			WebApp::addVar("ExtendedItemsResultsConf_EC_".$idGrid, $tmp); 		
			
			$this->eLearningTypeActualConf["EC"] = $ECconf;
			
			//*****************************************************************		
				$prConf["pr_ec_nr_display"] = "yes";
				$prConf["pr_ec_nr_title"] = "Modules Completed";
				$prConf["pr_ec_nr_label"] = "Modules Completed: {{pr_ec_nr_completed}} from {{pr_ec_nr_total}} available";

				$prConf["pr_cme_display"] 	= "yes";
				$prConf["pr_cme_display"] 	= "no";
				$prConf["pr_cme_title"] 	= "CME Credits Earned";
				$prConf["pr_cme_label"] 	= "CME Credits Earned: {{pr_cme_earned}} from {{pr_cme_total}} available";

				
				$prConf["pr_test_display"] 			= "yes";
				$prConf["pr_test_link_display"] 	= "yes";
				$prConf["pr_test_link_label"] 		= "View details";
				$prConf["pr_test_title"]		 	= "Program Examination";
				$prConf["pr_test_label"] 			= "Points taken: {{pr_test_earned}} from a total of {{pr_test_total}}";
		
				$prConf["pr_diploma_display"] 	= "yes";
				$prConf["pr_diploma_title"] 	= "Program Diploma";
				$prConf["pr_diploma_label_missing"]	= "Not available";
				$prConf["pr_diploma_link_label"] 	= "View Certificate";
				
				$prConf["pr_details_display"] 	= "yes";
				$prConf["pr_details_title"] 	= "Program Details";	
				
			$tmp["data"][0] = $prConf;
			$tmp["AllRecs"] = count($tmp["data"]);
			WebApp::addVar("ExtendedItemsResultsConf_PR_".$idGrid, $tmp); 		
			
			$this->eLearningTypeActualConf["PR"] = $ECconf;				
				
			//*****************************************************************		
		}
		if (!isset($this->partecipationInfo)) {
			$this->getPartecipationInfo();
		}		
		if ($data["actualFilterStatus"]=="PR") {
			
			
			$this->userRelatedResultToPR($data["referenceIDsCOF"],$idGrid);
			
			//$this->userRelatedResultToSurgicalVideos($data["referenceIDsCOF"],$idGrid);
			
		} elseif ($data["actualFilterStatus"]=="EC") {
			$this->userRelatedResultToEC($data["referenceIDsCOF"],$idGrid);
		}
			
		$tmpG = array("data" => array(), "AllRecs" => "0");$ind=0;
		$tmpG["data"][0]["type"] = $data["actualFilterStatus"];
		$tmpG["AllRecs"] = count($tmpG["data"]);
		WebApp::addVar("ExtendedResults_".$idGrid, $tmpG); 
		WebApp::addVar("ExtendedResultsID", $idGrid); 		

		//$totals = WebApp::get_formatted_microtime() - $starts;
		$debugTime .= "2->".round($totals, 2)." sec | ".round(memory_get_usage(true)/1024,2)." kb
		";
		//echo "eLearningResultsTime:$debugTime";		
	}	
	function userRelatedResultToPR ($referenceIDsCOF,$idGrid) {
		global $session;
			
			$prConf = array();
			$prIds = array();
			
			$prConf["pr_ec_nr_total"] 		= "0";
			$prConf["pr_ec_nr_completed"] 	= "0";
			$prConf["pr_ec_nr_precent"] 	= "0";

			$prConf["pr_cme_total"] 	= "0";
			$prConf["pr_cme_earned"] 	= "0";
			$prConf["pr_cme_percent"] 	= "0";		

			$prConf["pr_test_date"]		= "";
			$prConf["pr_test_total"] 	= "0";
			$prConf["pr_test_earned"] 	= "0";
			$prConf["pr_test_percent"] 	= "0";				

			$tmpG = array("data" => array(), "AllRecs" => "0");$ind=0;
			reset($referenceIDsCOF);
			WHILE (list($programID,$dt)=each($referenceIDsCOF)) {
				$prIds[$programID] = $programID;
			}
			if (count($prIds)>0) {
			} else {
				return;
			}
			
			$prIdsStr = implode(",",$prIds);
			$cmeCredits	= $this->getCmeCredits($prIdsStr);
			
			$fieldToSelect = "";
			$orderBySql = "ORDER BY firstPArt*1 ASC, secondPArt*1 ASC, ordF ASC, ecc_reference";
			$orderBySqlPr = "ORDER BY nivel_0_family.orderToDisplay, nivel_4.orderMenu ASC,  firstPArt*1 ASC, secondPArt*1 ASC, ordF ASC, ecc_reference";
			$cnt_data = "	
					SELECT DISTINCT c.content_id as content_id,		 
							
							IF((c.imageSm_id >0),c.imageSm_id,'') 			as imageSm_id,
							IF((c.imageBg_id >0),c.imageBg_id,'') 			as imageBg_id,						
							
							coalesce(ecc_reference*1, orderContent) as ordF, ecc_reference,
							SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 1), '_', -1) as firstPArt,
							SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 2), '_', -1) as secondPArt

					  FROM content as c 
					  JOIN nivel_4 
						ON nivel_4.id_zeroNivel	= c.id_zeroNivel
					   AND nivel_4.id_firstNivel	= c.id_firstNivel
					   AND nivel_4.id_secondNivel	= c.id_secondNivel
					   AND nivel_4.id_thirdNivel	= c.id_thirdNivel
					   AND nivel_4.id_fourthNivel = c.id_fourthNivel

					   JOIN nivel_0_family 

						 ON nivel_0_family.node_family_id =  nivel_4.node_family_id
						AND nivel_0_family.id_zeroNivel = nivel_4.id_zeroNivel

			   LEFT JOIN ci_elearning_extended on c.content_id = ci_elearning_extended.content_id 
					 AND ci_elearning_extended.lng_id = '".$this->lngId."' AND ci_elearning_extended.statusInfo = '".$this->thisModeCode."'
				   WHERE c.content_id in (".$prIdsStr.") ".$orderBySqlPr;		

			$rs = WebApp::execQuery($cnt_data);	
			while (!$rs->EOF())  {
				$programIDC		= $rs->Field("content_id");
				$programIdKey = $this->POTS["PR_coord"][$programIDC];
				

				//$totals = array();
				$totals["cmeCreditEarned"] 	= 0;
				$totals["cmeCreditTotal"] 	= 0;
				$totals["completed"] 		= 0;
				$totals["totals"] 			= 0;
				$totals["runinng"] 			= 0;


				$StructuredInformationEcc = $this->getPartialStructuredInformationEcc("modules_level",$referenceIDsCOF,"results",$programIdKey);
				if (isset($StructuredInformationEcc["MS"])) {
					$idToGetResults = implode (",",$StructuredInformationEcc["MS"]);
					$dataReturnedMS = $this->userTestResultMulti($idToGetResults);	
				}				
				
				if (isset($this->POTS["ECInPR"][$programIdKey])) {
					$tmp = $this->POTS["ECInPR"][$programIdKey];
					$totalsFromEC = $this->userRelatedResultToEC ($tmp,$programIdKey,$programIdKey);
				}	

				$totals["totals"]++;	
				if (isset($cmeCredits[$moduleIdS])) {
					$totals["cmeCreditTotal"] += $cmeCredits[$moduleIdS];
				}		
					$flagToIncludeModule="no";	
					
					if ($totalsFromEC["totals"]>0) {
						if (isset($cmeCredits[$moduleIdS])) {
							$totals["cmeCreditTotal"] += $totalsFromEC["cmeCreditTotal"];
						}		
						$totalsCmeCredit = $totalsFromEC["cmeCreditTotal"];
					}
					//echo $totalsCmeCredit.":totalsCmeCredit:$programIDC:$programIdKey<br>";
					if ($totalsFromEC["totals"]>0) { // && $totalsFromEC["runinng"]>0

						$tmpG["data"][$ind] = $prConf;
						$tmpG["data"][$ind]["programIdKey"] = $programIdKey;	

						$tmpG["data"][$ind]["pr_ec_nr_total"]		= "".$totalsFromEC["totals"];
						$tmpG["data"][$ind]["pr_ec_nr_completed"]	= "".$totalsFromEC["completed"];
						if ($totalsFromEC["totals"]>0) {
							$tmpG["data"][$ind]["pr_ec_nr_precent"] 	= "".(($totalsFromEC["completed"]/$totalsFromEC["totals"])*100);
						}

						$tmpG["data"][$ind]["pr_cme_total"]	= "".$totalsCmeCredit;
						$tmpG["data"][$ind]["pr_cme_earned"]	= "".$totalsFromEC["cmeCreditEarned"];
						
						if ($totalsCmeCredit>0) {

							$tmpG["data"][$ind]["pr_cme_percent"]	= "".(round(($totalsFromEC["cmeCreditEarned"]/$totalsCmeCredit)*100,0));
						}

						$totals["cmeCreditEarned"] 	+= $totalsFromEC["cmeCreditTotal"];
						$totals["cmeCreditTotal"] 	+= $totalsFromEC["cmeCreditTotal"];
						
						if ($totalsFromEC["completed"]==$totalsFromEC["totals"])
							$totals["completed"]++;
						$totals["runinng"]++;
						
						$flagToIncludeModule="yes";	
					} 
					
					if ($flagToIncludeModule=="yes") {

						$tmpG["data"][$ind]["pr_test_certificate_display"] 	= "no";	
						$tmpG["data"][$ind]["pr_test_display"] 				= "no";	
						
						if (isset($StructuredInformationEcc["MS"][$moduleIdKey]) && $this->eLearningTypeActualConf["EC"]["pr_test_display"] == "yes") {

							if (isset($dataReturnedMS[$StructuredInformationEcc["MS"][$moduleIdKey]])
								) { //&& $dataReturnedES[$StructuredInformationEcc["MS"][$moduleIdKey]]["passed"]>0
								$dataRes = $this->userBestResults($StructuredInformationEcc["MS"][$moduleIdKey]);

								if (isset($dataRes["total_user_points"])) {
									
									$tmpG["data"][$ind]["pr_test_display"] 				= "yes";	
									
									$tmpG["data"][$ind]["pr_test_date"] 	= "".$dataRes["date_of_test"];
									$tmpG["data"][$ind]["pr_test_total"] 	= "".$dataRes["total_points"];
									$tmpG["data"][$ind]["pr_test_earned"] 	= "".$dataRes["total_user_points"];
									$tmpG["data"][$ind]["pr_test_percent"] 	= "".$dataRes["user_points_perqindje"];	
									$tmpG["data"][$ind]["pr_test_color_results"] 	= "".$dataRes["trafficLightCase"];	

									if ($this->eLearningTypeActualConf["EC"]["pr_test_certificate_display"] == "yes" && $dataRes["CertificateRelatedTag"]!="") {
										$tmpG["data"][$ind]["pr_test_certificate_tag"] 	= "".$dataRes["CertificateRelatedTag"];
										$tmpG["data"][$ind]["pr_test_certificate_display"] 	= "yes";

										if (isset($cmeCredits[$moduleIdS])) {
											$totals["cmeCreditEarned"] += $cmeCredits[$moduleIdS];
										}										
									} 						
								}	
							}
						}	
						//pjesa poshte ishte ketu
					}
					
					$imageSm_id		= $rs->Field("imageSm_id");
					$imageBg_id		= $rs->Field("imageBg_id");
					$imgID = "";
					if ($imageSm_id!="" && $imageSm_id>"0") {
						$imgID = $imageSm_id;
					} elseif ($imageBg_id!="" && $imageBg_id>"0") {
						$imgID 		= $imageBg_id;
					}				

					if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") {
						CiManagerFe::get_SL_CACHE_INDEX($imgID, "","");		
					}						

					$tmpG["data"][$ind]["TT"] = $this->POTS["ci"][$programIDC]["TT"];
					$tmpG["data"][$ind]["ND"] = $this->POTS["ci"][$programIDC]["ND"];
					$tmpG["data"][$ind]["type_of_programe"] = $this->POTS["ci"][$programIDC]["type_of_programe"];
					$tmpG["data"][$ind]["ecc_reference"] = $rs->Field("ecc_reference");
					$tmpG["data"][$ind]["CID"] = $rs->Field("content_id");
					$tmpG["data"][$ind]["imgID"] = $imgID;

					$ind++;						
					
				$rs->MoveNext();
			}
			$tmpG["AllRecs"] = count($tmpG["data"]);
			WebApp::addVar("ExtendedItemsResults_PR_".$idGrid, $tmpG); 
	}	
	function userRelatedResultToEC ($referenceIDsCOF,$idGrid,$nodeIdKey="") {
		global $session;
			

		/*echo "$idGrid,$nodeIdKey-userRelatedResultToEC<textarea>";
        print_r($referenceIDsCOF);
        echo "</textarea>";		*/




			if ($nodeIdKey=="") {
				$referenceIDsCOF = array_flip($referenceIDsCOF);
				//normalisht ky duhet te thirret vetem per nje module ne kohe
				$bar = each($referenceIDsCOF);
				$nodeIdKey = $bar["key"];
			}
			$StructuredInformationEcc = $this->getPartialStructuredInformationEcc("modules_level",$referenceIDsCOF,"results",$nodeIdKey);
			if (isset($StructuredInformationEcc["MS"])) {
				$idToGetResults = implode (",",$StructuredInformationEcc["MS"]);
				$dataReturnedMS = $this->userTestResultMulti($idToGetResults);	
			}

			$tmpG = array("data" => array(), "AllRecs" => "0");$ind=0;
			$orderBySql = "ORDER BY firstPArt*1 ASC, secondPArt*1 ASC, ordF ASC, ecc_reference";

			//$totals = array();
			$totals["cmeCreditEarned"] 	= 0;
			$totals["cmeCreditTotal"] 	= 0;
			$totals["runinng"] 		= 0;
			$totals["completed"] 		= 0;
			$totals["totals"] 			= 0;
			
			$ECconf = array();
			
			$ECconf["ec_el_nr_total"]		= "0";
			$ECconf["ec_el_nr_completed"]	= "0";
			$ECconf["ec_el_nr_precent"] 	= "0";			
			
			$ECconf["ec_cme_total"] 		= "0";
			$ECconf["ec_cme_earned"] 		= "0";
			$ECconf["ec_cme_percent"] 	= "0";
			
			$ECconf["ec_test_date"]		= "";
			$ECconf["ec_test_total"] 		= "";
			$ECconf["ec_test_earned"] 	= "";
			$ECconf["ec_test_percent"] 	= "";		
			
			if (count($referenceIDsCOF)>0) {
				$EcIdsStr = implode(",",$referenceIDsCOF);
				$cmeCredits	= $this->getCmeCredits($EcIdsStr);
				$mdData = "	
						SELECT DISTINCT c.content_id as content_id,
								
								IF((c.imageSm_id >0),c.imageSm_id,'') 			as imageSm_id,
								IF((c.imageBg_id >0),c.imageBg_id,'') 			as imageBg_id,						
								
								coalesce(ecc_reference*1, orderContent) as ordF, ecc_reference,
								SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 1), '_', -1) as firstPArt,
								SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 2), '_', -1) as secondPArt

						  FROM content as c 

				   LEFT JOIN ci_elearning_extended on c.content_id = ci_elearning_extended.content_id 
						 AND ci_elearning_extended.lng_id = '".$this->lngId."' AND ci_elearning_extended.statusInfo = '".$this->thisModeCode."'
					   WHERE c.content_id in (".$EcIdsStr.") ".$orderBySql;		

				$rs= WebApp::execQuery($mdData);
				while (!$rs->EOF())  {
					
					$moduleIdS		= $rs->Field("content_id");
					$moduleIdKey = $this->POTS["EC_coord"][$moduleIdS];
					$totalsFromEl = array();
					if (isset($this->POTS["ELInEC"][$moduleIdKey])) {
						$tmp = $this->POTS["ELInEC"][$moduleIdKey];
						$totalsFromEl = $this->userRelatedResultToEL($tmp,$moduleIdKey,$moduleIdKey);
					}
				
					$totals["totals"]++;	
					/*if (isset($cmeCredits[$moduleIdS])) {
						$totals["cmeCreditTotal"] += $cmeCredits[$moduleIdS];
					}*/				
					
					//if ($totalsFromEl["totals"]>0) {
					if ($totalsFromEl["userTotals"]>0) {
						$totals["cmeCreditTotal"] 	+= $totalsFromEl["cmeCreditTotal"];
					}
					$flagToIncludeModule="no";	
					
					if ($totalsFromEl["userTotals"]>0) { // && $totalsFromEl["completed"]>0
						
						$tmpG["data"][$ind]["ec_el_nr_total"]		= "0";
						$tmpG["data"][$ind]["ec_el_nr_completed"]	= "0";
						$tmpG["data"][$ind]["ec_el_nr_precent"] 	= "0";	

						$tmpG["data"][$ind]["ec_cme_total"]		= "0";
						$tmpG["data"][$ind]["ec_cme_earned"]	= "0";
						$tmpG["data"][$ind]["ec_cme_percent"] 	= "0";		

						$tmpG["data"][$ind]["ec_test_certificate_display"] 	= "no";	
						$tmpG["data"][$ind]["ec_tutorial_display"] 	= "no";	

						$tmpG["data"][$ind]["ec_cme_display"] 	= "no";	
						$tmpG["data"][$ind]["ec_test_display"] 	= "no";							
						
						$tmpG["data"][$ind] = $ECconf;
						$tmpG["data"][$ind]["moduleIdKey"] = $moduleIdKey;	
						
						if (isset($totalsFromEl["totals"]) && $totalsFromEl["totals"]>0) {
							$tmpG["data"][$ind]["ec_el_nr_total"]		= "".$totalsFromEl["userTotals"];
							$tmpG["data"][$ind]["ec_el_nr_completed"]	= "".$totalsFromEl["completed"];
							$tmpG["data"][$ind]["ec_el_nr_precent"] 	= "".(($totalsFromEl["completed"]/$totalsFromEl["userTotals"])*100);	

							$tmpG["data"][$ind]["ec_cme_total"]		= "".$totalsFromEl["cmeCreditTotal"];
							$tmpG["data"][$ind]["ec_cme_earned"]	= "".$totalsFromEl["cmeCreditEarned"];
							if (isset($totalsFromEl["cmeCreditTotal"]) && $totalsFromEl["cmeCreditTotal"]>0)
							$tmpG["data"][$ind]["ec_cme_percent"]	= "".(round(($totalsFromEl["cmeCreditEarned"]/$totalsFromEl["cmeCreditTotal"])*100,0));	
						} 
						
						$totals["cmeCreditEarned"] 	+= $totalsFromEl["cmeCreditEarned"];
						
						$totals["runinng"]++;
						if ($totalsFromEl["completed"]==$totalsFromEl["totals"])
							$totals["completed"]++;
							
						$flagToIncludeModule="yes";	
					}

					if ($flagToIncludeModule=="yes") {
			
						$tmpG["data"][$ind]["ec_test_certificate_display"] 	= "no";	
						$tmpG["data"][$ind]["ec_test_display"] 	= "no";	
						if (isset($StructuredInformationEcc["MS"][$moduleIdKey]) && $this->eLearningTypeActualConf["EC"]["ec_test_display"] == "yes") {
							
							if (isset($dataReturnedMS[$StructuredInformationEcc["MS"][$moduleIdKey]])
								) { //&& $dataReturnedES[$StructuredInformationEcc["MS"][$moduleIdKey]]["passed"]>0
								$dataRes = $this->userBestResults($StructuredInformationEcc["MS"][$moduleIdKey]);

								if (isset($dataRes["total_user_points"])) {
									$tmpG["data"][$ind]["ec_test_date"] 	= "".$dataRes["date_of_test"];
									$tmpG["data"][$ind]["ec_test_total"] 	= "".$dataRes["total_points"];
									$tmpG["data"][$ind]["ec_test_earned"] 	= "".$dataRes["total_user_points"];
									$tmpG["data"][$ind]["ec_test_percent"] 	= "".$dataRes["user_points_perqindje"];	
									$tmpG["data"][$ind]["ec_test_color_results"] 	= "".$dataRes["trafficLightCase"];	
									
									$tmpG["data"][$ind]["ec_test_display"] 	= "yes";	

									if ($this->eLearningTypeActualConf["EC"]["ec_test_certificate_display"] == "yes" && $dataRes["CertificateRelatedTag"]!="") {
										$tmpG["data"][$ind]["ec_test_certificate_tag"] 	= "".$dataRes["CertificateRelatedTag"];
										$tmpG["data"][$ind]["ec_test_certificate_display"] 	= "yes";
										
										/*if (isset($cmeCredits[$moduleIdS])) {
											$totals["cmeCreditEarned"] += $cmeCredits[$moduleIdS];
										}*/										
									} 						
								}	
							}
						}	
						
						/*if ($this->eLearningTypeActualConf["EC"]["ec_test_display"] == "yes" && $tmpG["data"][$ind]["ec_test_display"] 	== "no") {
						
						}*/
				
						$tmpG["data"][$ind]["ec_tutorial_display"] 	= "no";	
						if ($this->eLearningTypeActualConf["EC"]["ec_tutorial_display"] == "yes") {
							if (isset($this->POTS["TCInEC"][$moduleIdKey])) {
								
								reset($this->POTS["TCInEC"][$moduleIdKey]);
								while (list($tutId, $ddd)=each($this->POTS["TCInEC"][$moduleIdKey])) {
									if (isset($this->POTS["TC_coord"][$tutId])) {
										
										$filterByNodesTeEvents = array();
										//$filterByNodesTeEvents[$moduleKey] = $this->POTS["TC_coord"][$tutId];
										$filterByNodesTeEvents[$moduleKey] = $tutId;
											 //findEventsExtendedInformation($filterByNodes,$filterByTime="",$gridKey="",$orderBy="",$sortBy="",$ci_target="",$isMeine="no",$my_ci_target="",$limit="") {	
										$eventsTutorialInfo = $this->findEventsExtendedInformation($filterByNodesTeEvents,"",		"",			"event_date_near",			"DESC",		"",			"",				"",1,$moduleIdKey,$tutId);
										if (isset($eventsTutorialInfo)) {
											$bar = each($eventsTutorialInfo);
											$tutorial_title = $bar["value"]["titleToDisplay"];
											
											$tmpG["data"][$ind]["ec_tutorial_tc_cid"] = $bar["value"]["CID"];
											$tmpG["data"][$ind]["ec_tutorial_tc_title"] = $tutorial_title;
											$tmpG["data"][$ind]["ec_tutorial_display"] 	= "yes";	
										}
									}
								}
							}
						}
						

						
						
						//pjesa qe ndertonte griden ishte ketu, nese duhet te raportohen dhe modulet e tjera kjo duhet te kaloje me poshte
						
						$imageSm_id		= $rs->Field("imageSm_id");
						$imageBg_id		= $rs->Field("imageBg_id");
						$imgID = "";
						if ($imageSm_id!="" && $imageSm_id>"0") {
							$imgID = $imageSm_id;
						} elseif ($imageBg_id!="" && $imageBg_id>"0") {
							$imgID 		= $imageBg_id;
						}				

						if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") {
							CiManagerFe::get_SL_CACHE_INDEX($imgID, "","");		
						}						

						$tmpG["data"][$ind]["TT"] = $this->POTS["ci"][$moduleIdS]["TT"];
						$tmpG["data"][$ind]["ND"] = $this->POTS["ci"][$moduleIdS]["ND"];
						$tmpG["data"][$ind]["ecc_reference"] = $rs->Field("ecc_reference");
						$tmpG["data"][$ind]["CID"] = $rs->Field("content_id");
						$tmpG["data"][$ind]["imgID"] = $imgID;

						$ind++;							
					} 
					$rs->MoveNext();
				}
			}		
			
			$tmpG["AllRecs"] = count($tmpG["data"]);
			WebApp::addVar("ExtendedItemsResults_EC_".$idGrid, $tmpG); 		
		return $totals;
	}		
	function userRelatedResultToEL ($referenceIDsCOF,$idGrid,$moduleIdKey="") {
		global $session;

			//$totals = array();
			$totals["cmeCreditEarned"] = 0;
			$totals["cmeCreditTotal"] = 0;
			$totals["runinng"] 		= 0;
			$totals["completed"] 	= 0;
			$totals["totals"] 		= 0;
			$totals["userTotals"] 	= 0;
			
			$StructuredInformationEcc = $this->getPartialStructuredInformationEcc("lectures_level",$referenceIDsCOF,"results",$moduleIdKey);
			if (isset($StructuredInformationEcc["ES"])) {
				$idToGetResults = implode (",",$StructuredInformationEcc["ES"]);
				$dataReturnedES = $this->userTestResultMulti($idToGetResults);	
			}
			
			$tempConf = array();
			$tempConf["el_cme_earned"] 	= "0";

			$tempConf["el_quiz_total"] 		= "0";
			$tempConf["el_quiz_earned"] 	= "0";
			$tempConf["el_quiz_percent"] 	= "0";
			$tempConf["el_quiz_date"] 		= "";
			$tempConf["el_quiz_cid"] 		= "";
			$tempConf["el_quiz_color_results"] 		= "red";

			$tempConf["el_test_total"] 		= "0";
			$tempConf["el_test_earned"] 	= "0";
			$tempConf["el_test_percent"] 	= "0";
			$tempConf["el_test_date"] 		= "";
			$tempConf["el_test_cid"] 		= "";
			$tempConf["el_test_color_results"] 		= "red";
			$tempConf["el_test_certificate_tag"] 	= "";
			
			$tmpG = array("data" => array(), "AllRecs" => "0");$ind=0;
			
			if (count($referenceIDsCOF)>0) {
				$EcIdsStr 	= implode(",",$referenceIDsCOF);
				$cmeCredits	= $this->getCmeCredits($EcIdsStr);
				$mdData = "	
						SELECT DISTINCT c.content_id as content_id,
								
								IF((c.imageSm_id >0),c.imageSm_id,'') 			as imageSm_id,
								IF((c.imageBg_id >0),c.imageBg_id,'') 			as imageBg_id,						
								
								coalesce(ecc_reference*1, orderContent) as ordF, ecc_reference,
								SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 1), '_', -1) as firstPArt,
								SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 2), '_', -1) as secondPArt

						  FROM content as c 

				   LEFT JOIN ci_elearning_extended on c.content_id = ci_elearning_extended.content_id 
						 AND ci_elearning_extended.lng_id = '".$this->lngId."' AND ci_elearning_extended.statusInfo = '".$this->thisModeCode."'
					   WHERE c.content_id in (".$EcIdsStr.") ".$orderBySql;		

				$rs= WebApp::execQuery($mdData);
				while (!$rs->EOF())  {
					
					$lectureIdS		= $rs->Field("content_id");
					$lectureIdKey = $this->POTS["EL_coord"][$lectureIdS];
					$totals["totals"]++;
					
					if (isset($this->partecipationInfo["$lectureIdKey"])) {
					
						$totals["userTotals"]++;
						if (isset($cmeCredits[$lectureIdS])) {
							$totals["cmeCreditTotal"] += $cmeCredits[$lectureIdS];
						}					
						
						$examinationFlag = "no";
						$examinationFlag = "yes";
						
						if (isset($StructuredInformationEcc["ES"][$lectureIdKey])) {
							if (isset($dataReturnedES[$StructuredInformationEcc["ES"][$lectureIdKey]]["passed"])
								&& $dataReturnedES[$StructuredInformationEcc["ES"][$lectureIdKey]]["passed"]>0) {
										$dataRes = $this->userBestResults($StructuredInformationEcc["ES"][$lectureIdKey]);
										$examinationFlag = "yes";
										$totals["completed"] += 1;
							}
						}
						//echo $examinationFlag.":examinationFlag|$lectureIdKey:lectureIdKey<br>";
						
						if ($examinationFlag == "yes") {

							$tmpG["data"][$ind] = $tempConf;
							
							$tmpG["data"][$ind]["el_test_display_results"] 	= "no";

							$tmpG["data"][$ind]["el_test_cid"] 	= "".$StructuredInformationEcc["ES"][$lectureIdKey];
							$tmpG["data"][$ind]["el_test_date"] 	= "".$dataRes["date_of_test"];
							$tmpG["data"][$ind]["el_test_total"] 	= "".$dataRes["total_points"];
							$tmpG["data"][$ind]["el_test_earned"] 	= "".$dataRes["total_user_points"];
							$tmpG["data"][$ind]["el_test_percent"] 	= "".$dataRes["user_points_perqindje"];
							$tmpG["data"][$ind]["el_test_color_results"] 	= "".$dataRes["trafficLightCase"];	
							
							if (isset($tmpG["data"][$ind]["el_test_total"]) && $tmpG["data"][$ind]["el_test_total"]>0) {
							
									$tmpG["data"][$ind]["el_test_display_results"] 	= "yes";
									
							} else {
								$tmpG["data"][$ind]["el_test_date"] 	= "";
								$tmpG["data"][$ind]["el_test_total"] 	= "0";
								$tmpG["data"][$ind]["el_test_earned"] 	= "0";
								$tmpG["data"][$ind]["el_test_percent"] 	= "0";
								$tmpG["data"][$ind]["el_test_color_results"] 	= "";								

								$tmpG["data"][$ind]["el_test_display_results"] 	= "no";
							}

							
							//el_test_display_results
							
							if ($this->eLearningTypeActualConf["EL"]["el_test_certificate_display"] == "yes" && $dataRes["CertificateRelatedTag"]!="") {
								$tmpG["data"][$ind]["el_test_certificate_tag"] 	= "".$dataRes["CertificateRelatedTag"];

								if (isset($cmeCredits[$lectureIdS])) {
									$tmpG["data"][$ind]["el_cme_earned"] 	= "".$cmeCredits[$lectureIdS];
									$totals["cmeCreditEarned"] += $cmeCredits[$lectureIdS];
								}							

							} else {
								$tmpG["data"][$ind]["el_test_certificate_display"] 	= "no";
								
							}
							
							if ($this->eLearningTypeActualConf["EL"]["el_quiz_display"] = "yes" && isset($StructuredInformationEcc["CQ"][$lectureIdKey])) {
								$dataRes = $this->userBestResults($StructuredInformationEcc["CQ"][$lectureIdKey]);
								$tmpG["data"][$ind]["el_quiz_cid"] 	= "".$StructuredInformationEcc["CQ"][$lectureIdKey];

								if (isset($dataRes["total_user_points"])) {

									$tmpG["data"][$ind]["el_quiz_date"] 	= "".$dataRes["date_of_test"];
									$tmpG["data"][$ind]["el_quiz_total"] 	= "".$dataRes["total_points"];
									$tmpG["data"][$ind]["el_quiz_earned"] 	= "".$dataRes["total_user_points"];
									$tmpG["data"][$ind]["el_quiz_percent"] 	= "".$dataRes["user_points_perqindje"];	
									$tmpG["data"][$ind]["el_quiz_color_results"] 	= "".$dataRes["trafficLightCase"];	


								} else {
									$tmpG["data"][$ind]["el_quiz_display"] 	= "no";
								}
							} else {
								$tmpG["data"][$ind]["el_quiz_display"] 	= "no";
							}
						}
						
						//if ($examinationFlag == "yes") {
						
							$imageSm_id		= $rs->Field("imageSm_id");
							$imageBg_id		= $rs->Field("imageBg_id");
							$imgID = "";
							if ($imageSm_id!="" && $imageSm_id>"0") {
								$imgID = $imageSm_id;
							} elseif ($imageBg_id!="" && $imageBg_id>"0") {
								$imgID 		= $imageBg_id;
							}				

							if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") {
								CiManagerFe::get_SL_CACHE_INDEX($imgID, "","");		
							}						

							$tmpG["data"][$ind]["TT"] = $this->POTS["ci"][$lectureIdS]["TT"];
							$tmpG["data"][$ind]["ND"] = $this->POTS["ci"][$lectureIdS]["ND"];
							$tmpG["data"][$ind]["ecc_reference"] = $rs->Field("ecc_reference");
							$tmpG["data"][$ind]["CID"] = $rs->Field("content_id");
							$tmpG["data"][$ind]["imgID"] = $imgID;

							$tmpG["data"][$ind]["moduleIdKey"] = $lectureIdKey;
							$ind++;							
						//}
												
					}
					$rs->MoveNext();
				}
			}
			
			$tmpG["AllRecs"] = count($tmpG["data"]);
			WebApp::addVar("ExtendedItemsResults_EL_".$idGrid, $tmpG); 	
			return $totals;
	}

    function catalogOfLearningItemsForProduct($ciFl)
    {
        global $session;
        
 		$this->getOverallProgrammeTree();
		$tmpMainLearningItemsGrid = array();$indPiLi=0;

		require_once(INCLUDE_AJAX_PATH."CIExtended/WiExtended.Class.php");
		$learningItemsRelations = WiExtended::getWILearningItemsRelations($ciFl,$this->lngId,$this->thisModeCode);
	 				
			if (isset($learningItemsRelations[$ciFl]["main"])) { //main role
				
				$MainLearningItems = $learningItemsRelations[$ciFl]["main"];
				if (isset($MainLearningItems["pr"]) && count($MainLearningItems["pr"])>0) {
					while (list($key,$value)=each($MainLearningItems["pr"])) {
						//echo "$key,$value<br>";
						$prCoord = implode("_",explode(".",$value));
						if (isset($this->POTS["PR_coord_r"][$prCoord])) {
							$idsOfLearningItems=array();
							$prId = $this->POTS["PR_coord_r"][$prCoord];
							
							$idsOfLearningItems[$prId]	= $prId;	
							$tmpMainLearningItemsGrid["data"][$indPiLi]["label"] = "Program:$prId";
							$tmpMainLearningItemsGrid["data"][$indPiLi]["key"] = "pr_simple";
						
							$tmpMainLearningItemsGrid["data"][$indPiLi]["key"] = "pr_simple";
							$tmpMainLearningItemsGrid["data"][$indPiLi]["additionalKey"] = $prId;
							$tmpMainLearningItemsGrid["data"][$indPiLi]["configuration_type_defined"] = $tmpMainLearningItemsGrid["data"][$indPiLi]["key"].$prId;
							
							$this->collectLearningItemsToCatalog($tmpMainLearningItemsGrid["data"][$indPiLi]["key"],$idsOfLearningItems,$prId);	
							$indPiLi++;
						}
					}
				}
				if (isset($MainLearningItems["ec"]) && count($MainLearningItems["ec"])>0) {
					
					if (count($MainLearningItems["ec"])==1) {
						$tmpMainLearningItemsGrid["data"][$indPiLi]["label"] = "Module";
						$tmpMainLearningItemsGrid["data"][$indPiLi]["key"] = "ec_simple";
					} else {
						$tmpMainLearningItemsGrid["data"][$indPiLi]["label"] = "boundle modules";
						$tmpMainLearningItemsGrid["data"][$indPiLi]["key"] = "ec_boundle";
					}	
					$idsOfLearningItems=array();
					while (list($key,$value)=each($MainLearningItems["ec"])) {
						$prCoord = implode("_",explode(".",$value));
						if (isset($this->POTS["EC"][$prCoord])) {
							$prId = $this->POTS["EC"][$prCoord];
							$idsOfLearningItems[$prId]	= $prId;		
						}
					}					
					$tmpMainLearningItemsGrid["data"][$indPiLi]["configuration_type_defined"] = $tmpMainLearningItemsGrid["data"][$indPiLi]["key"];
					$this->collectLearningItemsToCatalog($tmpMainLearningItemsGrid["data"][$indPiLi]["key"],$idsOfLearningItems);

					$indPiLi++;
				}
			
				if (isset($MainLearningItems["el"]) && count($MainLearningItems["el"])>0) {

					if (count($MainLearningItems["el"])==1) {
						$tmpMainLearningItemsGrid["data"][$indPiLi]["label"] = "Lecture";
						$tmpMainLearningItemsGrid["data"][$indPiLi]["key"] = "el_simple";
					} else {
						$tmpMainLearningItemsGrid["data"][$indPiLi]["label"] = "boundle lectures";
						$tmpMainLearningItemsGrid["data"][$indPiLi]["key"] = "el_boundle";
					}
					$idsOfLearningItems=array();
					while (list($key,$value)=each($MainLearningItems["el"])) {
					
						//echo "$key,$value<br>";
						$prCoord = implode("_",explode(".",$value));
						if (isset($this->POTS["EL"][$prCoord])) {
							$prId = $this->POTS["EL"][$prCoord];
							//$idsOfLearningItems[$prId]	= $prCoord;		
							$idsOfLearningItems[$prId]	= $prId;		
						}
					}					
					$tmpMainLearningItemsGrid["data"][$indPiLi]["configuration_type_defined"] = $tmpMainLearningItemsGrid["data"][$indPiLi]["key"];
					$this->collectLearningItemsToCatalog($tmpMainLearningItemsGrid["data"][$indPiLi]["key"],$idsOfLearningItems);					
					$indPiLi++;
				}					
				if (isset($MainLearningItems["ac"]) && count($MainLearningItems["ac"])>0) {
					$tmpMainLearningItemsGrid["data"][$indPiLi]["label"] = "boundle package resources";
					$tmpMainLearningItemsGrid["data"][$indPiLi]["key"] = "ac";
					$indPiLi++;
				}				
				$tmpMainLearningItemsGrid["data"]      = $tmpMainLearningItemsGrid["data"];		
				$tmpMainLearningItemsGrid["AllRecs"]   = count($tmpMainLearningItemsGrid["data"]);
				WebApp::addVar("LearningItemsMainGrid",$tmpMainLearningItemsGrid);  
			}
    }

    function collectLearningItemsToCatalog($typeOfCatalogItems,$referenceIDsCOF,$additionalKey="")
    {
				$distinctsIdsOfLectures = array();
				$gridFilter=array();$indk=0;
				
				//ec_boundle
				if (isset($referenceIDsCOF) && count($referenceIDsCOF)>0) {
				if (($typeOfCatalogItems=="pr_simple" || $typeOfCatalogItems=="pr_boundle") || $typeOfCatalogItems=="programs") {

							$typeOfCatalogItemsToCollect = "modules";
							
							reset($referenceIDsCOF);
							WHILE (list($programID,$dt)=each($referenceIDsCOF)) {
								$prIds[$programID] = $programID;
							}							
							$prIdsStr = implode(",",$prIds);
							
							if (count($prIds)==1) {
							} elseif (count($prIds)>1) {
							} else return;

							$fieldToSelect = "";
							$orderBySql = "ORDER BY firstPArt*1 ASC, secondPArt*1 ASC, ordF ASC, ecc_reference";
							$orderBySqlPr = "ORDER BY nivel_0_family.orderToDisplay, nivel_4.orderMenu ASC,  firstPArt*1 ASC, secondPArt*1 ASC, ordF ASC, ecc_reference";
							$cnt_data = "	
									SELECT DISTINCT  		 
											c.content_id as content_id,coalesce(ecc_reference*1, orderContent) as ordF, ecc_reference,
											SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 1), '_', -1) as firstPArt,
											SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 2), '_', -1) as secondPArt

									  FROM content as c 
									  JOIN nivel_4 
									    ON nivel_4.id_zeroNivel	= c.id_zeroNivel
									   AND nivel_4.id_firstNivel	= c.id_firstNivel
									   AND nivel_4.id_secondNivel	= c.id_secondNivel
									   AND nivel_4.id_thirdNivel	= c.id_thirdNivel
									   AND nivel_4.id_fourthNivel = c.id_fourthNivel
									   
									   JOIN nivel_0_family 
									   
										 ON nivel_0_family.node_family_id =  nivel_4.node_family_id
										AND nivel_0_family.id_zeroNivel = nivel_4.id_zeroNivel
										
										
							   LEFT JOIN ci_elearning_extended on c.content_id = ci_elearning_extended.content_id 
									 AND ci_elearning_extended.lng_id = '".$this->lngId."' AND ci_elearning_extended.statusInfo = '".$this->thisModeCode."'
								   WHERE c.content_id in (".$prIdsStr.") ".$orderBySqlPr;		

							$rs = WebApp::execQuery($cnt_data);	
							while (!$rs->EOF())  {
								$programIDC		= $rs->Field("content_id");
								if (isset($this->POTS["PR_coord"][$programIDC])) {
									$programIdKey = $this->POTS["PR_coord"][$programIDC];
									if (isset($this->POTS["ECInPR"][$programIdKey])) {
										$filterName = $this->POTS["ci"][$programIDC]["ND"];
										$gridFilter["data"][$indk]["class_n"] = "";
										$gridFilter["data"][$indk]["filterName"] = $filterName;
										$gridFilter["data"][$indk++]["filterID"] = $programIdKey;	
										$tmp = $this->POTS["ECInPR"][$programIdKey];
										$distinctsIdsOfLectures = array_merge($distinctsIdsOfLectures,$tmp);
									}					
								}								
								$rs->MoveNext();
							}
				} elseif ($typeOfCatalogItems=="ec_boundle" || $typeOfCatalogItems=="ec_simple" || $typeOfCatalogItems=="modules") {	
							$typeOfCatalogItemsToCollect = "modules";
							$distinctsIdsOfLectures = array_merge($referenceIDsCOF);
				} elseif ($typeOfCatalogItems=="el_simple" || $typeOfCatalogItems=="el_boundle" || $typeOfCatalogItems=="lectures") {	
							$typeOfCatalogItemsToCollect = "lectures";
							$distinctsIdsOfLectures = array_merge($referenceIDsCOF);
				}
				if (count($distinctsIdsOfLectures)>0) {
					$tmpData = $this->findCisExtendedInformation($distinctsIdsOfLectures,$typeOfCatalogItems,$orderBy,$ci_target,"maybe",$my_ci_target,$limitToSqlNr,$typeOfCatalogItemsToCollect);
					WebApp::addVar("listOfItemsCiGrid_".$typeOfCatalogItems."".$additionalKey,$tmpData);
				}			
			}
    }

	function initReferenceByStateOrConf($allow_override="0",$programe_reference_ids= array()) {
		global $session;
			
			
			$referenceIDsCOF =array();
			$initialize_from_overrided="no";
			
			if ($allow_override=="0") { //yes
				//echo $this->appRelSt[$simulationModeId]."$allow_override:$simulationModeId:initReferenceByStateOrConf";

				
					if (isset($this->appRelSt["elearningInfo"]["step"]) && 
						(
							$this->appRelSt["elearningInfo"]["step"]=="PR" || 
							$this->appRelSt["elearningInfo"]["step"]=="EC" || 
							$this->appRelSt["elearningInfo"]["step"]=="EL")
					) {
							$actualFilterStatus 			= $this->appRelSt["elearningInfo"]["step"];
							$initialize_from_overrided  	= "yes";

							$ciFl = $session->Vars["idElC"];
							if ($this->appRelSt["elearningInfo"]["step"]=="PR" && isset($this->POTS["PR_coord"][$ciFl]))
								$referenceIDsCOF[$ciFl]	= $this->POTS["PR_coord"][$ciFl];
							elseif ($this->appRelSt["elearningInfo"]["step"]=="EC" && isset($this->POTS["EC_coord"][$ciFl]))
								$referenceIDsCOF[$ciFl]	= $this->POTS["EC_coord"][$ciFl];
							elseif ($this->appRelSt["elearningInfo"]["step"]=="EL" && isset($this->POTS["EL_coord"][$ciFl]))
								$referenceIDsCOF[$ciFl]	= $this->POTS["EL_coord"][$ciFl];
							
							/*echo $this->ci_type_configuration."ci_type_configuration\n";
							echo $this->cidFlow."cidFlow\n";
							echo $actualFilterStatus."actualFilterStatus\n";
							echo $ciFl."ciFl\n";
							echo $session->Vars["idElC"]."idElC\n";*/
							
							
					} elseif (isset($session->Vars["idElC"])  && isset($this->appRelSt[$session->Vars["idElC"]]) &&

						(		$this->appRelSt[$session->Vars["idElC"]]["ci_type"]=="PR" 
							|| $this->appRelSt[$session->Vars["idElC"]]["ci_type"]=="EC" 
							|| $this->appRelSt[$session->Vars["idElC"]]["ci_type"]=="EL" 
							|| $this->appRelSt[$session->Vars["idElC"]]["ci_type"]=="TC")
					) {


							$actualFilterStatus = $this->appRelSt[$session->Vars["idElC"]]["ci_type"];
							$initialize_from_overrided = "yes";

							$ciFl = $session->Vars["idElC"];
							if ($actualFilterStatus=="PR" && isset($this->POTS["PR_coord"][$ciFl]))
								$referenceIDsCOF[$ciFl]	= $this->POTS["PR_coord"][$ciFl];
							elseif ($actualFilterStatus=="EC" && isset($this->POTS["EC_coord"][$ciFl]))
								$referenceIDsCOF[$ciFl]	= $this->POTS["EC_coord"][$ciFl];
							elseif ($actualFilterStatus=="EL" && isset($this->POTS["EL_coord"][$ciFl]))
								$referenceIDsCOF[$ciFl]	= $this->POTS["EL_coord"][$ciFl];						
							elseif ($actualFilterStatus=="TC" && isset($this->POTS["TC_coord"][$ciFl]))
								$referenceIDsCOF[$ciFl]	= $this->POTS["TC_coord"][$ciFl];						

					} elseif (isset($this->referenceFromCiIncludeItems["ordered"]) && count($this->referenceFromCiIncludeItems["ordered"])>0) {

						$nrOfParams 		= count($this->referenceFromCiIncludeItems["ordered"]);
						$lastParamsType 	= $this->referenceFromCiIncludeItems["ordered"][$nrOfParams]["type"];

						if ($lastParamsType=="PR" || $lastParamsType=="EC" || $lastParamsType=="EL" || $lastParamsType=="TC") {

								$actualFilterStatus = $lastParamsType;
								$initialize_from_overrided = "yes";							

								$ciFl = $this->referenceFromCiIncludeItems["ordered"][$nrOfParams]["id"];
								if ($actualFilterStatus=="PR" && isset($this->POTS["PR_coord"][$ciFl]))
									$referenceIDsCOF[$ciFl]	= $this->POTS["PR_coord"][$ciFl];
								elseif ($actualFilterStatus=="EC" && isset($this->POTS["EC_coord"][$ciFl]))
									$referenceIDsCOF[$ciFl]	= $this->POTS["EC_coord"][$ciFl];
								elseif ($actualFilterStatus=="EL" && isset($this->POTS["EL_coord"][$ciFl]))
									$referenceIDsCOF[$ciFl]	= $this->POTS["EL_coord"][$ciFl];	
								elseif ($actualFilterStatus=="TC" && isset($this->POTS["TC_coord"][$ciFl]))
									$referenceIDsCOF[$ciFl]	= $this->POTS["TC_coord"][$ciFl];						
						}

					} else {
						//kontrollo nese ke nje reference te programi, moduli apo leksioni
						if (isset($this->appRelSt["LECTURE_RELATED_ALL"]["PR"]) && count($this->appRelSt["LECTURE_RELATED_ALL"]["PR"])==1) {	
							$ciFl = $this->appRelSt["LECTURE_RELATED"]["PR"];
							if (isset($this->POTS["PR_coord"][$ciFl])) {
								$referenceIDsCOF[$ciFl]	= $this->POTS["PR_coord"][$ciFl];						
								$actualFilterStatus = "PR";
								$initialize_from_overrided = "yes";					
							}
						} elseif (isset($this->appRelSt["LECTURE_RELATED_ALL"]["EC"]) && count($this->appRelSt["LECTURE_RELATED_ALL"]["EC"])==1) {	
							$ciFl = $this->appRelSt["LECTURE_RELATED"]["EC"];
							if (isset($this->POTS["EC_coord"][$ciFl])) {
								$referenceIDsCOF[$ciFl]	= $this->POTS["EC_coord"][$ciFl];						
								$actualFilterStatus = "EC";
								$initialize_from_overrided = "yes";					
							}						
						} elseif (isset($this->appRelSt["LECTURE_RELATED_ALL"]["EL"]) && count($this->appRelSt["LECTURE_RELATED_ALL"]["EL"])==1) {	
							$ciFl = $this->appRelSt["LECTURE_RELATED"]["EL"];
							if ( isset($this->POTS["EL_coord"][$ciFl])) {
								$referenceIDsCOF[$ciFl]	= $this->POTS["EL_coord"][$ciFl];						
								$actualFilterStatus = "EL";
								$initialize_from_overrided = "yes";					
							}						
						} 
					}
			}
			
			/*echo "<textarea>";
			print_r($this->appRelSt["elearningInfo"]);
			print_r($session->Vars["idElC"].":idElC\n");
			print_r($ciFl.":ciFl\n");
			print_r($initialize_from_overrided.":initialize_from_overrided\n");
			print_r($actualFilterStatus.":actualFilterStatus\n");
			print_r($referenceIDsCOF);
			print_r($this->POTS["EC_coord"]);
			echo "</textarea>";	*/
			
			if ($initialize_from_overrided == "no" || count($referenceIDsCOF)==0) {
				if (isset($programe_reference_ids) && count($programe_reference_ids)>0) {
					$actualFilterStatus 	= "PR";
					while (list($key,$value)=each($programe_reference_ids)) {
						if (isset($this->POTS["PR_coord"][$value]))
							$referenceIDsCOF[$value]	= $this->POTS["PR_coord"][$value];
					}
					//$initialize_from_overrided = "yes";			
				} elseif (isset($this->POTS["PR_coord"]) && count($this->POTS["PR_coord"])>0) {
					
					//inicializo programet nga programet qe jane realisht ne zone
					
					$actualFilterStatus 	= "PR";
					//$referenceIDsCOF	= $this->POTS["PR_coord"];
					if (isset($this->POTS["eltypes"][$session->Vars["level_0"]]["PR"])) {
						$referenceIDsCOF = $this->POTS["eltypes"][$session->Vars["level_0"]]["PR"];
					}

				}
			} 
			$ret["actualFilterStatus"] 			= $actualFilterStatus;
			$ret["referenceIDsCOF"]				= $referenceIDsCOF;	
			$ret["initialize_from_overrided"]	= $initialize_from_overrided;	

/*
				echo "<textarea>";
				print_r($this->appRelSt);
				print_r($ret);
				echo "</textarea>";	*/

		
		return $ret;
	}	    
    function catalogOfLecturesFuncionality($orderBy="",$ci_reference="",$ci_target="",$my_ci_target="",$limitToSqlNr="")
    {
        global $session;

		$starts	= WebApp::get_formatted_microtime();
		$this->getOverallProgrammeTree();

//nem_id	nem_name	nem_box	nem_attPath	nem_order	nemAtt_w	nemAtt_h	filterNem	is_editable_simple_mode	tools_related
//274	Lectures Catalogs Collector	EccElearning/CatalogOfLectures/CatalogOfLectures.html	prcss=CatalogOfLectures	0	700	600	y	yes	73
//275	Modules Catalogs Collector	EccElearning/CatalogOfLectures/CatalogOfLectures.html	prcss=CatalogOfLectures	0	700	600	y	yes	72
//276	My Current Working Lectures	EccElearning/CatalogOfLectures/CatalogOfLectures.html	prcss=CatalogOfLectures	0	700	600	y	yes	
//293	Live Events (tutorial) Catalog Collector	EccElearning/CatalogOfLectures/CatalogOfLectures.html	prcss=CatalogOfLectures	0	740	600	y	yes	74
//294	Tutorials Catalogs Collector	EccElearning/CatalogOfLectures/CatalogOfLectures.html	prcss=CatalogOfLectures	0	740	600	y	yes	74

//323	Product Item - Learning Items Catalogs Collector	EccElearning/ProductItemCatalog/ProductItemCatalog.html	prcss=CatalogOfLectures	5	700	600	y	yes	110
//333	Enroll to Event	EccElearning/enrollToEvent/enrollToEventInDetails.html	prcss=CatalogOfLectures	255	740	600	y	yes	

//nem_id	nem_name	nem_attPath	data_type
//42	Lectures Catalogs Collector	prcss=MpesaCatalogOfLearningItems	lectures
//43	Modules Catalogs Collector	prcss=MpesaCatalogOfLearningItems	modules

		
/*if ($this->configuration_nemid==277) {	//277	Ecc Related Catalogs Collector	

} else*/


		//		echo  "CATALOGOFLECTURESFUNCIONALITY<br>";
		//		echo $this->NEM_PROP["typeOfItems"]."-TYPEOFITEMS<br>";
		
				$this->configuration_type_defined = $this->NEM_PROP["typeOfItems"];
				$this->typeOfCatalogItems = $this->configuration_type_defined;

				$dataInicializationOfState = $this->initReferenceByStateOrConf($this->allow_override, $this->programe_reference_ids);
				if (isset($dataInicializationOfState["referenceIDsCOF"]))
						$this->referenceIDsCOF = $dataInicializationOfState["referenceIDsCOF"];
				else	$this->referenceIDsCOF = array();
				
				if (isset($dataInicializationOfState["actualFilterStatus"]))
						$this->actualFilterStatus = $dataInicializationOfState["actualFilterStatus"];
				else	$this->actualFilterStatus 	= $this->ci_type_configuration;		
				
				if (isset($dataInicializationOfState["initialize_from_overrided"]))
						$initialize_from_overrided = $dataInicializationOfState["initialize_from_overrided"];
				else	$initialize_from_overrided="no";				

/*echo "KETU 6";				
		
echo $initialize_from_overrided."-initialize_from_overrided<br>";
echo $this->configuration_type."-CONFIGURATION_TYPE<br>";
echo $this->configuration_type_defined."-CONFIGURATION_TYPE_DEFINED<br>";
echo $this->actualFilterStatus."-ACTUALFILTERSTATUS<br>";
echo $this->typeOfCatalogItems."-typeOfCatalogItems<br>";
echo $this->enable_catalog_personalization."-enable_catalog_personalization<br>";
echo $this->apply_catalog_enrollement_state."-apply_catalog_enrollement_state<br>";
echo "<textarea>";
print_r($dataInicializationOfState);
print_r($this->programe_reference_ids);
print_r($this->referenceIDsCOF);
echo "</textarea>";*/

				if ($this->apply_catalog_enrollement_state=="yes" && $this->enable_catalog_personalization=="yes") {
					//kontrollo qe referencat te jene enrolled
					$referenceIDsCOFToBeControled = $this->referenceIDsCOF;
					$this->referenceIDsCOF = array();
					if ($this->actualFilterStatus=='PR') {
						reset($referenceIDsCOFToBeControled);
						WHILE (list($liID,$liKey)=each($referenceIDsCOFToBeControled)) {
							if (is_array($this->partecipationStr['pr']) && in_array($liKey,$this->partecipationStr['pr'])) 
								$this->referenceIDsCOF[$liID] = $liKey;
						}
					}
					if ($this->actualFilterStatus=='EC') {
						reset($referenceIDsCOFToBeControled);
						WHILE (list($liID,$liKey)=each($referenceIDsCOFToBeControled)) {
							if (is_array($this->partecipationStr['ec']) && in_array($liKey,$this->partecipationStr['ec'])) 
								$this->referenceIDsCOF[$liID] = $liKey;
						}
					}					
					if ($this->actualFilterStatus=='EL') {
						reset($referenceIDsCOFToBeControled);
						WHILE (list($liID1,$liID)=each($referenceIDsCOFToBeControled)) {
							if (isset($this->POTS["EL_coord"][$liID]) ) {
								$liKey = $this->POTS["EL_coord"][$liID];
								if (is_array($this->partecipationStr['el']) && in_array($liKey,$this->partecipationStr['el'])) 
									$this->referenceIDsCOF[$liID] = $liID;
							}							
						}
					}
				}
				$distinctsIdsOfLectures = array();
				$gridFilter=array();$indk=0;
				
				$childRelatedOfDistinctsIds = array();
				$childRelatedTypes = "";
				
				
				if (($this->typeOfCatalogItems=="programs") && isset($this->referenceIDsCOF) && count($this->referenceIDsCOF)>0) {

							reset($this->referenceIDsCOF);
							WHILE (list($programID,$dt)=each($this->referenceIDsCOF)) {
								$prIds[$programID] = $programID;
							}							
							$prIdsStr = implode(",",$prIds);
							if (count($prIds)==1) {
							} elseif (count($prIds)>1) {
							} else {
								return;
							}

							$fieldToSelect = "";
							$orderBySql = "ORDER BY firstPArt*1 ASC, secondPArt*1 ASC, ordF ASC, ecc_reference";
							$orderBySqlPr = "ORDER BY nivel_0_family.orderToDisplay, nivel_4.orderMenu ASC,  firstPArt*1 ASC, secondPArt*1 ASC, ordF ASC, ecc_reference";
							$cnt_data = "	
									SELECT DISTINCT  		 
											c.content_id as content_id,coalesce(ecc_reference*1, orderContent) as ordF, ecc_reference,
											SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 1), '_', -1) as firstPArt,
											SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 2), '_', -1) as secondPArt

									  FROM content as c 
									  JOIN nivel_4 
									    ON nivel_4.id_zeroNivel	= c.id_zeroNivel
									   AND nivel_4.id_firstNivel	= c.id_firstNivel
									   AND nivel_4.id_secondNivel	= c.id_secondNivel
									   AND nivel_4.id_thirdNivel	= c.id_thirdNivel
									   AND nivel_4.id_fourthNivel = c.id_fourthNivel
									   
									   JOIN nivel_0_family 
									   
										 ON nivel_0_family.node_family_id =  nivel_4.node_family_id
										AND nivel_0_family.id_zeroNivel = nivel_4.id_zeroNivel
										
							   LEFT JOIN ci_elearning_extended on c.content_id = ci_elearning_extended.content_id 
									 AND ci_elearning_extended.lng_id = '".$this->lngId."' AND ci_elearning_extended.statusInfo = '".$this->thisModeCode."'
								   WHERE c.content_id in (".$prIdsStr.") ".$orderBySqlPr;		

							$rs = WebApp::execQuery($cnt_data);	
							while (!$rs->EOF())  {
								$programIDC		= $rs->Field("content_id");
								$distinctsIdsOfLectures[$programIDC] = $programIDC;
								
								$filterName = $this->POTS["ci"][$programIDC]["type_of_programe"];
								$gridFilter["data"][$indk]["class_n"] = "";
								$gridFilter["data"][$indk]["filterName"] = "{{_".$filterName."}}";
								$gridFilter["data"][$indk++]["filterID"] = $filterName;		
								
								$programIdKey = $this->POTS["PR_coord"][$programIDC];
								if (isset($this->POTS["ECInPR"][$programIdKey])) {
									$childRelatedOfDistinctsIds[$programIDC] =$this->POTS["ECInPR"][$programIdKey];
								}				
								$rs->MoveNext();
							}
							
						$childRelatedTypes = "modules"; 
			
				} elseif ($this->typeOfCatalogItems=="modules" && isset($this->referenceIDsCOF) && count($this->referenceIDsCOF)>0) {

						//po implementohet apply_catalog_enrollement_state
						//	echo "inHere";
						
						if ($this->actualFilterStatus == "PR") {
							
							reset($this->referenceIDsCOF);
							WHILE (list($programID,$dt)=each($this->referenceIDsCOF)) {
								$prIds[$programID] = $programID;
							}							
							$prIdsStr = implode(",",$prIds);
							
							if (count($prIds)==1) {
							} elseif (count($prIds)>1) {
							} else return;


							$fieldToSelect = "";
							$orderBySql = "ORDER BY firstPArt*1 ASC, secondPArt*1 ASC, ordF ASC, ecc_reference";
							$orderBySqlPr = "ORDER BY nivel_0_family.orderToDisplay, nivel_4.orderMenu ASC,  firstPArt*1 ASC, secondPArt*1 ASC, ordF ASC, ecc_reference";
							$cnt_data = "	
									SELECT DISTINCT  		 
											c.content_id as content_id,coalesce(ecc_reference*1, orderContent) as ordF, ecc_reference,
											SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 1), '_', -1) as firstPArt,
											SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 2), '_', -1) as secondPArt

									  FROM content as c 
									  JOIN nivel_4 
									    ON nivel_4.id_zeroNivel	= c.id_zeroNivel
									   AND nivel_4.id_firstNivel	= c.id_firstNivel
									   AND nivel_4.id_secondNivel	= c.id_secondNivel
									   AND nivel_4.id_thirdNivel	= c.id_thirdNivel
									   AND nivel_4.id_fourthNivel = c.id_fourthNivel
									   
									   JOIN nivel_0_family 
									   
										 ON nivel_0_family.node_family_id =  nivel_4.node_family_id
										AND nivel_0_family.id_zeroNivel = nivel_4.id_zeroNivel
										
										
							   LEFT JOIN ci_elearning_extended on c.content_id = ci_elearning_extended.content_id 
									 AND ci_elearning_extended.lng_id = '".$this->lngId."' AND ci_elearning_extended.statusInfo = '".$this->thisModeCode."'
								   WHERE c.content_id in (".$prIdsStr.") ".$orderBySqlPr;		

							$rs = WebApp::execQuery($cnt_data);	
							while (!$rs->EOF())  {
								$programIDC		= $rs->Field("content_id");
								if (isset($this->POTS["PR_coord"][$programIDC])) {
									$programIdKey = $this->POTS["PR_coord"][$programIDC];
									if (isset($this->POTS["ECInPR"][$programIdKey])) {
										$filterName = $this->POTS["ci"][$programIDC]["ND"];
										$gridFilter["data"][$indk]["class_n"] = "";
										$gridFilter["data"][$indk]["filterName"] = $filterName;
										$gridFilter["data"][$indk++]["filterID"] = $programIdKey;	
										$tmp = $this->POTS["ECInPR"][$programIdKey];
										$distinctsIdsOfLectures = array_merge($distinctsIdsOfLectures,$tmp);
									}					
								}								
								$rs->MoveNext();
							}
						}
						if ($this->actualFilterStatus == "EC") {
							//find related modules
							reset($this->referenceIDsCOF);
							WHILE (list($moduleID,$moduleKey)=each($this->referenceIDsCOF)) {
									$tmpT = explode("_",$moduleKey);
									$programIdKey = $tmpT[0]."_".$tmpT[1];
									if (isset($this->POTS["ECInPR"][$programIdKey])) {
										$tmp = $this->POTS["ECInPR"][$programIdKey];
										$distinctsIdsOfLectures = array_merge($distinctsIdsOfLectures,$tmp);
									}
									$keyIndex = array_search($moduleID, $distinctsIdsOfLectures);
									unset($distinctsIdsOfLectures[$keyIndex]);									
							}							
						}	
						
						$controllParentsOfLearningItems = "no";
						if ($initialize_from_overrided=="no") {
							//jemi ne rastin qe nuk ka override 
							if (count($this->programe_reference_ids)==1) {
								//jemi ne rastin qe catalog eshte inicializuar nga nje reference, ne konteksin e my enrolled items duhet te sillet si catalog per programin current
								$controllParentsOfLearningItems = "inContextOfProgramEnroll";
							} else {
								//jemi ne rastin qe catalog nuk eshte inicializuar nga ndonje reference, ne konteksin e my enrolled items duhet te sillet si boundle
								$controllParentsOfLearningItems = "inContextOfBoundleOfModulesEnroll";
							}
						}						
	
						//CONDITION: TO INLCUDE ONLY MY ITEMS
						if ($this->apply_catalog_enrollement_state=="yes" && $this->enable_catalog_personalization=="yes") {
							//kontrollo qe referencat te jene enrolled
							$referenceIDsCOFToBeControled = $distinctsIdsOfLectures;
							$distinctsIdsOfLectures = array();
								reset($referenceIDsCOFToBeControled);
								WHILE (list($ooo,$liID)=each($referenceIDsCOFToBeControled)) {
									if (isset($this->POTS["EC_coord"][$liID]) ) {
										$liKey = $this->POTS["EC_coord"][$liID];
										if (is_array($this->partecipationStr['ec']) && in_array($liKey,$this->partecipationStr['ec'])) {
											$includeLi = "yes";
											if ($controllParentsOfLearningItems == "inContextOfProgramEnroll") {
												//BEJ NJE VALIDIM SHTESE
												if (isset($this->partecipationStrInContext["ec"]) 
													&& in_array($liID,$this->partecipationStrInContext["ec"])) {
													$includeLi = "yes";
												}												
											}
											if ($controllParentsOfLearningItems == "inContextOfBoundleOfModulesEnroll") {
												$includeLi = "no";
												if (/*isset($this->POTS["PR_coord_r"][$prKEY]) 
													&&*/ isset($this->partecipationStrInContext["ec"]) 
													&& in_array($liID,$this->partecipationStrInContext["ec"])) {
													$includeLi = "yes";
												}											
											}
											if ($includeLi == "yes")
												$distinctsIdsOfLectures[$liKey] = $liID;
										}
									}
								}
						}
						if (count($distinctsIdsOfLectures)>0) {
							$tmpp = $distinctsIdsOfLectures;
							WHILE (list($ooo,$moduleId)=each($tmpp)) {
								if (isset($this->POTS["eltypesB"][$moduleId]["borrowed_mode"]) 
									&& $this->POTS["eltypesB"][$moduleId]["borrowed_mode"]=="borrowfrom_structure"
									&& $this->POTS["eltypesB"][$moduleId]["borrowed_id"]>0
									) {
									
									$borrowed_id = $this->POTS["eltypesB"][$moduleId]["borrowed_id"];
									if (isset($this->POTS["EC_coord"][$borrowed_id])) {
										$moduleKey = $this->POTS["EC_coord"][$borrowed_id];
										$childRelatedOfDistinctsIds[$moduleId] =$this->POTS["ELInEC"][$moduleKey];
									}
								} elseif (isset($this->POTS["EC_coord"][$moduleId])) {
									$moduleKey = $this->POTS["EC_coord"][$moduleId];
									$childRelatedOfDistinctsIds[$moduleId] =$this->POTS["ELInEC"][$moduleKey];
								} 
							}
						}
						
						
						//$distinctsIdsOfLectures - loop itesm to find lectures
						$childRelatedTypes = "lectures"; 
				} elseif (($this->typeOfCatalogItems=="insideLecture" || $this->typeOfCatalogItems=="lectures") && isset($this->referenceIDsCOF) && count($this->referenceIDsCOF)>0) {

						//po implementohet apply_catalog_enrollement_state
						$prIds = array();
						reset($this->referenceIDsCOF);
						if ($this->actualFilterStatus == "PR") {
							WHILE (list($programID,$dt)=each($this->referenceIDsCOF)) 
								$prIds[$programID] = $programID;
							
							$prIdsStr = implode(",",$prIds);
							if (count($prIds)==1) {
							} elseif (count($prIds)>1) {
							} else return;

							$fieldToSelect = "";
							$orderBySql = "ORDER BY firstPArt*1 ASC, secondPArt*1 ASC, ordF ASC, ecc_reference";
							$orderBySqlPr = "ORDER BY nivel_0_family.orderToDisplay, nivel_4.orderMenu ASC,  firstPArt*1 ASC, secondPArt*1 ASC, ordF ASC, ecc_reference";
							$cnt_data = "	
									SELECT DISTINCT  		 
											c.content_id as content_id,coalesce(ecc_reference*1, orderContent) as ordF, ecc_reference,
											SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 1), '_', -1) as firstPArt,
											SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 2), '_', -1) as secondPArt

									  FROM content as c 
									  JOIN nivel_4 
									    ON nivel_4.id_zeroNivel	= c.id_zeroNivel
									   AND nivel_4.id_firstNivel	= c.id_firstNivel
									   AND nivel_4.id_secondNivel	= c.id_secondNivel
									   AND nivel_4.id_thirdNivel	= c.id_thirdNivel
									   AND nivel_4.id_fourthNivel = c.id_fourthNivel
									   
									   JOIN nivel_0_family 
									   
										 ON nivel_0_family.node_family_id =  nivel_4.node_family_id
										AND nivel_0_family.id_zeroNivel = nivel_4.id_zeroNivel
										
										
							   LEFT JOIN ci_elearning_extended on c.content_id = ci_elearning_extended.content_id 
									 AND ci_elearning_extended.lng_id = '".$this->lngId."' AND ci_elearning_extended.statusInfo = '".$this->thisModeCode."'
								   WHERE c.content_id in (".$prIdsStr.") ".$orderBySqlPr;			
							
							$rs = WebApp::execQuery($cnt_data);	
							while (!$rs->EOF())  {
								$programIDC		= $rs->Field("content_id");
								if (isset($this->POTS["PR_coord"][$programIDC])) {
									$programIdKey = $this->POTS["PR_coord"][$programIDC];
									if (isset($this->POTS["ELInPREC"][$programIdKey])) {
										$modulesInsideArr = $this->POTS["ELInPREC"][$programIdKey];
										if (count($modulesInsideArr)>0) {
											$filterName = $this->POTS["ci"][$programIDC]["ND"];
											$gridFilter["data"][$indk]["class_n"] = "";
											$gridFilter["data"][$indk]["filterName"] = $filterName;
											$gridFilter["data"][$indk++]["filterID"] = $programIdKey;	
											$EcIds = array();
											while (list($moduleIdKey,$lecturesData)=each($modulesInsideArr)) {
												if (count($lecturesData)>0) {
													$distinctsIdsOfLectures = array_merge($distinctsIdsOfLectures,$lecturesData);
													if (isset($this->POTS["EC"][$moduleIdKey])) {
														$moduleId 	= $this->POTS["EC"][$moduleIdKey];
														$EcIds[$moduleId] = $moduleId;
													}
												}
											}
											if (count($EcIds)>0) {
												$EcIdsStr = implode(",",$EcIds);
												$mdData = "	
														SELECT DISTINCT  		 
																c.content_id as content_id,coalesce(ecc_reference*1, orderContent) as ordF, ecc_reference,
																SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 1), '_', -1) as firstPArt,
																SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 2), '_', -1) as secondPArt

														  FROM content as c 

												   LEFT JOIN ci_elearning_extended on c.content_id = ci_elearning_extended.content_id 
														 AND ci_elearning_extended.lng_id = '".$this->lngId."' AND ci_elearning_extended.statusInfo = '".$this->thisModeCode."'
													   WHERE c.content_id in (".$EcIdsStr.") ".$orderBySql;		
												$rsM = WebApp::execQuery($mdData);
												while (!$rsM->EOF())  {
													
													$moduleIdS		= $rsM->Field("content_id");
													$moduleIdKey = $this->POTS["EC_coord"][$moduleIdS];
													$filterName = $this->POTS["ci"][$moduleIdS]["ND"];
													$contructFilter = "yes";							
													if ($this->apply_catalog_enrollement_state=="yes" && $this->enable_catalog_personalization=="yes") {
														//kontrollo qe referencat te jene enrolled
															$contructFilter = "no";	
															if (is_array($this->partecipationStr['ec']) && in_array($moduleIdKey,$this->partecipationStr['ec'])) 
																$contructFilter = "yes";	
													}
													if ($contructFilter == "yes") {
														$gridFilter["data"][$indk]["class_n"] = "bld";
														$gridFilter["data"][$indk]["filterName"] = " - ".$filterName;
														$gridFilter["data"][$indk++]["filterID"] = $moduleIdKey;	
													}
													$rsM->MoveNext();
												}
											}
										}
									}	
								}	
								$rs->MoveNext();
							}		
						}
						if ($this->actualFilterStatus == "EC") {
							reset($this->referenceIDsCOF);
							WHILE (list($moduleID,$moduleKey)=each($this->referenceIDsCOF)) {
								if (isset($this->POTS["ELInEC"][$moduleKey])) {
									$tmp = $this->POTS["ELInEC"][$moduleKey];
									$distinctsIdsOfLectures = array_merge($distinctsIdsOfLectures,$tmp);
								}						
							}
						}						
						
						if ($this->actualFilterStatus == "EL") {
							if ($this->typeOfCatalogItems=="insideLecture") {
								reset($this->referenceIDsCOF);
								WHILE (list($lectureId,$lectureKey)=each($this->referenceIDsCOF)) {
										$distinctsIdsOfLectures[$lectureId] = $lectureId;
								}								
							} else {
							
								//find related lectures
								reset($this->referenceIDsCOF);
								WHILE (list($lectureId,$lectureKey)=each($this->referenceIDsCOF)) {
										if (isset($this->POTS["ECofEl_coord"][$lectureId])) {
											$moduleKey = $this->POTS["ECofEl_coord"][$lectureId];
											$tmp = $this->POTS["ELInEC"][$moduleKey];
											$distinctsIdsOfLectures = array_merge($distinctsIdsOfLectures,$tmp);
										}	
										$keyIndex = array_search($lectureId, $distinctsIdsOfLectures);
										unset($distinctsIdsOfLectures[$keyIndex]);
								}
							}
						}
						
						//CONDITION: TO INLCUDE ONLY MY ITEMS
						if ($this->apply_catalog_enrollement_state=="yes" && $this->enable_catalog_personalization=="yes") {
								//kontrollo qe referencat te jene enrolled
								$referenceIDsCOFToBeControled = $distinctsIdsOfLectures;
								$distinctsIdsOfLectures = array();
								reset($referenceIDsCOFToBeControled);
								WHILE (list($ooo,$liID)=each($referenceIDsCOFToBeControled)) {
									//echo "<br>-$liID-";
									if (isset($this->POTS["EL_coord"][$liID]) ) {
										$liKey = $this->POTS["EL_coord"][$liID];
											//echo "-$liKey-";
										if (is_array($this->partecipationStr['el']) && in_array($liKey,$this->partecipationStr['el'])) {
											$distinctsIdsOfLectures[$liKey] = $liID;
										}
									}										
								}					
						}

						//$distinctsIdsOfLectures - loop items to find learning Items of lectures
						$childRelatedTypes = "insideLecture"; 
						
						
				/*echo $this->actualFilterStatus."<br>distinctsIdsOfLectures<pre>";
				print_r($this->referenceIDsCOF);
				echo "</pre>";							

				echo $this->typeOfCatalogItems."<br>distinctsIdsOfLectures<pre>";
				print_r($distinctsIdsOfLectures);
				echo "</pre>";			
						
				echo "childRelatedOfDistinctsIds<pre>";
				print_r($childRelatedOfDistinctsIds);
				echo "</pre>";	*/					
						
						
						
						
					

				} /*elseif ($this->typeOfCatalogItems=="insideLecture") {
				
					echo "<textarea>";// && $this->actualFilterStatus=="EL"
					print_r($this);
					echo "</textarea>";
						
					$childRelatedTypes = "insideLecture"; 
				}*/
				
				$gridFilter["AllRecs"] 	= count($gridFilter["data"]);
				WebApp::addVar("gridFilter_".$this->configuration_type_defined,$gridFilter);	
				
				
				/*echo "<textarea>";
				print_r($this->referenceIDsCOF);
				print_r($distinctsIdsOfLectures);
				echo "</textarea>";*/
				
				
				if (count($distinctsIdsOfLectures)>0) {
					$this->findCisExtendedInformation($distinctsIdsOfLectures,$this->configuration_type_defined,$orderBy,$ci_target,"maybe",$my_ci_target,$limitToSqlNr,$this->typeOfCatalogItems);
				} else {
					$gridDataSrcAll["data"] = array();
					$gridDataSrcAll["AllRecs"] 	= count($gridDataSrcAll["data"]);
					WebApp::addVar("listOfItemsCiGrid_$gridKey",$gridDataSrcAll);
				}
				
				
			/*	echo $this->typeOfCatalogItems."<br>distinctsIdsOfLectures<pre>";
				print_r($distinctsIdsOfLectures);
				echo "</pre>";			
						
				echo "childRelatedOfDistinctsIds<pre>";
				print_r($childRelatedOfDistinctsIds);
				echo "</pre>";	*/				
				
				
				//echo "<br>".$childRelatedTypes.":CHILDRELATEDTYPES<br>";
				if (count($distinctsIdsOfLectures)>0 && ($childRelatedTypes=="lectures" || $childRelatedTypes=="modules")) {
					WHILE (list($ooo,$liID)=each($distinctsIdsOfLectures)) {
						if (isset($childRelatedOfDistinctsIds[$liID]) && count($childRelatedOfDistinctsIds[$liID])>0) {
							$this->findCisExtendedInformation($childRelatedOfDistinctsIds[$liID],"childs_".$liID,$orderBy,$ci_target,"maybe",$my_ci_target,$limitToSqlNr,$childRelatedTypes);
						}
					}			
				} 
				elseif (count($distinctsIdsOfLectures)>0 && ($childRelatedTypes=="insideLecture")) {
					WHILE (list($ooo,$liID)=each($distinctsIdsOfLectures)) {
				
					$referenceIDsCOF = array();	
					if (isset($this->POTS["eltypesB"][$liID]["borrowed_mode"]) 
						&& $this->POTS["eltypesB"][$liID]["borrowed_mode"]=="borrowfrom_structure"
						&& $this->POTS["eltypesB"][$liID]["borrowed_id"]>0
						) {

						$borrowed_id = $this->POTS["eltypesB"][$liID]["borrowed_id"];
						
						$liKey = $this->POTS["EL_coord"][$borrowed_id];
						$crdItems = explode("_",$liKey );
						$referenceIDsCOF[$liID] = $liKey; 						
						$this->findCisExtendedInformation($referenceIDsCOF,"childs_".$liID,"node_order",$ci_target,"maybe",$my_ci_target,$limitToSqlNr,$childRelatedTypes,$crdItems);
						
					} elseif (isset($this->POTS["EL_coord"][$liID])) {
							$liKey = $this->POTS["ELR_coord"][$liID];
							$crdItems = explode("_",$liKey );
							$referenceIDsCOF[$liID] = $liKey; 
							$this->findCisExtendedInformation($referenceIDsCOF,"childs_".$liID,"node_order",$ci_target,"maybe",$my_ci_target,$limitToSqlNr,$childRelatedTypes,$crdItems);
					}						

					}
				}
		
		$dataToReturn= array();
		$dataToReturn["distinctsIdsOfLectures"] = $distinctsIdsOfLectures;
		$this->PAGELOAD_PARTIAL($starts,"catalogOfLecturesFuncionality END->");
			
		return $dataToReturn;
    }
	function getLectureNavigationChilds($crdItems,$gridKey="") {	
		global $session;
		
		IF ($session->Vars["thisMode"] == "_new")
			{$kusht_aktiv_joaktiv = " ";}
		ELSE
			{$kusht_aktiv_joaktiv = " AND nivel_4.active".$session->Vars["lang"]." != 1 ";}		
		
		if(defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP=="Y") 
			$nodeBootstrapFields = "coalesce(boostrap_class,'') as boostrap_class, coalesce(boostrap_ico,'') as boostrap_ico,";
		
		$sqlInfoTemplate = "SELECT 
		
		
							COALESCE(nivel_4.description".$session->Vars["lang"]."".$session->Vars["thisMode"].",      '') as NodeDescription, 		
							COALESCE(content.description".$session->Vars["lang"]."".$session->Vars["thisMode"].",      '') as CiDescription, 		
							
								".$nodeBootstrapFields."	
							COALESCE(nivel_4.imageSm_id,      '') as imageSm_id_node, 		
							coalesce(content.imageSm_id,'') as imageSm_id,	
							coalesce(content.imageBg_id,'') as imageBg_id,	

							title".$session->Vars["lang"]." as title,
							
							nivel_4.id_zeroNivel,nivel_4.id_firstNivel,nivel_4.id_secondNivel,nivel_4.id_thirdNivel,nivel_4.id_fourthNivel

						  FROM nivel_4
						  JOIN content 
							ON nivel_4.id_zeroNivel		= content.id_zeroNivel
						   AND nivel_4.id_firstNivel	= content.id_firstNivel
						   AND nivel_4.id_secondNivel	= content.id_secondNivel
						   AND nivel_4.id_thirdNivel	= content.id_thirdNivel
						   AND nivel_4.id_fourthNivel	= content.id_fourthNivel
					   
						   
						 JOIN profil_rights ON (       nivel_4.id_zeroNivel   = profil_rights.id_zeroNivel
														AND nivel_4.id_firstNivel  = profil_rights.id_firstNivel
														AND nivel_4.id_secondNivel = profil_rights.id_secondNivel
														AND nivel_4.id_thirdNivel  = profil_rights.id_thirdNivel
														AND nivel_4.id_fourthNivel = profil_rights.id_fourthNivel
														AND profil_rights.profil_id in (".$session->Vars["tip"].")
													)					   

						 WHERE nivel_4.state".$session->Vars["lang"]." != 7  
						   AND orderContent = '0' ".$kusht_aktiv_joaktiv."";


		
		if ($crdItems[4]>0) {
			$hierarchyLevel = 4;
			return;
		
		} elseif ($crdItems[3]>0) {			
			$hierarchyLevel = 3;
			
			$sqlInfoLevel = $sqlInfoTemplate."
									  AND nivel_4.id_zeroNivel = '".$crdItems[0]."'
									  AND nivel_4.id_firstNivel = '".$crdItems[1]."'
									  AND nivel_4.id_secondNivel = '".$crdItems[2]."'
									  AND nivel_4.id_thirdNivel  = '".$crdItems[3]."'
									  AND nivel_4.id_fourthNivel > 0 		
							 ORDER BY nivel_4.orderMenu";								
									
			
			
		
		} elseif ($crdItems[2]>0) {			
			$hierarchyLevel = 2;
			$sqlInfoLevel = $sqlInfoTemplate."
									  AND nivel_4.id_zeroNivel = '".$crdItems[0]."'
									  AND nivel_4.id_firstNivel = '".$crdItems[1]."'
									  AND nivel_4.id_secondNivel = '".$crdItems[2]."'
									  AND nivel_4.id_thirdNivel  > 0
									  AND nivel_4.id_fourthNivel = 0 		
							 ORDER BY nivel_4.orderMenu";								
			
		
		} elseif ($crdItems[1]>0) { 			
			$hierarchyLevel = 1;
			$sqlInfoLevel = $sqlInfoTemplate."
									  AND nivel_4.id_zeroNivel = '".$crdItems[0]."'
									  AND nivel_4.id_firstNivel = '".$crdItems[1]."'
									  AND nivel_4.id_secondNivel > 0
									  AND nivel_4.id_thirdNivel  = 0
									  AND nivel_4.id_fourthNivel = 0 		
							 ORDER BY nivel_4.orderMenu";			
		} else {			
			$hierarchyLevel = 0;	
			$sqlInfoLevel = $sqlInfoTemplate."
									  AND nivel_4.id_zeroNivel = '".$crdItems[0]."'
									  AND nivel_4.id_firstNivel > 0
									  AND nivel_4.id_secondNivel = 0
									  AND nivel_4.id_thirdNivel  = 0
									  AND nivel_4.id_fourthNivel = 0 		
							 ORDER BY nivel_4.orderMenu";			
		}

		$rsLev = WebApp::execQuery($sqlInfoLevel);	
		$levGrid["data"] = array(); $levGridI=0;
		WHILE (!$rsLev->EOF()) {

			$crd4 = array();
			$crd4[0] = $rsLev->Field("id_zeroNivel");
			$crd4[1] = $rsLev->Field("id_firstNivel");
			$crd4[2] = $rsLev->Field("id_secondNivel");
			$crd4[3] = $rsLev->Field("id_thirdNivel");
			$crd4[4] = $rsLev->Field("id_fourthNivel");
			
			$idKey 	= implode("_",$crd4);
			$idKeyNode = implode(",",$crd4);
			
			$linkhref  = "javascript:GoTo('thisPage?event=none.ch_state(k=".$linkCrd.")')";


			if(defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP=="Y") {
				$nodeClass 	 = $rsLev->Field("boostrap_class");
				$nodeIco	 = $rsLev->Field("boostrap_ico");
			}

			$nodeImageId 	= TRIM($rsLev->Field("imageSm_id_node"));	
			$imageSm_id 		= TRIM($rsLev->Field("imageSm_id"));	
			$imageBg_id 		= TRIM($rsLev->Field("imageBg_id"));	


			if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") {
				//thir griden
				if ($nodeImageId>0)
						CiManagerFe::get_SL_CACHE_INDEX($nodeImageId);	
				if ($imageSm_id>0)
						CiManagerFe::get_SL_CACHE_INDEX($imageSm_id);	
				if ($imageBg_id>0)
						CiManagerFe::get_SL_CACHE_INDEX($imageBg_id);	
			}	


			$NodeDescription = TRIM($rsLev->Field("NodeDescription"));	
			$CiTitle		 = TRIM($rsLev->Field("title"));
			$CiDescription	 = TRIM($rsLev->Field("CiDescription"));

			$tmp = array();
			$tmp["label"] 			= $NodeDescription;
			$tmp["NodeDescription"]	= $NodeDescription;
			$tmp["link"] 			= $linkhref;
			$tmp["linkcrd"] 		= $linkCrd;
			$tmp["linkhref"] 		= $linkhref;
			$tmp["ci_title"]		= $CiTitle;
			$tmp["ci_description"]	= $CiDescription;
			$tmp["nodeImageId"] 	= $nodeImageId;
			$tmp["imageSm_id"] 		= $imageSm_id;
			$tmp["imageBg_id"]		= $imageBg_id;

			$tmp["nodeClass"]		= $nodeClass;
			$tmp["boostrap_class"]	= $nodeClass;

			if ($tmp["nodeClass"]!="")
				$tmp["nodeClassExists"] = "yes";			

			$levGrid["data"][$levGridI++] = $tmp;
			
			$rsLev->MoveNext();
		}
		
		
	}	
	function findCisExtendedInformation($doc_id_set,$gridKey="",$orderBy="",$ci_target="",$isMeine="no",$my_ci_target="",$limitToSqlNr="",$typeOfCatalogItems="",$crdItems=array()) {	
			global $session;

			WebApp::addVar("enroll_description", "");	
			
			$join = "";
			$fieldToSelect = "
							SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 1), '_', -1) as firstPArt,
       						SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 2), '_', -1) as secondPArt,";
			$orderBySql = "ORDER BY firstPArt*1 ASC, secondPArt*1 ASC, ordF ASC, ecc_reference";
			
			if ($typeOfCatalogItems=="currentWorkingLectures") {
				$orderBySql = "ORDER BY z_analytics_progress_transition_last_access.date_created DESC";
				//$orderBySql = "ORDER BY date(z_analytics_progress_transition_last_access.date_created) DESC, time(z_analytics_progress_transition_last_access.date_created)   ASC ";  
				
				$join = " JOIN z_analytics_progress_transition_last_access 
							ON ses_userid = '".$session->Vars["ses_userid"]."' 
						   AND lecture_id = c.content_id
						   AND lecture_id != contentId";				
				  
				$fieldToSelect = " DATE_FORMAT(date_created, '%Y.%m.%d. %H:%i:%s')  as dd, ";				
			} elseif ($orderBy=="last_entered") {
				$orderBySql = "ORDER BY c.content_id DESC";
			} elseif ($orderBy=="sequence_id") {
				$fieldToSelect = "
							SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 1), '_', -1) as firstPArt,
       						SUBSTRING_INDEX(SUBSTRING_INDEX(ecc_reference, '_', 2), '_', -1) as secondPArt,";
				$orderBySql = "ORDER BY firstPArt*1 ASC, secondPArt*1 ASC, ordF ASC, ecc_reference";
			} elseif ($orderBy=="node_order") {
				$fieldToSelect = "";
				$orderBySql = "ORDER BY nivel_4.orderMenu ASC, nivel_4.id_zeroNivel, nivel_4.id_firstNivel, nivel_4.id_secondNivel, nivel_4.id_thirdNivel ";
				$join = "";
			} 
			$conditionToSql = "";
			if (isset($crdItems) && is_array($crdItems) && count($crdItems)>0) {
				if ($crdItems[4]>0) {
					$hierarchyLevel = 4;
					return;
				} elseif ($crdItems[3]>0) {			
					$hierarchyLevel = 3;
					$conditionToSql = " nivel_4.id_zeroNivel = '".$crdItems[0]."'
											  AND nivel_4.id_firstNivel = '".$crdItems[1]."'
											  AND nivel_4.id_secondNivel = '".$crdItems[2]."'
											  AND nivel_4.id_thirdNivel  = '".$crdItems[3]."'
											  AND nivel_4.id_fourthNivel > 0";								
				} elseif ($crdItems[2]>0) {			
					$hierarchyLevel = 2;
					$conditionToSql = "  nivel_4.id_zeroNivel = '".$crdItems[0]."'
											  AND nivel_4.id_firstNivel = '".$crdItems[1]."'
											  AND nivel_4.id_secondNivel = '".$crdItems[2]."'
											  AND nivel_4.id_thirdNivel  > 0
											  AND nivel_4.id_fourthNivel = 0";								
				} elseif ($crdItems[1]>0) { 			
					$hierarchyLevel = 1;
					$conditionToSql = "  nivel_4.id_zeroNivel = '".$crdItems[0]."'
											  AND nivel_4.id_firstNivel = '".$crdItems[1]."'
											  AND nivel_4.id_secondNivel > 0
											  AND nivel_4.id_thirdNivel  = 0
											  AND nivel_4.id_fourthNivel = 0";			
				} else {			
					$hierarchyLevel = 0;	
					$conditionToSql = "  nivel_4.id_zeroNivel = '".$crdItems[0]."'
											  AND nivel_4.id_firstNivel > 0
											  AND nivel_4.id_secondNivel = 0
											  AND nivel_4.id_thirdNivel  = 0
											  AND nivel_4.id_fourthNivel = 0 ";			
				}
				
				$conditionToSql .= " AND c.orderContent=0 ";
			
			} elseif (isset($doc_id_set) && is_array($doc_id_set) && count($doc_id_set)>0) {
				$valDocIds = implode(",",$doc_id_set);
				$conditionToSql = " c.content_id in (".$valDocIds.") ";
			} else return;
			$listData = array();
			$limitToSql ="";
			if ($limitToSqlNr!="" && $limitToSqlNr>0) {
				$limitToSql = " limit 0,$limitToSqlNr";
			} 

			$workingCi = new CiManagerFe();
			$indG 				= 0;
			$gridDataSrcAll 	= array();
		
			if(defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP=="Y") 
				$nodeBootstrapFields = "coalesce(boostrap_class,'') as boostrap_class, coalesce(boostrap_ico,'') as boostrap_ico,";

			$cnt_data = "	
					SELECT   		 
							c.content_id as content_id,coalesce(ecc_reference*1, orderContent) as ordF, ecc_reference,

							".$fieldToSelect."
							c.ci_type as ci_type,
					
							c.titleLng".$this->lngId." AS title,
							c.filenameLng".$this->lngId." AS filename,
						
							COALESCE(c.with_https,      'n') as with_https,
							
							if((c.descriptionLng".$this->lngId.$session->Vars["thisMode"]." IS NULL OR c.descriptionLng".$this->lngId.$session->Vars["thisMode"]." = ''), '', 
							   IF (CHAR_LENGTH(c.descriptionLng".$this->lngId.$session->Vars["thisMode"].") > 320, CONCAT(LEFT(c.descriptionLng".$this->lngId.$session->Vars["thisMode"].", 320),'...'), c.descriptionLng".$this->lngId.$session->Vars["thisMode"].")) AS description,	
							if((c.doc_sourceLng".$this->lngId." IS NULL 
									OR c.doc_sourceLng".$this->lngId." = ''),'', c.doc_sourceLng".$this->lngId.") 
								AS source,	
							if((c.source_authorLng".$this->lngId." IS NULL 
									OR c.source_authorLng".$this->lngId." = ''),'', 
									c.source_authorLng".$this->lngId.") 
								AS source_author,
							
							IF((c.imageSm_id >0),c.imageSm_id,'') 			as imageSm_id,
							coalesce(c.imageSm_id_name, '') 				as imageSm_id_name,

							IF((c.imageSm_id_mob >0),c.imageSm_id_mob,'') 	as imageSm_id_mob,
							coalesce(c.imageSm_id_mob_name, '') 			as imageSm_id_mob_name,

							IF((c.imageBg_id >0),c.imageBg_id,'') 			as imageBg_id,
							coalesce(c.imageBg_id_name, '') 				as imageBg_id_name,

							IF((c.imageBg_id_mob >0),c.imageBg_id_mob,'') 	as imageBg_id_mob,
							coalesce(c.imageBg_id_mob_name, '') 			as imageBg_id_mob_name,		
							
							COALESCE(nivel_4.imageSm_id,      '') as imageSm_id_node, 				
							".$nodeBootstrapFields."									
					
							
							
							c.id_zeroNivel as n0v, 
							c.id_firstNivel as n1v, 
							c.id_secondNivel as n2v, 
							c.id_thirdNivel as n3v, 
							c.id_fourthNivel as n4v,
							
							coalesce(ecc_reference, '') 		as ecc_reference, 				
							coalesce(reference, '') 			as reference,
							coalesce(reference_format, '') 		as reference_format,

							coalesce(identifier_key, '') 		as identifier_key,
							coalesce(identifier_type, '') 		as identifier_type,

							coalesce(doc_id, '') 				as ecc_doc_id,
							coalesce(category_kw_id, '') 		as category_kw_id,
							coalesce(category_kw_id_extra, '') 	as category_kw_id_extra,
							coalesce(doc_type, '') 				as doc_type,							
							
							IF(scheduling_from IS NULL,'', Date_Format(scheduling_from, '%d.%m.%Y')) 
								AS scheduling_from
			
		FROM content as c 
		".$join."
  		 				
  		 				
		JOIN nivel_4 
							ON nivel_4.id_zeroNivel = c.id_zeroNivel
						   AND nivel_4.id_firstNivel = c.id_firstNivel
						   AND nivel_4.id_secondNivel = c.id_secondNivel
						   AND nivel_4.id_thirdNivel = c.id_thirdNivel
						   AND nivel_4.id_fourthNivel = c.id_fourthNivel  		 				
  		 				
  		 				
  		 				
  		 				
			   LEFT JOIN ci_elearning_extended on c.content_id = ci_elearning_extended.content_id 
					 AND ci_elearning_extended.lng_id = '".$this->lngId."' AND ci_elearning_extended.statusInfo = '".$this->thisModeCode."'
  		 						
			   LEFT JOIN z_EccE_availability_information on c.content_id = z_EccE_availability_information.contentId 

				   WHERE ".$conditionToSql." GROUP BY c.content_id ".$orderBySql.$limitToSql."";		
	
		$rs = WebApp::execQuery($cnt_data);	
/*echo "<textarea>";	
		print_r($rs);	
		echo "</textarea>";*/
		$ln = $session->Vars["lang"];
		while (!$rs->EOF())  {

			$idX 				= $rs->Field("content_id");
			$item_ci_id			= $idX;
			$scheduling_from 	= $rs->Field("scheduling_from");
			$item_ci_type		= $rs->Field("ci_type");
			
			$lev4 = $rs->Field("n4v");
			$lev3 = $rs->Field("n3v");
			$lev2 = $rs->Field("n2v");
			$lev1 = $rs->Field("n1v");
			$lev0 = $rs->Field("n0v");
			
			$nodeKeyCrd = $lev0."_".$lev1."_".$lev2."_".$lev3."_".$lev4;	
			$listData[$idX] = array();
			if (isset($this->POTS["ASSETS"][$nodeKeyCrd])) {
				$listData[$idX] = $this->POTS["ASSETS"][$nodeKeyCrd];
			}

			$listData[$idX]["el_id"]	 			= $lev0.".".$lev1.".".$lev2.".".$lev3.".".$lev4;
			$listData[$idX]["el_filter"]	 		= $lev0."_".$lev1."_".$lev2."_".$lev3."_".$lev4;
			$listData[$idX]["is_favorites"]	 		= $favoritesCiData[$idX]["is_favorites"];
			
			$dtr = $this->controlFirstOrLastLearningItem($idX,$item_ci_type,$lev0,$lev1,$lev2,$lev3,$lev4);
			$listData[$idX] = array_merge($listData[$idX], $dtr);
			

    
			$listData[$idX]["CID"] 			= $idX;
			$listData[$idX]["idci"] 		= $idX;
			$listData[$idX]["id"] 			= $idX;
			
			$titleCI 			= $rs->Field("title");
			$filenameCI 		= $rs->Field("filename");
			$with_httpsCI 		= $rs->Field("with_https");
			
			$listData[$idX]["title"] 		= $rs->Field("title");
			$listData[$idX]["ci_type"] 		= $rs->Field("ci_type");
			
			$listData[$idX]["description"] 		= $rs->Field("description");
			$listData[$idX]["source"] 			= trim($rs->Field("source"));
			$listData[$idX]["source_author"] 	= trim($rs->Field("source_author"));

			if ($scheduling_from=='00.00.0000')	$scheduling_from = "";
			$listData[$idX]["scheduling_from"] = $scheduling_from;



			
					
			$hrefToDocTarget = "";
			$scheduling_from = $listData[$idX]["scheduling_from"];
			
			$titleToDisplay = $listData[$idX]["title"];
			$listData[$idX]["titleToAlt"] = $titleToDisplay;
			$listData[$idX]["titleToDisplay"] = $titleToDisplay;
			$listData[$idX]["ew_title"] =$listData[$idX]["title"];
			
			$description = 	$listData[$idX]["description"];
			$nr_char_description = 120;
			$listData[$idX]["abstractToDisplay"] 		= $description;					
			$listData[$idX]["short_description"] 		= $description;	
			
			if ($listData[$idX]["abstractToDisplay"]!="")  {
				$listData[$idX]["dp_abst"] = "yes";
				$descriptionfull = $description;
				$descriptionfull = strip_tags($descriptionfull);
				$descriptionfull = mb_substr($descriptionfull,0, $nr_char_description, 'utf-8');
				$pos = strrpos($descriptionfull, " ");
				if ($pos === false) 
						$descriptionfull .= "...";
				else	$descriptionfull = substr($descriptionfull,0, $pos)."...";
				$listData[$idX]["short_description"] = $descriptionfull;						
			} else $listData[$idX]["dp_abst"] = "no";	

			$listData[$idX]["dp_date"] = "no";
			if ($scheduling_from!="" && $this->param["display_date"]=="yes") {
					$listData[$idX]["dateToDisplay"] = $scheduling_from;
					$listData[$idX]["dp_date"] = "yes";
			} 
			$listData[$idX]["dp_source"] = "no";
			if ($listData[$idX]["source"]!="" && $this->param["display_source"]=="yes") {
						$listData[$idX]["sourceToDisplay"] = $listData[$idX]["source"];
						$listData[$idX]["dp_source"] = "yes";
			}  else 	$listData[$idX]["sourceToDisplay"] = "";			
			
			$listData[$idX]["dp_author"] = "no";
			if ($listData[$idX]["source_author"]!="" ) { //&& $this->param["display_author"]=="yes"
					$listData[$idX]["AuthorToDisplay"] = $listData[$idX]["source_author"];
					$listData[$idX]["dp_author"] = "yes";
			} else 	$listData[$idX]["AuthorToDisplay"] ="";
			
			$listData[$idX]["ew_source_author"] = $listData[$idX]["AuthorToDisplay"];
			$listData[$idX]["dp_image"] = "no";
		//	$listData[$idX]["linkToimage"] = "no";

			$imageSm_id = $rs->Field("imageSm_id");
			$imageBg_id = $rs->Field("imageBg_id");
			$nodeImageId = $rs->Field("imageSm_id_node");
			$listData[$idX]["nodeImageId"] = $nodeImageId;
			
			if(defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP=="Y") {
				$listData[$idX]["nodeClass"] = $rs->Field("boostrap_class");
				$listData[$idX]["nodeIco"] = $rs->Field("boostrap_ico");
			}
			$imgID = "";
			$imgIDName = "";
			
			
		//	boostrap_class		 boostrap_ico 
			
			if ($imageSm_id!="" && $imageSm_id>"0") {
				$ciDoublinCoreProp["dp_image"] 			= "yes";
				$imgID = $imageSm_id;
				$imgIDName = $imageSm_id_name;
			} elseif ($imageBg_id!="" && $imageBg_id>"0") {
				$ciDoublinCoreProp["dp_image"] 			= "yes";
				$imgID 		= $imageBg_id;
				$imgIDName 	= $imageBg_id_name;
			}
			
			if ($listData[$idX]["nodeImageId"] >0) {
				if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") {
					//thir griden
					$prop = CiManagerFe::get_SL_CACHE_INDEX($listData[$idX]["nodeImageId"]);	
					//print_r($prop);
					if (isset($prop["link_url"]) && $prop["link_url"]!='')
						$listData[$idX]["srcNodeImageToDisplay"] = $prop["link_url"];
					else {
						$listData[$idX]["srcNodeImageToDisplay"] = APP_URL."show_image.php?file_id=".$listData[$idX]["nodeImageId"];
					}
				}	
				if ($imgID>0) {
				} else $imgID = $listData[$idX]["nodeImageId"];
			}			
			
			
			
			
			if ($imgID != "") {
				$listData[$idX]["dp_image_ID"] 			= "$imgID";
				if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") {
					CiManagerFe::get_SL_CACHE_INDEX($imgID, "","");		
				} else {				
					$listData[$idX]["srcImageToDisplay"] = APP_URL."show_image.php?file_id=".$imgID;
					IF ($global_cache_dynamic == "Y") {
						$listData[$idX]["srcImageToDisplay"] = $cacheDyn->get_SlDocTitleToUrl($imgID, $imgIDName);
					} elseif ($application_is_cached=="yes") {
					}	
				}
				//if (isset($this->display_link_in["image"])) 
				//	$listData[$idX]["linkToimage"] 			= "yes";			
			}

			$listData[$idX]["imgID"] = $imgID;
			$listData[$idX]["dp_image_ID"] = $imgID;

			$descr = $workingCi->getDescriptors($item_ci_id, $this->thisModeCode);
			$listData[$idX] = array_merge($listData[$idX],$descr);
			
			
			/*$tmpI = $this->findPathToDiplayByCiType($item_ci_id,$item_ci_type, $lev0,$lev1,$lev2,$lev3,$lev4);
			
			echo "<textarea>";
			print_r("$item_ci_id,$item_ci_type, $lev0,$lev1,$lev2,$lev3,$lev4");
			print_r($tmpI);
			echo "</textarea>";
			
			$listData[$idX]["filterElInfo"] = $tmpI["filterElInfo"];
			$listData[$idX]["full_path_lecture"] = $tmpI["primary_path"];
			$listData[$idX]["node_description"] = $tmpI["ci_path_lecture"][$item_ci_id];

			$koord_level_node 	 					 = "$lev0,$lev1,$lev2,$lev3,$lev4";
			$listData[$idX]["koord_level_node"] 	= "$lev0,$lev1,$lev2,$lev3,$lev4";

			$listData[$idX]["dp_linkNivelLabel"] 	= "no";
			$listData[$idX]["dp_labelUserDefinedLink"] = "no";
					
			$hrefToDocTarget = "";
			$scheduling_from = $listData[$id]["scheduling_from"];
			$hrefTo = "javascript:GoTo('thisPage?event=none.ch_state(k=".$idX.")');";

			if ($global_cache_dynamic == "Y") {
					$hrefTo = $cacheDyn->get_CiTitleToUrl($idX, $this->ln_searchID, $titleCI, $filenameCI,"",$with_httpsCI);
			} elseif (defined("Caching_Metatags") AND (Caching_Metatags == "Y")) {
				if ($application_is_cached=="yes") {
					$destination= $cache->CgetCachedName($idX);
					if ($destination!="undefined") 
						$hrefTo = APP_URL.$cache->CACHE_PHP_PATH.$destination.".php";
				}
			}				
			
			if ($lev0!=$session->Vars["level_0"] && ($item_ci_type=="EL" || $item_ci_type=="EC" || $item_ci_type=="EL")) {
				$hrefTo = $this->URLS_EXTENDED["details_lecture"];
			}
			
			$listData[$idX]["hrefToDoc"] = $hrefTo;
			$listData[$idX]["hrefToDocTarget"] = $hrefToDocTarget;	*/			
			
			
			
			
			
			
			$chstateParams = array();
			$chstateExtraParams = array();
			
			$chstateParams["k"] = "";
			$chstateParams["kc"] = "";
			//$chstateExtraParams["idElC"] = "";

			/*if ($lev0==$session->Vars["level_0"]) {
					//$listData[$idX]["hrefToModuleOrLecture"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$idX.")');";
					$chstateParams["k"] = $idX;
			} else {
				if ($ci_target!="") {
					$trg = explode(",",$ci_target);
					if (count($trg)==1) {
							//$listData[$idX]["hrefToModuleOrLecture"] = "javascript:GoTo('thisPage?event=none.ch_state(kc={{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}};k=".$ci_target.";idElC=".$idX.")');";
							//$listData[$idX]["hrefToModuleOrLecture"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$ci_target.";idElC=".$idX.")');";
							
					} elseif (count($trg)==5) {
						//$listData[$idX]["hrefToModuleOrLecture"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$ci_target.";idElC=".$idX.")');";
					} else {
						//$listData[$idX]["hrefToModuleOrLecture"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$ci_target.";idElC=".$idX.")');";
					}
					
					$chstateParams["k"] = $ci_target;
					$chstateExtraParams["idElC"] = $idX;
				} else {
					$listData[$idX]["hrefToModuleOrLecture"] = "javascript:GoTo('thisPage?event=none.ch_state(kc={{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}};k=".$defaultTargetPages.";idElC=".$idX.")');";
					$chstateParams["k"] = $defaultTargetPages;
					$chstateParams["kc"] = "{{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}}";
					$chstateExtraParams["idElC"] = $idX;				
				}
			}*/
			
	
			

			//if (isset($this->appRelSt["elearningInfo"]["step"]) && ($this->appRelSt["elearningInfo"]["step"]=='EL' || $this->appRelSt["elearningInfo"]["step"]=='EC') ) {
					


		//	print_r($session->Vars["level_0"].":level_0<br>".$lev0.":lev0<br>");


					
			
				/*if ($lev0==$session->Vars["level_0"] || (isset($this->POTS["eltypesB"][$idX]) && $this->POTS["eltypesB"][$idX]>0)) {
					
					$listData[$idX]["hrefToModuleOrLecture"] = "javascript:GoTo('thisPage?event=none.ch_state(kc={{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}};k=".$idX.";idElC=".$idX.")');";
					$chstateParams["k"] = $idX;
					$chstateParams["kc"] = "{{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}}";
					$chstateExtraParams["idElC"] = $idX;					
				
				} else {*/
					
					//inside Lecture items from repository
					
					if ($lev4>0)			$keyL = 3;
					elseif ($lev3>0)		$keyL = 2;
					elseif ($lev2>0)		$keyL = 4;
					elseif ($lev1>0) 		$keyL = 0;
					else 					$keyL = 0;	
					
					if ($keyL==0) 	  $lectureKeyToControl = $lev0."_0_0_0_0";
					elseif ($keyL==1) $lectureKeyToControl = $lev0."_".$lev1."_0_0_0";
					elseif ($keyL==2) $lectureKeyToControl = $lev0."_".$lev1."_".$lev2."_0_0";
					elseif ($keyL==3) $lectureKeyToControl = $lev0."_".$lev1."_".$lev2."_".$lev3."_0";
					elseif ($keyL==4) $lectureKeyToControl = $lev0."_".$lev1."_".$lev2."_".$lev3."_".$lev4."";
					

					$itemId 				= $idX;													//idElC
					$itemBorrowedId 		= $idX;													//k
					$nodeToDisplayedItem 	= "{{lev0}},{{lev1}},{{lev2}},{{lev3}},{{lev4}}";		//kc
					$chstateParams = array();
					
					global $session;
					if (isset($this->POTS["eltypesB"][$itemId]) && $this->POTS["eltypesB"][$itemId]["borrowed_id"]>0) {
						 $itemBorrowedId = $this->POTS["eltypesB"][$itemId]["borrowed_id"];
					
						/*echo "<pre>";
						print_r($itemId.":itemId\n".$itemBorrowedId.":itemBorrowedId");
						print_r($this->POTS["eltypesB"][$itemId]);
						echo "</pre>";*/
						if (isset($this->POTS["EL_coord"][$itemId])) {
							$nodeToDisplayedItem = implode(",",explode("_",$this->POTS["EL_coord"][$itemId]));
						//	print_r($nodeToDisplayedItem);
						}

						$chstateParams["k"] 			= $itemBorrowedId;
						$chstateParams["kc"] 			= $nodeToDisplayedItem;
						$chstateExtraParams["idElC"] 	= $itemId;		
										
					} elseif ($lev0!=$session->Vars["level_0"]) { 
					
						if (isset($this->POTS["EL"][$lectureKeyToControl])) {
							
							$itemToCheckC = $this->POTS["EL"][$lectureKeyToControl];
							 
							if (isset($this->POTS["BORRFROM"][$session->Vars["level_0"]][$itemToCheckC]) 
									&& $this->POTS["BORRFROM"][$session->Vars["level_0"]][$itemToCheckC]>0) {
							
							
								//$itemBorrowedId = $this->POTS["EL"][$itemToCheckC];
								
								//if (isset($this->POTS["eltypesB"][$itemToCheck]) && $this->POTS["eltypesB"][$itemToCheck]["borrowed_id"]>0) {



								//	$itemBorrowedId = $this->POTS["eltypesB"][$itemToCheck]["borrowed_id"];
									$itemId = $this->POTS["BORRFROM"][$session->Vars["level_0"]][$itemToCheckC];

									if (isset($this->POTS["EL_coord"][$itemId])) {
										$nodeToDisplayedItem = implode(",",explode("_",$this->POTS["EL_coord"][$itemId]));
									//	print_r($nodeToDisplayedItem);
									}	
								//}
						
							}
						
						
						
						
						/*	echo "<pre>";
							print_r($lectureKeyToControl.":lectureKeyToControl\n");
							print_r($itemToCheckC.":itemToCheckC\n");
							print_r($this->POTS["BORRFROM"][$session->Vars["level_0"]][$itemToCheckC],":itemToCheckC\n");
							print_r($itemBorrowedId.":itemBorrowedId\n");
							print_r($this->POTS["EL_coord"][$itemBorrowedId]."-----\n");
							print_r($this->POTS["EL_coord"]);

							//print_r($itemToCheck.":itemToCheck\n");
							print_r($nodeToDisplayedItem.":nodeToDisplayedItem\n");
							print_r($itemId.":itemId\n".$itemBorrowedId.":itemBorrowedId");
							print_r($this->POTS["eltypesB"][$itemId]);
							echo "</pre>";	*/
							
								
							
							
						
						}
						
						
/*$this->appRelSt["elearningInfo"]
    [EL] => Array
            [12_6_1_0_0] => 106

        )
*/						
						
						
						
						
						$chstateParams["k"] 			= $itemBorrowedId;
						$chstateParams["kc"] 			= $nodeToDisplayedItem;
						$chstateExtraParams["idElC"] 	= $itemId;	

					} else {
						
						$chstateParams["k"] = $itemBorrowedId;					
					
					}
					


					
					
					/*echo "<textarea>";
					print_r($lectureKeyToControl.":\n$keyL:keyL\n:".$lev0."_".$lev1."_".$lev2."_".$lev3."_".$lev4.":KOORD\n$nodeKeyCrd:nodeKeyCrd\n");
					print_r($this->POTS["ELR"]);
					echo "</textarea>";*/
			//	}
			
			/*} else {
			
					//$listData[$idX]["hrefToModuleOrLecture"] = "javascript:GoTo('thisPage?event=none.ch_state(kc={{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}};k=".$idX.";idElC=".$idX.")');";
					$chstateParams["k"] = $idX;			
			}*/
			

/*
    [eltypesB] => Array
        (
            [292] => Array
                (
                    [borrowed_mode] => borrowfrom_structure
                    [borrowed_id] => 106
                    [borrowed_crd] => 12.6.1.0.0
                    [use_borrowed_metadata] => yes
                    [borrowed_zone_flag] => anotherZone
                    [zoneID] => 0
                    [borrowed_zoneID] => 12
                )
*/					
					
			$listData[$idX]["hrefToModuleOrLecture"] = $this->createDynamicOrCachedLinked($chstateParams,$chstateExtraParams,$this->lngId);
			$tmpInfoArray["list"]["CI_DATA"][$item_ci_id] = $listData[$item_ci_id];
			$tmpInfoArray["groupedType"][$item_ci_type][$item_ci_id] = $item_ci_id;		
			$gridDataSrcAll["data"][$indG++] 	= $listData[$idX];
					
			$rs->MoveNext();
		}
		
		/*echo "<textarea>";
		print_r($this->appRelSt);
		print_r($this->POTS);
		echo "</textarea>";	*/	
		
		
		
		
		
		
		$gridDataSrcAll["AllRecs"] 	= count($gridDataSrcAll["data"]);
		WebApp::addVar("listOfItemsCiGrid_$gridKey",$gridDataSrcAll);


		$this->tmpInfoArray = $tmpInfoArray;
		//nese kemi qe duhet te shfaqim extended prop ose content, duhet te gjejme propertite extended per cdo type dokumenti
		if (isset($tmpInfoArray["groupedType"]) && count($tmpInfoArray["groupedType"])>0) {
			
			
			
			
			
			
			
			while (list($ci_type_grouped,$ci_type_ids)=each($tmpInfoArray["groupedType"])) {
				
				
				$idsCiGroupedByType = implode(",",$ci_type_ids);
				$properties_extended = array();
				$properties_extended = CiManagerFe::getExtMultiProperties($ci_type_grouped,$this->thisModeCode, $idsCiGroupedByType,$this->lang,$this->lngId);
				$properties_InternalTargets = CiManagerFe::getInternalTargets($idsCiGroupedByType,$this->lngId,$this->thisModeCode);
				
				if (count($properties_extended)>0) {
					//merge general  prop with extended prop
					reset($properties_extended);
					while (list($ciExtendedID,$ciExtendedIDprop)=each($properties_extended)) {
						/*if (isset($ciExtendedIDprop["file_id"]) && $ciExtendedIDprop["file_id"]>0) {
						}
						if (is_array($ciExtendedIDprop) && is_array($tmpInfoArray["list"]["CI_DATA"][$ciExtendedID]))
							$tmpInfoArray["list"]["CI_DATA"][$ciExtendedID] 	= array_merge($tmpInfoArray["list"]["CI_DATA"][$ciExtendedID], $ciExtendedIDprop);	
						if (isset($properties_InternalTargets[$ciExtendedID])) {
							$tmpInfoArray["list"]["CI_DATA"][$ciExtendedID] 	= array_merge($tmpInfoArray["list"]["CI_DATA"][$ciExtendedID], $properties_InternalTargets[$ciExtendedID]);	
						}*/
						$extendedProp["data"][0] = 	$ciExtendedIDprop;
						$extendedProp["AllRecs"] 	= count($extendedProp["data"]);
						WebApp::addVar("extendedProp_".$ciExtendedID,$extendedProp);							
					}
				}
			}
		}
		return $gridDataSrcAll;
	}
    function findPathToDiplayByCiType ($item_ci_id,$item_ci_type, $lev0,$lev1,$lev2,$lev3,$lev4) {
		global $session;
		
		$ci_path_lecture	= array();
		$full_path_lecture	= array();
		$fullPathInfo 		= array();
		$filterElInfo 		= array();
		$programeTreeStructure = $this->POTS;
		$in = 0;
		$gridOfPath["data"] = array();
		if ($item_ci_type=="EC") { 
			if (isset($this->programeTreeStructure["EC"][$lev0."_".$lev1."_".$lev2])) {
				$cid = $this->programeTreeStructure["EC"][$lev0."_".$lev1."_".$lev2];
				$fullPathInfo[] = $programeTreeStructure["ci"][$item_ci_id]["ND"];
				
				$gridOfPath["data"][$in]["ci_type"] = "EC";
				$gridOfPath["data"][$in]["ci_id"] = $cid;
				$gridOfPath["data"][$in]["ND"] = $programeTreeStructure["ci"][$item_ci_id]["ND"];
				$gridOfPath["data"][$in++]["TT"] = $programeTreeStructure["ci"][$item_ci_id]["TT"];
				
				//$gridOfPath
			}
			if (isset($programeTreeStructure["PR_parents_EC"][$item_ci_id]) && count($programeTreeStructure["PR_parents_EC"][$item_ci_id])>0)	{
				reset($programeTreeStructure["PR_parents_EC"][$item_ci_id]);
				while (list($key,$ciRef)=each($programeTreeStructure["PR_parents_EC"][$item_ci_id])) {
					if (isset($programeTreeStructure["PR_coord"][$ciRef])) {
						$filterElInfo[] =  "f_".$programeTreeStructure["PR_coord"][$ciRef];
					}
				}
			}
			
			$full_path_lecture["ci_path_lecture"][$item_ci_id]	= $programeTreeStructure["ci"][$item_ci_id]["ND"];
			
		} elseif($item_ci_type=="EL") { 
			
			if (isset($programeTreeStructure["EC"][$lev0."_".$lev1."_".$lev2])) {
				$cid_ec = $programeTreeStructure["EC"][$lev0."_".$lev1."_".$lev2];
				$fullPathInfo[] = $programeTreeStructure["ci"][$cid_ec]["ND"];
				$gridOfPath["data"][$in]["ci_type"] = "EC";
				$gridOfPath["data"][$in]["ci_id"] = $cid_ec;
				$gridOfPath["data"][$in]["ND"] = $programeTreeStructure["ci"][$cid_ec]["ND"];
				$gridOfPath["data"][$in++]["TT"] = $programeTreeStructure["ci"][$cid_ec]["TT"];			
				
			}
			if (isset($programeTreeStructure["EL"][$lev0."_".$lev1."_".$lev2."_".$lev3])) {
				$cid_el = $programeTreeStructure["EL"][$lev0."_".$lev1."_".$lev2."_".$lev3];
				$fullPathInfo[] = $programeTreeStructure["ci"][$cid_el]["ND"];
				$gridOfPath["data"][$in]["ci_type"] = "EL";
				$gridOfPath["data"][$in]["ci_id"] = $cid_el;
				$gridOfPath["data"][$in]["ND"] = $programeTreeStructure["ci"][$cid_el]["ND"];
				$gridOfPath["data"][$in++]["TT"] = $programeTreeStructure["ci"][$cid_el]["TT"];				
				
			}
			if (isset($programeTreeStructure["EC_parents_El"][$item_ci_id]) && count($programeTreeStructure["EC_parents_El"][$item_ci_id])>0)	{
				reset($programeTreeStructure["EC_parents_El"][$item_ci_id]);
				while (list($key,$ciRef)=each($programeTreeStructure["EC_parents_El"][$item_ci_id])) {
					if (isset($programeTreeStructure["EC_coord"][$ciRef])) {
						$filterElInfo[] =  "f_".$programeTreeStructure["EC_coord"][$ciRef];
					}
				}
			}

			$full_path_lecture["ci_path_lecture"][$item_ci_id]	= $programeTreeStructure["ci"][$item_ci_id]["ND"];


		} elseif (in_array($item_ci_type,$this->pathLectureInfo["lectureAllowedTypes"])) {
			
			if (isset($programeTreeStructure["PR_coord_r"][$lev0."_".$lev1])) {
				$cid = $programeTreeStructure["PR_coord_r"][$lev0."_".$lev1];
				//$fullPathInfo[] = $programeTreeStructure["ci"][$cid]["ND"];
				
				$gridOfPath["data"][$in]["ci_type"] = "PR";
				$gridOfPath["data"][$in]["ci_id"] = $cid;
				$gridOfPath["data"][$in]["ND"] = $programeTreeStructure["ci"][$cid]["ND"];
				$gridOfPath["data"][$in++]["TT"] = $programeTreeStructure["ci"][$cid]["TT"];						
			}


			if (isset($programeTreeStructure["EC"][$lev0."_".$lev1."_".$lev2])) {
				$cid = $programeTreeStructure["EC"][$lev0."_".$lev1."_".$lev2];
				$fullPathInfo[] = $programeTreeStructure["ci"][$cid]["ND"];
				
				
				$gridOfPath["data"][$in]["ci_type"] = "EC";
				$gridOfPath["data"][$in]["ci_id"] = $cid;
				$gridOfPath["data"][$in]["ND"] = $programeTreeStructure["ci"][$cid]["ND"];
				$gridOfPath["data"][$in++]["TT"] = $programeTreeStructure["ci"][$cid]["TT"];						
			}
			
			if (isset($programeTreeStructure["EL"][$lev0."_".$lev1."_".$lev2][$lev3])) {
				$cid = $programeTreeStructure["EL"][$lev0."_".$lev1."_".$lev2][$lev3];
				$fullPathInfo[] = $programeTreeStructure["ci"][$cid]["ND"];
				$gridOfPath["data"][$in]["ci_type"] = "EL";
				$gridOfPath["data"][$in]["ci_id"] = $cid_el;
				$gridOfPath["data"][$in]["ND"] = $programeTreeStructure["ci"][$cid_el]["ND"];
				$gridOfPath["data"][$in++]["TT"] = $programeTreeStructure["ci"][$cid_el]["TT"];						
			}
			
			
		} else  if ($item_ci_type=="NI" || $item_ci_type=="CM") {
		
			if (isset($programeTreeStructure["TC"][$lev0."_".$lev1."_".$lev2][$lev3])) {
				$cid = $programeTreeStructure["TC"][$lev0."_".$lev1."_".$lev2][$lev3];
				$fullPathInfo[] = $programeTreeStructure["ci"][$cid]["ND"];
				if (isset($programeTreeStructure["EC"][$lev0."_".$lev1."_".$lev2])) {
					$cid = $programeTreeStructure["EC"][$lev0."_".$lev1."_".$lev2];
					$fullPathInfo[] = $programeTreeStructure["ci"][$cid]["ND"];
				}					
			} elseif (isset($programeTreeStructure["EL"][$lev0."_".$lev1."_".$lev2][$lev3])) {
				if (isset($programeTreeStructure["EC"][$lev0."_".$lev1."_".$lev2])) {
					$cid = $programeTreeStructure["EC"][$lev0."_".$lev1."_".$lev2];
					$fullPathInfo[] = $programeTreeStructure["ci"][$cid]["ND"];
				}					
				$cid = $programeTreeStructure["EL"][$lev0."_".$lev1."_".$lev2][$lev3];
				$fullPathInfo[] = $programeTreeStructure["ci"][$cid]["ND"];				
			
			} elseif (isset($programeTreeStructure["EC"][$lev0."_".$lev1."_".$lev2])) {
				$cid = $programeTreeStructure["EC"][$lev0."_".$lev1."_".$lev2];
				$fullPathInfo[] = $programeTreeStructure["ci"][$cid]["ND"];
			
			} elseif (isset($programeTreeStructure["PR"][$lev0."_".$lev1])) {
				$cid = $programeTreeStructure["PR"][$lev0."_".$lev1];
				$fullPathInfo[] = $programeTreeStructure["ci"][$cid]["ND"];
			
			} elseif (isset($programeTreeStructure["TT"][$lev0."_".$lev1])) {
				$cid = $programeTreeStructure["TT"][$lev0."_".$lev1];
				$fullPathInfo[] = $programeTreeStructure["ci"][$cid]["ND"];
			
			} 			
		}
		$full_path_lecture["fullPathInfo"]	= $fullPathInfo;
		if (count($fullPathInfo)>0)
				$full_path_lecture["primary_path"]	= implode(" / ",$fullPathInfo);
		else	$full_path_lecture["primary_path"]	= "";
			
		if (count($filterElInfo)>0)
				$full_path_lecture["filterElInfo"]	= implode(",",$filterElInfo);
		else	$full_path_lecture["filterElInfo"]	= "";
		
		if (count($gridOfPath["data"])>0) {
			$gridOfPath["AllRecs"] 	= count($gridOfPath["data"]);
			WebApp::addVar("fullPathToGrid_$item_ci_id",$gridOfPath);
		}		
		return $full_path_lecture;
    } 
	function getReferenceIdToCollectItems($typeToFindCoord='',$allowedTypeElearning = "all"){
        global $session;

		if (isset($this->ci_type_configuration) && $this->ci_type_configuration=="PR") {

				$hierarchy_level 	= $this->appRelSt["hierarchy_level"][$this->cidFlow];
				$coord 				= $this->appRelSt["coord"][$this->cidFlow];
				$id 				= $this->cidFlow;
				
				if (isset($this->POTS["PR"]["programe"][$id])) 
					$this->ReferenceToCollectItemsConf = "program";
				elseif (isset($this->POTS["PR"]["current_topic"][$id])) 
					$this->ReferenceToCollectItemsConf = "current_topic";
					
				$this->ReferenceToCollectItemsType = "PR";
				$this->ReferenceToCollectItemsId = $id;

		} elseif (isset($session->Vars["idElC"]) && isset($this->appRelSt[$session->Vars["idElC"]])
				&& $this->appRelSt[$session->Vars["idElC"]]["ci_type"]=="PR"
				)  {

				$hierarchy_level 	= $this->appRelSt[$session->Vars["idElC"]]["hierarchy_level"];
				$coord 				= $this->appRelSt[$session->Vars["idElC"]]["coord"];
				$id 				= $session->Vars["idElC"];

				if (isset($this->POTS["PR"]["programe"][$id])) 
					$this->ReferenceToCollectItemsConf = "program";
				elseif (isset($this->POTS["PR"]["current_topic"][$id])) 
					$this->ReferenceToCollectItemsConf = "current_topic";
				
				$this->ReferenceToCollectItemsType = "PR";
				$this->ReferenceToCollectItemsId = $id;

		} elseif (isset($this->ci_type_configuration) && $this->ci_type_configuration=="EC") {

				$hierarchy_level 	= $this->appRelSt["hierarchy_level"][$this->cidFlow];
				$coord 				= $this->appRelSt["coord"][$this->cidFlow];
				$id 				= $this->cidFlow;

				if (isset($this->POTS["EC_coord"][$id])) {
					$this->ReferenceToCollectItemsConf = "course";	
					$this->ReferenceToCollectItemsId = $id;
					$this->ReferenceToCollectItemsType = "EC";
				}

		} elseif (isset($session->Vars["idElC"]) && isset($this->appRelSt[$session->Vars["idElC"]])
				&& $this->appRelSt[$session->Vars["idElC"]]["ci_type"]=="EC"
				)  {

				$hierarchy_level 	= $this->appRelSt[$session->Vars["idElC"]]["hierarchy_level"];
				$coord 				= $this->appRelSt[$session->Vars["idElC"]]["coord"];
				$id 				= $session->Vars["idElC"];

				if (isset($this->POTS["EC_coord"][$id])) {
					$this->ReferenceToCollectItemsConf = "course";	
					$this->ReferenceToCollectItemsId = $id;
					$this->ReferenceToCollectItemsType = "EC";
				}
					
		} elseif (isset($this->ci_type_configuration) && $this->ci_type_configuration=="EL") {

				$hierarchy_level 	= $this->appRelSt["hierarchy_level"][$this->cidFlow];
				$coord 				= $this->appRelSt["coord"][$this->cidFlow];
				$id 				= $this->cidFlow;


				if (isset($this->POTS["EL_coord"][$id])) {
					$this->ReferenceToCollectItemsConf = "lecture";
					$this->ReferenceToCollectItemsId = $id;
					$this->ReferenceToCollectItemsType = "EL";
				}

		} elseif (isset($session->Vars["idElC"]) && isset($this->appRelSt[$session->Vars["idElC"]])
				&&	$this->appRelSt[$session->Vars["idElC"]]["ci_type"]=="EL"
				)  {

				$hierarchy_level 	= $this->appRelSt[$session->Vars["idElC"]]["hierarchy_level"];
				$coord 				= $this->appRelSt[$session->Vars["idElC"]]["coord"];					
				$id 				= $session->Vars["idElC"];

				if (isset($this->POTS["EL_coord"][$id])) {
					$this->ReferenceToCollectItemsConf = "lecture";					
					$this->ReferenceToCollectItemsId = $id;
					$this->ReferenceToCollectItemsType = "EL";
				}
		} elseif (isset($this->referenceCiForDetails)) {		
			if (isset($this->POTS["CI_TYPE"][$this->referenceCiForDetails])) {
				
					$this->ReferenceToCollectItemsId = $this->referenceCiForDetails;
					$this->ReferenceToCollectItemsType = $this->POTS["CI_TYPE"][$this->referenceCiForDetails];			
			}
		}
		if (isset($this->ReferenceToCollectItemsType)) {
			if ($this->ReferenceToCollectItemsType=="PR") {

				if (isset($this->POTS["PR_coord"][$this->ReferenceToCollectItemsId]))
					$this->ReferenceToCollectItemsCoord = $this->POTS["PR_coord"][$this->ReferenceToCollectItemsId];
			} elseif ($this->ReferenceToCollectItemsType=="EC") {
				if (isset($this->POTS["EC_coord"][$this->ReferenceToCollectItemsId]))
					$this->ReferenceToCollectItemsCoord = $this->POTS["EC_coord"][$this->ReferenceToCollectItemsId];

			} elseif ($this->ReferenceToCollectItemsType=="EL") {
				if (isset($this->POTS["EL_coord"][$this->ReferenceToCollectItemsId]))
					$this->ReferenceToCollectItemsCoord = $this->POTS["EL_coord"][$this->ReferenceToCollectItemsId];
			}
		} 
		/*echo "NI<textarea>";
			print_r($allowedTypeElearning);
			print_r($this->ReferenceToCollectItemsCoord);
			print_r($this->ReferenceToCollectItemsType);
			print_r($this->ReferenceToCollectItemsId);
		echo "</textarea>";*/
		
		if ($allowedTypeElearning == "all" ||  $this->ReferenceToCollectItemsType==$allowedTypeElearning) {
		} else {
			$coord_to_return = array();
			return $coord_to_return;
		}
		if (isset($this->ReferenceToCollectItemsCoord)) {
		
			$coordToConditionA = explode("_",$this->ReferenceToCollectItemsCoord);
			$level = count($coordToConditionA);
			
			$condition = array();
				if ($typeToFindCoord=="NI")	$ci_type_rel = "'NC'";
			elseif ($typeToFindCoord=="RI")	$ci_type_rel = "'RA'";
			elseif ($typeToFindCoord=="CM")	$ci_type_rel = "'CM'";
			else return;
			
			$condition[] = " ci_type in (".$ci_type_rel.")";
			if ($level==2) {
				$condition[] = "c.id_zeroNivel = '".$coordToConditionA[0]."'";
				$condition[] = "c.id_firstNivel = '".$coordToConditionA[1]."'";
				$condition[] = "c.id_secondNivel > '0'";
				$condition[] = "c.id_thirdNivel = '0'";
				$condition[] = "c.id_fourthNivel = '0'";
			} elseif ($level==3) {
				$condition[] = "c.id_zeroNivel 		= '".$coordToConditionA[0]."'";
				$condition[] = "c.id_firstNivel 		= '".$coordToConditionA[1]."'";
				$condition[] = "c.id_secondNivel 		= '".$coordToConditionA[2]."'";
				$condition[] = "c.id_thirdNivel > '0'";
				$condition[] = "c.id_fourthNivel = '0'";				
			} elseif ($level==4) {
				$condition[] = "c.id_zeroNivel 		= '".$coordToConditionA[0]."'";
				$condition[] = "c.id_firstNivel 		= '".$coordToConditionA[1]."'";
				$condition[] = "c.id_secondNivel 		= '".$coordToConditionA[2]."'";
				$condition[] = "c.id_thirdNivel 		= '".$coordToConditionA[3]."'";
				$condition[] = "c.id_fourthNivel > '0'";				
			}
			
			$conditionToSql = " AND ".implode(" AND ", $condition);
			global $session;
		
			$stateCondition="";
			$expire_condition="";
			
			$ln = $session->Vars["lang"];
			$md = $session->Vars["thisMode"];
			
			if ($session->Vars["thisMode"]=='') {
				$stateCondition=" 
					AND c.state".$session->Vars["lang"]." not in (0,5,7) 
					AND c.content".$session->Vars["lang"]."".$md." IS NOT NULL
					AND n4.active".$session->Vars["lang"]." != 1
					AND n4.state".$session->Vars["lang"]." != 7 
					AND n4.description".$session->Vars["lang"].$session->Vars["thisMode"]."  IS NOT NULL ";
			} else {
				$stateCondition=" AND n4.state".$session->Vars["lang"]." != 7  AND c.state".$session->Vars["lang"]." not in (7)";
			}	

				$cnt_data_get_documents = "	
					SELECT c.content_id as cid, c.id_zeroNivel as l0, c.id_firstNivel as l1, 
							c.id_secondNivel as l2, c.id_thirdNivel as l3,c.id_fourthNivel as l4,
							IF(nivel_4.description".$session->Vars["lang"]." IS NULL, '', nivel_4.description".$session->Vars["lang"].") as actualNodeDescription,
							title".$session->Vars["lang"]." as title, ci_type
					  FROM content as c
					  JOIN nivel_4 
						ON nivel_4.id_zeroNivel 	= c.id_zeroNivel
					   AND nivel_4.id_firstNivel 	= c.id_firstNivel
					   AND nivel_4.id_secondNivel 	= c.id_secondNivel
					   AND nivel_4.id_thirdNivel 	= c.id_thirdNivel
					   AND nivel_4.id_fourthNivel 	= c.id_fourthNivel

					 WHERE orderContent = 0 ".$conditionToSql."
					   AND content_id in (
									SELECT distinct c.content_id
									 FROM				profil_rights	AS p
												JOIN	nivel_4			AS n4	ON (    p.id_zeroNivel   = n4.id_zeroNivel
																					AND p.id_firstNivel  = n4.id_firstNivel
																					AND p.id_secondNivel = n4.id_secondNivel
																					AND p.id_thirdNivel  = n4.id_thirdNivel
																					AND p.id_fourthNivel = n4.id_fourthNivel
																					)
												JOIN	content			AS c	ON (    n4.id_zeroNivel  = c.id_zeroNivel
																					AND n4.id_firstNivel  = c.id_firstNivel
																					AND n4.id_secondNivel = c.id_secondNivel
																					AND n4.id_thirdNivel  = c.id_thirdNivel
																					AND n4.id_fourthNivel = c.id_fourthNivel)
								WHERE p.profil_id in ('".$session->Vars["tip"]."')
								  	".$conditionToSql." AND orderContent = 0
									  ".$stateCondition."
									  ".$expire_condition."
													  
					  )
					GROUP BY c.content_id";	
			$coord_to_return = array();
			$rs_list = WebApp::execQuery($cnt_data_get_documents);
			while (!$rs_list->EOF()) {
				
				$n0 = $rs_list->Field("l0");
				$n1 = $rs_list->Field("l1");
				$n2 = $rs_list->Field("l2");
				$n3 = $rs_list->Field("l3");
				$n4 = $rs_list->Field("l4");
				$coord_to_return[] = $n0.",".$n1.",".$n2.",".$n3.",".$n4;
				$rs_list->MoveNext();
			}	
			return $coord_to_return;
		}
	}

	function getConditionToGetContentByCoord ($coord) {
		
        global $session;
		$nivel_condition = array();
		//kap hierarkine
		if ($coord[4] >0) {
			$nivel_condition[0] = " content.id_zeroNivel = '" . $coord[0] . "' ";
			$nivel_condition[1] = " content.id_firstNivel = '" . $coord[1] . "' ";
			$nivel_condition[2] = " content.id_secondNivel = '" . $coord[2] . "' ";
			$nivel_condition[3] = " content.id_thirdNivel = '" . $coord[3] . "' ";
			$nivel_condition[4] = " content.id_fourthNivel = '" . $coord[4] . "' ";
		} elseif ($coord[3] >0) {
			$nivel_condition[0] = " content.id_zeroNivel = '" . $coord[0] . "' ";
			$nivel_condition[1] = " content.id_firstNivel = '" . $coord[1] . "' ";
			$nivel_condition[2] = " content.id_secondNivel = '" . $coord[2] . "' ";
			$nivel_condition[3] = " content.id_thirdNivel = '" . $coord[3] . "' ";
			$nivel_condition[4] = " content.id_fourthNivel > 0 ";
		} elseif ($coord[2] >0) {
			$nivel_condition[0] = " content.id_zeroNivel = '" . $coord[0] . "' ";
			$nivel_condition[1] = " content.id_firstNivel = '" . $coord[1] . "' ";
			$nivel_condition[2] = " content.id_secondNivel = '" . $coord[2] . "' ";
			$nivel_condition[3] = " content.id_thirdNivel > 0 ";
			$nivel_condition[4] = " content.id_fourthNivel = 0 ";
		} elseif ($coord[1] >0) {
			$nivel_condition[0] = " content.id_zeroNivel = '" . $coord[0] . "' ";
			$nivel_condition[1] = " content.id_firstNivel = '" . $coord[1] . "' ";
			$nivel_condition[2] = " content.id_secondNivel >0 ";
			$nivel_condition[3] = " content.id_thirdNivel = 0 ";
			$nivel_condition[4] = " content.id_fourthNivel = 0 ";
		} else {
			$nivel_condition[0] = " content.id_zeroNivel = '" . $coord[0] . "' ";
		}
	}
    function getStructuredInformationDynEcc($coord,$ci_type,$cidFlow)
    {
        global $session;
		
		$appRelSt = array();

		$ciTypeChild = "";
		$nivel_condition = $this->getConditionToGetContentByCoord($coord);
		$kushtSql = implode(" AND ", $nivel_condition);

		if ($ci_type == "PR")		$kushtSql .= " AND ci_type = 'EC' " ;
		elseif ($ci_type == "EC")	$kushtSql .= " AND ci_type = 'EL' " ;
		elseif ($ci_type == "EL")	
				return $appRelSt;	//$kushtSql .= " AND ci_type in ('".implode("','",$this->pathLectureInfo["typeLecture"])."') " ;
		ELSE 	return $appRelSt;
			
		$sql_con = "SELECT content.content_id, ci_type, n.description".$session->Vars["lang"].$session->Vars["thisMode"]." as nodeName,
										content.id_zeroNivel, content.id_firstNivel,content.id_secondNivel,content.id_thirdNivel,content.id_fourthNivel,
										titleLng1 as title, n.orderMenu as nodeOrder

									  FROM content
								  
										JOIN nivel_4			AS n	ON     content.id_zeroNivel   = n.id_zeroNivel
																AND content.id_firstNivel  = n.id_firstNivel
																AND content.id_secondNivel = n.id_secondNivel
																AND content.id_thirdNivel  = n.id_thirdNivel
																AND content.id_fourthNivel = n.id_fourthNivel
										LEFT JOIN profil_rights ON        content.id_zeroNivel   = profil_rights.id_zeroNivel
														AND content.id_firstNivel  = profil_rights.id_firstNivel
														AND content.id_secondNivel = profil_rights.id_secondNivel
														AND content.id_thirdNivel  = profil_rights.id_thirdNivel
														AND content.id_fourthNivel = profil_rights.id_fourthNivel
														AND profil_rights.profil_id in (".$session->Vars["tip"].")

										LEFT JOIN profil_rights_ci ON content.content_id = profil_rights_ci.content_id AND profil_rights_ci.profil_id in (".$session->Vars["tip"].")	
								WHERE " . $kushtSql . "
								  AND orderContent   = '0'
								  AND content.state".$session->Vars["lang"]." not in (0,5,7)
								  AND content.published".$session->Vars["lang"]." = 'Y'
								  AND n.active".$session->Vars["lang"]." != '1'
								  AND n.state".$session->Vars["lang"]." != 7
								   GROUP BY content.content_id
								  ORDER BY content.id_zeroNivel, content.id_firstNivel,content.id_secondNivel,content.id_thirdNivel,n.orderMenu";
				$order = 1;
				$orderR = 1;
                $rs_con = WebApp::execQuery($sql_con);

                while (!$rs_con->EOF()) {

                    $ci_type 	= $rs_con->Field("ci_type");
                    $relid 		= $rs_con->Field("content_id");
                    $title 		= $rs_con->Field("title");
                    $nodeName 	= $rs_con->Field("nodeName");
                                      
                    $appRelSt["LECTURE_RELATED_ALL"][$ci_type][$relid] = $relid;
                    $appRelSt["CiInf"][$relid]["title"] = $title;
                    $appRelSt["CiInf"][$relid]["nodeDescription"] = $nodeName;

                    $appRelSt["CiInf"][$relid]["coord"][0] = $rs_con->Field("id_zeroNivel");
                    $appRelSt["CiInf"][$relid]["coord"][1] = $rs_con->Field("id_firstNivel");
                    $appRelSt["CiInf"][$relid]["coord"][2] = $rs_con->Field("id_secondNivel");
                    $appRelSt["CiInf"][$relid]["coord"][3] = $rs_con->Field("id_thirdNivel");
                    $appRelSt["CiInf"][$relid]["coord"][4] = $rs_con->Field("id_fourthNivel");
                    $rs_con->MoveNext();
                }
        return $appRelSt;
	}   
	
   
/*
Searching for: getLecturesDetails
include_php\oot.session.base.class.php(3073): $this->getLecturesDetails($lecturesIds,"'EC'","EC");
include_php\oot.session.base.class.php(3199): $this->getLecturesDetails($lecturesIds,"'EL'","LC");
*/
	function getLecturesDetails ($id_lectures,$ci_type="DI",$gridKey="") {	
		return;
		global $session;
			
		if (is_array($id_lectures) && $id_lectures>0) {		
			$lecturesIds = implode(",",$id_lectures);

			//formohen kushte per searchin, qe kane lidhje me termin e searchit
			$ln = $session->Vars["lang"];
			$md = $session->Vars["thisMode"];

			//formohen kushte per searchin, qe kane lidhje me gjendjen e aplikimit
			$stateCondition="";
			$expire_condition="";
			if ($session->Vars["thisMode"]=='') {
				$stateCondition=" 
					AND c.state".$session->Vars["lang"]." not in (0,5,7) 
					AND c.content".$session->Vars["lang"]."".$md." IS NOT NULL
					AND n4.active".$session->Vars["lang"]." != 1
					AND n4.state".$session->Vars["lang"]." != 7 
					AND n4.description".$session->Vars["lang"].$session->Vars["thisMode"]." IS NOT NULL ";
			} else {
				$stateCondition=" 
					AND c.content".$session->Vars["lang"]."".$md." IS NOT NULL
					AND c.state".$session->Vars["lang"]." not in (7)";
			}	

			$nodes_restriction_to_sql = " AND n4.id_zeroNivel in (".ZONE_AUTHORING.") AND c.content_id in (".$lecturesIds.")";
			$cnt_data_get_documents = "	
				SELECT group_concat(c.content_id) as ids, 1 as grp
				  FROM content as c
				 WHERE ci_type in (".$ci_type.")
				   AND content_id in (
				 				SELECT distinct c.content_id
				 				 FROM				profil_rights	AS p
				 							JOIN	nivel_4			AS n4	ON (    p.id_zeroNivel   = n4.id_zeroNivel
				 																AND p.id_firstNivel  = n4.id_firstNivel
				 																AND p.id_secondNivel = n4.id_secondNivel
				 																AND p.id_thirdNivel  = n4.id_thirdNivel
				 																AND p.id_fourthNivel = n4.id_fourthNivel
				 																)
				 							JOIN	content			AS c	ON (    n4.id_zeroNivel  = c.id_zeroNivel
				 																AND n4.id_firstNivel  = c.id_firstNivel
				 																AND n4.id_secondNivel = c.id_secondNivel
				 																AND n4.id_thirdNivel  = c.id_thirdNivel
				 																AND n4.id_fourthNivel = c.id_fourthNivel)
				 
				 			WHERE p.profil_id in (".$session->Vars["tip"].")
				 			  AND ci_type in (".$ci_type.")
				 			  ".$nodes_restriction_to_sql.$stateCondition.$expire_condition."
					 									  
				  )
				GROUP BY grp";	
			$rs_list = WebApp::execQuery($cnt_data_get_documents);
			IF (!$rs_list->EOF()) {
				$lecturesIdsToFindResult = explode(",",$rs_list->Field("ids"));
				$this->findCisExtendedInformation($lecturesIdsToFindResult,$gridKey);
			}					  
		}
	}        
    function getCrmsProfile($cme_id_authors)
    {   
    } 
    function readCrmData($params)
    {
		global $session;
    } 
   
	function findEwEnrollButton($id_ci) {	
		//ci_type
	}
	function findEventsExtendedInformation($filterByNodes,$filterByTime="",$gridKey="",$orderBy="",$sortBy="",$ci_target="",$isMeine="no",$my_ci_target="",$limit="",$moduleIdKeyToGrid="",$tutId="",$ShowCancelledEvents="no") {		
		global $session;
		require_once(INCLUDE_AJAX_PATH."CIExtended/TCExtended.Class.php");
								  //findEventsExtendedInformation($filterByNodes,$filterByTime="",	$gridKey="",$orderBy="",$sortBy="",	$ci_target="",	$isMeine="no",	$my_ci_target="",	$limit="",	$moduleIdKeyToGrid="",	$tutorialID="",	$ShowCancelledEvents="no")
		$dataToReturn = TCExtended::findEventsExtendedInformation($filterByNodes,$filterByTime,		$gridKey,	$orderBy,	$sortBy,	$ci_target,		$isMeine,		$my_ci_target,		$limit,		$moduleIdKeyToGrid,		$tutId,			$ShowCancelledEvents,$this->NEM_PROP);
		$listData = $dataToReturn["listData"];
		$this->tutorialEventsIdsToCatalog = $dataToReturn["tutorialEventsIdsToCatalog"];
		return $listData;
	}
	function controlFirstOrLastLearningItem ($cId,$item_ci_type,$lev0,$lev1,$lev2,$lev3,$lev4) {
			
		global $session;
		$dataToReturn = array();	
		$BoCondition = "";
		$md = $session->Vars["thisMode"];
		if ($session->Vars["thisMode"]=="_new") { //a paaprovuar
			$BoCondition = "AND nivel_4.state".$session->Vars["lang"]." != 7
							AND content.state".$session->Vars["lang"]." not in (0,5,7)";
		} else {
			$BoCondition = "

							AND nivel_4.active".$session->Vars["lang"]." != 1
							AND nivel_4.state".$session->Vars["lang"]." != 7
							AND content.state".$session->Vars["lang"]." not in (0,5,7)
							AND content.published".$session->Vars["lang"]." = 'Y'		
							AND nivel_4.description".$session->Vars["lang"].$md." is not null";
		}		
		
		$dataToReturn["hasLastLearningItem"] = "no";
		$dataToReturn["hasStartLearningItem"] = "no";
		//control if user has rights to LastElearningItem
		if ($item_ci_type=="EL") {

			$sqlMainNode = "SELECT content_id as idLink, title".$session->Vars["lang"]." as titleLink, ci_type
			  FROM content
			  JOIN nivel_4 ON content.id_zeroNivel   = nivel_4.id_zeroNivel
								AND content.id_firstNivel  = nivel_4.id_firstNivel
								AND content.id_secondNivel = nivel_4.id_secondNivel
								AND content.id_thirdNivel  = nivel_4.id_thirdNivel
								AND content.id_fourthNivel = nivel_4.id_fourthNivel
			  JOIN profil_rights ON nivel_4.id_zeroNivel   = profil_rights.id_zeroNivel
								AND content.id_firstNivel  = profil_rights.id_firstNivel
								AND nivel_4.id_secondNivel = profil_rights.id_secondNivel
								AND nivel_4.id_thirdNivel  = profil_rights.id_thirdNivel
								AND nivel_4.id_fourthNivel = profil_rights.id_fourthNivel
								AND profil_rights.profil_id in (".$session->Vars["tip"].")

				 WHERE content.id_zeroNivel	= '".$lev0."'
				   AND content.id_firstNivel	= '".$lev1."'
				   AND content.id_secondNivel	= '".$lev2."'
				   AND content.id_thirdNivel	= '".$lev3."'
				   AND content.id_fourthNivel	> 0
				   AND orderContent = 0    
				   AND ci_type in ('SL','CQ','SC','SM','ES')
				  ".$BoCondition."
				ORDER BY nivel_4.orderMenu
				LIMIT 0,1";

			$rsNode = WebApp::execQuery($sqlMainNode);
			IF (!$rsNode->EOF()) {
 
				$idStartLearningItem =  $rsNode->Field("idLink");
				$dataToReturn["hasStartLearningItem"] = "yes";
				$dataToReturn["typeStartLearningItem"] = $rsNode->Field("ci_type");
				$dataToReturn["idStartLearningItem"] 		= $idStartLearningItem;
				$dataToReturn["titleStartLearningItem"] 	= TRIM($rsNode->Field("titleLink"));
				$dataToReturn["hrefToStartLearningItem"] 	= "javascript:GoTo('thisPage?event=none.ch_state(k=".$idStartLearningItem.")');";
			}		

			$getLastAccessed= "SELECT contentId, title".$session->Vars["lang"]." as title, ci_type
								 FROM z_analytics_progress_transition_last_access
								 
								 JOIN content ON content.content_id = contentId

								  JOIN nivel_4 ON content.id_zeroNivel   = nivel_4.id_zeroNivel
													AND content.id_firstNivel  = nivel_4.id_firstNivel
													AND content.id_secondNivel = nivel_4.id_secondNivel
													AND content.id_thirdNivel  = nivel_4.id_thirdNivel
													AND content.id_fourthNivel = nivel_4.id_fourthNivel


								 JOIN profil_rights ON content.id_zeroNivel   = profil_rights.id_zeroNivel
												   AND content.id_firstNivel  = profil_rights.id_firstNivel
												   AND content.id_secondNivel = profil_rights.id_secondNivel
												   AND content.id_thirdNivel  = profil_rights.id_thirdNivel
												   AND content.id_fourthNivel = profil_rights.id_fourthNivel
												   AND profil_rights.profil_id in (".$session->Vars["tip"].")
								 
								WHERE lecture_id = '".$cId."' and z_analytics_progress_transition_last_access.contentId != lecture_id 
								  AND ses_userid = '".$session->Vars["ses_userid"]."'
				   				  AND ci_type in ('SL','CQ','SC','SM','ES')
								  ".$BoCondition."
								  
							 ORDER BY date_created DESC
							 LIMIT 0,1";
			$rsLast=WebApp::execQuery($getLastAccessed);
			if (!$rsLast->EOF()) {	
				
				$lastAccessedItem =  $rsLast->Field("contentId");
				$title =  $rsLast->Field("title");
				$dataToReturn["hasLastLearningItem"] = "yes";
				$dataToReturn["typeLastLearningItem"] = $rsLast->Field("ci_type");
				$dataToReturn["idLastLearningItem"] = $lastAccessedItem;
				$dataToReturn["titleLastLearningItem"] = $title;
				$dataToReturn["hrefToLastLearningItem"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$lastAccessedItem.")');";
			}				
		}
		return $dataToReturn;
	}
	function createDynamicOrCachedLinked($chstateParams=array(),$chstateExtraParams=array(),$lngId)
    {
  			global $global_cache_dynamic,$cacheDyn;   
    	
    		$linkToRrn = "javascript:GoTo('thisPage?event=none.ch_state()');";
			$dynParams = array();
			$crdParams = array();
			$crdParams["k"] = "";
			if (count($chstateExtraParams)>0) {
				reset($chstateExtraParams);
				while (list($key,$value)=each($chstateExtraParams)) {
					$dynParams["$key"] = "$key=".$value."";
				}
			}    		
			IF ($global_cache_dynamic == "Y") {
				if (isset($chstateParams["k"]) && $chstateParams["k"]!="") {
					$trg = explode(",",$chstateParams["k"]);
					if (count($trg)==1) {
						$crdParams["k"] = $trg[0];
					} elseif (count($trg)==6) {
						$crdParams["k"] = $trg[5];
					} else {
						$crdParams["k"] = $chstateParams["k"];
					}
				}
				if (isset($chstateParams["kc"]) && $chstateParams["kc"]!="") {
					$crdParams["kc"] = $chstateParams["kc"];
				}				
				
				$linkToRrn = $cacheDyn->get_CiTitleToUrl($crdParams["k"], $lngId, "", "", $crdParams["kc"], "");
				if (count($dynParams)>0) {
					$linkToRrn .= "?".implode("&",$dynParams);
				}
			} else {
				if ($chstateParams["k"]!="") $dynParams["k"] = "k=".$chstateParams["k"]."";
				if ($chstateParams["kc"]!="") $dynParams["kc"] = "kc=".$chstateParams["kc"]."";
				if (count($dynParams)>0) {
					$linkToRrn = "javascript:GoTo('thisPage?event=none.ch_state(".implode(";",$dynParams).")');";
				}
			}
			return $linkToRrn;
    }
    function constuctStructuredDefaultEcc()
    {
        global $session;
    
        if (!isset($this->pathLectureInfo)) {
        
			$pathLectureTypes = array();
			$pathLectureNav = array();
			$pathLectureParents = array();

			$pathLectureParents["PR"] = "PR";    //Programme
			$pathLectureParents["EC"] = "EC";    //Course
			$pathLectureParents["TC"] = "TC";    //Tutorials
			$pathLectureParents["EL"] = "EL";    //Lecture

		 //   $pathSearchTypes["RI"] = "RI";    //Lecture guide and Prelearning
			$pathSearchTypes["SP"] = "SP";    //Lecture guide and Prelearning
			$pathSearchTypes["TE"] = "TE";    //Lecture guide and Prelearning
			$pathLectureTypes["SL"] = "SL";    //Lecture guide and Prelearning
			$pathLectureTypes["CQ"] = "CQ";    //Elearning Quiz Container -or during lecture quiz
			$pathLectureTypes["ES"] = "ES";    //Final Examination Container

			$pathLectureTypes["SC"] = "SC";    //Lecture Presentation (Player)
			$pathLectureTypes["SV"] = "SV";    //Lecture Presentation - (Player) Virtual Slides
			$pathLectureTypes["SM"] = "SM";    //Summary  of the lecture
			$pathLectureNav = $pathLectureTypes;

			$pathLectureTypes["US"] = "US";    //User Satisfaction - Survey
			$pathLectureTypes["UF"] = "UF";    //User Satisfaction - Feedback
			$pathLectureTypes["UE"] = "UE";    //User Experience - Lecture
			$pathLectureTypes["LR"] = "LR";    //Lecture Level Reports


			//$pathLectureRelated["US"] = "US";    //User Satisfaction - Survey
			//$pathLectureRelated["UF"] = "UF";    //User Satisfaction - Feedback
			$pathLectureRelated["NC"] = "NC";    //
			$pathLectureRelated["CT"] = "CT";    //
			$pathLectureRelated["CF"] = "CF";    //


			$pathRepository = array();
			$pathRepository["RA"] = "RA";    //Additional Knowledge Resources
			$pathRepository["RQ"] = "RQ";    //Quizes & Examninations Repository

			$pathLectureSubTypes = array();
			$pathLectureSubTypes["EQ"] = "EQ";    //Question Item
			$pathLectureSubTypes["RI"] = "RI";    //Additional Knowledge Item
			$pathLectureSubTypes["SP"] = "SP";    //Presantation Slide
			$pathLectureSubTypes["TE"] = "TE";    //Presantation Slide

			$this->pathLectureInfo["lectureAllowedTypes"] = array();

			$this->pathLectureInfo["lectureAllowedTypes"]["EL"] = "EL";    //Lecture
			$this->pathLectureInfo["lectureAllowedTypes"] = array_merge($this->pathLectureInfo["lectureAllowedTypes"], $pathLectureTypes);
			$this->pathLectureInfo["lectureAllowedTypes"] = array_merge($this->pathLectureInfo["lectureAllowedTypes"], $pathRepository);
			$this->pathLectureInfo["lectureAllowedTypes"] = array_merge($this->pathLectureInfo["lectureAllowedTypes"], $pathLectureSubTypes);   

			$this->pathLectureInfo["pathLectureNavRelated"] = $pathLectureRelated;
			$this->pathLectureInfo["typeLecture"] 			= $pathLectureNav;
			$this->pathLectureInfo["pathSearchTypes"] 		= $pathSearchTypes;
			$this->pathLectureInfo["typeLectureParents"] 	= $pathLectureParents;
			$this->pathLectureInfo["typeLecture"] 			= $pathLectureTypes;
			$this->pathLectureInfo["typeRepository"] 		= $pathRepository;
			$this->pathLectureInfo["SubTypesLecture"] 		= $pathLectureSubTypes;
			$this->pathLectureInfo["lectureAllowedTypes"] 	= $this->pathLectureInfo["lectureAllowedTypes"];
        }
    }	
	function controllCacheConfiguration(){
		global $session;
		/*$this->setURLs["isCached"]= "no";
		$this->setURLs["koord"]["ActualPage"] 			= $session->Vars["level_0"].",".$session->Vars["level_1"].",".$session->Vars["level_2"].",".$session->Vars["level_3"].",".$session->Vars["level_4"];
		$this->setURLs["koord"]["SelfPage"]				= SelfPages;		
		$this->setURLs["koord"]["BookListPage"]			= BookListPage;
		$this->setURLs["koord"]["BookDetailsPage"] 		= BookDetailsPage;		
		$this->setURLs["koord"]["AuthorDetailsPage"] 	= AuthorDetailsPage;
		$this->setURLs["koord"]["PublisherDetailsPage"] = PublisherDetailsPage;		
		$this->setURLs["koord"]["SearchListPage"]		= SearchListPage;			
		$this->setURLs = $this->setURLs;	*/		
	}
	function generatedCachedUrl($koord_target, $koordNode="")
	{
        global $session;
		global $cacheDyn;
		$filename= "";
		$koordNodeToStr= "";
		if ($koordNode!="") {
			
			$koordNodeToStr = preg_replace("#,#","/",$koordNode,-1);
			$koordArr = explode(",",$koordNode);
			 //gjejme titullin e CI ------------------------------------------------------------
			   $sql_con = "SELECT content_id,
								  titleLng".$this->lngId."    as title,
								  filenameLng".$this->lngId." as filename
							 FROM content
								JOIN	nivel_4			AS n	ON (    content.id_zeroNivel   = n.id_zeroNivel
																	AND content.id_firstNivel  = n.id_firstNivel
																	AND content.id_secondNivel = n.id_secondNivel
																	AND content.id_thirdNivel  = n.id_thirdNivel
																	AND content.id_fourthNivel = n.id_fourthNivel
																	)


							WHERE content.id_zeroNivel   = '".$koordArr[0]."' AND
								  content.id_firstNivel  = '".$koordArr[1]."' AND
								  content.id_secondNivel = '".$koordArr[2]."' AND
								  content.id_thirdNivel  = '".$koordArr[3]."' AND
								  content.id_fourthNivel = '".$koordArr[4]."' AND
								  orderContent   = '0'";

			   $rs_con = WebApp::execQuery($sql_con);
			   IF (!$rs_con->EOF())
				  {
				   $filename   = $rs_con->Field("title");
				  }  
		}
		//    function get_CiTitleToUrl($content_id, $lng_id=1, $title="", $filename="", $koord_level_node_param="",$with_https="")	
		return  $cacheDyn->get_CiTitleToUrl($koord_target, $this->lngId,$filename,"",$koordNodeToStr);
	}	
    function getDefaultTemplateForCi($ciId,$ciType,$ciSelectedTemplate)
    {
        global $session;
        //DI
        $gridD = array("data" => array(), "AllRecs" => "0");
        $indD = 0;
        $get_ci_template = " SELECT distinct (c.content_id) as content_id, c.titleLng1 as title
									FROM content	AS c
									JOIN nivel_4			AS n	ON (    c.id_zeroNivel   = n.id_zeroNivel
																		AND c.id_firstNivel  = n.id_firstNivel
																		AND c.id_secondNivel = n.id_secondNivel
																		AND c.id_thirdNivel  = n.id_thirdNivel
																		AND c.id_fourthNivel = n.id_fourthNivel
																		)
									JOIN profil_rights AS p ON (
													p.id_zeroNivel   = c.id_zeroNivel
													AND p.id_firstNivel  = c.id_firstNivel
													AND p.id_secondNivel = c.id_secondNivel
													AND p.id_thirdNivel  = c.id_thirdNivel
													AND p.id_fourthNivel = c.id_fourthNivel
									)

									 JOIN ti_data as dt
									   ON c.content_id = dt.content_id
									  AND dt.ci_type = '" . $ciType . "'
									WHERE p.profil_id in (2)
									  AND n.description".$session->Vars["lang"]."" . $session->Vars["thisMode"] . " IS NOT NULL
									  AND n.description".$session->Vars["lang"]."" . $session->Vars["thisMode"] . " !=''
									  AND c.ci_type in ('TI')
									  AND c.state".$session->Vars["lang"]." not in (0,5,7)
									  AND c.published".$session->Vars["lang"]." = 'Y'
									  AND n.active".$session->Vars["lang"]." != '1'
									  AND n.state".$session->Vars["lang"]." != 7
						ORDER BY c.content_id DESC";

        $rs_get_ci_template = WebApp::execQuery($get_ci_template);
		$gridD["data"][$indD]["templateID"] = -1;
		$gridD["data"][$indD]["templateIDSel"] = "";
        $gridD["data"][$indD]["templateTitle"] = "No Default Template";
        $gridD["data"][$indD]["templateClass"] = "bld";
		if ($ciSelectedTemplate == -1) {
			$gridD["data"][$indD]["templateIDSel"] = " selected=\"selected\"";
		}
		
		$indD++;
		$gridD["data"][$indD]["templateID"] = 0;
		$gridD["data"][$indD]["templateIDSel"] = "";
        $gridD["data"][$indD]["templateTitle"] = "Zone Default Template";
        $gridD["data"][$indD]["templateClass"] = "bld";
		if ($ciSelectedTemplate ==0) {
			$gridD["data"][$indD]["templateIDSel"] = " selected=\"selected\"";
		}
		
		$indD++;
        while (!$rs_get_ci_template->EOF()) {

            $templateID = $rs_get_ci_template->Field("content_id");
            $gridD["data"][$indD]["templateID"] = $templateID;
            $gridD["data"][$indD]["templateIDSel"] = "";
            $gridD["data"][$indD]["templateClass"] = "";

            if ($ciSelectedTemplate == $templateID) {
                $gridD["data"][$indD]["templateIDSel"] = " selected=\"selected\"";
            }
            $gridD["data"][$indD]["templateTitle"] = $rs_get_ci_template->Field("title");
            $indD++;
            $rs_get_ci_template->MoveNext();
        }

        $gridD["AllRecs"] = count($gridD["data"]);
        WebApp::addVar("gridTemplateAvaiable", $gridD);
    }
	function initNemConfiguration($idstemp=""){
		global $session;
		if ($idstemp=="")	$this->idstemp = $session->Vars["idstemp"];
		else				$this->idstemp = $idstemp;
		$objNemArr 	= explode("-",$this->idstemp);
		$type_doc 	= $objNemArr[0];
		$nemRefId 	= $objNemArr[1];
		$nemID 		= $objNemArr[2];
		$objNem 	= $objNemArr[4];
		$this->nemsConf[$type_doc][$nemRefId][nemID] = array();
		$this->returnNemProp();	
	}
	function getNemConfigurationAtr($idstemp=""){
		global $session;
		if ($idstemp=="")	$idstemp = $session->Vars["idstemp"];
		$objNemArr 	= explode("-",$idstemp);
		return $objNemArr;
	}
	function returnNemProp()
	{
		global $session;
    	$objects = WebApp::clearNemAtributes($this->idstemp);
		$this->NEM_TEMPLATE = "";
		require_once(INC_PHP_AJAX."NemsManager.class.php");
		$grn = NemsManager::getFrontEndGeneralProperties($objects);
		$this->generalMessages = $grn;
		if (isset($grn["NEM_FILENAME"]) && $grn["NEM_FILENAME"]!='') {
			$this->NEM_FILENAME = $grn["NEM_FILENAME"];
		}		
    	$this->NEM_PROP = $objects; //kjo eshte kapur qe te gjitha propertite qe mund te shtohen te kapen aty ku duhen vetme nese duhen
	}	
    function getTocOfLecture()
    {
		global $session;
        if (isset($this->appRelSt["LECTURE_RELATED"]["EL"])) {
            $lectureId = $this->appRelSt["LECTURE_RELATED"]["EL"];

            $getToc = "SELECT toc_id, toc_description , toc_order
							 FROM el_data_toc
							WHERE content_id = '" . $lectureId . "' AND lng_id = '" . $this->lngId . "'
							  AND toc_main = 'y'
							  AND statusInfo in (0)
						 ORDER BY toc_order";
            //$this->getToc = $getToc;
            $rs = WebApp::execQuery($getToc);
            $itemGrid = array("data" => array(), "AllRecs" => "0");
            $ind = 0;
            while (!$rs->EOF()) {

                $toc_id = $rs->Field("toc_id");
                $itemGrid["data"][$ind]["toc_id"] = $toc_id;
                $itemGrid["data"][$ind]["toc_description"] = WebApp::parseformulaDBtoHTML($rs->Field("toc_description"));
                $itemGrid["data"][$ind]["toc_order"] = $rs->Field("toc_order");
                $this->TOC[$toc_id] = $rs->Field("toc_description");
                $ind++;
                $rs->MoveNext();
            }

            $itemGrid["AllRecs"] = count($itemGrid["data"]);
            WebApp::addVar("TOC_Grid", $itemGrid);

            $getlo = "SELECT lo_id, lo_description , lo_order
							 FROM el_data_lo
							WHERE content_id = '" . $lectureId . "' AND lng_id = '" . $this->lngId . "'
							  AND lo_main = 'y'
							  AND statusInfo in (0)
						 ORDER BY lo_order";
            //$this->getlo = $getlo;
            $rs = WebApp::execQuery($getlo);
            $itemGrid = array("data" => array(), "AllRecs" => "0");
            $ind = 0;
            while (!$rs->EOF()) {

                $lo_id = $rs->Field("lo_id");
                $itemGrid["data"][$ind]["lo_id"] = $lo_id;
                $itemGrid["data"][$ind]["lo_description"] = WebApp::parseformulaDBtoHTML($rs->Field("lo_description"));
                $itemGrid["data"][$ind]["lo_order"] = $rs->Field("lo_order");
                $this->LO[$lo_id] = $rs->Field("lo_description");
                $ind++;
                $rs->MoveNext();
            }

            $itemGrid["AllRecs"] = count($itemGrid["data"]);
            WebApp::addVar("LO_Grid", $itemGrid);
        }
    }	
	function getrRmvAnalytics ($userId="") {//UserProgres
		$dlt = "DELETE FROM z_analytics_progress WHERE  ses_userid = '".$userId."'
				AND contentId in (SELECT content_id FROM content WHERE ci_type NOT IN ('AI','DE','EC','EL','EQ','ES','MS','PR','RI','SC','SL','SM','SP','CQ'))";
		WebApp::execQuery($dlt);
	}
	function getCiAnalyticsMainCi ($ids,$userId="") {//UserProgres
		global $session;

		if (isset($this->useridp) && $this->useridp>0) $userId=$this->useridp;
		elseif ($userId=="") $userId=$session->Vars["ses_userid"];
		
		$tmpAll = array(); 
		if ($gridId=="") {

			$sql_con = "SELECT contentId, sum(duration) as ci_duration
							 FROM z_analytics_progress
							WHERE contentId in (".$ids.")
							  AND contentId = cirel
							  AND lecture_id != contentId
							  AND ses_userid = '".$userId."'
						 GROUP BY contentId";
			$rs_con = WebApp::execQuery($sql_con); //
			while (!$rs_con->EOF()) {
				$content_id = $rs_con->Field("contentId");
				$this->CiAnalyticMain[$content_id]["ci_duration"]= $rs_con->Field("ci_duration");
				$rs_con->MoveNext();
			}
		}
		return $tmpAll;
	}
	function getCiAnalyticsInside ($ids,$gridId="",$userId="",$idslides) {//UserProgres
		global $session;

		if (isset($this->useridp) && $this->useridp>0) $userId=$this->useridp;
		elseif ($userId=="") $userId=$session->Vars["ses_userid"];
		
		$tmpAll = array(); 
		if ($gridId=="") {

			$sql_con = "SELECT contentId, sum(duration) as ci_duration, sum(nr_count) as nr_of_views, sum(nr_download) as nr_download
							 FROM z_analytics_progress
							WHERE contentId in (".$ids.")
							  AND cirel in (".$idslides.")
							  AND contentId != cirel
							  AND lecture_id != contentId
							  AND ses_userid = '".$userId."'
						 GROUP BY contentId";
			$rs_con = WebApp::execQuery($sql_con); //
			while (!$rs_con->EOF()) {
				$content_id = $rs_con->Field("contentId");
				$tmpAll[$content_id]["ci_duration"]= $rs_con->Field("ci_duration");
				$tmpAll[$content_id]["nr_of_views"]= $rs_con->Field("nr_of_views");
				$tmpAll[$content_id]["nr_download"]= $rs_con->Field("nr_download");
				$this->CiAnalytics[$content_id] = $tmpAll[$content_id];
				$this->totalTime += $tmpAll[$content_id]["ci_duration"];
				$rs_con->MoveNext();
			}
			if (isset($tmpAll) && count($tmpAll)>0 ) {
				while (list($content_id,$data)=each($tmpAll)) {
					$gridD = array("data" => array(), "AllRecs" => "1");
					$gridD["data"][0] = $data;
					WebApp::addVar("CiAnalytics_".$content_id, $gridD);	
				}
			}
		}
		return $tmpAll;
	}	
	function getCiAnalytics ($ids,$gridId="",$userId="") {//UserProgres
		global $session;

		if (isset($this->useridp) && $this->useridp>0) $userId=$this->useridp;
		elseif ($userId=="") $userId=$session->Vars["ses_userid"];
		
		$tmpAll = array(); 
		if ($gridId=="") {
			$sql_con = "SELECT contentId, sum(duration) as ci_duration, sum(nr_count) as nr_of_views, sum(nr_download) as nr_download
							 FROM z_analytics_progress
							WHERE contentId in (".$ids.")
							  AND contentId = cirel
							  AND lecture_id != contentId
							  AND ses_userid = '".$userId."'
						 GROUP BY contentId";
			$rs_con = WebApp::execQuery($sql_con); //
			while (!$rs_con->EOF()) {
				$content_id = $rs_con->Field("contentId");
				$tmpAll[$content_id]["ci_duration"]= $rs_con->Field("ci_duration");
				$tmpAll[$content_id]["nr_of_views"]= $rs_con->Field("nr_of_views");
				$tmpAll[$content_id]["nr_download"]= $rs_con->Field("nr_download");
				$this->CiAnalytics[$content_id] = $tmpAll[$content_id];
				$this->totalTime += $tmpAll[$content_id]["ci_duration"];
				$rs_con->MoveNext();
			}
			if (isset($tmpAll) && count($tmpAll)>0 ) {
				while (list($content_id,$data)=each($tmpAll)) {
					$gridD = array("data" => array(), "AllRecs" => "1");
					$gridD["data"][0] = $data;
					WebApp::addVar("CiAnalytics_".$content_id, $gridD);	
				}
			}
		}
		return $tmpAll;
	}
	function getLecturesActivity ($ciflow,$ids,$userId="") {//UserProgres
		global $session;

		if (isset($this->useridp) && $this->useridp>0) $userId=$this->useridp;
		elseif ($userId=="") $userId=$session->Vars["ses_userid"];
		
		$nrRelToControlForView = 0;	//contentId = '".$ciflow."' AND
		$tmpAll = array(); 
		$getNrOfRelated = "SELECT count(distinct(cirel)) as nrOpened
					  		 FROM z_analytics_progress
					 		WHERE cirel!=contentId
					   		  AND ses_userid = '".$userId."'
					 	 GROUP BY contentId";
		$rs_el = WebApp::execQuery($getNrOfRelated);
		if (!$rs_el->EOF()) {	
			$nrRelToControlForView	  = $rs_el->Field("nrOpened");
		}	
		return $nrRelToControlForView;
	}	
	function getRAAnalyticsCatGrouped ($ids,$userId="") {//UserProgres
		global $session;

		if (isset($this->useridp) && $this->useridp>0) $userId=$this->useridp;
		elseif ($userId=="")						   $userId=$session->Vars["ses_userid"];
		
		$dataToReturn["tot"] = array(); 
		
		$dataToReturn["tot"]["nrOpened"]	  					= 0;
		$dataToReturn["tot"]["duration"]	  					= 0;
		$dataToReturn["tot"]["nr_download"]	  					= 0;
		$dataToReturn["tot"]["nrOpenedFromDifferentLocation"]	= 0;		
		
		$dataToReturn["det"] = array(); 
	
		$nrRelToControlForView = 0;	//contentId = '".$ciflow."' AND
		$tmpAll = array(); 
		if ($ids!="") {
			$getNrOfRelated = "SELECT count(distinct(contentId)) as nrOpenedFromDifferentLocation,  
										sum(nr_count) as nrOpened, 
										sum(duration) as duration, 
										sum(nr_download) as nr_download, cirel
								 FROM z_analytics_progress
								WHERE  cirel in (".$ids.")
								  AND ses_userid = '".$userId."'
								   GROUP BY cirel";
			$rs_el = WebApp::execQuery($getNrOfRelated);
			while (!$rs_el->EOF()) {
				$cirel = $rs_el->Field("cirel");
				$dataToReturn["det"][$cirel]["nrOpened"]	  					= $rs_el->Field("nrOpened");
				$dataToReturn["det"][$cirel]["duration"]	  					= $rs_el->Field("duration");
				$dataToReturn["det"][$cirel]["nr_download"]	  					= $rs_el->Field("nr_download");
				$dataToReturn["det"][$cirel]["nrOpenedFromDifferentLocation"]	= $rs_el->Field("nrOpenedFromDifferentLocation");
				
				
				$dataToReturn["tot"]["nrOpened"]	  					+= $rs_el->Field("nrOpened");
				$dataToReturn["tot"]["duration"]	  					+= $rs_el->Field("duration");
				$dataToReturn["tot"]["nr_download"]	  					+= $rs_el->Field("nr_download");
				$dataToReturn["tot"]["nrOpenedFromDifferentLocation"]	+= $rs_el->Field("nrOpenedFromDifferentLocation");
				
				$rs_el->MoveNext();
			}
		}	
		return $dataToReturn;
	}		
	function getRAAnalyticsCat ($ids,$userId="") {//UserProgres
		global $session;

		if (isset($this->useridp) && $this->useridp>0) $userId=$this->useridp;
		elseif ($userId=="") $userId=$session->Vars["ses_userid"];		

		$dataToReturn["nrOpened"] = 0;
		$dataToReturn["duration"] = 0;
		$nrRelToControlForView = 0;	//contentId = '".$ciflow."' AND
		$tmpAll = array(); 
		if ($ids!="") {
			$getNrOfRelated = "SELECT count(distinct(cirel)) as nrOpened, sum(duration) as duration, sum(nr_download) as nr_download
								 FROM z_analytics_progress
								WHERE  cirel in (".$ids.")
								  AND ses_userid = '".$userId."'";
			$rs_el = WebApp::execQuery($getNrOfRelated);
			if (!$rs_el->EOF()) {	
				$dataToReturn["nrOpened"]	  = $rs_el->Field("nrOpened");
				$dataToReturn["duration"]	  = $rs_el->Field("duration");
				$dataToReturn["nr_download"]	  = $rs_el->Field("nr_download");
			}
		}	
		return $dataToReturn;
	}	
	function userOverallDashboardProgress () {
		global $session;

		$dataAn = array();
		$totalDownloads = 0;
		$totalViews = 0;
		$totalTimeSpent = 0;
		$getAnalitics = "SELECT lecture_id,sum(nr_count) as nr_count,
								sum(nr_download) as nr_download, sum(duration) as duration
						   FROM z_analytics_progress
						  WHERE z_analytics_progress.ses_userid = '".$session->Vars["ses_userid"]."'
					   GROUP BY lecture_id";

		$rs_getAnalitics= WebApp::execQuery($getAnalitics);
		While (!$rs_getAnalitics->EOF())  {
			
			$lecture_id	= $rs_getAnalitics->Field("lecture_id");
			$nr_download	= $rs_getAnalitics->Field("nr_download");
			$nr_count	= $rs_getAnalitics->Field("nr_count");
			$duration	= round($rs_getAnalitics->Field("duration")/3600);
			
			$dataAn["ids"][$lecture_id] 		= $lecture_id;
			$dataAn["nr_count"][$lecture_id] 	= $nr_count;
			$dataAn["nr_down"][$lecture_id] 	= $nr_download;
			$dataAn["timeSpent"][$lecture_id] 	= $duration;
			
			$totalViews 		+=$nr_count;
			$totalDownloads 	+=$nr_download;
			$totalTimeSpent 	+= $duration;			
			
			$rs_getAnalitics->MoveNext();
		}
		
		$analiticsToGrid = array();
		$dataMyLecturesID = array();
		$dataMyLectures = array();
		$dataMyLecturesIds = array();
		$dataMyLecturesCoordIds = array();
		$dataMyLecturesCoordIdsRev = array();
		
		$getUserLectures = "SELECT z_elearning_user_progress.item_id, progress_state, results_state, cme_points_received, 
								   date_format(date_owened,'%d.%m.%Y') as PurchaseTime,	
								   date_format(date_begin,'%d.%m.%Y') as date_begin,	
								   date_format(date_end,'%d.%m.%Y') as date_end,
								   content.id_zeroNivel, content.id_firstNivel,content.id_secondNivel,content.id_thirdNivel,content.id_fourthNivel,
								   title".$session->Vars["lang"]." as title,
								   coalesce(ecc_reference*1, concat(id_firstNivel,',',id_secondNivel)) as ecc_reference
								  
							FROM z_elearning_user_progress
							JOIN content on content.content_id = z_elearning_user_progress.item_id
							LEFT JOIN ci_elearning_extended on content.content_id = ci_elearning_extended.content_id 
							 AND ci_elearning_extended.lng_id = '".$this->lngId."' AND ci_elearning_extended.statusInfo = '1'							
							WHERE z_elearning_user_progress.user_id = '".$session->Vars["ses_userid"]."'";
		$rs_l= WebApp::execQuery($getUserLectures);
		$ind = 0;
		While (!$rs_l->EOF())  {
				
			$lecture_id 			= $rs_l->Field("lecture_id");
			$title 					= $rs_l->Field("title");
			$ecc_reference 					= $rs_l->Field("ecc_reference");
			
			$analiticsToGrid["ecc_reference"][$lecture_id] = "\"".$ecc_reference."\"";
			$analiticsToGrid["title"][$lecture_id] = "\"".$title."\"";
			$analiticsToGrid["ids"][$lecture_id] = "\"".$lecture_id."\"";
			$analiticsToGrid["nr_count"][$lecture_id] = "0";
			$analiticsToGrid["nr_down"][$lecture_id] = "0";
			$analiticsToGrid["timeSpent"][$lecture_id] = "0";

			$analiticsToGrid["nr_countP"][$lecture_id] = "0";
			$analiticsToGrid["nr_downP"][$lecture_id] = "0";
			$analiticsToGrid["timeSpentP"][$lecture_id] = "0";
			
			if (isset($dataAn["nr_count"][$lecture_id]) && $dataAn["nr_count"][$lecture_id]>0) {
				$analiticsToGrid["nr_count"][$lecture_id] = $dataAn["nr_count"][$lecture_id];
				$analiticsToGrid["nr_countP"][$lecture_id] = round($dataAn["nr_count"][$lecture_id]/$totalViews*100);
			}			
			if (isset($dataAn["timeSpent"][$lecture_id]) && $dataAn["timeSpent"][$lecture_id]>0) {
				$analiticsToGrid["timeSpent"][$lecture_id] = $dataAn["timeSpent"][$lecture_id];
				$analiticsToGrid["timeSpentP"][$lecture_id] = round($dataAn["timeSpent"][$lecture_id]/$totalTimeSpent*100);
			}
			if (isset($dataAn["nr_down"][$lecture_id]) && $dataAn["nr_down"][$lecture_id]>0) {
				$analiticsToGrid["nr_down"][$lecture_id] = $dataAn["nr_down"][$lecture_id];
				$analiticsToGrid["nr_downP"][$lecture_id] = round($dataAn["nr_down"][$lecture_id]/$totalDownloads*100);
			}

			$id_zeroNivel 			= $rs_l->Field("id_zeroNivel");
			$id_firstNivel 			= $rs_l->Field("id_firstNivel");
			$id_secondNivel 		= $rs_l->Field("id_secondNivel");
			$id_thirdNivel 			= $rs_l->Field("id_thirdNivel");
			$id_fourthNivel 		= $rs_l->Field("id_fourthNivel");
			
			$dataMyLecturesIds[$lecture_id] = $lecture_id;
			$dataMyLecturesCoord[$id_zeroNivel.",".$id_firstNivel.",".$id_secondNivel] = "'".$id_zeroNivel.",".$id_firstNivel.",".$id_secondNivel.",0,0'";
			$dataMyLecturesCoords[$id_zeroNivel.",".$id_firstNivel.",".$id_secondNivel][$lecture_id] = $lecture_id;
			
			$progress_state 		= $rs_l->Field("progress_state");
			$results_state 			= $rs_l->Field("results_state");
			$dataMyLecturesCoordsP[$progress_state][$id_zeroNivel.",".$id_firstNivel.",".$id_secondNivel][$lecture_id] = $lecture_id;
			
			$dataMyLecturesCoordsP[$progress_state][$results_state][$id_zeroNivel.",".$id_firstNivel.",".$id_secondNivel][$lecture_id] = $lecture_id;
			
			$cme_points_received 	= $rs_l->Field("cme_points_received");
			$PurchaseTime 			= $rs_l->Field("PurchaseTime");
			$date_begin 			= $rs_l->Field("date_begin");
			$date_end 				= $rs_l->Field("date_begin");

			$dataMyLectures[$ind]["progress_state"]			= $progress_state;
			$dataMyLectures[$ind]["results_state"]			= $results_state;
			$dataMyLectures[$ind]["cme_points_received"]	= "";
			$dataMyLectures[$ind]["PurchaseTime"]			= $PurchaseTime;
			$dataMyLectures[$ind]["date_begin"]				= "";
			$dataMyLectures[$ind]["date_end"]				= "";
			$dataMyLectures[$ind]["title"]					= $title;
			
			if ($progress_state=="new") {
			} elseif ($progress_state=="in_progress") {
				$dataMyLectures[$ind]["date_begin"]		= $date_begin;
			} elseif ($progress_state=="finished") {
				$dataMyLectures[$ind]["date_begin"]		= $date_begin;
				$dataMyLectures[$ind]["date_end"]		= $date_end;
			}
			$dataMyLecturesID[$lecture_id] = $dataMyLectures[$ind];
			$ind++;
			$rs_l->MoveNext();
		}

		if (isset($analiticsToGrid) && count($analiticsToGrid)>0) {
			$tmp["data"][0]["label"] = implode(",",$analiticsToGrid["title"]);
			$tmp["data"][0]["labelids"] = implode(",",$analiticsToGrid["ids"]);
			$tmp["data"][0]["ecc_references"] = implode(",",$analiticsToGrid["ecc_reference"]);
			
			$tmp["data"][0]["nr_count"] = implode(",",$analiticsToGrid["nr_count"]);
			$tmp["data"][0]["nr_down"] = implode(",",$analiticsToGrid["nr_down"]);
			$tmp["data"][0]["timeSpent"] = implode(",",$analiticsToGrid["timeSpent"]);
			
			$tmp["data"][0]["nr_countP"] = implode(",",$analiticsToGrid["nr_countP"]);
			$tmp["data"][0]["nr_downP"] = implode(",",$analiticsToGrid["nr_downP"]);
			$tmp["data"][0]["timeSpentP"] = implode(",",$analiticsToGrid["timeSpentP"]);

			$tmp["data"][0]["totalViews"] = "$totalViews";
			$tmp["data"][0]["totalTimeSpent"] = "$totalTimeSpent";
			$tmp["data"][0]["totalDownloads"] = "$totalDownloads";

			$tmp["AllRecs"] = count($tmp["data"]);
			WebApp::addVar("analitycsLectureGrid", $tmp);				
		}
		
		$moduleGrid = array();
		$indM = 0;
		
		if (isset($dataMyLecturesCoord) && count($dataMyLecturesCoord)>0) {

			$inNodesParam = implode(",",$dataMyLecturesCoord);
			IF ($session->Vars["thisMode"] == "_new")
				{$kusht_aktiv_joaktiv = "";}
			ELSE
				{$kusht_aktiv_joaktiv = " AND n.active".$session->Vars["lang"]." != 1 ";}

			$selectNodeGroupedInfo = "
				SELECT n.description".$session->Vars["lang"].$session->Vars["thisMode"]." as sel_node_descriptions,
						title".$session->Vars["lang"]." as title,
						concat(n.id_zeroNivel,',',n.id_firstNivel,',',n.id_secondNivel) as ke
						
				  FROM nivel_4 as n
				  JOIN content on content.id_zeroNivel = n.id_zeroNivel
				   AND content.id_firstNivel = n.id_firstNivel
				   AND content.id_secondNivel = n.id_secondNivel
				   AND content.id_thirdNivel = n.id_thirdNivel
				   AND content.id_fourthNivel = n.id_fourthNivel
				   AND orderContent = 0
				   AND ci_type='EC'
				   
				 WHERE concat(n.id_zeroNivel,',',n.id_firstNivel,',',n.id_secondNivel,',',n.id_thirdNivel,',',n.id_fourthNivel) in (".$inNodesParam.")
                   AND n.state".$session->Vars["lang"]." != 7  
                   AND COALESCE(n.description".$session->Vars["lang"].$session->Vars["thisMode"].", '') != '' 
				   ".$kusht_aktiv_joaktiv."
				  ORDER BY n.id_zeroNivel,n.orderMenu,n.id_firstNivel,n.orderMenu,n.id_secondNivel,n.orderMenu,n.id_thirdNivel,n.orderMenu,n.id_fourthNivel";
			
			$rsselectNodeGroupedInfo = WebApp::execQuery($selectNodeGroupedInfo);
			while (!$rsselectNodeGroupedInfo->EOF()) {
				
				$ke = $rsselectNodeGroupedInfo->Field("ke");
				//if (isset($dataMyLecturesCoords[$ke]) && count($dataMyLecturesCoords[$ke])>0) {
				
					$moduleGrid["data"][$indM]["nodeDescription"] 	= $rsselectNodeGroupedInfo->Field("sel_node_descriptions");
					$moduleGrid["data"][$indM]["nodeTitle"] 		= $rsselectNodeGroupedInfo->Field("title");
					$moduleGrid["data"][$indM]["total"]				= count($dataMyLecturesCoords[$ke]);
					
					$moduleGrid["data"][$indM]["finished"]			="0";
					$moduleGrid["data"][$indM]["in_progress"]		="0";
					$moduleGrid["data"][$indM]["passed"]			="0";
					$moduleGrid["data"][$indM]["not_passed"]		="0";
					
					$moduleGrid["data"][$indM]["finishedP"]			="0";
					$moduleGrid["data"][$indM]["in_progressP"]		="0";
					$moduleGrid["data"][$indM]["passedP"]			="0";
					$moduleGrid["data"][$indM]["not_passedP"]		="0";	
					$moduleGrid["data"][$indM]["no_progress"] 		="0";	
					
					$moduleGrid["data"][$indM]["cl_finished"]			="#ccc";
					$moduleGrid["data"][$indM]["cl_in_progressP"]		="#f8ac59";
					$moduleGrid["data"][$indM]["cl_passedP"]			="#5cb85c";
					$moduleGrid["data"][$indM]["cl_not_passedP"]		="#ed5565";	
					$moduleGrid["data"][$indM]["cl_no_progress"] 		="#dedede";	
					
					$moduleGrid["data"][$indM]["modul_progress"]	= "no_progress";
					$moduleGrid["data"][$indM]["cl_modul_progress"]	= "#dedede";
					
					if (isset($dataMyLecturesCoordsP["finished"][$ke])) {
						$moduleGrid["data"][$indM]["finished"]		= count($dataMyLecturesCoordsP["finished"][$ke]);
					}
					
					if (isset($dataMyLecturesCoordsP["in_progress"][$ke])) {
						$moduleGrid["data"][$indM]["in_progress"]		= count($dataMyLecturesCoordsP["in_progress"][$ke]);
						$moduleGrid["data"][$indM]["modul_progress"]	= "in_progress";
						$moduleGrid["data"][$indM]["cl_modul_progress"]	= "#f8ac59";
					}
					
					if ($moduleGrid["data"][$indM]["finished"]==$moduleGrid["data"][$indM]["total"]) {	
						$moduleGrid["data"][$indM]["modul_progress"]	= "finished";
						$moduleGrid["data"][$indM]["modul_progress"]	= "in_progress";
						$moduleGrid["data"][$indM]["cl_modul_progress"]	= "#f8ac59";
					}
					
					if (isset($dataMyLecturesCoordsP["finished"]["passed"][$ke])) {
						$moduleGrid["data"][$indM]["passed"]		= count($dataMyLecturesCoordsP["finished"]["passed"][$ke]);
					}					
					if (isset($dataMyLecturesCoordsP["finished"]["not_passed"][$ke])) {
						$moduleGrid["data"][$indM]["not_passed"]		= count($dataMyLecturesCoordsP["finished"]["not_passed"][$ke]);
					}						

					if ($moduleGrid["data"][$indM]["finished"]==$moduleGrid["data"][$indM]["total"]) {	
						$moduleGrid["data"][$indM]["modul_progress"]	= "finished";
						$moduleGrid["data"][$indM]["cl_modul_progress"]	= "#5cb85c";
					}

					if ($moduleGrid["data"][$indM]["passed"]>0)
						$moduleGrid["data"][$indM]["passedP"] = $moduleGrid["data"][$indM]["passed"]/$moduleGrid["data"][$indM]["finished"]*100;

					if ($moduleGrid["data"][$indM]["not_passed"]>0)
						$moduleGrid["data"][$indM]["not_passedP"] = $moduleGrid["data"][$indM]["not_passed"]/$moduleGrid["data"][$indM]["finished"]*100;
						
					if ($moduleGrid["data"][$indM]["finished"]>0)
						$moduleGrid["data"][$indM]["finishedP"] = $moduleGrid["data"][$indM]["finished"]/$moduleGrid["data"][$indM]["total"]*100;
						
					if ($moduleGrid["data"][$indM]["in_progress"]>0)
						$moduleGrid["data"][$indM]["in_progressP"] = $moduleGrid["data"][$indM]["in_progress"]/$moduleGrid["data"][$indM]["total"]*100;

					$moduleGrid["data"][$indM]["no_progress"] = "".($moduleGrid["data"][$indM]["total"] - ($moduleGrid["data"][$indM]["finished"]+$moduleGrid["data"][$indM]["in_progress"]));
						
					$indM++;	
				
				$rsselectNodeGroupedInfo->MoveNext();
			}
		}
		$moduleGrid["AllRecs"] = count($moduleGrid["data"]);
		
		WebApp::addVar("UserModuleToGrid", $moduleGrid);			
	}	
	function userOverallLectureProgress () {
		
		global $session;
		
		$dataMyLecturesID = array();
		$dataMyLectures = array();
		$dataMyLecturesIds = array();
		$dataMyLecturesCoordIds = array();
		$dataMyLecturesCoordIdsRev = array();
		
		$getUserLectures = "SELECT z_elearning_user_progress.item_id, progress_state, results_state, cme_points_received, 
								   date_format(date_owened,'%d.%m.%Y') as PurchaseTime,	
								   date_format(date_begin,'%d.%m.%Y') as date_begin,	
								   date_format(date_end,'%d.%m.%Y') as date_end,
								   content.id_zeroNivel, content.id_firstNivel,content.id_secondNivel,content.id_thirdNivel,content.id_fourthNivel,
								   title".$session->Vars["lang"]." as title
							FROM z_elearning_user_progress
							JOIN content on content.content_id = z_elearning_user_progress.item_id
							WHERE z_elearning_user_progress.user_id = '".$session->Vars["ses_userid"]."'";
		$rs_l= WebApp::execQuery($getUserLectures);
		$ind = 0;
		While (!$rs_l->EOF())  {
				
			$lecture_id 			= $rs_l->Field("lecture_id");
			$title 					= $rs_l->Field("title");
			
			$id_zeroNivel 			= $rs_l->Field("id_zeroNivel");
			$id_firstNivel 			= $rs_l->Field("id_firstNivel");
			$id_secondNivel 		= $rs_l->Field("id_secondNivel");
			
			$id_thirdNivel 			= $rs_l->Field("id_thirdNivel");
			$id_fourthNivel 		= $rs_l->Field("id_fourthNivel");
			
			$dataMyLecturesIds[$lecture_id] = $lecture_id;
			$dataMyLecturesCoord[$id_zeroNivel.",".$id_firstNivel.",".$id_secondNivel] = "'".$id_zeroNivel.",".$id_firstNivel.",".$id_secondNivel.",0,0'";
			$dataMyLecturesCoords[$id_zeroNivel.",".$id_firstNivel.",".$id_secondNivel][$lecture_id] = $lecture_id;
			
			$dataMyLecturesCoordsP[$progress_state][$id_zeroNivel.",".$id_firstNivel.",".$id_secondNivel][$lecture_id] = $lecture_id;

			$progress_state 		= $rs_l->Field("progress_state");
			$results_state 			= $rs_l->Field("results_state");
			$cme_points_received 	= $rs_l->Field("cme_points_received");
			$PurchaseTime 			= $rs_l->Field("PurchaseTime");
			$date_begin 			= $rs_l->Field("date_begin");
			$date_end 				= $rs_l->Field("date_begin");

			$dataMyLectures[$ind]["progress_state"]			= $progress_state;
			$dataMyLectures[$ind]["results_state"]			= $results_state;
			$dataMyLectures[$ind]["cme_points_received"]	= "";
			$dataMyLectures[$ind]["PurchaseTime"]			= $PurchaseTime;
			$dataMyLectures[$ind]["date_begin"]				= "";
			$dataMyLectures[$ind]["date_end"]				= "";
			$dataMyLectures[$ind]["title"]					= $title;
			
			if ($progress_state=="new") {
			} elseif ($progress_state=="in_progress") {
				$dataMyLectures[$ind]["date_begin"]		= $date_begin;
			} elseif ($progress_state=="finished") {
				$dataMyLectures[$ind]["date_begin"]		= $date_begin;
				$dataMyLectures[$ind]["date_end"]		= $date_end;
			}
			$dataMyLecturesID[$lecture_id] = $dataMyLectures[$ind];
			$ind++;
			$rs_l->MoveNext();
		}
			
		$moduleGrid = array();
		$indM = 0;
		
		if (isset($dataMyLecturesCoord) && count($dataMyLecturesCoord)>0) {
			$inNodesParam = implode(",",$dataMyLecturesCoord);
			IF ($session->Vars["thisMode"] == "_new")
				{$kusht_aktiv_joaktiv = "";}
			ELSE
				{$kusht_aktiv_joaktiv = " AND n.active".$session->Vars["lang"]." != 1 ";}

			$selectNodeGroupedInfo = "
				SELECT n.description".$session->Vars["lang"].$session->Vars["thisMode"]." as sel_node_descriptions,
						title".$session->Vars["lang"]." as title,
						concat(n.id_zeroNivel,',',n.id_firstNivel,',',n.id_secondNivel) as ke
						
				  FROM nivel_4 as n
				  JOIN content on content.id_zeroNivel = n.id_zeroNivel
				   AND content.id_firstNivel = n.id_firstNivel
				   AND content.id_secondNivel = n.id_secondNivel
				   AND content.id_thirdNivel = n.id_thirdNivel
				   AND content.id_fourthNivel = n.id_fourthNivel
				   AND orderContent = 0
				   AND ci_type='EC'
				   
				 WHERE concat(n.id_zeroNivel,',',n.id_firstNivel,',',n.id_secondNivel,',',n.id_thirdNivel,',',n.id_fourthNivel) in (".$inNodesParam.")
                   AND n.state".$session->Vars["lang"]." != 7  
                   AND COALESCE(n.description".$session->Vars["lang"].$session->Vars["thisMode"].", '') != '' 
				   ".$kusht_aktiv_joaktiv."
			 ORDER BY n.id_zeroNivel,n.orderMenu,n.id_firstNivel,n.orderMenu,n.id_secondNivel,n.orderMenu,n.id_thirdNivel,n.orderMenu,n.id_fourthNivel ";
			
			$rsselectNodeGroupedInfo = WebApp::execQuery($selectNodeGroupedInfo);
			while (!$rsselectNodeGroupedInfo->EOF()) {
				$ke = $rsselectNodeGroupedInfo->Field("ke");
				if (isset($dataMyLecturesCoords[$ke]) && count($dataMyLecturesCoords[$ke])>0) {
				
					$moduleGrid["data"][$indM]["nodeDescription"] 	= $rsselectNodeGroupedInfo->Field("sel_node_descriptions");
					$moduleGrid["data"][$indM]["nodeTitle"] 		= $rsselectNodeGroupedInfo->Field("title");
					$moduleGrid["data"][$indM]["indM"] 				= "".$indM;

					$moduleLectureGrid = array();
					$indML = 0;							
					
					while (list($kkk,$dt) =each($dataMyLecturesCoords[$ke])) {
					if (isset($dataMyLecturesID[$dt])) {
					
						$moduleLectureGrid["data"][$indML++] = $dataMyLecturesID[$dt];
					}}
					
					$moduleLectureGrid["AllRecs"] = count($moduleLectureGrid["data"]);
					WebApp::addVar("UserModuleLectureToGrid_$indM", $moduleLectureGrid);
						
					$indM++;				
				}
				$rsselectNodeGroupedInfo->MoveNext();
			}
		}
		$moduleGrid["AllRecs"] = count($moduleGrid["data"]);
		WebApp::addVar("UserModuleToGrid", $moduleGrid);			
	}
	function userRelatedFeedback () {
	
		$confFeedback = array();
		if (isset($this->appRelSt["LECTURE_RELATED"]["EL"]) && $this->appRelSt["LECTURE_RELATED"]["EL"] > 0) {					
			
			$referenceId = $this->appRelSt["LECTURE_RELATED"]["EL"];

			$confFeedback["lecture"]["ci"]					= $referenceId;
			$confFeedback["lecture"]["title"]				= $this->appRelSt["CiInf"][$referenceId]["title"];
			$confFeedback["lecture"]["nodeDescription"] 	= $this->appRelSt["CiInf"][$referenceId]["nodeDescription"];
			$confFeedback["lecture"]["coord"] 	= $this->appRelSt["CiInf"][$referenceId]["coord"];
			
			if (isset($this->appRelSt["LECTURE_RELATED"]["UF"]) && $this->appRelSt["LECTURE_RELATED"]["UF"] > 0) {	
				$confFeedback["feedback"]["ci"]					= $this->appRelSt["LECTURE_RELATED"]["UF"];
				$confFeedback["feedback"]["title"]				= $this->appRelSt["CiInf"][$confFeedback["feedback"]["ci"]]["title"];
				$confFeedback["feedback"]["nodeDescription"] 	= $this->appRelSt["CiInf"][$confFeedback["feedback"]["ci"]]["nodeDescription"];
			}		
			if (isset($this->appRelSt["LECTURE_RELATED"]["CT"]) && $this->appRelSt["LECTURE_RELATED"]["CT"] > 0) {	
				$confFeedback["discussion"]["ci"]					= $this->appRelSt["LECTURE_RELATED"]["CT"];
				$confFeedback["discussion"]["title"]				= $this->appRelSt["CiInf"][$confFeedback["discussion"]["ci"]]["title"];
				$confFeedback["discussion"]["nodeDescription"] 		= $this->appRelSt["CiInf"][$confFeedback["discussion"]["ci"]]["nodeDescription"];

			}	
			if (isset($this->appRelSt["LECTURE_RELATED"]["ES"]) && count($this->appRelSt["LECTURE_RELATED"]["ES"])>0 ) {
					$ciflow = $this->appRelSt["LECTURE_RELATED"]["ES"];
					$tmp = $this->userTestResult($ciflow);
					if (isset($tmp["totals"]["nr_certificate"])) {
						if ($tmp["totals"]["nr_certificate"]>0) {
							$confFeedback["survey"] = $this->userRelatedSurvey($tmp["totals"]["nr_certificate"]);
						}
					}
			}				
		}
		return $confFeedback;
	}
	function userRelatedSurveyToSurgicalVideo ($referenceId=0) {
		global $session;	

		if ($referenceId>0) {
			
			$progress_state="finished";
			$results_state="";
			$UsersResultsInSurgicalVideos = eLearningUserPlatform::getUsersResultsInSurgicalVideos($session->Vars["ses_userid"],$referenceId,$progress_state,$results_state);

				require_once(INCLUDE_AJAX_PATH."CIExtended/ACExtended.Class.php");
				$confSurvey = ACExtended::getACProp($referenceId,$this->lngId,$this->thisModeCode);
				if ($confSurvey["type_of_survey"]!="none" && $confSurvey["target_survey_ci"]>0) {
					$confSurvey = array_merge($confSurvey, CiManagerFe::getUSInfo($confSurvey["target_survey_ci"]));

						$getInstanceOfSurveyFilling = "SELECT test_state, 
															  date_format(date_of_test,'%d.%m.%Y') as date_of_test,
															  total_sessions, filled_sessions

														 FROM z_survey_user_exam
														WHERE examination_id 	= '".$confSurvey["target_survey_ci"]."'
														  AND user_id 			= '".$session->Vars["ses_userid"]."'
														  AND related_to_ci		= '".$referenceId."'
														  AND test_state in ('running','completed')";

						$rs_con = WebApp::execQuery($getInstanceOfSurveyFilling);
						IF (!$rs_con->EOF()) {
							$date_of_test 		= $rs_con->Field("date_of_test");
							$test_state 		= $rs_con->Field("test_state");
							$total_sessions 	= $rs_con->Field("total_sessions");
							$filled_sessions 	= $rs_con->Field("filled_sessions");

							$confSurvey["survey_filled_sessions"] 		= $filled_sessions;
							$confSurvey["survey_total_sessions"] 		= $total_sessions;
							$confSurvey["survey_test_state"] 			= $test_state;
							$confSurvey["survey_date_of_test"] 			= $date_of_test;

						} else {
							$confSurvey["survey_test_state"] 			= "empty";
						}				
					//survey_test_state
				}
		}
		return $confSurvey;
	}
	function userRelatedSurvey ($nr_test_done=0) {
		global $session;	

		if ($nr_test_done>0) {
					
        $tmpG = array("data" => array(), "AllRecs" => "0");$ind=0;
		
		if (isset($this->appRelSt["LECTURE_RELATED"]["EL"]) && $this->appRelSt["LECTURE_RELATED"]["EL"] > 0) {					
			
			$referenceId 		= $this->appRelSt["LECTURE_RELATED"]["EL"];
			$workingCiSurveyC 	= new CiManagerFe($referenceId, $session->Vars["lang"]);
			$confSurvey 		= $workingCiSurveyC->getSurveyConfigurationInParentNodes($this->thisModeCode,"EL",$referenceId);
			
			if ($confSurvey["type_of_survey"]=="internal" && $confSurvey["target_survey_ci"]=="") {
				
				if (isset($this->appRelSt["LECTURE_RELATED"]["US"]) && $this->appRelSt["LECTURE_RELATED"]["US"] > 0) {	
					$confSurvey["target_survey_ci"]	= $this->appRelSt["LECTURE_RELATED"]["US"];
				}
			}
		
			$existCiForCurrentState = CiManagerFe::controlCiStateForPublishMode($confSurvey["target_survey_ci"]);
			$confSurvey["existCiForCurrentState"] = $existCiForCurrentState;
			
			if ($confSurvey["type_of_survey"]!="none" && $confSurvey["target_survey_ci"]>0) {
					$confSurvey["survey_title"]			= $this->appRelSt["CiInf"][$confSurvey["target_survey_ci"]]["title"];
					$confSurvey["survey_nodeDesc"]		= $this->appRelSt["CiInf"][$confSurvey["target_survey_ci"]]["nodeDescription"];

					$confSurvey["referenceId"]			= $referenceId;
					$confSurvey["reference_title"]		= $this->appRelSt["CiInf"][$referenceId]["title"];
					$confSurvey["reference_nodeDesc"]	= $this->appRelSt["CiInf"][$referenceId]["nodeDescription"];

					$getInstanceOfSurveyFilling = "SELECT test_state, 
														  date_format(date_of_test,'%d.%m.%Y') as date_of_test,
														  total_sessions, filled_sessions

													 FROM z_survey_user_exam
													WHERE examination_id 	= '".$confSurvey["target_survey_ci"]."'
													  AND user_id 			= '".$session->Vars["ses_userid"]."'
													  AND related_to_ci		= '".$this->appRelSt["LECTURE_RELATED"]["EL"]."'
													  AND test_state in ('running','completed')";

					$rs_con = WebApp::execQuery($getInstanceOfSurveyFilling);
					IF (!$rs_con->EOF()) {
						$date_of_test 		= $rs_con->Field("date_of_test");
						$test_state 		= $rs_con->Field("test_state");
						$total_sessions 	= $rs_con->Field("total_sessions");
						$filled_sessions 	= $rs_con->Field("filled_sessions");

						$confSurvey["survey_filled_sessions"] 		= $filled_sessions;
						$confSurvey["survey_total_sessions"] 		= $total_sessions;
						$confSurvey["survey_test_state"] 			= $test_state;
						$confSurvey["survey_date_of_test"] 			= $date_of_test;

					} else {
						$confSurvey["survey_test_state"] 			= "empty";
					}
					//configuration of survey related to state
					$tmpG["data"][0] = $confSurvey;
					$tmpG["AllRecs"] = count($tmpG["data"]);
					WebApp::addVar("surveyFilledConfigurationRelatedToState", $tmpG);		

			}
			//'new','init','running','completed'
			}
		}
		return $confSurvey;
	}
	function userRelatedResult ($controlForSurvey="no") {
		global $session;

		$lectureRelatedResults	= array();
		$appRelSt 				= $this->appRelSt;

		$index = 0;
		if (isset($appRelSt["LECTURE_RELATED_ALL"]["CQ"]) && count($appRelSt["LECTURE_RELATED_ALL"]["CQ"])>0 ) {
			while (list($ciflow,$d)=each($appRelSt["LECTURE_RELATED_ALL"]["CQ"])) {
				
				$tmp = $this->userTestResult($ciflow);
				if ($tmp["totals"]["nr_test_done"]>0) {
					
					$lectureRelatedResults[$index]["ciflow"] 			= $ciflow;
					$lectureRelatedResults[$index]["title"] 			= $appRelSt["CiInf"][$ciflow]["title"];
					$lectureRelatedResults[$index]["nodeDescription"] 	= $appRelSt["CiInf"][$ciflow]["nodeDescription"];
					
					//$lectureRelatedResults[$index] = array_merge($lectureRelatedResults[$index],$tmp["totals"]);
					$lectureRelatedResults[$index]["started"] 		= "".$tmp["totals"]["started"];
					$lectureRelatedResults[$index]["nr_test_done"] 	= "".$tmp["totals"]["nr_test_done"];
					$lectureRelatedResults[$index]["is_running"] 	= "".$tmp["totals"]["is_running"];
					//if ($tmp["totals"]["nr_test_done"]>0) {
						$lectureRelatedResults[$index] = array_merge($lectureRelatedResults[$index],$tmp["Info"]);
					//} 
					$index++;
				}
			}
		}
		if (isset($appRelSt["LECTURE_RELATED_ALL"]["ES"]) && count($appRelSt["LECTURE_RELATED_ALL"]["ES"])>0 ) {
			while (list($ciflow,$d)=each($appRelSt["LECTURE_RELATED_ALL"]["ES"])) {
				$tmp = $this->userTestResult($ciflow);
				if ($tmp["totals"]["nr_test_done"]>0) {
					
					$lectureRelatedResults[$index]["ciflow"] 			= $ciflow;
					$lectureRelatedResults[$index]["title"] 			= $appRelSt["CiInf"][$ciflow]["title"];
					$lectureRelatedResults[$index]["nodeDescription"] 	= $appRelSt["CiInf"][$ciflow]["nodeDescription"];
					
					//$lectureRelatedResults[$index] = array_merge($lectureRelatedResults[$index],$tmp["totals"]);
					$lectureRelatedResults[$index]["started"] 		= "".$tmp["totals"]["started"];
					$lectureRelatedResults[$index]["nr_test_done"] 	= "".$tmp["totals"]["nr_test_done"];
					$lectureRelatedResults[$index]["is_running"] 	= "".$tmp["totals"]["is_running"];
					
					if ($tmp["totals"]["nr_test_done"]>0 && $controlForSurvey!="no") {
						$this->userRelatedSurvey($tmp["totals"]["nr_test_done"]);
					}
					//if ($tmp["totals"]["nr_test_done"]>0) {
						$lectureRelatedResults[$index] = array_merge($lectureRelatedResults[$index],$tmp["Info"]);
					//} 
					$index++;
				}
			}
		}	

		$GridSessionProp["data"] = $lectureRelatedResults;
		$GridSessionProp["AllRecs"] = count($GridSessionProp["data"]);
		WebApp::addVar("UserLectureResultToGrid", $GridSessionProp);	
		
		return $GridSessionProp;
	}
	function userBestResults ($examination_ids,$userId="") {
		global $session;
		$dataTest = array();
		
		if (isset($this->useridp) && $this->useridp>0) $userId=$this->useridp;
		elseif ($userId=="") $userId=$session->Vars["ses_userid"];
		
		$select_totals_status = "
			SELECT	test_id,examination_id,
			
							coalesce(testIdConfigurationRules,'') as testIdConfigurationRules,
							
							date_format(date_of_test,'%d.%m.%Y') as date_of_test,
							date_format(begin_time,'%H:%i:%s') as begin_time, 			

							date_format(date_of_test_end,'%d.%m.%Y') as date_of_test_end,
							date_format(end_time,'%H:%i:%s') as end_time, 	
							
							coalesce(time_spent,0) as time_spent,
							coalesce(time_spent_server,0) as time_spent_server,
							coalesce(timer_response,0) as timer_response,
							coalesce(time_allowed,1) as time_allowed,

							total_points,	
							total_user_points,	
							user_points_perqindje,
							per_points_to_pass,
							
							if ( results_state = 'passed', 1, 2) as orderPassed
							
			FROM z_EccE_user_examination
			WHERE user_id			= '".$userId."'
			  AND examination_id	in (".$examination_ids.")
			  ORDER BY results_state ASC, total_user_points DESC
			  LIMIT 0,1";	
			$rs__totals_status= WebApp::execQuery($select_totals_status);
			if (!$rs__totals_status->EOF())  {
					
					
					$test_id_cert		= $rs__totals_status->Field("test_id");
					$examination_id		= $rs__totals_status->Field("examination_id");
					
					$dataResult["date_of_test"] 			= $rs__totals_status->Field("date_of_test");
					$dataResult["begin_time"] 				= $rs__totals_status->Field("begin_time");
					$dataResult["date_of_test_end"] 		= $rs__totals_status->Field("date_of_test_end");
					$dataResult["end_time"] 				= $rs__totals_status->Field("end_time");

					$dataResult["total_points"] 			= number_format($rs__totals_status->Field("total_points"),0);
					$dataResult["total_user_points"] 		= number_format($rs__totals_status->Field("total_user_points"),0);

					$dataResult["user_points_perqindje"] 	= number_format($rs__totals_status->Field("user_points_perqindje"),0);
					$dataResult["per_points_to_pass"] 		= number_format($rs__totals_status->Field("per_points_to_pass"),0);
					
					$testIdConfigurationRules		= $rs__totals_status->Field("testIdConfigurationRules");
					if ($testIdConfigurationRules!="") {
						$tmp_conf = unserialize(base64_decode($testIdConfigurationRules));
					}
					
					$dataResult["enableTrafficLightFeedback"]	=  "no";
					$dataResult["CertificateRelatedTag"] = "";
					if (isset($tmp_conf["user_report_rules"]["traffic_light_feedback"]) && $tmp_conf["user_report_rules"]["traffic_light_feedback"]=="yes") {

						$dataResult["enableTrafficLightFeedback"]	=  "yes";
						$redLimit 		= $tmp_conf["user_report_rules"]["traffic_light_red"];
						$orangeLimit 	= $tmp_conf["user_report_rules"]["traffic_light_orange"];
						if ($dataResult["user_points_perqindje"]<=$redLimit) {
							$dataResult["trafficLightCase"]	=  "red";
							$tmp_conf["tagsToBeReplaced"]["trafficLightCase"] = "red";			
						} elseif ($dataResult["user_points_perqindje"]<=$orangeLimit) {
							$dataResult["trafficLightCase"]	=  "amber";
							$tmp_conf["tagsToBeReplaced"]["trafficLightCase"] = "amber";
						} else {
							$dataResult["trafficLightCase"]	=  "green";
							$tmp_conf["tagsToBeReplaced"]["trafficLightCase"] = "green";
						}

						$tmp_conf["tagsToBeReplaced"]["user_traffic_light_feedback_tag"] = $tmp_conf["modulDynamicMessages"]["trafficLight"][$tmp_conf["tagsToBeReplaced"]["trafficLightCase"]];
					} else {
						if ($dataResult["user_points_perqindje"]<$tmp_conf["evaluation_rules"]["per_points_to_pass"]) {
							$tmp_conf["tagsToBeReplaced"]["IconTag"] = "<i class=\"fa fa-exclamation-triangle\"></i>";
							$tmp_conf["tagsToBeReplaced"]["examinationColorCase"] = "red";
							$dataResult["trafficLightCase"]	=  "red";
							$tmp_conf["tagsToBeReplaced"]["UserResultRelatedTag"] = $tmp_conf["modulDynamicMessages"]["result"]["Failed"];
							$tmp_conf["tagsToBeReplaced"]["CmeCreditsRelatedTag"] = $tmp_conf["modulDynamicMessages"]["cmeCredits"]["Failed"];
						} else {
							$dataResult["trafficLightCase"]	=  "green";
							$tmp_conf["tagsToBeReplaced"]["IconTag"] = "<i class=\"fa fa-graduation-cap\"></i>";
							$tmp_conf["tagsToBeReplaced"]["examinationColorCase"] = "green";
							$tmp_conf["tagsToBeReplaced"]["UserResultRelatedTag"] = $tmp_conf["modulDynamicMessages"]["result"]["Passed"];
							$tmp_conf["tagsToBeReplaced"]["CmeCreditsRelatedTag"] = $tmp_conf["modulDynamicMessages"]["cmeCredits"]["Passed"];
							//$tmp_conf["user_report_rules"]["user_certificate"] = "no";
							if ($tmp_conf["user_report_rules"]["user_certificate"]=="yes") {
								if ($tmp_conf["user_report_rules"]["certificate_evaluation"]=="automatic") {
									
									$tmp_conf["tagsToBeReplaced"]["CertificateRelatedTag"] = $tmp_conf["modulDynamicMessages"]["certificate"]["immediate"];
									$controllCert = "SELECT COUNT(1) as  exist
													   FROM z_EccE_user_examination_certificate
													  WHERE test_id = '".$examination_id."'";
									$rsControllCert= WebApp::execQuery($controllCert);
									if (!$rsControllCert->EOF())  {	
											$dataResult["CertificateRelatedTag"] =
											"<a class=\"pull-right view-pdf\" href=\"{{APP_URL}}get_certificate.php%3Ftest_id%3D".$test_id_cert."\" title=\"{{_view_certificate}}\"><i class=\"fa-fw fa fa fa-graduation-cap\"></i></a>";
									}
								} elseif ($tmp_conf["user_report_rules"]["certificate_evaluation"]=="deadline") {
									//certificate_evaluation_days
									$tmp_conf["tagsToBeReplaced"]["CertificateRelatedTag"] = $tmp_conf["modulDynamicMessages"]["certificate"]["later"];
									$tmp_conf["tagsToBeReplaced"]["certificate_days"]		 = $tmp_conf["user_report_rules"]["certificate_evaluation_days"];
									$getDifDate = "SELECT DATEDIFF(now(), date_of_test_end)  as nrDays, 
														date_of_test_end, 
														date_of_test,
														now() as now_date
													 FROM z_EccE_user_examination
													WHERE test_id = '".$examination_id."'";	
									$rs__getDifDate= WebApp::execQuery($getDifDate);
									if (!$rs__getDifDate->EOF())  {
										$nrDays 			= $rs__getDifDate->Field("nrDays");
										$date_of_test 		= $rs__getDifDate->Field("date_of_test");
										$date_of_test_end 	= $rs__getDifDate->Field("date_of_test_end");
										$now_date 			= $rs__getDifDate->Field("now_date");

										if ($tmp_conf["user_report_rules"]["certificate_evaluation_days"]<$nrDays ) {
											$dataResult["CertificateRelatedTag"] =
											"<a class=\"pull-right view-pdf\" href=\"{{APP_URL}}get_certificate.php%3Ftest_id%3D".$test_id_cert."\" title=\"{{_view_certificate}}\"><i class=\"fa-fw fa fa fa-graduation-cap\"></i></a>";
										}
									}								
								} 							
							}
						}
					}					
			}
		return $dataResult;
	}
	function userTestResultMulti ($examination_ids,$userIdParam="") {
		global $session;
		
		
		if ($userIdParam>0 && $userIdParam!="") 			$userId=$userIdParam;
		elseif (isset($this->useridp) && $this->useridp>0) 	$userId=$this->useridp;
		elseif ($userId=="")								$userId=$session->Vars["ses_userid"];
		
		$dataTest = array();
		$select_totals_status = "
			SELECT  count(1) as nr_test_tot,examination_id,
					
					 COUNT( CASE WHEN (test_state = 'Evaluated' AND results_state = 'passed') THEN 1 END) passed,
					 COUNT( CASE WHEN (test_state = 'Evaluated' AND results_state = 'not_passed') THEN 1 END) not_passed,
					 COUNT( CASE WHEN (test_state != 'Evaluated' ) THEN 1 END) in_proces			

			FROM z_EccE_user_examination
			WHERE user_id			= '".$userId."'
			  AND examination_id	in (".$examination_ids.")
			  GROUP BY examination_id";	

			$rs__totals_status= WebApp::execQuery($select_totals_status);
			WHILE (!$rs__totals_status->EOF())  {
				
				$examination_id =  $rs__totals_status->Field("examination_id");
				
				$dataTest[$examination_id]["nr_test_tot"] 		= $rs__totals_status->Field("nr_test_tot");
				$dataTest[$examination_id]["in_proces"] 		= $rs__totals_status->Field("in_proces");
				$dataTest[$examination_id]["passed"] 			= $rs__totals_status->Field("passed");
				$dataTest[$examination_id]["not_passed"] 		= $rs__totals_status->Field("not_passed");
				
				$rs__totals_status->MoveNext();
			}

			$select_totals_status = "
			SELECT  count(1) as nr_test_tot,
			
					z_EccE_user_examination.examination_id,
					z_EccE_user_examination.history_flag,
					z_EccE_user_examination.history_id,
			
					coalesce(access_type,'without_external_control') as access_type, 
					coalesce(related_event,'') as related_event, 
					coalesce(examination_mode,'') as examination_mode, 
					coalesce(external_configuration_rules,'') as external_configuration_rules, 
					coalesce(history_begins,'') as history_begins, 
					coalesce(history_ends,'') as history_ends, 
					coalesce(record_user_id,'') as record_user_id, 
			
					
					
					 COUNT( CASE WHEN (test_state = 'Evaluated' AND results_state = 'passed') THEN 1 END) passed,
					 COUNT( CASE WHEN (test_state = 'Evaluated' AND results_state = 'not_passed') THEN 1 END) not_passed,
					 COUNT( CASE WHEN (test_state != 'Evaluated' ) THEN 1 END) in_proces			

			FROM z_EccE_user_examination
	  LEFT JOIN z_EccE_user_examination_locked_control 
	  		  ON z_EccE_user_examination_locked_control.examination_id = z_EccE_user_examination.examination_id
	  		  AND z_EccE_user_examination_locked_control.user_id = z_EccE_user_examination.user_id
			WHERE z_EccE_user_examination.user_id			= '".$userId."'
			  AND z_EccE_user_examination.examination_id	in (".$examination_ids.")
			  GROUP BY  z_EccE_user_examination.history_id";	

			$rs__totals_status= WebApp::execQuery($select_totals_status);
			WHILE (!$rs__totals_status->EOF())  {
				
				$examination_id =  $rs__totals_status->Field("examination_id");
				$history_flag =  $rs__totals_status->Field("history_flag");
				$history_id =  $rs__totals_status->Field("history_id");
				
				$dataTest["grouped"][$history_id][$examination_id]["nr_test_tot"] 		= $rs__totals_status->Field("nr_test_tot");
				$dataTest["grouped"][$history_id][$examination_id]["in_proces"] 		= $rs__totals_status->Field("in_proces");
				$dataTest["grouped"][$history_id][$examination_id]["passed"] 			= $rs__totals_status->Field("passed");
				$dataTest["grouped"][$history_id][$examination_id]["not_passed"] 		= $rs__totals_status->Field("not_passed");
				$dataTest["grouped"][$history_id][$examination_id]["access_type"] 				= $rs__totals_status->Field("access_type");
				$dataTest["grouped"][$history_id][$examination_id]["related_event"] 			= $rs__totals_status->Field("related_event");
				$dataTest["grouped"][$history_id][$examination_id]["examination_mode"] 			= $rs__totals_status->Field("examination_mode");
				$dataTest["grouped"][$history_id][$examination_id]["external_configuration_rules"] 		= $rs__totals_status->Field("external_configuration_rules");
				$dataTest["grouped"][$history_id][$examination_id]["history_begins"] 			= $rs__totals_status->Field("history_begins");
				$dataTest["grouped"][$history_id][$examination_id]["history_ends"] 				= $rs__totals_status->Field("history_ends");
				$dataTest["grouped"][$history_id][$examination_id]["record_user_id"] 			= $rs__totals_status->Field("record_user_id");
				$rs__totals_status->MoveNext();
			}
			return $dataTest;
	}	
	function userFirstAttemptConfiguration ($examination_id,$userId="") {
		global $session;
		
		$configuration_rules = array();
		
		$selectConfiguationOfFirstAttempt = "
				SELECT  test_id, coalesce(testIdConfigurationRules,'') as testIdConfigurationRules		
				FROM z_EccE_user_examination
				WHERE user_id			= '".$userId."'
				  AND examination_id	= '".$examination_id."'
				  AND test_state not in ('new','init','runinng')
			 ORDER BY test_id ASC
			 limit 0,1";			

		$rs__firstAttemptConf= WebApp::execQuery($selectConfiguationOfFirstAttempt);
		if (!$rs__firstAttemptConf->EOF())  {
				
			$testIdConfigurationRules = $rs__firstAttemptConf->Field("testIdConfigurationRules");
			if ($testIdConfigurationRules!="") {
				$tmp_conf = unserialize(base64_decode($testIdConfigurationRules));
				if (isset($tmp_conf["configuration_rules"])) {
					$configuration_rules = $tmp_conf["configuration_rules"];
				}
			}				
		}
		return $configuration_rules;
	}	
	function userTestResult ($examination_id,$userId="",$examination_history_id="") {
		global $session;

		if (isset($this->useridp) && $this->useridp>0) 	$userId=$this->useridp;
		elseif ($userId=="") 							$userId=$session->Vars["ses_userid"];
		
		$condition="";
		if ($examination_history_id>0) $condition=" AND history_id = '".$examination_history_id."' ";
	
		$tmp_conf = array();
		$dataResult = array();
		$dataItemGrid = array();
		$index=0;
		
		$GridSessionProp["data"] = array();	
		$totals["started"] 		=  0;
		$totals["nr_test_done"] 	=  0;
		$totals["is_running"] 	=  0;
		$totals["is_evaluated"] 	=  0;
		$totals["nr_certificate"] =  0;
		$totals["not_passed"] 	=  0;
		
		$totals["bestResult"] 	=  0;
		$totals["totalPoints"] 	=  0;
				
				//gjenden totalet per plotesimet ne lidhje me assesment
					$select_totals_status = "
						SELECT  test_id,test_state,
			
							coalesce(testIdConfigurationRules,'') as testIdConfigurationRules,
							
							date_format(date_of_test,'%d.%m.%Y') as date_of_test,
							date_format(begin_time,'%H:%i:%s') as begin_time, 			

							date_format(date_of_test_end,'%d.%m.%Y') as date_of_test_end,
							date_format(end_time,'%H:%i:%s') as end_time, 			

							total_user_time,

							if (date_of_test!=date_of_test_end, 'no','yes') as same_date,

							'bg-color-green' as actual,
							
							test_state,
							if (test_state='Evaluated','Evaluated','in_progress') as test_state_flag,
							
							if (test_state='Evaluated',1,0) as finished,
							if (test_state in ('new','init','runinng') ,1,0) as runinng,
							if (test_state='readyToEvaluate' ,1,0) as readyToEvaluate,
							
							if (test_state='Evaluated' AND results_state = 'passed',1,0) as passed,
							if (test_state='Evaluated' AND results_state = 'not_passed',1,0) as not_passed,

							if (test_state='Evaluated' AND results_state = 'passed','green','red') as fa_color,
							if (test_state='Evaluated' AND results_state = 'passed','fa-graduation-cap','fa-user-times') as fa_icon,
							if (test_state='Evaluated' AND results_state = 'passed','yes','no') as is_passed,

							coalesce(time_spent,0) as time_spent,
							coalesce(time_spent_server,0) as time_spent_server,
							coalesce(timer_response,0) as timer_response,
							coalesce(time_allowed,1) as time_allowed,

							total_points,	
							total_user_points,	
							user_points_perqindje,
							per_points_to_pass			
						 
			FROM z_EccE_user_examination
			WHERE user_id			= '".$userId."'
			  AND examination_id	= '".$examination_id."'
			  ".$condition."
			 
		 ORDER BY test_id DESC";	// AND test_state not in ('new','init','runinng')

			$rs__totals_status= WebApp::execQuery($select_totals_status);
			$lastResult = array();
			$lastResultID = "0";
			
			$lastAttempt = array();
			$lastAttemptID = "0";
			While (!$rs__totals_status->EOF())  {
				
				$testIdConfigurationRules			= $rs__totals_status->Field("testIdConfigurationRules");
				
				$totals["started"] 			+=  1;
				$totals["nr_test_done"] 	+=  $rs__totals_status->Field("finished");
				$totals["is_running"] 		+=  $rs__totals_status->Field("runinng");
				$totals["is_evaluated"] 	+=  $rs__totals_status->Field("readyToEvaluate");
				$totals["nr_certificate"] 	+=  $rs__totals_status->Field("passed");
				$totals["passed"] 			+=  $rs__totals_status->Field("passed");
				$totals["not_passed"] 		+=  $rs__totals_status->Field("not_passed");
				
				$test_state			= $rs__totals_status->Field("test_state");
				$test_id_cert		= $rs__totals_status->Field("test_id");
				$dataResult["CertificateRelatedTag"] ="";
				$Info = array();
					
					if ($testIdConfigurationRules!="") {
						$tmp_conf = unserialize(base64_decode($testIdConfigurationRules));
					}	
					$timer_remained								= $rs__totals_status->Field("timer_response");
					$time_allowed								= $rs__totals_status->Field("time_allowed");
					if ($time_allowed>120)
							$dataResult["total_time_allowed"]			= round($time_allowed/60,0);	
					else	$dataResult["total_time_allowed"]			= round($time_allowed/60,1);	
					//$dataResult["total_time_allowed_formated"]	= generalFunctionality::secondsToTime($time_allowed,"yes");

					$time_spent											= $rs__totals_status->Field("time_spent");
					$time_spent_server									= $rs__totals_status->Field("time_spent_server");
					$time_spended_by_the_user							= $time_spent_server;		
					$time_spended_by_the_user							= $time_spent;		
					//SI DO KALKULOHET TIME NE QUIZ
					
					$dataResult["time_spended_by_the_user_in_seconds"]	= $time_spended_by_the_user;	
					$dataResult["time_spended_by_the_user"] 			= round($time_spended_by_the_user/60,0);	

					if ($time_spended_by_the_user>120)
							$dataResult["time_spended_by_the_user"]			= round($time_spended_by_the_user/60,1);	
					else	$dataResult["time_spended_by_the_user"]			= round($time_spended_by_the_user/60,1);				
					//$dataResult["time_spended_by_the_user_formated"] 	= generalFunctionality::secondsToTime($time_spended_by_the_user,"ye");


					$tmp_conf["tagsToBeReplaced"]["total_time_allowed"] 				= $dataResult["total_time_allowed"];				
					//$tmp_conf["tagsToBeReplaced"]["total_time_allowed_formated"] 		= $dataResult["total_time_allowed_formated"];				
					$tmp_conf["tagsToBeReplaced"]["time_spended_by_the_user"] 			= $dataResult["time_spended_by_the_user"];				
					//$tmp_conf["tagsToBeReplaced"]["time_spended_by_the_user_formated"]	= $dataResult["time_spended_by_the_user_formated"];				

					$dataResult["same_date"] 				= $rs__totals_status->Field("same_date");
					$dataResult["date_of_test"] 			= $rs__totals_status->Field("date_of_test");
					$dataResult["begin_time"] 				= $rs__totals_status->Field("begin_time");
					$dataResult["date_of_test_end"] 		= $rs__totals_status->Field("date_of_test_end");
					$dataResult["end_time"] 				= $rs__totals_status->Field("end_time");

					$dataResult["total_points"] 			= "".round($rs__totals_status->Field("total_points"),1);
					$dataResult["test_state_flag"] 				= $rs__totals_status->Field("test_state_flag");
					
					if ($dataResult["test_state_flag"]=='Evaluated') {
					
						$dataResult["total_user_points"] 		= "".round($rs__totals_status->Field("total_user_points"),1);

						$dataResult["user_points_perqindje"] 	= "".number_format($rs__totals_status->Field("user_points_perqindje"),0);
						$dataResult["per_points_to_pass"] 		= "".number_format($rs__totals_status->Field("per_points_to_pass"),0);

						$tmp_conf["tagsToBeReplaced"]["user_points_percentage"]	= $dataResult["user_points_perqindje"];
						$tmp_conf["tagsToBeReplaced"]["total_user_points"]		= $dataResult["total_user_points"];
						$tmp_conf["tagsToBeReplaced"]["total_points"]			= $dataResult["total_points"];	

						$dataResult["enableTrafficLightFeedback"]	=  "no";
						if (isset($tmp_conf["user_report_rules"]["traffic_light_feedback"]) && $tmp_conf["user_report_rules"]["traffic_light_feedback"]=="yes") {

							$dataResult["enableTrafficLightFeedback"]	=  "yes";
							$redLimit 		= $tmp_conf["user_report_rules"]["traffic_light_red"];
							$orangeLimit 	= $tmp_conf["user_report_rules"]["traffic_light_orange"];
							
							if ($dataResult["user_points_perqindje"]<=$redLimit) {
								$dataResult["trafficLightCase"]	=  "red";
								$tmp_conf["tagsToBeReplaced"]["trafficLightCase"] = "red";			
							} elseif ($dataResult["user_points_perqindje"]<=$orangeLimit) {
								$dataResult["trafficLightCase"]	=  "amber";
								$tmp_conf["tagsToBeReplaced"]["trafficLightCase"] = "amber";
							} else {
								$dataResult["trafficLightCase"]	=  "green";
								$tmp_conf["tagsToBeReplaced"]["trafficLightCase"] = "green";
							}

							$tmp_conf["tagsToBeReplaced"]["user_traffic_light_feedback_tag"] = $tmp_conf["modulDynamicMessages"]["trafficLight"][$tmp_conf["tagsToBeReplaced"]["trafficLightCase"]];
						} else {
							if ( $dataResult["user_points_perqindje"]<$rs__totals_status->Field("per_points_to_pass")) {
								$tmp_conf["tagsToBeReplaced"]["IconTag"] = "<i class=\"fa fa-exclamation-triangle\"></i>";
								$tmp_conf["tagsToBeReplaced"]["examinationColorCase"] = "red";
								$dataResult["trafficLightCase"]	=  "red";
								$tmp_conf["tagsToBeReplaced"]["UserResultRelatedTag"] = $tmp_conf["modulDynamicMessages"]["result"]["Failed"];
								$tmp_conf["tagsToBeReplaced"]["CmeCreditsRelatedTag"] = $tmp_conf["modulDynamicMessages"]["cmeCredits"]["Failed"];
							} else {
								$dataResult["trafficLightCase"]	=  "green";
								$tmp_conf["tagsToBeReplaced"]["IconTag"] = "<i class=\"fa fa-graduation-cap\"></i>";
								$tmp_conf["tagsToBeReplaced"]["examinationColorCase"] = "green";
								$tmp_conf["tagsToBeReplaced"]["UserResultRelatedTag"] = $tmp_conf["modulDynamicMessages"]["result"]["Passed"];
								$tmp_conf["tagsToBeReplaced"]["CmeCreditsRelatedTag"] = $tmp_conf["modulDynamicMessages"]["cmeCredits"]["Passed"];
								
								if ($tmp_conf["user_report_rules"]["user_certificate"]=="yes") {

									if ($tmp_conf["user_report_rules"]["certificate_evaluation"]=="automatic") {

										$tmp_conf["tagsToBeReplaced"]["CertificateRelatedTag"] = $tmp_conf["modulDynamicMessages"]["certificate"]["immediate"];
										$controllCert = "SELECT COUNT(1) as  exist
														   FROM z_EccE_user_examination_certificate
														  WHERE test_id = '".$examination_id."'";
										$rsControllCert= WebApp::execQuery($controllCert);
										if (!$rsControllCert->EOF())  {	
												$dataResult["CertificateRelatedTag"] =
												"<a class=\"pull-right view-pdf\" href=\"{{APP_URL}}get_certificate.php%3Ftest_id%3D".$test_id_cert."\" title=\"{{_view_certificate}}\"><i class=\"fa-fw fa fa fa-graduation-cap\"></i></a>";
										}
									} elseif ($tmp_conf["user_report_rules"]["certificate_evaluation"]=="deadline") {
										//certificate_evaluation_days
										$tmp_conf["tagsToBeReplaced"]["CertificateRelatedTag"] = $tmp_conf["modulDynamicMessages"]["certificate"]["later"];
										$tmp_conf["tagsToBeReplaced"]["certificate_days"]		 = $tmp_conf["user_report_rules"]["certificate_evaluation_days"];
										$getDifDate = "SELECT DATEDIFF(now(), date_of_test_end)  as nrDays, 
															date_of_test_end, 
															date_of_test,

															now() as now_date


														 FROM z_EccE_user_examination
														WHERE test_id = '".$examination_id."'";	
										$rs__getDifDate= WebApp::execQuery($getDifDate);
										if (!$rs__getDifDate->EOF())  {
											$nrDays 			= $rs__getDifDate->Field("nrDays");
											$date_of_test 		= $rs__getDifDate->Field("date_of_test");
											$date_of_test_end 	= $rs__getDifDate->Field("date_of_test_end");
											$now_date 			= $rs__getDifDate->Field("now_date");

											if ($tmp_conf["user_report_rules"]["certificate_evaluation_days"]<$nrDays ) {
												$dataResult["CertificateRelatedTag"] =
												"<a class=\"pull-right view-pdf\" href=\"{{APP_URL}}get_certificate.php%3Ftest_id%3D".$test_id_cert."\" title=\"{{_view_certificate}}\"><i class=\"fa-fw fa fa fa-graduation-cap\"></i></a>";
											}
										}								
									} 
								}
							}
						}
					} else {
						//ne process
					}
				
				$dataResult["test_id_cert"]  = $test_id_cert;
				if (count($Info)==0) {
					$Info = $dataResult;
				}
				if ($totals["bestResult"] < $dataResult["user_points_perqindje"]) { // && $dataResult["user_points_perqindje"]>0
					$totals["bestResult"] = $dataResult["user_points_perqindje"];
					$totals["bestResultTestId"] = $test_id_cert;
					$Info = $dataResult;
				}
				
				if ($dataResult["test_state_flag"]=='Evaluated' && $test_id_cert>$lastResultID) {
					$lastResultID = $test_id_cert;
					$lastResult = $dataResult;
				}
				
				if ($lastAttemptID==0) {
					$lastAttempt = $dataResult;
					$lastAttemptID = $test_id_cert;				
				}
				
				$dataResult["trafficLightCaseG"]	=  $dataResult["trafficLightCase"];
				$tmp["attempts"][$index] 	= $dataResult;
				$tmp["attempts_id"][$index] = $test_id_cert;					
				$dataItemGrid["data"][$index++] 	= $dataResult;	
				$rs__totals_status->MoveNext();
			}
			
			$tmp["totals"] = $totals;
			$tmp["Info"] = $Info;
			$tmp["lastResult"] = $lastResult;
			$tmp["lastAttempt"] = $lastAttempt;
			
			$dataItemGrid["AllRecs"] = count($dataItemGrid["data"]);
			WebApp::addVar("UserLectureResultToGrid_$examination_id", $dataItemGrid);		
			return $tmp;
	}


	function userRelatedProgress ($contentId="") {
		global $session;

    	if ($contentId=="")	$idReference 		= $session->Vars["contentId"];
    	else				$idReference 		= $contentId;

		$dataToRtrUserRelatedProgress = array();
		$lectureRelatedProgress = array();
		$this->totalTime = 0;
		$nrPathNav = 0;

		if (isset($this->appRelSt["lecNav"][$idReference]))
			 $pathNAV 	= $this->appRelSt["lecNav"][$idReference];
		else $pathNAV 	= $this->appRelSt["lecNav"][$idRef];

		$appRelSt = $this->appRelSt;
		$totalTime=0;

		if (isset($this->appRelSt["LECTURE_RELATED"]["EL"])) {
			$lectureId = $this->appRelSt["LECTURE_RELATED"]["EL"];

		if (isset($this->appRelSt["LECTURE_RELATED"]["RA"])) {
			$RAID = $this->appRelSt["LECTURE_RELATED"]["RA"];
			$this->getRAcategory ($RAID);
		}

			if (isset($pathNAV) && count($pathNAV)>0 ) {
				
				$idsToAnalitics = array();
				while (list($ord,$ciflow)=each($pathNAV)) {
					$ciType = $appRelSt["CiInf"][$ciflow]["ci_type"];
					if ($ciType!="CQ" && $ciType!="ES") {
						$idsToAnalitics[] = $ciflow;
					}
				}
				
				$ids = implode(",",$idsToAnalitics);
				$this->getCiAnalytics($ids);
				reset($idsToAnalitics);
				while (list($ord,$ciflow)=each($idsToAnalitics)) {
					$this->getCiRelatedWithRa($ciflow);
					if (isset($this->CiAnalytics[$ciflow])) {
						$progresInfo[$ciflow] = $this->CiAnalytics[$ciflow];
					} 
					$index++;
				}
				$index = 0;
				reset($pathNAV);
				while (list($ord,$ciflow)=each($pathNAV)) {
				
					$lectureRelatedProgress[$index]["ciflow"] 				= $ciflow;
					$lectureRelatedProgress[$index]["title"] 				= $appRelSt["CiInf"][$ciflow]["title"];
					$lectureRelatedProgress[$index]["nodeDescription"] 		= $appRelSt["CiInf"][$ciflow]["nodeDescription"];
					$lectureRelatedProgress[$index]["statusGrid"] 			= "red";;
					$ciType 												= $appRelSt["CiInf"][$ciflow]["ci_type"];

					$lectureRelatedProgress[$index]["ci_duration"]			= "0";
					if ($ciType!="CQ" && $ciType!="ES") {
						if (isset($progresInfo[$ciflow]["ci_duration"])&& $progresInfo[$ciflow]["ci_duration"]>0)
							$lectureRelatedProgress[$index]["ci_duration"]			= "".$progresInfo[$ciflow]["ci_duration"];
						$totalTime +=$lectureRelatedProgress[$index]["ci_duration"];
					}

					$lectureRelatedProgress[$index]["colorRelatedToType"]	= "grey";
					$lectureRelatedProgress[$index]["progresRelatedToType"] = 1;
					$lectureRelatedProgress[$index]["progresRelatedToType"] = 1;
					$lectureRelatedProgress[$index]["ciType"]	= "$ciType";

						if ($ciType=="SL") { 

							$nrItemsTotal = "".$this->nrItems[$ciflow]["total"];
							$lectureRelatedProgress[$index]["nrItemsTotal"] 		= "".$nrItemsTotal;
							
							if (isset($progresInfo[$ciflow]["nr_of_views"]) && $progresInfo[$ciflow]["nr_of_views"]>0) {
								
								$nrItemsOpened 		= $this->nrItems[$ciflow]["viewd"];//+1
								$lectureRelatedProgress[$index]["nrItemsOpened"] 		= "".$nrItemsOpened;

								$lectureRelatedProgress[$index]["statusRelatedToType"] = "".$progresInfo[$ciflow]["status"];
								if ($nrItemsOpened>0)
								$lectureRelatedProgress[$index]["progresRelatedToType"]	= "".round(($nrItemsOpened/$nrItemsTotal)*100,0);

								if ($lectureRelatedProgress[$index]["progresRelatedToType"]=="100") {
									$lectureRelatedProgress[$index]["colorRelatedToType"]	= "green";
								} else {
									$lectureRelatedProgress[$index]["colorRelatedToType"]	= "amber";
								}
							} else {
								$lectureRelatedProgress[$index]["nrItemsOpened"] 		= "0";
							}

						} elseif ($ciType=="SM") {
							
							$nrItemsTotal 	= "0";
							$nrOpened 		= "0";
							$duration 		= "0";
														
							if (isset($this->raid_cis["all"]) && count($this->raid_cis["all"])>0) {
								$nrItemsTotal = "".(count($this->raid_cis["all"]));//+1
								$ids1 = implode(",",$this->raid_cis["all"]);
								$dataToReturn 	= $this->getRAAnalyticsCat ($ids1);	
								if (isset($dataToReturn["nrOpened"]))
									$nrOpened = $dataToReturn["nrOpened"];					
							}
							
							$lectureRelatedProgress[$index]["nrItemsTotal"] 		= "".$nrItemsTotal;
							if (isset($progresInfo[$ciflow]["nr_of_views"]) && $progresInfo[$ciflow]["nr_of_views"]>0) {
							
								$nrItemsOpened 		= $nrOpened;//+1
								$lectureRelatedProgress[$index]["nrItemsOpened"] 		= "".$nrItemsOpened;

								$lectureRelatedProgress[$index]["statusRelatedToType"] = "".$progresInfo[$ciflow]["status"];
								if ($nrItemsTotal>0)
								$lectureRelatedProgress[$index]["progresRelatedToType"]	= "".round(($nrItemsOpened/$nrItemsTotal)*100,0);

								if ($lectureRelatedProgress[$index]["progresRelatedToType"]=="100") {
									$lectureRelatedProgress[$index]["colorRelatedToType"]	= "green";
								} else {
									$lectureRelatedProgress[$index]["colorRelatedToType"]	= "amber";
								}
							} else {
								$lectureRelatedProgress[$index]["nrItemsOpened"] 		= "0";
							}

						} elseif ($ciType=="CQ" || $ciType=="ES") {

								$lectureRelatedProgress[$index]["CertificateRelatedTag"] = "";
								$lectureRelatedProgress[$index]["resultExist"] = "no";
	
								$tmp = $this->userTestResult($ciflow);
								if (isset($tmp["Info"]) && Count($tmp["Info"])>0) {
									
									$examInfo = $tmp["Info"];
									if (isset($tmp["lastResult"])) {
										$examInfo = array();
										$examInfo = $tmp["lastResult"];
									}	
									if (isset($tmp["lastAttempt"])) {
										$examInfo = array();
										$examInfo = $tmp["lastAttempt"];
									}										
									$lectureRelatedProgress[$index] = array_merge($lectureRelatedProgress[$index],$examInfo);			
									if ($examInfo["test_state_flag"]=='in_progress') {
										$lectureRelatedProgress[$index]["resultExist"] = "in_progress";
										$lectureRelatedProgress[$index]["statusRelatedToType"]	= "amber";
										$lectureRelatedProgress[$index]["colorRelatedToType"]	= "amber";
									}

									if ($examInfo["test_state_flag"]=='Evaluated') {
										$lectureRelatedProgress[$index]["resultExist"] = "yes";
										$lectureRelatedProgress[$index]["statusRelatedToType"] 	= $examInfo["trafficLightCase"];
										$lectureRelatedProgress[$index]["colorRelatedToType"] 	= $examInfo["trafficLightCase"];													
									}									
									
									$lectureRelatedProgress[$index]["ci_duration"] = $examInfo["time_spended_by_the_user_in_seconds"];
									$totalTime +=$lectureRelatedProgress[$index]["ci_duration"];
								} 

						} elseif ($ciType=="SC") {						
							if (isset($this->appRelSt["LECTURE_RELATED"]["SC"])) {
								$SCID = $this->appRelSt["LECTURE_RELATED"]["SC"];
								
								$this->getListOfSlides ($SCID);
								$ids1 = implode(",",$this->slides_cis);
								$nrItemsTotal= count($this->slides_cis);
								$lectureRelatedProgress[$index]["nrItemsTotal"] 		= "".$nrItemsTotal;

								if ($lectureRelatedProgress[$index]["ci_duration"]>0) {
									
									$dataToReturn 	= $this->getRAAnalyticsCat ($ids1);
									$nrItemsOpened	= $dataToReturn["nrOpened"];										
									$lectureRelatedProgress[$index]["nrItemsOpened"] 		= "".$nrItemsOpened;
									$lectureRelatedProgress[$index]["statusRelatedToType"] = "".$progresInfo[$ciflow]["status"];
									if ($nrItemsOpened>0)
									$lectureRelatedProgress[$index]["progresRelatedToType"]	= "".round(($nrItemsOpened/$nrItemsTotal)*100,0);

									if ($lectureRelatedProgress[$index]["progresRelatedToType"]=="100") {
										$lectureRelatedProgress[$index]["colorRelatedToType"]	= "green";
									} elseif ($lectureRelatedProgress[$index]["progresRelatedToType"]=="0") {
										$lectureRelatedProgress[$index]["colorRelatedToType"]	= "red";
										$lectureRelatedProgress[$index]["progresRelatedToType"] = 1;
									} else {
										$lectureRelatedProgress[$index]["colorRelatedToType"]	= "amber";
									}	
								} else {
									$lectureRelatedProgress[$index]["nrItemsOpened"] 		= "0";
								}
							}					
						}
					$lectureRelatedProgress[$index]["ci_duration_formated"]	= "".generalFunctionality::secondsToTime($lectureRelatedProgress[$index]["ci_duration"],"yes");
					$index++;
				}				
			}
		}
	
		$total_time_formated = generalFunctionality::secondsToTime($totalTime,"yes");
		//$total_time_formated .= "-".$totalTime;
		WebApp::addVar("total_time_formated", $total_time_formated);		
		$GridSessionProp["data"] = $lectureRelatedProgress;
		$GridSessionProp["AllRecs"] = count($GridSessionProp["data"]);
		WebApp::addVar("UserLectureProgressToGrid", $GridSessionProp);		
		
		$dataToRtrUserRelatedProgress["lectureRelatedProgress"] = $GridSessionProp;
		$dataToRtrUserRelatedProgress["totals"]["total_time_formated"] = $total_time_formated;
		

		$nrPathNav =$GridSessionProp["AllRecs"];
		WebApp::addVar("nrPathNav", $nrPathNav);	
		
		$dataToReturn["nrPathNav"] = $nrPathNav;
		if ($nrPathNav>0) {
			$nrPathNavWidth = 100/$nrPathNav;
			WebApp::addVar("nrPathNavWidth", $nrPathNavWidth);	
		}
		
		if (isset($this->appRelSt["LECTURE_RELATED"]["RA"])) {
			$RAID = $this->appRelSt["LECTURE_RELATED"]["RA"];
			$this->getRAcategory ($RAID);

			if (isset($this->raid_cis) && count($this->raid_cis)>0) {
					while (list($categoies,$cids)=each($this->raid_cis)) {
			
						if ($categoies!="other") {
							
							$lectureRelatedProgress[$index]["ciType"] 			= "RA";
							
							$ids1 = implode(",",$cids);
							if ($categoies=="all")
									$catLabel = $appRelSt["CiInf"][$RAID]["nodeDescription"];
							elseif ($categoies=="other")
									$catLabel = "{{_other}}";
							else	$catLabel = $categoies;
							//$this->getCiAnalytics($ids1,$categoies);
							$nrItemsTotal= count($cids);
							$dataToReturn 	= $this->getRAAnalyticsCat ($ids1);
							$nrItemsOpened	= $dataToReturn["nrOpened"];
							$duration		= $dataToReturn["duration"];
							
							$lectureRelatedProgress[$index]["ci_duration"]			= "".$duration;
							$lectureRelatedProgress[$index]["ci_duration_formated"]	= "".generalFunctionality::secondsToTime($duration,"yes");
							
							$lectureRelatedProgress[$index]["nrItemsTotal"] 		= "".$nrItemsTotal;
							$lectureRelatedProgress[$index]["nrItemsOpened"] 		= "".$nrItemsOpened;

							$lectureRelatedProgress[$index]["statusRelatedToType"] = $progresInfo[$ciflow]["status"];
							$lectureRelatedProgress[$index]["progresRelatedToType"]	= round(($nrItemsOpened/$nrItemsTotal)*100,0);
							
							if ($lectureRelatedProgress[$index]["progresRelatedToType"]=="100") {
								$lectureRelatedProgress[$index]["colorRelatedToType"]	= "green";
							} elseif ($lectureRelatedProgress[$index]["progresRelatedToType"]=="0") {
								$lectureRelatedProgress[$index]["colorRelatedToType"]	= "red";
								$lectureRelatedProgress[$index]["progresRelatedToType"] = 1;
							} else {
								$lectureRelatedProgress[$index]["colorRelatedToType"]	= "amber";
							}
							
							$lectureRelatedProgress[$index]["ciflow"] 			= $RAID;
							$lectureRelatedProgress[$index]["title"] 			= $appRelSt["CiInf"][$RAID]["title"];
							$lectureRelatedProgress[$index]["nodeDescription"] 	= $catLabel;
						}
						$index++;
					}			
			}
		}
		$GridSessionProp["data"] = $lectureRelatedProgress;
		$GridSessionProp["AllRecs"] = count($GridSessionProp["data"]);
		WebApp::addVar("UserLectureProgressExtToGrid", $GridSessionProp);	
		
		$dataToRtrUserRelatedProgress["UserLectureProgressExtToGrid"] = $GridSessionProp;
		return $dataToRtrUserRelatedProgress;
	}		
	function getListOfSlides ($presentation_id, $sc_coord=array()) { //UserProgres
		global $session;
	
		if (count($sc_coord)>0)
				$coord = $sc_coord;
		else	$coord = $this->appRelSt["CiInf"][$presentation_id]["coord"];		
		
		$slides_cis = array();
		$getCollectorProp = "SELECT coalesce(collect_virtual_slide,'no') as collect_virtual_slide
							   FROM sc_data 
							  WHERE content_id = '".$presentation_id."'";
        $joinTb = " AND sp_data.has_virtual_slide != 'yes'";
        $rsprop = WebApp::execQuery($getCollectorProp);    
		IF (!$rsprop->EOF()) {
			$collect_virtual_slide = $rsprop->Field("collect_virtual_slide");
			if ($collect_virtual_slide == "yes") 
            	 $joinTb = " AND sp_data.has_virtual_slide = 'yes'";
		}  

		$BoCondition = "";	
		if (isset($this->dataMode) && $this->dataMode=="FE") {
			$thisModeCode = 1;
				$BoCondition = "
								AND n.active".$session->Vars["lang"]." != 1
								AND n.state".$session->Vars["lang"]." != 7
								AND content.state".$session->Vars["lang"]." not in (0,5,7)
								AND content.published".$session->Vars["lang"]." = 'Y'		
								AND n.description".$session->Vars["lang"].$md." is not null";			
		} else {

			$md = $session->Vars["thisMode"];
			if ($this->thisModeCode=="0") { //a paaprovuar
				$BoCondition = "AND n.state".$session->Vars["lang"]." != 7
								AND content.state".$session->Vars["lang"]." not in (0,5,7)";
				//$BoCondition = "";
				$thisModeCode = 0;
			} else {
				$thisModeCode = 1;
				$BoCondition = "
								AND n.active".$session->Vars["lang"]." != 1
								AND n.state".$session->Vars["lang"]." != 7
								AND content.state".$session->Vars["lang"]." not in (0,5,7)
								AND content.published".$session->Vars["lang"]." = 'Y'		
								AND n.description".$session->Vars["lang"].$md." is not null";
			}
		}			
        $sql_con = "SELECT content.content_id
						  FROM content
						 JOIN nivel_4			AS n	ON (    content.id_zeroNivel   = n.id_zeroNivel
																AND content.id_firstNivel  = n.id_firstNivel
																AND content.id_secondNivel = n.id_secondNivel
																AND content.id_thirdNivel  = n.id_thirdNivel
																AND content.id_fourthNivel = n.id_fourthNivel
																)
						  JOIN profil_rights ON (       content.id_zeroNivel   = profil_rights.id_zeroNivel
													AND content.id_firstNivel  = profil_rights.id_firstNivel
													AND content.id_secondNivel = profil_rights.id_secondNivel
													AND content.id_thirdNivel  = profil_rights.id_thirdNivel
													AND content.id_fourthNivel = profil_rights.id_fourthNivel
													AND profil_rights.profil_id in (" . $session->Vars["tip"] . ")
												)
						LEFT JOIN sp_data on content.content_id = sp_data.content_id
							 AND sp_data.presentation_id = '" . $presentation_id . "' 
							 AND sp_data.lng_id = '" . $this->lngId . "'
							 AND sp_data.statusInfo = '".$thisModeCode."'
						WHERE content.id_zeroNivel 		= '" . $coord[0] . "'
						  AND content.id_firstNivel 	= '" . $coord[1] . "'
						  AND content.id_secondNivel	= '" . $coord[2] . "'
						  AND content.id_thirdNivel 	= '" . $coord[3] . "'
						  AND content.id_fourthNivel 	= '" . $coord[4] . "'
						  AND orderContent!=0
						  AND ci_type = 'SP'
						  ".$joinTb.$BoCondition."";

        $rs_con = WebApp::execQuery($sql_con);    
		while (!$rs_con->EOF()) {
			$content_id 			= $rs_con->Field("content_id");
			$slides_cis[$content_id] = $content_id;
			$rs_con->MoveNext();
		}
		$this->slides_cis = $slides_cis;
		return $slides_cis;
	}		
	function getRAcategory ($raid, $raid_coord=array()) {//UserProgres
		global $session;
	
		if (count($raid_coord)>0)
				$coord = $raid_coord;
		else	$coord = $this->appRelSt["CiInf"][$raid]["coord"];
		$raid_cis = array();

		$BoCondition = "";
		if (isset($this->tipp) && $this->useridp>0) $tipp=$this->tipp;
		else										$tipp=$session->Vars["tip"];
		
		if (isset($this->dataMode) && $this->dataMode=="FE") {
			$thisModeCode = 1;
				$BoCondition = "
								AND n.active".$session->Vars["lang"]." != 1
								AND n.state".$session->Vars["lang"]." != 7
								AND content.state".$session->Vars["lang"]." not in (0,5,7)
								AND content.published".$session->Vars["lang"]." = 'Y'		
								AND n.description".$session->Vars["lang"].$md." is not null";			
		} else {

			$md = $session->Vars["thisMode"];
			if ($this->thisModeCode=="0") { //a paaprovuar
				$BoCondition = "AND n.state".$session->Vars["lang"]." != 7
								AND content.state".$session->Vars["lang"]." not in (0,5,7)";
				//$BoCondition = "";
				$thisModeCode = 0;
			} else {
				$thisModeCode = 1;
				$BoCondition = "AND n.active".$session->Vars["lang"]." != 1
								AND n.state".$session->Vars["lang"]." != 7
								AND content.state".$session->Vars["lang"]." not in (0,5,7)
								AND content.published".$session->Vars["lang"]." = 'Y'		
								AND n.description".$session->Vars["lang"].$md." is not null";
			}
		}		
		
        $sql_con = "SELECT content.content_id, coalesce(category_kw_id_extra,'') as category_kw_id_extra,
        				coalesce(category_kw_id_extra,'') as category_kw_id_extra,
        				if (identifier_type='INTERNAL',identifier_key,'') as internal_cis
						  FROM content
						 JOIN nivel_4			AS n	ON (    content.id_zeroNivel   = n.id_zeroNivel
																AND content.id_firstNivel  = n.id_firstNivel
																AND content.id_secondNivel = n.id_secondNivel
																AND content.id_thirdNivel  = n.id_thirdNivel
																AND content.id_fourthNivel = n.id_fourthNivel)
						  JOIN profil_rights ON (       content.id_zeroNivel   = profil_rights.id_zeroNivel
													AND content.id_firstNivel  = profil_rights.id_firstNivel
													AND content.id_secondNivel = profil_rights.id_secondNivel
													AND content.id_thirdNivel  = profil_rights.id_thirdNivel
													AND content.id_fourthNivel = profil_rights.id_fourthNivel
													AND profil_rights.profil_id in (".$tipp."))
						LEFT JOIN ci_elearning_extended on content.content_id = ci_elearning_extended.content_id
							 AND ci_elearning_extended.lng_id = '" . $this->lngId . "' AND ci_elearning_extended.statusInfo = '".$thisModeCode."'
						WHERE content.id_zeroNivel 		= '" . $coord[0] . "'
						  AND content.id_firstNivel 	= '" . $coord[1] . "'
						  AND content.id_secondNivel	= '" . $coord[2] . "'
						  AND content.id_thirdNivel 	= '" . $coord[3] . "'
						  AND content.id_fourthNivel 	= '" . $coord[4] . "'
						  AND orderContent!=0
						  AND ci_type = 'RI'
						  ".$BoCondition."";

        $rs_con = WebApp::execQuery($sql_con);
		while (!$rs_con->EOF()) {
			$internal_cis 			= $rs_con->Field("internal_cis");
			$content_id 			= $rs_con->Field("content_id");
			$category_kw_id_extra 	= $rs_con->Field("category_kw_id_extra");
			
			if ($internal_cis!="" && $internal_cis>0) {
			} else $internal_cis = $content_id;
			$raid_cis["all"][$content_id] = $internal_cis;
			
			if ($category_kw_id_extra=="11,3")
					$raid_cis["Supporting"][$content_id] = $internal_cis;
			elseif ($category_kw_id_extra=="11,2")
					$raid_cis["Compulsory"][$content_id] = $internal_cis;
			elseif ($category_kw_id_extra=="11,1")
					$raid_cis["Recommended"][$content_id] = $internal_cis;
			else	$raid_cis["other"][$content_id] = $internal_cis;

			$rs_con->MoveNext();
		}
		$this->raid_cis = $raid_cis;
		return $this->raid_cis;
	}	
	function getCiRelatedWithSM ($ciflow, $raid_cis=array()) {//UserProgres
		global $session;
		$this->nrItems[$ciflow]["total"] = 0;//1
		$this->nrItems[$ciflow]["viewd"] = 0;
		$raid_cis_in = "";
		if (count( $raid_cis)>0)
			 $raid_cis_in = implode(",",$raid_cis["all"]);
		else if (isset($this->raid_cis["all"]) && count($raid_cis["all"])>0) 
			 $raid_cis_in = implode(",",$this->raid_cis["all"]);

		if (isset($this->dataMode) && $this->dataMode=="FE") {
			$thisModeCode = 1;
		} else {
			$thisModeCode = $this->thisModeCode;
		}
		
		if ($raid_cis_in!="") {
			$tmp = explode(",",$raid_cis_in);
			$this->nrItems[$ciflow]["total"] = count($tmp);//1
			if (count($tmp)>0) {
				while (list($key,$nrRelToControlForView)=each($tmp)) {
					$viewd = $this->getRAAnalytics($ciflow,$nrRelToControlForView);
					$this->nrItems[$ciflow]["viewd"] += $viewd;
				}
			}
			}
	}
	function getCiRelatedWithRa ($ciflow, $raid_cis=array()) {//UserProgres
		global $session;
		$this->nrItems[$ciflow]["total"] = 0;//1
		$this->nrItems[$ciflow]["viewd"] = 0;
		$raid_cis_in = array();
		if (count( $raid_cis)>0)
			 $raid_cis_in = implode(",",$raid_cis["all"]);
		else if (isset($this->raid_cis["all"])) 
			 $raid_cis_in = implode(",",$this->raid_cis["all"]);

		if (isset($this->dataMode) && $this->dataMode=="FE") {
			$thisModeCode = 1;
		} else {
			$thisModeCode = $this->thisModeCode;
		}

		if (count($raid_cis_in)) {
			$getNrOfRelated = "SELECT count(content_id) as nrRel,  group_concat(content_id_rel) as relDoc
								 FROM ci_keyword_ci 
								WHERE content_id in (".$ciflow.") AND content_id_rel in (".$raid_cis_in.") 
								  AND lng_id = '".$this->lngId."'  
								  AND statusInfo='".$thisModeCode."'
							 GROUP BY content_id";
			$rs_el = WebApp::execQuery($getNrOfRelated);
			if (!$rs_el->EOF()) {	
				$this->nrItems[$ciflow]["total"] += $rs_el->Field("nrRel");
				$nrRelToControlForView = $rs_el->Field("relDoc");
				$viewd = $this->getRAAnalytics($ciflow,$nrRelToControlForView);
				$this->nrItems[$ciflow]["viewd"] += $viewd;
			}
		}
	}	
	function getRAAnalytics ($ciflow,$ids,$userId="") {//UserProgres
		global $session;
		if (isset($this->useridp) && $this->useridp>0) $userId=$this->useridp;
		elseif ($userId=="") $userId=$session->Vars["ses_userid"];
		
		$nrRelToControlForView = 0;	//
		$tmpAll = array(); // contentId = '".$ciflow."' AND
		$getNrOfRelated = "SELECT count(distinct(cirel)) as nrOpened, 1 as grp
					  		 FROM z_analytics_progress
					 		WHERE cirel in (".$ids.")
					 		  AND cirel!=contentId
					   		  AND ses_userid = '".$userId."'
					 	 GROUP BY grp";
		$rs_el = WebApp::execQuery($getNrOfRelated);
		if (!$rs_el->EOF()) {	
			$nrRelToControlForView	  = $rs_el->Field("nrOpened");
		}	
		return $nrRelToControlForView;
	}	
	function constructLectureNavigation () {
		global $session;
		$idRef = "";
		$typeNav = "";
		$mainGridData = array();
    	
    	$referenceType = "";
    	if (($this->appRelSt["hierarchy_level"][$this->cidFlow]>3 || $this->ci_type_configuration=="EL")
    		&& isset($this->appRelSt["LECTURE_RELATED"]["EL"])) {  
    		//konfigurojme leksionin
    		if (isset($this->appRelSt["LECTURE_RELATED"]["EL"])) {
    			$referenceId = $this->appRelSt["LECTURE_RELATED"]["EL"];
    			$referenceType = "EL";
    		}
      	} elseif (($this->appRelSt["hierarchy_level"][$this->cidFlow]>3 || $this->ci_type_configuration=="TE" || $this->ci_type_configuration=="TC") 
      		&& isset($this->appRelSt["LECTURE_RELATED"]["TC"])) {
  		
    		//konfigurojme tutorialin
    			$referenceId = $this->appRelSt["LECTURE_RELATED"]["TC"];
    			$referenceType = "TC";   		
   		
    	} elseif ($this->appRelSt["hierarchy_level"][$this->cidFlow]>2 || $this->ci_type_configuration=="EC") {  
    		//konfigurojme modulin
    		if (isset($this->appRelSt["LECTURE_RELATED"]["EC"])) {
    			$referenceId = $this->appRelSt["LECTURE_RELATED"]["EC"];
    			$referenceType = "EC";
    		}    	
    	} elseif ($this->appRelSt["hierarchy_level"][$this->cidFlow]>1 || $this->ci_type_configuration=="PR") { 
    		//konfigurojme programin
    		if (isset($this->appRelSt["LECTURE_RELATED"]["PR"])) {
    			$referenceId = $this->appRelSt["LECTURE_RELATED"]["PR"];
    			$referenceType = "PR";
    		} 
    	
    	
    	} elseif (isset($this->appRelSt["LECTURE_RELATED"]["EL"])) {
     			$referenceId = $this->appRelSt["LECTURE_RELATED"]["EL"];
    			$referenceType = "EL";   	
    	} elseif ($this->appRelSt["hierarchy_level"][$this->cidFlow]==0) { 
    		//konfigurojme homen
    	}
    	if ($referenceType=="") {
    		if (isset($this->appRelSt["LECTURE_RELATED"]["EL"])) {
     			$referenceId = $this->appRelSt["LECTURE_RELATED"]["EL"];
    			$referenceType = "EL";   	
    		} else {
    		}
    	}
		
		$isElearningItem = "SELECT nivel_1.node_family_id 
					  		  FROM content
					  		  JOIN nivel_1 ON content.id_zeroNivel= nivel_1.id_zeroNivel AND content.id_firstNivel  = nivel_1.id_firstNivel 
					 		 WHERE content.content_id='".$session->Vars["contentId"]."'";		
		$isElearningItem = "SELECT nivel_1.node_family_id 
					  		  FROM content
					  		  JOIN nivel_1 ON content.id_zeroNivel= nivel_1.id_zeroNivel AND content.id_firstNivel  = nivel_1.id_firstNivel 
					 		 WHERE content.id_zeroNivel='".$session->Vars["level_0"]."'
					 		   AND content.id_firstNivel='".$session->Vars["level_1"]."'";		
					 		   
        $rsFlag 		= WebApp::execQuery($isElearningItem);
		$node_family_id	= $rsFlag->Field("node_family_id");		

		//struktura e programit template
		if ($session->Vars["level_0"]=='-1') { 
		} elseif (($node_family_id==7) || ($session->Vars["level_1"]==0)) {		} else {
			return;
		}	

  	
		if (isset($referenceId) && isset($this->appRelSt["CiInf"][$referenceId]) && ($referenceType =="PR")) {
		
				$idRef = $referenceId;
				$lectureRelatedProgress = array();
				$index = 0;
				
				$lectureRelatedProgressTutorial = array();
				$indexT = 0;				
				
				if (isset($this->appRelSt["lecPrEcNav"][$session->Vars["contentId"]]))
					 $pathNAV 	= $this->appRelSt["lecPrEcNav"][$session->Vars["contentId"]];
				else $pathNAV 	= $this->appRelSt["lecPrEcNav"][$idRef];
				
				$appRelSt	= $this->appRelSt;
				
				$typeNav = "group_modules";
						
				$tmpBorrowedAndNative = array();
				$tmpNative = array();
				if (isset($this->POTS["PR_coord"][$referenceId])) {
					$programKey 	= $this->POTS["PR_coord"][$referenceId];
					if (isset($this->POTS["ECInPR"][$programKey]))
						$tmpBorrowedAndNative 	= $this->POTS["ECInPR"][$programKey];
				}
				
				$showECs = "yes";
				$ciType = "EC";
				if (in_array($ciType, $this->nodeTypesToShowConf["show"]))
					$showECs = "yes";
				elseif (in_array($ciType, $this->nodeTypesToShowConf["hide"]))
					$showECs = "no";
					$showECs = "yes";					
				
				
				$showTCs = "yes";
				$ciType = "TC";
				if (is_array($this->nodeTypesToShowConf["show"]) && in_array($ciType, $this->nodeTypesToShowConf["show"]))
					$showTCs = "yes";
				elseif (is_array($this->nodeTypesToShowConf["show"]) && in_array($ciType, $this->nodeTypesToShowConf["hide"]))
					$showTCs = "no";
			
				
				reset($pathNAV);
				$totalSteps = count($pathNAV);
				while (list($ord,$ciflow)=each($pathNAV)) {
					
					if ($this->appRelSt["CiInf"][$ciflow]["ci_type"]=="PR") {
						
						WebApp::addVar("CiLectureHomeDescription", $appRelSt["CiInf"][$ciflow]["nodeDescription"]);			
						WebApp::addVar("CiLectureHome", $ciflow);			
						WebApp::addVar("CiLectureHomeType", $this->appRelSt["CiInf"][$ciflow]["ci_type"]);
						
						$mainGridData[0]["ci_type"]					= $this->appRelSt["CiInf"][$ciflow]["ci_type"];
						
						$mainGridData[0]["boostrap_ico"]			= $this->appRelSt["CiInf"][$ciflow]["boostrap_ico"];
						$mainGridData[0]["ciflow"]					= $ciflow;
						$mainGridData[0]["title"] 					= $appRelSt["CiInf"][$ciflow]["title"];
						$mainGridData[0]["nodeDescription"] 		= $appRelSt["CiInf"][$ciflow]["nodeDescription"];						
					
					} elseif ($this->appRelSt["CiInf"][$ciflow]["ci_type"]=="EC" && $showECs == "yes") {
					
						$lectureRelatedProgress[$index]["boostrap_ico"]				= $this->appRelSt["CiInf"][$ciflow]["boostrap_ico"];
						$lectureRelatedProgress[$index]["ci_type"]				= $this->appRelSt["CiInf"][$ciflow]["ci_type"];
						$lectureRelatedProgress[$index]["ciflow"] 				= $ciflow;
						$lectureRelatedProgress[$index]["title"] 				= $appRelSt["CiInf"][$ciflow]["title"];
						$lectureRelatedProgress[$index]["nodeDescription"] 		= $appRelSt["CiInf"][$ciflow]["nodeDescription"];
						$lectureRelatedProgress[$index]["relation"] 			= "native";
						
						
						$this->navigationSubLevels($ciflow, $appRelSt["CiInf"][$ciflow]);
						
						
						$index++;		
						
						if ($session->Vars["contentId"]==$ciflow) {
							WebApp::addVar("actualStepLecture", "insideTheLecture");
						}	
						$tmpNative[$ciflow] = $ciflow;
						
					} elseif ($this->appRelSt["CiInf"][$ciflow]["ci_type"]=="TC" && $showTCs == "yes") {
					
						$lectureRelatedProgressTutorial[$indexT]["boostrap_ico"]				= $this->appRelSt["CiInf"][$ciflow]["boostrap_ico"];
						$lectureRelatedProgressTutorial[$indexT]["ci_type"]				= $this->appRelSt["CiInf"][$ciflow]["ci_type"];
						$lectureRelatedProgressTutorial[$indexT]["ciflow"] 				= $ciflow;
						$lectureRelatedProgressTutorial[$indexT]["title"] 				= $appRelSt["CiInf"][$ciflow]["title"];
						$lectureRelatedProgressTutorial[$indexT]["nodeDescription"] 	= $appRelSt["CiInf"][$ciflow]["nodeDescription"];
						
						if ($session->Vars["contentId"]==$ciflow) {
							WebApp::addVar("actualStepLecture", "tutorial");
						}
						
						$indexT++;
					}
				}
			
			
				$showECBs = "no";
				$ciType = "ECB";
				if (in_array($ciType, $this->nodeTypesToShowConf["show"]))
					$showECBs = "yes";
				elseif (in_array($ciType, $this->nodeTypesToShowConf["hide"]))
					$showECBs = "no";
				if ($showECBs == "yes") {
					$findBorrowed = array_diff($tmpBorrowedAndNative,$tmpNative);
					if (count($findBorrowed)>0) {
						while (list($ord,$ciflow)=each($findBorrowed)) {
							
							$lectureRelatedProgress[$index]["ci_type"]				= $this->POTS["CI_TYPE"][$ciflow];
							$lectureRelatedProgress[$index]["ciflow"] 				= $ciflow;
							$lectureRelatedProgress[$index]["boostrap_ico"] 				= "";
							$lectureRelatedProgress[$index]["title"] 				= $this->POTS["ci"][$ciflow]["TT"];
							$lectureRelatedProgress[$index]["nodeDescription"] 		= $this->POTS["ci"][$ciflow]["ND"];
							$lectureRelatedProgress[$index]["relation"] 			= "borrowed";
							$index++;				
						}
					}
				}
				
			$GridSessionProp["data"] = $lectureRelatedProgress;
			$GridSessionProp["AllRecs"] = count($GridSessionProp["data"]);
			WebApp::addVar("UserLectureNavToGrid", $GridSessionProp);
			$UserLectureNavToGrid = $GridSessionProp;
			

			$GridSessionPropT["data"] = $lectureRelatedProgressTutorial;
			$GridSessionPropT["AllRecs"] = count($GridSessionPropT["data"]);
			WebApp::addVar("UserTutorialsNavToGrid", $GridSessionPropT);

		} elseif (isset($referenceId) && isset($this->appRelSt["CiInf"][$referenceId]) && ($referenceType =="EC")) {
	
				$nrPathNav 	= 0;
				$navInfo 	= array();
				$idRef 		= $referenceId;

				$typeNav = "group_lectures";

				/*$ids		= implode(",",$this->appRelSt["lecEcNav"][$idRef]);
				//$this->getCiAnalytics($ids);
				reset($this->appRelSt["lecNavRel"][$idRef]);
				while (list($ord,$ciflow)=each($this->appRelSt["lecNavRel"][$idRef])) {
					$navInfo[$ord]["ciType"] = $this->appRelSt["CiInf"][$ciflow]["ci_type"];
				}*/
				
				$tmpBorrowedAndNative = array();
				$tmpNative = array();
				
				if (isset($this->POTS["EC_coord"][$session->Vars["idRef"]])) {
					$programKey 	= $this->POTS["EC_coord"][$session->Vars["idRef"]];
					if (isset($this->POTS["ELInEC"][$programKey]))
						$tmpBorrowedAndNative 	= $this->POTS["ELInEC"][$programKey];
				}		
				
				$lectureRelatedProgress = array();
				$index = 0;
				
				if (isset($this->appRelSt["lecPrEcNav"][$session->Vars["contentId"]]))
					 $pathNAV 	= $this->appRelSt["lecPrEcNav"][$session->Vars["contentId"]];
				else $pathNAV 	= $this->appRelSt["lecPrEcNav"][$idRef];			
				
				$showELs = "yes";
				$ciType = "EL";
				if (in_array($ciType, $this->nodeTypesToShowConf["show"]))
					$showELs = "yes";
				elseif (in_array($ciType, $this->nodeTypesToShowConf["hide"]))
					$showELs = "no";
				
				$appRelSt	= $this->appRelSt;
				reset($pathNAV);
				$totalSteps = count($pathNAV);

				while (list($ord,$ciflow)=each($pathNAV)) {
					
					if ($this->appRelSt["CiInf"][$ciflow]["ci_type"]=="EC") {
						
						WebApp::addVar("CiLectureHomeDescription", $appRelSt["CiInf"][$ciflow]["nodeDescription"]);			
						WebApp::addVar("CiLectureHome", $ciflow);			
						WebApp::addVar("CiLectureHomeType", $this->appRelSt["CiInf"][$ciflow]["ci_type"]);
						
						$mainGridData[0]["boostrap_ico"]					= $this->appRelSt["CiInf"][$ciflow]["boostrap_ico"];
						$mainGridData[0]["ci_type"]					= $this->appRelSt["CiInf"][$ciflow]["ci_type"];
						$mainGridData[0]["ciflow"]					= $ciflow;
						$mainGridData[0]["title"] 					= $appRelSt["CiInf"][$ciflow]["title"];
						$mainGridData[0]["nodeDescription"] 		= $appRelSt["CiInf"][$ciflow]["nodeDescription"];						
					
					} elseif ($this->appRelSt["CiInf"][$ciflow]["ci_type"]=="TC") {
					
					} else {
					
						if ($showELs == "yes") {	
							
							$lectureRelatedProgress[$index]["enableLinks"] 			= "no";
							if (isset($this->POTS["EL_coord"][$ciflow])) {
								$LectureKey 	= $this->POTS["EL_coord"][$ciflow];
								if (isset($this->partecipationInfo[$LectureKey])) {
									$lectureRelatedProgress[$index]["enableLinks"] = "yes";
								}
							}
							$lectureRelatedProgress[$index]["enableLinks"] = "yes"; //kjo ben enable link te leksionit on or off te kontrollohet llogjika
							$lectureRelatedProgress[$index]["boostrap_ico"]				= $this->appRelSt["CiInf"][$ciflow]["boostrap_ico"];
							$lectureRelatedProgress[$index]["ci_type"]				= $this->appRelSt["CiInf"][$ciflow]["ci_type"];
							$lectureRelatedProgress[$index]["ciflow"] 				= $ciflow;
							$lectureRelatedProgress[$index]["title"] 				= $appRelSt["CiInf"][$ciflow]["title"];
							$lectureRelatedProgress[$index]["nodeDescription"] 		= $appRelSt["CiInf"][$ciflow]["nodeDescription"];
							$lectureRelatedProgress[$index]["relation"] 			= "native";
							$index++;
							
							//kontrollo te drejtat brenda
							$tmpNative[$ciflow] = $ciflow;
							if ($session->Vars["contentId"]==$ciflow) {
								WebApp::addVar("actualStepLecture", "insideTheLecture");
							}
						}						
					}
				}
				
				$showELBs = $showELs;
				$showELBs = "no";
				$ciType = "ELB";
				if (in_array($ciType, $this->nodeTypesToShowConf["show"]))
					$showELBs = "yes";
				elseif (in_array($ciType, $this->nodeTypesToShowConf["hide"]))
					$showELBs = "no";

					if ($showELBs == "yes") {
						$findBorrowed = array_diff($tmpBorrowedAndNative,$tmpNative);
						if (count($findBorrowed)>0) {
							while (list($ord,$ciflow)=each($findBorrowed)) {
								
								
								$lectureRelatedProgress[$index]["enableLinks"] 			= "no";
								if (isset($this->POTS["EL_coord"][$ciflow])) {
									$LectureKey 	= $this->POTS["EL_coord"][$ciflow];
									if (isset($this->partecipationInfo[$LectureKey])) {
										$lectureRelatedProgress[$index]["enableLinks"] = "yes";
									}
								}								
								
								$lectureRelatedProgress[$index]["ci_type"]				= $this->POTS["CI_TYPE"][$ciflow];
								$lectureRelatedProgress[$index]["ciflow"] 				= $ciflow;
								$lectureRelatedProgress[$index]["boostrap_ico"] 		= "";
								$lectureRelatedProgress[$index]["title"] 				= $this->POTS["ci"][$ciflow]["TT"];
								$lectureRelatedProgress[$index]["nodeDescription"] 		= $this->POTS["ci"][$ciflow]["ND"];
								$lectureRelatedProgress[$index]["relation"] 			= "borrowed";
								
								$index++;				
							}
						}
					}

			$GridSessionProp["data"] = $lectureRelatedProgress;
			$GridSessionProp["AllRecs"] = count($GridSessionProp["data"]);
			WebApp::addVar("UserLectureNavToGrid", $GridSessionProp);	
			$UserLectureNavToGrid = $GridSessionProp;
			
		} elseif (isset($referenceId) && isset($this->appRelSt["CiInf"][$referenceId]) && ($referenceType =="EL")) {

				
				$nrPathNav = 0;
				if (isset($this->appRelSt["LECTURE_RELATED"]["RA"])) {
					$RAID = $this->appRelSt["LECTURE_RELATED"]["RA"];
					$this->getRAcategory ($RAID);
				}

				WebApp::addVar("actualStepLecture", "relatedToLecture");			
				$navInfo = array();
				
				$typeNav = "group_lecture_inside";
					
				if (isset($referenceId) && isset($this->appRelSt["CiInf"][$referenceId]) && ($referenceType =="EL")) {

					$idRef = $referenceId;
					$lectureId = $referenceId;
					
					$pathNAV 	= array();
					if (isset($this->appRelSt["lecNav"][$session->Vars["contentId"]]))
						 $pathNAV 	= $this->appRelSt["lecNav"][$session->Vars["contentId"]];
					elseif (isset($this->appRelSt["lecNav"][$idRef]))
						$pathNAV 	= $this->appRelSt["lecNav"][$idRef];					
					
					if (isset($pathNAV) && count($pathNAV)>0 ) {
						//$idAssessment = "";
						//$issetAssessment = "no";
						//$confSurvey = array();
						$ids		= implode(",",$pathNAV);
						$this->getCiAnalytics($ids);
						reset($pathNAV);
						while (list($ord,$ciflow)=each($pathNAV)) {

									$navInfo[$ord]["ciflow"] 	= $ciflow;
									
									$ciType = $this->appRelSt["CiInf"][$ciflow]["ci_type"];
									$navInfo[$ord]["ciType"] 	= $ciType;	//this->appRelSt["CiInf"][$ciflow]
									
									
									$navInfo[$ord]["actualTestStatus"] = "not_done";
									$lectureRelatedProgress[$index]["nrItemsTotal"] 		= "".$nrItemsTotal;

								//	if ($navInfo[$ord]["ci_duration"]>0) {
										if ($ciType=="CQ" || $ciType=="ES") {
											$progresInfo[$ciflow]["testRelatedExist"] = "no";
											$tmp = $this->userTestResult($ciflow);
											if (isset($tmp["Info"]) && Count($tmp["Info"])>0) {
												$navInfo[$ord]["actualTestStatus"] = "done";
												$navInfo[$ord]["actualTestStatus"] = $tmp;
											} 
											if ($ciType=="ES") {
												$idAssessment = $ciflow;
												$issetAssessment = "yes";
												if ($tmp["totals"]["nr_certificate"]>0) {
													$confSurvey = $this->userRelatedSurvey($tmp["totals"]["nr_certificate"]);
												}									
											}
										} elseif ($ciType=="SC" && $navInfo[$ord]["ci_duration"]>0) {
												$this->getListOfSlides ($ciflow);
												$ids1 = implode(",",$this->slides_cis);
												$nrItemsTotal= count($this->slides_cis);
												$lectureRelatedProgress[$index]["nrItemsTotal"] 		= "".$nrItemsTotal;
												$dataToReturn 	= $this->getRAAnalyticsCat ($ids1);
												$nrItemsOpened	= $dataToReturn["nrOpened"];										
												if ($nrItemsTotal==$nrItemsOpened) {
													$navInfo[$ord]["actualTestStatus"] = "done";
												}
										} else {
											$navInfo[$ord]["actualTestStatus"] = "done";
										}
								//	}
						} //while (list($ord,$ciflow)=each($pathNAV)) {
						
						
						$lectureRelatedProgress = array();
						$index = 0;

						//$pathNAV 	= $this->appRelSt["lecNav"][$lectureId];
						$appRelSt	= $this->appRelSt;

						reset($pathNAV);
						$totalSteps = count($pathNAV);

						$actualCi = "no";
						while (list($ord,$ciflow)=each($pathNAV)) {
							
							$show = "yes";
							$ciType = $this->appRelSt["CiInf"][$ciflow]["ci_type"];
							if (in_array($ciType, $this->nodeTypesToShowConf["show"]))
								$show = "yes";
							elseif (in_array($ciType, $this->nodeTypesToShowConf["hide"]))
								$show = "no";
						
							if ($show == "yes") {							
									$previewsStep 	= $ord-1;
									$nextStep 		= $ord+1;
									$lectureRelatedProgress[$index]["enabledFuncionality"] 				= "no";
									if ($previewsStep>0) {
										//kontroll nese actual step eshte enabled apo jo
										if (isset($navInfo[$previewsStep])) {
											$previewsStatus = $navInfo[$previewsStep]["actualStatus"];
											if ($previewsStatus!="new") {
												$lectureRelatedProgress[$index]["actualTestStatusPreviews"] = $navInfo[$previewsStep]["actualTestStatus"];
												if (isset($navInfo[$previewsStep]["actualTestStatus"]) && $navInfo[$previewsStep]["actualTestStatus"]=="not_done") { 
												} else $lectureRelatedProgress[$index]["enabledFuncionality"] = "yes";
											}
										}
									} else {
										$previewsStatus = "first";
										$lectureRelatedProgress[$index]["enabledFuncionality"] 				= "yes";
									}
									if ($nextStep<$totalSteps) {
										//kontroll nese actual step eshte enabled apo jo
									} else {

									}
									if ($session->Vars["contentId"]==$ciflow) {
										WebApp::addVar("actualStepLecture", "insideTheLecture");
										$actualCi = "yes";
										if ($previewsStatus == "first") {
										} else {
											WebApp::addGlobalVar("previews_exist_nav", "yes");
											WebApp::addGlobalVar("previews_link", $navInfo[$previewsStep]["ciflow"]);
										}
										if ($nextStep<=$totalSteps) {
											//kontroll nese actual step eshte enabled apo jo
											WebApp::addGlobalVar("next_exist_nav", "yes");
											WebApp::addGlobalVar("next_link", $navInfo[$nextStep]["ciflow"]);
										} else {
										}						
									}
									
									$lectureRelatedProgress[$index]["boostrap_ico"]				= $this->appRelSt["CiInf"][$ciflow]["boostrap_ico"];
									$lectureRelatedProgress[$index]["ci_type"]				= $this->appRelSt["CiInf"][$ciflow]["ci_type"];
									$lectureRelatedProgress[$index]["actualTestStatus"]		= $navInfo[$ord]["actualTestStatus"];
									$lectureRelatedProgress[$index]["actualStatus"]			= $navInfo[$ord]["actualStatus"];
									$lectureRelatedProgress[$index]["previewsStatus"]		= $previewsStatus;
									$lectureRelatedProgress[$index]["ciflow"] 				= $ciflow;
									$lectureRelatedProgress[$index]["title"] 				= $appRelSt["CiInf"][$ciflow]["title"];
									$lectureRelatedProgress[$index]["nodeDescription"] 		= $appRelSt["CiInf"][$ciflow]["nodeDescription"];
									$ciType 												= $appRelSt["CiInf"][$ciflow]["ci_type"];
									
									$lectureRelatedProgress[$index]["actualNode"] 			= "no";
									if ($session->Vars["level_0"]==$appRelSt["CiInf"][$ciflow]["coord"][0]
									 && $session->Vars["level_1"]==$appRelSt["CiInf"][$ciflow]["coord"][1]
									 && $session->Vars["level_2"]==$appRelSt["CiInf"][$ciflow]["coord"][2]
									 && $session->Vars["level_3"]==$appRelSt["CiInf"][$ciflow]["coord"][3]
									 && $session->Vars["level_4"]==$appRelSt["CiInf"][$ciflow]["coord"][4]
									 )
										$lectureRelatedProgress[$index]["actualNode"] 			= "yes";							
									
									$index++;
							}
						} //while (list($ord,$ciflow)=each($pathNAV)) {

						//includimi i external survey ne menu
						$showSurvey = "yes";
						if ($showSurvey == "yes") {
							//$confSurveyWithConditions = $confSurvey;
							$workingCiSurveyC 	= new CiManagerFe($referenceId, $session->Vars["lang"]);
							$confSurvey = $workingCiSurveyC->getSurveyConfigurationInParentNodes($this->thisModeCode,"EL",$referenceId);
							
							if ($confSurvey["type_of_survey"]=="repository" && $confSurvey["target_survey_ci"]>0) {
								//$existCiForCurrentState = CiManagerFe::controlCiStateForPublishMode($confSurvey["target_survey_ci"]);
								$confSurvey = array_merge($confSurvey, CiManagerFe::getUSInfo($confSurvey["target_survey_ci"]));

								if ($confSurvey["us_exist"] == "yes" ) { //&& $confSurvey["survey_test_state"] !="completed"
										
									$lectureRelatedProgress[$index]["ci_type"]				= "US";
									$lectureRelatedProgress[$index]["ciflow"]				= $confSurvey["target_survey_ci"];
									
									$lectureRelatedProgress[$index]["id_reference"]		= $referenceId;
									
									$lectureRelatedProgress[$index]["title"] 				= $confSurvey["us_title"];
									$lectureRelatedProgress[$index]["nodeDescription"]		= $confSurvey["us_nodeName"];
									$lectureRelatedProgress[$index]["actualNode"]			= "no";
									$lectureRelatedProgress[$index]["type_of_survey"]		= "repository";

									if ($session->Vars["level_0"]==$appRelSt["CiInf"][$referenceId]["coord"][0]
										 && $session->Vars["level_1"]==$appRelSt["CiInf"][$referenceId]["coord"][1]
										 && $session->Vars["level_2"]==$appRelSt["CiInf"][$referenceId]["coord"][2]
										 && $session->Vars["level_3"]==$appRelSt["CiInf"][$referenceId]["coord"][3]
										 && $session->Vars["level_4"]==$appRelSt["CiInf"][$referenceId]["coord"][4]
										 && $session->Vars["contentId"]==$confSurvey["target_survey_ci"]
									 )
										$lectureRelatedProgress[$index]["actualNode"] 			= "yes";	
									$index++;
								}								
							}
						}						
					}
				}

				WebApp::addVar("CiLectureHomeDescription", $appRelSt["CiInf"][$this->appRelSt["LECTURE_RELATED"]["EL"]]["nodeDescription"]);			
				WebApp::addVar("CiLectureHome", $this->appRelSt["LECTURE_RELATED"]["EL"]);	
				WebApp::addVar("CiLectureHomeType", "EL");
				WebApp::addVar("CiLectureHomeReference", $referenceId);
				
				$mainGridData[0]["boostrap_ico"]					= $this->appRelSt["CiInf"][$referenceId]["boostrap_ico"];
				$mainGridData[0]["ci_type"]					= $this->appRelSt["CiInf"][$referenceId]["ci_type"];
				$mainGridData[0]["ciflow"]					= $referenceId;
				$mainGridData[0]["title"] 					= $this->appRelSt["CiInf"][$referenceId]["title"];
				$mainGridData[0]["nodeDescription"] 		= $this->appRelSt["CiInf"][$referenceId]["nodeDescription"];				

				$GridSessionProp["data"] = $lectureRelatedProgress;
				$GridSessionProp["AllRecs"] = count($GridSessionProp["data"]);
				WebApp::addVar("UserLectureNavToGrid", $GridSessionProp);
				$UserLectureNavToGrid = $GridSessionProp;
			
		} elseif (isset($referenceId) && isset($this->appRelSt["CiInf"][$referenceId]) && ($referenceType =="TC" || $referenceType =="TE")) {
		
				$index = 0;
				$lectureRelatedProgress =array();
				if (isset($this->appRelSt["lecPrEcNav"][$session->Vars["contentId"]]))
					 $pathNAV 	= $this->appRelSt["lecPrEcNav"][$session->Vars["contentId"]];
				else $pathNAV 	= $this->appRelSt["lecPrEcNav"][$idRef];
				
				$appRelSt	= $this->appRelSt;
				$totalSteps = count($pathNAV);
				if (isset($pathNAV) && count($pathNAV)>0) {
				reset($pathNAV);
				while (list($ord,$ciflow)=each($pathNAV)) {
						
						$showPrType = "";
						$ciType = $this->appRelSt["CiInf"][$ciflow]["ci_type"];
						if ($ciType=="PR") {
							
							if ($session->Vars["level_1"]=='-1') { //struktura e programit template
								$showPrType = "yes";
							} else {							
							
								if (isset($this->POTS["ci"][$ciflow]["type_of_programe"])) {
									$tpProg = $this->POTS["ci"][$ciflow]["type_of_programe"];
									if (array_key_exists ($tpProg, $this->nodeTypesToShowConf["pr"]) && $this->nodeTypesToShowConf["pr"][$tpProg]=="yes")
										$showPrType = "yes";
								} 
							}
							
						} else {
							//$showPrType = "yes";
						}
						
						if ($referenceId==$ciflow) {
						} else {
								//$showPrType = "yes";
								//echo "-$ciType-$ciflow-$showPrType-$tpProg<br>";
								if ($showPrType == "yes") {	
									$lectureRelatedProgress[$index]["boostrap_ico"]				= $this->appRelSt["CiInf"][$ciflow]["boostrap_ico"];
									$lectureRelatedProgress[$index]["ci_type"]				= $this->appRelSt["CiInf"][$ciflow]["ci_type"];
									$lectureRelatedProgress[$index]["ciflow"] 				= $ciflow;
									$lectureRelatedProgress[$index]["title"] 				= $appRelSt["CiInf"][$ciflow]["title"];
									$lectureRelatedProgress[$index]["nodeDescription"] 		= $appRelSt["CiInf"][$ciflow]["nodeDescription"];
									$lectureRelatedProgress[$index]["relation"] 			= "native";

										$lectureRelatedProgress[$index]["actualNode"] 			= "no";

										if ($session->Vars["level_0"]==$appRelSt["CiInf"][$ciflow]["coord"][0]
										 && $session->Vars["level_1"]==$appRelSt["CiInf"][$ciflow]["coord"][1]
										 && $session->Vars["level_2"]==$appRelSt["CiInf"][$ciflow]["coord"][2]
										 && $session->Vars["level_3"]==$appRelSt["CiInf"][$ciflow]["coord"][3]
										 && $session->Vars["level_4"]==$appRelSt["CiInf"][$ciflow]["coord"][4]
										 )
											$lectureRelatedProgress[$index]["actualNode"] 			= "yes";							

									$index++;
								}	
						}
				}}	
				
				$mainGridData[0]["boostrap_ico"]					= $this->appRelSt["CiInf"][$session->Vars["contentId"]]["boostrap_ico"];
				$mainGridData[0]["ci_type"]					= $this->appRelSt["CiInf"][$session->Vars["contentId"]]["ci_type"];
				$mainGridData[0]["ciflow"]					= $session->Vars["contentId"];
				$mainGridData[0]["title"] 					= $appRelSt["CiInf"][$session->Vars["contentId"]]["title"];
				
				if (isset($this->nodeTypesToShowConf["label"][$session->Vars["contentId"]])) 
					$mainGridData[0]["nodeDescription"] 		= $this->nodeTypesToShowConf["label"][$session->Vars["contentId"]];								
				else				
					$mainGridData[0]["nodeDescription"] 		= $appRelSt["CiInf"][$session->Vars["contentId"]]["nodeDescription"];								

				$GridSessionProp["data"] = $lectureRelatedProgress;
				$GridSessionProp["AllRecs"] = count($GridSessionProp["data"]);
				WebApp::addVar("HomeRelatedNavToGrid", $GridSessionProp);	
				$HomeRelatedNavToGrid = $GridSessionProp;

				WebApp::addVar("CiLectureHomeDescription", $appRelSt["CiInf"][$this->appRelSt["LECTURE_RELATED"]["TC"]]["nodeDescription"]);			
				WebApp::addVar("CiLectureHome", $this->appRelSt["LECTURE_RELATED"]["TC"]);	
				WebApp::addVar("CiLectureHomeType", "TC");
				
				$mainGridData[0]["boostrap_ico"]					= $this->appRelSt["CiInf"][$referenceId]["boostrap_ico"];
				$mainGridData[0]["ci_type"]					= $this->appRelSt["CiInf"][$referenceId]["ci_type"];
				$mainGridData[0]["ciflow"]					= $referenceId;
				$mainGridData[0]["title"] 					= $this->appRelSt["CiInf"][$referenceId]["title"];
				$mainGridData[0]["nodeDescription"] 		= $this->appRelSt["CiInf"][$referenceId]["nodeDescription"];	

				
		} else {
				
				$index = 0;
				$lectureRelatedProgress =array();
				if (isset($this->appRelSt["lecPrEcNav"][$session->Vars["contentId"]]))
					 $pathNAV 	= $this->appRelSt["lecPrEcNav"][$session->Vars["contentId"]];
				else $pathNAV 	= $this->appRelSt["lecPrEcNav"][$idRef];

				$appRelSt	= $this->appRelSt;
				$totalSteps = count($pathNAV);
				if (isset($pathNAV) && count($pathNAV)>0) {
				reset($pathNAV);
				while (list($ord,$ciflow)=each($pathNAV)) {
						
						$showPrType = "";
						$ciType = $this->appRelSt["CiInf"][$ciflow]["ci_type"];
						if ($ciType=="PR") {
							if ($session->Vars["level_1"]=='-1') { //struktura e programit template
								$showPrType = "yes";
							} else {
								if (isset($this->POTS["ci"][$ciflow]["type_of_programe"])) {
									$tpProg = $this->POTS["ci"][$ciflow]["type_of_programe"];
									if (array_key_exists ($tpProg, $this->nodeTypesToShowConf["pr"]) && $this->nodeTypesToShowConf["pr"][$tpProg]=="yes")
										$showPrType = "yes";
								}
							}
						} else {
							//$showPrType = "yes";
						}
						$showPrType = "yes";
						//echo "-$ciType-$ciflow-$showPrType-$tpProg<br>";
						if ($showPrType == "yes" && $appRelSt["CiInf"][$ciflow]["coord"][1]>0) {	
							
							$lectureRelatedProgress[$index]["boostrap_ico"]			= $this->appRelSt["CiInf"][$ciflow]["boostrap_ico"];
							$lectureRelatedProgress[$index]["ci_type"]				= $this->appRelSt["CiInf"][$ciflow]["ci_type"];
							$lectureRelatedProgress[$index]["ciflow"] 				= $ciflow;
							$lectureRelatedProgress[$index]["title"] 				= $appRelSt["CiInf"][$ciflow]["title"];
							$lectureRelatedProgress[$index]["nodeDescription"] 		= $appRelSt["CiInf"][$ciflow]["nodeDescription"];
							$lectureRelatedProgress[$index]["relation"] 			= "native";

							$lectureRelatedProgress[$index]["actualNode"] 			= "no";

							if ($session->Vars["level_0"]==$appRelSt["CiInf"][$ciflow]["coord"][0]
								 && $session->Vars["level_1"]==$appRelSt["CiInf"][$ciflow]["coord"][1]
								 && $session->Vars["level_2"]==$appRelSt["CiInf"][$ciflow]["coord"][2]
								 && $session->Vars["level_3"]==$appRelSt["CiInf"][$ciflow]["coord"][3]
								 && $session->Vars["level_4"]==$appRelSt["CiInf"][$ciflow]["coord"][4]
							)
								$lectureRelatedProgress[$index]["actualNode"] 			= "yes";							
							
							$this->navigationSubLevels($ciflow, $appRelSt["CiInf"][$ciflow]);
							$index++;
						}						
				}}	
				
				$mainGridData[0]["boostrap_ico"]					= $this->appRelSt["CiInf"][$session->Vars["contentId"]]["boostrap_ico"];
				$mainGridData[0]["ci_type"]					= $this->appRelSt["CiInf"][$session->Vars["contentId"]]["ci_type"];
				$mainGridData[0]["ciflow"]					= $session->Vars["contentId"];
				$mainGridData[0]["title"] 					= $appRelSt["CiInf"][$session->Vars["contentId"]]["title"];
				
				if (isset($this->nodeTypesToShowConf["label"][$session->Vars["contentId"]])) 
					$mainGridData[0]["nodeDescription"] 		= $this->nodeTypesToShowConf["label"][$session->Vars["contentId"]];								
				else				
					$mainGridData[0]["nodeDescription"] 		= $appRelSt["CiInf"][$session->Vars["contentId"]]["nodeDescription"];								

				$GridSessionProp["data"] = $lectureRelatedProgress;
				$GridSessionProp["AllRecs"] = count($GridSessionProp["data"]);
				WebApp::addVar("HomeRelatedNavToGrid", $GridSessionProp);	
				$HomeRelatedNavToGrid = $GridSessionProp;
		}

		$mainGridDataG["data"] = $mainGridData;
		$mainGridDataG["AllRecs"] = count($mainGridDataG["data"]);
		WebApp::addVar("UserParentNavToGrid", $mainGridDataG);
		$UserParentNavToGrid = $mainGridDataG;
		

		

		$tmMain = $mainGridData[0];
		
		$ecID = "";
		if ($tmMain["ci_type"]=="EC") {
			if (isset($this->POTS["EC_coord"][$tmMain["ciflow"]])) {
				$ecKey = $this->POTS["EC_coord"][$tmMain["ciflow"]];
			}
		}
	
		

		//echo $tmMain["ci_type"].":CI_TYPE\n\n\n";
		//echo $tmMain["ciflow"].":CIFLOW\n\n\n";

		if ($tmMain["ci_type"]=="EL") {
			
			if (isset($this->POTS["EL_coord"][$tmMain["ciflow"]])) {
				
				$keyCoord = $this->POTS["EL_coord"][$tmMain["ciflow"]];
				
				$progTemp 	= explode("_",$keyCoord);
				$programKey	= $progTemp[0]."_".$progTemp[1];					
				$modulKey 	= $progTemp[0]."_".$progTemp[1]."_".$progTemp[2];	
				
					$ecID = $this->POTS["EC"][$modulKey];
					$parentModule = array();
					$parentModule["ci_type"] 	= "EC";
					$parentModule["ciflow"] 	= $ecID;
					$parentModule["title"] 				= $this->POTS["ci"][$ecID]["TT"];
					$parentModule["nodeDescription"] 	= $this->POTS["ci"][$ecID]["ND"];
					
					$parentModuleG["data"][0] = $parentModule;
					$parentModuleG["AllRecs"] = count($parentModuleG["data"]);
					WebApp::addVar("UserParentNavEcElToGrid", $parentModuleG);					
				
			
					$prID = $this->POTS["PR_coord_r"][$programKey];
					$parentModule = array();
					$parentModule["ci_type"] 	= "PR";
					$parentModule["ciflow"] 	= $prID;
					$parentModule["title"] 				= $this->POTS["ci"][$prID]["TT"];
					$parentModule["nodeDescription"] 	= $this->POTS["ci"][$prID]["ND"];
					
					$parentModuleG["data"][0] = $parentModule;
					$parentModuleG["AllRecs"] = count($parentModuleG["data"]);
					WebApp::addVar("UserParentNavPrEcElToGrid", $parentModuleG);					

			}
		}
		
		
		if ($tmMain["ci_type"]=="EC") {
			if (isset($this->POTS["EC_coord"][$tmMain["ciflow"]])) {
				
					$keyCoord = $this->POTS["EC_coord"][$tmMain["ciflow"]];
					$progTemp 	= explode("_",$keyCoord);
					$programKey	= $progTemp[0]."_".$progTemp[1];					
					$prID = $this->POTS["PR_coord_r"][$programKey];
					$parentModule = array();
					$parentModule["ci_type"] 	= "PR";
					$parentModule["ciflow"] 	= $prID;
					$parentModule["title"] 				= $this->POTS["ci"][$prID]["TT"];
					$parentModule["nodeDescription"] 	= $this->POTS["ci"][$prID]["ND"];
					
					$parentModuleG["data"][0] = $parentModule;
					$parentModuleG["AllRecs"] = count($parentModuleG["data"]);
					WebApp::addVar("UserParentNavPrEcElToGrid", $parentModuleG);					
			}
		}		
		
		
		if ($tmMain["ci_type"]=="TC") {
			
			if (isset($this->POTS["TC_coord"][$tmMain["ciflow"]])) {
				
				$keyCoord = $this->POTS["TC_coord"][$tmMain["ciflow"]];
				
				$progTemp 	= explode("_",$keyCoord);
				$programKey	= $progTemp[0]."_".$progTemp[1];					
				$modulKey 	= $progTemp[0]."_".$progTemp[1]."_".$progTemp[2];	
				
					$ecID = $this->POTS["EC"][$modulKey];
					$parentModule = array();
					$parentModule["ci_type"] 	= "EC";
					$parentModule["ciflow"] 	= $ecID;
					$parentModule["title"] 				= $this->POTS["ci"][$ecID]["TT"];
					$parentModule["nodeDescription"] 	= $this->POTS["ci"][$ecID]["ND"];
					
					$parentModuleG["data"][0] = $parentModule;
					$parentModuleG["AllRecs"] = count($parentModuleG["data"]);
					WebApp::addVar("UserParentNavEcElToGrid", $parentModuleG);					
				
					$prID = $this->POTS["PR_coord_r"][$programKey];
					$parentModule = array();
					$parentModule["ci_type"] 	= "PR";
					$parentModule["ciflow"] 	= $prID;
					$parentModule["title"] 				= $this->POTS["ci"][$prID]["TT"];
					$parentModule["nodeDescription"] 	= $this->POTS["ci"][$prID]["ND"];
					$parentModuleG["data"][0] = $parentModule;
					$parentModuleG["AllRecs"] = count($parentModuleG["data"]);
					WebApp::addVar("UserParentNavPrEcElToGrid", $parentModuleG);	
					$UserParentNavPrEcElToGrid = $parentModuleG;

			}
		}
			
		if ($this->ci_type_configuration=="AC" || $this->appRelSt["MainNodeCiType"] =="AC") {
			
			//KONTROLL IF A SURVEY IS CONFIGURED
			
			
			if ($this->ci_type_configuration=="AC")
					$SurgicalVideoId = $this->cidFlow;
			else	$SurgicalVideoId = $this->appRelSt["MainNodeCiID"];
			$confSurvey = $this->userRelatedSurveyToSurgicalVideo($SurgicalVideoId);
			
								$indexSV = 0;
								$SVRelatedProgress = array();
								
								if ($confSurvey["us_exist"] == "yes" ) { //&& $confSurvey["survey_test_state"] !="completed"
										
									//$navInfo[$ord]["headline"] 				= $headline;
									
									
									$SVRelatedProgress[$indexSV]["ci_type"]				= "US";
									$SVRelatedProgress[$indexSV]["ciflow"]				= $confSurvey["target_survey_ci"];
									
									if (isset($referenceId)) 
											$SVRelatedProgress[$indexSV]["id_reference"]		= $referenceId;
									else	$SVRelatedProgress[$indexSV]["id_reference"]		= $SurgicalVideoId;
									
									$SVRelatedProgress[$indexSV]["title"] 				= $confSurvey["us_title"];
									$SVRelatedProgress[$indexSV]["nodeDescription"]		= $confSurvey["us_nodeName"];
									$SVRelatedProgress[$indexSV]["actualNode"]			= "no";
									$SVRelatedProgress[$indexSV]["type_of_survey"]		= "repository";

									if ($session->Vars["level_0"]==$appRelSt["CiInf"][$SurgicalVideoId]["coord"][0]
										 && $session->Vars["level_1"]==$appRelSt["CiInf"][$SurgicalVideoId]["coord"][1]
										 && $session->Vars["level_2"]==$appRelSt["CiInf"][$SurgicalVideoId]["coord"][2]
										 && $session->Vars["level_3"]==$appRelSt["CiInf"][$SurgicalVideoId]["coord"][3]
										 && $session->Vars["level_4"]==$appRelSt["CiInf"][$SurgicalVideoId]["coord"][4]
										 && $session->Vars["contentId"]==$confSurvey["target_survey_ci"]
									 )
										$SVRelatedProgress[$indexSV]["actualNode"] 			= "yes";											
								}		
			
			
					$tmpSV["data"] = $SVRelatedProgress;
					$tmpSV["AllRecs"] = count($tmpSV["data"]);
					WebApp::addVar("surveyToSurgicalVideo_".$SurgicalVideoId, $tmpSV);	
					
		}

		$lectureRelatedProgress 	= array();
		$pathNAV 	= array();
		if (isset($this->appRelSt["lecNavRel"][$session->Vars["contentId"]]))
			 $pathNAV 	= $this->appRelSt["lecNavRel"][$session->Vars["contentId"]];
		else $pathNAV 	= $this->appRelSt["lecNavRel"][$idRef];	
		
		$index = 0;		
		if (isset($pathNAV) && count($pathNAV)>0 ) {
			$appRelSt	= $this->appRelSt;
			$totalSteps = count($pathNAV);
			while (list($ord,$ciflow)=each($pathNAV)) {
					
					$showNd = "yes";
					$ciType = $this->appRelSt["CiInf"][$ciflow]["ci_type"];
					if (in_array($ciType, $this->nodeTypesToShowConf["show"]))
						$showNd = "yes";
					elseif (in_array($ciType, $this->nodeTypesToShowConf["hide"]))
						$showNd = "no";
					else if ($ciType=="RQ")
						$showNd = "no";
					 if ($ciflow == $session->Vars["contentId"] && $session->Vars["level_1"] == 0) {
					 	$showNd = "no";
					 }
					if ($referenceId==$ciflow || $appRelSt["CiInf"][$ciflow]["coord"][1]==0) {
					} else {
						if ($showNd == "yes") {					
							$lectureRelatedProgress[$index]["boostrap_ico"]				= $this->appRelSt["CiInf"][$ciflow]["boostrap_ico"];
							$lectureRelatedProgress[$index]["ci_type"]				= $this->appRelSt["CiInf"][$ciflow]["ci_type"];
							$lectureRelatedProgress[$index]["ciflow"] 				= $ciflow;
							$lectureRelatedProgress[$index]["title"] 				= $appRelSt["CiInf"][$ciflow]["title"];
							$lectureRelatedProgress[$index]["nodeDescription"] 		= $appRelSt["CiInf"][$ciflow]["nodeDescription"];
							$lectureRelatedProgress[$index]["actualNode"] 			= "no";
							
							$lectureRelatedProgress[$index]["n0"] 			= $appRelSt["CiInf"][$ciflow]["coord"][0];
							$lectureRelatedProgress[$index]["n1"] 			= $appRelSt["CiInf"][$ciflow]["coord"][1];
							$lectureRelatedProgress[$index]["n2"] 			= $appRelSt["CiInf"][$ciflow]["coord"][2];
							$lectureRelatedProgress[$index]["n3"] 			= $appRelSt["CiInf"][$ciflow]["coord"][3];

							$lectureRelatedProgress[$index]["actualNode"] 			= "no";
							if ($session->Vars["level_0"]==$appRelSt["CiInf"][$ciflow]["coord"][0]
							 && $session->Vars["level_1"]==$appRelSt["CiInf"][$ciflow]["coord"][1]
							 && $session->Vars["level_2"]==$appRelSt["CiInf"][$ciflow]["coord"][2]
							 && $session->Vars["level_3"]==$appRelSt["CiInf"][$ciflow]["coord"][3]
							 && $session->Vars["level_4"]==$appRelSt["CiInf"][$ciflow]["coord"][4]
							 )
								$lectureRelatedProgress[$index]["actualNode"] 			= "yes";

							$index++;
						}
					}
			}
		}
		
		$GridSessionProp["data"] = $lectureRelatedProgress;
		$GridSessionProp["AllRecs"] = count($GridSessionProp["data"]);
		WebApp::addVar("UserLectureRelatedNavToGrid", $GridSessionProp);
		$UserLectureRelatedNavToGrid = $GridSessionProp;
		return $typeNav;
	}	
	function navigationSubLevels($ciflow) {
		global $session;
		
		$md = $session->Vars["thisMode"];

		if ($this->thisModeCode=="0") { //a paaprovuar
			$BoCondition = "AND n.state".$session->Vars["lang"]." != 7
							AND content.state".$session->Vars["lang"]." not in (0,5,7)";

			//$BoCondition = "";
		} else {
			$BoCondition = "

							AND n.active".$session->Vars["lang"]." != 1
							AND n.state".$session->Vars["lang"]." != 7
							AND content.state".$session->Vars["lang"]." not in (0,5,7)
							AND content.published".$session->Vars["lang"]." = 'Y'		
							AND n.description".$session->Vars["lang"].$md." is not null";
		}
		 
		 if ($appRelSt["coord"][4]>0) {
		 	return;
		 } elseif ($appRelSt["coord"][3]>0) {
		 	$hierarchy_level = 3;
		 	
                $nivel_condition[0] = " content.id_zeroNivel = '" . $appRelSt["coord"][0] . "' ";
                $nivel_condition[1] = " content.id_firstNivel = '" . $appRelSt["coord"][1] . "' ";
                $nivel_condition[2] = " content.id_secondNivel = '" . $appRelSt["coord"][2] . "' ";
                $nivel_condition[3] = " content.id_thirdNivel = '".$appRelSt["coord"][3]."' ";
                $nivel_condition[4] = " content.id_fourthNivel > '0' ";		 	
		 	
		 } elseif ($appRelSt["coord"][2]>0) {
		 	$hierarchy_level = 2;
		 	
                $nivel_condition[0] = " content.id_zeroNivel = '" . $appRelSt["coord"][0] . "' ";
                $nivel_condition[1] = " content.id_firstNivel = '" . $appRelSt["coord"][1] . "' ";
                $nivel_condition[2] = " content.id_secondNivel = '" . $appRelSt["coord"][2] . "' ";
                $nivel_condition[3] = " content.id_thirdNivel  > '0'  ";
                $nivel_condition[4] = " content.id_fourthNivel = '0' ";	
                
		 } elseif ($appRelSt["coord"][1]>0) {
		 		$hierarchy_level = 1;
                $nivel_condition[0] = " content.id_zeroNivel = '" . $appRelSt["coord"][0] . "' ";
                $nivel_condition[1] = " content.id_firstNivel = '" . $appRelSt["coord"][1] . "' ";
                $nivel_condition[2] = " content.id_secondNivel > '0'  ";
                $nivel_condition[3] = " content.id_thirdNivel  = '0'  ";
                $nivel_condition[4] = " content.id_fourthNivel = '0' ";			 	
		 } else {
		 	
		 	return;
		 	$hierarchy_level = 1;
                $nivel_condition[0] = " content.id_zeroNivel = '" . $appRelSt["coord"][0] . "' ";
                $nivel_condition[1] = " content.id_firstNivel > '0' ";
                $nivel_condition[2] = " content.id_secondNivel = '0'  ";
                $nivel_condition[3] = " content.id_thirdNivel  = '0'  ";
                $nivel_condition[4] = " content.id_fourthNivel = '0' ";			 
		 }

		$tmpS=array();
		$indSub = 0;

           if (count($nivel_condition) > 0) {

               $kushtSql = implode(" AND ", $nivel_condition);
               $orderBy = "ORDER BY ordT DESC,  n.orderMenu";              
                
                $sql_con = "SELECT content.content_id, ci_type, profil_rights.`read_write` as rights, n.description".$session->Vars["lang"].$md." as nodeName,
											content.id_zeroNivel, content.id_firstNivel,content.id_secondNivel,content.id_thirdNivel,content.id_fourthNivel,
											titleLng1 as title, 
											if (ci_type in ('PR','EC','EL'), 1,0) as ordT,
											
											n.orderMenu as nodeOrder ".$familyOrderField.", 
											
											coalesce(boostrap_class,'') as boostrap_class, coalesce(boostrap_ico,'') as boostrap_ico, 
											
											nivel_1.node_family_id 

									  FROM content
									  JOIN document_types ON document_types.doctype_name = content.ci_type
								  
										JOIN nivel_4			AS n	ON (    content.id_zeroNivel   = n.id_zeroNivel
																AND content.id_firstNivel  = n.id_firstNivel
																AND content.id_secondNivel = n.id_secondNivel
																AND content.id_thirdNivel  = n.id_thirdNivel
																AND content.id_fourthNivel = n.id_fourthNivel
																)								  
									  
									  
										JOIN profil_rights ON (       content.id_zeroNivel   = profil_rights.id_zeroNivel
																AND content.id_firstNivel  = profil_rights.id_firstNivel
																AND content.id_secondNivel = profil_rights.id_secondNivel
																AND content.id_thirdNivel  = profil_rights.id_thirdNivel
																AND content.id_fourthNivel = profil_rights.id_fourthNivel
																AND profil_rights.profil_id in (".$session->Vars["tip"].")
															)
															
															
										JOIN nivel_1 ON content.id_zeroNivel= nivel_1.id_zeroNivel AND content.id_firstNivel  = nivel_1.id_firstNivel 
														AND nivel_1.node_family_id in (7,14)
															
										".$familyOrderJoin."
										
								WHERE " . $kushtSql . "
								  AND orderContent   = '0' 
								  AND ci_type in ('PR','EC','EL')

								  ".$BoCondition."
							GROUP BY content.content_id	  

								  ".$orderBy;

                	$rs_con = WebApp::execQuery($sql_con);
					$order = 1;
					$orderR = 1;
					$orderEc = 1;
					$orderGN = 1;
					
					$lecturesRelated = array();
					while (!$rs_con->EOF()) {

						$ci_type 			= $rs_con->Field("ci_type");
						$relid 				= $rs_con->Field("content_id");
						$title 				= $rs_con->Field("title");
						$nodeName 			= $rs_con->Field("nodeName");	
						$nodeOrder 			= $rs_con->Field("nodeOrder");
						$boostrap_ico		= $rs_con->Field("boostrap_ico");
						
						
						$showNd = "yes";
						if (in_array($ciType, $this->nodeTypesToShowConf["show"]))
							$showNd = "yes";
						elseif (in_array($ciType, $this->nodeTypesToShowConf["hide"]))
							$showNd = "no";
						else if ($ciType=="RQ")
							$showNd = "no";


						 if ($ciflow == $session->Vars["contentId"] && $session->Vars["level_1"] == 0) {
							$showNd = "no";
						 }
										
						//if ($showNd == "yes") {							
							$tmpS["data"][$indSub]["ci_type"] = $ci_type;
							$rights = explode(",", $rs_con->Field("rights"));
							if (is_array($rights) && in_array("W", $rights))
								$tmpS["data"][$indSub]["read_write"] = "W";
							else    $tmpS["data"][$indSub]["read_write"] = "R";


							$rights = explode(",", $rs_con->Field("rights"));
							if (is_array($rights) && in_array("W", $rights))
								$tmpS["data"][$indSub]["read_write"] = "W";
							else    $tmpS["data"][$indSub]["read_write"] = "R";

							$tmpS["data"][$indSub]["title"] = $title;
							$tmpS["data"][$indSub]["nodeDescription"] = $nodeName;
							$tmpS["data"][$indSub]["boostrap_ico"] = $boostrap_ico;
							$tmpS["data"][$indSub]["ciflow"] = $relid;
					
							$tmpS["data"][$indSub]["coord"][0] = $rs_con->Field("id_zeroNivel");
							$tmpS["data"][$indSub]["coord"][1] = $rs_con->Field("id_firstNivel");
							$tmpS["data"][$indSub]["coord"][2] = $rs_con->Field("id_secondNivel");
							$tmpS["data"][$indSub]["coord"][3] = $rs_con->Field("id_thirdNivel");
							$tmpS["data"][$indSub]["coord"][4] = $rs_con->Field("id_fourthNivel");
							
							$tmpS["data"][$indSub]["node_family_id"] = $rs_con->Field("node_family_id");	
							$this->navigationSubLevels($relid, $tmpS["data"][$indSub]);
							
							$indSub++;
						//}

						$rs_con->MoveNext();
					}
            }  		

		$tmpS["AllRecs"] = count($tmpS["data"]);
		WebApp::addVar("SubNavToGrid_".$ciflow, $tmpS);	
	}
	
	//BELOW FUNCTION TO BE REVIEWD CLEARED OR CALLED BY CONFIGURATION OF APPLICATION
    function getLecturePresentationExtended()    {	}
    function getPresentationLecturer()    {}	
    function getCrmList() {}       
	function getCrmDataEditorCourse($idCourse) {}	
	function getCrmDataModeratorTutorial($idCourse,$actualFilterStatus) {}    
    function CRM_EVENT_TUTORIAL_RELATED($referenceIDs,$actualFilterStatus) {}
    function CRM_RELATED($referenceIDs,$actualFilterStatus) {}	
    function CRM_LECTURE_RELATED($referenceIDs,$actualFilterStatus)    {}    
    function showProgressInTutorialOrEvents($typeOfCatalogItems,$prgCnf,$referenceIDsCOF, $distinctsIdsInCollector) {}
	function getPartecipationInfoInContextNotTrial ($productsToFindLIA) {}	
	function getSubscribers () {}
    function getProductInfoConfiguration($confNem,$keyToMainGridRelatedToNemConf="",$enrollementAgregatedConf="chilsInCollector")    {}
    function getProductInfoConfigurationItems($data,$enrollingConfiguration,$keyToMainGridRelatedToNemConf,$confNem)   {}	
	function enrollButtonControll($properties,$my_ci_target){}	
	function getPartecipationInfoInContext () {}		
	function initUserCartAndBasketState(){}	
    function getProductsItemsStateRelatedToUser()    {}	
	function getUserElearning(){	}
	function controlOwnerShipToLecturePlayMode($lectureID){	}	
	function controllUserProgressInLecturePlay(){	}
	function checkIfItemsAreInUserCME(){	}		
    function deleteOldUni($uniqueid)    {    }
    function controllEccERefIDStatic($EccERefID,$EccEmode,$Eccsid)    {    }			
    function controllEccERefID($EccERefId, $EccEmode, $Eccsid)    {    }		
    function controllEccERefIDmode()    {    }   	
	function getProductDetails($newConfigurationCiContainerParams=array()){	}
    function controllActualStateRelatedToLecture()    {    }	
	function controlOwnerShipToLecture($lectureID){	}	
}