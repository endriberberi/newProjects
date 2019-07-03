<?
 //MESSAGE ----------------------------------------------------------------------------------------
   $Grid_message_print = array('data' => array(), 'AllRecs' => '0');	

   IF (ISSET($G_APP_VARS["kodi"]) AND ($G_APP_VARS["kodi"] != ""))
      {
       $Grid_message_print['data'][0]['kodi'] = $G_APP_VARS["kodi"];
       $Grid_message_print['data'][0]['mesg'] = $G_APP_VARS["mesg"];
       $Grid_message_print['data'][0]['time'] = $G_APP_VARS["time"];
      }   

   IF (COUNT($Grid_message_print['data']) > 0) 
      {
       $Grid_message_print['AllRecs'] = COUNT($Grid_message_print['data']);
      }
   
   WebApp::addVar('Grid_message_print', $Grid_message_print);
 //MESSAGE ----------------------------------------------------------------------------------------
 
 //LEXOHEN PROPERTITE E NEMIT ---------------------------------------------------------------------
   IF (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") 
      {
       $idstemp = $session->Vars["idstemp"];
       $object  = unserialize(base64_decode(WebApp::findNemProp($idstemp)));
      }
 //LEXOHEN PROPERTITE E NEMIT ---------------------------------------------------------------------

 //KAPET TITULLI I CI -----------------------------------------------------------------------------
   $content_title = "";
   
   $sql = "SELECT IF(title".$session->Vars["lang"]." IS NULL, '', title".$session->Vars["lang"].") as content_title 
             FROM content 
            WHERE content_id = '".ValidateVarFun::f_real_escape_string($session->Vars["contentId"])."'
          ";

   $rs = WebApp::execQuery($sql);
   IF (!$rs->EOF())
      {
       $content_title = $rs->Field("content_title");
      }
 //KAPET TITULLI I CI -----------------------------------------------------------------------------

 //per pupup POSTIMI I VARIABLAVE -----------------------------------------------------------------
   IF (ISSET($_GET['vars_post']) AND ($_GET['vars_post'] != ""))
      {
       $GET_VARS_POST = f_app_vars_page($_GET['vars_post']);
       
       WHILE (LIST($key_post, $val_post) = EACH($GET_VARS_POST)) 
             {
              IF (!ISSET($G_APP_VARS[$key_post]))
                 {
                  $G_APP_VARS[$key_post] = $val_post;
                 }
             
             }
      }

   /*
   IF (ISSET($_POST) AND (COUNT($_POST) > 0))
      {
       //ne popup kemi bere post 
      }
   ELSE
      {
       IF (ISSET($_GET['vars_post']) AND ($_GET['vars_post'] != ""))
          {
           $G_APP_VARS = f_app_vars_page($_GET['vars_post']);
          }
      }
   */
 //per pupup POSTIMI I VARIABLAVE -----------------------------------------------------------------

 //RUHEN VARIABLAT E FAQES ------------------------------------------------------------------------
   FOR ($i=0; $i < count($vars_page_var); $i++)
       {
        $vars_page_var_sel = $vars_page_var[$i];
        
        $vars_page_kol[] = $vars_page_var_sel;
        
        IF (ISSET($G_APP_VARS[$vars_page_var_sel]))
           {
            $vars_page_val[] = $G_APP_VARS[$vars_page_var_sel];
           }
        ELSE
           {
            $vars_page_val[] = "";
           }
       }
   
   //GJENEROJME TOKENIN E FORMES ------------------------------------------------------------------
     $vars_page_kol[] = "form_id_token";
     $vars_page_val[] = f_app_form_id_token();
   //----------------------------------------------------------------------------------------------
   
   //mbajme id e nemit tek forma ------------------------------------------------------------------
     $vars_page_kol[]  = "nem_id";
     $vars_page_val[]  = $NEM_ID_SEL;
   //----------------------------------------------------------------------------------------------
   
   $vars_page = f_app_vars_page_encrypt($vars_page_kol, $vars_page_val);
 //RUHEN VARIABLAT E FAQES ------------------------------------------------------------------------

 //editim_konsultim -------------------------------------------------------------------------------
   $editim_konsultim = "konsultim";
   IF (ISSET($G_APP_VARS["editim_konsultim"]) AND ($G_APP_VARS["editim_konsultim"] != "") )
      {
       $editim_konsultim = $G_APP_VARS["editim_konsultim"];
      }   
 //editim_konsultim -------------------------------------------------------------------------------

 //kapim te drejtat -------------------------------------------------------------------------------
   $nem_rights = NemsManager::getFeRightsToNem($NEM_ID_SEL);
   //$nem_rights[$NEM_ID_SEL]["101"] = "Add";
   //$nem_rights[$NEM_ID_SEL]["102"] = "Modify";
   //$nem_rights[$NEM_ID_SEL]["103"] = "Delete";
   //$nem_rights[$NEM_ID_SEL]["104"] = "Export";
 //kapim te drejtat -------------------------------------------------------------------------------

$nr_rec_page_default = 10; //mire do ishte ta ruanim tek propertite e nemit
$data_url_preview    = APP_URL.'ajxDt.php?uni='.$session->Vars["uni"].'&ln='.$session->Vars["ln"].'&apprcss=modealone&crd='.$session->Vars["contentId"];

IF (DEFINED('BO_ENVIRONMENT') && (BO_ENVIRONMENT=="APPLICATION_BO")) 
   { 
	$data_url_this_nem = APP_URL.'indA.php?uni='.$session->Vars["uni"].'&ln='.$session->Vars["ln"].'&mode=alone&crd='.$session->Vars["contentId"];
   } 
ELSE
   {
	$data_url_this_nem = APP_URL.'?uni='.$session->Vars["uni"].'&ln='.$session->Vars["ln"].'&mode=alone&crd='.$session->Vars["contentId"];
   }

$data_url_upload      = APP_URL.'ajxDt.php?apprcss=upload_doc&uni='.$session->Vars["uni"].'&ln='.$session->Vars["ln"];
$data_url_download    = APP_URL.'ajxDt.php?apprcss=download&uni='.$session->Vars["uni"].'&ln='.$session->Vars["ln"];
$data_url_preview_pdf = EASY_URL.'plugins/pdfjs/web/viewer.html?file=';

WebApp::addVar("max_width", "1600");

$link_modal_width_default  = 1000;
$link_modal_height_default = 1000;
?>