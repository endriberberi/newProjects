<?
//admini duhet te kete te drejte te editoje te gjithe rekordet sepse mund te percaktoje userat per institucion -----------
  $user_tool_users_management = f_app_user_management_tool(14); //14 = toolsi i userave
//------------------------------------------------------------------------------------------------------------------------

//start ------------------------------------------------------------------------------------------------------------------
  INCLUDE(dirname(__FILE__)."/../../php_script/add_edit_01_start.php");
//start ------------------------------------------------------------------------------------------------------------------

//overright size per etiketat dhe objektet e formes ----------------------------------------------------------------------
  $nr_cols  = 3;
  $col_size = 12/$nr_cols;

  $col_1_size = 4; 
  $col_2_size = 4; 
  $col_3_size = 4; 
  
  $width_form = 12; //brenda kolones sa madhesi do zeme
  $width_lab  = 12;
  IF ($width_lab == 12)
     {
      $width_obj = 12; 
     }
  ELSE
     {
      $width_obj = 12 - $width_lab; 
     }
//overright size per etiketat dhe objektet e formes ----------------------------------------------------------------------

IF (ISSET($post_id) AND ($post_id != ""))
   {
    unset($tab);
    $kushti_where        = 'WHERE id_reporting_entity = "'.ValidateVarFun::f_real_escape_string($post_id).'"';
    $tab['tab_name']     = 'phi_reporting_entity';            //*emri i tabeles ku do behet select
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
    
    $upd_record_user      = f_app_record_user ($val_rec['record_user'][0]['vl']);
    $upd_record_timestamp = $val_rec['record_timestamp'][0]['vlf_dt'];

    //kapim id fshatrave me te cilat eshte e lidhur kjo qender -----------------------------------------------------------
      unset($lov);
      $lov["name"]         = "ids_village_reporting_entity";
      $lov["obj_or_label"] = "label";
      $lov["only_ids"]     = "Y";
      $lov["filter"]       = 'WHERE id_reporting_entity = "'.ValidateVarFun::f_real_escape_string($post_id).'"';
      $ids_village_reporting_entity_arr = f_app_lov_default_values($lov);
        
      IF (IS_ARRAY($ids_village_reporting_entity_arr))
         {
          $ids_village_reporting_entity = implode(",", $ids_village_reporting_entity_arr);
         }
      ELSE
         {
          $ids_village_reporting_entity = "";
         }
    //kapim id fshatrave me te cilat eshte e lidhur kjo qender -----------------------------------------------------------

    //kapim id userave me te cilat eshte e lidhur kjo qender -------------------------------------------------------------
      unset($lov);
      $lov["name"]                   = "ids_user_reporting_entity";
      $lov["obj_or_label"]           = "label";
      $lov["only_ids"]               = "Y";
      $lov["filter"]                 = 'WHERE id_reporting_entity = "'.ValidateVarFun::f_real_escape_string($post_id).'"';
      $ids_user_reporting_entity_arr = f_app_lov_default_values($lov);
        
      IF (IS_ARRAY($ids_user_reporting_entity_arr))
         {
          $ids_user_reporting_entity = implode(",", $ids_user_reporting_entity_arr);
         }
      ELSE
         {
          $ids_user_reporting_entity = "";
         }
    //kapim id userave me te cilat eshte e lidhur kjo qender -------------------------------------------------------------
   }

//NESE USERI ESHTE I LIDHUR ME NJE QENDER RAPORTIMI ----------------------------------------------------------------------
  IF ($editim_konsultim == 'editim')
     {
      IF (($user_dega_qendra["edit_branch_nr"] == "1") AND !ISSET($val_rec['id_branch'][0]['vl']))
         {
          $val_rec['id_branch'][0]['vl'] = $user_dega_qendra["edit_branch_ids"];
         }
     }
//NESE USERI ESHTE I LIDHUR ME NJE QENDER RAPORTIMI ----------------------------------------------------------------------

//rights -----------------------------------------------------------------------------------------------------------------
  INCLUDE(dirname(__FILE__)."/../../php_script/add_edit_02_rights.php");
//rights -----------------------------------------------------------------------------------------------------------------

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

    //kur vime ne popup per te shtuar laburator --------------------------------------------------------------------------
      IF (($G_APP_VARS["nem_mode_filter"] == "laboratory") OR ($user_tool_users_management == "Y"))
         {
          $lov["filter"]  = "";
         }
    //kur vime ne popup per te shtuar laburator --------------------------------------------------------------------------

    $lov_id_branch        = f_app_lov_default_values($lov);
  //id_branch ------------------------------------------------------------------------------------------------------------

  //id_reporting_entity_kind ---------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"] = "id_reporting_entity_kind";
    
    IF ($G_APP_VARS["nem_mode_filter"] == "laboratory")
       {
        $lov["filter"]                                = "WHERE id_reporting_entity_kind = 3";
        $lab_reporting_entity_kind                    = ' disabled';
        $val_rec['id_reporting_entity_kind'][0]['vl'] = 3;
       }

    IF ($val_rec['id_reporting_entity_kind'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['id_reporting_entity_kind'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "id_reporting_entity_kind";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;

    $lov_id_reporting_entity_kind = f_app_lov_default_values($lov);
  //id_municipality ------------------------------------------------------------------------------------------------------

  //id_municipality ------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "id_municipality";
    IF ($val_rec['address_id_municipality'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['address_id_municipality'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "address_id_municipality";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_address_id_municipality = f_app_lov_default_values($lov);
  //id_municipality ------------------------------------------------------------------------------------------------------

  //id_village ----------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "id_village";
    IF ($val_rec['address_id_village'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['address_id_village'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }

    IF ($val_rec['address_id_municipality'][0]['vl'] != "")
       {
        $lov["filter"] = "WHERE id_municipality = '".ValidateVarFun::f_real_escape_string($val_rec['address_id_municipality'][0]['vl'])."'";
       }
    ELSE
       {
        $lov["filter"] = "WHERE id_municipality = '-1'";
       }

    $lov["object_name"]   = "address_id_village";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_address_id_village = f_app_lov_default_values($lov);
  //id_village ----------------------------------------------------------------------------------------------------------

  //PER FILTRIMIN E FSHATRAVE --------------------------------------------------------------------------------------------
    IF ($editim_konsultim == 'editim')
       {
        unset($lov);
        $lov["name"]                        = "id_municipality_id_village";
        $lov["obj_or_label"]                = "label";
        $lov["all_data_array"]              = "Y";
        $lov_id_municipality_id_village_arr = f_app_lov_default_values($lov);
        $arr_js                             = f_app_js_arr($lov_id_municipality_id_village_arr);
        $script_js_in_page .= '
                                f_app_array_js["address_id_municipality"] = "'.$arr_js.'";
                              ';
       }
  //PER FILTRIMIN E FSHATRAVE --------------------------------------------------------------------------------------------

  //state_or_private ------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "state_or_private";
    IF ($val_rec['state_or_private'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['state_or_private'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "state_or_private";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_state_or_private = f_app_lov_default_values($lov);
  //id_municipality ------------------------------------------------------------------------------------------------------

  //fills_alert_form ------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "fills_alert_form";
    IF ($val_rec['fills_alert_form'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['fills_alert_form'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "fills_alert_form";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_fills_alert_form = f_app_lov_default_values($lov);
  //id_municipality ------------------------------------------------------------------------------------------------------

  //record_status --------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "record_status";
    IF ($val_rec['record_status'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['record_status'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "record_status";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["alert_etiketa"] = "Statusi";
    $lov["null_print"]    = "F";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_record_status    = f_app_lov_default_values($lov);
  //record_status --------------------------------------------------------------------------------------------------------

  //ids_user_reporting_entity --------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "UserId";
    IF ($ids_user_reporting_entity != "")
       {
        $lov["id_select"] = $ids_user_reporting_entity;
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "ids_user_reporting_entity";
    $lov["valid"]         = "0,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
    $lov["null_print"]    = "F";

    IF ($editim_konsultim == 'editim')
       {
        $lov["filter_plus"] = " AND Status_user = 'y'";
       }
    
    IF ($editim_konsultim == 'editim')
       {
        //e drejta per regjistrimin e userave do i lihet vetem atij qe ka te drejte te regjistroje perdorues -------------
        //pra ka te drejte te perdori toolsin me ID:14 Users Management
          IF ($user_tool_users_management == "Y")
             {
              //useri ka te drejte te regjistroje perdorues keshtu qe kete fushe e nxjerim per monitorim/editim pra leme ate qe eshte
             }
          ELSE
             {
              //useri nuk ka te drejte te regjistroje perdorues keshtu qe kete fushe e nxjerim per monitorim
              $lov["obj_or_label"] = "label";
             }
       }
    
    $lov_ids_user_reporting_entity = f_app_lov_default_values($lov);
  //ids_user_branch_view ------------------------------------------------------------------------------------------------

  //ids_village_reporting_entity ----------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"] = "district_commune_village";
    IF ($ids_village_reporting_entity != "")
       {
        $lov["id_select"] = $ids_village_reporting_entity;
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "ids_village_reporting_entity";
    $lov["valid"]         = "0,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
    $lov["null_print"]    = "F";
  
    $lov_ids_village_reporting_entity = f_app_lov_default_values($lov);
  //ids_village_reporting_entity -----------------------------------------------------------------------------------------

  //sa per te bere lidhjen me fshatrat -----------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "district_commune_name";
    $lov["id_select"]     = "-1";
    $lov["object_name"]   = "id_commune";
    $lov["valid"]         = "0,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
    $lov["null_print"]    = "T";
  
    $lov_id_commune = f_app_lov_default_values($lov);
//LOV --------------------------------------------------------------------------------------------------------------------
   
//Grid_form --------------------------------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{id_branch_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_id_branch';
  $Grid_form['data'][$nr]['id']               = 'id_id_branch_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'id_branch';
  $Grid_form['data'][$nr]['value']            = $lov_id_branch;
  $Grid_form['data'][$nr]['id']               = 'id_id_branch';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{id_branch_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //id_branch ------------------------------------------------------------------------------------------

  //name -----------------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{emertimi_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_name';
  $Grid_form['data'][$nr]['id']               = 'id_name_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'name';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['name'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_name';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '100';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{emertimi_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //name -----------------------------------------------------------------------------------------------

  //id_reporting_entity_kind ---------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{id_reporting_entity_kind_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_id_reporting_entity_kind';
  $Grid_form['data'][$nr]['id']               = 'id_id_reporting_entity_kind_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'id_reporting_entity_kind';
  $Grid_form['data'][$nr]['value']            = $lov_id_reporting_entity_kind;
  $Grid_form['data'][$nr]['id']               = 'id_id_reporting_entity_kind';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{id_reporting_entity_kind_mesg}}" '.$lab_reporting_entity_kind;
  $Grid_form['data'][$nr]['width']            = $width_obj;
  $Grid_form['data'][$nr]['filter']           = 'N';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //id_reporting_entity_kind ---------------------------------------------------------------------------

  
  //fills_alert_form ---------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{fills_alert_form_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_fills_alert_form';
  $Grid_form['data'][$nr]['id']               = 'id_fills_alert_form_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'fills_alert_form';
  $Grid_form['data'][$nr]['value']            = $lov_fills_alert_form;
  $Grid_form['data'][$nr]['id']               = 'id_fills_alert_form';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{fills_alert_form_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  $Grid_form['data'][$nr]['filter']           = 'N';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //fills_alert_form ---------------------------------------------------------------------------

  //state_or_private ---------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{state_or_private_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_state_or_private';
  $Grid_form['data'][$nr]['id']               = 'id_state_or_private_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'state_or_private';
  $Grid_form['data'][$nr]['value']            = $lov_state_or_private;
  $Grid_form['data'][$nr]['id']               = 'id_state_or_private';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{state_or_private_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  $Grid_form['data'][$nr]['filter']           = 'N';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //state_or_private ---------------------------------------------------------------------------

  //code -----------------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = '{{code_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_code';
  $Grid_form['data'][$nr]['id']               = 'id_code_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'code';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['code'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_code';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '30';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{code_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //code -----------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $col_2_size;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //address_id_municipality ----------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = '{{address_id_municipality_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_address_id_municipality';
  $Grid_form['data'][$nr]['id']               = 'id_address_id_municipality_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'address_id_municipality';
  $Grid_form['data'][$nr]['value']            = $lov_address_id_municipality;
  $Grid_form['data'][$nr]['id']               = 'id_address_id_municipality';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{address_id_municipality_mesg}}" onchange="f_app_filter_listbox(this.id)" js_data_array="address_id_municipality" id_obj_child="id_address_id_village"';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //address_id_municipality ----------------------------------------------------------------------------------------

  //address_id_village ----------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = '{{address_id_village_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_address_id_village';
  $Grid_form['data'][$nr]['id']               = 'id_address_id_village_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'address_id_village';
  $Grid_form['data'][$nr]['value']            = $lov_address_id_village;
  $Grid_form['data'][$nr]['id']               = 'id_address_id_village';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{address_id_village_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //address_id_village ----------------------------------------------------------------------------------------
  
  //address ---------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = '{{address_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_address';
  $Grid_form['data'][$nr]['id']               = 'id_address_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'address';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['address'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_address';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '250';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{address_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //address ---------------------------------------------------------------------------------

  //tel -----------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = '{{tel_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_tel';
  $Grid_form['data'][$nr]['id']               = 'id_tel_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'tel';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['tel'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_tel';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '100';
  $Grid_form['data'][$nr]['icon']             = 'tel';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{tel_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //tel -----------------------------------------------------------------------------------------

  //mobile -----------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = '{{mobile_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_mobile';
  $Grid_form['data'][$nr]['id']               = 'id_mobile_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'mobile';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['mobile'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_mobile';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '100';
  $Grid_form['data'][$nr]['icon']             = 'mobile';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{mobile_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //mobile -----------------------------------------------------------------------------------------

  //email -----------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = '{{email_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_email';
  $Grid_form['data'][$nr]['id']               = 'id_email_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'email';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['email'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_email';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '100';
  $Grid_form['data'][$nr]['icon']             = 'email';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,1,0" etiketa="{{email_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //email -----------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $col_3_size;
  $Grid_form['data'][$nr]['other_attributes'] = '';
  
  //ids_user_reporting_entity ---------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = '12';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{perdoruesit_qender_raportimi_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'ids_user_reporting_entity';
  $Grid_form['data'][$nr]['id']               = 'id_ids_user_reporting_entity_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  IF ($user_tool_users_management == "Y")
     {
      $Grid_form['data'][$nr]['type']         = $obj_type_select_multiple;  //ja leme te drejten vetem administratorit
     }
  ELSE
     {
      $Grid_form['data'][$nr]['type']         = 'obj_preview';
     }
  $Grid_form['data'][$nr]['name']             = 'ids_user_reporting_entity';
  $Grid_form['data'][$nr]['value']            = $lov_ids_user_reporting_entity;
  $Grid_form['data'][$nr]['id']               = 'id_ids_user_reporting_entity';
  $Grid_form['data'][$nr]['placeholder']      = '{{type_to_select_mesg}}';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{perdoruesit_view_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //ids_user_reporting_entity ---------------------------------------------------------------------

  //ids_village_reporting_entity ------------------------------------------------------------------
  IF ($val_rec['id_reporting_entity_kind'][0]['vl'] == 1)
     {
      //shfaqet vetem per qendrat shendetesore
      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'row_start';
      $Grid_form['data'][$nr]['other_attributes'] = '';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'col_start';
      $Grid_form['data'][$nr]['width']            = '12';
      $Grid_form['data'][$nr]['other_attributes'] = '';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'section_start';
      $Grid_form['data'][$nr]['other_attributes'] = '';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'label';
      $Grid_form['data'][$nr]['value']            = '{{fshatrat_qe_mbulohen_nga_qsh_mesg}}:';
      $Grid_form['data'][$nr]['for']              = 'id_ids_village_reporting_entity';
      $Grid_form['data'][$nr]['id']               = 'id_id_ids_village_reporting_entity_label';
      $Grid_form['data'][$nr]['other_attributes'] = '';
      $Grid_form['data'][$nr]['width']            = 12;

      $Grid_form['data'][$nr]['type']             = $obj_type_select_multiple;
      $Grid_form['data'][$nr]['name']             = 'ids_village_reporting_entity';
      $Grid_form['data'][$nr]['value']            = $lov_ids_village_reporting_entity;
      $Grid_form['data'][$nr]['id']               = 'id_ids_village_reporting_entity';
      $Grid_form['data'][$nr]['placeholder']      = '{{type_to_select_mesg}}';
      $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{fshatrat_qe_mbulohen_nga_qsh_mesg}}"';
      $Grid_form['data'][$nr]['width']            = 12;

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'section_end';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'col_end';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'row_end';
     }
  //ids_village_reporting_entity ------------------------------------------------------------------

  //SA PER TE PLOTESUAR FSHATRAT ------------------------------------------------------------------
  //ids_village_reporting_entity ------------------------------------------------------------------
  IF ($val_rec['id_reporting_entity_kind'][0]['vl'] == 1)
     {
      //shfaqet vetem per qendrat shendetesore
      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'row_start';
      $Grid_form['data'][$nr]['other_attributes'] = '';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'col_start';
      $Grid_form['data'][$nr]['width']            = '12';
      $Grid_form['data'][$nr]['other_attributes'] = '';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'section_start';
      $Grid_form['data'][$nr]['other_attributes'] = '';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'label';
      $Grid_form['data'][$nr]['value']            = 'Komuna:';
      $Grid_form['data'][$nr]['for']              = 'id_komuna';
      $Grid_form['data'][$nr]['id']               = 'id_id_komuna_label';
      $Grid_form['data'][$nr]['other_attributes'] = '';
      $Grid_form['data'][$nr]['width']            = 12;

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = $obj_type_select;
      $Grid_form['data'][$nr]['name']             = 'id_commune';
      $Grid_form['data'][$nr]['value']            = $lov_id_commune;
      $Grid_form['data'][$nr]['id']               = 'id_id_komuna';
      $Grid_form['data'][$nr]['placeholder']      = '{{type_to_select_mesg}}';
      $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{id_commune_mesg}}"';
      $Grid_form['data'][$nr]['width']            = 12;
      $Grid_form['data'][$nr]['filter']           = 'Y';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'section_end';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'col_end';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'row_end';
     }
  //ids_village_reporting_entity ------------------------------------------------------------------
  //SA PER TE PLOTESUAR FSHATRAT ------------------------------------------------------------------


  //notes -----------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = '{{notes_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_notes';
  $Grid_form['data'][$nr]['id']               = 'id_notes_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_textarea;
  $Grid_form['data'][$nr]['name']             = 'notes';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['notes'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_notes';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = ' rows="3" valid="0,0,0,0,0,0" etiketa="{{notes_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //id_commune -----------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  //statusi --------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $col_size;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{statusi_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'record_status';
  $Grid_form['data'][$nr]['id']               = 'id_record_status_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'record_status';
  $Grid_form['data'][$nr]['value']            = $lov_record_status;
  $Grid_form['data'][$nr]['id']               = 'id_record_status';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{statusi_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //statusi ----------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_body_end';

  //buttons --------------------------------------------------------------------------------------------------------------
    INCLUDE(dirname(__FILE__)."/../../php_script/add_edit_03_buttons.php");
  //buttons --------------------------------------------------------------------------------------------------------------

  IF (($G_APP_VARS["nem_mode"] == "select_record") AND ($post_id != ""))
     {
      //shtojme butonin per zgjedhjen e ketij rekordi -------------------------------------------------------------------
      //$nr = $nr + 1;
      //$Grid_form['data'][$nr]['type']             = 'form_footer_start';
      //$Grid_form['data'][$nr]['other_attributes'] = '';

      //id_reporting_entity_kind -----------------------------------------------------------------------------------------
        //unset($lov);
        //$lov["name"]               = "id_reporting_entity_kind";
        //$lov["id_select"]          = $val_rec['id_reporting_entity_kind'][0]['vl'];
        //$lov["obj_or_label"]       = "label";
        //$lov_reporting_entity_kind = f_app_lov_default_values($lov);
      //id_reporting_entity_kind -----------------------------------------------------------------------------------------

      //$name_sel_js       = $val_rec['name'][0]['vl'].' ('.$lov_reporting_entity_kind.')';
      $name_sel_js       = $val_rec['name'][0]['vl'];
      $name_sel_js       = STR_REPLACE(array("'", "\""), "", $name_sel_js);

      $but_select_action = ' onclick="javascript:parent.'.$G_APP_VARS["return_fun_name"].'(\''.$G_APP_VARS["return_elm_id"].'\', \''.$val_rec['id_reporting_entity'][0]['vl'].'\', \''.$name_sel_js.'\', \''.$val_rec['id_branch'][0]['vl'].'\');parent.EW.modal.close();"';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'row_start';
      $Grid_form['data'][$nr]['other_attributes'] = '';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'col_start';
      $Grid_form['data'][$nr]['width']            = '12';
      $Grid_form['data'][$nr]['other_attributes'] = '';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'button';
      $Grid_form['data'][$nr]['button_type']      = 'button';
      $Grid_form['data'][$nr]['name']             = 'but_select';
      $Grid_form['data'][$nr]['value']            = '{{select_mesg}}';
      $Grid_form['data'][$nr]['id']               = 'id_but_select';
      $Grid_form['data'][$nr]['other_attributes'] = $but_select_action;
      $Grid_form['data'][$nr]['primary']          = 'primary';
      $Grid_form['data'][$nr]['action_type']      = 'select';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'col_end';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'row_end';

      //$nr = $nr + 1;
      //$Grid_form['data'][$nr]['type']             = 'form_footer_end';
     }

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_closed';

  //audit trail ----------------------------------------------------------------------------------------------------------
    INCLUDE(dirname(__FILE__)."/../../php_script/add_edit_04_audit_trail.php");
  //audit trail ----------------------------------------------------------------------------------------------------------

  //end ------------------------------------------------------------------------------------------------------------------
    INCLUDE(dirname(__FILE__)."/../../php_script/add_edit_05_end.php");
  //end ------------------------------------------------------------------------------------------------------------------
//Grid_form --------------------------------------------------------------------------------------------------------------
?>