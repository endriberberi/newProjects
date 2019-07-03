<?
require_once(INC_PATH.'collector.Data.List.Ext.class.php');
function ListItemCollector_onRender() {
	global $session,$event, $global_cache_dynamic,$cacheDyn;

//	$starts = WebApp::get_formatted_microtime();

	$termSearch	= "";
	WebApp::addVar("backToListIcon","");
	$statusNem = '0';	//0-list of searchin with term(, 1- abstract, nqse abstracti do trajtohet nga ky nem )
	$targeted_page = "";

	$ILC_x = new collectorDataListExtClass();
	$ILC_x->InitClass($session->Vars["idstemp"]);
	$ILC_x->ConstructDataList();
	//$ILC_x->controlPersonalizationConfiguration();

	 /*echo'<textarea>';
	 	print_r($ILC_x);
	 echo'</textarea>';*/

	WebApp::addVar("typeOfCollector",$ILC_x->NEM_PROP->typeOfCollector);
	WebApp::addVar("include_default","<Include SRC=\"{{NEMODULES_PATH}}ListItemCollector/".$ILC_x->templateFileName."\"/>");

		$show_header_pageLengthControl = "no";
		$show_footer_pageLengthControl = "no";
	if(isset($ILC_x->NEM_PROP->show_header_page_length_controll) && $ILC_x->NEM_PROP->show_header_page_length_controll != "no"){
		$show_header_pageLengthControl = "yes";
		WebApp::addVar("show_header_pageLengthControl","yes");		
	}
	if(isset($ILC_x->NEM_PROP->show_footer_page_length_controll) && $ILC_x->NEM_PROP->show_footer_page_length_controll != "no"){
		$show_footer_pageLengthControl = "yes";
		WebApp::addVar("show_footer_pageLengthControl","yes");
	}

	if($show_header_pageLengthControl == "yes" || $show_footer_pageLengthControl == "yes" ){

		if(isset($ILC_x->NEM_PROP->nr_of_items) && $ILC_x->NEM_PROP->nr_of_items >0)
			$nr_of_items = $ILC_x->NEM_PROP->nr_of_items;

		$itemsPerPage = array();
		$itemsPerPage[10] = "10";
		$itemsPerPage[20] = "20";
		$itemsPerPage[50] = "50";
		$itemsPerPage[100] = "100";

		if(isset($ILC_x->NEM_PROP->nr_of_items) && $ILC_x->NEM_PROP->nr_of_items >0){
			$nr_of_items = $ILC_x->NEM_PROP->nr_of_items;
			$itemsPerPage[$nr_of_items] = "$nr_of_items";
		}

		ksort($itemsPerPage);

		$ind=0;
		$itemsPerPageGrid = array("AllRecs"=>"", "data"=>array());
		while(list($key,$val)=each($itemsPerPage)){
			$itemsPerPageGrid["data"][$ind]["id"] = "".$key;
			$itemsPerPageGrid["data"][$ind]["label"] = $val;
			$itemsPerPageGrid["data"][$ind]["sel"] = "";
			if($key == $nr_of_items){

				$itemsPerPageGrid["data"][$ind]["sel"] = "selected";
			}

			$ind++;
		}
		IF (count($itemsPerPageGrid["data"])>0) 
			$itemsPerPageGrid["AllRecs"] = count($itemsPerPageGrid["data"]);		
		WebApp::addVar("itemsPerPageGrid",$itemsPerPageGrid);	
	}



	
	/*echo $ILC_x->templateFileName.":templateFileName";*/
/*
	nodeImageId srcNodeImageToDisplay nodeClass nodeIco
*/	
/*	
	echo $ILC_x->inicializeCollectorProp["typeOfCollector"].":typeOfCollector";
	
if ($ILC_x->inicializeCollectorProp["typeOfCollector"]=="HRC" || $ILC_x->inicializeCollectorProp["typeOfCollector"]=="") {
	echo $ILC_x->inicializeCollectorProp["typeOfCollector"].":typeOfCollector";
echo $ILC_x->templateFileName.":templateFileName-";
		echo "<textarea> GRIDA";
		print_r($ILC_x);
		echo "</textarea>";
}	
	*/
	
	
//$totals = WebApp::get_formatted_microtime() - $starts;
//echo $ILC_x->templateFileName.":templateFileName-";
//echo $debugTime = "<br>inside ListItemCollector END->".round($totals, 2).":totals";
	

	/*if (isset($ILC_x->inicializeCollectorProp)) {
		WebApp::addVar("iconDependingOfConfiguration","yes");
		if (isset($ILC_x->inicializeCollectorProp["how_to_collect"])) 
			WebApp::addVar("how_to_collect",$ILC_x->inicializeCollectorProp["how_to_collect"]);
		if (isset($ILC_x->inicializeCollectorProp["user_preferences"])) 
			WebApp::addVar("user_preferences",$ILC_x->inicializeCollectorProp["user_preferences"]);
		
	} else {
		WebApp::addVar("iconDependingOfConfiguration","no");
	}*/
	


	/*WebApp::addVar("HeaderNavT","no");
	if (isset($ILC_x->templateHeaderNav) && $ILC_x->templateHeaderNav>=0) {
		WebApp::addVar("HeaderNavT","yes");
		if ($ILC_x->templateHeaderNav>0 && $ILC_x->templateHeaderFileName!="")
			WebApp::addVar("include_HeaderNav","<Include SRC=\"{{NEMODULES_PATH}}ListItemCollector/".$ILC_x->templateHeaderFileName."\"/>");
		else
			WebApp::addVar("include_HeaderNav","<Include SRC=\"{{NEMODULES_PATH}}ListItemCollector/navigation_default.html\"/>");
	}
	
	WebApp::addVar("FooterNavT","no");
	if (isset($ILC_x->templateFooterNav) && $ILC_x->templateFooterNav>=0) {
		WebApp::addVar("FooterNavT","yes");
		if ($ILC_x->templateFooterNav>0 && $ILC_x->templateFooterFileName!="")
		WebApp::addVar("include_FooterNav","<Include SRC=\"{{NEMODULES_PATH}}ListItemCollector/".$ILC_x->templateFooterFileName."\"/>");
		else
		WebApp::addVar("include_FooterNav","<Include SRC=\"{{NEMODULES_PATH}}ListItemCollector/navigation_default.html\"/>");
	}
	
	WebApp::addVar("SearchT","no");
	if (isset($ILC_x->templateSearchType) && $ILC_x->templateSearchType>=0) {
		WebApp::addVar("SearchT","yes");
	}*/
	
	
		/*echo $ILC_x->templateFileName."<textarea>";
		//print_r($ILC_x->display_details);
		print_r($ILC_x->NEM_PROP);
		//print_r($ILC_x->display_kw_atr);
		//print_r($ILC_x);
		echo "</textarea>";*/
	

	

	
	//DEGUB GRID ECHO

		/*
		".$ILC_x->inicializeCollectorProp["typeOfCollector"].":typeOfCollector<br>
		<textarea> GRIDA";
		print_r($ILC_x->sqlCount);
		print_r($ILC_x->getdata);
		print_r($ILC_x->gridDataSrc);
		print_r($ILC_x->NEM_PROP);
		//print_r($ILC_x);
		echo "</textarea>";*/
	
	/*if ($ILC_x->inicializeCollectorProp["typeOfCollector"]=="CE") {
	}*/
	
	

		/*echo $ILC_x->templateFileName.":templateFileName<br>
		".$ILC_x->inicializeCollectorProp["typeOfCollector"].":typeOfCollector<br>
		<textarea> GRIDA PROPERTIES";
		
		print_r($ILC_x->extras_display);
		print_r($ILC_x->extras_display_labels);
		
		print_r($ILC_x->asset_display);
		print_r($ILC_x->label_to_display);
					
			
		print_r($ILC_x->gridDataSrc);
		echo "</textarea>";*/
		
		
		/*echo $ILC_x->templateFileName.":templateFileName<br>
		".$ILC_x->inicializeCollectorProp["typeOfCollector"].":typeOfCollector<br>
		<textarea> GRIDA";
		print_r($ILC_x->gridDataSrc);
		print_r($ILC_x->NEM_PROP);
		//print_r($ILC_x);
		echo "</textarea>";*/


/*if ($ILC_x->inicializeCollectorProp["typeOfCollector"]=="CE") {
		echo $ILC_x->templateFileName.":templateFileName<br>
		".$ILC_x->inicializeCollectorProp["typeOfCollector"].":typeOfCollector<br>
		<textarea> GRIDA";
		print_r($ILC_x->gridDataSrc);
		print_r($ILC_x->NEM_PROP);
		//print_r($ILC_x);
		echo "</textarea>";
}	*/
	

	

/*	
	$generalMessages = array();
	$generalMessages["slogan_description_show"]		= "no";
	$generalMessages["slogan_description"] 			= "";
	$generalMessages["slogan_icon_show"] 			= "no";
	$generalMessages["slogan_icon"] 				= "";
	$generalMessages["help_show"] 					= "no";
	$generalMessages["help_title"] 					= "";
	$generalMessages["help_description"] 			= "";
	

	$generalMessages["block_link_show"] 			= "no";
	$generalMessages["block_link_label"] 			= "";
	$generalMessages["block_link_target"] 			= "";
	$generalMessages["block_link_url"] 			= "";
	
	$prop_arr = $ILC_x->arrayConf;
	if (isset($prop_arr["slogan_description_show"]) && $prop_arr["slogan_description_show"] == "yes" 
		&& isset($prop_arr["slogan_description"]) && $prop_arr["slogan_description"] != "") {
		$generalMessages["slogan_description_show"] = $prop_arr["slogan_description_show"];
		$generalMessages["slogan_description"] = $prop_arr["slogan_description"];
	}
	

	if (isset($prop_arr["slogan_icon_show"]) && $prop_arr["slogan_icon_show"] == "yes" 
		&& isset($prop_arr["slogan_icon"]) && $prop_arr["slogan_icon"] != "") {
		$generalMessages["slogan_icon_show"] = $prop_arr["slogan_icon_show"];
		$generalMessages["slogan_icon"] = $prop_arr["slogan_icon"];
	}	

	
	
	if (isset($prop_arr["help_show"]) && $prop_arr["help_show"] == "yes" 
		&& isset($prop_arr["help_title"]) && $prop_arr["help_title"] != ""
		&& isset($prop_arr["help_description"]) && $prop_arr["help_description"] != ""
		) {
		$generalMessages["help_show"] 			= $prop_arr["help_show"];
		$generalMessages["help_title"] 			= $prop_arr["help_title"];
		$generalMessages["help_description"] = $prop_arr["help_description"];
	}	

	if (isset($prop_arr["block_link_show"]) && $prop_arr["block_link_show"] == "yes" 
		&& isset($prop_arr["block_link_label"]) && $prop_arr["block_link_label"] != ""
		&& isset($prop_arr["block_link_target"]) && $prop_arr["block_link_target"] != ""
		) {
		
		
		if (isset($prop_arr["block_link_target"]) && $prop_arr["block_link_target"]!="") {
				$target_page_succes_ci = str_replace("k=","",$prop_arr["block_link_target"]);
				$block_link_targetHref = "javascript:GoTo('thisPage?event=none.ch_state(k=".$target_page_succes_ci.")')";
				$generalMessages["block_link_show"] 			= $prop_arr["block_link_show"];
				$generalMessages["block_link_label"] 			= $prop_arr["block_link_label"];
				$generalMessages["block_link_target"] 			= $prop_arr["block_link_target"];
				//get_CiTitleToUrl get_CiMainTitleOfNodeToUrl
				IF ($global_cache_dynamic == "Y") 
					$block_link_targetHref = $cacheDyn->get_CiTitleToUrl($target_page_succes_ci, $session->Vars["ln"]);
				
			//	echo $target_page_succes_ci.":target_page_succes_ci";
			//	echo $block_link_targetHref.":block_link_targetHref";
			
				$generalMessages["block_link_target_Href"]		= $block_link_targetHref;
		}

		
	}	
	
	reset($generalMessages);
	while (list($key,$value)=each($generalMessages)) {
			WebApp::addGlobalVar("$key", "$value");
	}	
	
*/


	/*if ($ILC_x->inicializeCollectorProp["typeOfCollector"]=="NC") {
		echo "<textarea>";
	//	print_r($prop_arr["block_link_url"].":block_link_url");
	//	print_r($prop_arr);
		print_r($ILC_x->generalMessages);
//		print_r($ILC_x->arrayConf["block_link_target"]);
//		print_r($ILC_x->arrayConf);
		echo "</textarea>";
	}*/


	
	
	
	
	


		$ILC_x->filter_by_kw_structure = array();
		if (isset($ILC_x->filter_by_kw) && count($ILC_x->filter_by_kw)>0) {
			$KwObjFilterKW = new KwManagerFamily($session->Vars["ses_userid"],$session->Vars["lang"]);
			$FamilyDataSourceArray = $KwObjFilterKW->getAllFamilyData();
			if (count($FamilyDataSourceArray)>0) { //nxirret lista nje dimensionale per cdo familje qe eshte zgjedhur per filtrim
				while (list($grID,$infoGrArr)=each($FamilyDataSourceArray)) {
						if (count($infoGrArr["ids"])>0) {
						while (list($idFamily,$descriptionFamily)=each($infoGrArr["ids"])) {
								
								if (in_array($idFamily,$ILC_x->filter_by_kw)) {
									$KwObjFilterKW->setTreePositionProperties("0,".$idFamily);

									if ($KwObjFilterKW->family_type_id==1) {
										
										$KwObjItemSo = $KwObjFilterKW->setKwObjItem($KwObjFilterKW->family_type_id);
										
										$KwObjItemSo->setTreePositionProperties("0,".$idFamily);
										$dataItem = $KwObjItemSo->generateList();
																				
										if (count($dataItem)>0) {
											if (isset($ILC_x->filterby_kw_labels[$idFamily]) && $ILC_x->filterby_kw_labels[$idFamily]!="")
												$ILC_x->filter_by_kw_structure[$idFamily]["desc"] = $ILC_x->filterby_kw_labels[$idFamily];
											else
												$ILC_x->filter_by_kw_structure[$idFamily]["desc"] = $descriptionFamily;
												
											if (isset($ILC_x->kw_selection[$idFamily]) && $ILC_x->kw_selection[$idFamily]!="")
												$ILC_x->filter_by_kw_structure[$idFamily]["type_selection"] = $ILC_x->kw_selection[$idFamily];
											else
												$ILC_x->filter_by_kw_structure[$idFamily]["type_selection"] = "single";
												
											$ILC_x->filter_by_kw_structure[$idFamily]["members"] = $dataItem;
										} 
									}
								} 
						}}
				}
			}		
		}
		
		$gridFilterByKeyowrds = array(
			"data" 			=>  array(),
			"AllRecs" 		=> "0"
		);			
		$indBBKW = 0;	
		if (count($ILC_x->filter_by_kw_structure)>0) {
			while (list($kwKey,$kwVal)=each($ILC_x->filter_by_kw_structure)) {
				
				$gridFilterByKeyowrds["data"][$indBBKW]["kw_fam_id_filter"] = $kwKey;
				$gridFilterByKeyowrds["data"][$indBBKW]["kw_fam_descr_filter"] = $kwVal["desc"];
				$gridFilterByKeyowrds["data"][$indBBKW]["kw_fam_type"] = $kwVal["type_selection"];
				$gridFilterByKeyowrds["data"][$indBBKW]["kw_fam_type_inc"] = "<Include SRC=\"{{NEMODULES_PATH}}li_collector/".$kwVal["type_selection"]."_selection_grid.html";
				
				$gridFilterByKeyowrdsMembers = array(
					"data" 			=>  array(),
					"AllRecs" 		=> "0"
				);				
				
				$indBBKWM = 0;
				while (list($kwKeyM,$kwValM)=each($kwVal["members"])) {
					$kdd = explode(",",$kwKeyM);
					if (isset($ILC_x->filterByCategorization[$kwKey][$kdd[1]])) {
						$idsCisCategorized = array(
							"data" 			=>  array(),
							"AllRecs" 		=> "0"
						);
						While (list($ccid,$valu)=each($ILC_x->filterByCategorization[$kwKey][$kdd[1]])) {
							$idsCisCategorized["data"][]["ciExtendedID"] = $ccid;
						}
						IF (count($idsCisCategorized["data"])>0) 
							$idsCisCategorized["AllRecs"] = count($idsCisCategorized["data"]);
						WebApp::addVar("gridDataSrcUncategorized_".$kdd[0]."_".$kdd[1],$idsCisCategorized);
				
						$gridFilterByKeyowrdsMembers["data"][$indBBKWM]["kwKey"] = $kdd[0];
						$gridFilterByKeyowrdsMembers["data"][$indBBKWM]["kwID"] = $kdd[1];
						$gridFilterByKeyowrdsMembers["data"][$indBBKWM]["kw_id"] = $kwKeyM;
						$gridFilterByKeyowrdsMembers["data"][$indBBKWM]["kw_descr"] = $kwValM;
						$indBBKWM++;
					}
				}
				IF (count($gridFilterByKeyowrdsMembers["data"])>0) 
					$gridFilterByKeyowrdsMembers["AllRecs"] = count($gridFilterByKeyowrdsMembers["data"]);
				WebApp::addVar("gridFilterByKeywords_".$kwKey,$gridFilterByKeyowrdsMembers);	
				$indBBKW++;
			}
		}
			
		IF (count($gridFilterByKeyowrds["data"])>0) 
			$gridFilterByKeyowrds["AllRecs"] = count($gridFilterByKeyowrds["data"]);
		WebApp::addVar("grDFilterByKeyowrds",$gridFilterByKeyowrds);	 		
	
		$idsCisUnCategorized = array(
			"data" 			=>  array(),
			"AllRecs" 		=> "0"
		);

		if (isset($ILC_x->idsCisUnCategorized) && count($ILC_x->idsCisUnCategorized)>0) {
			While (list($ccid,$valu)=each($ILC_x->idsCisUnCategorized)) {
				$idsCisUnCategorized["data"][]["ciExtendedID"] = $ccid;
			}
		}
		IF (count($idsCisUnCategorized["data"])>0) 
			$idsCisUnCategorized["AllRecs"] = count($idsCisUnCategorized["data"]);
		WebApp::addVar("gridDataSrcUncategorized",$idsCisUnCategorized);	




		/*if ($session->Vars["EccEmode"] == "Review") {
			echo $session->Vars["EccEmode"].":EccEmode<br>";
			echo $session->Vars["thisMode"].":THISMODE<br>";
		}*/

}







/*

<pre>



Configuring: EXTRA Properties FOR HRI ITEMS


	Condition Variables And Labels From Nem Configuration

    	
    	{{display_generalities}}=='yes'	=>	{{extrasLabels_generalities_title}}
    	
			display_firstname			=>	{{extrasLabels_firstname}}
		    display_middlename			=>	{{extrasLabels_middlename}}
			display_lastname			=>	{{extrasLabels_lastname}}
		    display_birthdate			=>	{{extrasLabels_birthdate}}
		    display_nationality			=>	{{extrasLabels_nationality}}
		    display_prefixtitle			=>	{{extrasLabels_prefixtitle}}
			
    	{{display_proffesional}}=='yes'	=>	{{extrasLabels_proffesional_title}}
    	
			display_academictitle		=>	{{extrasLabels_academictitle}}
		    display_qualificationtitle	=>	{{extrasLabels_qualificationtitle}}
			display_organogram			=>	{{extrasLabels_organogram}}
		    display_jobtitle			=>	{{extrasLabels_jobtitle}}
		    display_docAtached			=>	{{extrasLabels_docAtached}}

    	{{display_contacttext}}=='yes'	=>	{{extrasLabels_contact_title}}
    	
			display_contact				=>	{{extrasLabels_contacttext}}
			display_contacttel			=>	{{extrasLabels_contacttel}}
		    display_contactfax			=>	{{extrasLabels_contactfax}}
		    display_contactemail		=>	{{extrasLabels_contactemail}}

 

	Condition Variables And Labels Recordset
 	

			docAtached - CV

				[docAtached] => 1035
				[dp_docAtached] => yes
				[asset_fileid] => 1035
				[asset_filename] => Argomed_Web_service_test_template_1035.doc
				[asset_fileurl] => http://192.168.1.114/ham/rc/doc/Argomed_Web_service_test_template_1035.doc
				[asset_icotype] => word
				[asset_mimetype] => application/msword
				[asset_filesize] => 766.00 KB
				[asset_duration] => 
				[asset_dimension] => 
				[ico_type] => word
				[extension] => doc
				[link_url] => http://192.168.1.114/ham/rc/doc/Argomed_Web_service_test_template_1035.doc
				[stream_url] => http://192.168.1.114/ham/rc/doc/Argomed_Web_service_test_template_1035.doc
				[allowed_download] => yes
				[allowed_preview] => no

				<Grid gridId="DocCachedInfo_{{asset_fileid}}">

				</Grid>      

					
            
			[generalitiesBlockExist] => yes
				[firstname] => Jon.Ida
				[dp_firstname] => yes
				[middlename] => J.
				[dp_middlename] => yes
				[lastname] => Cu.Ko
				[dp_lastname] => yes

				[prefixtitle] => Frau
				[dp_prefixtitle] => yes

				[nationality] => albanisch
				[dp_nationality] => yes
				[dp_nationality_id] => yes

				[birthdate] => 08.09.2000
				[dp_birthdate] => yes
				[dp_street] => yes
				[dp_zip] => yes
				[dp_location] => yes
				[dp_country] => yes             
                
			[proffesionalBlockExist] => yes
				[jobtitle] => job title text
				[dp_jobtitle] => yes

				[proffesionaltitle] => Andere
				[dp_proffesionaltitle] => yes


			[addressBlockExist] => yes
				[addressname] => adress info text
				[street] => street 256
				[zip] => 1000
				[location] => ort:tirane
				[country] => country:Albania
				[latitude] => 
				[longitude] => 

			[dp_addressname] => yes

				[contactBlockExist] => yes            
				[contacttext] => contact info text
				[contacttel] => 11111111
				[contactfax] => 4444
				[contactemail] => asdf@sadf.sdf
				[dp_contacttext] => yes
				[dp_contacttel] => yes
				[dp_contactfax] => yes
				[dp_contactemail] => yes
            



Configuring: Main Configuring: General Properties

	check to control atributes that are configured to be displayed

		DC_thumbnail_display		=>	{{DC_thumbnail_display}}
		DC_abstract_display			=>	{{DC_abstract_display}}
		DC_date_display				=>	{{DC_date_display}}
		DC_time_display				=>	{{DC_time_display}}
		DC_source_display			=>	{{DC_source_display}}
		DC_sourceauthor_display	=>	{{DC_sourceauthor_display}}
		DC_content_display			=>	{{DC_content_display}}

	label for atributes
		_DC_datetime_label	=>	{{_DC_datetime_label}}
		_DC_source_label	=>	{{_DC_source_label}}
		_DC_sourceauthor_label	=>	{{_DC_sourceauthor_label}}



Configuring: Main document attached id e filet attach eshte te var asset_id=>{{asset_id}}	[ecc_doc_id]

	atributet e filet attach kapen me griden e meposhtem DocCachedInfo_{{asset_id}}
	<Grid gridId="DocCachedInfo_{{asset_id}}">
		te gjitha atributet dhe propertite me poshte jane te lidhura me griden qe jep infomarcionin per documentin attach
	</Grid>

	check to control atributes that are configured to be displayed

		DA_icotype_display	=>	{{DA_icotype_display}}

		DA_filename_display	=>	{{DA_filename_display}}
		DA_mimetype_display	=>	{{DA_mimetype_display}}
		DA_filesize_display	=>	{{DA_filesize_display}}
		DA_duration_display	=>	{{DA_duration_display}}
		DA_dimension_display	=>	{{DA_dimension_display}}

	label for atributes
		_DA_filename_label	=>	{{_DA_filename_label}}
		_DA_mimetype_label	=>	{{_DA_mimetype_label}}
		_DA_filesize_label	=>	{{_DA_filesize_label}}
		_DA_duration_label	=>	{{_DA_duration_label}}
		_DA_dimension_label	=>	{{_DA_dimension_label}}


	check to control icons

		display_download	=>	{{display_download}}
		display_info		=>	{{display_info}}
		display_preview		=>	{{display_preview}}
		display_favorit		=>	{{display_favorit}}

		_DA_download_iconlabel	=>	{{_DA_download_iconlabel}}
		_DA_finfo_iconlabel		=>	{{_DA_info_iconlabel}}
		_DA_preview_iconlabel	=>	{{_DA_preview_iconlabel}}



Configuring: Extra properties - EVENT ITEM



	flage to control interelation - te gjitha atributet e eventit
	
            [event_timing_text] => 10.06.2017 bis 11.06.2017
            [eventStartDate] => 10.06.2017
            [eventEndDate] => 
            [eventStartTime] => 09:00
            [eventEndTime] => 12:00
            
			[flagToCntPeriod] => [noEndDate|sameDate|diffdate]

            
            [addressBlockExist] => yes
            
            [address_name] => 
            [street] => Hofmattstrasse 9
            [dp_street] => yes
            [zip] => 5223 
            [dp_zip] => yes
            [location] => Riniken
            [dp_location] => yes
            [country] => Schweiz
            [dp_country] => yes
            
            [addressKoordExist] => yes
            [latitudeEvent] => 47.4969225000
            [dp_latitudeEvent] => yes
            [longitudeEvent] => 8.1888468000
            [dp_longitudeEvent] => yes
            
            [ContactBlockExist] => yes
            [organizational_contact] => 
            [eventTel] => 0692070726
            [dp_eventTel] => yes
            [eventFax] => 0692070726
            [dp_eventFax] => yes
            [eventEmail] => albanruci@gmail.com
            [dp_eventEmail] => yes



	check to control atributes that are configured to be displayed

		display_eventStartDate	=>	{{display_eventStartDate}}
		display_eventEndDate	=>	{{display_eventEndDate}}
		display_eventTime		=>	{{display_eventTime}}
		eventPeriodText			=>	{{eventPeriodText}}
		

		display_address			=>	{{display_address}}
		display_country			=>	{{display_country}}
		
		
		
		
		display_organizational_contact		=>	{{display_organizational_contact}}
		display_eventTel		=>	{{display_eventTel}}
		display_eventFax		=>	{{display_eventFax}}
		display_eventEmail		=>	{{display_eventEmail}}

	label for atributes

		extrasLabels_dateblock		=>	{{extrasLabels_dateblock}}
		extrasLabels_locationblock	=>	{{extrasLabels_locationblock}}
		extrasLabels_eventTel		=>	{{extrasLabels_eventTel}}
		extrasLabels_eventFax		=>	{{extrasLabels_eventFax}}
		extrasLabels_eventEmail		=>	{{extrasLabels_eventEmail}}
		extrasLabels_contactblock	=>	{{extrasLabels_contactblock}}


</pre>




    <If condition="'{{dp_labelUserDefinedLink}}' == 'yes'">
        <a href="{{hrefToDoc}}" class="btn btn-primary btn-sm legitRipple" role="button" tabindex="0">{{labelUserDefinedLink}}</a>
    </If>
    <If condition="'{{dp_labelUserDefinedDocAttachedLink}}' == 'yes'">
		<Grid gridId="DocCachedInfo_{{asset_id}}">
		<a title="{{titleToAlt}}" data-title="{{titleToAlt}}" target="_blank"
			class="line-clamping view-{{ico_type}} item-title" data-key="{{identifier_type}}"
			data-width="{{dt_width}}" data-height="{{dt_height}}" data-id="{{CID_REF}}"
			data-url="{{identifier_key}}" href="{{stream_url}}" target="_blank">
			{{labelUserDefinedDocAttachedLink}}
		</a>
		</Grid>
	</If>

<Grid gridId="gridKwBlockToFilter_{{fid}}">
			<Header>
			<div class="dropdown">
				<button aria-expanded="false" aria-haspopup="true" class="btn dropdown-toggle dropdownMenuButton" data-toggle="dropdown" id="dropdownMenuButton_{{fid}}" type="button">
					<span class="filterName">{{fname}}
						<span class="fa chevron"></span>
					</span>
				</button>
				<div aria-labelledby="dropdownMenuButton_{{fid}}" class="dropdown-menu filterDD">
			</Header>

						<Var name="filterSel">('{{filterIsActive}}'=='yes' == 'active in' ? '' : '')</Var>
						<a class="dropdown-item {{filterSel}}"
							<If condition="'{{global_cache_dynamic}}'=='Y'">
								 href="{{hrefListNav}}?fkwid={{fkid}}_{{kid}}&ser={{objId}}&msv={{msvLst}}"
							</If>
							<If condition="'{{global_cache_dynamic}}'!='Y'">
								  href="JavaScript:GoTo('thisPage?event=none.srm(k={{k}};kc={{kc}};msv={{msvLst}};ser={{objId}};fkwid={{fid}}_{{kid}})')"
							</If>
						data-key="{{fid}}_{{kid}}" ><i class="fa fa-caret-right color-green"
							aria-hidden="true" style="padding-left: 0"></i>
							<!--{{kwfullpath}}-->
							<!--{{kwname}}-->
							<!--{{kwname}} ({{kwparentpath}})-->
								{{kwname}} ({{nrOfCiRelated}})
							<If condition="'{{filterIsActive}}'=='yes'">
								FILTERED BY
							</If>
							</a>
			<Footer>
				</div>
			</div>
			</Footer>
			</Grid>

*/

?>