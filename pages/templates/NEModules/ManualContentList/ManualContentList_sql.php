<?
function getCiPropertyData($categKey="",$itemKey="",$itemId="", $configuration_by_type, $prop_arr=array(), $kwConfig=array()){
	global $session;

		$profileRightCondition = "";
		$profilRightsJoin = "";


		if($configuration_by_type == "userAccessRights"){
			$profileRightCondition = " AND p.profil_id IN (".$session->Vars["tip"].")";
			$profilRightsJoin = " JOIN profil_rights as p ON (    c.id_zeroNivel   = p.id_zeroNivel
												AND c.id_firstNivel  = p.id_firstNivel
												AND c.id_secondNivel = p.id_secondNivel
												AND c.id_thirdNivel  = p.id_thirdNivel
												AND c.id_fourthNivel = p.id_fourthNivel
											) ";
		}

		$filterToSqlArray = array();
		if ($session->Vars["thisMode"]=='') {
			$filterToSqlArray[] =" c.state".$session->Vars["lang"]." not in (0,5,7)";
			$filterToSqlArray[] =" c.published".$session->Vars["lang"]." = 'Y'";
		//	$filterToSqlArray[] =" n.active".$this->lang." != '1' ";
			$filterToSqlArray[] =" n.state".$session->Vars["lang"]." != 7 ";
		} else {
			$filterToSqlArray[] =" c.state".$session->Vars["lang"]." not in (7)";
		}

		$filterToSql = "";
		if(count($filterToSqlArray)>0){
			$filterToSql = "AND ".implode(" AND ", $filterToSqlArray);
		}

		$nodeBootstrapFields = "";
		if(defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP=="Y") 
			$nodeBootstrapFields = "coalesce(boostrap_class,'') as boostrap_class, coalesce(boostrap_ico,'') as boostrap_ico,";

		$getData = "SELECT
					c.content_id  as content_id,
					c.id_zeroNivel, c.id_firstNivel, c.id_secondNivel, c.id_thirdNivel, c.id_fourthNivel,
					n.orderMenu as nodeOrder,
					c.templateId  as templateId,

					title".$session->Vars["lang"]." as title, 
					filename".$session->Vars["lang"]." AS filename,
					c.ci_type,
						
					COALESCE(c.with_https,      'n') as with_https,

					IF (coalesce(date_approve".$session->Vars["lang"].",'') !='' AND date_approve".$session->Vars["lang"]."!='0000-00-00',DATE_FORMAT(date_approve".$session->Vars["lang"].",'%d.%m.%Y')  ,'') 
					as date_approve,

					IF (coalesce(dateModified,'') !=''	 AND dateModified!='0000-00-00',DATE_FORMAT(dateModified,'%d.%m.%Y')  ,'') 	as dateModified,

					IF (coalesce(creation_date,'') !=''	 AND creation_date!='0000-00-00',DATE_FORMAT(creation_date,'%d.%m.%Y')  ,'') as creation_date, 

					IF (coalesce(scheduling_from,'') !='' AND scheduling_from!='0000-00-00',DATE_FORMAT(scheduling_from,'%d.%m.%Y')  ,'') as scheduling_from,	            	

					IF (coalesce(scheduling_to,'') !='' AND scheduling_to!='0000-00-00',DATE_FORMAT(scheduling_to,'%d.%m.%Y')  ,'') as scheduling_to,	 
					

					IF (coalesce(source_creation_date,'') !=''  ,DATE_FORMAT(source_creation_date,'%d.%m.%Y')  ,'') as source_creation_date,	 

					IF (coalesce(source_creation_date,'') !=''  ,DATE_FORMAT(source_creation_date,'%H:%i')  ,'') as source_creation_time,	 
				
				
					IF (coalesce(source_creation_date,'') !=''  ,DATE_FORMAT(source_creation_date,'%h:%i %P')  ,'') as source_creation_time_12,	
					
					
					IF (coalesce(source_creation_date,'') !=''  ,DATE_FORMAT(source_creation_date,'%d.%m.%Y.%H.%h.%i.%s.%p')  ,'') 	as source_creation_time_array,		
					
					IF (DATE_FORMAT(source_creation_date,'%d.%m.%Y')=DATE_FORMAT(now(),'%d.%m.%Y'), 'yes','no') as source_creation_is_today,
					
					IF((source_author".$session->Vars["lang"]." IS NULL    OR source_author".$session->Vars["lang"]." = ''),'', source_author".$session->Vars["lang"].")  as source_author,


					IF((c.description".$session->Vars["lang"].$session->Vars["thisMode"]." IS NULL  OR c.description".$session->Vars["lang"].$session->Vars["thisMode"]." = ''),'', c.description".$session->Vars["lang"].$session->Vars["thisMode"].")
						as description,

					IF((doc_source".$session->Vars["lang"]." IS NULL 	OR doc_source".$session->Vars["lang"]." = ''),'', doc_source".$session->Vars["lang"].") as source,

					IF((c.imageSm_id >0),c.imageSm_id,'') as imageSm_id,coalesce(c.imageSm_id_name, '') as imageSm_id_name,
						
					IF((c.imageSm_id_mob >0),c.imageSm_id_mob,'') as imageSm_id_mob, coalesce(c.imageSm_id_mob_name, '') as imageSm_id_mob_name,
						
					IF((c.imageBg_id >0),c.imageBg_id,'') as imageBg_id, coalesce(c.imageBg_id_name, '') as imageBg_id_name,
										
					IF((c.imageBg_id_mob >0),c.imageBg_id_mob,'') as imageBg_id_mob, coalesce(c.imageBg_id_mob_name, '') as imageBg_id_mob_name,
			
					coalesce(content".$session->Vars["lang"].$session->Vars["thisMode"].",'') as ci_content,
						
					COALESCE(n.imageSm_id,      '') as imageSm_id_node, 				
						".$nodeBootstrapFields."						
						
					orderContent


				FROM content AS c
				JOIN	nivel_4			AS n	ON (    c.id_zeroNivel   = n.id_zeroNivel
													AND c.id_firstNivel  = n.id_firstNivel
													AND c.id_secondNivel = n.id_secondNivel
													AND c.id_thirdNivel  = n.id_thirdNivel
													AND c.id_fourthNivel = n.id_fourthNivel
												)
				".$profilRightsJoin."

				WHERE c.content_id = '".$itemId."' 
					".$filterToSql."	
					".$profileRightCondition."				
				";

	$rsGetdata = WebApp::execQuery($getData);
	$ciProp = array(); $indA=0;
	if(!$rsGetdata->EOF()){
		$content_id = $rsGetdata->Field("content_id");

		$ciProp[$indA]["CID"]		= $content_id;
		$ciProp[$indA]["n0"] 		= $rsGetdata->Field("id_zeroNivel");
		$ciProp[$indA]["n1"] 		= $rsGetdata->Field("id_firstNivel");
		$ciProp[$indA]["n2"] 		= $rsGetdata->Field("id_secondNivel");
		$ciProp[$indA]["n3"] 		= $rsGetdata->Field("id_thirdNivel");
		$ciProp[$indA]["n4"] 		= $rsGetdata->Field("id_fourthNivel");
		
		$ciProp[$indA]["ci_type"] 	= $rsGetdata->Field("ci_type");
		$ciProp[$indA]["zone_id"] 	= $rsGetdata->Field("id_zeroNivel");

		$ciProp[$indA]["ciOrderContent"] 	= $rsGetdata->Field("orderContent");
		$ciProp[$indA]["templateId"] 		= $rsGetdata->Field("templateId");

			

		$ciProp[$indA]["manualCiLabel"] 	= "";
		$ciProp[$indA]["hasCiLabel"] 		= "no";
		if(isset($prop_arr["ci_label_name"][$categKey][$itemKey]) && $prop_arr["ci_label_name"][$categKey][$itemKey] !=""){
			$ciProp[$indA]["hasCiLabel"] 	= "yes";
			$ciProp[$indA]["manualCiLabel"] = $prop_arr["ci_label_name"][$categKey][$itemKey];
		}

		$ciProp[$indA]["showManualCiIcon"] = "no";
		if(isset($prop_arr["icon_show"][$categKey][$itemKey]) && $prop_arr["icon_show"][$categKey][$itemKey] !=""){
			$ciProp[$indA]["showManualCiIcon"] = $prop_arr["icon_show"][$categKey][$itemKey];
		}

		$ciProp[$indA]["manualCiIcon"] = "";
		if(isset($prop_arr["CI_icon"][$categKey][$itemKey]) && $prop_arr["CI_icon"][$categKey][$itemKey] !=""){
			$ciProp[$indA]["manualCiIcon"] = $prop_arr["CI_icon"][$categKey][$itemKey];
		}

		$ciProp[$indA]["manualCiImage"]		= "";
		$ciProp[$indA]["manualCiImageScr"] 	= "";
		if(isset($prop_arr["CI_Image"][$categKey][$itemKey]) && $prop_arr["CI_Image"][$categKey][$itemKey] >0){
			$manualCiImageId = $prop_arr["CI_Image"][$categKey][$itemKey];
			$ciProp[$indA]["manualCiImage"] = $manualCiImageId;
			$ciProp[$indA]["manualCiImageScr"] 	= APP_URL."show_image.php?file_id=".$manualCiImageId;
		}


		$targetedNode = "GoTo";
		if(isset($prop_arr["targetedNode"][$categKey][$itemKey]) && $prop_arr["targetedNode"][$categKey][$itemKey] !=""){
			$targetedNode = $prop_arr["targetedNode"][$categKey][$itemKey];
		}

		$modalParams = "";	
		$modalSize = "md";
		if($targetedNode == "Modal"){
			$modalSize = "md";
			if(isset($prop_arr["modalSize"][$categKey][$itemKey]) && $prop_arr["modalSize"][$categKey][$itemKey] !=""){
				$modalSize = $prop_arr["modalSize"][$categKey][$itemKey];
			}
			$hrefTo 	= "javascript:void(0);";
			$modalParams = ' data-bs-modal="true" data-modal-size="modal-'.$modalSize.'" data-modal-ajax="true" data-url="&apprcss=userProfileCall&cis='.$content_id.'#calleUserFunction" ';	
		}elseif($targetedNode == "CallIn"){
			$hrefTo = "javascript:GoTo('thisPage?event=none.ch_state(k=".$content_id.";kc={{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}};)');";
		}elseif($targetedNode == "GoTo"){
			$hrefTo = "javascript:GoTo('thisPage?event=none.ch_state(k=".$content_id.")');";
		}

		$ciProp[$indA]["hrefTo"] 		= $hrefTo;	
		$ciProp[$indA]["modalParams"] 	= $modalParams;	


		//TITLE
		$titleToDisplay 			= $rsGetdata->Field("title");
		$titleToAlt 				= str_replace("\"","&quot;",$titleToDisplay);
		$titleToAlt					= str_replace("\r\n", " ", $titleToAlt);

		$ciProp[$indA]["titleToAlt"] 		= $titleToAlt;
		$ciProp[$indA]["titleToDisplay"] 	= $titleToDisplay;	


		//EW_ABSTRACT
		$ciProp[$indA]["abstractToDisplay"] 		= $rsGetdata->Field("description");
		if (trim(strip_tags($ciProp[$indA]["abstractToDisplay"]))!="")  { // 
			$ciProp[$indA]["dp_abst"] = "yes";
		} else $ciProp[$indA]["dp_abst"] = "no";
		
		$find_content = $rsGetdata->Field("ci_content");
		$find_content = strip_tags($find_content,'span');
		if (trim($find_content)!='') {
			$ciProp[$indA]["dp_content"] 		= "yes";		
		} else {
			$ciProp[$indA]["dp_content"] 		= "no";		
		}
		

		//data e dokumentit doublin core
		//source_creation_date source_creation_time_12 source_creation_time
		
		$source_creation_date 			= $rsGetdata->Field("source_creation_date");
		$source_creation_time 			= $rsGetdata->Field("source_creation_time");
		
		$source_creation_time_12 		= $rsGetdata->Field("source_creation_time_12");
		$source_creation_time_array 	= $rsGetdata->Field("source_creation_time_array");
		$source_creation_is_today 		= $rsGetdata->Field("source_creation_is_today");
			
		$ciProp[$indA]["dp_docDate"] = "no";	
		$ciProp[$indA]["dp_docTime"] = "no";
		$ciProp[$indA]["source_creation_date"] = "$source_creation_date";
		$ciProp[$indA]["source_creation_time"] = "$source_creation_time";

		if ($ciProp[$indA]["source_creation_time"]=="00:00") {
			$ciProp[$indA]["source_creation_time"] 	= "";
		} else {
			$ciProp[$indA]["dp_docTime"] 	= "yes";
		}

		if ($source_creation_time!="" && $source_creation_time!="00:00") {//00:00:00	
			$ciProp[$indA]["dp_docTime"] = "yes";
		}
		if ($source_creation_date!="") {
			$ciProp[$indA]["dp_docDate"] = "yes";
		}
		//SCHEDULING FROM  SCHEDULING TO
		$scheduling_from 	= $rsGetdata->Field("scheduling_from");		
		$scheduling_to 		= $rsGetdata->Field("scheduling_to");
		
		$ciProp[$indA]["scheduling_to"] 		= $scheduling_to;
		$ciProp[$indA]["scheduling_from"] 	= $scheduling_from;

		//SOURCE
		$ciProp[$indA]["sourceToDisplay"]	= $rsGetdata->Field("source");
		$ciProp[$indA]["dp_source"] = "no";
		if (trim($ciProp[$indA]["sourceToDisplay"])!="") {
			$ciProp[$indA]["dp_source"] = "yes";
		}

		//SOURCE AUTHOR
		$ciProp[$indA]["dp_author"] 		= "no";
		$ciProp[$indA]["AuthorToDisplay"]  = $rsGetdata->Field("source_author");
		if (trim($ciProp[$indA]["AuthorToDisplay"])!="") {
			$ciProp[$indA]["dp_author"] = "yes";
		}

		$ciProp[$indA]["dp_image"] 				= "no";
		$ciProp[$indA]["dp_Bigimage"] 			= "no";
	//	$ciProp[indA]["linkToimage"] 			= "no";
		$ciProp[$indA]["srcImageToDisplay"] 		= "";

		$imageSm_id = $rsGetdata->Field("imageSm_id");
		$imageBg_id = $rsGetdata->Field("imageBg_id");
		$imageSm_id_name = $rsGetdata->Field("imageSm_id_name");
		$imageBg_id_name = $rsGetdata->Field("imageBg_id_name");

		$ciProp[$indA]["imageSm_id"] = "$imageSm_id";
		$ciProp[$indA]["imageBg_id"] = "$imageBg_id";	

		$imgID = "0";
		$imgIDName = "";
		if ($imageSm_id!="" && $imageSm_id>"0") {
			$ciProp[$indA]["dp_image"] 			= "yes";
			$imgID = $imageSm_id;
			$imgIDName = $imageSm_id_name;
			
			if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") {
				//thir griden
				$prop = CiManagerFe::get_SL_CACHE_INDEX($imageSm_id);	
				if (isset($prop["link_url"]) && $prop["link_url"]!='')
					$ciProp[$indA]["srcSmallImageToDisplay"] = $prop["link_url"];
				else {
					$ciProp[$indA]["srcSmallImageToDisplay"] = APP_URL."show_image.php?file_id=".$imageSm_id;
				}
			}				
		} 
		if ($imageBg_id!="" && $imageBg_id>"0") {
	
			if ($ciProp[$indA]["dp_image"]!='yes') {
				$ciProp[$indA]["dp_image"] 			= "yes";
				$imgID 		= $imageBg_id;
				$imgIDName 	= $imageBg_id_name;
			}
			if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") {
				//thir griden
				$prop = CiManagerFe::get_SL_CACHE_INDEX($imageBg_id);	
				//print_r($prop);
				if (isset($prop["link_url"]) && $prop["link_url"]!='')
					$ciProp[$indA]["srcBigImageToDisplay"] = $prop["link_url"];
				else {
					$ciProp[$indA]["srcBigImageToDisplay"] = APP_URL."show_image.php?file_id=".$imageBg_id;
				}
			}				
		}

		$ciProp[$indA]["nodeImageId"] = $rsGetdata->Field("imageSm_id_node");
		if(defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP=="Y") {
			$ciProp[$indA]["nodeClass"] = $rsGetdata->Field("boostrap_class");
			$ciProp[$indA]["nodeIco"] = $rsGetdata->Field("boostrap_ico");
		}

		if ($ciProp[$indA]["nodeImageId"] >0) {
			if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") {
				//thir griden
				$prop = CiManagerFe::get_SL_CACHE_INDEX($ciProp[$indA]["nodeImageId"]);	
				//print_r($prop);
				if (isset($prop["link_url"]) && $prop["link_url"]!='')
					$ciProp[$indA]["srcNodeImageToDisplay"] = $prop["link_url"];
				else {
					$ciProp[$indA]["srcNodeImageToDisplay"] = APP_URL."show_image.php?file_id=".$ciProp[$indA]["nodeImageId"];
				}
			}	
			
			if ($imgID>0) {
			
			} else $imgID = $ciProp[$indA]["nodeImageId"];
		}



		if ($imgID >0) {
			//thumbnail
			$ciProp[$indA]["dp_image_ID"] 			= "$imgID";
			if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") {
				//thir griden
				$prop = CiManagerFe::get_SL_CACHE_INDEX($imgID);	
				if (isset($prop["link_url"]) && $prop["link_url"]!='')
					$ciProp[$indA]["srcImageToDisplay"] = $prop["link_url"];
				else {
					$ciProp[$indA]["srcImageToDisplay"] = APP_URL."show_image.php?file_id=".$imgID;
				}
				
				
			} else {				
				$ciProp[indA]["srcImageToDisplay"] = APP_URL."show_image.php?file_id=".$imgID;
				IF ($global_cache_dynamic == "Y") {
					$ciProp[$indA]["srcImageToDisplay"] = $cacheDyn->get_SlDocTitleToUrl($imgID, $imgIDName);
				} elseif ($application_is_cached=="yes") {

				}	
			}
			//if (isset($this->display_link_in["image"])) 
			//	$ciProp[indA]["linkToimage"] 			= "yes";			
		}/*		$indA++;
		$rsGetdata->MoveNext();*/

		if(count($kwConfig)>0){
			getKwsRelatedToCiToPublish($content_id,$kwConfig);
		}
	}

	return $ciProp;

}


function getKwsRelatedToCiToPublish($ciId="",$kwConfig=array()){	
		global $session;
		$kw_display 		= array();
		$kw_display_labels  = array();
		$kw_display_type  = array();

		if(isset($kwConfig["kw_display"]) && count($kwConfig["kw_display"])>0)
			$kw_display = $kwConfig["kw_display"];

		if(isset($kwConfig["kw_display_labels"]) && count($kwConfig["kw_display_labels"])>0)
			$kw_display_labels = $kwConfig["kw_display_labels"];

		if(isset($kwConfig["kw_display_type"]) && count($kwConfig["kw_display_type"])>0)
			$kw_display_type = $kwConfig["kw_display_type"];

		if ($session->Vars["thisMode"]=="_new")    $thisModeCode = 0;
        else										$thisModeCode = 1;	

		//echo "\n->getKwsRelatedToCiToPublish\n";
		$mainCisKeywordsRelatedItem = array();
		$KeywordsRelatedItemInfo = array();
		$KeywordsRelatedItemInfoDetailed = array();
		if (isset($kw_display) && count($kw_display)>0) {
		
			$idsFamilyRelated = implode(",",$kw_display);
			
			
			$mainCisKeywordsRelatedItem = array();
			$getKwCi = "SELECT content_id, family_id, kw_id as id, family_id FROM kw_ci_relations 
						 WHERE content_id in (".$ciId.") 
						   AND family_id in (".$idsFamilyRelated.") 
						   AND lng_id = '".$session->Vars["ln"]."' 
						   AND statusInfo = '".$thisModeCode."'";		
			$rsKwCi = WebApp::execQuery($getKwCi);
			
			while (!$rsKwCi->EOF()) {
				$content_id = $rsKwCi->Field("content_id");
				$family_id = $rsKwCi->Field("family_id");
				$id = $rsKwCi->Field("id");
				$KeywordsRelatedItemInfo["Item"][$family_id][$id] = $id;
				$KeywordsRelatedItemInfo["family"][$family_id] = $family_id;
				
				$mainCisKeywordsRelatedItem[$content_id][$family_id][$id] = "$family_id,$id";
				$rsKwCi->MoveNext();
			}	
			
			if (count($KeywordsRelatedItemInfo["family"])>0) {
			
				$idsFamilyRelated = implode(",",$KeywordsRelatedItemInfo["family"]);
				$getKwFamily="SELECT coalesce(description".$session->Vars["lang"].",name) as description, family_id, family_type_id,  table_name
								FROM kw_family
							   WHERE family_id in (".$idsFamilyRelated.")";
				$rsKwFamily = WebApp::execQuery($getKwFamily);
				while (!$rsKwFamily->EOF()) {

					$tableName 		= $rsKwFamily->Field("table_name");
					$family_id 		= $rsKwFamily->Field("family_id");
					$family_type_id = $rsKwFamily->Field("family_type_id");
					
					$KeywordsRelatedItemInfoDetailed["family"][$family_id]["tableName"] = $tableName;
					$KeywordsRelatedItemInfoDetailed["family"][$family_id]["family_type_id"] = $family_type_id;

					if (isset($KeywordsRelatedItemInfo["Item"][$family_id]) && count($KeywordsRelatedItemInfo["Item"][$family_id])>0) {

						$keyWordsData = array();
						$idsKWRelated = implode(",",$KeywordsRelatedItemInfo["Item"][$family_id]);

						$getKwDEsc = "SELECT description".$session->Vars["lang"]." as description , kw_id as id
									  FROM ".$tableName." 
									 WHERE kw_id in (".$idsKWRelated.") 
									 ORDER BY display_order";		

						$rsKwDEsc = WebApp::execQuery($getKwDEsc);
						while (!$rsKwDEsc->EOF()) {
							$id = $rsKwDEsc->Field("id");
							$keyWordsdescription = $rsKwDEsc->Field("description");
							$KeywordsRelatedItemInfoDetailed[$family_id][$id] = $keyWordsdescription;
							$rsKwDEsc->MoveNext();
						}	
					}
					$rsKwFamily->MoveNext();
				}				
			}
			while (list($ciExtendedID,$fam_related) =each($mainCisKeywordsRelatedItem)) {
			
				$gridDataSrcKeyowrds = array(
					"data" 			=>  array(),
					"AllRecs" 		=> "0"
				);						
				$indexGridKeywords = 0;
				$alias = $ciExtendedID;
				while (list($ci_family_rel,$kw_related) =each($fam_related)) {
				
					$tmpInfoArray["list"]["CI_DATA"][$ciExtendedID]["dp_keywords"] = "yes";
					$gridDataSrcKeyowrds["data"][$indexGridKeywords]["docKeywords"] = "";

					$gridDataSrcKeyowrds["data"][$indexGridKeywords]["dp_family_name"] = "yes";
					$gridDataSrcKeyowrds["data"][$indexGridKeywords]["family_name"] = $kw_display_labels["kwLabels_".$ci_family_rel];
		
						
					$gridDataSrcKeyowrds["data"][$indexGridKeywords]["family_id"] = $ci_family_rel;
					$gridDataSrcKeyowrdsItems = array(
						"data" 			=>  array(),
						"AllRecs" 		=> "0"
					);	
					$kky_id = 0;
					$imploded_kw_array = array();
					
					while (list($ci_kw_rel,$tttmm) =each($kw_related)) {
							$desc = $KeywordsRelatedItemInfoDetailed[$ci_family_rel][$ci_kw_rel];
							$imploded_kw_array[] = $desc;
							$gridDataSrcKeyowrdsItems["data"][$kky_id]["docKeywordsItem"]= $desc;
							$gridDataSrcKeyowrdsItems["data"][$kky_id]["docKwItemLb"]= $desc;
							$gridDataSrcKeyowrdsItems["data"][$kky_id]["docKwItemid"]= $ci_kw_rel;
							$kky_id++;
					}
					
					$gridDataSrcKeyowrdsItems["AllRecs"] = count($gridDataSrcKeyowrdsItems["data"]);
					if ($gridDataSrcKeyowrdsItems["AllRecs"] > 0) {
						WebApp::addVar("gridDataSrcKeywordsItems_".$alias."_".$ci_family_rel,$gridDataSrcKeyowrdsItems);	
					}
					if (count($imploded_kw_array)>0) {
						$gridDataSrcKeyowrds["data"][$indexGridKeywords]["docKeywords"]  = implode(", ",$imploded_kw_array);
					}

					if(isset($kw_display_type["kw_display_type_".$ci_family_rel])){
							$gridDataSrcKeyowrds["data"][$indexGridKeywords]["keywordToDisplay"] = $gridDataSrcKeyowrds["data"][$indexGridKeywords]["docKeywords"];
							$gridDataSrcKeyowrds["data"][$indexGridKeywords]["keywordToDisplayType"] = "term";
						if($kw_display_type["kw_display_type_".$ci_family_rel] == "fullpath"){
							$gridDataSrcKeyowrds["data"][$indexGridKeywords]["keywordToDisplay"] = $gridDataSrcKeyowrds["data"][$indexGridKeywords]["family_name"].": ".$gridDataSrcKeyowrds["data"][$indexGridKeywords]["docKeywords"];
							$gridDataSrcKeyowrds["data"][$indexGridKeywords]["keywordToDisplayType"] = "fullpath";
						}
					}

					$indexGridKeywords++;
				
				}
				$gridDataSrcKeyowrds["AllRecs"] = count($gridDataSrcKeyowrds["data"]);
 				//ky array do krijohet si varibel global vetem nese ka te dhena
 				if ($gridDataSrcKeyowrds["AllRecs"] > 0) {
 					WebApp::addVar("gridDataSrcKeywords_".$alias,$gridDataSrcKeyowrds);	
 				}				
			}
		}
	}

?>


