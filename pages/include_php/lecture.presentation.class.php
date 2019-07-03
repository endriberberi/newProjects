<?php
require_once(ELEARNING_USER_PLATFORM_CLASS);
//require_once LIB_PATH.'jsonwrapper/jsonwrapper.php';
require_once(GETID3_PATH);

class eLearningPresentation extends eLearningUserPlatform
{
	/*****************************************************
	*** CONSTRUCTOR OF THE CLASS ****************
	******************************************************/
	function eLearningPresentation () {
		global $session;
		parent::eLearningUserPlatform();
		
        global $session;
        $sessLANG = $session->Vars["lang"];
        if (isset($sessLANG) && $sessLANG != "") {
            //if (eregi("Lng", $sessLANG)) {        
            if (strpos($sessLANG,"Lng") !== false) {
                $lngIDCode = str_replace("Lng", "", $sessLANG) * 1;
                if (!defined("LNG" . $lngIDCode)) {
                    $lngIDCode = 1;
                } else {
                    $session->Vars["lang"] = 'Lng' . $lngIDCode;
                }
            }
        }
        $this->lang = $session->Vars["lang"];
        $this->lngId = str_replace("Lng", "", $this->lang);
        $this->thisMode = $session->Vars["thisMode"];
        $this->uniqueid = $session->Vars["uni"];
        $this->uni = $this->uniqueid;

        if (isset($session->Vars["tip"]) && $session->Vars["tip"] > 0)
            $this->tip = $session->Vars["tip"];
        if (isset($session->Vars["ses_userid"]) && $session->Vars["ses_userid"] > 0)
            $this->userSystemID = $session->Vars["ses_userid"];

      //  $this->initUserProfile();
      //  $this->initRoleUserProfile();
        
        if ($session->Vars["thisMode"]=="_new")
        		$this->thisModeCode = 0;
        else	$this->thisModeCode = 1;   		
	}
	function userPreferencesJsonData ($params) 
	{
		$MStrToJson["references"] = array();
		$RefToJson = array();
		$RefToJson["toBasket"] 				= false;
		$RefToJson["id"] 					= "";
		if (isset($params["idList"]) && count($params["idList"])>0) {
		
			$idsCis = implode(",",$params["idList"]);
			$favoritesCiData = personalization::areCisInUserFavorites($idsCis,$this->lngId,$this->userSystemID);	//$ci_id, $lang, $uid 
			if (isset($favoritesCiData) && count($favoritesCiData)>0) {
				while (list($identifierReference,$datar)=each($favoritesCiData)) {
						$tmpRef 			= $RefToJson;
						$tmpRef["id"] 		= $identifierReference;
						
						if ($datar["is_favorites"]==1)
						$tmpRef["toBasket"] = true;

						$MStrToJson["references"]["reference_".$identifierReference] = $tmpRef;
				}
			}
		}
		$jsonD = json_encode($MStrToJson);	
		header ('Content-type: application/json');
		header ('Access-Control-Allow-Origin: *');		
		print_r($jsonD);	
	}	

	function createJsonVirtualSlideData () {
		global $sl_file_nedded_data,$session;

			$MStrToJson = array();

			$MStrToJson["has_virtual_slides"] = true;
			$MStrToJson["video_id"] 			= $this->cidFlow;
			
			$mainProp = $this->mixedNeededData[$this->cidFlow]["DC"];	
			
			if (isset($mainProp["ew_title"]))				$MStrToJson["title"] 		= trim(strip_tags($mainProp["ew_title"]));
			if (isset($mainProp["ew_abstract"]))			$MStrToJson["abstract"] 	= trim(strip_tags($mainProp["ew_abstract"]));
			if (isset($mainProp["ew_source_author"]))		$MStrToJson["author"] 		= trim(strip_tags($mainProp["ew_source_author"]));

			$MStrToJson["small_img"]	= "";
			$MStrToJson["big_img"]		= "";



			if (isset($mainProp["imageSm_id"]) && $mainProp["imageSm_id"]!="" && $mainProp["imageSm_id"]>0) {
				$imageSm_id = $mainProp["imageSm_id"];
				if (isset($sl_file_nedded_data["data"][$imageSm_id]))	$MStrToJson["small_img"] = $sl_file_nedded_data["data"][$imageSm_id]["relative_url"];	
			}
			if (isset($mainProp["imageBg_id"]) && $mainProp["imageBg_id"]!="" && $mainProp["imageBg_id"]>0) {
				$imageBg_id = $mainProp["imageBg_id"];
				if (isset($sl_file_nedded_data["data"][$imageBg_id]))				$MStrToJson["big_img"] = $sl_file_nedded_data["data"][$imageBg_id]["relative_url"];	
			}			
			
			$MStrToJson["keywords"] 	= array();
			$MStrToJson["file_info"] 	= array();
			$MStrToJson["durationCalculated"] 	= 0;	
			$refKeyWords = array();
			if (isset($this->thisKwsRelatedToCi["docKeywordsArray"][$this->cidFlow]) && count($this->thisKwsRelatedToCi["docKeywordsArray"][$this->cidFlow])>0) {
			reset($this->thisKwsRelatedToCi["docKeywordsArray"][$this->cidFlow]);
			while (list($fid,$dataV)=each($this->thisKwsRelatedToCi["docKeywordsArray"][$this->cidFlow])) {
				if (($fid=="7" || $fid =="8") and count($dataV)>0) {
					$refKeyWords = array_merge($refKeyWords, $dataV);
				}
			}}
			if (count($refKeyWords)>0) {
				$MStrToJson["keywords"] = "\"".implode("\",\"",$refKeyWords)."\"";
			}


			if (isset($sl_file_nedded_data["data"][$mainProp["ecc_doc_id"]])) {
				$docInfo = $sl_file_nedded_data["data"][$mainProp["ecc_doc_id"]];

				$tmpRef["file_info"]["file_id"]			= $mainProp["ecc_doc_id"];
				$tmpRef["file_info"]["file_name"] 		= $docInfo["file_name_cached"];

				$tmpRef["file_info"]["stream_url"] 		= $docInfo["relative_stream_url"];
				$tmpRef["file_info"]["link_url"] 		= $docInfo["relative_url"];

				$tmpRef["file_info"]["file_size"] 		= $docInfo["file_size"];
				$tmpRef["file_info"]["mimetype"] 		= $docInfo["mimetype"];									
				$tmpRef["file_info"]["extension"] 		= $docInfo["extension"];	
				$tmpRef["file_info"]["icon_type"] 		= $docInfo["ico_type"];
				$durationCalculated 		= $docInfo["file_duration"];
			} 
			
			$MStrToJson["durationCalculated"] 	= $durationCalculated;
			$MStrToJson["file_info"] = $tmpRef["file_info"];
			$MStrToJson["slides"] = array();

			$SldToJson = array();
		//	$SldToJson["slide_id"] 		= "";
			$SldToJson["title"] 			= "";
			$SldToJson["abstract"] 		= "";

			$SldToJson["start_time"] 		= "";
			$SldToJson["end_time"] 		= "";

			$orderS = 1;

				$this->getGeneralDataRelated($this->cidFlow);				
			
			
				$getToc = "SELECT virtual_slide_id, virtual_slide_order,
								  coalesce(virtual_slide_text,'') as virtual_slide_text,
								  coalesce(virtual_slide_description,'') as virtual_slide_description,
							
								  coalesce(start_time,'') as start_time,
								  coalesce(end_time,'') as end_time
								  
							 FROM ci_elearning_virtual_slide 
							WHERE content_id = '".$this->cidFlow."' 
							  AND lng_id = '".$this->lngId."' 
							  AND statusInfo in (0)
						 ORDER BY virtual_slide_order";		
				
				$rs=WebApp::execQuery($getToc);	

				while(!$rs->EOF())	{					

					$virtual_slide_id				= $rs->Field("virtual_slide_id");
					$virtual_slide_order			= $rs->Field("virtual_slide_order");
					$virtual_slide_text				= $rs->Field("virtual_slide_text");
					$virtual_slide_description		= $rs->Field("virtual_slide_description");
					$start_time						= $rs->Field("start_time");
					$end_time						= $rs->Field("end_time");
				
					$tmp = $SldToJson;
					$identifierReference = $value["CID"];
					$tmp["slide_id"]	= trim(strip_tags($virtual_slide_id));
					$tmp["title"]	= trim(strip_tags($virtual_slide_text));
					$tmp["abstract"] = trim(strip_tags($virtual_slide_description));
					
					$start_time = trim(strip_tags($start_time));
					$tm = explode(":",$start_time);

					$end_time = trim(strip_tags($end_time));
					$tme = explode(":",$end_time);
					
					$tmp["start_time"] 		= $tm[0]*60+$tm[1];
					$tmp["end_time"] 		= $tme[0]*60+$tme[1];
				//	$tmp["slide_id"] 		= trim(strip_tags($virtual_slide_order));
				
					$tmp["durationCalculated"] 		= $tmp["end_time"]-$tmp["start_time"];
				
				
					$tmp["index"] 		= $orderS;

					$MStrToJson["slides"]["slide_".$orderS] = $tmp;
					$orderS++;
			
					$rs->MoveNext(); 
				}

			$jsonD = json_encode($MStrToJson);	
			//$jsonD = _json_encode($MStrToJson);
			header ('Content-type: application/json');
			header ('Access-Control-Allow-Origin: *');		

			print_r($jsonD);

	}
	function getAnnotationJson () {
		global $sl_file_nedded_data,$session;
	
		//getRelatedRaDocuments($raID,$relatedToCis)
		$approvedReferences = array();
        if (isset($this->appRelSt["LECTURE_RELATED"]["RA"]) && $this->appRelSt["LECTURE_RELATED"]["RA"] > 0) {
            $raID = $this->appRelSt["LECTURE_RELATED"]["RA"];
            $this->getRelatedRaDocuments($raID,$this->cidFlow);
  			$approvedReferences = $this->itemCollectorRelatedIds[$raID . "_" . $this->cidFlow];    
  		
        }
		$MStrToJson = array();           
		$getToc = "SELECT reference_content_id, text_html, generated_html,attributes,order_key_id
							 FROM sp_data_annotation
							WHERE content_id = '" . $this->cidFlow . "' AND lng_id = '".$this->lngId."' AND statusInfo in ('".$this->thisModeCode."')
							  AND flag_used = 'y'
							  
						 ORDER BY order_key_id";
            //$this->getToc = $getToc;
            $rs = WebApp::execQuery($getToc);
            $indOrderToJson = 0;
            while (!$rs->EOF()) {
                
                $reference_content_id		= $rs->Field("reference_content_id");
                if (in_array($reference_content_id, $approvedReferences)) {
                
					$order_key_id				= $rs->Field("order_key_id");
					$text_html					= $rs->Field("text_html");
					$generated_html				= $rs->Field("generated_html");

					$prop			= $rs->Field("attributes");
					$attributes 	= unserialize(base64_decode($prop));

					$MStrToJson[$indOrderToJson] = $attributes;
					$MStrToJson[$indOrderToJson]["text"] =  $generated_html;

					$indOrderToJson++;
				}
                $rs->MoveNext();
            }
            $ugjetFlag="no";
            if (count($MStrToJson)==0) {
					$this->getGeneralDataRelated($this->cidFlow);				
					$this->getNodeListDocuments();
				    $this->getCollectorSlideExtended();  
				  /*  echo $this->cidFlow."<textarea>";
				    print_r($this->SlidesExtended);
				    echo "</textarea>";*/
					if (isset($this->SlidesExtended[$this->cidFlow]["Annotation"]["json"])) {			
						//kontrollo nese kemi 
						$dt							= $this->SlidesExtended[$this->cidFlow]["Annotation"]["json"];
						$tmp["annotation"]["json"]	= $dt["relative_url"];
						if (file_exists(APP_PATH.$tmp["annotation"]["json"])) {
								$jsonString = file_get_contents(APP_PATH.$tmp["annotation"]["json"]);
								echo $jsonString;	
								$ugjetFlag="yes";
								return;
						}
					}	
				$jsonD = json_encode($MStrToJson);
			//	header ("Content-type: application/json; charset=".APP_ENCODING);
			//	header ('Access-Control-Allow-Origin: *');		
				print_r($jsonD);					
            } else {
				//KJO PJESA KETU DO TE ZEVENDESOHET ME NJE ARRAY BOSH											
				$jsonD = json_encode($MStrToJson);
			//	header ("Content-type: application/json; charset=".APP_ENCODING);
			//	header ('Access-Control-Allow-Origin: *');		

				print_r($jsonD);
			}

	}
	function createPresenationGeneralInfo () {
	
		global $sl_file_nedded_data,$session;

		$MStrToJson = array();
		
		$MStrToJson["has_virtual_slides"] = true;
		$MStrToJson["presentation_id"] = $this->cidFlow;
		$MStrToJson["title"] 	= "";
		$MStrToJson["abstract"] = "";
		$MStrToJson["durationCalculated"] 	= "";
		$MStrToJson["durationExpected"] 	= "";
		
		$MStrToJson["big_img"] = "";
		$MStrToJson["small_img"] = "";
		
		$MStrToJson["keywords"] = array();		
		
		$MStrToJson["author"] = array();
		$MStrToJson["author"]["name"]				= "";
		
		$MStrToJson["author"]["firstname"]			= "";
		$MStrToJson["author"]["secondname"]			= "";
		$MStrToJson["author"]["user_title"]			= "";
		
		$MStrToJson["author"]["affiliation"]		= "";
		$MStrToJson["author"]["country"]	 		= "";
		$MStrToJson["author"]["img"]	 			= "";
		$MStrToJson["author"]["additional_info"]	= "";
		$MStrToJson["author"]["id_author"]	 		= "";		
		
		$mainTmp = $this->mixedNeededData[$this->cidFlow];
		
		if (isset($mainTmp["DC"]["ew_title"]))									$MStrToJson["presentation_title"] 	= trim(strip_tags($mainTmp["DC"]["ew_title"]));
		if (isset($mainTmp["DC"]["ew_abstract"]))								$MStrToJson["abstract"] 			= trim(strip_tags($mainTmp["DC"]["ew_abstract"]));
		
		if (isset($mainTmp["DC"]["imageSm_id"]) && $mainTmp["DC"]["imageSm_id"]!="" && $mainTmp["DC"]["imageSm_id"]>0) {
			$imageSm_id = $mainTmp["DC"]["imageSm_id"];
			if (isset($sl_file_nedded_data["data"][$imageSm_id])) 				$MStrToJson["small_img"] = $sl_file_nedded_data["data"][$imageSm_id]["relative_url"];	
		}
		if (isset($mainTmp["DC"]["imageBg_id"]) && $mainTmp["DC"]["imageBg_id"]!="" && $mainTmp["DC"]["imageBg_id"]>0) {
			$imageBg_id = $mainTmp["DC"]["imageBg_id"];
			if (isset($sl_file_nedded_data["data"][$imageBg_id]))				$MStrToJson["big_img"] = $sl_file_nedded_data["data"][$imageBg_id]["relative_url"];	
		}			
			
		if (isset($mainTmp["EX"]["presentation_duration_calulated_int"]))		$MStrToJson["durationCalculated"] = $mainTmp["EX"]["presentation_duration_calulated_int"];
		if (isset($mainTmp["EX"]["presentation_duration_expected_int"])) 		$MStrToJson["durationExpected"] = $mainTmp["EX"]["presentation_duration_expected_int"];

		if (isset($this->lecturerPresentation) && count($this->lecturerPresentation)>0) {
			$lecturerPresentation = $this->lecturerPresentation;
			$MStrToJson["author"]["name"]			= $lecturerPresentation["cme_author_title"]." ".$lecturerPresentation["auth_name"]." ".$lecturerPresentation["auth_sname"];
			
			$MStrToJson["author"]["firstname"]		= $lecturerPresentation["auth_name"];
			$MStrToJson["author"]["secondname"]		= $lecturerPresentation["auth_sname"];
			$MStrToJson["author"]["user_title"]		= $lecturerPresentation["cme_author_title"];
			
			
			
			$MStrToJson["author"]["affiliation"]		= $lecturerPresentation["cme_author_inst"];
			$MStrToJson["author"]["country"]	 		= $lecturerPresentation["cme_author_country"];
			$MStrToJson["author"]["id_author"]	 		= $lecturerPresentation["id_author"];
			if (isset($lecturerPresentation["photoId"]) && $lecturerPresentation["photoId"]>0)
				$photoId    =   $lecturerPresentation["photoId"];                 
                if ($photoId!="" && $photoId>0)
					$sl_file_nedded_data["data"][$photoId] = CiManagerFe::get_SL_CACHE_INDEX($photoId);						
				if (isset($sl_file_nedded_data["data"][$photoId])) 	
					$MStrToJson["author"]["img"] = $sl_file_nedded_data["data"][$photoId]["relative_url"];
				$MStrToJson["author"]["additional_info"]	= $lecturerPresentation["add_info"];		
		}
		return $MStrToJson;
	}	
	function createJsonData () {
		
		global $sl_file_nedded_data,$session;
		//createJsonData
		$this->getStructuredInformationEcc();
		$this->getNodeListDocuments();
		$this->getPresentationLecturer();
        
		$controlForSlide = $_REQUEST["sID"];
		if (isset($_REQUEST["sID"]) && $_REQUEST["sID"]>0 && $this->apprcss == "createJsonDataSlide") {
			$controlForSlide = $_REQUEST["sID"];

		}
		
		$MStrToJson = $this->createPresenationGeneralInfo();
		$mergeKw = array();
		$this->getCiMultiOrSingleKewRelations($this->cidFlow);
		if (isset($this->thisKwsRelatedToCi["docKeywordsArray"][$this->cidFlow])
			&& count($this->thisKwsRelatedToCi["docKeywordsArray"][$this->cidFlow])>0) {
			if (isset($this->thisKwsRelatedToCi["docKeywordsArray"][$this->cidFlow][8])) {
				//$MStrToJson["keywords"] = $this->thisKwsRelatedToCi["docKeywordsArray"][$this->cidFlow][8];
				$mergeKw = array_merge($mergeKw,$this->thisKwsRelatedToCi["docKeywordsArray"][$this->cidFlow][8]);
			}
		}	
		$MStrToJson["has_virtual_slides"] = false;
		$MStrToJson["annotationsEmpty"] = false;		//true {Boolean} nese ska annotations (per te gjithe lecture-in)
		$MStrToJson["transcriptionEmpty"] = false;		//true {Boolean} nese ska transcription (per te gjithe lecture-in)
		

		
		$MStrToJson["slides"] = array();
		$SldToJson = array();
		
		$SldToJson["slide_id"] 		= "";
		$SldToJson["title"] 		= "";
		$SldToJson["abstract"] 		= "";
		$SldToJson["toc_id"] 		= "";
		$SldToJson["keywords"] 		= array();
		$SldToJson["references"] 	= array();
		
		$SldToJson["video"] 		= array();
		$SldToJson["audio"] 		= array();
		
		$SldToJson["subtitles"] 	= array();
		$SldToJson["annotation"] 	= array();
		
		$SldToJson["small_img"] 			= "";
		$SldToJson["big_img"] 				= "";		
		
		$SldToJson["durationCalculated"] 	= "";
		
		$SldToJson["durationExtra"] 		= "";
		$SldToJson["sequence_id"] 			= "";
		$SldToJson["trackable_code"] 		= "";
		
		$SldToJson["index"] =			"";
		
		$this->getCollectorSlideExtended();
		$RAID = $this->appRelSt["LECTURE_RELATED"]["RA"];
		
		$getAndMergeAllReferences 			= array();
		$saveReferencesToTraitThem 			= array();
		$saveReferencesToTraitThemAllData 	= array();
    
		$orderS = 1;
		
		/*echo "<texarea>";
		print_r($this);
		echo "</texarea>";
		
		return;*/
		
		if (isset($this->ListOFSocumentsInActualNode[$this->cidFlow]["data"]) && count($this->ListOFSocumentsInActualNode[$this->cidFlow]["data"])>0) {
		
				$slideObj = $this->ListOFSocumentsInActualNode[$this->cidFlow]["data"];
				
				reset($slideObj);
				while (list($key,$value)=each($slideObj)) {
					
					
					if ($value["ci_type"]=="SP") {
						
						if (($controlForSlide==$value["CID"] && $this->apprcss == "createJsonDataSlide") || $this->apprcss == "createJsonData") {

									$identifierReference = $value["CID"];
									$tmp = $SldToJson;

									$tmp["index"] = $orderS;
									$tmp["big_img"]		= $MStrToJson["big_img"];
									$tmp["small_img"]	= $MStrToJson["small_img"];

									$tmp["slide_id"] = $identifierReference;

									if (isset($value["ci_title"]))			$tmp["title"] 			= trim(strip_tags($value["ci_title"]));
									if (isset($value["ew_abstract"]))		$tmp["abstract"] 		= trim(strip_tags($value["ew_abstract"]));

									if (isset($this->mixedNeededData[$identifierReference]["EX"]["duration_calulated_int"])) 
										$tmp["durationCalculated"] = $this->mixedNeededData[$identifierReference]["EX"]["duration_calulated_int"];


											
								//	if ($tmp["durationExtra"]==0) $tmp["durationExtra"] = 2;

									if ($tmp["durationCalculated"]=="" || $tmp["durationCalculated"]==0)
										$tmp["durationCalculated"] = 10;
										
									if (isset($this->mixedNeededData[$identifierReference]["EX"]["duration_extra_int"])) 
											$tmp["durationExtra"] = $this->mixedNeededData[$identifierReference]["EX"]["duration_extra_int"];
									else	$tmp["durationExtra"] = 0;										
										
									//nuk duhet qe shume e kohes se slidet (real ose minumum=10) te jete me e vogel se minumumi i lejuar
									//duration extra mund te jete edhe negative
									$totDurationSum = $tmp["durationCalculated"]+$tmp["durationExtra"];
									if (($totDurationSum*1)<1) {
										$tmp["durationExtra"] = 0;		
									}
									
									
									$tmp["totDurationSum"] = $totDurationSum;	
									
									$file_duration = $tmp["durationCalculated"];
									
									
									$durationCalculatedFromState += $tmp["durationCalculated"]+ $tmp["durationExtra"];
									
									
									if (isset($this->SlidesExtended[$identifierReference]["mediaContent"]["Image"])) {			
										$dt					= $this->SlidesExtended[$identifierReference]["mediaContent"]["Image"];
										$tmp["big_img"]		= $dt["relative_url"];
									}	
									if (isset($this->SlidesExtended[$identifierReference]["smallImage"]["Image"])) {			
										$dt					= $this->SlidesExtended[$identifierReference]["smallImage"]["Image"];
										$tmp["small_img"]	= $dt["relative_url"];
									}
									if (isset($this->SlidesExtended[$identifierReference]["mediaContent"]["Audio"])) {			
										$dt								= $this->SlidesExtended[$identifierReference]["mediaContent"]["Audio"];
										$tmp["audio"]["mp3"]["type"]	= $dt["video_type"];
										$tmp["audio"]["mp3"]["url"]		= $dt["relative_url"];	
										$file_duration = $dt["file_duration"];
									}							
									if (isset($this->SlidesExtended[$identifierReference]["mediaContent"]["VideoMp4"])) {			
										$dt								= $this->SlidesExtended[$identifierReference]["mediaContent"]["VideoMp4"];
										$tmp["video"]["mp4"]["type"]	= $dt["video_type"];
										$tmp["video"]["mp4"]["url"]		= $dt["relative_url"];
										$file_duration = $dt["file_duration"];
									} 							
									
									if (isset($this->SlidesExtended[$identifierReference]["Caption"]["json"])) {			
										$dt							= $this->SlidesExtended[$identifierReference]["Caption"]["json"];
										$tmp["subtitles"]["json"]	= $dt["relative_url"];
									} else {
									 	$paths = $this->createEmptyAnnotationTransription ($file_duration,"Caption",$identifierReference,"SP");
									 	$tmp["subtitles"]["vtt"] 	= $paths["identifier_vtt"];
									 	$tmp["subtitles"]["json"] 	= $paths["identifierJson"];
									}
									if (isset($this->SlidesExtended[$identifierReference]["Caption"]["vtt"])) {			
										$dt							= $this->SlidesExtended[$identifierReference]["Caption"]["vtt"];
										$tmp["subtitles"]["vtt"]	= $dt["relative_url"];
									} else {
									 	$paths = $this->createEmptyAnnotationTransription ($file_duration,"Caption",$identifierReference,"SP");
									 	$tmp["subtitles"]["vtt"] 	= $paths["identifier_vtt"];
									}

									if (isset($this->SlidesExtended[$identifierReference]["Annotation"]["vtt"])) {			
										$dt							= $this->SlidesExtended[$identifierReference]["Annotation"]["vtt"];
										$tmp["annotation"]["vtt"]	= $dt["relative_url"];
									} else {
									 	$paths = $this->createEmptyAnnotationTransription ($file_duration,"Annotation",$identifierReference,"SP");
									 	$tmp["annotation"]["vtt"] 	= $paths["identifier_vtt"];
									 	$tmp["annotation"]["json"] 	= $paths["identifierJson"];								
									}
									
									if (isset($this->SlidesExtended[$identifierReference]["Annotation"]["json"])) {			
										//kontrollo nese kemi 
										$dt							= $this->SlidesExtended[$identifierReference]["Annotation"]["json"];
										$tmp["annotation"]["json"]	= $dt["relative_url"];
									} else {
									 	$paths = $this->createEmptyAnnotationTransription ($file_duration,"Annotation",$identifierReference,"SP");
									 	 
									 	$tmp["annotation"]["vtt"] 	= $paths["identifier_vtt"];
									 	$tmp["annotation"]["json"] 	= $paths["identifierJson"];										
									}
									
									$tmp["annotation"]["json"]	= $tmp["annotation"]["json"]."?s=1";
									//CREATE DYNAMICALLY
								//	$existAnnotation = $this->existStructuredAnnotation($identifierReference);
								//	if ($existAnnotation>0)
										$tmp["annotation"]["json"]	= "ajxDt.php?uni=".$this->uni."&apprcss=getAnnotationJson&cis=".$identifierReference."&sID=".$identifierReference."";

									$this->getCiMultiOrSingleKewRelations($identifierReference);
									if (isset($this->thisKwsRelatedToCi["docKeywordsArray"][$identifierReference])
										&& count($this->thisKwsRelatedToCi["docKeywordsArray"][$identifierReference])>0) {
										if (isset($this->thisKwsRelatedToCi["docKeywordsArray"][$identifierReference][8])) {
											$tmp["keywords"] = $this->thisKwsRelatedToCi["docKeywordsArray"][$identifierReference][8];
											$mergeKw = array_merge($mergeKw,$tmp["keywords"]);
										}
									}	

									if (isset($this->mixedNeededData[$identifierReference]["EX"]["sequence_id"])) 
										$tmp["sequence_id"] = $this->mixedNeededData[$identifierReference]["EX"]["sequence_id"];
									if (isset($this->mixedNeededData[$identifierReference]["EX"]["toc_id_slide"])) 
										$tmp["toc_id"] = $this->mixedNeededData[$identifierReference]["EX"]["toc_id_slide"];
									
									$tmp["trackable_code"] = "<script>
									EW.analytics.trigger('open', '".$identifierReference."');
									clearInterval(analyticsintervalwidg);
									if (typeof ANALYTICS_TIMER !== 'undefined') {
										var analyticsintervalwidg = setInterval(function(){
												EW.analytics.trigger('update', '".$identifierReference."');
											}, ANALYTICS_TIMER);
									}
									</script>";
									
									$slideRefencesRa = array();	
									if (isset($this->itemCollectorRelated[$RAID."_".$identifierReference]["data"]) && count($this->itemCollectorRelated[$RAID."_".$identifierReference]["data"])>0) {

										$referecesObj = $this->itemCollectorRelated[$RAID."_".$identifierReference]["data"];
										while (list($key,$value)=each($referecesObj)) {

												$identifierReferenceRa 	= $value["CID"];
												$slideRefencesRa[] 		= $value["CID"];
												
												$saveReferencesToTraitThem[$value["CID"]] = $value["CID"];
												$saveReferencesToTraitThemAllData[$value["CID"]] = $value;
												
										} //while referecesObj
									} //if (isset($this->itemCollectorRelated[$RAID.						

									if (count($slideRefencesRa)>0) {
										$tmp["references"] = $slideRefencesRa;
									}		
									$MStrToJson["slides"]["slide_".$orderS] = $tmp;
									$orderS++;
							
						} //duhet inkluduar slide
					} //while
		} //nese eksiston info per slidet
		
		$temp = $this->getReferecesToJonStructure($saveReferencesToTraitThem, $saveReferencesToTraitThemAllData,$mergeKw);
		
		$MStrToJson["references"] = $temp["references"];
		//$MStrToJson["durationCalculatedFromState"] = $durationCalculatedFromState;
		$MStrToJson["durationCalculated"] = "".$durationCalculatedFromState;

		if (isset($mergeKw) && count($mergeKw)>0)
			$MStrToJson["keywords"] = $mergeKw;
			$MStrToJson["TOC"] = array();
			if (isset($this->TOC) && count($this->TOC)>0)  {
			while (list($key,$value)=each($this->TOC)) {
				$MStrToJson["TOC"]["toc_$key"]["id"]		= $key;
				$MStrToJson["TOC"]["toc_$key"]["title"]	= strip_tags($value);
			}}
		}	

	
//$MStrToJson["references"]
		$jsonD = json_encode($MStrToJson);
		header ("Content-type: application/json; charset=".APP_ENCODING);
		header ('Access-Control-Allow-Origin: *');		
		print_r($jsonD);
	}
	function getReferecesToJonStructure($saveReferencesToTraitThem, $saveReferencesToTraitThemAllData,$mergeKw) {
		
		global $sl_file_nedded_data;
		$MStrToJson["references"] = array();
		$RefToJson = array();
		
		$RefToJson["title"] 				= "";
		$RefToJson["author"] 				= "";
		$RefToJson["ecc_reference"] 		= "";
		$RefToJson["abstract"] 				= "";
		$RefToJson["small_img"] 			= "";
		$RefToJson["small_img"] 			= "";
		$RefToJson["reference_format"] 		= "";
		$RefToJson["identifier_key"] 		= "";
		$RefToJson["identifier_type"] 		= "";
		
		$RefToJson["category"] 					= "";
		$RefToJson["CompulsoryOrRecommended"] 	= "";
		$RefToJson["keywords"] 					= array();
		
		$RefToJson["file_info"]["file_id"] 		= "";		 
		$RefToJson["file_info"]["file_name"] 	= "";
		$RefToJson["file_info"]["link_url"] 	= "";
		$RefToJson["file_info"]["stream_url"] 	= "";
		$RefToJson["file_info"]["file_size"] 	= "";
		$RefToJson["file_info"]["mimetype"] 	= "";
		$RefToJson["file_info"]["extension"] 	= "";
		$RefToJson["file_info"]["icon_type"] 	= "";
		
		$RefToJson["toBasket"] 				= false;
		$RefToJson["id"] 					= "";
		
		if (isset($saveReferencesToTraitThem) && count($saveReferencesToTraitThem)>0) {
			$docId = implode(",",$saveReferencesToTraitThem);
		
			 $sql_con = "SELECT content.content_id, coalesce(ecc_reference*1, orderContent) as ordF, ecc_reference	
						  FROM content
							JOIN ci_elearning_extended on content.content_id = ci_elearning_extended.content_id 
							 AND ci_elearning_extended.lng_id = '".$this->lngId."' AND ci_elearning_extended.statusInfo = '".$this->thisModeCode."'						  
						  
						WHERE content.content_id in (".$docId .")
					 GROUP BY content.content_id
					 ORDER BY ordF asc, ecc_reference";

			$rs = WebApp::execQuery($sql_con);	
			$itemGrid = array("data"=>array(),"AllRecs"=> "0");$ind=0;
			while(!$rs->EOF())	{	
			
					$identifierReference	= $rs->Field("content_id");
					
					$tmpRef = $RefToJson;
					$value = $saveReferencesToTraitThemAllData[$identifierReference];




					
					if (isset($value["ci_title"]))				$tmpRef["title"] 			= strip_tags($value["ci_title"]);
					if (isset($value["ecc_reference"]))			$tmpRef["ecc_reference"] 	= strip_tags($value["ecc_reference"]);
					
					if (isset($value["source_author"]))			$tmpRef["author"] 			= $value["source_author"];
					if (isset($value["source_author"]))			$tmpRef["author"] 			= $value["source_author"];
					if (isset($value["description"]))			$tmpRef["abstract"] 		= trim(strip_tags($value["description"]));	
					
					if (isset($value["imageSm_id"]))			$tmpRef["small_img"] 		= $value["imageSm_id"];	
					
					if (isset($value["reference_format"]))		$tmpRef["reference_format"] 		= $value["reference_format"];	
					if (isset($value["identifier_key"]))		$tmpRef["identifier_key"] 			= $value["identifier_key"];	
					if (isset($value["identifier_type"]))		$tmpRef["identifier_type"] 			= $value["identifier_type"];	
					if (isset($value["category_Label"]))		$tmpRef["category"] 				= $value["category_Label"];	
					if (isset($value["category_Label_rel"]))	$tmpRef["CompulsoryOrRecommended"] 	= $value["category_Label_rel"];	

					if (isset($value["allowed_preview"]))		$tmpRef["allowed_preview"] 			= $value["allowed_preview"];	
					if (isset($value["allowed_download"]))		$tmpRef["allowed_download"] 		= $value["allowed_download"];	
					
					if (isset($value["CID_REF"]))				$tmpRef["ref_id"] 		= $value["CID_REF"];	
					else										$tmpRef["ref_id"] 		= $identifierReference;	
					

				
					if (isset($sl_file_nedded_data["data"][$value["ecc_doc_id"]])) {
						$docInfo = $sl_file_nedded_data["data"][$value["ecc_doc_id"]];

						$tmpRef["file_info"]["file_id"]			= $value["ecc_doc_id"];
						$tmpRef["file_info"]["file_name"] 		= $docInfo["file_name_cached"];
						
						$tmpRef["file_info"]["stream_url"] 		= $docInfo["relative_stream_url"];
						$tmpRef["file_info"]["link_url"] 		= $docInfo["relative_url"];

						$tmpRef["file_info"]["file_size"] 		= $docInfo["file_size"];
						$tmpRef["file_info"]["mimetype"] 		= $docInfo["mimetype"];									
						$tmpRef["file_info"]["extension"] 		= $docInfo["extension"];	
						$tmpRef["file_info"]["ico_type"] 		= $docInfo["ico_type"];
						$tmpRef["file_info"]["icon_type"] 		= $docInfo["ico_type"];
						if ($tmpRef["file_info"]["icon_type"]=="video") {
							$getNr = "SELECT count(1) as exist_virtual_slide_id

										 FROM ci_elearning_virtual_slide 
										WHERE content_id = '".$identifierReference."' 
										  AND lng_id = '".$this->lngId."' 
										  AND statusInfo in (".$this->thisModeCode.")
									 ORDER BY virtual_slide_order";		
							$rsNr=WebApp::execQuery($getNr);		
							IF (!$rsNr->EOF()) {
								$nrVirtualSlide	= $rsNr->Field("exist_virtual_slide_id");
								if ($nrVirtualSlide>=1) {
									$tmpRef["file_info"]["icon_type"] = "virtual-slide";
								}
							}		
						}
					} else {
						if (isset($value["dt_width"]))				$tmpRef["ref_viewer_width"] 		= $value["dt_width"];	
						else										$tmpRef["ref_viewer_width"] 		= "800";	

						if (isset($value["dt_height"]))				$tmpRef["ref_viewer_height"] 		= $value["dt_height"];	
						else										$tmpRef["ref_viewer_height"] 		= "600";	

						if ($value["identifier_type"]=="INTERNAL" && isset($value["extension"]))
								$tmpRef["ref_ico_type"]		= $value["extension"];
						else	$tmpRef["ref_ico_type"]		= "link-ref";					
					}
					
					
					
/*

    [extension] => link-ref
    [ico_type] => link-ref
-----------------------------------------
    [extension] => link-ref-play
    [ico_type] => link-ref
-----------------------------------------
    [allowed_preview] => yes

-----------------------------------------

    [identifier_key] => 2189
    [identifier_type] => INTERNAL
 

*/					
					
					
					
					
					
					
					$tmpRef["id"] 		= $identifierReference;	
					$this->getCiMultiOrSingleKewRelations($identifierReference);
					if (isset($this->thisKwsRelatedToCi["docKeywordsArray"][$identifierReference])
						&& count($this->thisKwsRelatedToCi["docKeywordsArray"][$identifierReference])>0) {
						$refKeyWords = array();
						reset($this->thisKwsRelatedToCi["docKeywordsArray"][$identifierReference]);
						while (list($fid,$dataV)=each($this->thisKwsRelatedToCi["docKeywordsArray"][$identifierReference])) {
							if (($fid =="8") and count($dataV)>0) {	//$fid=="7" || 
								$refKeyWords = array_merge($refKeyWords, $dataV);
							}
						}
						if (count($refKeyWords)>0) {
							$tmpRef["keywords"] = $refKeyWords;
						}
					}	
					
					$getAndMergeAllReferencesNeFund[$identifierReference] = $tmpRef;
					$MStrToJson["references"]["reference_".$identifierReference] = $tmpRef;			
			
				$rs->MoveNext(); 
			}
		}

		return $MStrToJson;
	}
	function createJsonDataSCVirtual () {
		
		global $sl_file_nedded_data,$session;

		$this->getStructuredInformationEcc();
		$this->getNodeListDocuments();
		$this->getPresentationLecturer();
		
		$controlForSlide = $_REQUEST["sID"];
		if (isset($_REQUEST["sID"]) && $_REQUEST["sID"]>0 && $this->apprcss == "createJsonDataSlide") {
			$controlForSlide = $_REQUEST["sID"];
		}

		$MStrToJson = $this->createPresenationGeneralInfo();
		$mergeKw = array();
		$this->getCiMultiOrSingleKewRelations($this->cidFlow);
		if (isset($this->thisKwsRelatedToCi["docKeywordsArray"][$this->cidFlow])
			&& count($this->thisKwsRelatedToCi["docKeywordsArray"][$this->cidFlow])>0) {
			if (isset($this->thisKwsRelatedToCi["docKeywordsArray"][$this->cidFlow][8])) {
				//$MStrToJson["keywords"] = $this->thisKwsRelatedToCi["docKeywordsArray"][$this->cidFlow][8];
				$mergeKw = array_merge($mergeKw,$this->thisKwsRelatedToCi["docKeywordsArray"][$this->cidFlow][8]);
			}
		}	
		$MStrToJson["has_virtual_slides"] = true;
		$MStrToJson["internal_virtual"] = true;

		$MStrToJson["slides"] = array();

		$SldToJson = array();
		
		$SldToJson["slide_id"] 		= "";
		$SldToJson["title"] 		= "";
		$SldToJson["abstract"] 		= "";
		$SldToJson["toc_id"] 		= "";
		$SldToJson["keywords"] 		= array();
		$SldToJson["references"] 	= array();
		
		$SldToJson["video"] 		= array();
		$SldToJson["audio"] 		= array();
		
		$SldToJson["subtitles"] 	= array();
		$SldToJson["annotation"] 	= array();
		
		$SldToJson["small_img"] 			= "";
		$SldToJson["big_img"] 				= "";		
		
		$SldToJson["durationCalculated"] 	= "";
		$SldToJson["durationExtra"] 		= "";
		$SldToJson["sequence_id"] 			= "";
		$SldToJson["trackable_code"] 		= "";
		
		$SldToJson["virtual_slides"] 		= array();
		
		$SldToJson["index"] =			"";
		$this->getCollectorSlideExtended();
		
		$RAID = $this->appRelSt["LECTURE_RELATED"]["RA"];
		
		$getAndMergeAllReferences = array();
		$saveReferencesToTraitThem = array();
		$saveReferencesToTraitThemAllData = array();
		
		
		/*echo "ListOFSocumentsInActualNode<textarea>";
		print_r($this->ListOFSocumentsInActualNode);
		echo "</textarea>";


		echo "SlidesExtended<textarea>";
		print_r($this->SlidesExtended);
		echo "</textarea>";
		
		return;*/
		
		$orderS = 1;
		if (isset($this->ListOFSocumentsInActualNode[$this->cidFlow]["data"]) && count($this->ListOFSocumentsInActualNode[$this->cidFlow]["data"])>0) {
		
				$slideObj = $this->ListOFSocumentsInActualNode[$this->cidFlow]["data"];
				reset($slideObj);
				while (list($key,$value)=each($slideObj)) {
					if ($value["ci_type"]=="SP") {
						if (($controlForSlide==$value["CID"] && $this->apprcss == "createJsonDataSlide") || $this->apprcss == "createJsonData") {

									$identifierReference = $value["CID"];
									
									$tmp = $SldToJson;

									$tmp["index"] = $orderS;
									$tmp["big_img"]		= $MStrToJson["big_img"];
									$tmp["small_img"]	= $MStrToJson["small_img"];

									$tmp["slide_id"] = $identifierReference;

									if (isset($value["ci_title"]))			$tmp["title"] 			= trim(strip_tags($value["ci_title"]));
									if (isset($value["ew_abstract"]))		$tmp["abstract"] 		= trim(strip_tags($value["ew_abstract"]));

									if (isset($this->mixedNeededData[$identifierReference]["EX"]["duration_calulated_int"])) 
										$tmp["durationCalculated"] = $this->mixedNeededData[$identifierReference]["EX"]["duration_calulated_int"];

									if (isset($this->mixedNeededData[$identifierReference]["EX"]["duration_extra_int"])) 
											$tmp["durationExtra"] = $this->mixedNeededData[$identifierReference]["EX"]["duration_extra_int"];
									
									//if ($tmp["durationExtra"]==0) $tmp["durationExtra"] = 2;
									if ($tmp["durationCalculated"]=="" || $tmp["durationCalculated"]==0)
										$tmp["durationCalculated"] = 10;
									
									$file_duration = $tmp["durationCalculated"];
									if (isset($this->SlidesExtended[$identifierReference]["mediaContent"]["Image"])) {			
										$dt					= $this->SlidesExtended[$identifierReference]["mediaContent"]["Image"];
										$tmp["big_img"]		= $dt["relative_url"];
									}	

									if (isset($this->SlidesExtended[$identifierReference]["smallImage"]["Image"])) {			
										$dt					= $this->SlidesExtended[$identifierReference]["smallImage"]["Image"];
										$tmp["small_img"]	= $dt["relative_url"];
									}

									if (isset($this->SlidesExtended[$identifierReference]["mediaContent"]["Audio"])) {			
										$dt								= $this->SlidesExtended[$identifierReference]["mediaContent"]["Audio"];
										$tmp["audio"]["mp3"]["type"]	= $dt["video_type"];
										$tmp["audio"]["mp3"]["url"]		= $dt["relative_url"];	
										$file_duration = $dt["file_duration"];
									}							

									if (isset($this->SlidesExtended[$identifierReference]["mediaContent"]["VideoMp4"])) {			
										$dt								= $this->SlidesExtended[$identifierReference]["mediaContent"]["VideoMp4"];
										$tmp["video"]["mp4"]["type"]	= $dt["video_type"];
										$tmp["video"]["mp4"]["url"]		= $dt["relative_url"];
										$file_duration = $dt["file_duration"];
									} 							

						
									
									if (isset($this->SlidesExtended[$identifierReference]["Caption"]["json"])) {			
										$dt							= $this->SlidesExtended[$identifierReference]["Caption"]["json"];
										$tmp["subtitles"]["json"]	= $dt["relative_url"];
									} else {
									 	$paths = $this->createEmptyAnnotationTransription ($file_duration,"Caption",$identifierReference,"SP");
									 	$tmp["subtitles"]["vtt"] 	= $paths["identifier_vtt"];
									 	$tmp["subtitles"]["json"] 	= $paths["identifierJson"];
									}
									if (isset($this->SlidesExtended[$identifierReference]["Caption"]["vtt"])) {			
										$dt							= $this->SlidesExtended[$identifierReference]["Caption"]["vtt"];
										$tmp["subtitles"]["vtt"]	= $dt["relative_url"];
									} else {
									 	$paths = $this->createEmptyAnnotationTransription ($file_duration,"Caption",$identifierReference,"SP");
									 	$tmp["subtitles"]["vtt"] 	= $paths["identifier_vtt"];
									}									

									if (isset($this->SlidesExtended[$identifierReference]["Annotation"]["json"])) {			
										$dt							= $this->SlidesExtended[$identifierReference]["Annotation"]["json"];
										$tmp["annotation"]["json"]	= $dt["relative_url"];
									}	

									if (isset($this->SlidesExtended[$identifierReference]["Annotation"]["vtt"])) {			
										$dt							= $this->SlidesExtended[$identifierReference]["Annotation"]["vtt"];
										$tmp["annotation"]["vtt"]	= $dt["relative_url"];
									
									} else {
									 	$paths = $this->createEmptyAnnotationTransription ($file_duration,"Annotation",$identifierReference);
									 	 
									 	$tmp["annotation"]["vtt"] 	= $paths["identifier_vtt"];
									 	$tmp["annotation"]["json"] 	= $paths["identifierJson"];									
									}
									
									
									$tmp["annotation"]["json"]	= $tmp["annotation"]["json"]."?s=1";
									//CREATE DYNAMICALLY
								//	$existAnnotation = $this->existStructuredAnnotation($identifierReference);
								//	if ($existAnnotation>0)
									//	$tmp["annotation"]["json"]	= "ajxDt.php?uni=".$this->uni."&apprcss=getAnnotationJson&cis=".$identifierReference."&sID=".$identifierReference."";
								
									
									

									$this->getCiMultiOrSingleKewRelations($identifierReference);
									if (isset($this->thisKwsRelatedToCi["docKeywordsArray"][$identifierReference])
										&& count($this->thisKwsRelatedToCi["docKeywordsArray"][$identifierReference])>0) {
										if (isset($this->thisKwsRelatedToCi["docKeywordsArray"][$identifierReference][8])) {
											$tmp["keywords"] = $this->thisKwsRelatedToCi["docKeywordsArray"][$identifierReference][8];
											$mergeKw = array_merge($mergeKw,$tmp["keywords"]);
										}
									}	

									if (isset($this->mixedNeededData[$identifierReference]["EX"]["sequence_id"])) 
										$tmp["sequence_id"] = $this->mixedNeededData[$identifierReference]["EX"]["sequence_id"];
									if (isset($this->mixedNeededData[$identifierReference]["EX"]["toc_id_slide"])) 
										$tmp["toc_id"] = $this->mixedNeededData[$identifierReference]["EX"]["toc_id_slide"];


									
$tmp["trackable_code"] = "<script>
EW.analytics.trigger('open', '".$identifierReference."');
clearInterval(analyticsintervalwidg);
if (typeof ANALYTICS_TIMER !== 'undefined') {
    var analyticsintervalwidg = setInterval(function(){
            EW.analytics.trigger('update', '".$identifierReference."');
        }, ANALYTICS_TIMER);
}
</script>";
									
									
									
								
									
									
									
									
									
									
									
									$slideRefencesRa = array();	
									if (isset($this->itemCollectorRelated[$RAID."_".$identifierReference]["data"]) && count($this->itemCollectorRelated[$RAID."_".$identifierReference]["data"])>0) {

										$referecesObj = $this->itemCollectorRelated[$RAID."_".$identifierReference]["data"];
										while (list($key,$value)=each($referecesObj)) {

												$identifierReferenceRa 	= $value["CID"];
												$slideRefencesRa[] 		= $value["CID"];
												
												$saveReferencesToTraitThem[$value["CID"]] = $value["CID"];
												$saveReferencesToTraitThemAllData[$value["CID"]] = $value;
												
										} //while referecesObj
									} //if (isset($this->itemCollectorRelated[$RAID.						

									if (count($slideRefencesRa)>0) {
										$tmp["references"] = $slideRefencesRa;
									}					



									$this->getVirtualSlideExtendedSP($identifierReference);							
									$orderSV = 1;							
									
									$MStrToJson["virtual_slides"] = array();
									
									$SldToJson = array();
									$SldToJson["title"] 			= "";
									$SldToJson["abstract"] 			= "";
									$SldToJson["start_time"] 		= "";
									$SldToJson["end_time"] 			= "";			

									while (list($ind, $valVirtualSlide) = each($this->VirtualSlideData)) {

										$virtual_slide_id				= $valVirtualSlide["virtualSlideID"];
										$virtual_slide_order			= $valVirtualSlide["virtual_slide_order"];
										$virtual_slide_text				= $valVirtualSlide["virtualSlideItem"];
										$virtual_slide_description		= $valVirtualSlide["virtualSlideAbstrat"];
										$start_time						= $valVirtualSlide["start_time"];
										$end_time						= $valVirtualSlide["end_time"];

										$tmpSl = $SldToJson;
										$tmpSl["slide_id"]		= trim(strip_tags($virtual_slide_id));
										$tmpSl["title"]			= trim(strip_tags($virtual_slide_text));
										$tmpSl["abstract"] 		= trim(strip_tags($virtual_slide_description));

										$start_time = trim(strip_tags($start_time));
										$tm = explode(":",$start_time);

										$end_time = trim(strip_tags($end_time));
										$tme = explode(":",$end_time);

										$tmpSl["start_time"] 		= $tm[0]*60+$tm[1];
										$tmpSl["end_time"] 		= $tme[0]*60+$tme[1];

										$tmpSl["durationCalculated"] 		= $tmp["end_time"]-$tmp["start_time"];
										$tmpSl["index"] 		= $orderSV;

										$MStrToJson["virtual_slides"]["slide_".$orderSV] = $tmpSl;

										$orderSV++;

									}


									$MStrToJson["slides"]["slide_".$orderS] = $tmp;
									$orderS++;
							
						} //duhet inkluduar slide
					} //while
					
		} //nese eksiston info per slidet


		$MStrToJson["references"] = array();
		
		$RefToJson = array();
		
		$RefToJson["title"] 				= "";
		$RefToJson["author"] 				= "";
		$RefToJson["ecc_reference"] 		= "";
		$RefToJson["abstract"] 				= "";
		$RefToJson["small_img"] 			= "";
		$RefToJson["small_img"] 			= "";
		$RefToJson["reference_format"] 		= "";
		$RefToJson["identifier_key"] 		= "";
		$RefToJson["identifier_type"] 		= "";
		
		$RefToJson["category"] 					= "";
		$RefToJson["CompulsoryOrRecommended"] 	= "";
		$RefToJson["keywords"] 					= array();
		
		
		
		$RefToJson["file_info"]["file_id"] 		= "";		 
		$RefToJson["file_info"]["file_name"] 	= "";
		$RefToJson["file_info"]["link_url"] 	= "";
		$RefToJson["file_info"]["stream_url"] 	= "";
		$RefToJson["file_info"]["file_size"] 	= "";
		$RefToJson["file_info"]["mimetype"] 	= "";
		$RefToJson["file_info"]["extension"] 	= "";
		$RefToJson["file_info"]["icon_type"] 	= "";

		$RefToJson["toBasket"] 				= false;
		$RefToJson["id"] 					= "";
		
		if (isset($saveReferencesToTraitThem) && count($saveReferencesToTraitThem)>0) {
			$docId = implode(",",$saveReferencesToTraitThem);
		
			 $sql_con = "SELECT content.content_id, coalesce(ecc_reference*1, orderContent) as ordF, ecc_reference	
						  FROM content
							JOIN ci_elearning_extended on content.content_id = ci_elearning_extended.content_id 
							 AND ci_elearning_extended.lng_id = '".$this->lngId."' AND ci_elearning_extended.statusInfo = '".$this->thisModeCode."'						  
						  
						WHERE content.content_id in (".$docId .")
					 GROUP BY content.content_id
					 ORDER BY ordF asc, ecc_reference";

			$rs = WebApp::execQuery($sql_con);	
			$itemGrid = array("data"=>array(),"AllRecs"=> "0");$ind=0;
			while(!$rs->EOF())	{	
			
					$identifierReference	= $rs->Field("content_id");
					
					$tmpRef = $RefToJson;
					$value = $saveReferencesToTraitThemAllData[$identifierReference];
					
					if (isset($value["ci_title"]))			$tmpRef["title"] 			= strip_tags($value["ci_title"]);
					if (isset($value["ecc_reference"]))		$tmpRef["ecc_reference"] 	= strip_tags($value["ecc_reference"]);
					
					
					if (isset($value["source_author"]))		$tmpRef["author"] 			= $value["source_author"];
					if (isset($value["source_author"]))		$tmpRef["author"] 			= $value["source_author"];
					if (isset($value["description"]))		$tmpRef["abstract"] 		= trim(strip_tags($value["description"]));	
					
					if (isset($value["imageSm_id"]))		$tmpRef["small_img"] 		= $value["imageSm_id"];	
					
					if (isset($value["reference_format"]))		$tmpRef["reference_format"] 		= $value["reference_format"];	
					if (isset($value["identifier_key"]))		$tmpRef["identifier_key"] 			= $value["identifier_key"];	
					if (isset($value["identifier_type"]))		$tmpRef["identifier_type"] 			= $value["identifier_type"];	
					if (isset($value["category_Label"]))		$tmpRef["category"] 				= $value["category_Label"];	
					if (isset($value["category_Label_rel"]))	$tmpRef["CompulsoryOrRecommended"] 	= $value["category_Label_rel"];	

					if (isset($value["allowed_preview"]))		$tmpRef["allowed_preview"] 			= $value["allowed_preview"];	
					if (isset($value["allowed_download"]))		$tmpRef["allowed_download"] 		= $value["allowed_download"];	
					
					
					if (isset($value["CID_REF"]))				$tmpRef["ref_id"] 		= $value["CID_REF"];	
					else										$tmpRef["ref_id"] 		= $identifierReference;	



					if (isset($sl_file_nedded_data["data"][$value["ecc_doc_id"]])) {
						$docInfo = $sl_file_nedded_data["data"][$value["ecc_doc_id"]];

						$tmpRef["file_info"]["file_id"]			= $value["ecc_doc_id"];
						$tmpRef["file_info"]["file_name"] 		= $docInfo["file_name_cached"];
						
						$tmpRef["file_info"]["stream_url"] 		= $docInfo["relative_stream_url"];
						$tmpRef["file_info"]["link_url"] 		= $docInfo["relative_url"];

						$tmpRef["file_info"]["file_size"] 		= $docInfo["file_size"];
						$tmpRef["file_info"]["mimetype"] 		= $docInfo["mimetype"];									
						$tmpRef["file_info"]["extension"] 		= $docInfo["extension"];	
						$tmpRef["file_info"]["ico_type"] 		= $docInfo["ico_type"];
						$tmpRef["file_info"]["icon_type"] 		= $docInfo["ico_type"];
						
						if ($tmpRef["file_info"]["icon_type"]=="video") {
						//icon_type: "video"
						
							$getNr = "SELECT count(1) as exist_virtual_slide_id

										 FROM ci_elearning_virtual_slide 
										WHERE content_id = '".$identifierReference."' 
										  AND lng_id = '".$this->lngId."' 
										  AND statusInfo in (".$this->thisModeCode.")
									 ORDER BY virtual_slide_order";		

							$rsNr=WebApp::execQuery($getNr);		
							IF (!$rsNr->EOF()) {
								$nrVirtualSlide	= $rsNr->Field("exist_virtual_slide_id");
								if ($nrVirtualSlide>=1) {
									$tmpRef["file_info"]["icon_type"] = "virtual-slide";
								}
							}		
						}
						
					} else {
						if (isset($value["dt_width"]))				$tmpRef["ref_viewer_width"] 		= $value["dt_width"];	
						else										$tmpRef["ref_viewer_width"] 		= "800";	

						if (isset($value["dt_height"]))				$tmpRef["ref_viewer_height"] 		= $value["dt_height"];	
						else										$tmpRef["ref_viewer_height"] 		= "600";	

						if ($value["identifier_type"]=="INTERNAL" && isset($value["extension"]))
								$tmpRef["ref_ico_type"]		= $value["extension"];
						else	$tmpRef["ref_ico_type"]		= "link-ref";					
					}
					
					$tmpRef["id"] 		= $identifierReference;	
					
					$this->getCiMultiOrSingleKewRelations($identifierReference);
					

					if (isset($this->thisKwsRelatedToCi["docKeywordsArray"][$identifierReference])
						&& count($this->thisKwsRelatedToCi["docKeywordsArray"][$identifierReference])>0) {
						
						$refKeyWords = array();
						reset($this->thisKwsRelatedToCi["docKeywordsArray"][$identifierReference]);
						
						while (list($fid,$dataV)=each($this->thisKwsRelatedToCi["docKeywordsArray"][$identifierReference])) {
							if (($fid =="8") and count($dataV)>0) {	//$fid=="7" || 
								$refKeyWords = array_merge($refKeyWords, $dataV);
							}
						}
						if (count($refKeyWords)>0) {
							$tmpRef["keywords"] = $refKeyWords;
						}
					}	
					
					$getAndMergeAllReferencesNeFund[$identifierReference] = $tmpRef;
					$MStrToJson["references"]["reference_".$identifierReference] = $tmpRef;			
			
				$rs->MoveNext(); 
			}
		}

	
		if (isset($mergeKw) && count($mergeKw)>0)
		$MStrToJson["keywords"] = $mergeKw;
		

		
		
		$MStrToJson["TOC"] = array();
		if (isset($this->TOC) && count($this->TOC)>0)  {
		while (list($key,$value)=each($this->TOC)) {
			$MStrToJson["TOC"]["toc_$key"]["id"]		= $key;
			$MStrToJson["TOC"]["toc_$key"]["title"]	= strip_tags($value);
		}}
						

		}

	
		$jsonD = json_encode($MStrToJson);
		header ("Content-type: application/json; charset=".APP_ENCODING);
		header ('Access-Control-Allow-Origin: *');		
		print_r($jsonD);
	
	}		
	
	
	
	
	function getMetaDataFromFile() {
		
		global $session;
		$tot_duration = "0";
		if ($this->ci_type_configuration=='SP') {
			$this->ci_collected["groupedType"]["SP"][$this->cidFlow]=$this->cidFlow;	
		
		} else {
			
			$this->getNodeListDocuments();
			$this->MainNodeCi = $this->appRelSt["MainNodeCiID"];
		}
		
		
		$this->getCollectorSlideExtended();
		$getID3 = new getID3();
		$duration = array();
		
		$cat = "mediaContent";
		reset($this->SlidesExtended);
		While (list($cidFlow,$Slides) =each($this->SlidesExtended)) {
			
			$durTemp = array();
			$maxSec		 = 0;
			$maxDuration = 0;
			if (isset($Slides[$cat]) && count($Slides[$cat])>1) {
			While (list($subCat,$dt) =each($Slides[$cat])) {
				
					$public_filepath = $this->PUBLIC_FILEPATH.$dt["identifier"].$dt["file_name"];
					$ThisFileInfo = $getID3->analyze($public_filepath);
					
					if (isset($ThisFileInfo["playtime_string"])) {
						$duration[$cidFlow]["playtime_string"][$cat."_".$subCat] = $ThisFileInfo["playtime_string"];
						$durTemp[$ThisFileInfo["playtime_string"]]=$ThisFileInfo["playtime_string"];
					}
					
					if (isset($ThisFileInfo["playtime_seconds"]) && $ThisFileInfo["playtime_seconds"]>$maxSec) {
						$maxSec = $ThisFileInfo["playtime_seconds"];
						$maxDuration = $ThisFileInfo["playtime_string"];
					}
			}}
			
			$tmp 			= explode(":",$maxDuration);
			$durationToSave = $tmp[0]*60+ $tmp[1];
			
 			$update_content_general="UPDATE sp_data 
										SET duration_calulated = '".$durationToSave."'
					 			      WHERE content_id 	= ".$cidFlow."
					 			        AND lng_id 		= '".$this->lngId."'
					 			        AND statusInfo 	= '".$this->thisModeCode."'";					 
			WebApp::execQuery($update_content_general);		
			$this->approveSPprop($cidFlow);		
		}
			
		$this->MainNodeCi 		= $this->appRelSt["MainNodeCiID"];
		//$this->MainNodeCi = $this->appRelSt["LECTURE_RELATED"]["SC"];
		
		/*echo 	$this->ci_type_configuration.":ci_type_configuration<br>";	
		echo 	$this->MainNodeCi.":MainNodeCi<br>";	
		echo 	$this->cidFlow.":cidFlow<br>";	
		echo 	$durationToSave.":durationToSave<br>";	*/


		 $getTotalDuration = "SELECT sum(duration_calulated) as tot_duration 
							   FROM sp_data
							  WHERE presentation_id  = '".$this->MainNodeCi."'
									AND lng_id 		= '".$this->lngId."'
									AND statusInfo 	= '".$this->thisModeCode."'";
		$rsAllNode = WebApp::execQuery($getTotalDuration);
		IF (!$rsAllNode->EOF()) {
			$tot_duration = $rsAllNode->Field("tot_duration");
		}
		$update_content_general="UPDATE sc_data 
									SET duration_calulated = '".$tot_duration."'
								  WHERE content_id 	= '".$this->MainNodeCi."'
									AND lng_id 		= '".$this->lngId."'
									AND statusInfo 	= '".$this->thisModeCode."'";					 
		WebApp::execQuery($update_content_general);				
		
		if ($this->ci_type_configuration=='SC') {
			
			$this->approveSCprop($cidFlow);	
		
		} else {
			$tot_duration = $durationToSave;
		}
		return $tot_duration;
	}

    function getGeneralDataRelated()
    {
        global $session;
    }	
    
    function getStructuredInformationEcc($contentId = '')
    {
		global $session;
        
        
        $pathLectureTypes = array();

       // $pathLectureTypes["SL"] = "SL";    //Lecture guide and Prelearning

       // $pathLectureTypes["CQ"] = "CQ";    //Elearning Quiz Container -or during lecture quiz
       // $pathLectureTypes["ES"] = "ES";    //Final Examination Container

        $pathLectureTypes["SC"] = "SC";    //Lecture Presentation (Player)
       // $pathLectureTypes["SV"] = "SV";    //Lecture Presentation - (Player) Virtual Slides

       // $pathLectureTypes["SM"] = "SM";    //Summary  of the lecture

       // $pathLectureTypes["US"] = "US";    //User Satisfaction - Survey
       // $pathLectureTypes["UF"] = "UF";    //User Satisfaction - Feedback
       // $pathLectureTypes["UE"] = "UE";    //User Experience - Lecture
       // $pathLectureTypes["LR"] = "LR";    //Lecture Level Reports
       // $pathLectureTypes["NC"] = "LR";    //News Collector

        $pathRepository = array();
        $pathRepository["RA"] = "RA";    //Additional Knowledge Resources
       // $pathRepository["RQ"] = "RQ";    //Quizes & Examninations Repository

        $pathLectureSubTypes = array();
       // $pathLectureSubTypes["EQ"] = "EQ";    //Question Item
        $pathLectureSubTypes["RI"] = "RI";    //Additional Knowledge Item
       // $pathLectureSubTypes["NI"] = "NI";    //Additional Knowledge Item
        $pathLectureSubTypes["SP"] = "SP";    //Presantation Slide

        $pathLectureAllowedTypes = array();

        $pathLectureAllowedTypes = array_merge($pathLectureAllowedTypes, $pathLectureTypes);
        $pathLectureAllowedTypes = array_merge($pathLectureAllowedTypes, $pathRepository);
        $pathLectureAllowedTypes = array_merge($pathLectureAllowedTypes, $pathLectureSubTypes);


        $this->pathLectureInfo["typeLecture"] 		= $pathLectureTypes;
        $this->pathLectureInfo["typeRepository"] 	= $pathRepository;
        $this->pathLectureInfo["SubTypesLecture"] 	= $pathLectureSubTypes;


        $this->pathLectureInfo["lectureAllowedTypes"] = $pathLectureAllowedTypes;

       // $this->pathLectureInfo["mainParents"]["PR"] = "PR";
       // $this->pathLectureInfo["mainParents"]["EC"] = "EC";
        $this->pathLectureInfo["mainParents"]["EL"] = "EL";
       // $this->pathLectureInfo["mainParents"]["TC"] = "TC";

        $hierarchy_level = $this->appRelSt["hierarchy_level"][$this->cidFlow];


		$BoCondition = "";
		if ($this->thisModeCode=="0") { //a paaprovuar
		
		} else {
			$BoCondition = "AND n.active" . $session->Vars["lang"] . " != '1'
							AND n.state" . $session->Vars["lang"] . " != 7";
			$BoCondition = "AND n.state" . $session->Vars["lang"] . " != 7";
		}


            $nivel_conditionLecture = array();
            //kap hierarkine
            if ($hierarchy_level == 4) {
                $nivel_conditionLecture[0] = " content.id_zeroNivel = '" . $this->appRelSt["coord"][$this->cidFlow][0] . "' ";
                $nivel_conditionLecture[1] = " content.id_firstNivel = '" . $this->appRelSt["coord"][$this->cidFlow][1] . "' ";
                $nivel_conditionLecture[2] = " content.id_secondNivel = '" . $this->appRelSt["coord"][$this->cidFlow][2] . "' ";
                $nivel_conditionLecture[3] = " content.id_thirdNivel = '" . $this->appRelSt["coord"][$this->cidFlow][3] . "'  ";
                $nivel_conditionLecture[4] = " content.id_fourthNivel = 0 ";

            } elseif ($hierarchy_level == 3) {
                $nivel_conditionLecture[0] = " content.id_zeroNivel = '" . $this->appRelSt["coord"][$this->cidFlow][0] . "' ";
                $nivel_conditionLecture[1] = " content.id_firstNivel = '" . $this->appRelSt["coord"][$this->cidFlow][1] . "' ";
                $nivel_conditionLecture[2] = " content.id_secondNivel = '" . $this->appRelSt["coord"][$this->cidFlow][2] . "' ";
                $nivel_conditionLecture[3] = " content.id_thirdNivel = 0 ";
                $nivel_conditionLecture[4] = " content.id_fourthNivel = 0 ";

            } elseif ($hierarchy_level == 2) {
                $nivel_conditionLecture[0] = " content.id_zeroNivel = '" . $this->appRelSt["coord"][$this->cidFlow][0] . "' ";
                $nivel_conditionLecture[1] = " content.id_firstNivel = '" . $this->appRelSt["coord"][$this->cidFlow][1] . "' ";
                $nivel_conditionLecture[2] = " content.id_secondNivel = 0 ";
                $nivel_conditionLecture[3] = " content.id_thirdNivel = 0 ";
                $nivel_conditionLecture[4] = " content.id_fourthNivel = 0 ";
            }

            if (count($nivel_conditionLecture) > 0) {

                $kushtSql = implode(" AND ", $nivel_conditionLecture);
                $sql_el = "SELECT content_id, ci_type, `read_write` as rights,
											coalesce(doctype_description,'document') as doctype_name,
											content.id_zeroNivel, content.id_firstNivel,content.id_secondNivel,content.id_thirdNivel,content.id_fourthNivel,
											titleLng1 as title

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
																AND profil_rights.profil_id in (" . $this->tip . ")
															)
								WHERE " . $kushtSql . "
								  AND orderContent   = '0'
								  AND content.state" . $this->lang . " not in (0,5,7)
								  ".$BoCondition."";

                $rs_el = WebApp::execQuery($sql_el);
                if (!$rs_el->EOF()) {

                    $ci_type = $rs_el->Field("ci_type");
                    $relid = $rs_el->Field("content_id");
                    $title = $rs_el->Field("title");

                    $this->appRelSt["LECTURE_RELATED_ALL"][$ci_type][$relid] = $relid;

                    $this->appRelSt["CiRights"][$relid]["ci_type"] = $ci_type;
                    $this->appRelSt["LECTURE_RELATED"][$ci_type] = $relid;
                    $rights = explode(",", $rs_el->Field("rights"));
                    if (is_array($rights) && in_array("W", $rights))
                        	$this->appRelSt["CiRights"][$relid]["read_write"] = "W";
                    else    $this->appRelSt["CiRights"][$relid]["read_write"] = "R";

                    $this->getCiNeededVar($relid);

                    $this->appRelSt["LECTURE_RELATED_INFO"][$relid]["title"] = $title;
                    $this->appRelSt["LECTURE_RELATED_INFO"][$relid]["actualNodeDescription"] = $this->mixedNeededData[$relid]["DC"]["actualNodeDescription"];

                    $this->appRelSt["LECTURE_RELATED_INFO"][$relid]["doctype_name"] = $rs_el->Field("doctype_name");

                    $this->appRelSt["LECTURE_RELATED_INFO"][$relid]["coord"][0] = $rs_el->Field("id_zeroNivel");
                    $this->appRelSt["LECTURE_RELATED_INFO"][$relid]["coord"][1] = $rs_el->Field("id_firstNivel");
                    $this->appRelSt["LECTURE_RELATED_INFO"][$relid]["coord"][2] = $rs_el->Field("id_secondNivel");
                    $this->appRelSt["LECTURE_RELATED_INFO"][$relid]["coord"][3] = $rs_el->Field("id_thirdNivel");
                    $this->appRelSt["LECTURE_RELATED_INFO"][$relid]["coord"][4] = $rs_el->Field("id_fourthNivel");
                }
            }

            //ketu gjej dhe data e lecture EL
            $nivel_condition = array();
            //kap hierarkine
            if ($hierarchy_level == 4) {
                $nivel_condition[0] = " content.id_zeroNivel = '" . $this->appRelSt["coord"][$this->cidFlow][0] . "' ";
                $nivel_condition[1] = " content.id_firstNivel = '" . $this->appRelSt["coord"][$this->cidFlow][1] . "' ";
                $nivel_condition[2] = " content.id_secondNivel = '" . $this->appRelSt["coord"][$this->cidFlow][2] . "' ";
                $nivel_condition[3] = " content.id_thirdNivel = '" . $this->appRelSt["coord"][$this->cidFlow][3] . "' ";
                $nivel_condition[4] = " content.id_fourthNivel >0 ";

            } elseif ($hierarchy_level == 3) {
                $nivel_condition[0] = " content.id_zeroNivel = '" . $this->appRelSt["coord"][$this->cidFlow][0] . "' ";
                $nivel_condition[1] = " content.id_firstNivel = '" . $this->appRelSt["coord"][$this->cidFlow][1] . "' ";
                $nivel_condition[2] = " content.id_secondNivel = '" . $this->appRelSt["coord"][$this->cidFlow][2] . "' ";
                $nivel_condition[3] = " content.id_thirdNivel >0 ";
                $nivel_condition[4] = " content.id_fourthNivel > 0 ";

            } elseif ($hierarchy_level == 2) {
                $nivel_condition[0] = " content.id_zeroNivel = '" . $this->appRelSt["coord"][$this->cidFlow][0] . "' ";
                $nivel_condition[1] = " content.id_firstNivel = '" . $this->appRelSt["coord"][$this->cidFlow][1] . "' ";
                $nivel_condition[2] = " content.id_secondNivel >0 ";
                $nivel_condition[3] = " content.id_thirdNivel > 0 ";
                $nivel_condition[4] = " content.id_fourthNivel > 0 ";
            } 

            if (count($nivel_condition) > 0) {

                $kushtSql = implode(" AND ", $nivel_condition);

                $sql_con = "SELECT content_id, ci_type, `read_write` as rights,
										coalesce(doctype_description,'document') as doctype_name,
										content.id_zeroNivel, content.id_firstNivel,content.id_secondNivel,content.id_thirdNivel,content.id_fourthNivel,
										titleLng1 as title

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
															AND profil_rights.profil_id in (" . $this->tip . ")
														)
								WHERE " . $kushtSql . "
								  AND orderContent   = '0'
								  AND content.state" . $this->lang . " not in (0,5,7)
								  ".$BoCondition."";

                $rs_con = WebApp::execQuery($sql_con);
                while (!$rs_con->EOF()) {

                    $ci_type = $rs_con->Field("ci_type");
                    $relid = $rs_con->Field("content_id");
                    $title = $rs_con->Field("title");

                    $this->appRelSt["LECTURE_RELATED_ALL"][$ci_type][$relid] = $relid;
                    $this->appRelSt["CiRights"][$relid]["ci_type"] = $ci_type;
                    $rights = explode(",", $rs_con->Field("rights"));
                    if (is_array($rights) && in_array("W", $rights))
                        $this->appRelSt["CiRights"][$relid]["read_write"] = "W";
                    else    $this->appRelSt["CiRights"][$relid]["read_write"] = "R";

                    if (in_array($ci_type, $pathLectureAllowedTypes)) {

                        $this->appRelSt["LECTURE_RELATED"][$ci_type] = $relid;

                        $rights = explode(",", $rs_con->Field("rights"));
                        if (is_array($rights) && in_array("W", $rights))
                            $this->appRelSt["LECTURE_RELATED_INFO"][$relid]["read_write"] = "W";
                        else    $this->appRelSt["LECTURE_RELATED_INFO"][$relid]["read_write"] = "R";
                    }

                    $this->appRelSt["LECTURE_RELATED_INFO"][$relid]["title"] = $title;
                    $this->appRelSt["LECTURE_RELATED_INFO"][$relid]["actualNodeDescription"] = $this->mixedNeededData[$relid]["DC"]["actualNodeDescription"];

                    $this->appRelSt["LECTURE_RELATED_INFO"][$relid]["doctype_name"] = $rs_con->Field("doctype_name");

                    $this->appRelSt["LECTURE_RELATED_INFO"][$relid]["coord"][0] = $rs_con->Field("id_zeroNivel");
                    $this->appRelSt["LECTURE_RELATED_INFO"][$relid]["coord"][1] = $rs_con->Field("id_firstNivel");
                    $this->appRelSt["LECTURE_RELATED_INFO"][$relid]["coord"][2] = $rs_con->Field("id_secondNivel");
                    $this->appRelSt["LECTURE_RELATED_INFO"][$relid]["coord"][3] = $rs_con->Field("id_thirdNivel");
                    $this->appRelSt["LECTURE_RELATED_INFO"][$relid]["coord"][4] = $rs_con->Field("id_fourthNivel");
                    $rs_con->MoveNext();
                }
            } 
//        $this->getTocOfLecture();
    }    
    function getVirtualSlideExtendedSP($slideID = "")
    {
        if ($slideID == "") $slideID = $this->cidFlow;
        $getToc = "SELECT virtual_slide_id, virtual_slide_order,
								  coalesce(virtual_slide_text,'') as virtual_slide_text,
								  coalesce(virtual_slide_description,'') as virtual_slide_description,

								  coalesce(start_time,'') as start_time,
								  coalesce(end_time,'') as end_time

							 FROM sp_data_virtual_slide
							WHERE content_id = '" . $slideID . "'
							  AND lng_id = '" . $this->lngId . "'
							  AND statusInfo in (0)
						 ORDER BY virtual_slide_order";

        $rs = WebApp::execQuery($getToc);
        $ind = 0;
        while (!$rs->EOF()) {
            $virtual_slide_id = $rs->Field("virtual_slide_id");
            $virtual_slide_order = $rs->Field("virtual_slide_order");
            $itemGrid["data"][$ind]["virtualSlideID"] = $virtual_slide_id;
            $itemGrid["data"][$ind]["virtualSlideItem"] = $rs->Field("virtual_slide_text");
            $itemGrid["data"][$ind]["virtualSlideAbstrat"] = $rs->Field("virtual_slide_description");
            $itemGrid["data"][$ind]["virtual_slide_order"] = $rs->Field("virtual_slide_order");
            $itemGrid["data"][$ind]["start_time"] = $rs->Field("start_time");
            $itemGrid["data"][$ind]["end_time"] = $rs->Field("end_time");

            $this->VirtualSlide[$slideID][$virtual_slide_order] = $itemGrid["data"][$ind];
            $ind++;
            $rs->MoveNext();
        }
        $this->VirtualSlideData = $itemGrid["data"];
    }    
    function getNodeListDocuments()
    {
        $coord = $this->appRelSt["coord"][$this->cidFlow];
        $fieldToSelect = ", orderContent as seq";
        $joinTb = "";
        $orderBy = "ORDER BY orderContent DESC";
        $joinTypeCondition = "";

        if ($this->ci_type_configuration == "SC") {    //slide collector

            $orderBy = "ORDER BY orderContent ASC";
            //$fieldToSelect = ", coalesce(sequence_id,orderContent) as seq ";
            $joinTb = " JOIN sp_data on sp_data.content_id= content.content_id and sp_data.lng_id = '" . $this->lngId . "' ";

            if (isset($this->mixedNeededData[$this->cidFlow]["EX"]["collect_virtual_slide"]) && $this->mixedNeededData[$this->cidFlow]["EX"]["collect_virtual_slide"] == "yes") {

                $joinTb .= " AND sp_data.has_virtual_slide = 'yes'";
            } else        $joinTb .= " AND sp_data.has_virtual_slide != 'yes'";

            $fieldToSelect = ", coalesce(sequence_id,orderContent) as seq, coalesce(sp_data.has_virtual_slide,'no') as has_virtual_slide ";

        } elseif ($this->ci_type_configuration == "SP") {    //slide collector

            $orderBy = "ORDER BY orderContent ASC";
            //$fieldToSelect = ", coalesce(sequence_id,orderContent) as seq ";
            $joinTb = " JOIN sp_data on sp_data.content_id= content.content_id and sp_data.lng_id = '" . $this->lngId . "' AND sp_data.content_id = '".$this->cidFlow."'";

            if (isset($this->mixedNeededData[$this->cidFlow]["EX"]["collect_virtual_slide"]) && $this->mixedNeededData[$this->cidFlow]["EX"]["collect_virtual_slide"] == "yes") {

                $joinTb .= " AND sp_data.has_virtual_slide = 'yes'";
            } else        $joinTb .= " AND sp_data.has_virtual_slide != 'yes'";

            $fieldToSelect = ", coalesce(sequence_id,orderContent) as seq, coalesce(sp_data.has_virtual_slide,'no') as has_virtual_slide ";

        } else {

            if ($this->ci_type_configuration == "RQ"
                || $this->ci_type_configuration == "CQ"
                || $this->ci_type_configuration == "ES"
            ) {    //quiz collector

                $RQID = $this->appRelSt["LECTURE_RELATED"]["RQ"];
                $coord = $this->appRelSt["LECTURE_RELATED_INFO"][$RQID]["coord"];
                $orderBy = "ORDER BY orderContent ASC";
                $fieldToSelect = ", coalesce(eq_data.sequence_id,orderContent) as seq ";
                $joinTb = "LEFT JOIN eq_data on  eq_data.content_id= content.content_id and eq_data.repository_id = '" . $RQID . "'  and eq_data.lng_id = '" . $this->lngId . "' ";
                $joinTypeCondition = "AND content.ci_type= 'EQ'";
            } else {
                $orderBy = "ORDER BY ordF asc, ecc_reference";
            }
       }


		$BoConditionCI = "";
		if ($this->thisModeCode=="0") { //a paaprovuar
			$BoConditionCI = "
						  AND content.state" . $this->lang . " not in (0,5,7)";		
		} else {
			$BoConditionCI = "
						  AND content.state" . $this->lang . " not in (0,5,7)
						  AND content.published" . $this->lang . " = 'Y'";
		}

        $sql_con = "SELECT distinct content.content_id, ci_type, coalesce(doctype_description,'') as doctype_description,
							   title" . $this->lang . " as ci_title,
							   coalesce(searchable,'') as searchable,
							   coalesce(doc_source" . $this->lang . ",'') as source,
							   coalesce(source_author" . $this->lang . ",'') as source_author,
							   coalesce(description" . $this->lang . $this->thisMode . ",'') as description,
							   coalesce(imageSm_id,'') as imageSm_id,
							   coalesce(imageBg_id,'') as imageBg_id
							   " . $fieldToSelect . ",

							   coalesce(ecc_reference*1, orderContent) as ordF, ecc_reference

						  FROM content
						  JOIN profil_rights ON (       content.id_zeroNivel   = profil_rights.id_zeroNivel
													AND content.id_firstNivel  = profil_rights.id_firstNivel
													AND content.id_secondNivel = profil_rights.id_secondNivel
													AND content.id_thirdNivel  = profil_rights.id_thirdNivel
													AND content.id_fourthNivel = profil_rights.id_fourthNivel
													AND profil_rights.profil_id in (" . $this->tip . ")
												)
						  JOIN document_types ON document_types.doctype_name = content.ci_type

						  " . $joinTb . "

								LEFT JOIN ci_elearning_extended on content.content_id = ci_elearning_extended.content_id
								 AND ci_elearning_extended.lng_id = '" . $this->lngId . "' AND ci_elearning_extended.statusInfo = '".$this->thisModeCode."'

						WHERE content.id_zeroNivel 		= '" . $coord[0] . "'
						  AND content.id_firstNivel 	= '" . $coord[1] . "'
						  AND content.id_secondNivel	= '" . $coord[2] . "'
						  AND content.id_thirdNivel 	= '" . $coord[3] . "'
						  AND content.id_fourthNivel 	= '" . $coord[4] . "'
						  AND orderContent!=0
							".$BoConditionCI."
						    ".$joinTypeCondition."

					 GROUP BY content.content_id
					" . $orderBy . "";


        $this->ListOFSocumentsInActualNodeSql[$this->cidFlow] = $sql_con;
        $itemGrid = array("data" => array(), "AllRecs" => "0");
        $ind = 0;
        $ddd = array();

        $rs = WebApp::execQuery($sql_con);
        while (!$rs->EOF()) {

            $content_id = $rs->Field("content_id");
            
            
            $this->getCiNeededVar($content_id);
            
            $ci_type = $rs->Field("ci_type");

            $this->ci_collected["groupedType"][$ci_type][$content_id] = $content_id;

            $this->ci_collected_node[$this->cidFlow][$content_id] = $content_id;

            $itemGrid["data"][$ind]["ci_title"] = $rs->Field("ci_title");
            $itemGrid["data"][$ind]["CID"] = $content_id;
            $itemGrid["data"][$ind]["ci_type"] = $rs->Field("ci_type");
            $itemGrid["data"][$ind]["source"] = $rs->Field("source");
            $itemGrid["data"][$ind]["source_author"] = $rs->Field("source_author");
            $itemGrid["data"][$ind]["description"] = $rs->Field("description");
            $itemGrid["data"][$ind]["ci_type_desc"] = $rs->Field("doctype_description");
            $itemGrid["data"][$ind]["seqNr"] = $rs->Field("seq");

            if ($this->ci_type_configuration == "SC") {
                $itemGrid["data"][$ind]["has_virtual_slide"] = $rs->Field("has_virtual_slide");
            }
            $ind++;
            $rs->MoveNext();

        }
        
        $this->ListOFSocumentsInActualNode[$this->cidFlow] = $itemGrid;
      //  $this->ci_collected["groupedType"] = $tmpInfoArray["groupedType"];
    }	
    function getCiMultiOrSingleKewRelations($ciIdsInList = "")
    {

        $idsCis = "";
        if ($ciIdsInList == "") {
            if (isset($this->ci_collected_node[$this->cidFlow]) && count($this->ci_collected_node[$this->cidFlow]) > 0) {
                $idsCis = implode(",", $this->ci_collected_node[$this->cidFlow]);
            }
        } else {
            $idsCis = $ciIdsInList;
        }

        if ($idsCis == "") {
        } else {

            $mainCisKeywordsRelatedItem = array();
            $KeywordsRelatedItemInfo = array();
            $KeywordsRelatedItemInfoDetailed = array();

            $mainCisKeywordsRelatedItem = array();
            $getKwCi = "SELECT content_id, family_id, kw_id as id, family_id
						  FROM kw_ci_relations
						 WHERE content_id in (" . $idsCis . ")
						   AND lng_id = '" . $this->lngId . "'
						   AND statusInfo = '".$this->thisModeCode."'";
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

            if (count($KeywordsRelatedItemInfo["family"]) > 0) {

                $idsFamilyRelated = implode(",", $KeywordsRelatedItemInfo["family"]);
                $getKwFamily = "SELECT coalesce(description" . $this->lang . ",name) as description, family_id, family_type_id,  table_name
								FROM kw_family
							   WHERE family_id in (" . $idsFamilyRelated . ")";
                $rsKwFamily = WebApp::execQuery($getKwFamily);
                while (!$rsKwFamily->EOF()) {

                    $tableName = $rsKwFamily->Field("table_name");
                    $family_id = $rsKwFamily->Field("family_id");
                    $family_type_id = $rsKwFamily->Field("family_type_id");

                    $KeywordsRelatedItemInfoDetailed["family"][$family_id]["tableName"] = $tableName;
                    $KeywordsRelatedItemInfoDetailed["family"][$family_id]["family_type_id"] = $family_type_id;

                    if (isset($KeywordsRelatedItemInfo["Item"][$family_id]) && count($KeywordsRelatedItemInfo["Item"][$family_id]) > 0) {

                        $keyWordsData = array();
                        $idsKWRelated = implode(",", $KeywordsRelatedItemInfo["Item"][$family_id]);

                        $getKwDEsc = "SELECT description" . $this->lang . " as description , kw_id as id
									  FROM " . $tableName . "
									 WHERE kw_id in (" . $idsKWRelated . ")
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

            $mainCisKeywordsRelatedItemValidate = array();
            while (list($ciExtendedID, $fam_related) = each($mainCisKeywordsRelatedItem)) {

                $gridDataSrcKeyowrds = array("data" => array(),"AllRecs" => "0");	$indexGridKeywords = 0;
                $alias = $ciExtendedID;

                while (list($ci_family_rel, $kw_related) = each($fam_related)) {

                    $tmpInfoArray["list"]["CI_DATA"][$ciExtendedID]["dp_keywords"] = "yes";
                    $gridDataSrcKeyowrds["data"][$indexGridKeywords]["docKeywords"] = "";

                    if ($this->kw_publish_labels[$family_id] != "") {
                        $gridDataSrcKeyowrds["data"][$indexGridKeywords]["dp_family_name"] = "yes";
                        $gridDataSrcKeyowrds["data"][$indexGridKeywords]["family_name"] = $this->kw_publish_labels[$ci_family_rel];
                    } else {
                        $gridDataSrcKeyowrds["data"][$indexGridKeywords]["dp_family_name"] = "no";
                    }
                    $gridDataSrcKeyowrds["data"][$indexGridKeywords]["family_id"] = $ci_family_rel;
                    $gridDataSrcKeyowrdsItems = array("data" => array(),"AllRecs" => "0"); $kky_id = 0;
                    $imploded_kw_array = array();

                    while (list($ci_kw_rel, $tttmm) = each($kw_related)) {
                        if (isset($KeywordsRelatedItemInfoDetailed[$ci_family_rel][$ci_kw_rel])) {
                            $desc = $KeywordsRelatedItemInfoDetailed[$ci_family_rel][$ci_kw_rel];
                            $imploded_kw_array[] = $desc;
                            $gridDataSrcKeyowrdsItems["data"][$kky_id]["docKeywordsItem"] = $desc;
                            $gridDataSrcKeyowrdsItems["data"][$kky_id]["docKwItemLb"] = $desc;
                            $gridDataSrcKeyowrdsItems["data"][$kky_id]["docKwItemid"] = $ci_kw_rel;
                            $kky_id++;
                        }
                    }

                    $gridDataSrcKeyowrdsItems["AllRecs"] = count($gridDataSrcKeyowrdsItems["data"]);
                    if ($gridDataSrcKeyowrdsItems["AllRecs"] > 0) {
                        WebApp::addVar("gridDataSrcKeywordsItems_" . $alias . "_" . $ci_family_rel, $gridDataSrcKeyowrdsItems);
                    }
                    $this->thisKwsRelatedToCi["familyGrid"][$alias][$ci_family_rel] = $gridDataSrcKeyowrdsItems;
                    if (count($imploded_kw_array) > 0) {
                        $gridDataSrcKeyowrds["data"][$indexGridKeywords]["docKeywords"] = implode(", ", $imploded_kw_array);
                        $this->thisKwsRelatedToCi["docKeywords"][$alias][$ci_family_rel] = implode(", ", $imploded_kw_array);
                        $this->thisKwsRelatedToCi["docKeywordsArray"][$alias][$ci_family_rel] = $imploded_kw_array;
                        $tmpKwRelatedToCi = array();
                        $tmpKwRelatedToCi["data"][0]["ci_kw_sel"] = $this->thisKwsRelatedToCi["docKeywords"][$alias][$ci_family_rel];
                        $tmpKwRelatedToCi["AllRecs"] = 1;
                        WebApp::addVar("docKeywords_" . $alias . "_" . $ci_family_rel, $tmpKwRelatedToCi);
                    }
                    $indexGridKeywords++;
                }

                $gridDataSrcKeyowrds["AllRecs"] = count($gridDataSrcKeyowrds["data"]);
                //ky array do krijohet si varibel global vetem nese ka te dhena
                if ($gridDataSrcKeyowrds["AllRecs"] > 0) {
                    WebApp::addVar("gridDataSrcKeywords_" . $alias, $gridDataSrcKeyowrds);
                    $this->gridDataSrcKeyowrds[$indexArray] = $gridDataSrcKeyowrds;
                }
            }
        }
    }	
    function getCollectorSlideExtended()
    {
		//$this->getStructuredSlides();
		$raID = "";
		if (isset($this->appRelSt["LECTURE_RELATED"]["RA"]) && $this->appRelSt["LECTURE_RELATED"]["RA"] > 0) 
			$raID = $this->appRelSt["LECTURE_RELATED"]["RA"];
       
        if ($this->ci_type_configuration == "SC") {
            $gridTemp = array();
            $getFileSlideInfo = "SELECT content_id,
										if (category_of_data='mediaContent',1,0) as ord1,
										coalesce(category_of_data, '') 	as category_of_data,
										coalesce(type_of_data, '') 		as type_of_data,
										coalesce(file_name, '') 		as file_name,
										coalesce(file_name_ori, file_name) 	as file_name_ori,
										coalesce(file_id, '') 			as file_id,
										coalesce(file_type, '') 		as file_type,
										coalesce(file_size, '') 		as file_size,
										coalesce(identifier, '') 		as identifier,
										coalesce(file_duration, '') 	as file_duration,
										coalesce(file_width, '') 	as file_width,
										coalesce(file_height, '') as file_height
								   FROM sc_data_doc
						   		  WHERE content_id 	in  (" . $this->cidFlow . ")
						  			AND lng_id 		= '" . $this->lngId . "'
						  			AND statusInfo 	= '0'
						  			
							   ORDER BY content_id, ord1,category_of_data, type_of_data";

            $rsD = WebApp::execQuery($getFileSlideInfo); 
            $indD = 0;
            while (!$rsD->EOF()) {

                $content_id = $rsD->Field("content_id");
                $category_of_data = $rsD->Field("category_of_data");

                $category_sub 	= $rsD->Field("type_of_data");
                $file_name 		= $rsD->Field("file_name");
                
                if ( $file_name!="" && is_file(APP_PATH.RC_FOLDER_NAME.$rsD->Field("identifier").$rsD->Field("file_name")) ) {
                
						$gridTemp[$content_id]["data"][$indD]['file_name'] 		= $rsD->Field("file_name");
						$gridTemp[$content_id]["data"][$indD]['mainCat'] = $category_of_data;
						$gridTemp[$content_id]["data"][$indD]['subCat'] = $category_sub;

						$gridTemp[$content_id]["data"][$indD]["file_duration"] = $rsD->Field("file_duration");
						$gridTemp[$content_id]["data"][$indD]["file_width"] = $rsD->Field("file_width");
						$gridTemp[$content_id]["data"][$indD]["file_height"] = $rsD->Field("file_height");

						if ($rsD->Field("category_of_data") == "mediaContent") {
							$type_of_data = "{{_" . $category_sub . "}}";
						} else $type_of_data = "";

						$gridTemp[$content_id]["data"][$indD]['category_of_data'] = "{{_" . $rsD->Field("category_of_data") . "}}";
						$gridTemp[$content_id]["data"][$indD]['type_of_data']	= $type_of_data;
						$gridTemp[$content_id]["data"][$indD]['file_name_cached'] = $rsD->Field("file_name");
						$file_name_ori = $rsD->Field("file_name_ori");

						$fileName = $rsD->Field("file_name");

						$gridTemp[$content_id]["data"][$indD]['file_name_ori'] = $file_name_ori;
						//$gridTemp[$content_id]["data"][$indD]['file_id'] 			= $rsD->Field("file_id");
						$gridTemp[$content_id]["data"][$indD]['mimetype'] = $rsD->Field("file_type");
						$gridTemp[$content_id]["data"][$indD]['identifier'] = $rsD->Field("identifier");

						$fileextensionArray = DocManager::findNameSurfixFromFilename($fileName);
						$fileextension = str_replace(".", "", $fileextensionArray["fileextension"]);

						$gridTemp[$content_id]["data"][$indD]["file_category"] = DocManager::getTypeExternalFile($fileextension);        //0:imazh, 1:doc, 2:media, 3:flash

						$gridTemp[$content_id]["data"][$indD]["extension"] = $fileextension;
						$gridTemp[$content_id]["data"][$indD]["file_size"] = DocManager::ByteSize($rsD->Field("file_size"));

						$gridTemp[$content_id]["data"][$indD]["ico_type"] = DocManager::formatMimeType($fileextension);
						$gridTemp[$content_id]["data"][$indD]['link_url'] = RC_FOLDER_NAME . $rsD->Field("identifier") . $rsD->Field("file_name");

						$gridTemp[$content_id]["data"][$indD]['relative_url'] = RC_FOLDER_NAME . $rsD->Field("identifier") . $rsD->Field("file_name");

						$gridTemp[$content_id]["data"][$indD]['file_type'] = $rsD->Field("file_type");
						$gridTemp[$content_id]["data"][$indD]['video_type'] = $gridTemp[$content_id]["data"][$indD]['file_type'];
						$gridTemp[$content_id]["data"][$indD]['stream_url'] = $gridTemp[$content_id]["data"][$indD]['link_url'];

						if (defined('RTMP_URL_ENABLED') && RTMP_URL_ENABLED == "Y") {

							if ($gridTemp[$content_id]["data"][$indD]['extension'] == "mp4") {

								$gridTemp[$content_id]["data"][$indD]['video_type'] = "rtmp/mp4";
								$gridTemp[$content_id]["data"][$indD]['relative_url'] = $rsD->Field("identifier") . $rsD->Field("file_name");

							} elseif ($gridTemp[$content_id]["data"][$indD]['extension'] == "mp3") {
								$gridTemp[$content_id]["data"][$indD]['video_type'] = "audio/mp3";

							} elseif ($gridTemp[$content_id]["data"][$indD]['extension'] == "webm")
								$gridTemp[$content_id]["data"][$indD]['video_type'] = "video/webm";
						} else {
							if ($gridTemp[$content_id]["data"][$indD]['extension'] == "mp4")
								$gridTemp[$content_id]["data"][$indD]['video_type'] = "video/mp4";
							if ($gridTemp[$content_id]["data"][$indD]['extension'] == "mp3")
								$gridTemp[$content_id]["data"][$indD]['video_type'] = "audio/mp3";
							if ($gridTemp[$content_id]["data"][$indD]['extension'] == "webm")
								$gridTemp[$content_id]["data"][$indD]['video_type'] = "video/webm";
						}

						$gridTemp[$content_id]["data"][$indD]['link_url_app'] = APP_URL . RC_FOLDER_NAME . $rsD->Field("identifier") . $rsD->Field("file_name");
						$gridTemp[$content_id]["data"][$indD]['stream_url_app'] = APP_URL . RC_FOLDER_NAME . $rsD->Field("identifier") . $rsD->Field("file_name");

						if (defined('RTMP_URL_ENABLED_SL') && RTMP_URL_ENABLED_SL == "Y") {
							if ($gridTemp[$content_id]["data"][$indD]['extension'] == "mp4") {
								$gridTemp[$content_id]["data"][$indD]['stream_url_app'] = STREAMING_URL_SL . $rsD->Field("identifier") . $rsD->Field("file_name");
							}
						}
						$this->SlidesExtended [$content_id][$category_of_data][$category_sub] = $gridTemp[$content_id]["data"][$indD];
						$gridD = array("data" => array(), "AllRecs" => "1");
						$gridD["data"][0] = $gridTemp[$content_id]["data"][$indD];

						
						
						
						WebApp::addVar("SlideGrid_" . $content_id . "_" . $category_of_data, $gridD);
						WebApp::addVar("SlideItemGrid_" . $content_id . "_" . $category_of_data . "_" . $category_sub, $gridD);
						
						if ($raID!="")
							$this->getRelatedRaDocuments($raID,$content_id);

						$indD++;
                }
                $rsD->MoveNext();
            }
        }
      
        if (isset($this->ci_collected["groupedType"]["SP"]) && count($this->ci_collected["groupedType"]["SP"]) > 0) {

            $gridTemp = array();

            $ids = implode(",", $this->ci_collected["groupedType"]["SP"]);
          //  $ids = $ids . "," . $this->appRelSt["MainNodeCiID"];
          


            $getFileSlideInfo = "SELECT content_id, if (category_of_data='mediaContent',1,0) as ord1,

										coalesce(category_of_data, '') 	as category_of_data,
										coalesce(type_of_data, '') 		as type_of_data,
										coalesce(file_name, '') 		as file_name,
										coalesce(file_name_ori, file_name) 	as file_name_ori,
										coalesce(file_id, '') 			as file_id,
										coalesce(file_type, '') 		as file_type,
										coalesce(file_size, '') 		as file_size,
										coalesce(identifier, '') 		as identifier,
										coalesce(file_duration, '') 		as file_duration,
										coalesce(file_width, '') 	as file_width,
										coalesce(file_height, '') as file_height

							FROM sp_data_doc 
						   WHERE content_id 	in  (" . $ids . ")
							 AND lng_id 		= '" . $this->lngId . "'
							 AND statusInfo 	= '".$this->thisModeCode."'
						ORDER BY content_id, ord1,category_of_data, type_of_data";

            $rsD = WebApp::execQuery($getFileSlideInfo);
            
         /*  echo "getCollectorSlideExtended<textarea>";
            print_r($rsD);
            echo "</textarea>";    */        
            
            
            $indD = 0;
            while (!$rsD->EOF()) {

                $content_id = $rsD->Field("content_id");
                $category_of_data = $rsD->Field("category_of_data");

                $category_sub 	= $rsD->Field("type_of_data");
                $file_name 		= $rsD->Field("file_name");
               
                if ( $file_name!="" && is_file(APP_PATH.RC_FOLDER_NAME.$rsD->Field("identifier").$rsD->Field("file_name")) ) {
                
					$gridTemp[$content_id]["data"][$indD]['mainCat'] = $category_of_data;
					$gridTemp[$content_id]["data"][$indD]['subCat'] = $category_sub;

					$gridTemp[$content_id]["data"][$indD]["lastUser"] = $rsD->Field("FirstName") . " " . $rsD->Field("SecondName");
					$gridTemp[$content_id]["data"][$indD]["version_id"] = $rsD->Field("version_id");
					$gridTemp[$content_id]["data"][$indD]["user_id"] = $rsD->Field("user_id");
					$gridTemp[$content_id]["data"][$indD]["timestamp"] = $rsD->Field("timestamp");

					$gridTemp[$content_id]["data"][$indD]["file_duration"] = $rsD->Field("file_duration");
					$gridTemp[$content_id]["data"][$indD]["file_width"] = $rsD->Field("file_width");
					$gridTemp[$content_id]["data"][$indD]["file_height"] = $rsD->Field("file_height");

					if ($rsD->Field("category_of_data") == "mediaContent") {
						$type_of_data = "{{_" . $category_sub . "}}";
					} else $type_of_data = "";

					$gridTemp[$content_id]["data"][$indD]['category_of_data'] = "{{_" . $rsD->Field("category_of_data") . "}}";
					$gridTemp[$content_id]["data"][$indD]['type_of_data'] = $type_of_data;
					$gridTemp[$content_id]["data"][$indD]['file_name'] = $rsD->Field("file_name");
					$gridTemp[$content_id]["data"][$indD]['file_name_cached'] = $rsD->Field("file_name");
					$file_name_ori = $rsD->Field("file_name_ori");

					$fileName = $rsD->Field("file_name");

					$gridTemp[$content_id]["data"][$indD]['file_name_ori'] = $file_name_ori;
					//$gridTemp[$content_id]["data"][$indD]['file_id'] 			= $rsD->Field("file_id");
					$gridTemp[$content_id]["data"][$indD]['mimetype'] = $rsD->Field("file_type");
					$gridTemp[$content_id]["data"][$indD]['identifier'] = $rsD->Field("identifier");

					$fileextensionArray = DocManager::findNameSurfixFromFilename($fileName);
					$fileextension = str_replace(".", "", $fileextensionArray["fileextension"]);

					$gridTemp[$content_id]["data"][$indD]["file_category"] = DocManager::getTypeExternalFile($fileextension);        //0:imazh, 1:doc, 2:media, 3:flash

					$gridTemp[$content_id]["data"][$indD]["extension"] = $fileextension;
					$gridTemp[$content_id]["data"][$indD]["file_size"] = DocManager::ByteSize($rsD->Field("file_size"));

					$gridTemp[$content_id]["data"][$indD]["ico_type"] = DocManager::formatMimeType($fileextension);
					$gridTemp[$content_id]["data"][$indD]['link_url'] = RC_FOLDER_NAME . $rsD->Field("identifier") . $rsD->Field("file_name");
					$gridTemp[$content_id]["data"][$indD]['relative_url'] = RC_FOLDER_NAME . $rsD->Field("identifier") . $rsD->Field("file_name");

					$gridTemp[$content_id]["data"][$indD]['file_type'] = $rsD->Field("file_type");
					$gridTemp[$content_id]["data"][$indD]['video_type'] = $gridTemp[$content_id]["data"][$indD]['file_type'];
					$gridTemp[$content_id]["data"][$indD]['stream_url'] = $gridTemp[$content_id]["data"][$indD]['link_url'];

					if (defined('RTMP_URL_ENABLED') && RTMP_URL_ENABLED == "Y") {

						if ($gridTemp[$content_id]["data"][$indD]['extension'] == "mp4") {

							$gridTemp[$content_id]["data"][$indD]['video_type'] = "rtmp/mp4";
							$gridTemp[$content_id]["data"][$indD]['relative_url'] = $rsD->Field("identifier") . $rsD->Field("file_name");

						} elseif ($gridTemp[$content_id]["data"][$indD]['extension'] == "mp3") {

							$gridTemp[$content_id]["data"][$indD]['video_type'] = "audio/mp3";

						} elseif ($gridTemp[$content_id]["data"][$indD]['extension'] == "webm")
							$gridTemp[$content_id]["data"][$indD]['video_type'] = "video/webm";
					} else {
						if ($gridTemp[$content_id]["data"][$indD]['extension'] == "mp4")
							$gridTemp[$content_id]["data"][$indD]['video_type'] = "video/mp4";
						if ($gridTemp[$content_id]["data"][$indD]['extension'] == "mp3")
							$gridTemp[$content_id]["data"][$indD]['video_type'] = "audio/mp3";
						if ($gridTemp[$content_id]["data"][$indD]['extension'] == "webm")
							$gridTemp[$content_id]["data"][$indD]['video_type'] = "video/webm";
					}

					$gridTemp[$content_id]["data"][$indD]['link_url_app'] = APP_URL . RC_FOLDER_NAME . $rsD->Field("identifier") . $rsD->Field("file_name");
					$gridTemp[$content_id]["data"][$indD]['stream_url_app'] = APP_URL . RC_FOLDER_NAME . $rsD->Field("identifier") . $rsD->Field("file_name");

					if (defined('RTMP_URL_ENABLED_SL') && RTMP_URL_ENABLED_SL == "Y") {
						if ($gridTemp[$content_id]["data"][$indD]['extension'] == "mp4") {
							$gridTemp[$content_id]["data"][$indD]['stream_url_app'] = STREAMING_URL_SL . $rsD->Field("identifier") . $rsD->Field("file_name");
						}
					}
					$this->SlidesExtended [$content_id][$category_of_data][$category_sub] = $gridTemp[$content_id]["data"][$indD];
					$gridD = array("data" => array(), "AllRecs" => "1");
					$gridD["data"][0] = $gridTemp[$content_id]["data"][$indD];

					WebApp::addVar("SlideGrid_" . $content_id . "_" . $category_of_data, $gridD);
					WebApp::addVar("SlideItemGrid_" . $content_id . "_" . $category_of_data . "_" . $category_sub, $gridD);
					
					
					if ($raID!="")
						$this->getRelatedRaDocuments($raID,$content_id);

					$indD++;
				}
                $rsD->MoveNext();
            }
        }        

    }	
    function getCollectorVirtualSlideExtended()
    {

        $gridD = array("data" => array(), "AllRecs" => "0");
        $indD = 0;

        if (isset($this->slide_structure_to_grid_sv)) {
            while (list($category, $tdata) = each($this->slide_structure_to_grid_sv)) {

                $gridD["data"][$indD]["cat_key"] = "$category";
                $gridD["data"][$indD]["cat_label"] = "{{_sl_$category}}";
                $gridD["data"][$indD]["typeOfDrop"] = "";
                $gridD["data"][$indD]["cat_label_desc"] = "{{_sl_" . $category . "_desc}}";

                $gridSD = array("data" => array(), "AllRecs" => "0");
                $indDS = 0;
                while (list($sub_cat_id, $el) = each($tdata)) {

                    $gridSD["data"][$indDS]["sub_cat_key"] = "$sub_cat_id";
                    $gridSD["data"][$indDS]["key_flag"] = $el["info"]["key"];
                    $gridSD["data"][$indDS]["sub_cat_label"] = $el["info"]["label"];
                    $gridSD["data"][$indDS]["sub_cat_help"] = $el["info"]["help"];
                    $gridSD["data"][$indDS]["recomandationFormat"] = $el["info"]["recomandationFormat"];
                    $gridSD["data"][$indDS++]["type_meta"] = $el["info"]["type_meta"];

                    $gridSDDD = array("data" => array(), "AllRecs" => "0");
                    $indDSDD = 0;
                    while (list($ind, $val) = each($el["subSubGridat"])) {
                        $gridSDDD["data"][$indDSDD]["label"] = $val["label"];
                        $gridSDDD["data"][$indDSDD]["help"] = $val["help"];
                        $gridSDDD["data"][$indDSDD]["type_meta"] = $val["type_meta"];
                        $gridSDDD["data"][$indDSDD]["cat_id_key"] = $val["cat_id"];
                        $gridSDDD["data"][$indDSDD]["extentions_Allowed"] = $val["extentions_Allowed"];
                        $gridSDDD["data"][$indDSDD]["getJsonRelated"] = $val["getJsonRelated"];

                        $gridSDDD["data"][$indDSDD]["recomandationFormat"] = $val["recomandationFormat"];

                        $gridSDDD["data"][$indDSDD++]["sub_cat_id_key"] = $val["sub_cat_id"];
                    }

                    $gridSDDD["AllRecs"] = count($gridSDDD["data"]);
                    WebApp::addVar("slideDropZoneStructureGrid_" . $category . "_" . $sub_cat_id, $gridSDDD);
                }

                $gridSD["AllRecs"] = count($gridSD["data"]);
                WebApp::addVar("gridSubStructureHtml" . $category, $gridSD);

                $tmp["data"][0] = $gridD["data"][$indD];
                $tmp["AllRecs"] = 1;

                WebApp::addVar("slidesGroupedGrid_" . $category, $tmp);
                $indD++;
			}
        }

        $gridD["AllRecs"] = count($gridD["data"]);
        WebApp::addVar("gridStructureHtml", $gridD);
        WebApp::addVar("gridStructureHtmlSV", $gridD);
        WebApp::addVar("category_of_drop_zone_file_SV", $gridD);
        $getFileSlideInfo = "SELECT content_id,

								if (category_of_data='mediaContent',1,0) as ord1,

								coalesce(category_of_data, '') 	as category_of_data,
								coalesce(type_of_data, '') 		as type_of_data,
								coalesce(file_name, '') 		as file_name,
								coalesce(file_name_ori, file_name) 	as file_name_ori,
								coalesce(file_id, '') 			as file_id,
								coalesce(file_type, '') 		as file_type,
								coalesce(file_size, '') 		as file_size,
								coalesce(identifier, '') 		as identifier,

								coalesce(file_type, '') 		as file_type,
								coalesce(file_duration, '') 		as file_duration,


								  coalesce(file_width, '') 	as file_width,
								  coalesce(file_height, '') as file_height,
								  coalesce(version_id, '') 	as version_id,
								  coalesce(user_id, '') 	as user_id,
								  coalesce(FirstName, '') 	as FirstName,
								  coalesce(SecondName, '') 	as SecondName,

								  coalesce(date_format(timestamp, '%d.%m.%Y %H:%i:%s'),'') as timestamp

							FROM sv_data_doc

									 LEFT JOIN users on sv_data_doc.user_id = users.UserId


						   WHERE content_id 	in  (" . $this->cidFlow . ")
							 AND lng_id 		= '" . $this->lngId . "'
							 AND statusInfo 	= '".$this->thisModeCode."'

							 ORDER BY content_id, ord1,category_of_data, type_of_data";


        $rsD = WebApp::execQuery($getFileSlideInfo);
        $indD = 0;
        while (!$rsD->EOF()) {

            $content_id = $rsD->Field("content_id");
            $category_of_data = $rsD->Field("category_of_data");

            $category_sub = $rsD->Field("type_of_data");

            $gridTemp[$content_id]["data"][$indD]['mainCat'] = $category_of_data;
            $gridTemp[$content_id]["data"][$indD]['subCat'] = $category_sub;

            $gridTemp[$content_id]["data"][$indD]["lastUser"] = $rsD->Field("FirstName") . " " . $rsD->Field("SecondName");
            $gridTemp[$content_id]["data"][$indD]["version_id"] = $rsD->Field("version_id");
            $gridTemp[$content_id]["data"][$indD]["user_id"] = $rsD->Field("user_id");
            $gridTemp[$content_id]["data"][$indD]["timestamp"] = $rsD->Field("timestamp");

            $gridTemp[$content_id]["data"][$indD]["file_duration"] = $rsD->Field("file_duration");
            $gridTemp[$content_id]["data"][$indD]["file_width"] = $rsD->Field("file_width");
            $gridTemp[$content_id]["data"][$indD]["file_height"] = $rsD->Field("file_height");

            if ($rsD->Field("category_of_data") == "mediaContent") {
                $type_of_data = "{{_" . $category_sub . "}}";
            } else $type_of_data = "";

            $gridTemp[$content_id]["data"][$indD]['category_of_data'] = "{{_" . $rsD->Field("category_of_data") . "}}";
            $gridTemp[$content_id]["data"][$indD]['type_of_data'] = $type_of_data;
            $gridTemp[$content_id]["data"][$indD]['file_name'] = $rsD->Field("file_name");
            $gridTemp[$content_id]["data"][$indD]['file_name_cached'] = $rsD->Field("file_name");
            $file_name_ori = $rsD->Field("file_name_ori");

            $fileName = $rsD->Field("file_name");

            $gridTemp[$content_id]["data"][$indD]['file_name_ori'] = $file_name_ori;
            $gridTemp[$content_id]["data"][$indD]['mimetype'] = $rsD->Field("file_type");
            $gridTemp[$content_id]["data"][$indD]['identifier'] = $rsD->Field("identifier");

            $fileextensionArray = DocManager::findNameSurfixFromFilename($fileName);
            $fileextension = str_replace(".", "", $fileextensionArray["fileextension"]);

            $gridTemp[$content_id]["data"][$indD]["file_category"] = DocManager::getTypeExternalFile($fileextension);        //0:imazh, 1:doc, 2:media, 3:flash

            $gridTemp[$content_id]["data"][$indD]["extension"] = $fileextension;
            $gridTemp[$content_id]["data"][$indD]["file_size"] = DocManager::ByteSize($rsD->Field("file_size"));

            $gridTemp[$content_id]["data"][$indD]["ico_type"] = DocManager::formatMimeType($fileextension);
            $gridTemp[$content_id]["data"][$indD]['link_url'] = RC_FOLDER_NAME . $rsD->Field("identifier") . $rsD->Field("file_name");

            $gridTemp[$content_id]["data"][$indD]['relative_url'] = RC_FOLDER_NAME . $rsD->Field("identifier") . $rsD->Field("file_name");

            $gridTemp[$content_id]["data"][$indD]['file_type'] = $rsD->Field("file_type");
            $gridTemp[$content_id]["data"][$indD]['video_type'] = $gridTemp[$content_id]["data"][$indD]['file_type'];
            $gridTemp[$content_id]["data"][$indD]['stream_url'] = $gridTemp[$content_id]["data"][$indD]['link_url'];

            if (defined('RTMP_URL_ENABLED') && RTMP_URL_ENABLED == "Y") {
                if ($gridTemp[$content_id]["data"][$indD]['extension'] == "mp4") {
                    $gridTemp[$content_id]["data"][$indD]['video_type'] = "rtmp/mp4";
                    $gridTemp[$content_id]["data"][$indD]['relative_url'] = $rsD->Field("identifier") . $rsD->Field("file_name");
                } elseif ($gridTemp[$content_id]["data"][$indD]['extension'] == "mp3") {
                    $gridTemp[$content_id]["data"][$indD]['video_type'] = "audio/mp3";
                } elseif ($gridTemp[$content_id]["data"][$indD]['extension'] == "webm")
                    $gridTemp[$content_id]["data"][$indD]['video_type'] = "video/webm";
            } else {
                if ($gridTemp[$content_id]["data"][$indD]['extension'] == "mp4")
                    $gridTemp[$content_id]["data"][$indD]['video_type'] = "video/mp4";
                if ($gridTemp[$content_id]["data"][$indD]['extension'] == "mp3")
                    $gridTemp[$content_id]["data"][$indD]['video_type'] = "audio/mp3";
                if ($gridTemp[$content_id]["data"][$indD]['extension'] == "webm")
                    $gridTemp[$content_id]["data"][$indD]['video_type'] = "video/webm";
            }
            $gridTemp[$content_id]["data"][$indD]['link_url_app'] = APP_URL . RC_FOLDER_NAME . $rsD->Field("identifier") . $rsD->Field("file_name");
            $gridTemp[$content_id]["data"][$indD]['stream_url_app'] = APP_URL . RC_FOLDER_NAME . $rsD->Field("identifier") . $rsD->Field("file_name");

            if (defined('RTMP_URL_ENABLED_SL') && RTMP_URL_ENABLED_SL == "Y") {
                if ($gridTemp[$content_id]["data"][$indD]['extension'] == "mp4") {
                    $gridTemp[$content_id]["data"][$indD]['stream_url_app'] = STREAMING_URL_SL . $rsD->Field("identifier") . $rsD->Field("file_name");
                }
            }
            $this->SlidesExtended [$content_id][$category_of_data][$category_sub] = $gridTemp[$content_id]["data"][$indD];
            $gridD = array("data" => array(), "AllRecs" => "1");
            $gridD["data"][0] = $gridTemp[$content_id]["data"][$indD];

            WebApp::addVar("SlideGrid_" . $content_id . "_" . $category_of_data, $gridD);
            WebApp::addVar("SlideItemGrid_" . $content_id . "_" . $category_of_data . "_" . $category_sub, $gridD);

            $rsD->MoveNext();
        }
    }    
    function getStructuredSlides()
    {
		$this->slide_structure_to_grid = array
		(
			"mediaContent" => array(

				"1" => array(
					"info" => array(
						"label" => "{{_video_mp4}}",
						"help" => "{{_video_mp4_desc}}",
						"type_meta" => "pub",
						"recomandationFormat" => "",
						"layout_grid" => "single",
						"key" => "video"
					),
					"subSubGridat" => array(
						"1" => array(
							"label" => "{{_mediaContent_video_mp4}}",
							"help" => "{{_mediaContent_video_mp4_desc}}",
							"type_meta" => "pub",
							"extentions_Allowed" => ".mp4",

							"cat_id" => "mediaContent",
							"sub_cat_id" => "VideoMp4"
						)
					)
				),
				"2" => array(
					"info" => array(

						"label" => "{{_image_audio}}",
						"help" => "{{_image_audio_desc}}",
						"type_meta" => "pub",
						"recomandationFormat" => "Image (960px X 720px)",
						"layout_grid" => "multi",
						"key" => "image_audio"

					),
					"subSubGridat" => array(
						"1" => array(
							"label" => "{{_mediaContent_audio}}",
							"help" => "{{_mediaContent_audio_desc}}",
							"type_meta" => "pub",
							"extentions_Allowed" => ".mp3",

							"cat_id" => "mediaContent",
							"sub_cat_id" => "Audio"
						),
						"2" => array(
							"label" => "{{_mediaContent_image}}",
							"help" => "{{_mediaContent_image_desc}}",
							"type_meta" => "pub",
							"extentions_Allowed" => ".png, .jpg, .jpeg",

							"cat_id" => "mediaContent",
							"sub_cat_id" => "Image"
						)
					)

				),
				"3" => array(
					"info" => array(

						"label" => "{{_html_package}}",
						"help" => "{{_html_package_desc}}",
						"type_meta" => "pub",
						"recomandationFormat" => "",
						"layout_grid" => "single",
						"key" => "html_package"
					),

					"subSubGridat" => array(
						"1" => array(
							"label" => "{{_html_package_html}}",
							"help" => "{{_html_package_html_desc}}",
							"type_meta" => "pub",
							"recomandationFormat" => "",
							"cat_id" => "mediaContent",
							"sub_cat_id" => "HTML"
						)
					)

				),
			),
			"Caption" => array(
				"1" => array(
					"info" => array(

						"label" => "{{_vtt}}",
						"help" => "{{_vtt_desc}}",
						"type_meta" => "pub",
						"recomandationFormat" => "",
						"getJsonRelated" => "yes",
						"layout_grid" => "mixed",
						"key" => "vtt",

					),
					"subSubGridat" => array(
						"1" => array(

							"label" => "{{_vtt_info}}",
							"help" => "{{_vtt_info_desc}}",
							"type_meta" => "pub",
							"recomandationFormat" => "",
							"extentions_Allowed" => ".vtt",
							"getJsonRelated" => "yes",
							"cat_id" => "Caption",
							"sub_cat_id" => "vtt"
						)
					)
				)
			),
			"Annotation" => array(
				"1" => array(
					"info" => array(

						"label" => "{{_vtt}}",
						"help" => "{{_vtt_desc}}",
						"type_meta" => "pub",
						"recomandationFormat" => "",
						"layout_grid" => "mixed",
						"getJsonRelated" => "yes",
						"key" => "vtt"

					),
					"subSubGridat" => array(
						"1" => array(

							"label" => "{{_vtt_info}}",
							"help" => "{{_vtt_info_desc}}",
							"type_meta" => "pub",
							"cat_id" => "Annotation",
							"recomandationFormat" => "",
							"extentions_Allowed" => ".vtt",
							"getJsonRelated" => "yes",
							"sub_cat_id" => "vtt"
						)
					)
				)
			),
			"smallImage" => array(
				"1" => array(
					"info" => array(

						"label" => "{{_thumbnail_player}}",
						"help" => "{{_thumbnail_player_desc}}",
						"recomandationFormat" => "",
						"type_meta" => "pub",
						"key" => "Image"
					),
					"subSubGridat" => array(
						"1" => array(

							"label" => "{{_image}}",
							"help" => "{{_image_desc}}",
							"type_meta" => "pub",
							"cat_id" => "smallImage",
							"recomandationFormat" => "(200px X 100px)",
							"extentions_Allowed" => ".png, .jpg, .jpeg",
							"sub_cat_id" => "Image"
						)
					)
				)
			),
			"lecturer" => array(
				"1" => array(
					"info" => array(

						"label" => "{{_video_lecturer}}",
						"help" => "{{_video_lecturer_desc}}",
						"recomandationFormat" => "",
						"type_meta" => "pub",
						"key" => "Video"
					),
					"subSubGridat" => array(
						"1" => array(

							"label" => "{{_video_lecturer_info}}",
							"help" => "{{_video_lecturer_desc}}",
							"type_meta" => "pub",
							"cat_id" => "lecturer",
							"recomandationFormat" => "",
							"extentions_Allowed" => ".mp4",
							"sub_cat_id" => "Video"
						)
					)
				)
			),
			"slideFromPpt" => array(
				"1" => array(
					"info" => array(

						"label" => "{{_slide_from_ppt}}",
						"help" => "{{_slide_from_ppt_desc}}",
						"recomandationFormat" => "",
						"type_meta" => "syst",
						"key" => "ppt"
					),
					"subSubGridat" => array(
						"1" => array(

							"label" => "{{_slide_from_ppt_info}}",
							"help" => "{{_slide_from_ppt_info_desc}}",
							"type_meta" => "syst",
							"cat_id" => "slideFromPpt",
							"recomandationFormat" => "",
							"extentions_Allowed" => ".ppt, .pptx",
							"sub_cat_id" => "ppt"
						)
					)
				)
			)
		); 
		$this->slide_structure_to_grid_sc = array
		(
			"presentation_cover_small" => array(
				"1" => array(
					"info" => array(

						"label" => "{{_thumbnail_player}}",
						"help" => "{{_thumbnail_player_desc}}",
						"recomandationFormat" => "(200px X 100px)",
						"type_meta" => "pub",
						"key" => "Image"
					),
					"subSubGridat" => array(
						"1" => array(

							"label" => "{{_thumbnail}}",
							"help" => "{{_thumbnail}}",
							"type_meta" => "pub",

							"cat_id" => "smallImage",
							"extentions_Allowed" => ".png, .jpg, .jpeg",
							"sub_cat_id" => "Image"
						)
					)
				)
			),
			"presentation_cover_big" => array(
				"1" => array(
					"info" => array(

						"label" => "{{_big_img}}",
						"help" => "{{_big_img}}",
						"type_meta" => "pub",
						"recomandationFormat" => " (960px X 720px)",
						"key" => "Image"
					),
					"subSubGridat" => array(
						"1" => array(

							"label" => "{{_image}}",
							"help" => "{{_image_desc}}",
							"type_meta" => "pub",

							"cat_id" => "lecturer",
							"extentions_Allowed" => ".png, .jpg, .jpeg",
							"sub_cat_id" => "Image"
						)
					)
				)
			),
			"themePpt" => array(
				"1" => array(
					"info" => array(

						"label" => "{{_presentation_theme}}",
						"help" => "{{_presentation_theme_desc}}",
						"recomandationFormat" => " (960px X 720px)",
						"type_meta" => "pub",
						"key" => "ppt"
					),
					"subSubGridat" => array(
						"1" => array(

							"label" => "{{_ppt_template}}",
							"help" => "{{_ppt_template_desc}}",
							"type_meta" => "pub",
							"cat_id" => "themePpt",

							"extentions_Allowed" => ".ppt, .pptx, .pdf, .jpeg, .png, .gif, .jpg",
							"sub_cat_id" => "ppt"
						)
					)
				)
			),
			"slideFromPpt" => array(
				"1" => array(
					"info" => array(

						"label" => "{{_backup_from_ppt}}",
						"help" => "{{_backup_from_ppt_desc}}",
						"recomandationFormat" => "",
						"type_meta" => "syst",
						"key" => "ppt"
					),
					"subSubGridat" => array(
						"1" => array(

							"label" => "{{_slide_from_ppt_info}}",
							"help" => "{{_slide_from_ppt_info_desc}}",
							"type_meta" => "syst",
							"cat_id" => "slideFromPpt",

							"extentions_Allowed" => ".ppt, .pptx",
							"sub_cat_id" => "ppt"
						)
					)
				)
			),
			"trascription" => array(
				"1" => array(
					"info" => array(

						"label" => "{{_trascription}}",
						"help" => "{{_trascription}}",
						"recomandationFormat" => "",
						"type_meta" => "syst",
						"key" => "exel"
					),
					"subSubGridat" => array(
						"1" => array(

							"label" => "{{_trascription}}",
							"help" => "{{_trascription}}",
							"type_meta" => "syst",
							"cat_id" => "trascription",

							"extentions_Allowed" => ".xlsx,.xls",
							"sub_cat_id" => "xls"
						)
					)
				)
			)
		); 
		$this->slide_structure_nomenclature = array
		(
			"mediaContent" => array(
				"VideoMp4" => "mediaContent-VideoMp4-xxx.extension",
				"Audio" => "mediaContent-Audio-xxx.extension",
				"HTML" => "mediaContent-HTML-xxx.extension",
				"Image" => "mediaContent-Image-xxx.extension"
			),
			"Caption" => array("vtt" => "Caption-vtt-xxx.extension"),
			"Annotation" => array("vtt" => "Annotation-vtt-xxx.extension"),

			"smallImage" => array("Image" => "smallImage-xxx.extension"),

			"lecturer" => array("Video" => "lecturer-Video-xxx.extension",
				"Image" => "lecturer-Image-xxx.extension"
			),
			"slideFromPpt" => array("ppt" => "slideFromPpt-ppt-xxx.extension"),
			"trascription" => array("xls" => "trascription-xls-xxx.extension")
		);	
		$this->slide_structure = array
		(
			"mediaContent" => array("VideoMp4" => "SC1",
				"Audio" => "SC3",
				"Image" => "SC4",
				"HTML" => "SC5"
			),
			"Caption" => array("vtt" => "SB1"),
			"Annotation" => array("vtt" => "AN1"),
			"smallImage" => array("Image" => "IM1"),
			"lecturer" => array("Video" => "LP1",
				"Image" => "LP2"
			),
			"slideFromPpt" => array("ppt" => "BK1"),
			"trascription" => array("xls" => "EXL")
		);
    	$this->slide_structure_pub = array
		(
			"mediaContent" => array(
				"VideoMp4" => "pub",
				"Audio" => "pub",
				"HTML" => "pub",
				"Image" => "pub"
			),
			"Caption" => array("vtt" => "pub"),
			"Annotation" => array("vtt" => "pub"),
			"smallImage" => array("Image" => "pub"),
			"lecturer" => array("Video" => "pub",
				"Image" => "pub"
			),
			"slideFromPpt" => array("ppt" => "syst")
		);		
    }
    function getCiNeededVar($contentToFindProp = "")
    {
        global $session;
        if ($contentToFindProp == "") {
            if (isset($this->lecture_id) && $this->lecture_id > 0)
					$contentToFindProp = $this->lecture_id;
            else	$contentToFindProp = $session->Vars["contentId"];
        }

        if (!isset($this->KeywordsDataPredefined)) {
            $this->getKeywordsData();
        }
        if (!isset($this->mixedNeededData[$contentToFindProp])) {

            $grid_id = $contentToFindProp;

            $workingCi = new CiManagerFe($contentToFindProp, $session->Vars["lang"]);
            $prop = array();

            if ($contentToFindProp == $this->cidFlow) {
                $workingCi->getDocProperties();

                $this->mixedNeededData[$contentToFindProp]["DC"] = $workingCi->properties_structured["DC"];
                $this->mixedNeededData[$contentToFindProp]["EX"] = $workingCi->properties_structured["EX"];
                $this->mixedNeededData[$contentToFindProp]["KW"] = $workingCi->properties_structured["KW"];
                $this->mixedNeededData[$contentToFindProp]["TiContent"] = $workingCi->properties_structured["TiContent"];
                if (isset($workingCi->propertiesGrouped["OPT"]) && $workingCi->properties_structured["OPT"] != "") {
                    $this->mixedNeededData[$contentToFindProp]["OPT"] = $workingCi->properties_structured["OPT"];
                }

                if ($this->mixedNeededData[$contentToFindProp]["DC"] && isset($this->mixedNeededData[$contentToFindProp]["EX"]))
                    $this->MainGridData = array_merge($this->mixedNeededData[$contentToFindProp]["DC"], $this->mixedNeededData[$contentToFindProp]["EX"]);
               
                $this->MainGridData["doctype_description"] = $this->appRelSt["doctype_description"][$grid_id];
                $this->MainGridData["cidFlowItm"] = $this->cidFlow;

                $this->getCiMultiOrSingleKewRelations($contentToFindProp);
                $workingCi->parseDocumentToDisplayPreviewMode();

                if (isset($workingCi->properties_structured["KW"])) {
                    while (list($famId, $KwData) = each($workingCi->properties_structured["KW"])) {
                        if (isset($KwData["ids"]) && count($KwData["ids"]) > 0) {
                            $gridD = array("data" => array(), "AllRecs" => "0");
                            $indD = 0;
                            while (list($kid, $kname) = each($KwData["ids"])) {
                                if ($kname != "") {
                                    $gridD["data"][$indD]["kid"] = $kid;
                                    $gridD["data"][$indD++]["kname"] = $kname;
                                }
                            }
                            $gridD["AllRecs"] = count($gridD["data"]);
                            WebApp::addVar("kw_" . $famId . "_Grid", $gridD);
                        }

                        $gridD = array("data" => array(), "AllRecs" => "0");
                        $indD = 0;
                        if (isset($this->KeywordsDataPredefined["GrKwToDisplays_".$famId]["data"])) {
                        reset($this->KeywordsDataPredefined["GrKwToDisplays_".$famId]["data"]);
                        while (list($indexK, $dataA) = each($this->KeywordsDataPredefined["GrKwToDisplays_" . $famId]["data"])) {
                            $gridD["data"][$indexK] = $dataA;
                            $gridD["data"][$indexK]["sel"] = "";
                            if (is_array($KwData["ids"]) && array_key_exists($dataA["kw_id"], $KwData["ids"])) {
                                //	if (in_array($gridD["data"][$indexK]["kid"],$dataA))
                                $gridD["data"][$indexK]["sel"] = " selected=\"selected\"";
                            }
                        }}
                        $gridD["AllRecs"] = count($gridD["data"]);
                        WebApp::addVar("fullKw_" . $famId . "_" . $contentToFindProp . "_Grid", $gridD);
                    }
                }
                if (isset($workingCi->properties_structured["TiContent"]) && $workingCi->properties_structured["TiContent"] != "") {
                    $gridD = array("data" => array(), "AllRecs" => "0");
                    $gridD["data"][0]['templateHtml'] = $workingCi->properties_structured["TiContent"];
                    $gridD["AllRecs"] = count($gridD["data"]);
                    WebApp::addVar("TemplateGrid", $gridD);
                }
            } else {

                $workingCi->getDocProperties();
                $this->mixedNeededData[$contentToFindProp]["DC"] = $workingCi->properties_structured["DC"];
                $this->mixedNeededData[$contentToFindProp]["DC"]["doctype_description"] = $this->appRelSt["doctype_description"][$contentToFindProp];

                if (isset($workingCi->properties_structured["OPT"]) && $workingCi->properties_structured["OPT"] != "") {
                    $this->mixedNeededData[$contentToFindProp]["OPT"] = $workingCi->properties_structured["OPT"];
                }
                $this->mixedNeededData[$contentToFindProp]["EX"] = $workingCi->properties_structured["EX"];
                $this->mixedNeededData[$contentToFindProp]["KW"] = $workingCi->properties_structured["KW"];
            }
        }
    }
    
    function getKeywordsData()
    {
        if (!isset($this->KeywordsDataPredefined)) {

            $familyToDisplays = array();

            $GrfamilyDataToDisplays = array("data" => array(),"AllRecs" => "0");
            $elems_stru_Kw_to_javscript = "";

            require_once(INCLUDE_KW_AJAX_PATH . 'KwManager.Base.class.php');
            $KwObj = new KwManagerFamily($session->Vars["ses_userid"], $session->Vars["lang"]);

            $GrSourcePredefined = array("data" => array(),"AllRecs" => "0");
            $FamilyDataSourceArray = $KwObj->getAllFamilyData('so');
            $indSo = 0;
            if (count($FamilyDataSourceArray) > 0) {

                while (list($grID, $infoGrArr) = each($FamilyDataSourceArray)) {

                    if (count($infoGrArr["ids"]) > 0 && $grID == 1) {
                        while (list($idFamily, $descriptionFamily) = each($infoGrArr["ids"])) {

                            $KwObj->setTreePositionProperties("0," . $idFamily);

                            $KwObjItemSo = $KwObj->setKwObjItem($KwObj->family_type_id);
                            $KwObjItemSo->setTreePositionProperties("0," . $idFamily);
                            $dataItem = $KwObjItemSo->generateList();
                            if (count(dataItem) > 0) {
                                while (list($idkw, $vels) = each($dataItem)) {
                                    $GrSourcePredefined["data"][$indSo]["source_id"] = $idkw;
                                    $GrSourcePredefined["data"][$indSo]["source_label"] = $vels;
                                    $indSo++;
                                }
                            }
                        }
                    }
                }
            }

            IF (count($GrSourcePredefined["data"]) > 0)
                $GrSourcePredefined["AllRecs"] = count($GrSourcePredefined["data"]);
            WebApp::addVar("GrSourcePredefined", $GrSourcePredefined);

            $this->KeywordsDataPredefined["GrSourcePredefined"] = $GrSourcePredefined;
            $GrAuthorPredefined = array("data" => array(),"AllRecs" => "0");
            $FamilyDataSourceArray = $KwObj->getAllFamilyData('au');

            $allAuthorsTmp = array();
            $allAuthorsTmp = '';
            $indSo = 0;
            if (count($FamilyDataSourceArray) > 0) {

                while (list($grID, $infoGrArr) = each($FamilyDataSourceArray)) {

                    if (count($infoGrArr["ids"]) > 0 && $grID == 1) {
                        while (list($idFamily, $descriptionFamily) = each($infoGrArr["ids"])) {


                            $KwObj->setTreePositionProperties("0," . $idFamily);

                            $KwObjItemSo = $KwObj->setKwObjItem($KwObj->family_type_id);
                            $KwObjItemSo->setTreePositionProperties("0," . $idFamily);

                            $dataItem = $KwObjItemSo->generateList();
                            if (count(dataItem) > 0) {

                                //$allAuthors = implode(",",$dataItem);
                                while (list($idkw, $vels) = each($dataItem)) {

                                    $elemt = explode(",", $idkw);
                                    $family = $elemt[0];
                                    $kw_id = $elemt[1];

                                    $allAuthorsTmp [] = "'" . trim($vels) . "'";
                                    $GrAuthorPredefined["data"][$indSo]["famid"] = $family;
                                    $GrAuthorPredefined["data"][$indSo]["source_id"] = $kw_id;
                                    $GrAuthorPredefined["data"][$indSo]["source_label"] = $vels;
                                    $indSo++;
                                }
                            }
                        }
                    }
                }
            }

            if ($allAuthorsTmp > 0) {
                $allAuthors = implode(",", $allAuthorsTmp);
                WebApp::addVar("GrAuthorPredefinedToGuessBy", $allAuthors);
            }

            IF (count($GrAuthorPredefined["data"]) > 0)
                $GrAuthorPredefined["AllRecs"] = count($GrAuthorPredefined["data"]);
            WebApp::addVar("GrAuthorPredefined", $GrAuthorPredefined);

            $this->KeywordsDataPredefined["GrAuthorPredefined"] = $GrAuthorPredefined;


            $FamilyDataArray = $KwObj->getAllFamilyData('li');
            $indF = 0;
            $indFG = 0;
            if (count($FamilyDataArray) > 0) {

                $familyDataMinToDisplays = array("data" => array(), "AllRecs" => "0");
                $indFFF = 0;

                while (list($grID, $infoGrArr) = each($FamilyDataArray)) {

                    if (count($infoGrArr["ids"]) > 0 && $grID != 5 && $grID != 6) {

                        $GrfamilyDataToDisplays["data"][$indFG]["GrId"] = $grID;
                        $GrfamilyDataToDisplays["data"][$indFG]["Grname"] = $infoGrArr["desc"];

                        $indFG++;

                        $familyDataToDisplays = array("data" => array(), "AllRecs" => "0");
                        $indF = 0;

                        while (list($idFamily, $descriptionFamily) = each($infoGrArr["ids"])) {

                            //$familyToDisplays [$idFamily] = $idFamily;

                            $familyDataToDisplays["data"][$indF]["id"] = $idFamily;
                            $familyDataToDisplays["data"][$indF]["name"] = $descriptionFamily;

                            $familyToDisplays [$idFamily] = $familyDataToDisplays["data"][$indF];

                            $familyDataToDisplays["data"][$indF]["kws"] = "";
                            $familyDataToDisplays["data"][$indF]["kwsSel"] = "";
                            $familyDataToDisplays["data"][$indF]["isSelectet"] = "";

                            $tmpAuthors["data"][0]["ID"] = $idFamily;

                            $KwObj->setTreePositionProperties("0," . $idFamily);

                            $KwObjItemSo = $KwObj->setKwObjItem($KwObj->family_type_id);
                            $KwObjItemSo->setTreePositionProperties("0," . $idFamily);
                            $dataItem = $KwObjItemSo->generateList();

                            $tmpLst = array("data" => array(), "AllRecs" => "0");
                            $indLst = 0;

                            $tmpToJavascript = array();

                            if (count($dataItem) > 0) {
                                while (list($idkw, $vels) = each($dataItem)) {

                                    $tmpLst["data"][$indLst]["idkw"] = $idkw;
                                    $elemt = explode(",", $idkw);
                                    $tmpLst["data"][$indLst]["fam_id"] = $elemt[0];
                                    $tmpLst["data"][$indLst]["kw_id"] = $elemt[1];
                                    $tmpLst["data"][$indLst++]["kw_name"] = $vels;

                                    $familyDataToDisplays["data"][$indF]["fam_id"] = $elemt[0];
                                    //$familyDataToDisplays["data"][$indF]["isSelectet"] = "selected=\"selected\"";
                                    $tmpToJavascript[] = "'" . addslashes($vels) . "'";
                                }
                            }
                            if (isset($this->mixedNeededData[$this->lecture_id]["KW"][$idFamily]["ids"])
                                &&
                                count($this->mixedNeededData[$this->lecture_id]["KW"][$idFamily]["ids"]) > 0
                            ) {
                                $familyDataToDisplays["data"][$indF]["kwsSel"] = implode(",", $this->mixedNeededData[$this->lecture_id]["KW"][$idFamily]["ids"]);
                            }
                            IF (count($tmpLst["data"]) > 0) {
                                $tmpLst["AllRecs"] = count($tmpLst["data"]);
                                $familyData["data"][$indF]["name_kw"] = $tmpLst["data"][$indF]["nm"];
                                $familyDataToDisplays["data"][$indF]["kws"] = implode(",", $tmpToJavascript);
                            }
                            //shih tabelen qe te ndryshosh dc tek kw_family
                            WebApp::addVar("GrKwToDisplays_$idFamily", $tmpLst);
                            $this->KeywordsDataPredefined["GrKwToDisplays_" . $idFamily] = $tmpLst;
                            $familyDataMinToDisplays["data"][$indFFF++] = $familyDataToDisplays["data"][$indF];
                            $indF++;
                        }
                        IF (count($familyDataToDisplays["data"]) > 0)
                            $familyDataToDisplays["AllRecs"] = count($familyDataToDisplays["data"]);

                        $this->KeywordsDataPredefined["KwStrToDisplaysGrid_" . $grID] = $familyDataToDisplays;
                        WebApp::addVar("KwStrToDisplaysGrid_" . $grID, $familyDataToDisplays);
                        $this->KwStrToDisplaysGrid["grID"] = $familyDataToDisplays["data"];
                    }
                }
            }

            IF (count($familyDataMinToDisplays["data"]) > 0)
                $familyDataMinToDisplays["AllRecs"] = count($familyDataMinToDisplays["data"]);
            WebApp::addVar("familyDataToDisplaysGrid", $familyDataMinToDisplays);
            $this->KeywordsDataPredefined["familyDataToDisplaysGrid"] = $familyDataMinToDisplays;
            IF (count($GrfamilyDataToDisplays["data"]) > 0)
                $GrfamilyDataToDisplays["AllRecs"] = count($GrfamilyDataToDisplays["data"]);
            WebApp::addVar("GrfamilyDataToDisplays", $GrfamilyDataToDisplays);
            $this->familyToDisplays = $familyToDisplays;
        }
    }	

    function getRelatedRaDocuments($raID,$relatedToCis)
    {

		global $session;
        $getChecked = "";
        if ($relatedToCis == "") $relatedToCis = $this->cidFlow;
        if (isset($raID) && $raID > 0 && isset($relatedToCis) && $relatedToCis > 0) {
            //po kapim related documents
            $coord = $this->appRelSt["LECTURE_RELATED_INFO"][$raID]["coord"];
			$joinRelatedDoc = "
					 JOIN ci_keyword_ci
							ON content.content_id = ci_keyword_ci.content_id_rel 
						   AND ci_keyword_ci.content_id = '" . $relatedToCis . "' 
						   AND ci_keyword_ci.lng_id = '" . $this->lngId . "' 
						   AND ci_keyword_ci.statusInfo = '".$this->thisModeCode."'";
        } else {
           return;
        }
        
        if (!isset($this->itemCollectorRelated[$raID . "_" . $relatedToCis])) {
        	//echo "GETRELATEDRADOCUMENTS:$raID:$all:$relatedToCis<br>";

			$BoConditionCI = "";
			if ($this->thisModeCode=="0") { //a paaprovuar
				$BoConditionCI = "
							  AND content.state" . $this->lang . " not in (0,5,7)";		
			} else {
				$BoConditionCI = "
							  AND content.state" . $this->lang . " not in (0,5,7)
							  AND content.published" . $this->lang . " = 'Y'";
			}


			$sql_con = "SELECT content.content_id, content.ci_type, coalesce(doctype_description,'') as doctype_description,
								   content.title" . $this->lang . " as ci_title,
								   coalesce(content.searchable,'') as searchable,
								   coalesce(content.doc_source" . $this->lang . ",'') as source,
								   coalesce(content.source_author" . $this->lang . ",'') as source_author,
								   coalesce(content.description" . $this->lang . $this->thisMode . ",'') as description,
								   coalesce(content.imageSm_id,'') as imageSm_id,
								   coalesce(content.imageBg_id,'') as imageBg_id " . $getChecked . ",

								   coalesce(ecc_reference*1, orderContent) as ordF, ecc_reference

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
														AND profil_rights.profil_id in (" . $this->tip . ")
													)
							  JOIN document_types ON document_types.doctype_name = content.ci_type

							  " . $joinRelatedDoc . "

							LEFT JOIN ci_elearning_extended on content.content_id = ci_elearning_extended.content_id
								 AND ci_elearning_extended.lng_id = '" . $this->lngId . "' AND ci_elearning_extended.statusInfo = '".$this->thisModeCode."'


							WHERE content.id_zeroNivel 		= '" . $coord[0] . "'
							  AND content.id_firstNivel 	= '" . $coord[1] . "'
							  AND content.id_secondNivel	= '" . $coord[2] . "'
							  AND content.id_thirdNivel 	= '" . $coord[3] . "'
							  AND content.id_fourthNivel 	= '" . $coord[4] . "'
							  AND orderContent!=0

							  ".$BoConditionCI."

						 GROUP BY content.content_id
						 ORDER BY ordF asc, ecc_reference";

			$rs = WebApp::execQuery($sql_con);
			$itemGrid = array("data" => array(), "AllRecs" => "0");
			$ind = 0;
			while (!$rs->EOF()) {

				$content_id = $rs->Field("content_id");
				$ci_type = $rs->Field("ci_type");

				$this->ci_collected["groupedType"][$ci_type][$content_id] = $content_id;
				
				$tmpInfoArray["groupedType"][$ci_type][$content_id] = $content_id;
				if (isset($raID) && $raID > 0 && $all == "all") {
					$tmpInfoArray["list"][$content_id]["selected"] = $rs->Field("selected");
					if ($tmpInfoArray["list"][$content_id]["selected"] == "selected")
						$tmpInfoArray["list"][$content_id]["isSelect"] = " checked=\"checked\"";
					else    $tmpInfoArray["list"][$content_id]["isSelect"] = "";
				}

				$tmpInfoArray["list"][$content_id]["CID"] = $content_id;
				
				$this->itemCollectorRelatedIds[$raID . "_" . $relatedToCis][$content_id] = $content_id;

				$tmpInfoArray["list"][$content_id]["searchable"] = $rs->Field("searchable");
				$tmpInfoArray["list"][$content_id]["ci_title"] = $rs->Field("ci_title");
				$tmpInfoArray["list"][$content_id]["ci_type"] = $rs->Field("ci_type");
				$tmpInfoArray["list"][$content_id]["ci_type_desc"] = $rs->Field("doctype_description");

				$tmpInfoArray["list"][$content_id]["source"] = $rs->Field("source");
				$tmpInfoArray["list"][$content_id]["source_author"] = $rs->Field("source_author");
				$tmpInfoArray["list"][$content_id]["ew_source_author"] = $rs->Field("source_author");

				$tmpInfoArray["list"][$content_id]["description"] = $rs->Field("description");

				$tmpInfoArray["list"][$content_id]["imageSm_id"] = $rs->Field("imageSm_id");
				$tmpInfoArray["list"][$content_id]["imageBg_id"] = $rs->Field("imageBg_id");
				$tmpInfoArray["list"][$content_id]["doctype_description"] = $rs->Field("doctype_description");

				$tmpInfoArray["grid"][$ind++] = $content_id;
				$rs->MoveNext();
			}
			if (isset($tmpInfoArray["groupedType"]) && count($tmpInfoArray["groupedType"]) > 0) {
				$groupedType = $tmpInfoArray["groupedType"];
				while (list($ci_type_grouped, $ci_type_ids) = each($groupedType)) {

					$idsCiGroupedByType = implode(",", $ci_type_ids);
					$properties_extended = array();

					if ($ci_type_grouped == "EI") $properties_extended = CiManagerFe::getEIMultiProperties($this->thisModeCode, $idsCiGroupedByType, $this->lang, $this->lngId);
					elseif ($ci_type_grouped == "ED") $properties_extended = CiManagerFe::getEDMultiProperties($this->thisModeCode, $idsCiGroupedByType, $this->lang, $this->lngId);
					elseif ($ci_type_grouped == "LK") $properties_extended = CiManagerFe::getLKMultiProperties($this->thisModeCode, $idsCiGroupedByType, $this->lang, $this->lngId);
					elseif ($ci_type_grouped == "PL") $properties_extended = CiManagerFe::getPLMultiProperties($this->thisModeCode, $idsCiGroupedByType, $this->lang, $this->lngId);
					elseif ($ci_type_grouped == "LB") $properties_extended = CiManagerFe::getLBMultiProperties($this->thisModeCode, $idsCiGroupedByType, $this->lang, $this->lngId);
					elseif ($ci_type_grouped == "SP") $properties_extended = $this->getSPMultiProperties($this->thisModeCode, $idsCiGroupedByType, $this->lang, $this->lngId);
					elseif ($ci_type_grouped == "RI") $properties_extended = CiManagerFe::getRIMultiProperties($this->thisModeCode, $idsCiGroupedByType, $this->lang, $this->lngId);
					if (count($properties_extended) > 0) {
						//merge general  prop with extended prop
						while (list($ciExtendedID, $ciExtendedIDprop) = each($properties_extended)) {
							if (isset($tmpInfoArray["list"][$ciExtendedID])) {
								if (isset($ciExtendedIDprop["filename"])) {
									$ciExtendedIDprop["mim_icon"] = $this->ReturnMimetypeIcon($ciExtendedIDprop["filename"]);
								}
								$tmpInfoArray["list"][$ciExtendedID] = array_merge($tmpInfoArray["list"][$ciExtendedID], $ciExtendedIDprop);
								$ggg = array();
								$ggg["data"][0] = $tmpInfoArray["list"][$ciExtendedID];
								$ggg["data"][0]["cidFlowItm"] = $ciExtendedID;
								$ggg["AllRecs"] = count($ggg["data"]);
								WebApp::addVar("ci_in_list_grid_" . $ciExtendedID, $ggg);
							}
						}
					}
				}
			}
			$this->nodeDocuments = $tmpInfoArray["list"];
			if (isset($tmpInfoArray["grid"]) && $tmpInfoArray["grid"] > 0) {
				while (list($ind, $cidi) = each($tmpInfoArray["grid"])) {
					$itemGrid["data"][$ind] = $tmpInfoArray["list"][$cidi];
				}
			}
			$itemGrid["AllRecs"] = count($itemGrid["data"]);
			WebApp::addVar("RA_Grid" . $raID . "_" . $relatedToCis, $itemGrid);
			$this->itemCollectorRelated[$raID . "_" . $relatedToCis]["data"] = $itemGrid["data"];
			//$this->ci_collected["groupedType"] = $tmpInfoArray["groupedType"];
       	}
    }    
	function secondsToTime($seconds)
	{
		// extract hours
		$obj = array(
			"h" => 0,
			"m" => 0,
			"s" => 0,
		);
		
		$hours = floor($seconds / (60 * 60));

		// extract minutes
		$divisor_for_minutes = $seconds % (60 * 60);
		$minutes = floor($divisor_for_minutes / 60);

		// extract the remaining seconds
		$divisor_for_seconds = $divisor_for_minutes % 60;
		$seconds = ceil($divisor_for_seconds);

		// return the final array
		$obj = array(
			"h" => (int) $hours,
			"m" => (int) $minutes,
			"s" => (int) $seconds,
		);
		
		if (strlen($obj["h"])==0)	$obj["h"] = "00";
		if (strlen($obj["m"])==0)	$obj["m"] = "00";
		if (strlen($obj["s"])==0)	$obj["s"] = "00";

		if (strlen($obj["h"])==1)	$obj["h"] = "0".$obj["h"];
		if (strlen($obj["m"])==1)	$obj["m"] = "0".$obj["m"];
		if (strlen($obj["s"])==1)	$obj["s"] = "0".$obj["s"];
		
		return $obj["h"].":".$obj["m"].":".$obj["s"];
		
	}
	function createEmptyAnnotationTransription ($duration, $category_of_data, $cidFlow, $typeOfDoc="SP") 
	{
		global $session,$authoringObj;
		if (isset($authoringObj->isSetGlobalObj) && $authoringObj->isSetGlobalObj=="yes") {
		} else {
			require_once(INC_PHP_AJAX.'authoring.base.ext.class.php');
			$authoringObj = new authoringZoneExt();
			$authoringObj->initCiReference($this->cidFlow);
			$authoringObj->isSetGlobalObj="yes";
		}
		return $authoringObj->createEmptyAnnotationTransription($duration, $category_of_data, $cidFlow, $typeOfDoc);
	}		
}
?>