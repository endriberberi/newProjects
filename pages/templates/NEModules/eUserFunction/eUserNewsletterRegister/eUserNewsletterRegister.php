<?
function eUserNewsletterRegister_onRender()
{
	global $session;

	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {

		$prop_arr = WebApp::clearNemAtributes($session->Vars["idstemp"]);
		$NEM_FILENAME = 'default_template.html';

		require_once(INC_PHP_AJAX."NemsManager.class.php");
		$grn = NemsManager::getFrontEndGeneralProperties($prop_arr);
		
		if (isset($grn["NEM_FILENAME"]) && $grn["NEM_FILENAME"]!='') {
			$NEM_FILENAME = $grn["NEM_FILENAME"];
		}		
		
		$NEM_TEMPLATE = '<Include SRC="{{NEMODULES_PATH}}eUserFunction/eUserNewsletterRegister/'.$NEM_FILENAME.'"/>';
		WebApp::addVar("NEM_TEMPLATE", $NEM_TEMPLATE);
		

		if(count($prop_arr) > 0)
			foreach($prop_arr as $k => $v)
				if(!is_array($v))
					WebApp::addVar($k, $v);

		//get salutation grid
		require_once(INCLUDE_KW_AJAX_PATH.'KwManager.Base.class.php');
		$KwObj = new KwManagerFamily($session->Vars["ses_userid"],$session->Vars["lang"]);
		$FamilyDataSourceArray = $KwObj->getSpecialFamilies("'USRTI'");
		if (count($FamilyDataSourceArray)>0) {
			foreach($FamilyDataSourceArray as $family_type_id => $infoGrArr){
				if ($family_type_id == 1 && count($infoGrArr) > 0) {
					foreach ($infoGrArr as $specialization => $dataFamily) {
						foreach ($dataFamily as $idFamily => $familyName) {

							$KwObjItemSo = $KwObj->setKwObjItem($family_type_id);
							$KwObjItemSo->setTreePositionProperties("0,".$idFamily);
							$dataItem = $KwObjItemSo->generateExtendedList();
							$GrSourcePredefined = array("data"=>array(),"AllRecs"=>"0"); $indSo = 0;
							if (count(dataItem)>0) {
								foreach ($dataItem as $idkw => $dt) {
									$GrSourcePredefined["data"][$indSo]["source_fid"] = $idFamily; // was familyid
									$GrSourcePredefined["data"][$indSo]["source_kid"] = $idkw;
									$GrSourcePredefined["data"][$indSo]["source_sel"] = "";

									$GrSourcePredefined["data"][$indSo]["source_code"] = $dt["descriptionCode"];
									$GrSourcePredefined["data"][$indSo++]["source_label"] = $dt["description"];
									IF (count($GrSourcePredefined["data"])>0)
										$GrSourcePredefined["AllRecs"] = count($GrSourcePredefined["data"]);
									WebApp::addVar("SalutationGrid",$GrSourcePredefined);
								}
							}
						}
					}
				}
			}
		}
		//get salutation grid
	}
}

