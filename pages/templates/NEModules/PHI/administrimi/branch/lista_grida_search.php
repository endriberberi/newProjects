<?
//GRID SEARCH ------------------------------------------------------------------------------------------------------------
  $arg_id_form             = "id_skeda_search";
  $arg_webbox              = $WEBBOX_SEL;
  
  $arg_event_search        = 'search';
  $arg_event_change_page   = 'change_page';
  $arg_event_next_prev     = 'next_prev';
  $arg_event_change_nr_rec = 'change_nr_rec';
  $arg_event_col_order     = 'col_order';

  //id_municipality ------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "id_municipality";
    IF ($G_APP_VARS["s_address_id_municipality"] != "")
       {
        $lov["id_select"] = ValidateVarFun::f_real_escape_string($G_APP_VARS["s_address_id_municipality"]);
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "s_address_id_municipality";
    $lov["valid"]         = "0,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov_s_address_id_municipality      = f_app_lov_default_values($lov);
  //id_municipality ------------------------------------------------------------------------------------------------------
  
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

  //col ------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = '4';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{address_id_municipality_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_s_address_id_municipality';
  $Grid_form['data'][$nr]['id']               = 'id_s_address_id_municipality_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '4';
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'select';
  $Grid_form['data'][$nr]['name']             = 's_address_id_municipality';
  $Grid_form['data'][$nr]['value']            = $lov_s_address_id_municipality;
  $Grid_form['data'][$nr]['id']               = 'id_s_address_id_municipality';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{address_id_municipality_mesg}}"';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['filter']           = 'Y';
  $Grid_form['data'][$nr]['width']            = '8';
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //col ------------------------------------------------------------------------------------------------------------------

  //col ------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = '4';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{emertimi_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_s_name';
  $Grid_form['data'][$nr]['id']               = 'id_s_name_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '4';
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'text';
  $Grid_form['data'][$nr]['name']             = 's_name';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($G_APP_VARS["s_name"]).'';
  $Grid_form['data'][$nr]['id']               = 'id_s_name';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{emertimi_mesg}}"';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '50';
  $Grid_form['data'][$nr]['width']            = '8';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //col ------------------------------------------------------------------------------------------------------------------

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