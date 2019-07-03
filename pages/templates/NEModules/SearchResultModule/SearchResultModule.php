<?
require_once(INCLUDE_PATH.'intServices/Internal.SearchResult.Module.class.php');
/*	global $session,$event;
	$LP = new InternalSearchResultModule("union");


	global $global_cache_dynamic,$cacheDyn;
	$hrefSearchResult = "";
	IF ($global_cache_dynamic == "Y") {
		$hrefSearchResult = $cacheDyn->get_CiTitleToUrl($session->Vars["contentId"], SUBSTR($session->Vars["lang"], -1));
	}	   
	WebApp::addVar("hrefSearchResultNext",$hrefSearchResult);
	WebApp::addVar("tid","");
}*/




function SearchResultModule_onRender() {
	global $session,$event;

	//POPULLIMI I TAB-eve
	$getTabData	= array();
	$indexTab 	= -1;
	//ITEM PROPERTIES
	$indexTab++;


	WebApp::addVar("termSearch","");


	$s_id_var = "";
	if (isset($_GET["tid"])) {
		$s_id_var = $_GET["tid"];
	} 
	if (isset($event->args["tid"])) {
		$s_id_var = $event->args["tid"];
	} 
	
	global $global_cache_dynamic,$cacheDyn;
	$hrefSearchResult = "";
	IF ($global_cache_dynamic == "Y") {
		$hrefSearchResult = $cacheDyn->get_CiTitleToUrl($session->Vars["contentId"], SUBSTR($session->Vars["lang"], -1));
	}	       

	$imageDefaultForNode = "0";		
	if ($s_id_var=="") {
		$getTabData["data"][$indexTab]["tab_current"] 	= " selected txt-bold bold";
		
		$getTabData["data"][$indexTab]["tab_link_server"] = "javascript:void(0);";
	} else {
		$getTabData["data"][$indexTab]["tab_current"] 	= "";
		$getTabData["data"][$indexTab]["tab_link_server"] = "javaScript:GoTo('thisPage?event=none.srm(k={{k}};kc={{kc}};msv={{msvSrc}};tid=;)')";
		
		IF ($global_cache_dynamic == "Y") {
			$getTabData["data"][$indexTab]["tab_link_server"] = $hrefSearchResult."?msv={{msvSrc}}";
		}			
		
	}
	$getTabData["data"][$indexTab]["imageSm_id"] = "0";
	$getTabData["data"][$indexTab]["tab_description"] = "{{_All_search_results}}";
	$getTabData["data"][$indexTab]["tab_inc_clients_data"] = "<Include SRC=\"{{NEMODULES_PATH}}SearchResultModule/template_default.html\">";
	$getTabData["data"][$indexTab]["inc_data_web_box_include"] = "yes";
	$getTabData["data"][$indexTab]["tab_koord"] = $session->Vars["level_0"].",0,0,0,0";
	$getTabData["data"][$indexTab]["indexTab"] = "0";
	
	

//tid	do dergohet si parameter
//sns	selectedNodes
//osns	selectedNodes
	$koordinates_to_restrict_search = "";
	$all_nodes["koord"] = array();
	$all_nodes["img"] = array();
	

	$prop_arr_t = unserialize(base64_decode(WebApp::findNemProp($session->Vars["idstemp"])));
	
	
/*
    [showSubNodes] => show
    [selectedNodes] => Array
        (
            [0] => 
            [1] => 0,21,0,0,0
            [2] => 0,22,0,0,0
            [3] => 0,23,0,0,0
            [4] => 0,24,0,0,0
        )

    [otherSelectedNodes] => show
    [OtherSelectedNodes] => Array
        (
            [0] => 
        )
*/	
	

	
	
        
      IF ($session->Vars["thisMode"] == "_new")
         {$kusht_aktiv_joaktiv = "";}
      ELSE
         {$kusht_aktiv_joaktiv = " AND n.active".$session->Vars["lang"]." != 1 ";}


		$all_nodes_from_same_nivel = array();
		if (isset($prop_arr_t["selectedNodes"]) && is_array($prop_arr_t["selectedNodes"]) && count($prop_arr_t["selectedNodes"])>0) {
			$all_nodes_from_same_nivel = $prop_arr_t["selectedNodes"];
		} elseif (isset($prop_arr_t["selectedNodes"]) && !is_array($prop_arr_t["selectedNodes"]) && $prop_arr_t["selectedNodes"]!="") {
			$all_nodes_from_same_nivel = explode("-",$prop_arr_t["selectedNodes"]);
		}
		
		if (count($all_nodes_from_same_nivel) >0) {
			
			
			$all_nodes_from_same_nivel = array_diff($all_nodes_from_same_nivel, array('undefined','0',''));
			
			$inNodesParam = "'".implode("','",$all_nodes_from_same_nivel)."'";
			$selectNodeGroupedInfo = "
				SELECT description".$session->Vars["lang"].$session->Vars["thisMode"]." as sel_node_descriptions, 
						IF(imageSm_id >0, imageSm_id, '') as imageSm_id,
						IF(imageSm_id >0, coalesce(imageSm_id_name,''), '') as imageSm_id_name,
						
				concat(n.id_zeroNivel,',',n.id_firstNivel,',',n.id_secondNivel,',',n.id_thirdNivel,',',n.id_fourthNivel) as ke
				  FROM nivel_4 as n
				  JOIN profil_rights as p 
				  	ON n.id_zeroNivel   = p.id_zeroNivel   AND
                       n.id_firstNivel  = p.id_firstNivel  AND
                       n.id_secondNivel = p.id_secondNivel AND
                       n.id_thirdNivel  = p.id_thirdNivel  AND
                       n.id_fourthNivel = p.id_fourthNivel 
				 WHERE concat(n.id_zeroNivel,',',n.id_firstNivel,',',n.id_secondNivel,',',n.id_thirdNivel,',',n.id_fourthNivel) in (".$inNodesParam.")
				   AND p.profil_id      IN (".$session->Vars["tip"].") 
				   
                   AND n.state".$session->Vars["lang"]." != 7  
                   AND COALESCE(n.description".$session->Vars["lang"].$session->Vars["thisMode"].", '') != '' 
				   
				   ".$kusht_aktiv_joaktiv."
				   
				  GROUP BY n.id_zeroNivel,n.id_firstNivel,n.id_secondNivel,n.id_thirdNivel,n.id_fourthNivel
				  ORDER BY n.id_zeroNivel,n.id_firstNivel,n.id_secondNivel,n.id_thirdNivel,n.id_fourthNivel, sel_node_descriptions 
				  
			";
			
			$rsselectNodeGroupedInfo = WebApp::execQuery($selectNodeGroupedInfo);
			while (!$rsselectNodeGroupedInfo->EOF()) {

				$sel_node_descriptions = $rsselectNodeGroupedInfo->Field("sel_node_descriptions");
				$imageSm_id = $rsselectNodeGroupedInfo->Field("imageSm_id");
				$imageSm_id_name = $rsselectNodeGroupedInfo->Field("imageSm_id_name");
				$ke = $rsselectNodeGroupedInfo->Field("ke");
					
				$all_nodes["koord"][$ke]=$ke;
					
				$indexTab++;
				$getTabData["data"][$indexTab]["indexTab"] = $indexTab;
				$getTabData["data"][$indexTab]["tab_link_server"] = "javaScript:GoTo('thisPage?event=none.srm(k={{k}};kc={{kc}};msv={{msvSrc}};tid=".$indexTab.")')";
				
				if ($s_id_var!="" && $s_id_var== $indexTab) {
					$koordinates_to_restrict_search = $ke;
					
					$getTabData["data"][$indexTab]["tab_current"] = " selected txt-bold bold";
					$getTabData["data"][$indexTab]["tab_link_server"] = "javascript:void(0);";
					WebApp::addVar("sns","$ke");

					if ($imageSm_id>0 && $imageSm_id_name!="") {
						$imageDefaultForNode = $imageSm_id;	
						$imageDefaultForNodeName = $imageSm_id_name;	
					}						

				} else {
					$getTabData["data"][$indexTab]["tab_current"] = "";
					IF ($global_cache_dynamic == "Y") {
						$getTabData["data"][$indexTab]["tab_link_server"] = $hrefSearchResult."?tid=".$indexTab."&msv={{msvSrc}}";
					}				
				}

				$getTabData["data"][$indexTab]["tab_koord"] = $ke;
				$getTabData["data"][$indexTab]["imageSm_id"] = "".$imageSm_id."";

				if ($imageSm_id>0) {
					$getTabData["data"][$indexTab]["imageSm_Src"] = APP_URL."show_image.php?file_id=".$imageSm_id;
					if ($global_cache_dynamic == "Y") {
						$imageToDisplay = $cacheDyn->get_SlDocTitleToUrl($imageSm_id, $imageSm_id_name);
					}					
					$all_nodes["img"][$ke]=$imageSm_id;
				}

				$getTabData["data"][$indexTab]["tab_description"] = $sel_node_descriptions;
				$getTabData["data"][$indexTab]["tab_inc_clients_data"] = "<Include SRC=\"{{NEMODULES_PATH}}SearchResultModule/empty.html\">";
				$getTabData["data"][$indexTab]["inc_data_web_box_include"] = "no";			

				$rsselectNodeGroupedInfo->MoveNext();			
			}			
		}
		
		$all_nodes_from_same_nivel = array();
		if (isset($prop_arr_t["otherSelectedNodes"]) && is_array($prop_arr_t["otherSelectedNodes"]) && count($prop_arr_t["otherSelectedNodes"])>0) {
			$all_nodes_from_same_nivel = $prop_arr_t["otherSelectedNodes"];
		} elseif (isset($prop_arr_t["otherSelectedNodes"]) && !is_array($prop_arr_t["otherSelectedNodes"]) && $prop_arr_t["otherSelectedNodes"]!="") {
			$all_nodes_from_same_nivel = explode("-",$prop_arr_t["otherSelectedNodes"]);
		}
		
			
		if (count($all_nodes_from_same_nivel) >0) {
			
			$all_nodes_from_same_nivel = array_diff($all_nodes_from_same_nivel, array('undefined','0',''));
			
			$inNodesParam = "'".implode("','",$all_nodes_from_same_nivel)."'";
			
			$selectNodeGroupedInfo = "
				SELECT description".$session->Vars["lang"].$session->Vars["thisMode"]." as sel_node_descriptions,
						IF(imageSm_id >0, imageSm_id, '') as imageSm_id,
						IF(imageSm_id >0, coalesce(imageSm_id_name,''), '') as imageSm_id_name,

				concat(n.id_zeroNivel,',',n.id_firstNivel,',',n.id_secondNivel,',',n.id_thirdNivel,',',n.id_fourthNivel) as ke
				  FROM nivel_4 as n
				  JOIN profil_rights as p 
				  	ON n.id_zeroNivel   = p.id_zeroNivel   AND
                       n.id_firstNivel  = p.id_firstNivel  AND
                       n.id_secondNivel = p.id_secondNivel AND
                       n.id_thirdNivel  = p.id_thirdNivel  AND
                       n.id_fourthNivel = p.id_fourthNivel 
				 WHERE concat(n.id_zeroNivel,',',n.id_firstNivel,',',n.id_secondNivel,',',n.id_thirdNivel,',',n.id_fourthNivel) in (".$inNodesParam.")
				   AND p.profil_id      IN (".$session->Vars["tip"].") 
                   AND n.state".$session->Vars["lang"]." != 7  
                   AND COALESCE(n.description".$session->Vars["lang"].$session->Vars["thisMode"].", '') != '' 
				   
				   ".$kusht_aktiv_joaktiv."
				  GROUP BY n.id_zeroNivel,n.id_firstNivel,n.id_secondNivel,n.id_thirdNivel,n.id_fourthNivel
				  ORDER BY n.id_zeroNivel,n.id_firstNivel,n.id_secondNivel,n.id_thirdNivel,n.id_fourthNivel, sel_node_descriptions 
				   
			";			
			
			$rsselectNodeGroupedInfo = WebApp::execQuery($selectNodeGroupedInfo);
			while (!$rsselectNodeGroupedInfo->EOF()) {

				$sel_node_descriptions = $rsselectNodeGroupedInfo->Field("sel_node_descriptions");
				$imageSm_id = $rsselectNodeGroupedInfo->Field("imageSm_id");
				$imageSm_id_name = $rsselectNodeGroupedInfo->Field("imageSm_id_name");
				$ke = $rsselectNodeGroupedInfo->Field("ke");
					
				$all_nodes["koord"][$ke]=$ke;
					
					$indexTab++;
					
					$getTabData["data"][$indexTab]["indexTab"] = $indexTab;
					$getTabData["data"][$indexTab]["tab_link_server"] = "javaScript:GoTo('thisPage?event=none.srm(k={{k}};kc={{kc}};msv={{msvSrc}};tid=".$indexTab.")')";
					
					if ($s_id_var!="" && $s_id_var== $indexTab) {
						$koordinates_to_restrict_search = $ke;
												
						
						$getTabData["data"][$indexTab]["tab_current"] = " selected txt-bold bold";
						$getTabData["data"][$indexTab]["tab_link_server"] = "javascript:void(0);";
						WebApp::addVar("sns","$ke");
						
						if ($imageSm_id>0 && $imageSm_id_name!="") {
							$imageDefaultForNode = $imageSm_id;	
							$imageDefaultForNodeName = $imageSm_id_name;	
						}					
						
					} else {
						$getTabData["data"][$indexTab]["tab_current"] = "";
						IF ($global_cache_dynamic == "Y") {
							$getTabData["data"][$indexTab]["tab_link_server"] = $hrefSearchResult."?tid=".$indexTab."&msv={{msvSrc}}";
						}	  					
					}
					
					$getTabData["data"][$indexTab]["tab_koord"] = $ke;

					$getTabData["data"][$indexTab]["imageSm_id"] = "".$imageSm_id."";
					
					
					//$imageSm_id_name
					if ($imageSm_id>0) {
						$getTabData["data"][$indexTab]["imageSm_Src"] = APP_URL."show_image.php?file_id=".$imageSm_id;
						if ($global_cache_dynamic == "Y") {
							$imageToDisplay = $cacheDyn->get_SlDocTitleToUrl($imageSm_id, $imageSm_id_name);
						}
						$all_nodes["img"][$ke]=$imageSm_id;
					}
					
					$getTabData["data"][$indexTab]["tab_description"] = $sel_node_descriptions;
					$getTabData["data"][$indexTab]["tab_inc_clients_data"] = "<Include SRC=\"{{NEMODULES_PATH}}SearchResultModule/empty.html\">";
					$getTabData["data"][$indexTab]["inc_data_web_box_include"] = "no";			

				$rsselectNodeGroupedInfo->MoveNext();			
			}				
		}
//	}

	
	
	
	//$startTime = microtime();
	$LP = new InternalSearchResultModule("union",$koordinates_to_restrict_search,$all_nodes);
	
	//$endTime = microtime();
	//echo $LP->returnMiliSeconds($startTime,$endTime,"-KOMPLET KLASA-union--<br><br>");

	WebApp::addVar("gridPageSrcNr",$LP->gridPageSrcNr);
	while (list($indexTab,$tabsData)=each($getTabData["data"])) {
		//$restrict_search_to_nodes = explode(",",$valK);
		$tab_koord = $tabsData["tab_koord"];

		if (isset($LP->find_result_to_nodes["cnt"][$tab_koord])) {
			$getTabData["data"][$indexTab]["tab_result"] = "".$LP->find_result_to_nodes["cnt"][$tab_koord]."";
			if ($LP->find_result_to_nodes["cnt"][$tab_koord]=="0")
				$getTabData["data"][$indexTab]["tab_link_server"] = "javascript:void(0);";
		} 
	}
/*
	var $error_code = "5";
	var $error_code_description	= array(1=>"FoundResults",2=>"ResultsEmtpy",3=>"TermSearchEmpty",4=>"InvalidTerm",5=>"SomeErrorOccurred");
*/

	WebApp::addVar("error_code","".$LP->error_code."");
	$getTabData["AllRecs"] = count($getTabData["data"]);
	WebApp::addVar("searchTab",$getTabData);
	
	if ($imageDefaultForNode>0) {
		WebApp::addVar("dpNode_image","yes");
		WebApp::addVar("dpNode_image_src", APP_URL."show_image.php?file_id=".$imageDefaultForNode);
		
		if ($global_cache_dynamic == "Y") {
			$imageToDisplay = $cacheDyn->get_SlDocTitleToUrl($imageDefaultForNode, $imageDefaultForNodeName);
			WebApp::addVar("dpNode_image_src", $imageToDisplay);
		}		
		
	}

	WebApp::addVar("tid","$s_id_var");
	WebApp::addVar("hrefSearchResultNext",$hrefSearchResult);
	WebApp::addVar("global_cache_dynamic",$global_cache_dynamic);

		//	$getTabData["data"][$indexTab]["tab_link_server"] = $hrefSearchResult."?msv={{msvSrc}}";
		
	//echo "<textarea>";
	//print_r($getTabData);
	//echo "</textarea>";
}