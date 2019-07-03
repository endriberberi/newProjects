<?
//kushti i kerkimit ------------------------------------------------------------------------------------------------------
  $kushti_kerkim  = '';

 //APLIKOJME LIDHJEN E USERIT ME DEGE/QENDER -----------------------------------------------------------------------------
   
   //print_r($user_dega_qendra);
   $kushti_kerkim .= ' AND (phi_event.share = "Y" OR phi_event.id_branch IN ('.$user_dega_qendra["view_branch_ids"].') OR phi_event_branch.id_branch IN ('.$user_dega_qendra["view_branch_ids"].'))';

   IF ($user_dega_qendra["tipi_userit"] == "D")
      {
       //$tab_users_branch_or_reporting_entity   = "phi_branch_users";
       //$field_users_branch_or_reporting_entity = "id_branch";
      }
   ELSE
      {
       RETURN; //USER QENDER RAPORTIMI
      }
 //APLIKOJME LIDHJEN E USERIT ME DEGE/QENDER -----------------------------------------------------------------------------

  IF (ISSET($G_APP_VARS['s_id_branch']) AND ($G_APP_VARS['s_id_branch'] != ''))
     {
      $kushti_kerkim .= ' AND (
                               phi_event.id_branch        = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_id_branch']).'" OR 
                               phi_event_branch.id_branch = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_id_branch']).'"
                              )
                        ';
     }

  IF (ISSET($G_APP_VARS['s_event_status']) AND ($G_APP_VARS['s_event_status'] != ''))
     {
      $kushti_kerkim .= ' AND (
                               phi_event_branch.event_status = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_event_status']).'" AND
                               phi_event_branch.id_branch    IN ('.$user_dega_qendra["edit_branch_ids"].')
                              )
                            ';
     }

  IF (ISSET($G_APP_VARS['s_dt1']) AND ($G_APP_VARS['s_dt1'] != ''))
     {
      $kushti_kerkim .= ' AND phi_event.date >= "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_dt1']).'" ';
     }

  IF (ISSET($G_APP_VARS['s_dt2']) AND ($G_APP_VARS['s_dt2'] != ''))
     {
      $kushti_kerkim .= ' AND phi_event.date <= "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_dt2']).'" ';
     }

  IF (ISSET($G_APP_VARS['s_id_event_source']) AND ($G_APP_VARS['s_id_event_source'] != ''))
     {
      $kushti_kerkim .= ' AND phi_event.id_event_source = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_id_event_source']).'" ';
     }

  IF (ISSET($G_APP_VARS['s_channel_registration']) AND ($G_APP_VARS['s_channel_registration'] != ''))
     {
      $kushti_kerkim .= ' AND phi_reporting_entity.channel_registration = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_channel_registration']).'" ';
     }
//kushti i kerkimit ------------------------------------------------------------------------------------------------------

//numri i rekordeve total ------------------------------------------------------------------------------------------------
  //IF ($kushti_kerkim != '')
  //   {
  //    $kushti_kerkim = ' WHERE '.SUBSTR($kushti_kerkim, 5);
  //   }
     
  $sql = 'SELECT count(DISTINCT phi_event.id_event) as nr_rec_total
            FROM phi_event 

                     INNER JOIN phi_event_branch ON
                           phi_event.id_event = phi_event_branch.id_event 
                     
                     INNER JOIN phi_branch ON
                           phi_event.id_branch = phi_branch.id_branch 

           WHERE phi_event.record_status = 1 
                 '.$kushti_kerkim.'
         ';

  $rs = WebApp::execQuery($sql);
  IF (!$rs->EOF())
     {
      $nr_rec_total = $rs->Field('nr_rec_total');
     }
  ELSE
     {
      $nr_rec_total = 0;
     }
//------------------------------------------------------------------------------------------------------------------------

//LISTA DATA -------------------------------------------------------------------------------------------------------------
  IF ($nr_rec_total > 0)
     {
      $nr_rec_page_exp = 100000;

      IF ($exp_data == "Y")
         {
          $nr_rec_start = 0;
          $nr_rec_page  = $nr_rec_page_exp;

          unset($row_sel);
          unset($cel_sel);
          $cel_sel['exp_file_name']    = ''; //ne rast se kjo lihet bosh atehere tek exp.php kemi kapur titullin e CI qe inkudon nemin
          //$cel_sel['exp_sheet_name'] = "sheet_name";
          $cel_sel['exp_titull']       = ''; //ne rast se kjo lihet bosh atehere tek exp.php kemi kapur titullin e CI qe inkudon nemin

          $row_sel["properties"]     = $cel_sel;
          $data_arr[]                = $row_sel;
        }

      $kolonat_fusha[] = "phi_event.id_event";                  $kolonat_etiketa[] = WebApp::getVar("id_mesg");
      $kolonat_fusha[] = "phi_branch.name";                     $kolonat_etiketa[] = WebApp::getVar("dega_regjistruese_mesg");
      $kolonat_fusha[] = "phi_event.date";                      $kolonat_etiketa[] = WebApp::getVar("data_e_raportimit_mesg");
      $kolonat_fusha[] = "phi_event.subject";                   $kolonat_etiketa[] = WebApp::getVar("subject_mesg");
      $kolonat_fusha[] = "phi_event.id_event_source";           $kolonat_etiketa[] = WebApp::getVar("id_event_source_sh_mesg");
      $kolonat_fusha[] = "phi_event.channel_registration";      $kolonat_etiketa[] = WebApp::getVar("channel_registration_sh_mesg");
      $kolonat_fusha[] = "phi_event_branch.id_branch";          $kolonat_etiketa[] = WebApp::getVar("dega_ndjekese_mesg");
      $kolonat_fusha[] = "phi_event.event_status";              $kolonat_etiketa[] = WebApp::getVar("statusi_mesg");
      
      IF ($exp_data != "Y")
         {
          $kolonat_fusha[] = "";          $kolonat_etiketa[] = " "; //ikona preview
         }

      IF (($exp_data != "Y") AND ISSET($nem_rights[$NEM_ID_SEL]["102"]) AND ($nem_rights[$NEM_ID_SEL]["102"] != ""))
         {
          $kolonat_fusha[] = "";          $kolonat_etiketa[] = " ";  //ikona edit
         }
      
      unset($row_sel);
      FOR ($i=0; $i < count($kolonat_fusha); $i++)
          {
           unset($cel_sel);
           $cel_sel['tag']              = 'th';
           $cel_sel['tag_att']          = '';
           $cel_sel['link']             = 'N'; //Y/N  //e trajtoje me vone ne skriptin e centralizuar php_script\lista_info.php
           $cel_sel['link_att']         = '';
           $cel_sel['data_type']        = 'label'; //label/icon
           
           $cel_sel['vl']               = $kolonat_etiketa[$i];
           $cel_sel['vl_db']            = $kolonat_fusha[$i];
           $cel_sel['vl_db_indx']       = $i;
           $cel_sel['vlf']              = '';
           $cel_sel['bold']             = 'Y';
           $cel_sel['align']            = '';
           $cel_sel['style']            = '';
           $cel_sel['colspan']          = '';
           $cel_sel['format_number']    = ''; //per xlsx

           $row_sel[]                   = $cel_sel;
          }
      
      $data_arr[] = $row_sel;
     }
//------------------------------------------------------------------------------------------------------------------------

//LISTA DATA -------------------------------------------------------------------------------------------------------------
  IF ($nr_rec_total > 0)
     {
      //id_branch -----------------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]           = "id_branch";
        $lov["obj_or_label"]   = "label";
        $lov["all_data_array"] = "Y";
        $id_branch_arr         = f_app_lov_default_values($lov);
      //id_branch -----------------------------------------------------------------------------------------------

      //event_status --------------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]           = "event_status";
        $lov["obj_or_label"]   = "label";
        $lov["all_data_array"] = "Y";
        $event_status_arr      = f_app_lov_default_values($lov);
      //event_status --------------------------------------------------------------------------------------------
      
      //id_event_source -----------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]           = "id_event_source";
        $lov["obj_or_label"]   = "label";
        $lov["all_data_array"] = "Y";
        $id_event_source_arr   = f_app_lov_default_values($lov);
      //id_event_source -----------------------------------------------------------------------------------------

      //channel_registration ------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]              = "channel_registration";
        $lov["obj_or_label"]      = "label";
        $lov["all_data_array"]    = "Y";
        $channel_registration_arr = f_app_lov_default_values($lov);
      //channel_registration ------------------------------------------------------------------------------------

      //event_status --------------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]              = "event_status";
        $lov["obj_or_label"]      = "label";
        $lov["all_data_array"]    = "Y";
        $event_status_arr         = f_app_lov_default_values($lov);
      //event_status --------------------------------------------------------------------------------------------

      //exp_data ---------------------------------------------------------------------------------------------------------
        IF ($exp_data == "Y")
           {
            //
           }
      //exp_data ---------------------------------------------------------------------------------------------------------

      //order_by_name ----------------------------------------------------------------------------------------------------
        IF (!ISSET($G_APP_VARS["order_by"]) OR ($G_APP_VARS["order_by"] == ""))
           {
            $G_APP_VARS["order_by"] = '2';
           }
        
        $order_by      = 'ASC';
        $order_by_name = $kolonat_fusha[0];

        IF ($G_APP_VARS["order_by"] == 2)
           {
            $order_by = 'DESC';
           }
        
        IF (ISSET($kolonat_fusha[$G_APP_VARS["order_by_indx"]]) AND ($kolonat_fusha[$G_APP_VARS["order_by_indx"]] != ""))
           {
            $order_by_name = $kolonat_fusha[$G_APP_VARS["order_by_indx"]];
           }
      //------------------------------------------------------------------------------------------------------------------

      //futemi ne kursorin e te dhenave ----------------------------------------------------------------------------------
        $sql = 'SELECT DISTINCT
                       phi_event.id_event             as id_sel,
                       phi_event.id_branch            as id_branch_sel,
                       phi_branch.name                as dega_name,

                       IF (phi_event.date IS NULL, "", DATE_FORMAT(phi_event.date, "%d.%m.%Y")) as date_f,
                       
                       COALESCE (phi_event.subject,              "")  as subject,
                       COALESCE (phi_event.id_event_source,      "")  as id_event_source,
                       COALESCE (phi_event.channel_registration, "")  as channel_registration,
                       COALESCE (phi_event.event_status,         "")  as event_status

                  FROM phi_event 
                     
                              INNER JOIN phi_event_branch ON
                                         phi_event.id_event = phi_event_branch.id_event 

                              INNER JOIN phi_branch ON
                                         phi_event.id_branch = phi_branch.id_branch 

                 WHERE phi_event.record_status = 1 
                       '.$kushti_kerkim.'

              ORDER BY '.$order_by_name.' '.$order_by.'
                       Limit '.$nr_rec_start.','.$nr_rec_page.'
	            ';

        $rs_list = WebApp::execQuery($sql);
        $rs_list->MoveFirst();
        WHILE (!$rs_list->EOF())
	          {
               $id_sel               = $rs_list->Field('id_sel');
               $id_branch_sel        = $rs_list->Field('id_branch_sel');
               $dega_name            = $rs_list->Field('dega_name');
               $date_f               = $rs_list->Field('date_f');
               $subject              = $rs_list->Field('subject');
               $id_event_source      = $rs_list->Field('id_event_source');
               $channel_registration = $rs_list->Field('channel_registration');
               $event_status         = $rs_list->Field('event_status');

               //kapim deget qe po ndjekin ngjarjen ----------------------------------------------------------------------
                 $edit_deget_ndjekese = "N";
                 
                 $dega_names = "";
                 $sql1 = 'SELECT id_branch
                            FROM phi_event_branch 
                           WHERE id_event  = "'.$id_sel.'"
	                     ';

                 $rs1 = WebApp::execQuery($sql1);
                 $rs1->MoveFirst();
                 WHILE (!$rs1->EOF())
	                   {
                        $id_bra_sel = $rs1->Field('id_branch');
	                       
                        IF (ISSET($user_dega_qendra["edit_branch_id"][$id_bra_sel]) AND ($user_dega_qendra["edit_branch_id"][$id_bra_sel] == $id_bra_sel))
                           {
                            $edit_deget_ndjekese = "Y";
                           }
                           
                        $dega_names .= ' | '.$id_branch_arr[$id_bra_sel];
                        
                        $rs1->MoveNext();
                       }
                 
                 IF ($dega_names != "")
                    {
                     $dega_names = SUBSTR($dega_names, 3);
                    }
               //kapim deget qe po ndjekin ngjarjen ----------------------------------------------------------------------
               
               //exp_data ------------------------------------------------------------------------------------------------
                 IF ($exp_data == "Y")
                    {
                     //
                    }
                 ELSE
                    {
                     IF (STRLEN($subject) > 50)
                        {
                         $subject = mb_substr($subject, 0, 47).'...';
                        }

                     IF (STRLEN($dega_names) > 50)
                        {
                         $dega_names = mb_substr($dega_names, 0, 47).'...';
                        }
                    }
               //exp_data ------------------------------------------------------------------------------------------------
               
               //array me datat ------------------------------------------------------------------------------------------
                 unset($row_sel);

                 unset($cel_sel);
                 $cel_sel['tag']              = 'td';
                 $cel_sel['tag_att']          = '';
                 $cel_sel['link']             = 'N'; //Y/N
                 $cel_sel['link_att']         = '';
                 $cel_sel['data_type']        = 'label'; //label/icon
                 $cel_sel['vl']               = $id_sel;
                 $cel_sel['vl_db']            = '';
                 $cel_sel['vlf']              = '';
                 $cel_sel['bold']             = '';
                 $cel_sel['align']            = '';
                 $cel_sel['style']            = '';
                 $cel_sel['colspan']          = '';
                 $cel_sel['format_number']    = ''; //per xlsx
                 $row_sel[]                   = $cel_sel;

                 unset($cel_sel);
                 $cel_sel['tag']              = 'td';
                 $cel_sel['tag_att']          = '';
                 $cel_sel['link']             = 'N'; //Y/N
                 $cel_sel['link_att']         = '';
                 $cel_sel['data_type']        = 'label'; //label/icon
                 $cel_sel['vl']               = $dega_name;
                 $cel_sel['vl_db']            = '';
                 $cel_sel['vlf']              = '';
                 $cel_sel['bold']             = '';
                 $cel_sel['align']            = '';
                 $cel_sel['style']            = '';
                 $cel_sel['colspan']          = '';
                 $cel_sel['format_number']    = ''; //per xlsx
                 $row_sel[]                   = $cel_sel;

                 unset($cel_sel);
                 $cel_sel['tag']              = 'td';
                 $cel_sel['tag_att']          = '';
                 $cel_sel['link']             = 'N'; //Y/N
                 $cel_sel['link_att']         = '';
                 $cel_sel['data_type']        = 'label'; //label/icon
                 $cel_sel['vl']               = $date_f;
                 $cel_sel['vl_db']            = '';
                 $cel_sel['vlf']              = '';
                 $cel_sel['bold']             = '';
                 $cel_sel['align']            = '';
                 $cel_sel['style']            = '';
                 $cel_sel['colspan']          = '';
                 $cel_sel['format_number']    = ''; //per xlsx
                 $row_sel[]                   = $cel_sel;

                 unset($cel_sel);
                 $cel_sel['tag']              = 'td';
                 $cel_sel['tag_att']          = '';
                 $cel_sel['link']             = 'N'; //Y/N
                 $cel_sel['link_att']         = '';
                 $cel_sel['data_type']        = 'label'; //label/icon
                 $cel_sel['vl']               = $subject;
                 $cel_sel['vl_db']            = '';
                 $cel_sel['vlf']              = '';
                 $cel_sel['bold']             = '';
                 $cel_sel['align']            = '';
                 $cel_sel['style']            = '';
                 $cel_sel['colspan']          = '';
                 $cel_sel['format_number']    = ''; //per xlsx
                 $row_sel[]                   = $cel_sel;

                 unset($cel_sel);
                 $cel_sel['tag']              = 'td';
                 $cel_sel['tag_att']          = '';
                 $cel_sel['link']             = 'N'; //Y/N
                 $cel_sel['link_att']         = '';
                 $cel_sel['data_type']        = 'label'; //label/icon
                 $cel_sel['vl']               = $id_event_source_arr[$id_event_source].'';
                 $cel_sel['vl_db']            = '';
                 $cel_sel['vlf']              = '';
                 $cel_sel['bold']             = '';
                 $cel_sel['align']            = '';
                 $cel_sel['style']            = '';
                 $cel_sel['colspan']          = '';
                 $cel_sel['format_number']    = ''; //per xlsx
                 $row_sel[]                   = $cel_sel;

                 unset($cel_sel);
                 $cel_sel['tag']              = 'td';
                 $cel_sel['tag_att']          = '';
                 $cel_sel['link']             = 'N'; //Y/N
                 $cel_sel['link_att']         = '';
                 $cel_sel['data_type']        = 'label'; //label/icon
                 $cel_sel['vl']               = $channel_registration_arr[$channel_registration].'';
                 $cel_sel['vl_db']            = '';
                 $cel_sel['vlf']              = '';
                 $cel_sel['bold']             = '';
                 $cel_sel['align']            = '';
                 $cel_sel['style']            = '';
                 $cel_sel['colspan']          = '';
                 $cel_sel['format_number']    = ''; //per xlsx
                 $row_sel[]                   = $cel_sel;
                 
                 unset($cel_sel);
                 $cel_sel['tag']              = 'td';
                 $cel_sel['tag_att']          = '';
                 $cel_sel['link']             = 'N'; //Y/N
                 $cel_sel['link_att']         = '';
                 $cel_sel['data_type']        = 'label'; //label/icon
                 $cel_sel['vl']               = $dega_names.'';
                 $cel_sel['vl_db']            = '';
                 $cel_sel['vlf']              = '';
                 $cel_sel['bold']             = '';
                 $cel_sel['align']            = '';
                 $cel_sel['style']            = '';
                 $cel_sel['colspan']          = '';
                 $cel_sel['format_number']    = ''; //per xlsx
                 $row_sel[]                   = $cel_sel;

                 unset($cel_sel);
                 $cel_sel['tag']              = 'td';
                 $cel_sel['tag_att']          = '';
                 $cel_sel['link']             = 'N'; //Y/N
                 $cel_sel['link_att']         = '';
                 $cel_sel['data_type']        = 'label'; //label/icon
                 $cel_sel['vl']               = $event_status_arr[$event_status].'';
                 $cel_sel['vl_db']            = '';
                 $cel_sel['vlf']              = '';
                 $cel_sel['bold']             = '';
                 $cel_sel['align']            = '';
                 $cel_sel['style']            = '';
                 $cel_sel['colspan']          = '';
                 $cel_sel['format_number']    = ''; //per xlsx
                 $row_sel[]                   = $cel_sel;

                 IF ($exp_data != "Y")
                    {
                     $post_id_sel = f_app_encrypt($id_sel.'|'.$NEM_ID_SEL, DESK_KEY, DESK_IV);
                    }

                 IF ($exp_data != "Y")
                    {
                     //variabli per preview ---------------------------------------------------------
                       $vars_post_kol    = null;
                       $vars_post_val    = null;
                     
                       $vars_post_kol[]  = 'gjendje';
                       $vars_post_val[]  = 'record_detaje';

                       $vars_post_kol[]  = 'editim_konsultim';
                       $vars_post_val[]  = 'konsultim';

                       $vars_post_kol[]  = 'post_id';
                       $vars_post_val[]  = $post_id_sel;

                       $vars_post        = f_app_vars_page_encrypt($vars_post_kol, $vars_post_val);
                       //$data_url_sel   = $data_url_preview.'&vars_post='.$vars_post;  //preview e shpejte kur nuk ka shume info
                       $data_url_sel     = $data_url_this_nem.'&vars_post='.$vars_post; //previev me iframe kur kemi scroll
                     //variabli per preview ---------------------------------------------------------
                     
                     unset($cel_sel);
                     $cel_sel['tag']              = 'td';
                     $cel_sel['tag_att']          = '';

                     $cel_sel['link']              = 'Y'; //Y/N
                     $cel_sel['link_att']          = 'href="javascript:void(0);"';
                     $cel_sel['link_data_modal']   = 'Y';
                     $cel_sel['link_data_title']   = $content_title." - ".WebApp::getVar("preview_mesg");
                     $cel_sel['link_data_url']     = $data_url_sel;
                     $cel_sel['link_modal_iframe'] = 'true'; //previev me iframe kur kemi scroll            
                     $cel_sel['link_modal_width']  = '1200'; //$link_modal_width_default e dua cike me te gjere modalin tek eventi
                     $cel_sel['link_modal_height'] = $link_modal_height_default;
                     $cel_sel['data_type']        = 'icon'; //label/icon
                     $cel_sel['vl']               = 'icon_preview';
                     $cel_sel['vl_db']            = '';
                     $cel_sel['vlf']              = '';
                     $cel_sel['bold']             = '';
                     $cel_sel['align']            = '';
                     $cel_sel['style']            = '';
                     $cel_sel['colspan']          = '';
                     $cel_sel['format_number']    = ''; //per xlsx
                     $row_sel[]                   = $cel_sel;
                    }

                 IF (($exp_data != "Y") AND ISSET($nem_rights[$NEM_ID_SEL]["102"]) AND ($nem_rights[$NEM_ID_SEL]["102"] != ""))
                    {
                     unset($cel_sel);
                     $cel_sel['tag']              = 'td';
                     $cel_sel['tag_att']          = '';
                     $cel_sel['link']             = 'Y'; //Y/N
                     
                     IF (
                         (ISSET($user_dega_qendra["edit_branch_id"][$id_branch_sel]) AND ($user_dega_qendra["edit_branch_id"][$id_branch_sel] == $id_branch_sel))
                         OR
                         ($edit_deget_ndjekese == "Y")
                        )
                        {
                         //USERI KA TE DREJTE TE EDITOJE NE KETE DEGE 
                         $cel_sel['link_att']     = 'href="javascript:f_app_add_edit(\''.$arg_webbox.'\', \'add_edit\', \''.$arg_id_form.'\', \''.$post_id_sel.'\')"';
                         $cel_sel['data_type']    = 'icon'; //label/icon
                         $cel_sel['vl']           = 'icon_edit';
                        }
                     ELSE
                        {
                         //USERI NUK KA TE DREJTE TE EDITOJE NE KETE DEGE 
                         $cel_sel['link_att']     = '';
                         $cel_sel['data_type']    = 'label'; //label/icon
                         $cel_sel['vl']           = '';
                        }

                     $cel_sel['vl_db']            = '';
                     $cel_sel['vlf']              = '';
                     $cel_sel['bold']             = '';
                     $cel_sel['align']            = '';
                     $cel_sel['style']            = '';
                     $cel_sel['colspan']          = '';
                     $cel_sel['format_number']    = ''; //per xlsx
                     $row_sel[]                   = $cel_sel;
                    }

                 $data_arr[] = $row_sel;
               //array me datat ------------------------------------------------------------------------------------------

	          $rs_list->MoveNext();
	         }
      //futemi ne kursorin e akteve --------------------------------------------------------------------------------------
     
      //per exportin -----------------------------------------------------------------------------------------------------
        IF (ISSET($nem_rights[$NEM_ID_SEL]["104"]) AND ($nem_rights[$NEM_ID_SEL]["104"] != ""))
           {
            //shtojme ne vars_page variabla shtese ---------------------------------------------------------------------------
              $webbox_list_data = STR_REPLACE(APP_PATH, "", __FILE__);

              $vars_page_kol[]  = "webbox";
              $vars_page_val[]  = $webbox_list_data;

              $vars_page_kol[]  = "idstemp";
              $vars_page_val[]  = $session->Vars["idstemp"];

              $vars_page_exp    = f_app_vars_page_encrypt($vars_page_kol, $vars_page_val);
            //shtojme ne vars_page variabla shtese ---------------------------------------------------------------------------

            $exp_params["vars_page"]  = $vars_page_exp;
            $exp_params["nr_rec_exp"] = $nr_rec_page_exp;
            $exp_params["xls"]        = "Y";
            $exp_params["cvs"]        = "Y";
            $exp_params["html"]       = "Y";
            $exp_params["pdf"]        = "Y";
            $exp_params["doc"]        = "N";
           }
      //per exportin -----------------------------------------------------------------------------------------------------
     }
//LISTA DATA -------------------------------------------------------------------------------------------------------------
?>