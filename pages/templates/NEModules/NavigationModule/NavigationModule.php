<?php
function NavigationModule_onRender() 
{
    global $session,$event,$global_cache_dynamic,$cacheDyn,$G_HOME_LINK,$linkParams, $linkParamsCached;


$starts = WebApp::get_formatted_microtime();
	
	
	$flagsToControlImages = array();


	$NEM_TEMPLATE = "MainNavigationTemplate.html";

	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {

		$prop_arr = WebApp::clearNemAtributes($session->Vars["idstemp"]);
		$NEM_FILENAME = 'default_template.html';

		require_once(INC_PHP_AJAX."NemsManager.class.php");
		$grn = NemsManager::getFrontEndGeneralProperties($prop_arr);
		
		if (isset($grn["NEM_FILENAME"]) && $grn["NEM_FILENAME"]!='') {
			$NEM_FILENAME = $grn["NEM_FILENAME"];
		}	
		
		if(isset($grn['home_link_show']) && $grn['home_link_show']=="yes") {
					WebApp::addVar("home_link_show", $grn['home_link_show']);
					WebApp::addVar("home_link_label_exist", $grn['home_link_label_exist']);
					WebApp::addVar("home_link_label", $grn['home_link_label']);
		
		}
		
		$showHeadline = "no";
		if(isset($prop_arr["slogan_title"]) && $prop_arr["slogan_title"] != ""){
			$showHeadline = "yes";
		}
		WebApp::addVar("showHeadline", $showHeadline);
		
		
		$NEM_TEMPLATE = '<Include SRC="{{NEMODULES_PATH}}NavigationModule/'.$NEM_FILENAME.'"/>';
		
		//READ CONFIGURATION
		if(isset($prop_arr['properties_show'])) {
			reset($prop_arr['properties_show']);
			foreach($prop_arr['properties_show'] as $val){
				switch($val){
					case 'nodeLabel':
						WebApp::addVar("show_nodeLabel", "yes");
					break;
					case 'mainContentTitle':
						WebApp::addVar("show_mainContentTitle", "yes");
					break;
					case 'mainContentAbstract':
						WebApp::addVar("show_mainContentAbstract", "yes");
					break;
					case 'mainContentThumbnail':
						WebApp::addVar("show_mainContentThumbnail", "yes");
						$flagsToControlImages["show_mainContentThumbnail"] = "yes";
					break;
					case 'nodeIcon':
						WebApp::addVar("show_nodeIcon", "yes");
					break;                
					case 'nodeImage':
						WebApp::addVar("show_nodeImage", "yes");
						$flagsToControlImages["show_nodeImage"] = "yes";
					break;
				}
			}
		}




		if(isset($prop_arr['makeLink'])) {
			reset($prop_arr['makeLink']);
			foreach($prop_arr['makeLink'] as $val){
				switch($val){
					case 'nodeLabel':
						WebApp::addVar("makeLink_nodeLabel", "yes");
					break;
					case 'nodeImage':
						WebApp::addVar("makeLink_nodeImage", "yes");
					break;
					case 'mainContentTitle':
						WebApp::addVar("makeLink_mainContentTitle", "yes");
					break;
					case 'mainContentThumbnail':
						WebApp::addVar("makeLink_mainContentThumbnail", "yes");
					break;
				}
			}
		}
		if(isset($prop_arr['showLinkToDetails']) && $prop_arr['showLinkToDetails']==1) {
			if(isset($prop_arr['linkLabel']) && $prop_arr['linkLabel']!="") {
					WebApp::addVar("linkLabel", $prop_arr['linkLabel']);
					WebApp::addVar("showLinkToDetails", "yes");

			}		
		}
		//READ CONFIGURATION

//    [familiesRelated]  >= 1

		
		if(defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP=="Y") 
			$nodeBootstrapFields = "coalesce(boostrap_class,'') as boostrap_class, coalesce(boostrap_ico,'') as boostrap_ico,";

		IF ($session->Vars["thisMode"] == "_new")
			{$kusht_aktiv_joaktiv = " ";}
		ELSE
			{$kusht_aktiv_joaktiv = " AND nivel_4.active".$session->Vars["lang"]." != 1 
									  AND c.published".$session->Vars["lang"]." = 'Y'";}
			
			
		$kusht_aktiv_joaktiv = " ";

		if ($session->Vars["thisMode"]=="") {
			$kusht_aktiv_joaktiv .=	" AND c.state".$session->Vars["lang"]." not in (0,5,7)";
			$kusht_aktiv_joaktiv .=	" AND c.published".$session->Vars["lang"]." = 'Y'";
		//	$kusht_aktiv_joaktiv .=	" AND nivel_4.active".$session->Vars["lang"]." != '1' ";
			$kusht_aktiv_joaktiv .=	" AND nivel_4.state".$session->Vars["lang"]." != 7 ";
		} else {
			$kusht_aktiv_joaktiv .=	" AND c.state".$session->Vars["lang"]." not in (7)";
		}


			
		
		$collectionMode = "sessionNodes";
		if ($prop_arr['collectionMode']=="4")
			$collectionMode = "sessionNodesOrSiblingsIfEmpty";
		if ($prop_arr['collectionMode']=="3")
			$collectionMode = "firstLevelSubtree";
			
		$familiesRelated = "none";
		if ($prop_arr['collectionMode']=="0" && isset($prop_arr['familiesRelated']) && $prop_arr['familiesRelated']>=0) {
			$collectionMode = "families";
			$familiesRelated = $prop_arr['familiesRelated'];
		}
		
		
		if ($prop_arr['collectionMode']=="9" && isset($prop_arr['familiesRelated']) && $prop_arr['familiesRelated']>=0) {
			$collectionMode = "families_first_level";
			$familiesRelated = $prop_arr['familiesRelated'];
		}		
		
		
		WebApp::addVar("collectionMode", $collectionMode);
		WebApp::addVar("familiesRelated", $familiesRelated);

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
		
		
		$actualLevelKey4 = $session->Vars["level_0"]."_".$session->Vars["level_1"]."_".$session->Vars["level_2"]."_".$session->Vars["level_3"]."_0";
		$actualLevelKey3 = $session->Vars["level_0"]."_".$session->Vars["level_1"]."_".$session->Vars["level_2"]."_0_0";
		$actualLevelKey2 = $session->Vars["level_0"]."_".$session->Vars["level_1"]."_0_0_0";
		$actualLevelKey1 = $session->Vars["level_0"]."_0_0_0_0";
		$idKeyHome = $actualLevelKey1;
    
    	$actualKey = $session->Vars["level_0"]."_".$session->Vars["level_1"]."_".$session->Vars["level_2"]."_".$session->Vars["level_3"]."_".$session->Vars["level_4"];
    	WebApp::addVar("actualKey", $actualKey);
    
		if ($hierarchyLevel == 4) {
			WebApp::addVar("childKey", $actualLevelKey4);
			WebApp::addVar("parentKey", $actualLevelKey3);
		} elseif ($hierarchyLevel == 3) {	
			WebApp::addVar("childKey", $actualLevelKey4);
			WebApp::addVar("parentKey", $actualLevelKey3);
		} elseif ($hierarchyLevel == 2) {	
			WebApp::addVar("childKey", $actualLevelKey3);
			WebApp::addVar("parentKey", $actualLevelKey2);
		} elseif ($hierarchyLevel == 1) {	
			WebApp::addVar("childKey", $actualLevelKey2);
			WebApp::addVar("parentKey", $actualLevelKey1);
		} else {
			 WebApp::addVar("childKey", $actualLevelKey1);
		}

		WebApp::addVar("hierarchyLevel", $hierarchyLevel);
				

    
/*
collectionMode_0	Families
collectionMode_1 	Session Subnodes
collectionMode_2 	Specific Nodes
collectionMode_4 	Session Subnodes Or Siblings Nodes if Subnodes empty

prop_arrArray
(
    [workGroup]  >= 0
    [collectionMode]  >= 1
    [familiesRelated]  >= 4
    [nivel_start]  >= 1
    [nivel_end]  >= 4
    [actualNodeSelectedCoord]  >= Array
        (
            [0]  >= 
        )
    [nodeSelectedCoord]  >= Array
        (
            [0]  >= 
        )
)
INSERT INTO `kw_app_publicationcategories` (`kw_id`, `id_parent`, `family_id`, `status`, `display_order`, `description`, `descriptionLng1`, `descriptionLng2`, `descriptionLng3`, `abstractLng1`, `abstractLng2`, `abstractLng3`, `created_by`, `time_stamp`) VALUES 
  (29, 1, 1, '0', 0, 'Revista Ekonomike', 'Revista Ekonomike', 'Economic Review', 'Item Description', '', '', '', 1, '2009-01-29 14:34:31')
INSERT INTO `kw_app_publicationcategories` (`kw_id`, `id_parent`, `family_id`, `status`, `display_order`, `description`, `descriptionLng1`, `descriptionLng2`, `descriptionLng3`, `abstractLng1`, `abstractLng2`, `abstractLng3`, `created_by`, `time_stamp`) VALUES 
  (30, 1, 1, '0', 0, 'Revista Shkencore', 'Revista Shkencore', 'Research Newsletter', 'Item Description', '', '', '', 1, '2009-01-29 14:34:31')

INSERT INTO `template_list` ( `template_name`, `template_tip`, `template_box`, `document_type`, `master_template`, `is_default`, `relativePathToTemplate`) VALUES 
  ('Sitemap (family related)', 'NavigationModule', 'sitemap_family.html', 'al', 'default', 'no', NULL);


INSERT INTO `template_list` ( `template_name`, `template_tip`, `template_box`, `document_type`, `master_template`, `is_default`, `relativePathToTemplate`) VALUES 
  ('Full Subtree of Session First Level', 'NavigationModule', 'firstLevelSubtree.html', 'al', 'default', 'no', NULL);

E:\projects_128\boa_internal_2017\templates\NEModules\NavigationModule\firstLevelSubtree.html

E:\projects_128\boa_internal_2017\templates\NEModules\NavigationModule\sitemap_family.html

*/

        
		$allLevelsNodeInfo = array();
	
        							
        							
		$sqlInfoTemplate = "SELECT IF(nivel_4.description".$session->Vars["lang"]."".$session->Vars["thisMode"]." IS NULL, 
							'', nivel_4.description".$session->Vars["lang"]."".$session->Vars["thisMode"].") as NodeDescription,
								".$nodeBootstrapFields."	
							COALESCE(nivel_4.imageSm_id,      '') as imageSm_id_node, 		
							
							nivel_4.isExpanded                                   as isExpanded, 
							nivel_4.clickable                                    as clickable, 
							
							coalesce(c.imageSm_id,'') as imageSm_id,	
							coalesce(c.imageBg_id,'') as imageBg_id,	
							
							c.ci_type		as ci_type,
							c.content_id	as content_id,
							
							COALESCE(c.title".$session->Vars["lang"].", '') as ci_title,
							COALESCE(c.description".$session->Vars["lang"].$session->Vars["thisMode"].", '') as ci_description,
							
							nivel_4.id_zeroNivel,nivel_4.id_firstNivel,nivel_4.id_secondNivel,nivel_4.id_thirdNivel,nivel_4.id_fourthNivel

						  FROM nivel_4
						  JOIN content as c 
							ON nivel_4.id_zeroNivel		= c.id_zeroNivel
						   AND nivel_4.id_firstNivel	= c.id_firstNivel
						   AND nivel_4.id_secondNivel	= c.id_secondNivel
						   AND nivel_4.id_thirdNivel	= c.id_thirdNivel
						   AND nivel_4.id_fourthNivel	= c.id_fourthNivel
						   
						   
						 JOIN profil_rights ON (       nivel_4.id_zeroNivel   = profil_rights.id_zeroNivel
														AND nivel_4.id_firstNivel  = profil_rights.id_firstNivel
														AND nivel_4.id_secondNivel = profil_rights.id_secondNivel
														AND nivel_4.id_thirdNivel  = profil_rights.id_thirdNivel
														AND nivel_4.id_fourthNivel = profil_rights.id_fourthNivel
														AND profil_rights.profil_id in (".$session->Vars["tip"].")
													)					   

						 WHERE nivel_4.state".$session->Vars["lang"]." != 7  
						   AND orderContent = '0' ".$kusht_aktiv_joaktiv."
						   
						   
					   GROUP BY nivel_4.id_zeroNivel,nivel_4.id_firstNivel,nivel_4.id_secondNivel,nivel_4.id_thirdNivel,nivel_4.id_fourthNivel
						   
						   ";




/*if ($session->Vars["uni"]=="20180215120559192168120269887652") {

		 echo $collectionMode."<textarea>prop_arr";
		 print_r($prop_arr);
		// print_r($allLevelsNodeInfo);
		// print_r($prop_arr);
		// print_r($nodeToFindInfo);
		// print_r($sqlInfoLevel);
		 print_r($rsData);
		 echo '</textarea>';  
}*/
//NEW IMPLEMENATION
	


				$sqlInfoLevelSingle = "
				SELECT distinct nivel_4.id_zeroNivel,nivel_4.id_firstNivel,nivel_4.id_secondNivel,nivel_4.id_thirdNivel,nivel_4.id_fourthNivel, 
							node_family_id, nivel_4.orderMenu

					  FROM nivel_4
					  JOIN content as c 
						ON nivel_4.id_zeroNivel		= c.id_zeroNivel
					   AND nivel_4.id_firstNivel	= c.id_firstNivel
					   AND nivel_4.id_secondNivel	= c.id_secondNivel
					   AND nivel_4.id_thirdNivel	= c.id_thirdNivel
					   AND nivel_4.id_fourthNivel	= c.id_fourthNivel
					   AND orderContent = '0'

					 JOIN profil_rights ON (       nivel_4.id_zeroNivel   = profil_rights.id_zeroNivel
													AND nivel_4.id_firstNivel  = profil_rights.id_firstNivel
													AND nivel_4.id_secondNivel = profil_rights.id_secondNivel
													AND nivel_4.id_thirdNivel  = profil_rights.id_thirdNivel
													AND nivel_4.id_fourthNivel = profil_rights.id_fourthNivel
													AND profil_rights.profil_id in (".$session->Vars["tip"].")
												)					   

					 WHERE nivel_4.state".$session->Vars["lang"]." != 7  
					    ".$kusht_aktiv_joaktiv."
					   ";	
/*
sessionNodesOrSiblingsIfEmpty:	4 Session Subnodes Or Siblings Nodes if Subnodes empty

families:						0 Families
families_first_level:			9 Families (only first level)

sessionNodes:					1 Session Subnodes			

firstLevelSubtree:				3 First Level Subtree
*/	
				
				
				if ($collectionMode == "sessionNodesOrSiblingsIfEmpty") {
					$sqlInfoLevelSingle = $sqlInfoLevelSingle."  AND nivel_4.id_zeroNivel = '".$session->Vars["level_0"]."'
												  AND nivel_4.id_firstNivel > 0
												  AND nivel_4.id_secondNivel = 0
												  AND nivel_4.id_thirdNivel  = 0
												  AND nivel_4.id_fourthNivel = 0								  
											ORDER BY nivel_4.orderMenu";				

				} elseif ($collectionMode == "families") {
					$sqlInfoLevelSingle = $sqlInfoLevelSingle."  AND nivel_4.id_zeroNivel = '".$session->Vars["level_0"]."'
												  	  AND nivel_4.node_family_id ='".$familiesRelated."'
 													  AND nivel_4.id_firstNivel >= 0
													  AND nivel_4.id_secondNivel >= 0
 													  AND nivel_4.id_thirdNivel  >= 0
 													  AND nivel_4.id_fourthNivel >= 0												  	  
											ORDER BY nivel_4.orderMenu";				

				} elseif ($collectionMode == "families_first_level") {
					$sqlInfoLevelSingle = $sqlInfoLevelSingle."  AND nivel_4.id_zeroNivel = '".$session->Vars["level_0"]."'
												  	  AND nivel_4.node_family_id ='".$familiesRelated."'
													  AND nivel_4.id_firstNivel >= 0
													  AND nivel_4.id_secondNivel = 0
													  AND nivel_4.id_thirdNivel = 0
													  AND nivel_4.id_fourthNivel = 0
												  	  AND nivel_4.node_family_id ='".$familiesRelated."'
											ORDER BY nivel_4.orderMenu";				
				
				} elseif ($collectionMode == "sessionNodes") {
					
					
					
						//	if ($session->Vars["level_4"]>0) 				$hierarchyLevel = 4;
						//	elseif ($session->Vars["level_3"]>0) 			$hierarchyLevel = 3;
						//	elseif ($session->Vars["level_2"]>0) 			$hierarchyLevel = 2;
						//	elseif ($session->Vars["level_1"]>0) 			$hierarchyLevel = 1;
						//	else											$hierarchyLevel = 0;
		
					//session childs nuk ka per nivelin e katert
					if ($hierarchyLevel == 4) {
					
						$sqlInfoLevelSingle = $sqlInfoLevelSingle."  AND 1=2";	
					
					} elseif ($hierarchyLevel == 3) { 
						
						$sqlInfoLevelSingle = $sqlInfoLevelSingle."  
														  AND nivel_4.id_zeroNivel 		= '".$session->Vars["level_0"]."'
														  AND nivel_4.id_firstNivel 	= '".$session->Vars["level_1"]."'
														  AND nivel_4.id_secondNivel 	= '".$session->Vars["level_2"]."'
														  AND nivel_4.id_thirdNivel 	= '".$session->Vars["level_3"]."'
														  AND nivel_4.id_fourthNivel >0
												ORDER BY nivel_4.orderMenu";						
					
					} elseif ($hierarchyLevel == 2) { 
						
						//sessionNodes
						
						$sqlInfoLevelSingle = $sqlInfoLevelSingle."  AND nivel_4.id_zeroNivel 	= '".$session->Vars["level_0"]."'
														  AND nivel_4.id_firstNivel 			= '".$session->Vars["level_1"]."'
														  AND nivel_4.id_secondNivel 			= '".$session->Vars["level_2"]."'
														  AND nivel_4.id_thirdNivel >0
														  AND nivel_4.id_fourthNivel =0
												ORDER BY nivel_4.orderMenu";						
					
					} elseif ($hierarchyLevel == 1) { 
						
						$sqlInfoLevelSingle = $sqlInfoLevelSingle."  AND nivel_4.id_zeroNivel 	= '".$session->Vars["level_0"]."'
														  AND nivel_4.id_firstNivel 			= '".$session->Vars["level_1"]."'
														  AND nivel_4.id_secondNivel >0
														  AND nivel_4.id_thirdNivel  = 0
														  AND nivel_4.id_fourthNivel = 0
												ORDER BY nivel_4.orderMenu";						
					
					} else {
						$sqlInfoLevelSingle = $sqlInfoLevelSingle."  AND nivel_4.id_zeroNivel = '".$session->Vars["level_0"]."'
														  AND nivel_4.id_firstNivel = '".$session->Vars["level_1"]."'
														  AND nivel_4.id_secondNivel = '".$session->Vars["level_2"]."'
														  AND nivel_4.id_thirdNivel = '".$session->Vars["level_3"]."'
														  AND nivel_4.id_fourthNivel = '".$session->Vars["level_4"]."'
												ORDER BY nivel_4.orderMenu";											
																
					}
					
					
					
					
											
					
//  {{debugTimeNv}}

					

											
				
				} elseif ($collectionMode == "firstLevelSubtree") {
					$sqlInfoLevelSingle = $sqlInfoLevelSingle."  AND nivel_4.id_zeroNivel = '".$session->Vars["level_0"]."'
													  AND nivel_4.id_firstNivel = '".$session->Vars["level_1"]."'
													  AND nivel_4.id_secondNivel  >= 0
													  AND nivel_4.id_thirdNivel  >= 0
													  AND nivel_4.id_fourthNivel  >= 0
											ORDER BY nivel_4.orderMenu";				
				
				} else {
				
					$sqlInfoLevelSingle = $sqlInfoLevelSingle."  
													  AND nivel_4.id_zeroNivel = '".$session->Vars["level_0"]."'
													  AND nivel_4.id_firstNivel = '".$session->Vars["level_1"]."'
													  AND nivel_4.id_secondNivel = 0
													  AND nivel_4.id_thirdNivel  = 0
													  AND nivel_4.id_fourthNivel = 0								  
											ORDER BY nivel_4.orderMenu";					
				}


				$allLevelsNodeInfo = array();
				$levelsFamilyGrid = array();
				$levelsHierarchy = array();
				$nodeToFindInfo = array();
				
				$rsLevSingle = WebApp::execQuery($sqlInfoLevelSingle);
        		$levelSingle = array();
				WHILE (!$rsLevSingle->EOF()) {
				
					$crd = array();
					$node_family_id = $rsLevSingle->Field("node_family_id");
					$orderMenu 		= $rsLevSingle->Field("orderMenu");
					$crd[0] 		= $rsLevSingle->Field("id_zeroNivel");
					$crd[1] 		= $rsLevSingle->Field("id_firstNivel");
					$crd[2] 		= $rsLevSingle->Field("id_secondNivel");
					$crd[3] 		= $rsLevSingle->Field("id_thirdNivel");
					$crd[4] 		= $rsLevSingle->Field("id_fourthNivel");
					
					$itemLevel = 0;
					$parentCrd = $crd;
					
					if ($crd[4]>0)			{$itemLevel = 4;$parentCrd[4]=0;}
					elseif ($crd[3]>0)		{$itemLevel = 3;$parentCrd[3]=0;}
					elseif ($crd[2]>0)		{$itemLevel = 2;$parentCrd[2]=0;}
					elseif ($crd[1]>0)		{$itemLevel = 1;$parentCrd[1]=0;}
					
					
					

					
					
					$idKey 	= implode("_",$crd);
					$idKeyNode = implode(",",$crd);
					$idKeyNodeParent = implode("_",$parentCrd);

					//if ($crd[1]>0) {
						//$levelsHierarchy[$node_family_id][$idKeyNodeParent][$orderMenu] = $idKey;
						
						$levelsHierarchy["childs"][$idKeyNodeParent][] = $idKey;
						
						$levelsHierarchy["tree"][$node_family_id][$idKeyNodeParent][] = $idKey;
						$levelsHierarchy["data"][$idKey]["lev"] = $itemLevel;
						$levelsHierarchy["data"][$idKey]["isActive"] = "no";
						$levelsHierarchy["levels"][$node_family_id][$itemLevel][$idKeyNodeParent][] = $idKey;
						
						
						$nodeToFindInfo[$idKey] = $idKeyNode;
					//}
					
					if ($itemLevel==1 && $crd[0]==$session->Vars["level_0"] && $crd[1]==$session->Vars["level_1"] )
						$levelsHierarchy["data"][$idKey]["isActive"] = "yes";
					elseif ($itemLevel==2 && $crd[0]==$session->Vars["level_0"] && $crd[1]==$session->Vars["level_1"] && $crd[2]==$session->Vars["level_2"] )
						$levelsHierarchy["data"][$idKey]["isActive"] = "yes";					
					elseif ($itemLevel==3 && 		
												$crd[0]==$session->Vars["level_0"] 
											 && $crd[1]==$session->Vars["level_1"] 
											 && $crd[2]==$session->Vars["level_2"] 
											 && $crd[3]==$session->Vars["level_3"] )
						$levelsHierarchy["data"][$idKey]["isActive"] = "yes";					
					elseif ($itemLevel==4 && 		
												$crd[0]==$session->Vars["level_0"] 
											 && $crd[1]==$session->Vars["level_1"] 
											 && $crd[2]==$session->Vars["level_2"] 
											 && $crd[3]==$session->Vars["level_3"]
											 && $crd[4]==$session->Vars["level_4"])
						$levelsHierarchy["data"][$idKey]["isActive"] = "yes";					
					
					if ($crd[0]==$session->Vars["level_0"] 
					 && $crd[1]==$session->Vars["level_1"] 
					 && $crd[2]==$session->Vars["level_2"]
					 && $crd[3]==$session->Vars["level_3"]  
					 && $crd[4]==$session->Vars["level_4"]) {
						$levelsHierarchy["data"][$idKey]["isActive"] = "actual";	
					}			
										
					
//	if ($session->Vars["level_2"]==0)
//							$levelsHierarchy["data"][$idKey]["isActive"] = "actual";				
					
					
					
					$rsLevSingle->MoveNext();
				}
				
				


					/*echo "<br>levelsHierarchy<textarea>";
					print_r($levelsHierarchy);
					echo "</textarea>";*/				
/*if ($collectionMode == "sessionNodes" && $session->Vars["uni"]=="20180215120559192168120269887652") {
			
					echo "<br>$collectionMode:$hierarchyLevel<textarea>";
					print_r($rsLevSingle);
					echo "</textarea>";


					echo "<br>levelsHierarchy<textarea>";
					print_r($levelsHierarchy);
					echo "</textarea>";
					



}	*/				
				
				
				$dtInit = array();
				$dtInit["keyIden"] = "";
				$dtInit["isActive"] = "no";
				$dtInit["nodelevel"] = "";
				$dtInit["nr_of_childs"] = "0";
				
				if (count($levelsHierarchy["levels"])>0) {
					while (list($idfam,$datafam)=each($levelsHierarchy["levels"])) {
						if (count($datafam)>0) {
							while (list($level,$levelchilds)=each($datafam)) {
								if ($level>0) {	
									while (list($idKeyNodeParent,$childs)=each($levelchilds)) {
										$dtGrid = array();
										while (list($order,$childsKey)=each($childs)) {
											$tmp = $dtInit;
											$tmp["nodelevel"] = $level;
											$tmp["keyIden"] = $childsKey;
											$tmp["isActive"] = $levelsHierarchy["data"][$childsKey]["isActive"];
											if (isset($levelsHierarchy["childs"][$childsKey])) 
												$tmp["nr_of_childs"] = count($levelsHierarchy["childs"][$childsKey]);
											$dtGrid["data"][] = $tmp;
										}
										$dtGrid["AllRecs"] = count($dtGrid["data"]);
										if ($level==1) {
											WebApp::addVar("GridAuthorNav_".$idKeyNodeParent, $dtGrid);	
											WebApp::addVar("GridAuthorNavFam_".$idfam, $dtGrid);	
										} else {
											WebApp::addVar("GridAuthorNav_".$idKeyNodeParent, $dtGrid);	
										}
									}
								}	
							}
						}
					}
				}
        
					
	

	//END ESHTTE NJELLOJ



		
     


				if (count($nodeToFindInfo)>0) {
				$sql = "SELECT 
				
							content_id as node_ci_id,
							
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

						 WHERE row(nivel_4.id_zeroNivel,nivel_4.id_firstNivel,nivel_4.id_secondNivel,nivel_4.id_thirdNivel,nivel_4.id_fourthNivel) 
							in (row(".implode("),row(",$nodeToFindInfo)."))
						   AND nivel_4.state".$session->Vars["lang"]." != 7 
						   AND orderContent = 0
						ORDER BY nivel_4.id_zeroNivel,nivel_4.id_firstNivel,nivel_4.id_secondNivel,nivel_4.id_thirdNivel,nivel_4.id_fourthNivel";
						
				$rsData = WebApp::execQuery($sql);
				while (!$rsData->EOF()) {
					
					
					$id0 = $rsData->Field("id_zeroNivel");	
					$id1 = $rsData->Field("id_firstNivel");	
					$id2 = $rsData->Field("id_secondNivel");	
					$id3 = $rsData->Field("id_thirdNivel");	
					$id4 = $rsData->Field("id_fourthNivel");
					$node_ci_id = $rsData->Field("node_ci_id");
					
					
					
					$nodeKeyCrd = $id0."_".$id1."_".$id2."_".$id3."_".$id4;	
					$linkCrd = $id0.",".$id1.",".$id2.",".$id3.",".$id4;	
					
					$linkhref  = "javascript:GoTo('thisPage?event=none.ch_state(k=".$linkCrd.")')";
					//$linkhrefCached  = "javascript:GoTo('thisPage?event=none.ch_state(k=".$linkCrd.")')";
					
					IF ($global_cache_dynamic == "Y") {
						$linkhref = $cacheDyn->get_CiMainTitleOfNodeToUrl($linkCrd,$session->Vars["ln"]);
						//ECHO $node_nedded_data[$koord_level_node_key]["hrefToNodeTarget"]."-$koord_level_node<br>";
					} elseif ($application_is_cached=="yes") {

					}					
			
					$nodeImageId 	= TRIM($rsData->Field("imageSm_id_node"));	
					$imageSm_id 		= TRIM($rsData->Field("imageSm_id"));	
					$imageBg_id 		= TRIM($rsData->Field("imageBg_id"));	
					

					if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") {
						//thir griden
						
						
						
						if (isset($flagsToControlImages["show_nodeImage"]) && $flagsToControlImages["show_nodeImage"]=="yes") {
							if ($nodeImageId>0)
								CiManagerFe::get_SL_CACHE_INDEX($nodeImageId);	
						
						}
						if (isset($flagsToControlImages["show_mainContentThumbnail"]) && $flagsToControlImages["show_mainContentThumbnail"]=="yes") {
						
							if ($imageSm_id>0)
									CiManagerFe::get_SL_CACHE_INDEX($imageSm_id);	
							if ($imageBg_id>0)
									CiManagerFe::get_SL_CACHE_INDEX($imageBg_id);	
						}
					}	


					$NodeDescription = TRIM($rsData->Field("NodeDescription"));	
					$CiTitle		 = TRIM($rsData->Field("title"));
					$CiDescription	 = TRIM($rsData->Field("CiDescription"));
			
					
					$tmp = array();
					
					$nodeClass 	 = "";
					$nodeIco	 = "";

					if(defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP=="Y") {
						$nodeClass 	 = $rsData->Field("boostrap_class");
						$nodeIcon	 = $rsData->Field("boostrap_ico");
					}					
					
					$tmp["node_ci_id"]	= $node_ci_id;
					$tmp["nodeClass"]	= $nodeClass;
					$tmp["nodeIcon"]	= $nodeIcon;
					
					
					$tmp["boostrap_class"]	= $tmp["nodeClass"];
					$tmp["boostrap_ico"]	= $tmp["nodeIcon"];
					
					
					if ($tmp["nodeClass"]!="")
						$tmp["nodeClassExists"] = "yes";
									
					if ($tmp["nodeIcon"]!="")
						$tmp["nodeIconExists"] = "yes";
						
					
					$tmp["label"] 				= $NodeDescription;
					$tmp["NodeDescription"]		= $NodeDescription;
					
					$tmp["link"] 			= $linkhref;
					$tmp["linkcrd"] 		= $linkCrd;
					$tmp["linkhref"] 		= $linkhref;
					
					
					$tmp["ci_title"]			= $CiTitle;
					$tmp["ci_description"]		= $CiDescription;
					
					
					$tmp["nodeImageId"] 	= $nodeImageId;
					$tmp["imageSm_id"] 		= $imageSm_id;
					$tmp["imageBg_id"]		= $imageBg_id;
					
			
					$ndInfo["data"][0] = $tmp;
					$ndInfo["AllRecs"]  = 1;		
					
					$allLevelsNodeInfo["info"][$nodeKeyCrd] = $tmp;
	
					WebApp::addVar("GridAuthorNavData_".$nodeKeyCrd, $ndInfo);		
					//echo "<br>GridAuthorNavData_".$nodeKeyCrd;
				
					$rsData->MoveNext();
				} 
			}




   }
	

	
/*
		 echo $collectionMode."<textarea>prop_arr";
		 print_r($lev1Familygrid);
		 print_r($allLevelsNodeInfo);
		 print_r($prop_arr);
		 print_r($nodeToFindInfo);
		 print_r($sqlInfoLevel);
		 print_r($rsData);
		 echo '</textarea>';  */
		
		
	WebApp::addVar("NAVIGATION_NEM_TEMPLATE", $NEM_TEMPLATE);
	
	
	$homeCrd = $session->Vars["level_0"].",0,0,0,0";
	$homeNavigationLink = "javascript:GoTo('thisPage?event=none.ch_state(k=".$homeCrd.")');";
	IF ($global_cache_dynamic == "Y") {
		$homeNavigationLink = $cacheDyn->get_CiMainTitleOfNodeToUrl($homeCrd,$session->Vars["ln"]);
		//ECHO $node_nedded_data[$koord_level_node_key]["hrefToNodeTarget"]."-$koord_level_node<br>";
	} 	
	WebApp::addVar("homeNavigationLink", $homeNavigationLink);
    
	//WebApp::addVar("parentKey", $actualLevelKey3);
	//WebApp::addVar("childKey", $actualLevelKey4);
	//home - shildet 1
	//OLD IMPLENTATION - MBARON
	
$totals = WebApp::get_formatted_microtime() - $starts;  
   $debugTime = "".round($totals, 2)."";	
	
	WebApp::addVar("debugTimeNv", $debugTime);
}
/*	


INSERT INTO `template_list` ( `template_name`, `template_tip`, `template_box`, `document_type`, `master_template`, `is_default`, `relativePathToTemplate`) VALUES 
  ('Navigation Inside Content Promo (image, abstract...)', 'NavigationModule', 'inside_content_navigation_promo.html', 'al', 'default', 'no', NULL);


E:\projects_128\boa_internal_2017\templates\NEModules\NavigationModule\inside_content_navigation.html
		konfigurimi i propertive

		show_nodeLabel				== yes
		show_mainContentTitle		== yes
		show_mainContentAbstract	== yes
		
		show_nodeIcon				== yes
		
		show_mainContentThumbnail	== yes
		show_nodeImage				== yes
		
		
		makeLink_nodeLabel				== yes
		makeLink_nodeImage				== yes
		makeLink_mainContentTitle		== yes
		makeLink_mainContentThumbnail	== yes
		
		
		showLinkToDetails				== yes
		linkLabel
		
		
                    [0]  >= Array
                        (
                            [keyIden]  >= 0_10_1_0_0
                            [level]  >= 2
                            [nr_of_childs]  >= 5
                        )
                        
                        
            [0_10_4_0_0]  >= Array
                (
                    [0]  >= Array
                        (
                            [keyIden]  >= 0_10_4_1_0
                        )

                    [1]  >= Array
                        (
                            [keyIden]  >= 0_10_4_2_0
                        )

                    [2]  >= Array
                        (
                            [keyIden]  >= 0_10_4_3_0
                        )

                )




		Grida e atributeve per nje node	GridAuthorNavData_{{keyIden}}
		
		
                    [NodeDescription]  >= Rregullore
                    [CiDescription]  >= 
                    [boostrap_class]  >= 
                    [boostrap_ico]  >= 
                    [imageSm_id_node]  >= 0
                    [imageSm_id]  >= 0
                    [imageBg_id]  >= 0
                    [title]  >= Document Title 6887 1
                    [id_zeroNivel]  >= 0
                    [id_firstNivel]  >= 10
                    [id_secondNivel]  >= 4
                    [id_thirdNivel]  >= 1
                    [id_fourthNivel]  >= 0		
		
		
		
		
		
		
		
*/