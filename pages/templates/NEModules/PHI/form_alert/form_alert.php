<?
function form_alert_eventHandler($event)
{
    GLOBAL $session, $event, $G_APP_VARS;
    extract($event->args);

    //KAPIM KUSH NEM JEMI ------------------------------------------------------------------------------------------------
      $kush_jam_une = f_app_kush_nem_jam_une(__FILE__);
   
      $NEM_ID_SEL   = $kush_jam_une["nem_id"];
      $WEBBOX_SEL   = $kush_jam_une["webbox"];
    //KAPIM KUSH NEM JEMI ------------------------------------------------------------------------------------------------

    //script eventHandler ------------------------------------------------------------------------------------------------
      INCLUDE(dirname(__FILE__)."/../php_script/eventHandler_nav_search.php");
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
                    $order_form_alert_agegroup = 0;
                    
                    FOR ($i=0; $i < count($kol_array_all); $i++)
                        {
                         IF (SUBSTR($kol_array_all[$i], 0, 12) == "total_cases_")
                            {
                             $id_syndrome_sel = SUBSTR($kol_array_all[$i], 12);
                             
                             IF ($val_array_all[$i] == "")
                                {
                                 $val_array_all[$i] = 0;
                                }
                             
                             $form_alert_sindroma_id_syndrome_arr[] = $id_syndrome_sel;
                             $form_alert_sindroma_total_cases_arr[] = $val_array_all[$i];
                            }
                         ELSEIF (SUBSTR($kol_array_all[$i], 0, 6) == "cases_")
                            {
                             $id_syndrome_id_agegroup_sel = SUBSTR($kol_array_all[$i], 6);
                             $id_syndrome_id_agegroup_arr = EXPLODE("_", $id_syndrome_id_agegroup_sel);
                             
                             $id_syndrome_sel             = $id_syndrome_id_agegroup_arr[0];
                             $id_agegroup_sel             = $id_syndrome_id_agegroup_arr[1];
                             
                             IF ($val_array_all[$i] == "")
                                {
                                 $val_array_all[$i] = 0;
                                }

                             $form_alert_sindroma_agegroup_id_syndrome_arr[] = $id_syndrome_sel;
                             $form_alert_sindroma_agegroup_id_agegroup_arr[] = $id_agegroup_sel;
                             $form_alert_sindroma_agegroup_cases_arr[]       = $val_array_all[$i];
                             
                             IF (!ISSET($order_form_alert_agegroup_arr[$id_agegroup_sel]))
                                {
                                 $order_form_alert_agegroup                       = $order_form_alert_agegroup + 1;
                                 $order_form_alert_agegroup_arr[$id_agegroup_sel] = $order_form_alert_agegroup;
                                }
                            }
                         ELSE
                            {
                             $kol_array[] = $kol_array_all[$i];
                             $val_array[] = $val_array_all[$i];
                            }

                         $kol_val_array[$kol_array_all[$i]] = $val_array_all[$i];
                        }
                  //mbajme datat ne array --------------------------------------------------------------------------------

                  //NE FILLIM VERIFIKOJME NESE KY NUMER FORMULARI ESHTE REGJISTRUAR ME PARE ------------------------------
                    //verifikimi form_number -----------------------------------------------------------------------------
                      $form_alert_ekziston = 0;
                      $sql = "SELECT COUNT(1) AS form_alert_ekziston
                                FROM phi_form_alert 
                               WHERE id_branch             = '".ValidateVarFun::f_real_escape_string($kol_val_array["id_branch"])."'                     AND
                                     id_reporting_entity   = '".ValidateVarFun::f_real_escape_string($kol_val_array["id_reporting_entity"])."'           AND
                                     YEAR(week_date_start) = '".ValidateVarFun::f_real_escape_string(SUBSTR($kol_val_array["week_date_start"], 0, 4))."' AND
                                     form_number           = '".ValidateVarFun::f_real_escape_string($kol_val_array["form_number"])."'                   AND
                                     id_form_alert        != '".ValidateVarFun::f_real_escape_string($id_pk)."'
                             ";
                    
                      //print $sql;
                    
                      $rs = WebApp::execQuery($sql);
                      IF (!$rs->EOF())
                         {
                          $form_alert_ekziston = $rs->Field("form_alert_ekziston");
                         }
                  
                      IF ($form_alert_ekziston > 0)
                         {
                          $G_APP_VARS["kodi"]         = "warning";
                          $G_APP_VARS["mesg"]         = "{{form_alert_ekziston_mesg}} {{ruajtja_deshtoi_mesg}}";
                          $G_APP_VARS["time"]         = "15";

                          $G_APP_VARS["kol_val_post"] = $kol_val_array;

                          $G_APP_VARS["id_sel"]           = $id_pk."|".$NEM_ID_SEL;
                          $G_APP_VARS["gjendje"]          = "record_detaje";
                          $G_APP_VARS["editim_konsultim"] = "editim";
                        
                          RETURN;
                         }
                    //verifikimi form_number -----------------------------------------------------------------------------
                  //NE FILLIM VERIFIKOJME NESE KY NUMER FORMULARI ESHTE REGJISTRUAR ME PARE ------------------------------

                  //regjistrohen vlerat ne db ----------------------------------------------------------------------------
                    IF ($id_pk == "")
                       {
                        $AKSIONI_SEL = "INSERT";
                        
                        //gjenerohet max_id ------------------------------------------------------------------------------
                          $max_id = 0;
                          $sql = "SELECT IF(id_form_alert IS NULL, 0, MAX(id_form_alert)) as max_id 
                                    FROM phi_form_alert 
                                 ";
                          $rs = WebApp::execQuery($sql);
                          IF (!$rs->EOF())
                             {
                              $max_id = $rs->Field("max_id");
                             }
                          
                          $id_pk = $max_id + 1;
                        //gjenerohet max_id ------------------------------------------------------------------------------

                        //RASTI INSERT -----------------------------------------------------------------------------------
                          $kolonat = "id_form_alert, record_user_ins,                    record_timestamp_ins";
                          $vlerat  = "'".$id_pk."',  '".$session->Vars["ses_userid"]."', '".DATE("Y-m-d H:i:s")."'";

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

                            $sql_INS_UPD = "INSERT INTO phi_form_alert (".$kolonat.") VALUES(".$vlerat.")";
			              //formohet inserti -----------------------------------------------------------------------------
                        //RASTI INSERT -----------------------------------------------------------------------------------
                       }
                    ELSE
                       {
                        //RASTI UPDATE -----------------------------------------------------------------------------------
                          $sql_INS_UPD = "record_user_upd = '".$session->Vars["ses_userid"]."'";

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
                          $sql_INS_UPD = "UPDATE phi_form_alert SET ".$sql_INS_UPD." WHERE id_form_alert = '".ValidateVarFun::f_real_escape_string($id_pk)."'";
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
                          $G_APP_VARS["kodi"]         = "success";
                          $G_APP_VARS["mesg"]         = "{{ruajtja_sukses_mesg}}";
                          $G_APP_VARS["time"]         = "5";

                          //MENAXHOJME TABELAT E VARURA ------------------------------------------------------------------
                            //FSHIME phi_form_alert_sindroma -------------------------------------------------------------
                              $sql_del = "DELETE 
                                            FROM phi_form_alert_sindroma 
                                           WHERE id_form_alert = '".ValidateVarFun::f_real_escape_string($id_pk)."'
                                         ";
                              WebApp::execQuery($sql_del);
                            //--------------------------------------------------------------------------------------------

                            //FSHIME phi_form_alert_sindroma_agegroup ----------------------------------------------------
                              $sql_del = "DELETE 
                                            FROM phi_form_alert_sindroma_agegroup 
                                           WHERE id_form_alert = '".ValidateVarFun::f_real_escape_string($id_pk)."'
                                         ";
                              WebApp::execQuery($sql_del);
                            //--------------------------------------------------------------------------------------------

                            //REGJISTROJME total_cases -------------------------------------------------------------------
                              $order_form_alert = 0;
                              FOR ($i=0; $i < count($form_alert_sindroma_id_syndrome_arr); $i++)
                                  {
                                   $id_syndrome_sel  = $form_alert_sindroma_id_syndrome_arr[$i];
                                   $total_cases_sel  = $form_alert_sindroma_total_cases_arr[$i];
                                   $order_form_alert = $order_form_alert + 1;
                                   
                                   $sql_kol   = null; 
                                   $sql_val   = null;
                                    
                                   $sql_kol[] = "id_form_alert"; 
                                   $sql_val[] = "'".ValidateVarFun::f_real_escape_string($id_pk)."'"; 

                                   $sql_kol[] = "id_syndrome"; 
                                   $sql_val[] = "'".ValidateVarFun::f_real_escape_string($id_syndrome_sel)."'"; 

                                   $sql_kol[] = "order_form_alert"; 
                                   $sql_val[] = "'".$order_form_alert."'"; 

                                   $sql_kol[] = "total_cases"; 
                                   $sql_val[] = "'".ValidateVarFun::f_real_escape_string($total_cases_sel)."'"; 
                                   
                                   $sql_kol[] = "record_user_ins"; 
                                   $sql_val[] = "'".$session->Vars["ses_userid"]."'"; 
                                   
                                   $sql_kol[] = "record_status"; 
                                   $sql_val[] = "'1'"; 

                                   $sql_kol_ins = IMPLODE(",", $sql_kol);
                                   $sql_val_ins = IMPLODE(",", $sql_val);
                                   
                                   $sql_ins     = "INSERT INTO phi_form_alert_sindroma (".$sql_kol_ins.") VALUES(".$sql_val_ins.")";
                                   WebApp::execQuery($sql_ins);
                                  }
                            //--------------------------------------------------------------------------------------------

                            //REGJISTROJME cases -------------------------------------------------------------------------
                              FOR ($i=0; $i < count($form_alert_sindroma_agegroup_id_syndrome_arr); $i++)
                                  {
                                   $id_syndrome_sel  = $form_alert_sindroma_agegroup_id_syndrome_arr[$i];
                                   $id_agegroup_sel  = $form_alert_sindroma_agegroup_id_agegroup_arr[$i];
                                   $cases_sel        = $form_alert_sindroma_agegroup_cases_arr[$i];
                                   $order_form_alert = $order_form_alert_agegroup_arr[$id_agegroup_sel];
                                   
                                   $sql_kol   = null; 
                                   $sql_val   = null;
                                    
                                   $sql_kol[] = "id_form_alert"; 
                                   $sql_val[] = "'".ValidateVarFun::f_real_escape_string($id_pk)."'"; 

                                   $sql_kol[] = "id_syndrome"; 
                                   $sql_val[] = "'".ValidateVarFun::f_real_escape_string($id_syndrome_sel)."'"; 

                                   $sql_kol[] = "id_agegroup"; 
                                   $sql_val[] = "'".ValidateVarFun::f_real_escape_string($id_agegroup_sel)."'"; 

                                   $sql_kol[] = "order_form_alert"; 
                                   $sql_val[] = "'".$order_form_alert."'"; 

                                   $sql_kol[] = "cases"; 
                                   $sql_val[] = "'".ValidateVarFun::f_real_escape_string($cases_sel)."'"; 
                                   
                                   $sql_kol[] = "record_user_ins"; 
                                   $sql_val[] = "'".$session->Vars["ses_userid"]."'"; 
                                   
                                   $sql_kol[] = "record_status"; 
                                   $sql_val[] = "'1'"; 

                                   $sql_kol_ins = IMPLODE(",", $sql_kol);
                                   $sql_val_ins = IMPLODE(",", $sql_val);
                                   
                                   $sql_ins     = "INSERT INTO phi_form_alert_sindroma_agegroup (".$sql_kol_ins.") VALUES(".$sql_val_ins.")";
                                   WebApp::execQuery($sql_ins);
                                  }
                             //-------------------------------------------------------------------------------------------
                          //MENAXHOJME TABELAT E VARURA ------------------------------------------------------------------
                          
                          //NOTIFIKIMI ME EMAIL --------------------------------------------------------------------------
                            IF ($AKSIONI_SEL == "INSERT")
                               {
                                //KAPIM DEGET OSE QENDRAT E RAPORTIMIT ME TE CILAT ESHTE I LIDHUR USERI ------------------
                                  $user_dega_qendra = f_app_user_dega_qendra ($session->Vars["ses_userid"]);
                                //KAPIM DEGET OSE QENDRAT E RAPORTIMIT ME TE CILAT ESHTE I LIDHUR USERI ------------------

                                IF ($user_dega_qendra["tipi_userit"] == "Q")
                                   {
                                    //FORMULARIN E KA REGJISTRUAR QENDRA E RAPORTIMIT KESHTU QE NOTIFIKOJME DEGEN --------
                                      //KAPIM EMAILIET E DEGES QE DO NOTIFIKOHEN -----------------------------------------
                                        $arg_id_nem      = '118';
                                        $arg_id_action   = '101,102'; //Add Modify
                                        $arg_view_edit   = 'E';       //V/E
                                        $user_emails_arr = f_app_dega_user_emails ($kol_val_array["id_branch"], $arg_id_nem, $arg_id_action, $arg_view_edit);
                                      //----------------------------------------------------------------------------------
                                      
                                      IF (IS_ARRAY($user_emails_arr) AND (COUNT($user_emails_arr) > 0))
                                         {
                                          //id_reporting_entity ----------------------------------------------------------
                                            unset($lov);
                                            //$lov["name"]         = "id_reporting_entity";
                                            $lov["name"]           = "id_reporting_entity_dhe_tipi";
                                            $lov["id_select"]      = $id_pk;
                                            $lov["object_name"]    = "id_reporting_entity";
                                            $lov["obj_or_label"]   = "label";
                                            $reporting_entity_name = f_app_lov_default_values($lov);
                                          //id_reporting_entity --------------------------------------------------------------------------------------------------

                                          //FORMOHET MESAZHI I NOTIFIKIMIT -----------------------------------------------------
                                            $p_dt1 = SUBSTR($kol_val_array["week_date_start"], 8, 2).".".SUBSTR($kol_val_array["week_date_start"], 5, 2).".".SUBSTR($kol_val_array["week_date_start"], 0, 4);
                                            $p_dt2 = SUBSTR($kol_val_array["week_date_end"],   8, 2).".".SUBSTR($kol_val_array["week_date_end"],   5, 2).".".SUBSTR($kol_val_array["week_date_end"],   0, 4);
                                            
                                            UNSET($array_info_email);
                                            $email_message  = '<h2>'.WebApp::getVar("header_alt_mesg").'</h2>';
                                            $email_message .= WebApp::getVar("id_reporting_entity_mesg").': <b>'.$reporting_entity_name.'</b><br>';
                                            $email_message .= WebApp::getVar("email_message_form_alert_mesg").' <b>'.$kol_val_array["form_number"].'</b><br>';
                                            $email_message .= WebApp::getVar("email_periudha_e_raportimit_mesg").': <b>'.$p_dt1.' - '.$p_dt2.'</b><br>';
                                          //FORMOHET MESAZHI I NOTIFIKIMIT -----------------------------------------------------
                                
                                          $array_info_email["email_subject"] = WebApp::getVar("email_subject_form_alert_mesg");
                                          $array_info_email["email_message"] = $email_message;
                                          $array_info_email["email_to"]      = $user_emails_arr;

                                          f_app_send_email($array_info_email);
                                         }
                                   }
                               }
                          //NOTIFIKIMI ME EMAIL --------------------------------------------------------------------------
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
                    //
                  //kontrollohet per te dhena te varura ------------------------------------------------------------------

                  IF ($nr_rreshta_te_lidhura == 0)
                     {
                      //NUK E FSHIME NGA DB POR NDRYSHOJME STATUSIN E REKORDIT -------------------------------------------
                      $sql_del = "UPDATE phi_form_alert SET record_status = '0' WHERE id_form_alert = '".ValidateVarFun::f_real_escape_string($id_pk)."'";
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

function form_alert_onRender()
{
 GLOBAL $session, $event, $G_APP_VARS;
 extract($event->args);

 //data sot ----------------------------------------------------------------------------------------
   $data_sot = DATE("d.m.Y");
   WebApp::addVar("data_sot", $data_sot);
 //-------------------------------------------------------------------------------------------------
 
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
   $vars_page_var[] = "s_id_reporting_entity";
   $vars_page_var[] = "s_dt1";
   $vars_page_var[] = "s_dt2";
   $vars_page_var[] = "s_form_number";
   $vars_page_var[] = "s_id_reporting_entity_kind";
 //VARIABLAT E FAQES -------------------------------------------------------------------------------
 
 //script onRender ---------------------------------------------------------------------------------
   INCLUDE(dirname(__FILE__)."/../php_script/on_render.php");
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
       INCLUDE(dirname(__FILE__)."/../php_script/lista_info.php");
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