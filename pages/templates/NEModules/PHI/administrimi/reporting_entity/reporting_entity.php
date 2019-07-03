<?
function reporting_entity_eventHandler($event)
{
    GLOBAL $session, $event, $G_APP_VARS;
    extract($event->args);

    //KAPIM KUSH NEM JEMI ------------------------------------------------------------------------------------------------
      $kush_jam_une = f_app_kush_nem_jam_une(__FILE__);
   
      $NEM_ID_SEL   = $kush_jam_une["nem_id"];
      $WEBBOX_SEL   = $kush_jam_une["webbox"];
    //KAPIM KUSH NEM JEMI ------------------------------------------------------------------------------------------------

    //script eventHandler ------------------------------------------------------------------------------------------------
      INCLUDE(dirname(__FILE__)."/../../php_script/eventHandler_nav_search.php");
    //script eventHandler ------------------------------------------------------------------------------------------------

	switch ($event->name)
	       {
		    case "save":
                  //SKRIPTI SPECIFIK PER NGJARJEN SAVE -------------------------------------------------------------------
                  $var_post      = STR_REPLACE(array("<pikp>","<_and_>"), array(";","&"), $var_post);
                  $var_post_arr  = EXPLODE("<->", $var_post);
                  $kol_array_all = EXPLODE("<_>", $var_post_arr[0]);
                  $val_array_all = EXPLODE("<_>", $var_post_arr[1]);
                         
                  //mbajme datat ne array --------------------------------------------------------------------------------
                    FOR ($i=0; $i < count($kol_array_all); $i++)
                        {
                         IF (($kol_array_all[$i] != "ids_user_reporting_entity") AND ($kol_array_all[$i] != "ids_village_reporting_entity") AND ($kol_array_all[$i] != "id_commune"))
                            {
                             $kol_array[] = $kol_array_all[$i];
                             $val_array[] = $val_array_all[$i];
                            }

                         $kol_val_array[$kol_array_all[$i]] = $val_array_all[$i];
                        }
                  //mbajme datat ne array --------------------------------------------------------------------------------

                  //regjistrohen vlerat ne db ----------------------------------------------------------------------------
                    IF ($id_pk == "")
                       {
                        //gjenerohet max_id ------------------------------------------------------------------------------
                          $max_id = 0;
                          $sql = "SELECT IF(id_reporting_entity IS NULL, 0, MAX(id_reporting_entity)) as max_id 
                                    FROM phi_reporting_entity 
                                 ";
                          $rs = WebApp::execQuery($sql);
                          IF (!$rs->EOF())
                             {
                              $max_id = $rs->Field("max_id");
                             }
                          
                          $id_pk = $max_id + 1;
                        //gjenerohet max_id ------------------------------------------------------------------------------

                        //RASTI INSERT -----------------------------------------------------------------------------------
                          $kolonat = "id_reporting_entity,    record_user";
                          $vlerat  = "'".$id_pk."', '".$session->Vars["ses_userid"]."'";


			              //formohet inserti -----------------------------------------------------------------------------
                            FOR ($i=0; $i < count($kol_array); $i++)
                                {
                                 IF ($val_array[$i] == "")
                                    {
                                     $val_sel = "NULL";
                                    }
                                 ELSE
                                    {
                                     $val_sel = "'".ValidateVarFun::f_real_escape_string($val_array[$i])."'";
                                    }
                               
                                 $kolonat .= ", ".$kol_array[$i]; 
                                 $vlerat  .= ", ".$val_sel;
                                }

                            $sql_INS_UPD = "INSERT INTO phi_reporting_entity (".$kolonat.") VALUES(".$vlerat.")";
			              //formohet inserti -----------------------------------------------------------------------------
                        //RASTI INSERT -----------------------------------------------------------------------------------
                       }
                    ELSE
                       {
                        //RASTI UPDATE -----------------------------------------------------------------------------------
                          $sql_INS_UPD = "record_user = '".$session->Vars["ses_userid"]."'";

                          FOR ($i=0; $i < count($kol_array); $i++)
                              {
                               IF ($val_array[$i] == "")
                                  {
                                   $val_sel = "NULL";
                                  }
                               ELSE
                                  {
                                   $val_sel = "'".ValidateVarFun::f_real_escape_string($val_array[$i])."'";
                                  }

                               $sql_INS_UPD .= ", ".$kol_array[$i]." = ".$val_sel; 
                              }

                          //$sql_INS_UPD = SUBSTR($sql_INS_UPD, 1);
                          $sql_INS_UPD = "UPDATE phi_reporting_entity SET ".$sql_INS_UPD." WHERE id_reporting_entity = '".ValidateVarFun::f_real_escape_string($id_pk)."'";
                        //RASTI UPDATE -----------------------------------------------------------------------------------
                       }

                    //EKZEKUTOHET SQL ------------------------------------------------------------------------------------
                      IF (!WebApp::execQuery($sql_INS_UPD))
                         {
                          $G_APP_VARS["kodi"]         = "error";
                          $G_APP_VARS["mesg"]         = "{{ruajtja_deshtoi_mesg}}";
                          $G_APP_VARS["time"]         = "10";

                          $G_APP_VARS["kol_val_post"] = $kol_val_array;
                         }
                      ELSE
                         {
                          //MENAXHOJME PERDORUESIT E QENDERES  -----------------------------------------------------------
                            //MEQENESE USERAT KA TE DREJTE TI EDITOJE VETEM ADMINI SIGUROHEMI QE ESHTE POSTUAR VARIABLI I TYRE
                            //PERPARA SE TE UPDEJTOJME TE DHENAT
                            IF (ISSET($kol_val_array["ids_user_reporting_entity"]))
                               {
                                //FSHIME FSHATRAT E LIDHUR ME PARE -------------------------------------------------------
                                  $sql_del = "DELETE 
                                                FROM phi_reporting_entity_users 
                                               WHERE id_reporting_entity = '".ValidateVarFun::f_real_escape_string($id_pk)."'
                                             ";
                                  WebApp::execQuery($sql_del);
                                //----------------------------------------------------------------------------------------

                                //REGJISTROJME USERAT ME TE DREJTA VIEW --------------------------------------------------
                                  IF ($kol_val_array["ids_user_reporting_entity"] != "")
                                     {
                                      $ids_arr = EXPLODE(",", $kol_val_array["ids_user_reporting_entity"]);
                                      FOR ($i=0; $i < count($ids_arr); $i++)
                                          {
                                           $sql_kol     = null; 
                                           $sql_val     = null;
                                       
                                           $sql_kol[]   = "id_reporting_entity"; 
                                           $sql_val[]   = "'".ValidateVarFun::f_real_escape_string($id_pk)."'"; 

                                           $sql_kol[]   = "UserId"; 
                                           $sql_val[]   = "'".ValidateVarFun::f_real_escape_string($ids_arr[$i])."'"; 

                                           $sql_kol[]   = "record_user"; 
                                           $sql_val[]   = "'".$session->Vars["ses_userid"]."'"; 
                                       
                                           $sql_kol_ins = IMPLODE(",", $sql_kol);
                                           $sql_val_ins = IMPLODE(",", $sql_val);
                                      
                                           $sql_ins     = "INSERT INTO phi_reporting_entity_users (".$sql_kol_ins.") VALUES(".$sql_val_ins.")";
                                           WebApp::execQuery($sql_ins);
                                          }
                                     }
                               }
                            //--------------------------------------------------------------------------------------------
                          //MENAXHOJME PERDORUESIT E QENDERES  -----------------------------------------------------------

                          //MENAXHOJME FSHATRAT QE MBULLOHEN NGA KJO QENDER  ---------------------------------------------
                            IF (ISSET($kol_val_array["ids_village_reporting_entity"]))
                               {
                                //FSHIME FSHATRAT E LIDHUR ME PARE -------------------------------------------------------
                                  $sql_del = "DELETE 
                                                FROM phi_reporting_entity_village 
                                               WHERE id_reporting_entity = '".ValidateVarFun::f_real_escape_string($id_pk)."'
                                             ";
                                  WebApp::execQuery($sql_del);
                                //----------------------------------------------------------------------------------------

                                //REGJISTROJME USERAT ME TE DREJTA VIEW --------------------------------------------------
                                  IF ($kol_val_array["ids_village_reporting_entity"] != "")
                                     {
                                      $ids_arr = EXPLODE(",", $kol_val_array["ids_village_reporting_entity"]);
                                      FOR ($i=0; $i < count($ids_arr); $i++)
                                          {
                                           $sql_kol     = null; 
                                           $sql_val     = null;
                                       
                                           $sql_kol[]   = "id_reporting_entity"; 
                                           $sql_val[]   = "'".ValidateVarFun::f_real_escape_string($id_pk)."'"; 

                                           $sql_kol[]   = "id_village"; 
                                           $sql_val[]   = "'".ValidateVarFun::f_real_escape_string($ids_arr[$i])."'"; 

                                           $sql_kol[]   = "record_user"; 
                                           $sql_val[]   = "'".$session->Vars["ses_userid"]."'"; 
                                       
                                           $sql_kol_ins = IMPLODE(",", $sql_kol);
                                           $sql_val_ins = IMPLODE(",", $sql_val);
                                      
                                           $sql_ins     = "INSERT INTO phi_reporting_entity_village (".$sql_kol_ins.") VALUES(".$sql_val_ins.")";
                                           WebApp::execQuery($sql_ins);
                                          }
                                     }
                               }
                            //--------------------------------------------------------------------------------------------
                          //MENAXHOJME FSHATRAT QE MBULLOHEN NGA KJO QENDER  ---------------------------------------------

                          //SA PER TE BERE LIDHJEN ME FSHATRAT -----------------------------------------------------------
                            IF (ISSET($kol_val_array["id_commune"]) AND ($kol_val_array["id_commune"] != ""))
                               {
                                //FSHIME FSHATRAT E LIDHUR ME PARE -------------------------------------------------------
                                  $sql_del = "DELETE 
                                                FROM phi_reporting_entity_village 
                                               WHERE id_reporting_entity = '".ValidateVarFun::f_real_escape_string($id_pk)."'
                                             ";
                                  WebApp::execQuery($sql_del);
                                //----------------------------------------------------------------------------------------

                                //REGJISTROJME USERAT ME TE DREJTA VIEW --------------------------------------------------
                                  $sql = "SELECT id_village
                                            FROM phi_village 
                                           WHERE id_commune = '".ValidateVarFun::f_real_escape_string($kol_val_array["id_commune"])."'
                                         ";
                       
                                  $rs  = WebApp::execQuery($sql);
                                  $rs->MoveFirst();
                                  WHILE (!$rs->EOF())
	                                    {
	                                     $id_village  = $rs->Field("id_village");

                                         $sql_kol     = null; 
                                         $sql_val     = null;
                                       
                                         $sql_kol[]   = "id_reporting_entity"; 
                                         $sql_val[]   = "'".ValidateVarFun::f_real_escape_string($id_pk)."'"; 

                                         $sql_kol[]   = "id_village"; 
                                         $sql_val[]   = "'".ValidateVarFun::f_real_escape_string($id_village)."'"; 

                                         $sql_kol[]   = "record_user"; 
                                         $sql_val[]   = "'".$session->Vars["ses_userid"]."'"; 
                                       
                                         $sql_kol_ins = IMPLODE(",", $sql_kol);
                                         $sql_val_ins = IMPLODE(",", $sql_val);
                                      
                                         $sql_ins     = "INSERT INTO phi_reporting_entity_village (".$sql_kol_ins.") VALUES(".$sql_val_ins.")";
                                         WebApp::execQuery($sql_ins);

                                         $rs->MoveNext();
                                        }
                               }
                          //SA PER TE BERE LIDHJEN ME FSHATRAT -----------------------------------------------------------
                          
                          $G_APP_VARS["kodi"]         = "success";
                          $G_APP_VARS["mesg"]         = "{{ruajtja_sukses_mesg}}";
                          $G_APP_VARS["time"]         = "5";
                         }

                      $G_APP_VARS["id_sel"]           = $id_pk."|".$NEM_ID_SEL;
                      $G_APP_VARS["gjendje"]          = "record_detaje";
                      $G_APP_VARS["editim_konsultim"] = "editim";
                    //EKZEKUTOHET SQL ------------------------------------------------------------------------------------
                  //regjistrohen vlerat ne db ----------------------------------------------------------------------------
                  break;

            case "del":
                  //SKRIPTI SPECIFIK PER NGJARJEN del --------------------------------------------------------------------
                  //kontrollohet per te dhena te varura ------------------------------------------------------------------
                    $nr_rreshta_te_lidhura = 1;

                    $sql = "SELECT count(*) as nr_rreshta_te_lidhura FROM phi_form_notification WHERE id_reporting_entity = '".ValidateVarFun::f_real_escape_string($id_pk)."'";
                    $rs  = WebApp::execQuery($sql);
                    IF (!$rs->EOF())
                       {
                        $nr_rreshta_te_lidhura = $rs->Field("nr_rreshta_te_lidhura");
                       }
                    
                    IF ($nr_rreshta_te_lidhura == 0)
                       {
                        $sql = "SELECT count(*) as nr_rreshta_te_lidhura FROM phi_form_notification_laboratory WHERE id_reporting_entity_laboratory = '".ValidateVarFun::f_real_escape_string($id_pk)."'";
                        $rs  = WebApp::execQuery($sql);
                        IF (!$rs->EOF())
                           {
                            $nr_rreshta_te_lidhura = $rs->Field("nr_rreshta_te_lidhura");
                           }
                       }
                  //kontrollohet per te dhena te varura ------------------------------------------------------------------

                  IF ($nr_rreshta_te_lidhura == 0)
                     {
                      $sql_del = "DELETE FROM phi_reporting_entity       WHERE id_reporting_entity = '".ValidateVarFun::f_real_escape_string($id_pk)."'";
                      WebApp::execQuery($sql_del);

                      $sql_del = "DELETE FROM phi_reporting_entity_users WHERE id_reporting_entity = '".ValidateVarFun::f_real_escape_string($id_pk)."'";
                      WebApp::execQuery($sql_del);

                      $G_APP_VARS["kodi"]  = "success";
                      $G_APP_VARS["mesg"]  = "{{fshirja_sukses_mesg}}";
                      $G_APP_VARS["time"]  = "5";
                     }
                  ELSE
                     {
                      $G_APP_VARS["kodi"]  = "warning";
                      $G_APP_VARS["mesg"]  = "{{te_dhena_te_varura_mesg}}";
                      $G_APP_VARS["time"]  = "15";
                     }

                  $G_APP_VARS["gjendje"] = "lista";
                  break;

           }
}

function reporting_entity_onRender()
{
 GLOBAL $session, $event, $G_APP_VARS;
 extract($event->args);

 //KAPIM DEGET OSE QENDRAT E RAPORTIMIT ME TE CILAT ESHTE I LIDHUR USERI ---------------------------
   $user_dega_qendra = f_app_user_dega_qendra ($session->Vars["ses_userid"]);
 //KAPIM DEGET OSE QENDRAT E RAPORTIMIT ME TE CILAT ESHTE I LIDHUR USERI ---------------------------

 //KAPIM KUSH NEM JEMI -----------------------------------------------------------------------------
   $kush_jam_une = f_app_kush_nem_jam_une(__FILE__);
   
   $NEM_ID_SEL   = $kush_jam_une["nem_id"];
   $WEBBOX_SEL   = $kush_jam_une["webbox"];
 //KAPIM KUSH NEM JEMI -----------------------------------------------------------------------------

 //VARIABLAT E FAQES -------------------------------------------------------------------------------
   $vars_page_var[] = "nr_rec_page";
   $vars_page_var[] = "nr_rec_start";
   $vars_page_var[] = "order_by";
   $vars_page_var[] = "order_by_indx";
   $vars_page_var[] = "s_id_branch";
   $vars_page_var[] = "s_address_id_municipality";
   $vars_page_var[] = "s_name";
   $vars_page_var[] = "s_id_reporting_entity_kind";
   $vars_page_var[] = "s_state_or_private";
   $vars_page_var[] = "s_fills_alert_form";
 
   //kur nemin e therasim per te shtuar rekorde ----------------------------------------------------
     $vars_page_var[] = "nem_mode";
     $vars_page_var[] = "nem_mode_filter";
     $vars_page_var[] = "return_fun_name";
     $vars_page_var[] = "return_elm_id";
 //VARIABLAT E FAQES -------------------------------------------------------------------------------

 //script onRender ---------------------------------------------------------------------------------
   INCLUDE(dirname(__FILE__)."/../../php_script/on_render.php");
 //script onRender ---------------------------------------------------------------------------------

 //formohet html -----------------------------------------------------------------------------------
   //gjendje ---------------------------------------------------------------------------------------
     $gjendje_sel = "lista";
     IF (ISSET($G_APP_VARS["gjendje"]) AND ($G_APP_VARS["gjendje"] != "") )
        {
         $gjendje_sel = $G_APP_VARS["gjendje"];
        }   
   //gjendje ---------------------------------------------------------------------------------------

   $template_sel      = "";
   $script_js_in_page = "";
   
   IF ($gjendje_sel == "lista")
      {
       $template_sel = "LIST";
       INCLUDE(dirname(__FILE__)."/lista_grida_search.php");
       INCLUDE(dirname(__FILE__)."/lista_data.php");
       INCLUDE(dirname(__FILE__)."/../../php_script/lista_info.php");
      }

   IF ($gjendje_sel == "record_detaje")
      {
       $template_sel = "DETAJE";
       INCLUDE(dirname(__FILE__)."/add_edit.php");
      }

   WebApp::addVar("template_sel",      $template_sel);
   WebApp::addVar("script_js_in_page", $script_js_in_page);
 //formohet html -----------------------------------------------------------------------------------
}
?>