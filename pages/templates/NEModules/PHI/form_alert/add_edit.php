<?
WebApp::addVar("max_width", "1000");

//start ------------------------------------------------------------------------------------------------------------------
  INCLUDE(dirname(__FILE__)."/../php_script/add_edit_01_start.php");
//start ------------------------------------------------------------------------------------------------------------------

//overright size per etiketat dhe objektet e formes ----------------------------------------------------------------------
  $nr_cols  = 3;
  $col_size = 12/$nr_cols;

  $col_1_size = 4; 
  $col_2_size = 4; 
  $col_3_size = 4; 
  
  $width_form = 12; //brenda kolones sa madhesi do zeme
  $width_lab  = 6;
  IF ($width_lab == 12)
     {
      $width_obj = 12; 
     }
  ELSE
     {
      $width_obj = 12 - $width_lab; 
     }

  //per kolonen e dyte 
  $width_lab2  = 8;
  $width_obj2  = 4;
//overright size per etiketat dhe objektet e formes ----------------------------------------------------------------------

//syndrome_preview_arr kapim gjithe etiketat -----------------------------------------------------------------------------
  unset($lov);
  $lov["name"]           = "id_syndrome";
  $lov["obj_or_label"]   = "label";
  $lov["all_data_array"] = "Y";
  $syndrome_preview_arr  = f_app_lov_default_values($lov);
//syndrome_preview_arr kapim gjithe etiketat -----------------------------------------------------------------------------

//agegroup_preview_arr kapim gjithe etiketat -----------------------------------------------------------------------------
  unset($lov);
  $lov["name"]           = "id_agegroup";
  $lov["obj_or_label"]   = "label";
  $lov["all_data_array"] = "Y";
  $agegroup_preview_arr  = f_app_lov_default_values($lov);
//agegroup_preview_arr ---------------------------------------------------------------------------------------------------

IF (ISSET($post_id) AND ($post_id != ""))
   {
    unset($tab);
    $kushti_where        = 'WHERE id_form_alert = "'.ValidateVarFun::f_real_escape_string($post_id).'"';
    $tab['tab_name']     = 'phi_form_alert';            //*emri i tabeles ku do behet select
    $tab['sql_where']    = $kushti_where;           //default = "", kthen gjithe rekordet pa filtrim; perndryshe shkruani kushtin e filtrimit, mos haroni fjalen WHERE;
    $tab['nr_rec_tot']   = 'F';                     //default = "F", (FALSE); pranon vlerat T,F; kur eshte True kthen dhe numrin total te rekordeve qe kthen selekti;
    $tab['kol_filter']   = '';                      //default = ""(bosh); kur i do gjithe kolonat e tabeles lihet bosh; kur nuk te interesojne disa kolona i vendos emrat e kolonave te ndara me presjeve; zakonisht perdoret per te filtruar fushat e tipit blob;
    $tab['kol_select']   = '';                      //default = ""(bosh); kur i do gjithe kolonat e tabeles lihet bosh; kur te interesojne vetem disa kolona i vendos emrat e kolonave te ndara me presjeve;
    $tab['kol_order']    = '';                      //default = "", pra rekordet nuk renditen sipas ndonje kolone; emri i kolones sipas se ciles do renditen rekordet;
    $tab['kol_asc_desc'] = '';                      //default = "ASC"; pranon vlerat ASC, DESC; meret parasysh kur $tab['kol_order'] != "";
    $tab['rec_limit']    = '';                      //default = "", pra pa limit; perndryshe kthen ato rekorde qe jane percaktuar ne limit, formati = 0,10;
    $tab['obj_class']    = '';            		  //default = "txtbox"; emri i klases ne style, vlen kur $tab['is_form'] = "T";
    $tab['distinct']     = 'F';      				  //default = "F" -> pranon vlerat T,F (pra true ose false);
    $tab['is_form']      = 'F';                     //default = "T"; pranon vlerat T,F;
    $val_rec             = f_app_select_form_table($tab);
    
    $ins_record_user      = f_app_record_user ($val_rec['record_user_ins'][0]['vl']);
    $ins_record_timestamp = $val_rec['record_timestamp_ins'][0]['vlf_dt'];

    $upd_record_user      = f_app_record_user ($val_rec['record_user_upd'][0]['vl']);
    $upd_record_timestamp = $val_rec['record_timestamp_upd'][0]['vlf_dt'];
    
    //kapim tabelen phi_form_alert_sindroma ------------------------------------------------------------------------------
      unset($tab);
      $kushti_where        = 'WHERE id_form_alert = "'.ValidateVarFun::f_real_escape_string($post_id).'"';
      $tab['tab_name']     = 'phi_form_alert_sindroma';            //*emri i tabeles ku do behet select
      $tab['sql_where']    = $kushti_where;           //default = "", kthen gjithe rekordet pa filtrim; perndryshe shkruani kushtin e filtrimit, mos haroni fjalen WHERE;
      $tab['nr_rec_tot']   = 'F';                     //default = "F", (FALSE); pranon vlerat T,F; kur eshte True kthen dhe numrin total te rekordeve qe kthen selekti;
      $tab['kol_filter']   = '';                      //default = ""(bosh); kur i do gjithe kolonat e tabeles lihet bosh; kur nuk te interesojne disa kolona i vendos emrat e kolonave te ndara me presjeve; zakonisht perdoret per te filtruar fushat e tipit blob;
      $tab['kol_select']   = '';                      //default = ""(bosh); kur i do gjithe kolonat e tabeles lihet bosh; kur te interesojne vetem disa kolona i vendos emrat e kolonave te ndara me presjeve;
      $tab['kol_order']    = 'order_form_alert';                      //default = "", pra rekordet nuk renditen sipas ndonje kolone; emri i kolones sipas se ciles do renditen rekordet;
      $tab['kol_asc_desc'] = '';                      //default = "ASC"; pranon vlerat ASC, DESC; meret parasysh kur $tab['kol_order'] != "";
      $tab['rec_limit']    = '';                      //default = "", pra pa limit; perndryshe kthen ato rekorde qe jane percaktuar ne limit, formati = 0,10;
      $tab['obj_class']    = '';            		  //default = "txtbox"; emri i klases ne style, vlen kur $tab['is_form'] = "T";
      $tab['distinct']     = 'F';      				  //default = "F" -> pranon vlerat T,F (pra true ose false);
      $tab['is_form']      = 'F';                     //default = "T"; pranon vlerat T,F;
      $val_rec_sindroma    = f_app_select_form_table($tab);
      
      //formojme arrayn e sindromave qe do bredhim -----------------------------------------------------------------------
        FOR ($i=0; $i < $val_rec_sindroma['nr_rec']; $i++)
            {
             $id_syndrome_vl                     = $val_rec_sindroma['id_syndrome'][$i]['vl'];
             $syndrome_data_arr[$id_syndrome_vl] = $syndrome_preview_arr[$id_syndrome_vl];
             
             IF ($val_rec_sindroma['total_cases'][$i]['vl'] == 0)
                {
                 $phi_form_alert_sindroma_arr[$id_syndrome_vl] = '';
                }
             ELSE
                {
                 $phi_form_alert_sindroma_arr[$id_syndrome_vl] = $val_rec_sindroma['total_cases'][$i]['vl'];
                }
            }
      //formojme arrayn e sindromave qe do bredhim -----------------------------------------------------------------------
    //kapim tabelen phi_form_alert_sindroma ------------------------------------------------------------------------------

    //kapim tabelen phi_form_alert_sindroma_agegroup ---------------------------------------------------------------------
      unset($tab);
      $kushti_where        = 'WHERE id_form_alert = "'.ValidateVarFun::f_real_escape_string($post_id).'"';
      $tab['tab_name']     = 'phi_form_alert_sindroma_agegroup';            //*emri i tabeles ku do behet select
      $tab['sql_where']    = $kushti_where;           //default = "", kthen gjithe rekordet pa filtrim; perndryshe shkruani kushtin e filtrimit, mos haroni fjalen WHERE;
      $tab['nr_rec_tot']   = 'F';                     //default = "F", (FALSE); pranon vlerat T,F; kur eshte True kthen dhe numrin total te rekordeve qe kthen selekti;
      $tab['kol_filter']   = '';                      //default = ""(bosh); kur i do gjithe kolonat e tabeles lihet bosh; kur nuk te interesojne disa kolona i vendos emrat e kolonave te ndara me presjeve; zakonisht perdoret per te filtruar fushat e tipit blob;
      $tab['kol_select']   = '';                      //default = ""(bosh); kur i do gjithe kolonat e tabeles lihet bosh; kur te interesojne vetem disa kolona i vendos emrat e kolonave te ndara me presjeve;
      $tab['kol_order']    = 'order_form_alert';                      //default = "", pra rekordet nuk renditen sipas ndonje kolone; emri i kolones sipas se ciles do renditen rekordet;
      $tab['kol_asc_desc'] = '';                      //default = "ASC"; pranon vlerat ASC, DESC; meret parasysh kur $tab['kol_order'] != "";
      $tab['rec_limit']    = '';                      //default = "", pra pa limit; perndryshe kthen ato rekorde qe jane percaktuar ne limit, formati = 0,10;
      $tab['obj_class']    = '';            		  //default = "txtbox"; emri i klases ne style, vlen kur $tab['is_form'] = "T";
      $tab['distinct']     = 'F';      				  //default = "F" -> pranon vlerat T,F (pra true ose false);
      $tab['is_form']      = 'F';                     //default = "T"; pranon vlerat T,F;
      $val_rec_agegroup    = f_app_select_form_table($tab);

      //formojme arrayn e grupmoshave qe do bredhim -----------------------------------------------------------------------
        FOR ($i=0; $i < $val_rec_agegroup['nr_rec']; $i++)
            {
             $id_agegroup_vl                     = $val_rec_agegroup['id_agegroup'][$i]['vl'];
             $agegroup_data_arr[$id_agegroup_vl] = $agegroup_preview_arr[$id_agegroup_vl];
             
             $id_syndrome_vl                     = $val_rec_agegroup['id_syndrome'][$i]['vl'];

             IF ($val_rec_agegroup['cases'][$i]['vl'] == 0)
                {
                 $phi_form_alert_sindroma_agegroup[$id_syndrome_vl][$id_agegroup_vl] = '';
                }
             ELSE
                {
                 $phi_form_alert_sindroma_agegroup[$id_syndrome_vl][$id_agegroup_vl] = $val_rec_agegroup['cases'][$i]['vl'];
                }
            }
      //formojme arrayn e sindromave qe do bredhim -----------------------------------------------------------------------
    //kapim tabelen phi_form_alert_sindroma_agegroup ---------------------------------------------------------------------
   }
ELSE
   {
    //PO SHTOHET REKORD I RI ---------------------------------------------------------------------------------------------

    //kapim sindromat aktive ---------------------------------------------------------------------------------------------
      unset($lov);
      $lov["name"]           = "id_syndrome";
      $lov["obj_or_label"]   = "label";
      $lov["all_data_array"] = "Y";
      $lov["filter"]         = "WHERE record_status = '1'";
      $lov["order_by"]       = 'order_form_alert';
      $syndrome_data_arr     = f_app_lov_default_values($lov);
    //syndrome_data kapim gjithe etiketat --------------------------------------------------------------------------------

    //kapim agegroup aktive ----------------------------------------------------------------------------------------------
      unset($lov);
      $lov["name"]           = "id_agegroup";
      $lov["obj_or_label"]   = "label";
      $lov["all_data_array"] = "Y";
      $lov["filter"]         = "WHERE record_status = '1'";
      $lov["order_by"]       = 'order_form_alert';
      $agegroup_data_arr     = f_app_lov_default_values($lov);
    //kapim agegroup aktive ----------------------------------------------------------------------------------------------

    $val_rec['date_receipt_in_center'][0]['vlf'] = DATE('d.m.Y');
   }
   
//NESE USERI ESHTE I LIDHUR ME NJE QENDER RAPORTIMI ----------------------------------------------------------------------
  IF ($editim_konsultim == 'editim')
     {
      IF (($user_dega_qendra["edit_branch_nr"] == "1") AND !ISSET($val_rec['id_branch'][0]['vl']))
         {
          $val_rec['id_branch'][0]['vl'] = $user_dega_qendra["edit_branch_ids"];
         }

      IF (($user_dega_qendra["reporting_entity_nr"] == "1") AND !ISSET($val_rec['id_reporting_entity'][0]['vl']))
         {
          $val_rec['id_reporting_entity'][0]['vl'] = $user_dega_qendra["reporting_entity_ids"];
         }
     
      IF ($user_dega_qendra["tipi_userit"] == "Q")
         {
          $date_receipt_in_center_disabled = ' disabled';
         }
     }
   
//NESE USERI ESHTE I LIDHUR ME NJE QENDER RAPORTIMI ----------------------------------------------------------------------

//rights -----------------------------------------------------------------------------------------------------------------
  INCLUDE(dirname(__FILE__)."/../php_script/add_edit_02_rights.php");
//rights -----------------------------------------------------------------------------------------------------------------

//forma kete ka specifikat e saje prandaj e trajtojme wecmas -------------------------------------------------------------
//AKSIONI INS/UPD NE DB KA DESHTUAR KESHTU QE MUNDOHEMI TE NXJERIM EDHE NJEHERE VLERAT QE KA MBUSHUR USERI ---------------
  IF (ISSET($G_APP_VARS["kol_val_post"]) AND IS_ARRAY($G_APP_VARS["kol_val_post"]))
     {
      RESET($G_APP_VARS["kol_val_post"]);
      WHILE (LIST($key, $val) = EACH($G_APP_VARS["kol_val_post"])) 
            {
             //total_cases -----------------------------------------------------------------------------------------------
               IF (SUBSTR($key, 0, 12) == "total_cases_")
                  {
                   $id_syndrome_sel = SUBSTR($key, 12);
                             
                   IF ($val == 0)
                      {
                       $phi_form_alert_sindroma_arr[$id_syndrome_sel] = '';
                      }
                   ELSE
                      {
                       $phi_form_alert_sindroma_arr[$id_syndrome_sel] = $val;
                      }
                  }
             //total_cases -----------------------------------------------------------------------------------------------

             //cases -----------------------------------------------------------------------------------------------------
               IF (SUBSTR($key, 0, 6) == "cases_")
                  {
                   $id_syndrome_id_agegroup_sel = SUBSTR($key, 6);
                   $id_syndrome_id_agegroup_arr = EXPLODE("_", $id_syndrome_id_agegroup_sel);
                   
                   $id_syndrome_sel             = $id_syndrome_id_agegroup_arr[0];
                   $id_agegroup_sel             = $id_syndrome_id_agegroup_arr[1];
                   
                   IF ($val == 0)
                      {
                       $phi_form_alert_sindroma_agegroup[$id_syndrome_sel][$id_agegroup_sel] = '';
                      }
                   ELSE
                      {
                       $phi_form_alert_sindroma_agegroup[$id_syndrome_sel][$id_agegroup_sel] = $val;
                      }
                  }
             //cases -----------------------------------------------------------------------------------------------------
            }
     }
//------------------------------------------------------------------------------------------------------------------------

//ndryshojme funksionin e savit meqe kemi validime ekstra per kete forme -------------------------------------------------
  $but_regj_action = STR_REPLACE("f_app_save", "f_app_save_local", $but_regj_action); 
//------------------------------------------------------------------------------------------------------------------------

//LOV --------------------------------------------------------------------------------------------------------------------
  IF ($editim_konsultim == 'editim')
     {
      $branch_ids_sel = $user_dega_qendra["edit_branch_ids"];
     }
  ELSE
     {
      $branch_ids_sel = $user_dega_qendra["view_branch_ids"];
     }

  //id_branch ------------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"] = "id_branch";
    IF ($val_rec['id_branch'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['id_branch'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "id_branch";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
    $lov["filter"]        = "WHERE id_branch IN (".$branch_ids_sel.") ";
    $lov_id_branch        = f_app_lov_default_values($lov);
  //id_branch ------------------------------------------------------------------------------------------------------------

  //id_reporting_entity --------------------------------------------------------------------------------------------------
    unset($lov);
    //$lov["name"] = "id_reporting_entity";
    $lov["name"] = "id_reporting_entity_dhe_tipi";
    IF ($val_rec['id_reporting_entity'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['id_reporting_entity'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }

    $lov["filter"]  = "WHERE id_branch IN (".$branch_ids_sel.") ";
    $lov["filter"] .= " AND fills_alert_form = 'Y'";

    IF ($val_rec['id_branch'][0]['vl'] != "")
       {
        $lov["filter"] .= " AND id_branch = '".ValidateVarFun::f_real_escape_string($val_rec['id_branch'][0]['vl'])."'";

        IF ($user_dega_qendra["reporting_entity_ids"] != "")
           {
            $lov["filter"] .= " AND id_reporting_entity IN (".$user_dega_qendra["reporting_entity_ids"].")";
           }
       }
    ELSE
       {
        $lov["filter"] .= " AND id_branch = '-1'";
       }

    $lov["object_name"]      = "id_reporting_entity";
    $lov["valid"]            = "1,0,0,0,0,0";
    $lov["only_options"]     = "Y";
    $lov["obj_or_label"]     = $obj_or_label;
    $lov_id_reporting_entity = f_app_lov_default_values($lov);
  //id_reporting_entity --------------------------------------------------------------------------------------------------

  //PER FILTRIMIN E QENDRAVE TE RAPORTIMIT -------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]           = "id_branch_id_reporting_entity_dhe_tipi";
    $lov["obj_or_label"]   = "label";
    $lov["all_data_array"] = "Y";

    $lov["filter"]  = "WHERE id_branch IN (".$branch_ids_sel.") ";
    
    //formen alert nuk e plotesojne te gjitha qendrat keshtu qe i filtrojme ----------------------------------------------
    $lov["filter"] .= " AND fills_alert_form = 'Y'";
    //formen alert nuk e plotesojne te gjitha qendrat keshtu qe i filtrojme ----------------------------------------------

    IF ($user_dega_qendra["reporting_entity_ids"] != "")
       {
        $lov["filter"] .= " AND id_reporting_entity IN (".$user_dega_qendra["reporting_entity_ids"].")";
       }

    $lov_app_js            = f_app_lov_default_values($lov);
    $arr_js                = f_app_js_arr($lov_app_js);
    $script_js_in_page .= '
                           f_app_array_js["id_branch"] = "'.$arr_js.'";
                          ';
  //PER FILTRIMIN E QENDRAVE TE RAPORTIMIT -------------------------------------------------------------------------------
//LOV --------------------------------------------------------------------------------------------------------------------

//TITULLI E MBIVENDOSIM  -------------------------------------------------------------------------------------------------
  $titull_print  = WebApp::getVar("form_alert_titull_mesg");
  $titull_print .= " - ".WebApp::getVar("form_alert_nentitull_mesg");
   
//Grid_form -----------------------------------------------------------------------------------------------------------
  $nr  = -1;
  $Grid_form = array('data' => array(), 'AllRecs' => '0');	

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_header';
  $Grid_form['data'][$nr]['label']            = $titull_print;
  $Grid_form['data'][$nr]['data-action']      = '';//collapse

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_open';
  $Grid_form['data'][$nr]['name']             = 'skeda';
  $Grid_form['data'][$nr]['id']               = 'id_skeda';
  $Grid_form['data'][$nr]['method']           = 'post';
  $Grid_form['data'][$nr]['onSubmit']         = 'return false;';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'hidden';
  $Grid_form['data'][$nr]['name']             = 'vars_page';
  $Grid_form['data'][$nr]['value']            = $vars_page;
  $Grid_form['data'][$nr]['id']               = '';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_body_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $col_1_size;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //id_branch ------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{id_branch_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_branch';
  $Grid_form['data'][$nr]['id']               = 'id_branch_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'id_branch';
  $Grid_form['data'][$nr]['value']            = $lov_id_branch;
  $Grid_form['data'][$nr]['id']               = 'id_id_branch';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{id_branch_mesg}}" onchange="f_app_filter_listbox(this.id)" js_data_array="id_branch" id_obj_child="id_id_reporting_entity"';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //id_branch ------------------------------------------------------------------------------------------

  //id_reporting_entity --------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']              = 'label';
  $Grid_form['data'][$nr]['value']             = $yll.'{{id_reporting_entity_mesg}}:';
  $Grid_form['data'][$nr]['for']               = 'id_reporting_entity';
  $Grid_form['data'][$nr]['id']                = 'id_reporting_entity_label';
  $Grid_form['data'][$nr]['other_attributes']  = '';
  $Grid_form['data'][$nr]['width']             = 12;

  IF ($editim_konsultim == 'editim')
     {
      IF (!ISSET($post_id) OR ($post_id == ""))
         {
          //SHFAQIM IKONEN E HAPJESE SE NEMIT PER TE SHTUAR phi_reporting_entity NE RAST SE KA TE DREJTA 
          //107 = Reporting entity
          $url_shto_reporting_entity = f_app_url_nem (107, 101);

          $vars_post_kol    = null;
          $vars_post_val    = null;
          
          $vars_post_kol[]  = 'editim_konsultim';
          $vars_post_val[]  = 'editim';

          $vars_post_kol[]  = 'nem_mode';
          $vars_post_val[]  = 'select_record';

          $vars_post_kol[]  = 'return_fun_name';
          $vars_post_val[]  = 'f_app_return';

          $vars_post_kol[]  = 'return_elm_id';
          $vars_post_val[]  = 'id_id_reporting_entity';

          $vars_post        = f_app_vars_page_encrypt($vars_post_kol, $vars_post_val);
          $url_shto         = $url_shto_reporting_entity.'&vars_post='.$vars_post;

          $Grid_form['data'][$nr]['link']              = 'Y';
          $Grid_form['data'][$nr]['link_att']          = 'href="javascript:void(0);"';
          $Grid_form['data'][$nr]['link_data_modal']   = 'Y';
          $Grid_form['data'][$nr]['link_data_title']   = WebApp::getVar("shto_reporting_entity_mesg");
          $Grid_form['data'][$nr]['link_data_url']     = $url_shto;
          $Grid_form['data'][$nr]['has_icon']          = 'Y';
          $Grid_form['data'][$nr]['icon_type']         = 'icon_edit';
          $Grid_form['data'][$nr]['link_modal_iframe'] = 'true';
          $Grid_form['data'][$nr]['link_modal_size']   = 'modal-lg'; //link_modal_width mer perparesi
          $Grid_form['data'][$nr]['link_modal_width']  = '1300';
          $Grid_form['data'][$nr]['link_modal_height'] = '1000';
         }
     }

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'id_reporting_entity';
  $Grid_form['data'][$nr]['value']            = $lov_id_reporting_entity;
  $Grid_form['data'][$nr]['id']               = 'id_id_reporting_entity';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{id_reporting_entity_mesg}}" id_obj_parent="id_id_branch"';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //id_reporting_entity ----------------------------------------------------------------------------------------

  //form_number -----------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{form_number_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_form_number';
  $Grid_form['data'][$nr]['id']               = 'id_form_number_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'form_number';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['form_number'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_form_number';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '30';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{form_number_mesg}}" disabled';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //form_number -----------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //mbyllet col 1 ----------------------------------------------------------------------------------------------------
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $col_2_size;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //date_receipt_from_reporting_entity ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{date_receipt_from_reporting_entity_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_date_receipt_from_reporting_entity';
  $Grid_form['data'][$nr]['id']               = 'id_date_receipt_from_reporting_entity_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_date;
  $Grid_form['data'][$nr]['name']             = 'date_receipt_from_reporting_entity';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['date_receipt_from_reporting_entity'][0]['vlf']).'';
  $Grid_form['data'][$nr]['id']               = 'id_date_receipt_from_reporting_entity';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '10';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,1,0,0" etiketa="{{date_receipt_from_reporting_entity_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //date_receipt_from_reporting_entity -------------------------------------------------------------------------------------------

  //date_receipt_in_center ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{date_receipt_in_center_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_date_receipt_in_center';
  $Grid_form['data'][$nr]['id']               = 'id_date_receipt_in_center_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_date;
  $Grid_form['data'][$nr]['name']             = 'date_receipt_in_center';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['date_receipt_in_center'][0]['vlf']).'';
  $Grid_form['data'][$nr]['id']               = 'id_date_receipt_in_center';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '10';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,1,0,0" etiketa="{{date_receipt_in_center_mesg}}"'.$date_receipt_in_center_disabled;
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //date_receipt_in_center -------------------------------------------------------------------------------------------

  //total_number_doctors ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{total_number_doctors_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_total_number_doctors';
  $Grid_form['data'][$nr]['id']               = 'id_total_number_doctors_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'total_number_doctors';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['total_number_doctors'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_total_number_doctors';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '4';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,1,0,0,0,1" etiketa="{{total_number_doctors_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //total_number_doctors ----------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //mbyllet col 1 ----------------------------------------------------------------------------------------------------
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $col_3_size;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //number_doctors_reported ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{number_doctors_reported_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_number_doctors_reported';
  $Grid_form['data'][$nr]['id']               = 'id_number_doctors_reported_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'number_doctors_reported';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['number_doctors_reported'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_number_doctors_reported';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '4';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,1,0,0,0,1" etiketa="{{number_doctors_reported_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //number_doctors_reported ----------------------------------------------------------------------------------------

  //week_date_start ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{week_date_start_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_week_date_start';
  $Grid_form['data'][$nr]['id']               = 'id_week_date_start_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_date;
  $Grid_form['data'][$nr]['name']             = 'week_date_start';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['week_date_start'][0]['vlf']).'';
  $Grid_form['data'][$nr]['id']               = 'id_week_date_start';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '10';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,1,0,0" etiketa="{{week_date_start_mesg}}" onchange="f_change_week_date_start(this.value)"';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //week_date_start -------------------------------------------------------------------------------------------

  //week_date_end ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{week_date_end_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_week_date_end';
  $Grid_form['data'][$nr]['id']               = 'id_week_date_end_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_date;
  $Grid_form['data'][$nr]['name']             = 'week_date_end';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['week_date_end'][0]['vlf']).'';
  $Grid_form['data'][$nr]['id']               = 'id_week_date_end';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '10';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,1,0,0" etiketa="{{week_date_end_mesg}}" disabled';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //week_date_end -------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //col 2 ----------------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  
  //tab label --------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //$nr = $nr + 1;
  //$Grid_form['data'][$nr]['type']             = 'section_start';
  //$Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'obj_preview';
  $Grid_form['data'][$nr]['value']            = '{{numri_vizitave_te_reja_mesg}}:';
  $Grid_form['data'][$nr]['for']              = '';
  $Grid_form['data'][$nr]['id']               = '';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '0';

  //$nr = $nr + 1;
  //$Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //tab label --------------------------------------------------------------------------------------------------------------

  //tab --------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'table_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'thead_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tr_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //th -------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_start';
  $Grid_form['data'][$nr]['other_attributes'] = 'rowspan="2"';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{sindroma_infeksioze_mesg}}';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '0';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_end';
  //th -------------------------------------------------------------------------------------------------------------------

  //th -------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_start';
  $Grid_form['data'][$nr]['other_attributes'] = 'rowspan="2"';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{raste_total_mesg}}';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '0';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_end';
  //th -------------------------------------------------------------------------------------------------------------------

  //th -------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_start';
  $Grid_form['data'][$nr]['other_attributes'] = 'colspan="'.COUNT($agegroup_data_arr).'"';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{raste_sipas_grupmoshave_mesg}}';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '0';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_end';
  //th -------------------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tr_end';
  //tr1 ------------------------------------------------------------------------------------------------------------------

  //tr2 koka -------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tr_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  RESET($agegroup_data_arr);
  WHILE (LIST($key, $val) = EACH($agegroup_data_arr)) 
        {
         $id_agegroup_vl = $key;
         $id_agegroup_et = $val;
       
         //th ------------------------------------------------------------------------------------------------------------
           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'th_start';
           $Grid_form['data'][$nr]['other_attributes'] = '';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'label';
           $Grid_form['data'][$nr]['value']            = $id_agegroup_et;
           $Grid_form['data'][$nr]['other_attributes'] = '';
           $Grid_form['data'][$nr]['width']            = '0';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'th_end';
         //th ------------------------------------------------------------------------------------------------------------
        }

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tr_end';
  //tr2 koka -------------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'thead_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tbody_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //tr trupi -------------------------------------------------------------------------------------------------------------
  RESET($syndrome_data_arr);
  WHILE (LIST($key, $val) = EACH($syndrome_data_arr)) 
        {
         $id_syndrome_vl = $key;
         $id_syndrome_et = $val;
       
         $nr = $nr + 1;
         $Grid_form['data'][$nr]['type']             = 'tr_start';
         $Grid_form['data'][$nr]['other_attributes'] = '';

         //td syndrome ---------------------------------------------------------------------------------------------------
           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_start';
           $Grid_form['data'][$nr]['other_attributes'] = '';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'label';
           $Grid_form['data'][$nr]['value']            = $id_syndrome_et;
           $Grid_form['data'][$nr]['other_attributes'] = '';
           $Grid_form['data'][$nr]['width']            = '0';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_end';
         //td syndrome ---------------------------------------------------------------------------------------------------

         //td raste totale -----------------------------------------------------------------------------------------------
           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_start';
           $Grid_form['data'][$nr]['other_attributes'] = ' style="width:100px;"';

           $id_total_cases = 'id_total_cases_'.$id_syndrome_vl;
           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = $obj_type_text;
           $Grid_form['data'][$nr]['name']             = 'total_cases_'.$id_syndrome_vl;
           $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($phi_form_alert_sindroma_arr[$id_syndrome_vl]).'';
           $Grid_form['data'][$nr]['id']               = $id_total_cases;
           $Grid_form['data'][$nr]['placeholder']      = '';
           $Grid_form['data'][$nr]['maxlength']        = '5';
           $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,1,0,0,0,1" etiketa="{{total_cases_mesg}}" disabled';
           $Grid_form['data'][$nr]['width']            = '0';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_end';
         //td raste totale -----------------------------------------------------------------------------------------------

         //td raste grupmoshat -------------------------------------------------------------------------------------------
           RESET($agegroup_data_arr);
           WHILE (LIST($key1, $val1) = EACH($agegroup_data_arr)) 
                 {
                  $id_agegroup_vl = $key1;
                  $id_agegroup_et = $val1;
       
                  //td ---------------------------------------------------------------------------------------------------
                    $nr = $nr + 1;
                    $Grid_form['data'][$nr]['type']             = 'td_start';
                    $Grid_form['data'][$nr]['other_attributes'] = ' style="width:55px;"';

                    $nr = $nr + 1;
                    $Grid_form['data'][$nr]['type']             = $obj_type_text;
                    $Grid_form['data'][$nr]['name']             = 'cases_'.$id_syndrome_vl.'_'.$id_agegroup_vl;
                    $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($phi_form_alert_sindroma_agegroup[$id_syndrome_vl][$id_agegroup_vl]).'';
                    $Grid_form['data'][$nr]['id']               = 'id_cases_'.$id_syndrome_vl;
                    $Grid_form['data'][$nr]['placeholder']      = '';
                    $Grid_form['data'][$nr]['maxlength']        = '5';
                    $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,3,0,0,0,1" etiketa="{{cases_mesg}}" style="" id_elm_total="'.$id_total_cases.'" onchange="f_change_cases(\''.$id_total_cases.'\')"';
                    $Grid_form['data'][$nr]['width']            = '0';

                    $nr = $nr + 1;
                    $Grid_form['data'][$nr]['type']             = 'td_end';
                  //td ---------------------------------------------------------------------------------------------------
                 }
         //td raste grupmoshat -------------------------------------------------------------------------------------------

         $nr = $nr + 1;
         $Grid_form['data'][$nr]['type']             = 'tr_end';
         //tr trupi ------------------------------------------------------------------------------------------------------
        }

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tbody_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'table_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //tab ------------------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_body_end';

  //largojme butonat -----------------------------------------------------------------------------------------------------
    IF ($editim_konsultim == 'editim')
       {
        $nr = $nr + 1;
        $Grid_form['data'][$nr]['type']             = 'row_start';
        $Grid_form['data'][$nr]['other_attributes'] = '';

        $nr = $nr + 1;
        $Grid_form['data'][$nr]['type']             = 'col_start';
        $Grid_form['data'][$nr]['width']            = '12';
        $Grid_form['data'][$nr]['other_attributes'] = '';

        $nr = $nr + 1;
        $Grid_form['data'][$nr]['type']             = 'label';
        $Grid_form['data'][$nr]['value']            = '';
        $Grid_form['data'][$nr]['for']              = '';
        $Grid_form['data'][$nr]['id']               = '';
        $Grid_form['data'][$nr]['other_attributes'] = '';
        $Grid_form['data'][$nr]['width']            = '';

        $nr = $nr + 1;
        $Grid_form['data'][$nr]['type']             = 'col_end';

        $nr = $nr + 1;
        $Grid_form['data'][$nr]['type']             = 'row_end';
       }
  //largojme butonat -----------------------------------------------------------------------------------------------------

  //buttons --------------------------------------------------------------------------------------------------------------
    INCLUDE(dirname(__FILE__)."/../php_script/add_edit_03_buttons.php");
  //buttons --------------------------------------------------------------------------------------------------------------
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_closed';

  //audit trail ----------------------------------------------------------------------------------------------------------
    INCLUDE(dirname(__FILE__)."/../php_script/add_edit_04_audit_trail.php");
  //audit trail ----------------------------------------------------------------------------------------------------------

  //end ------------------------------------------------------------------------------------------------------------------
    INCLUDE(dirname(__FILE__)."/../php_script/add_edit_05_end.php");
  //end ------------------------------------------------------------------------------------------------------------------
//Grid_form --------------------------------------------------------------------------------------------------------------
?>