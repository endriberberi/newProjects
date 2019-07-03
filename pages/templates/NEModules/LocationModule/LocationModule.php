<?

function LocationModule_onRender() 
{
    global $session,$event,$global_cache_dynamic,$cacheDyn,$G_HOME_LINK,$linkParams, $linkParamsCached;
	
	$NEM_TEMPLATE = "";

	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {

		$prop_arr = WebApp::clearNemAtributes($session->Vars["idstemp"]);
		$NEM_FILENAME = 'default_template.html';

		require_once(INC_PHP_AJAX."NemsManager.class.php");
		$grn = NemsManager::getFrontEndGeneralProperties($prop_arr);
		
		if (isset($grn["NEM_FILENAME"]) && $grn["NEM_FILENAME"]!='') {
			$NEM_FILENAME = $grn["NEM_FILENAME"];
		}		
		
		$NEM_TEMPLATE = '<Include SRC="{{NEMODULES_PATH}}LocationModule/'.$NEM_FILENAME.'"/>';
		
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
		
		$lcpath["AllRecs"]  = count($lcpath["data"]);		
		$actualLevel["AllRecs"]  = count($actualLevel["data"]);		
		$parentLevel["AllRecs"]  = count($parentLevel["data"]);		

		
		WebApp::addVar("LocationPathGrid", $lcpath);		
		WebApp::addVar("actualLevelGrid", $actualLevel);		
		WebApp::addVar("parentLevelGrid", $parentLevel);		

	
/*echo "<textarea>$hierarchyLevel:hierarchyLevel\n";
echo "\n actualLevel \n";
print_r($actualLevel);
echo "\n parentLevel \n";
print_r($parentLevel);
echo "\n lcpath \n";
print_r($lcpath);
echo "\n dataLevels \n";
print_r($dataLevels);

echo "</textarea>";	*/	

	}

	WebApp::addVar("PATH_NEM_TEMPLATE", $NEM_TEMPLATE);
}
/*
			<Grid gridId="DocCachedInfo_{{nodeImageId}}">
					<img class="card-img-top img-fluid" src="{{link_url}}" alt=""/>
			</Grid>
2:hierarchyLevel

 actualLevel 
Array
(
    [data] => Array
        (
            [0] => Array
                (
                    [linkcrd] => 0,10,2,0,0
                    [linkhref] => javascript:GoTo('thisPage?event=none.ch_state(k=0,10,2,0,0)')
                    [nodeClass] => category-2
                    [nodeImageId] => 2
                    [imageSm_id] => 
                    [imageBg_id] => 
                    [NodeDescription] => General M-Pesa Product Training
                    [CiTitle] => General M-Pesa Product Training
                )

        )

    [AllRecs] => 1
)

 parentLevel 
Array
(
    [data] => Array
        (
            [0] => Array
                (
                    [linkcrd] => 0,10,0,0,0
                    [linkhref] => javascript:GoTo('thisPage?event=none.ch_state(k=0,10,0,0,0)')
                    [nodeClass] => 
                    [nodeImageId] => 0
                    [imageSm_id] => 
                    [imageBg_id] => 
                    [NodeDescription] => Training
                    [CiTitle] => Training
                )

        )

    [AllRecs] => 1
)

 lcpath 
Array
(
    [data] => Array
        (
            [0] => Array
                (
                    [level0_linkcrd] => 0,0,0,0,0
                    [level0_linkhref] => javascript:GoTo('thisPage?event=none.ch_state(k=0,0,0,0,0)')
                    [level0_nodeClass] => 
                    [level0_nodeIco] => 
                    [level0_nodeImageId] => 0
                    [level0_imageSm_id] => 
                    [level0_imageBg_id] => 
                    [level0_NodeDescription] => Home
                    [level0_CiTitle] => 
                    [0_exist] => yes
                    [level1_linkcrd] => 0,10,0,0,0
                    [level1_linkhref] => javascript:GoTo('thisPage?event=none.ch_state(k=0,10,0,0,0)')
                    [level1_nodeClass] => 
                    [level1_nodeIco] => 
                    [level1_nodeImageId] => 0
                    [level1_imageSm_id] => 
                    [level1_imageBg_id] => 
                    [level1_NodeDescription] => Training
                    [level1_CiTitle] => 
                    [1_exist] => yes
                    [level2_linkcrd] => 0,10,2,0,0
                    [level2_linkhref] => javascript:GoTo('thisPage?event=none.ch_state(k=0,10,2,0,0)')
                    [level2_nodeClass] => category-2
                    [level2_nodeIco] => 
                    [level2_nodeImageId] => 2
                    [level2_imageSm_id] => 
                    [level2_imageBg_id] => 
                    [level2_NodeDescription] => General M-Pesa Product Training
                    [level2_CiTitle] => 
                    [2_exist] => yes
                )

        )

    [AllRecs] => 1
)

 dataLevels 
Array
(
    [0] => Array
        (
            [linkcrd] => 0,0,0,0,0
            [linkhref] => javascript:GoTo('thisPage?event=none.ch_state(k=0,0,0,0,0)')
            [nodeClass] => 
            [nodeImageId] => 0
            [imageSm_id] => 
            [imageBg_id] => 
            [NodeDescription] => Home
            [CiTitle] => Home
        )

    [1] => Array
        (
            [linkcrd] => 0,10,0,0,0
            [linkhref] => javascript:GoTo('thisPage?event=none.ch_state(k=0,10,0,0,0)')
            [nodeClass] => 
            [nodeImageId] => 0
            [imageSm_id] => 
            [imageBg_id] => 
            [NodeDescription] => Training
            [CiTitle] => Training
        )

    [2] => Array
        (
            [linkcrd] => 0,10,2,0,0
            [linkhref] => javascript:GoTo('thisPage?event=none.ch_state(k=0,10,2,0,0)')
            [nodeClass] => category-2
            [nodeImageId] => 2
            [imageSm_id] => 
            [imageBg_id] => 
            [NodeDescription] => General M-Pesa Product Training
            [CiTitle] => General M-Pesa Product Training
        )

)

*/
