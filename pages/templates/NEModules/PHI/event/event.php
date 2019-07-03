<?
function event_eventHandler($event)
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
            case "refresh":
                  //shperthejme variablat e faqes dhe validojme te drejtat -----------------------------------------------
                    IF ($vars_page != "")
                       {
                        $G_APP_VARS                     = f_app_vars_page($vars_page);
                        $G_APP_VARS["id_sel"]           = $G_APP_VARS["id_pk"]."|".$NEM_ID_SEL;
                        $G_APP_VARS["gjendje"]          = "record_detaje";
                        $G_APP_VARS["editim_konsultim"] = "editim";
                       }
                  break;

		    case "save":
                  //SKRIPTI SPECIFIK PER NGJARJEN SAVE -------------------------------------------------------------------
                  $var_post      = STR_REPLACE(array("<pikp>","<_and_>"), array(";","&"), $var_post);
                  $var_post_arr  = EXPLODE("<->", $var_post);
                  $kol_array_all = EXPLODE("<_>", $var_post_arr[0]);
                  $val_array_all = EXPLODE("<_>", $var_post_arr[1]);
                         
                  //mbajme datat ne array --------------------------------------------------------------------------------
                    FOR ($i=0; $i < count($kol_array_all); $i++)
                        {
                         IF ($kol_array_all[$i] == "ids_branch")
                            {
                             //$ids_branch = $val_array_all[$i];
                            }
                         ELSE
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
                          $kolonat = "id_event,     record_user_ins,                    record_timestamp_ins";
                          $vlerat  = "'".$id_pk."', '".$session->Vars["ses_userid"]."', '".DATE("Y-m-d H:i:s")."'";

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
                          $sql_INS_UPD = "UPDATE phi_event SET ".$sql_INS_UPD." WHERE id_event = '".ValidateVarFun::f_real_escape_string($id_pk)."'";
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
                            IF (ISSET($kol_val_array["ids_branch"]))
                               {
                                //shtojme nje id = -1 per rastin kur jane fshire gjithe deget
                                  $ids_branch_not_del = $kol_val_array["ids_branch"];
                                  
                                  IF ($ids_branch_not_del == "")
                                     {
                                      $ids_branch_not_del = '-1'; 
                                     }
                                //----------------------------------------------------------------------------------------
                                
                                //FSHIME phi_event_branch ----------------------------------------------------------------
                                  $sql_del = "DELETE 
                                                FROM phi_event_branch 
                                               WHERE id_event = '".ValidateVarFun::f_real_escape_string($id_pk)."' AND
                                                     id_branch NOT IN (".$ids_branch_not_del.")
                                             ";
                                  WebApp::execQuery($sql_del);
                                //----------------------------------------------------------------------------------------

                                //FSHIME phi_docs, phi_docs_file ---------------------------------------------------------
                                  $sql = "SELECT id_doc
                                            FROM phi_docs 
                                           WHERE id_event = '".ValidateVarFun::f_real_escape_string($id_pk)."' AND
                                                 id_branch NOT IN (".$ids_branch_not_del.")
                                         ";
                       
                                  $rs  = WebApp::execQuery($sql);
                                  $rs->MoveFirst();
                                  WHILE (!$rs->EOF())
	                                    {
	                                     $id_doc  = $rs->Field("id_doc");

                                         $sql_del = "DELETE FROM phi_docs                 WHERE id_doc = '".ValidateVarFun::f_real_escape_string($id_doc)."'";
                                         WebApp::execQuery($sql_del);

                                         $sql_del = "DELETE FROM phi_docs_fulltext_search WHERE id_doc = '".ValidateVarFun::f_real_escape_string($id_doc)."'";
                                         WebApp::execQuery($sql_del);

                                         $rs->MoveNext();
                                        }
                                //----------------------------------------------------------------------------------------

                                //REGJISTROJME phi_event_branch ----------------------------------------------------------
                                  IF ($kol_val_array["ids_branch"] != "")
                                     {
                                      UNSET($id_branch_notifikim_arr);
                                      
                                      $ids_branch_arr = EXPLODE(",", $kol_val_array["ids_branch"]);
                                  
                                      FOR ($i=0; $i < count($ids_branch_arr); $i++)
                                          {
                                           $id_branch_sel = $ids_branch_arr[$i];
                                       
                                           $branch_exist = 0;
                                           $sql = "SELECT COUNT(1) as branch_exist 
                                                     FROM phi_event_branch 
                                                    WHERE id_event  = '".ValidateVarFun::f_real_escape_string($id_pk)."' AND
                                                          id_branch = '".ValidateVarFun::f_real_escape_string($id_branch_sel)."'
                                                  ";

                                           $rs = WebApp::execQuery($sql);
                                           IF (!$rs->EOF())
                                              {
                                               $branch_exist = $rs->Field("branch_exist");
                                              }
                                       
                                           IF ($branch_exist == 0)
                                              {
                                               $sql_kol     = null; 
                                               $sql_val     = null;
                                    
                                               $sql_kol[]   = "id_event"; 
                                               $sql_val[]   = "'".ValidateVarFun::f_real_escape_string($id_pk)."'"; 

                                               $sql_kol[]   = "id_branch"; 
                                               $sql_val[]   = "'".ValidateVarFun::f_real_escape_string($id_branch_sel)."'"; 

                                               $sql_kol[]   = "record_user_ins"; 
                                               $sql_val[]   = "'".$session->Vars["ses_userid"]."'"; 
                                   
                                               $sql_kol[]   = "record_timestamp_ins"; 
                                               $sql_val[]   = "'".DATE("Y-m-d H:i:s")."'"; 
                                   
                                               $sql_kol_ins = IMPLODE(",", $sql_kol);
                                               $sql_val_ins = IMPLODE(",", $sql_val);
                                   
                                               $sql_ins     = "INSERT INTO phi_event_branch (".$sql_kol_ins.") VALUES(".$sql_val_ins.")";
                                               WebApp::execQuery($sql_ins);

                                               //PER NOTIFIKIMIN E QENDRES SE RE ME EMAIL---------------------------------
                                                 IF ($id_branch_sel != $kol_val_array["id_branch"])
                                                    {
                                                     $id_branch_notifikim_arr[$id_branch_sel] = $id_branch_sel;
                                                    }
                                               //-------------------------------------------------------------------------
                                              }
                                          }
                                          
                                      //NOTIFIKIMOJME QENDREN E RE ME EMAIL-----------------------------------------------
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
                                                  $email_message .= WebApp::getVar("email_mesage_event_mesg").': <b>'.$kol_val_array["subject"].'</b><br>';
                                                  $email_message .= WebApp::getVar("jeni_ngarkuar_event_mesg").'<br>';
                                                //FORMOHET MESAZHI I NOTIFIKIMIT -----------------------------------------
                                    
                                                $array_info_email["email_subject"] = WebApp::getVar("email_subject_event_mesg");
                                                $array_info_email["email_message"] = $email_message;
                                                $array_info_email["email_to"]      = $user_emails_arr;
    
                                                f_app_send_email($array_info_email);
                                               }
                                           }
                                      //NOTIFIKIMOJME QENDREN E RE ME EMAIL-----------------------------------------------
                                     }
                                //----------------------------------------------------------------------------------------
                               }
                          //MENAXHOJME TABELAT E VARURA ------------------------------------------------------------------
                         }

                      $G_APP_VARS["id_sel"]           = $id_pk."|".$NEM_ID_SEL;
                      $G_APP_VARS["gjendje"]          = "record_detaje";
                      $G_APP_VARS["editim_konsultim"] = "editim";
                    //EKZEKUTOHET SQL ------------------------------------------------------------------------------------
                  //regjistrohen vlerat ne db ----------------------------------------------------------------------------
                  break;

		    case "save_info":
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
                      $nem_rights = NemsManager::getFeRightsToNem($NEM_ID_SEL);
                      
                      IF ($id_pk == "")
                         {
                          $id_action_sel = 101; //Add
                         }
                      ELSE
                         {
                          $id_action_sel = 102; //Modify
                         }

                      IF (ISSET($nem_rights[$NEM_ID_SEL][$id_action_sel]) AND ($nem_rights[$NEM_ID_SEL][$id_action_sel] != ""))
                         {
                          //useri ka te drejte te Add/Modify ... jemi ok
                         }
                      ELSE
                         {
                          UNSET($vars_page_arr);
                          $G_APP_VARS["kodi"]  = "error";
                          $G_APP_VARS["mesg"]  = "{{ruajtja_deshtoi_mesg}} {{nuk_keni_te_drejte_mesg}}";
                          $G_APP_VARS["time"]  = "15";
                          RETURN;
                         }
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
                    $post_id_arr    = EXPLODE(",", $id_pk);
                    $post_id_event  = $post_id_arr[0];
                    $post_id_branch = $post_id_arr[1];

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
                          $sql_INS_UPD = "UPDATE phi_event_branch 
                                             SET ".$sql_INS_UPD." 
                                           WHERE id_event  = '".ValidateVarFun::f_real_escape_string($post_id_event)."' AND
                                                 id_branch = '".ValidateVarFun::f_real_escape_string($post_id_branch)."'
                                         ";
                        //RASTI UPDATE -----------------------------------------------------------------------------------

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
                          //NOTIFIKIMOJME QENDRAT E TJERA NE RAST SE EVENTI KA STATUSIN E përfunduar ---------------------
                            IF ($kol_val_array["event_status"] == 3)
                               {
                                //KAPIM QENDRAT QE JANE TE LIDHUR ME EVENTIN ---------------------------------------------
                                  UNSET($id_branch_notifikim_arr);

                                  //kapim id_branch qe ka hapur eventin tabelen phi_event --------------------------------
                                    $sql1 = 'SELECT id_branch,
                                                    subject
                                               FROM phi_event 
                                              WHERE id_event = "'.ValidateVarFun::f_real_escape_string($post_id_event).'"
	                                        ';

                                    $rs1 = WebApp::execQuery($sql1);
                                    IF (!$rs1->EOF())
                                       {
                                        $event_id_branch = $rs1->Field('id_branch');
                                        $event_subject   = $rs1->Field('subject');

                                        IF ($event_id_branch != $post_id_branch)
                                           {
                                            $id_branch_notifikim_arr[$event_id_branch] = $event_id_branch;
                                           }
                                       }
                                  //--------------------------------------------------------------------------------------

                                  //kapim tabelen phi_event_branch -------------------------------------------------------
                                    $sql1 = 'SELECT id_branch
                                               FROM phi_event_branch 
                                              WHERE id_event   = "'.ValidateVarFun::f_real_escape_string($post_id_event).'" AND
                                                    id_branch != "'.ValidateVarFun::f_real_escape_string($post_id_branch).'"
	                                        ';

                                    $rs1 = WebApp::execQuery($sql1);
                                    $rs1->MoveFirst();
                                    WHILE (!$rs1->EOF())
	                                      {
                                           $id_branch_notifikim                           = $rs1->Field('id_branch');
                                           $id_branch_notifikim_arr[$id_branch_notifikim] = $id_branch_notifikim;
                        
                                           $rs1->MoveNext();
                                          }
                                  //--------------------------------------------------------------------------------------

                                  //NOTIFIKIMOJME QENDRAT ME EMAIL--------------------------------------------------------
                                    IF (ISSET($id_branch_notifikim_arr) AND IS_ARRAY($id_branch_notifikim_arr))
                                       {
                                        //id_branch ----------------------------------------------------------------------
                                          unset($lov);
                                          $lov["name"]         = "id_branch";
                                          $lov["id_select"]    = $post_id_branch;
                                          $lov["obj_or_label"] = "label";
                                          $id_branch_name      = f_app_lov_default_values($lov);
                                        //id_branch ----------------------------------------------------------------------

                                        //event_status -------------------------------------------------------------------
                                          unset($lov);
                                          $lov["name"]         = "event_status";
                                          $lov["id_select"]    = $kol_val_array["event_status"];
                                          $lov["obj_or_label"] = "label";
                                          $event_status_name   = f_app_lov_default_values($lov);
                                        //event_status -------------------------------------------------------------------

                                        $ids_branch_notifikim  = implode(",", $id_branch_notifikim_arr);

                                        //KAPIM EMAILIET E DEGES QE DO NOTIFIKOHEN, pervec useri qe po ben regjistrimin
                                          $arg_id_nem      = '122';
                                          $arg_id_action   = '102'; //Modify
                                          $arg_view_edit   = 'V';   //V/E
                                          $user_emails_arr = f_app_dega_user_emails ($ids_branch_notifikim, $arg_id_nem, $arg_id_action, $arg_view_edit);
                                        //--------------------------------------------------------------------------------

                                        IF (IS_ARRAY($user_emails_arr) AND (COUNT($user_emails_arr) > 0))
                                           {
                                            //FORMOHET MESAZHI I NOTIFIKIMIT ---------------------------------------------
                                              UNSET($array_info_email);
                                              $email_message  = '<h2>'.WebApp::getVar("header_alt_mesg").'</h2>';
                                              $email_message .= WebApp::getVar("event_mesg").': <b>'.$event_subject.'</b><br>';
                                              $email_message .= WebApp::getVar("id_branch_mesg").': <b>'.$id_branch_name.'</b><br>';
                                              $email_message .= WebApp::getVar("event_status_mesg").': <b>'.$event_status_name.'</b><br>';
                                            //FORMOHET MESAZHI I NOTIFIKIMIT ---------------------------------------------
                                  
                                            $array_info_email["email_subject"] = WebApp::getVar("email_subject_event_fund_mesg");
                                            $array_info_email["email_message"] = $email_message;
                                            $array_info_email["email_to"]      = $user_emails_arr;
    
                                            f_app_send_email($array_info_email);
                                           }
                                       }
                                  //NOTIFIKIMOJME QENDREN E RE ME EMAIL-----------------------------------------------
                               }
                          //NOTIFIKIMI ME EMAIL ---------------------------------------------------------


                          $G_APP_VARS["kodi"]         = "success";
                          $G_APP_VARS["mesg"]         = "{{ruajtja_sukses_mesg}}";
                          $G_APP_VARS["time"]         = "5";
                          
                          //KETU DUHET TE LESHOJME SINJAL TE RIFRESKOJME PAMJEN POSHTE -----------------------------------
                            $G_APP_VARS["parent_refresh"] = "Y";
                          //----------------------------------------------------------------------------------------------
                         }

                      $G_APP_VARS["id_sel"]           = $id_pk."|".$NEM_ID_SEL;
                      $G_APP_VARS["gjendje"]          = "record_detaje_info";
                      $G_APP_VARS["editim_konsultim"] = "editim";
                    //EKZEKUTOHET SQL ------------------------------------------------------------------------------------
                  //regjistrohen vlerat ne db ----------------------------------------------------------------------------
                  break;

		    case "save_doc":
                  
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
                      $id_pk      = $vars_page_arr["id_pk"]; //id_pk NE KETE RAST ESHTE E PERBERE DHE NGA ID_EVENTI
                      $nem_rights = NemsManager::getFeRightsToNem($NEM_ID_SEL);
                      
                      //IF ($id_pk == "")
                      //   {
                          $id_action_sel_add = 101; //Add
                      //   }
                      //ELSE
                      //   {
                          $id_action_sel_upd = 102; //Modify
                      //   }

                      IF (ISSET($nem_rights[$NEM_ID_SEL][$id_action_sel_add]) OR ($nem_rights[$NEM_ID_SEL][$id_action_sel_upd] != ""))
                         {
                          //useri ka te drejte te Add/Modify ... jemi ok
                         }
                      ELSE
                         {
                          UNSET($vars_page_arr);
                          $G_APP_VARS["kodi"]  = "error";
                          $G_APP_VARS["mesg"]  = "{{ruajtja_deshtoi_mesg}} {{nuk_keni_te_drejte_mesg}}";
                          $G_APP_VARS["time"]  = "15";
                          RETURN;
                         }

                      //keto i ekstraktojme nga ID E DOKUMENTIT
                      //$post_id_arr    = EXPLODE(",", $id_pk);
                      //$post_id_event  = $post_id_arr[0];
                      //$post_id_branch = $post_id_arr[1];
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
                    IF (ISSET($id_doc_upload) AND ($id_doc_upload != ""))
                       {
                        $id_sel_nem_id = f_app_decrypt($id_doc_upload, DESK_KEY, DESK_IV);
                       }
                    ELSE
                       {
                        UNSET($vars_page_arr);
                        $G_APP_VARS["kodi"]  = "error";
                        $G_APP_VARS["mesg"]  = "{{ruajtja_deshtoi_mesg}}";
                        $G_APP_VARS["time"]  = "10";
                        RETURN;
                       }

                    $id_sel_nem_id_arr = EXPLODE("|", $id_sel_nem_id);
                    $post_id           = $id_sel_nem_id_arr[0];
                    $post_id_nem       = $id_sel_nem_id_arr[1];
                    
                    //VALIDOJME NEMIN QE EDHTE TEK ID E DOKUMENTIT -------------------------------------------------------
                      IF ($post_id_nem != $NEM_ID_SEL)
                         {
                          UNSET($vars_page_arr);
                          $G_APP_VARS["kodi"]  = "error";
                          $G_APP_VARS["mesg"]  = "{{ruajtja_deshtoi_mesg}}";
                          $G_APP_VARS["time"]  = "10";
                          RETURN;
                         }
                    //VALIDOJME NEMIN QE EDHTE TEK ID E DOKUMENTIT -------------------------------------------------------

                    $post_id_arr          = UNSERIALIZE($post_id);
                    $post_id_doc          = $post_id_arr["id_doc"];
                    $post_id_event        = $post_id_arr["id_event"];
                    $post_id_branch       = $post_id_arr["id_branch"];
                    $post_id_doc_category = $post_id_arr["id_doc_category"];
                    
                    //RASTI UPDATE ---------------------------------------------------------------------------------------
                      $sql_INS_UPD = "record_status = '1'";

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
                      $sql_INS_UPD = "UPDATE phi_docs 
                                         SET ".$sql_INS_UPD." 
                                       WHERE id_doc  = '".ValidateVarFun::f_real_escape_string($post_id_doc)."'
                                     ";
                      //print $sql_INS_UPD;
                    //RASTI UPDATE ---------------------------------------------------------------------------------------

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
                          
                          //SHOHIM NESE DUHET TE REGULLOJME PATHIN E SKEDARIT SE MOS ESHTE RUAJTUR NEN NDONJE DEGE TJETER 
                            $sql = "SELECT file_path AS file_path_db,
                                           file_name as file_name_db
                                      FROM phi_docs
                                     WHERE id_doc = '".ValidateVarFun::f_real_escape_string($post_id_doc)."'
                                   ";
       
                            $rs  = WebApp::execQuery($sql);
                            IF (!$rs->EOF())
                               {
                                $file_path_db = $rs->Field("file_path_db");
                                $file_name_db = $rs->Field("file_name_db");
                               }
                            
                            $file_path  =  ValidateVarFun::f_only_all_natural_numbers($kol_val_array["id_branch"])."/";
                            $file_path .=  ValidateVarFun::f_only_numbers($post_id_doc_category)."/";
                            $file_path .= $post_id_doc.'_'.$file_name_db;
                            
                            IF ($file_path != $file_path_db)
                               {
                                //kopjojme skedarin tek direktoria e sakte
                                  $file_name_disk_old = PATH_ROOT_DOCS.$file_path_db;
                                  $file_name_disk_new = PATH_ROOT_DOCS.$file_path;

                                  //KOPJOHET SKEDARI ---------------------------------------------------------------------
                                    //CHMOD($_FILES["file"]["tmp_name"], 0644); //ndryshojme te drejtat e skedarit qe ta lexoje user aspcache
                                    $cmd_copy_file    = USER_SUDO.PATH_SCRIPT_COPY_FILE." ".$file_name_disk_old." ".$file_name_disk_new." N";
                 
                                    exec($cmd_copy_file, $doc_write_sukses);
                                    IF ($doc_write_sukses[0] == "1")
                                       {
                                        //skedari eshte kopjuar ne regull tek direktoria e re ... 
                                        //fshime skedarin nga direktoria e vjeter
                                          $cmd_delete_file   = USER_SUDO.PATH_SCRIPT_DELETE_FILE." ".$file_name_disk_old;
                                          //$doc_delete_sukses = passthru($cmd_delete_file);
                                          exec($cmd_delete_file, $doc_delete_sukses);
                                        //FSHIHET SKEDARI ----------------------------------------------------------------
                                        
                                        //UPDEJTOJME PATHIN NE DB --------------------------------------------------------
                                          $sql_upd = "UPDATE phi_docs 
                                                         SET file_path = '".ValidateVarFun::f_real_escape_string($file_path)."'
                                                       WHERE id_doc    = '".ValidateVarFun::f_real_escape_string($post_id_doc)."'
                                                     ";
                                          WebApp::execQuery($sql_upd);
                                        //UPDEJTOJME PATHIN NE DB --------------------------------------------------------
                                       }
                                    ELSE
                                       {
                                        $G_APP_VARS["kodi"]         = "error";
                                        $G_APP_VARS["mesg"]         = "{{ruajtja_deshtoi_mesg}}";
                                        $G_APP_VARS["time"]         = "10";

                                        $G_APP_VARS["kol_val_post"] = $kol_val_array;
                                       }
                                //KOPJOHET SKEDARI -----------------------------------------------------------------------
                               }
                          //SHOHIM NESE DUHET TE REGULLOJME PATHIN E SKEDARIT --------------------------------------------
                          
                          //KETU DUHET TE LESHOJME SINJAL TE RIFRESKOJME PAMJEN POSHTE -----------------------------------
                            $G_APP_VARS["parent_refresh"] = "Y";
                          //----------------------------------------------------------------------------------------------
                         }

                      $id_pk                          = $post_id_event.','.$post_id_branch.','.$post_id_doc; //id_event,id_branch,id_doc
                      $G_APP_VARS["id_sel"]           = $id_pk."|".$NEM_ID_SEL;
                      $G_APP_VARS["gjendje"]          = "record_detaje_doc";
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
                      $sql_del = "UPDATE phi_event SET record_status = '0' WHERE id_event = '".ValidateVarFun::f_real_escape_string($id_pk)."'";
                      WebApp::execQuery($sql_del);

                      $sql_del = "DELETE FROM phi_event_branch             WHERE id_event = '".ValidateVarFun::f_real_escape_string($id_pk)."'";
                      WebApp::execQuery($sql_del);

                      $sql_del = "DELETE FROM phi_docs_fulltext_search WHERE id_doc IN (SELECT id_doc FROM phi_docs WHERE id_event = '".ValidateVarFun::f_real_escape_string($id_pk)."')";
                      WebApp::execQuery($sql_del);

                      $sql_del = "DELETE FROM phi_docs                 WHERE id_event = '".ValidateVarFun::f_real_escape_string($id_pk)."'";
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

            case "del_doc":
                  //shperthejme variablat e faqes dhe validojme te drejtat -----------------------------------------------
                    IF ($vars_page != "")
                       {
                        $vars_page_arr = f_app_vars_page($vars_page);
                       }
                    
                    IF (!ISSET($vars_page_arr["form_id_token"]) OR ($vars_page_arr["form_id_token"] != "Y"))
                       {
                        UNSET($vars_page_arr);
                        RETURN;
                       }
                    
                    //VALIDOJME QE KERKESA KA ARDHUR PO NGA KY NEM -------------------------------------------------------
                      $kush_jam_une = f_app_kush_nem_jam_une(__FILE__);
                      $NEM_ID_SEL   = $kush_jam_une["nem_id"];
                    
                      IF (!ISSET($vars_page_arr["nem_id"]) OR ($vars_page_arr["nem_id"] != $NEM_ID_SEL) OR ($NEM_ID_SEL == 0))
                         {
                          UNSET($vars_page_arr);
                          $G_APP_VARS["kodi"]  = "error";
                          $G_APP_VARS["mesg"]  = "{{fshirja_deshtoi_mesg}}";
                          $G_APP_VARS["time"]  = "10";
                          RETURN;
                         }
                    //----------------------------------------------------------------------------------------------------

                    //validojme te drejtat tek nemi ----------------------------------------------------------------------
                      $nem_rights = NemsManager::getFeRightsToNem($NEM_ID_SEL);
                      
                      IF (ISSET($nem_rights[$NEM_ID_SEL][103]) AND ($nem_rights[$NEM_ID_SEL][103] != ""))
                         {
                          //useri ka te drejte te Delete ... jemi ok
                         }
                      ELSE
                         {
                          UNSET($vars_page_arr);
                          $G_APP_VARS["kodi"]  = "error";
                          $G_APP_VARS["mesg"]  = "{{fshirja_deshtoi_mesg}} {{nuk_keni_te_drejte_mesg}}";
                          $G_APP_VARS["time"]  = "15";
                          RETURN;
                         }
                    //----------------------------------------------------------------------------------------------------
                  //------------------------------------------------------------------------------------------------------

                  $id_pk          = $vars_page_arr["id_pk"];
                  $post_id_arr    = EXPLODE(",", $id_pk);
                  $post_id_event  = $post_id_arr[0];
                  $post_id_branch = $post_id_arr[1];
                  $post_id_doc    = $post_id_arr[2];

                  //kontrollohet per te dhena te varura ------------------------------------------------------------------
                    //
                  //kontrollohet per te dhena te varura ------------------------------------------------------------------

                  IF ($nr_rreshta_te_lidhura == 0)
                     {
                      //FSHIME SKEDARIN NGA FILE SYSTEM ------------------------------------------------------------------      
                        $sql = "SELECT file_path AS file_path_db
                                  FROM phi_docs
                                 WHERE id_doc = '".ValidateVarFun::f_real_escape_string($post_id_doc)."'
                               ";
       
                        $rs  = WebApp::execQuery($sql);
                        IF (!$rs->EOF())
                           {
                            $file_path_db = $rs->Field("file_path_db");
                           }

                        $file_name_disk = PATH_ROOT_DOCS.$file_path_db;

                        $cmd_delete_file   = USER_SUDO.PATH_SCRIPT_DELETE_FILE." ".$file_name_disk;
                        exec($cmd_delete_file, $doc_delete_sukses);
                      //FSHIME SKEDARIN NGA FILE SYSTEM ------------------------------------------------------------------      

                      //FSHIME SKEDARIN NGA DB ---------------------------------------------------------------------------
                        $sql_del = "DELETE FROM phi_docs                 WHERE id_doc = '".ValidateVarFun::f_real_escape_string($post_id_doc)."'";
                        WebApp::execQuery($sql_del);

                        $sql_del = "DELETE FROM phi_docs_fulltext_search WHERE id_doc = '".ValidateVarFun::f_real_escape_string($post_id_doc)."'";
                        WebApp::execQuery($sql_del);
                      //FSHIME SKEDARIN NGA DB ---------------------------------------------------------------------------

                      $G_APP_VARS["kodi"]  = "success";
                      $G_APP_VARS["mesg"]  = "{{fshirja_sukses_mesg}}";
                      $G_APP_VARS["time"]  = "5";

                      $G_APP_VARS["parent_refresh"] = "Y";

                      $id_pk                        = $post_id_event.',0,0'; //id_event,id_branch,id_doc
                     }
                  ELSE
                     {
                      $G_APP_VARS["kodi"]  = "warning";
                      $G_APP_VARS["mesg"]  = "{{te_dhena_te_varura_mesg}}";
                      $G_APP_VARS["time"]  = "15";

                      $id_pk               = $post_id_event.','.$post_id_branch.','.$post_id_doc; //id_event,id_branch,id_doc
                     }

                  $G_APP_VARS["id_sel"]           = $id_pk."|".$NEM_ID_SEL;
                  $G_APP_VARS["gjendje"]          = "record_detaje_doc";
                  $G_APP_VARS["editim_konsultim"] = "editim";
                  break;

           }
}

function event_onRender()
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
   $vars_page_var[] = "s_event_status";
   $vars_page_var[] = "s_id_event_source";
   $vars_page_var[] = "s_channel_registration";
   $vars_page_var[] = "s_dt1";
   $vars_page_var[] = "s_dt2";
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

   IF ($gjendje_sel == "record_detaje_info")
      {
       $template_sel = "DETAJE";
       INCLUDE(dirname(__FILE__)."/add_edit_info.php");
      }

   IF ($gjendje_sel == "record_detaje_doc")
      {
       $template_sel = "DETAJE";
       INCLUDE(dirname(__FILE__)."/add_edit_doc.php");
      }

   WebApp::addVar("template_sel",      $template_sel);
   WebApp::addVar("script_js_in_page", $script_js_in_page);
 //formohet html -----------------------------------------------------------------------------------
}
?>