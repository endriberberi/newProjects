<?
function MainContainer_onRender() {

	global $session, $sessUserObj, $event,$VS;	


   
	require_once(INCLUDE_AJAX_PATH . "/CiManagerFe.class.php");
    $getPublishedHtmlForMainCi = "yes";
	$workingCi = new CiManagerFe($session->Vars["contentId"],$session->Vars["lang"]);
	$workingCi->getMainDocumentCiToGrid();	
	

    
}
?>