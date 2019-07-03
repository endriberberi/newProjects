<?
set_time_limit(0);
INI_SET("memory_limit", "256M");

//INCLUDE(dirname(__FILE__)."/../../application.php");

//parametrat -------------------------------------------------------------------------------------------------
  $tipi_exp  		= $_GET["tipi_exp"];
  $vars_page_sel 	= $_GET["vars_page"];
//parametrat -------------------------------------------------------------------------------------------------

//validohet vars_page ----------------------------------------------------------------------------------------
  GLOBAL $G_APP_VARS;
  IF (ISSET($vars_page_sel) AND ($vars_page_sel != ""))
     {
      $G_APP_VARS = f_app_vars_page($vars_page_sel);
     }
  ELSE
     {
      HEADER('Location: ' . APP_URL);
      EXIT;
     }
//validohet vars_page ----------------------------------------------------------------------------------------

//inkludohet faili i mesazheve -------------------------------------------------------------------------------
  IF ($session->Vars["ln"] != "") 
     {
      $lang_name = "LNG".$session->Vars["ln"]."_Name";
      $name_lang = constant($lang_name);
      IF ($name_lang != "")
         {
          $message_file = APP_PATH."templates/".$name_lang.".mesg";
         }
      ELSE
         {
          $message_file = APP_PATH."templates/Shqip.mesg";
         }

     $faili_mesazheve = new WebBox("id_mesg_mesg");
     $faili_mesazheve->parse_msg_file($message_file);
     EXTRACT($GLOBALS["tplVars"]->Vars[0]);	
    }
//inkludohet faili i mesazheve -------------------------------------------------------------------------------
    
//------------------------------------------------------------------------------------------------------------
//KUSH NA THERET ---------------------------------------------------------------------------------------------
  $exp_data = "Y";

  //VALIDOHET WEBBOX_SEL -------------------------------------------------------------------------------------
    IF (!ISSET($G_APP_VARS["webbox"]) OR ($G_APP_VARS["webbox"] == ""))
       {
        HEADER('Location: ' . APP_URL);
        EXIT;
       }
    
    IF (!ISSET($G_APP_VARS["nem_id"]) OR ($G_APP_VARS["nem_id"] == ""))
       {
        HEADER('Location: ' . APP_URL);
        EXIT;
       }

    $webbox_sel     = $G_APP_VARS["webbox"];
    $webbox_include = APP_PATH.$webbox_sel;

    IF ((SUBSTR($webbox_include, -4) != ".php") OR (!FILE_EXISTS($webbox_include)))
       {
        HEADER('Location: ' . APP_URL);
        EXIT;
       }
  //VALIDOHET WEBBOX_SEL -------------------------------------------------------------------------------------
  
  //WEBOXI I NEMIT EKZISTON, VALIDOJME NESE USERI KA TE DREJTE TE EKZEKUTOJE KETE NEM ------------------------
    $NEM_ID_SEL = $G_APP_VARS["nem_id"];
    $nem_rights = NemsManager::getFeRightsToNem($NEM_ID_SEL);

    IF (ISSET($nem_rights[$NEM_ID_SEL]["104"]) AND ($nem_rights[$NEM_ID_SEL]["104"] != ""))
       {
        //KA TE DREJTE EKSPORTI TEK KY NEM
       }
    ELSE
       {
        HEADER('Location: ' . APP_URL);
        EXIT;
       }
    
    //KAPIM ID E NEMIT ---------------------------------------------------------------------------------------
      $idstemp_arr    = EXPLODE("-", $G_APP_VARS["idstemp"]); //CI-459-101-1-123
      $content_id     = $idstemp_arr[1];

      $content_title  = "";
      
      //VALIDOJME NESE USERI ME KETE UNI KA TE DREJTE TE PERDORI KETE NEM ------------------------------------
        //NDOSHTA DUHET TE THARES DICKA GJENERALE NGA JONIDA
        $sql = "SELECT IF(content.titleLng".$session->Vars["ln"]." IS NULL, '', content.titleLng".$session->Vars["ln"].") as content_title 
                  FROM content INNER JOIN ci_nems ON
                               content.content_id = ci_nems.content_id
                               
                               INNER JOIN profil_rights ON
                                     content.id_zeroNivel    = profil_rights.id_zeroNivel   AND
                                     content.id_firstNivel   = profil_rights.id_firstNivel  AND
                                     content.id_secondNivel  = profil_rights.id_secondNivel AND
                                     content.id_thirdNivel   = profil_rights.id_thirdNivel  AND
                                     content.id_fourthNivel  = profil_rights.id_fourthNivel 
                 WHERE content.content_id       = '".ValidateVarFun::f_real_escape_string($content_id)."' AND
                       ci_nems.nem_id           = '".ValidateVarFun::f_real_escape_string($NEM_ID_SEL)."' AND
                       profil_rights.profil_id IN (".$session->Vars["tip"].")
               ";

        /*
        //titulli i CI me duhet ta nxjere si header tek exportet
        $sql = "SELECT IF(titleLng".$session->Vars["ln"]." IS NULL, '', titleLng".$session->Vars["ln"].") as content_title 
                  FROM content 
                 WHERE content_id = '".ValidateVarFun::f_real_escape_string($content_id)."'
               ";
        */
        
        $rs = WebApp::execQuery($sql);
        IF (!$rs->EOF())
           {
            $content_title = $rs->Field("content_title");
           }
        ELSE
           {
            HEADER('Location: ' . APP_URL);
            EXIT;
           }
      //VALIDOJME NESE USERI ME KETE UNI KA TE DREJTE TE PERDORI KETE NEM ------------------------------------
    //KAPIM ID E NEMIT ---------------------------------------------------------------------------------------
  //WEBOXI I NEMIT EKZISTON, VALIDOJME NESE USERI KA TE DREJTE TE EKZEKUTOJE KETE NEM ------------------------

  //KAPIM DEGET OSE QENDRAT E RAPORTIMIT ME TE CILAT ESHTE I LIDHUR USERI ------------------------------------
    $user_dega_qendra = f_app_user_dega_qendra ($session->Vars["ses_userid"]);
  //KAPIM DEGET OSE QENDRAT E RAPORTIMIT ME TE CILAT ESHTE I LIDHUR USERI ------------------------------------

  INCLUDE($webbox_include);
//KUSH NA THERET ---------------------------------------------------------------------------------------------

//SHKRUAJME SKEDARIN -----------------------------------------------------------------------------------------
  IF ($tipi_exp == 1)
     {
      //XLSX
      $output_browser = "Y";
      INCLUDE (dirname(__FILE__)."/exp_xlsx.php");
     }
  ELSEIF ($tipi_exp == 2)
     {
      //CVS
      $output_browser = "Y";
      INCLUDE (dirname(__FILE__)."/exp_cvs.php");
     }
  ELSEIF (($tipi_exp == 3) OR ($tipi_exp == 4))
     {
      //HTML PDF
      $output_browser = "Y";
      INCLUDE (dirname(__FILE__)."/exp_html_pdf.php");
     }
  ELSEIF ($tipi_exp == 5)
     {
      //DOCX
      //$output_browser = "Y";
      //INCLUDE (dirname(__FILE__)."/exp_doc.php"); //duhet te shkruaj docx
     }
  ELSEIF ($tipi_exp == 6)
     {
      //graf
      //INCLUDE (dirname(__FILE__)."/exp_graf.php");
     }
  ELSE
     {
      //echo "ERROR DEBUG\n".$webbox_include."\n|$output_browser:output_browser|$tipi_exp:tipi_exp<textarea>";
      //print_r($_GET);
      //print_r($session->Vars);
      //print_r($G_APP_VARS);
      //echo "</textarea>";     

      EXIT;
     }
//SHKRUAJME SKEDARIN -----------------------------------------------------------------------------------------
?>