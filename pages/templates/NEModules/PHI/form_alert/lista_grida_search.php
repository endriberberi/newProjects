<?
//GRID SEARCH ------------------------------------------------------------------------------------------------------------
  $arg_id_form             = "id_skeda_search";
  $arg_webbox              = $WEBBOX_SEL;
  
  $arg_event_search        = 'search';
  $arg_event_change_page   = 'change_page';
  $arg_event_next_prev     = 'next_prev';
  $arg_event_change_nr_rec = 'change_nr_rec';
  $arg_event_col_order     = 'col_order';

  //s_id_branch ----------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"] = "id_branch";
    
    IF ($user_dega_qendra["view_branch_nr"] == 1)
       {
        $G_APP_VARS["s_id_branch"] = $user_dega_qendra["view_branch_ids"];
       }
    
    IF ($G_APP_VARS["s_id_branch"] != "")
       {
        $lov["id_select"] = ValidateVarFun::f_real_escape_string($G_APP_VARS["s_id_branch"]);
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }

    $lov["filter"]        = "WHERE id_branch IN (".$user_dega_qendra["view_branch_ids"].") ";
    $lov["object_name"]   = "s_id_branch";
    $lov["valid"]         = "0,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov_s_id_branch      = f_app_lov_default_values($lov);
  //s_id_branch ----------------------------------------------------------------------------------------------------------

  //id_reporting_entity --------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"] = "id_reporting_entity";
    
    IF ($user_dega_qendra["reporting_entity_nr"] == 1)
       {
        $G_APP_VARS["s_id_reporting_entity"] = $user_dega_qendra["reporting_entity_ids"];
       }
    
    IF ($G_APP_VARS["s_id_reporting_entity"] != "")
       {
        $lov["id_select"] = ValidateVarFun::f_real_escape_string($G_APP_VARS["s_id_reporting_entity"]);
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }

    $lov["filter"]  = "WHERE id_branch IN (".$user_dega_qendra["view_branch_ids"].") ";
    $lov["filter"] .= " AND fills_alert_form = 'Y'";

    IF ($G_APP_VARS["s_id_branch"] != "")
       {
        $lov["filter"] .= " AND id_branch = '".ValidateVarFun::f_real_escape_string($G_APP_VARS["s_id_branch"])."'";
       }

    IF ($user_dega_qendra["reporting_entity_ids"] != "")
       {
        $lov["filter"] .= " AND id_reporting_entity IN (".$user_dega_qendra["reporting_entity_ids"].")";
       }
    
    $lov["object_name"]   = "s_id_reporting_entity";
    $lov["valid"]         = "0,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov_s_id_reporting_entity = f_app_lov_default_values($lov);
  //id_reporting_entity --------------------------------------------------------------------------------------------------

  //PER FILTRIMIN E QENDRAVE TE RAPORTIMIT -------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]           = "id_branch_id_reporting_entity";
    $lov["obj_or_label"]   = "label";
    $lov["all_data_array"] = "Y";

    $lov["filter"]         = "WHERE id_branch IN (".$user_dega_qendra["view_branch_ids"].") ";
    $lov["filter"]        .= " AND fills_alert_form = 'Y'";

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
  
  //s_id_reporting_entity_kind -------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"] = "id_reporting_entity_kind";
    IF ($G_APP_VARS["s_id_reporting_entity_kind"] != "")
       {
        $lov["id_select"] = ValidateVarFun::f_real_escape_string($G_APP_VARS["s_id_reporting_entity_kind"]);
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }

    $lov["object_name"]   = "s_id_reporting_entity_kind";
    $lov["valid"]         = "0,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov_s_id_reporting_entity_kind = f_app_lov_default_values($lov);
  //s_id_reporting_entity_kind -------------------------------------------------------------------------------------------

//size per etiketat dhe objektet e formes --------------------------------------------------------------------------------
  $nr_cols  = 3;
  $col_size = 12/$nr_cols;
  
  $width_form = 12; //brenda kolones sa madhesi do zeme
  $width_lab  = 5;
  IF ($width_lab == 12)
     {
      $width_obj = 12; 
     }
  ELSE
     {
      $width_obj = 12 - $width_lab; 
     }
//size per etiketat dhe objektet e formes --------------------------------------------------------------------------------

//formatohen datat -------------------------------------------------------------------------------------------------------
  $s_dt1 = "";
  $s_dt2 = "";

  IF (ISSET($G_APP_VARS["s_dt1"]) AND ($G_APP_VARS["s_dt1"] != ""))
     {
      $s_dt1 = SUBSTR($G_APP_VARS["s_dt1"], 8, 2).".".SUBSTR($G_APP_VARS["s_dt1"], 5, 2).".".SUBSTR($G_APP_VARS["s_dt1"], 0, 4);
     }

  IF (ISSET($G_APP_VARS["s_dt2"]) AND ($G_APP_VARS["s_dt2"] != ""))
     {
      $s_dt2 = SUBSTR($G_APP_VARS["s_dt2"], 8, 2).".".SUBSTR($G_APP_VARS["s_dt2"], 5, 2).".".SUBSTR($G_APP_VARS["s_dt2"], 0, 4);
     }
//formatohen datat -------------------------------------------------------------------------------------------------------

  $nr = -1;
  $Grid_form = array('data' => array(), 'AllRecs' => '0');	

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_header';
  $Grid_form['data'][$nr]['label']            = '{{atributet_e_filtrimit_mesg}}';
  $Grid_form['data'][$nr]['data-action']      = 'collapse';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_open';
  $Grid_form['data'][$nr]['name']             = 'skeda_search';
  $Grid_form['data'][$nr]['id']               = $arg_id_form;
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
  $Grid_form['data'][$nr]['width']            = $col_size;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //id_branch ------------------------------------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = '{{id_branch_sh_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_s_id_branch';
  $Grid_form['data'][$nr]['id']               = 'id_s_id_branch_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 5;
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'select';
  $Grid_form['data'][$nr]['name']             = 's_id_branch';
  $Grid_form['data'][$nr]['value']            = $lov_s_id_branch;
  $Grid_form['data'][$nr]['id']               = 'id_s_id_branch';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{id_branch_mesg}}" onchange="f_app_filter_listbox(this.id)" js_data_array="id_branch" id_obj_child="id_s_id_reporting_entity"';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['filter']           = 'Y';
  $Grid_form['data'][$nr]['width']            = 7;
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //id_branch ------------------------------------------------------------------------------------------------------------------

  //id_reporting_entity ------------------------------------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = '{{id_reporting_entity_sh_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_s_id_reporting_entity';
  $Grid_form['data'][$nr]['id']               = 'id_s_id_reporting_entity_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'select';
  $Grid_form['data'][$nr]['name']             = 's_id_reporting_entity';
  $Grid_form['data'][$nr]['value']            = $lov_s_id_reporting_entity;
  $Grid_form['data'][$nr]['id']               = 'id_s_id_reporting_entity';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{id_reporting_entity_mesg}}"';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['filter']           = 'Y';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //id_reporting_entity ------------------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //col 1 ---------------------------------------------------------------------------------------------------------------------------------
  
  //col 2 ---------------------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $col_size;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //s_dt1 ----------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = '{{data_e_raportimit_mesg}} {{nga_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_s_dt1';
  $Grid_form['data'][$nr]['id']               = 'id_s_dt1_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'date';
  $Grid_form['data'][$nr]['name']             = 's_dt1';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($s_dt1).'';
  $Grid_form['data'][$nr]['id']               = 'id_s_dt1';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '10';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,1,0,0" etiketa="{{data_e_raportimit_mesg}} {{nga_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //s_dt1 -------------------------------------------------------------------------------------------

  //s_dt2 ----------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = '{{data_e_raportimit_mesg}} {{deri_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_s_dt2';
  $Grid_form['data'][$nr]['id']               = 'id_s_dt2_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'date';
  $Grid_form['data'][$nr]['name']             = 's_dt2';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($s_dt2).'';
  $Grid_form['data'][$nr]['id']               = 'id_s_dt2';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '10';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,1,0,0" etiketa="{{data_e_raportimit_mesg}} {{deri_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //s_dt2 -------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //col 2 ---------------------------------------------------------------------------------------------------------------------------------

  //col 3 ---------------------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $col_size;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //s_form_number ------------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = '{{form_number_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_s_form_number';
  $Grid_form['data'][$nr]['id']               = 'id_s_form_number_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'text';
  $Grid_form['data'][$nr]['name']             = 's_form_number';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['s_form_number'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_s_form_number';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '30';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{form_number_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //s_form_number ------------------------------------------------------------------------------------------

  //s_id_reporting_entity_kind ------------------------------------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = '{{id_reporting_entity_kind_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_s_id_reporting_entity_kind';
  $Grid_form['data'][$nr]['id']               = 'id_s_id_reporting_entity_kind_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'select';
  $Grid_form['data'][$nr]['name']             = 's_id_reporting_entity_kind';
  $Grid_form['data'][$nr]['value']            = $lov_s_id_reporting_entity_kind;
  $Grid_form['data'][$nr]['id']               = 'id_s_id_branch';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{id_reporting_entity_kind_mesg}}"';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['filter']           = 'N';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //s_id_reporting_entity_kind ------------------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //col 3 ---------------------------------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_body_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_footer_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = '12';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'button';
  $Grid_form['data'][$nr]['button_type']      = 'submit';
  $Grid_form['data'][$nr]['name']             = 's_kerko';
  $Grid_form['data'][$nr]['value']            = '{{kerko_mesg}}';
  $Grid_form['data'][$nr]['id']               = 'id_btnkerko';
  $Grid_form['data'][$nr]['other_attributes'] = ' onclick="javascript:f_app_search(\''.$arg_webbox.'\', \''.$arg_event_search.'\', \''.$arg_id_form.'\');"';
  $Grid_form['data'][$nr]['primary']          = 'primary';
  $Grid_form['data'][$nr]['action_type']      = 'search';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'button';
  $Grid_form['data'][$nr]['button_type']      = 'button';
  $Grid_form['data'][$nr]['name']             = 's_pastro';
  $Grid_form['data'][$nr]['value']            = '{{pastro_mesg}}';
  $Grid_form['data'][$nr]['id']               = 'id_btnpastro';
  $Grid_form['data'][$nr]['other_attributes'] = ' onclick="javascript:f_app_pastro(\''.$arg_id_form.'\');"';
  $Grid_form['data'][$nr]['primary']          = 'default';
  $Grid_form['data'][$nr]['action_type']      = 'clear';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_footer_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_closed';

  IF (COUNT($Grid_form['data']) > 0) 
     {
      $Grid_form['AllRecs'] = COUNT($Grid_form['data']);
     }
  WebApp::addVar('Grid_form', $Grid_form);
//GRID SEARCH ------------------------------------------------------------------------------------------------------------

//parametrat e navigimi --------------------------------------------------------------------------------------------------    
  IF (!ISSET($G_APP_VARS['nr_rec_page']) OR ($G_APP_VARS['nr_rec_page']==''))
     {
      $G_APP_VARS['nr_rec_page'] = $nr_rec_page_default;
     }
  $nr_rec_page = $G_APP_VARS['nr_rec_page'];
  
  IF (ISSET($G_APP_VARS['nr_rec_start']) AND ($G_APP_VARS['nr_rec_start'] != ''))
     {
      $nr_rec_start = $G_APP_VARS['nr_rec_start'];
     }
  ELSE    
     {
      $nr_rec_start = "0";
     }
  $nr_rec_start_print = $nr_rec_start + 1;
  $nr_rec_end         = $nr_rec_start + $nr_rec_page;
//parametrat e navigimi --------------------------------------------------------------------------------------------------    
?>