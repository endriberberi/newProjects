<?
function event_reporting_eventHandler($event)
{
    GLOBAL $session, $event, $G_APP_VARS;
    extract($event->args);

	switch ($event->name)
	       {
		    case "save":
                  //shperthejme variablat e faqes dhe validojme te drejtat -----------------------------------------------
                    IF ($vars_page != "")
                       {
                        $vars_page_arr = f_app_vars_page($vars_page);
                       }
                    
                    IF (!ISSET($vars_page_arr["form_id_token"]) OR ($vars_page_arr["form_id_token"] != "Y"))
                       {
                        UNSET($vars_page_arr);
                        //NUK PO NXJERIM MESAZH SE MUND TE JETE RASTI REFRESH 
                        //$G_APP_VARS["kodi"]  = "error";
                        //$G_APP_VARS["mesg"]  = "{{ruajtja_deshtoi_mesg}}";
                        //$G_APP_VARS["time"]  = "10";
                        RETURN;
                       }
                    
                    //VALIDOJME QE KERKESA KA ARDHUR PO NGA KY NEM -------------------------------------------------------
                      $kush_jam_une = f_app_kush_nem_jam_une(__FILE__);
                      $NEM_ID_SEL   = $kush_jam_une["nem_id"];
                    
                      IF (!ISSET($vars_page_arr["nem_id"]) OR ($vars_page_arr["nem_id"] != $NEM_ID_SEL) OR ($NEM_ID_SEL == 0))
                         {
                          UNSET($vars_page_arr);
                          $G_APP_VARS["kodi"]  = "error";
                          $G_APP_VARS["mesg"]  = "{{ruajtja_deshtoi_mesg}}";
                          $G_APP_VARS["time"]  = "10";
                          RETURN;
                         }
                    //----------------------------------------------------------------------------------------------------

                    //validojme te drejtat tek nemi ----------------------------------------------------------------------
                      $id_pk      = $vars_page_arr["id_pk"];
                    //----------------------------------------------------------------------------------------------------
                  //------------------------------------------------------------------------------------------------------
                  
                  $var_post      = STR_REPLACE(array("<pikp>","<_and_>"), array(";","&"), $var_post);
                  $var_post_arr  = EXPLODE("<->", $var_post);
                  $kol_array_all = EXPLODE("<_>", $var_post_arr[0]);
                  $val_array_all = EXPLODE("<_>", $var_post_arr[1]);
                         
                  //mbajme datat ne array --------------------------------------------------------------------------------
                    FOR ($i=0; $i < count($kol_array_all); $i++)
                        {
                         $kol_array[] = $kol_array_all[$i];
                         $val_array[] = $val_array_all[$i];

                         $kol_val_array[$kol_array_all[$i]] = $val_array_all[$i];
                        }
                  //mbajme datat ne array --------------------------------------------------------------------------------

                  //regjistrohen vlerat ne db ----------------------------------------------------------------------------
                        $AKSIONI_SEL = "INSERT";
                        
                        //gjenerohet max_id ------------------------------------------------------------------------------
                          $max_id = 0;
                          $sql = "SELECT IF(id_event IS NULL, 0, MAX(id_event)) as max_id 
                                    FROM phi_event 
                                 ";
                          $rs = WebApp::execQuery($sql);
                          IF (!$rs->EOF())
                             {
                              $max_id = $rs->Field("max_id");
                             }
                          
                          $id_pk = $max_id + 1;
                        //gjenerohet max_id ------------------------------------------------------------------------------

                        //RASTI INSERT -----------------------------------------------------------------------------------
                          IF (strlen($kol_val_array["description"]) > 50)
                             {
                              $subject = SUBSTR($kol_val_array["description"], 0, 50)." ...";
                             }
                          ELSE
                             {
                              $subject = $kol_val_array["description"];
                             }

                          //KAPIM DEGEN KU KA NDODHUR NGJARJA ------------------------------------------------------------
                            $id_branch = "";
                            IF (ISSET($kol_val_array["place_id_municipality"]) AND ($kol_val_array["place_id_municipality"] != ""))
                               {
                                $id_branch = f_app_id_branch_municipality_village($kol_val_array["place_id_municipality"], $kol_val_array["place_id_village"]);
                               }
                            
                            IF ($id_branch == "")
                               {
                                $id_branch = 1; //ne rast se nuk eshte kapur dega e regjistrojme tek dega qendrore
                               }
                          //KAPIM DEGEN KU KA NDODHUR NGJARJA ------------------------------------------------------------
                          
                          $kolonat = "id_event,     id_branch,        id_event_source, channel_registration, date,                event_status, subject,                                              record_user_ins,                    record_timestamp_ins";
                          $vlerat  = "'".$id_pk."', '".$id_branch."', 1,               2,                    '".DATE("Y-m-d")."', 1,            '".ValidateVarFun::f_real_escape_string($subject)."', '".$session->Vars["ses_userid"]."', '".DATE("Y-m-d H:i:s")."'";

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

                            $sql_INS_UPD = "INSERT INTO phi_event (".$kolonat.") VALUES(".$vlerat.")";
                            WebApp::execQuery($sql_INS_UPD);

                            //REGJISTROJME EDHE DEGEN NE NGARKIM ---------------------------------------------------------
                              $sql_INS_UPD = "INSERT INTO phi_event_branch (id_event,     id_branch,        event_status, record_user_ins,                    record_timestamp_ins)
                                                                    VALUES ('".$id_pk."', '".$id_branch."', 1,            '".$session->Vars["ses_userid"]."', '".DATE("Y-m-d H:i:s")."')
                                             ";
                              WebApp::execQuery($sql_INS_UPD);
                            //REGJISTROJME EDHE DEGEN NE NGARKIM ---------------------------------------------------------
                        //RASTI INSERT -----------------------------------------------------------------------------------

                        //EKZEKUTOHET SQL --------------------------------------------------------------------------------
                          $G_APP_VARS["kodi"]         = "success";
                          $G_APP_VARS["mesg"]         = "{{ruajtja_sukses_mesg}}";
                          $G_APP_VARS["time"]         = "5";


                          //NOTIFIKIMOJME QENDREN E RE ME EMAIL-----------------------------------------------------------
                            $id_branch_notifikim_arr[$id_branch] = $id_branch; //qendra baze
                            
                            IF (ISSET($id_branch_notifikim_arr) AND IS_ARRAY($id_branch_notifikim_arr))
                               {
                                $ids_branch_notifikim = implode(",", $id_branch_notifikim_arr);

                                //KAPIM EMAILIET E DEGES QE DO NOTIFIKOHEN, pervec useri qe po ben regjistrimin
                                  $arg_id_nem      = '122';
                                  $arg_id_action   = '102'; //Modify
                                  $arg_view_edit   = 'E';   //V/E
                                  $user_emails_arr = f_app_dega_user_emails ($ids_branch_notifikim, $arg_id_nem, $arg_id_action, $arg_view_edit);
                                //----------------------------------------------------------------------------
                              
                                IF (IS_ARRAY($user_emails_arr) AND (COUNT($user_emails_arr) > 0))
                                   {
                                    //FORMOHET MESAZHI I NOTIFIKIMIT -----------------------------------------
                                      UNSET($array_info_email);
                                      $email_message  = '<h2>'.WebApp::getVar("header_alt_mesg").'</h2>';
                                      $email_message .= WebApp::getVar("email_mesage_event_mesg").': <b>'.$subject.'</b><br>';
                                      $email_message .= WebApp::getVar("verifikim_event_mesg").'<br>';
                                    //FORMOHET MESAZHI I NOTIFIKIMIT -----------------------------------------
                          
                                    $array_info_email["email_subject"] = WebApp::getVar("email_subject_event_mesg");
                                    $array_info_email["email_message"] = $email_message;
                                    $array_info_email["email_to"]      = $user_emails_arr;
    
                                    f_app_send_email($array_info_email);
                                   }
                               }
                          //NOTIFIKIMOJME QENDREN E RE ME EMAIL-----------------------------------------------

                      //$G_APP_VARS["id_sel"]           = $id_pk."|".$NEM_ID_SEL;
                      $G_APP_VARS["gjendje"]          = "record_detaje_save";
                      //$G_APP_VARS["editim_konsultim"] = "editim";
                    //EKZEKUTOHET SQL ------------------------------------------------------------------------------------
                  //regjistrohen vlerat ne db ----------------------------------------------------------------------------
                  break;
           }
}

function event_reporting_onRender()
{
 GLOBAL $session, $event, $G_APP_VARS;
 extract($event->args);

 //data sot ----------------------------------------------------------------------------------------
   $data_sot = DATE("d.m.Y");
   WebApp::addVar("data_sot", $data_sot);
 //-------------------------------------------------------------------------------------------------
 
 //KAPIM DEGET OSE QENDRAT E RAPORTIMIT ME TE CILAT ESHTE I LIDHUR USERI ---------------------------
   //$user_dega_qendra = f_app_user_dega_qendra ($session->Vars["ses_userid"]);
 //KAPIM DEGET OSE QENDRAT E RAPORTIMIT ME TE CILAT ESHTE I LIDHUR USERI ---------------------------

 //KAPIM KUSH NEM JEMI -----------------------------------------------------------------------------
   $kush_jam_une = f_app_kush_nem_jam_une(__FILE__);
   
   $NEM_ID_SEL   = $kush_jam_une["nem_id"];
   $WEBBOX_SEL   = $kush_jam_une["webbox"];
 //KAPIM KUSH NEM JEMI -----------------------------------------------------------------------------

 //VARIABLAT E FAQES -------------------------------------------------------------------------------
   //$vars_page_var[] = "nr_rec_page";
   //$vars_page_var[] = "nr_rec_start";
   //$vars_page_var[] = "order_by";
   //$vars_page_var[] = "order_by_indx";
   
   //$vars_page_var[] = "s_id_branch";
   //$vars_page_var[] = "s_event_status";
   //$vars_page_var[] = "s_id_event_source";
   //$vars_page_var[] = "s_channel_registration";
   //$vars_page_var[] = "s_dt1";
   //$vars_page_var[] = "s_dt2";
 //VARIABLAT E FAQES -------------------------------------------------------------------------------
 
 //script onRender ---------------------------------------------------------------------------------
   $id_sel        = "";
   $id_add        = f_app_encrypt($id_sel.'|'.$NEM_ID_SEL, DESK_KEY, DESK_IV);

   $G_APP_VARS["post_id"]          = $id_add;
   $G_APP_VARS["editim_konsultim"] = "editim";

   INCLUDE(dirname(__FILE__)."/../php_script/on_render.php");
 //script onRender ---------------------------------------------------------------------------------

 //formohet html -----------------------------------------------------------------------------------
   //gjendje ---------------------------------------------------------------------------------------
     $gjendje_sel = "record_detaje";
     IF (ISSET($G_APP_VARS["gjendje"]) AND ($G_APP_VARS["gjendje"] != "") )
        {
         $gjendje_sel = $G_APP_VARS["gjendje"];
        }   
   //gjendje ---------------------------------------------------------------------------------------

   $template_sel      = "";
   $script_js_in_page = "";
   
   IF ($gjendje_sel == "record_detaje")
      {
       $template_sel = "DETAJE";
       INCLUDE(dirname(__FILE__)."/add_edit.php");
      }

   IF ($gjendje_sel == "record_detaje_save")
      {
       $template_sel = "DETAJE";
       INCLUDE(dirname(__FILE__)."/add_konfirm.php");
      }

   WebApp::addVar("template_sel",      $template_sel);
   WebApp::addVar("script_js_in_page", $script_js_in_page);
 //formohet html -----------------------------------------------------------------------------------
}
?>