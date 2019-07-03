<?
//kushti i kerkimit ------------------------------------------------------------------------------------------------------
  $kushti_kerkim  = '';

  IF (ISSET($G_APP_VARS['s_name']) AND ($G_APP_VARS['s_name'] != ''))
     {
      $kushti_kerkim .= ' AND name LIKE "%'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_name']).'%" ';
     }
//kushti i kerkimit ------------------------------------------------------------------------------------------------------
    
//numri i rekordeve total ------------------------------------------------------------------------------------------------
  IF ($kushti_kerkim != '')
     {
      $kushti_kerkim = ' WHERE '.SUBSTR($kushti_kerkim, 5);
     }
     
  $sql = 'SELECT count(1) as nr_rec_total
            FROM phi_type_of_sample
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

      $kolonat_fusha[] = "id_type_of_sample";     $kolonat_etiketa[] = WebApp::getVar("id_mesg");
      $kolonat_fusha[] = "name";          $kolonat_etiketa[] = WebApp::getVar("emertimi_mesg");
      $kolonat_fusha[] = "record_status"; $kolonat_etiketa[] = WebApp::getVar("statusi_mesg");

      IF ($exp_data != "Y")
         {
          $kolonat_fusha[] = "";            $kolonat_etiketa[] = " "; //ikona preview
         }

      IF (($exp_data != "Y") AND ISSET($nem_rights[$NEM_ID_SEL]["102"]) AND ($nem_rights[$NEM_ID_SEL]["102"] != ""))
         {
          $kolonat_fusha[] = "";          $kolonat_etiketa[] = " ";
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
      //record_status_data -----------------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]           = "record_status";
        $lov["obj_or_label"]   = "label";
        $lov["all_data_array"] = "Y";
        $record_status_arr     = f_app_lov_default_values($lov);
      //status -----------------------------------------------------------------------------------------------------------

      //order_by_name ----------------------------------------------------------------------------------------------------
        IF (!ISSET($G_APP_VARS["order_by"]) OR ($G_APP_VARS["order_by"] == ""))
           {
            $G_APP_VARS["order_by"] = '1';
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
        $sql = 'SELECT id_type_of_sample                      as id_sel,
                       IF (name          IS NULL, "", name)          as name,
                       IF (record_status IS NULL, "", record_status) as record_status
                  FROM phi_type_of_sample
                       '.$kushti_kerkim.'
              ORDER BY '.$order_by_name.' '.$order_by.'
                       Limit '.$nr_rec_start.','.$nr_rec_page.'
	            ';
 
        $rs_list = WebApp::execQuery($sql);
        $rs_list->MoveFirst();
        WHILE (!$rs_list->EOF())
	          {
               $id_sel        = $rs_list->Field('id_sel');
               $name          = $rs_list->Field('name');
               $record_status = $rs_list->Field('record_status');
               
               //record_status -------------------------------------------------------------------------------------------
                 $lov_record_status = $record_status_arr[$record_status];
               //status --------------------------------------------------------------------------------------------------

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
                 $cel_sel['vl']               = $name;
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
                 $cel_sel['vl']               = $lov_record_status;
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
                       $data_url_sel     = $data_url_preview.'&vars_post='.$vars_post;
                     //variabli per preview ---------------------------------------------------------
                     
                     unset($cel_sel);
                     $cel_sel['tag']              = 'td';
                     $cel_sel['tag_att']          = '';

                     $cel_sel['link']             = 'Y'; //Y/N
                     $cel_sel['link_att']         = 'href="javascript:void(0);"';
                     $cel_sel['link_data_modal']  = 'Y';
                     $cel_sel['link_data_title']  = $content_title." - ".WebApp::getVar("preview_mesg");
                     $cel_sel['link_data_url']    = $data_url_sel;
                     
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
                     $cel_sel['link_att']         = 'href="javascript:f_app_add_edit(\''.$arg_webbox.'\', \'add_edit\', \''.$arg_id_form.'\', \''.$post_id_sel.'\')"';
                     $cel_sel['data_type']        = 'icon'; //label/icon
                     $cel_sel['vl']               = 'icon_edit';
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