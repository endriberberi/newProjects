<?
WebApp::addVar("max_width", "800");

//$action_refresh    = ' onclick="javascript:f_app_refresh(\''.$arg_webbox.'\', \'save\', \''.$arg_id_form.'\');"'; //ka te drejte te modifikoje


//start ------------------------------------------------------------------------------------------------------------------
  INCLUDE(dirname(__FILE__)."/../php_script/add_edit_01_start.php");
//start ------------------------------------------------------------------------------------------------------------------

//overright size per etiketat dhe objektet e formes ----------------------------------------------------------------------
  $nr_cols  = 3;
  $col_size = 12/$nr_cols;

  $col_1_size = 12; 
  
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

  //per kolonen e dyte 
  $width_lab2  = 8;
  $width_obj2  = 4;
//overright size per etiketat dhe objektet e formes ----------------------------------------------------------------------

IF ($post_id == "")
   {
    EXIT;
   }

$post_id_arr    = EXPLODE(",", $post_id);
$post_id_event  = $post_id_arr[0];
$post_id_branch = $post_id_arr[1];
    
//kapim tabelen phi_event ------------------------------------------------------------------------------
    unset($tab);
    $kushti_where        = 'WHERE id_event = "'.ValidateVarFun::f_real_escape_string($post_id_event).'"';
    $tab['tab_name']     = 'phi_event';            //*emri i tabeles ku do behet select
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
    $val_rec_event       = f_app_select_form_table($tab);
//kapim tabelen phi_event ------------------------------------------------------------------------------

//kapim tabelen phi_event_branch ------------------------------------------------------------------------------
  unset($tab);
  $kushti_where         = 'WHERE id_event = "'.ValidateVarFun::f_real_escape_string($post_id_event).'" AND id_branch = "'.ValidateVarFun::f_real_escape_string($post_id_branch).'"';
  $tab['tab_name']      = 'phi_event_branch';            //*emri i tabeles ku do behet select
  $tab['sql_where']     = $kushti_where;           //default = "", kthen gjithe rekordet pa filtrim; perndryshe shkruani kushtin e filtrimit, mos haroni fjalen WHERE;
  $tab['nr_rec_tot']    = 'F';                     //default = "F", (FALSE); pranon vlerat T,F; kur eshte True kthen dhe numrin total te rekordeve qe kthen selekti;
  $tab['kol_filter']    = '';                      //default = ""(bosh); kur i do gjithe kolonat e tabeles lihet bosh; kur nuk te interesojne disa kolona i vendos emrat e kolonave te ndara me presjeve; zakonisht perdoret per te filtruar fushat e tipit blob;
  $tab['kol_select']    = '';                      //default = ""(bosh); kur i do gjithe kolonat e tabeles lihet bosh; kur te interesojne vetem disa kolona i vendos emrat e kolonave te ndara me presjeve;
  $tab['kol_order']     = '';                      //default = "", pra rekordet nuk renditen sipas ndonje kolone; emri i kolones sipas se ciles do renditen rekordet;
  $tab['kol_asc_desc']  = '';                      //default = "ASC"; pranon vlerat ASC, DESC; meret parasysh kur $tab['kol_order'] != "";
  $tab['rec_limit']     = '';                      //default = "", pra pa limit; perndryshe kthen ato rekorde qe jane percaktuar ne limit, formati = 0,10;
  $tab['obj_class']     = '';            		  //default = "txtbox"; emri i klases ne style, vlen kur $tab['is_form'] = "T";
  $tab['distinct']      = 'F';      				  //default = "F" -> pranon vlerat T,F (pra true ose false);
  $tab['is_form']       = 'F';                     //default = "T"; pranon vlerat T,F;
  $val_rec              = f_app_select_form_table($tab);
  
  $ins_record_user      = f_app_record_user ($val_rec['record_user_ins'][0]['vl']);
  $ins_record_timestamp = $val_rec['record_timestamp_ins'][0]['vlf_dt'];

  $upd_record_user      = f_app_record_user ($val_rec['record_user_upd'][0]['vl']);
  $upd_record_timestamp = $val_rec['record_timestamp_upd'][0]['vlf_dt'];
//kapim tabelen phi_event_branch ------------------------------------------------------------------------------
   
//rights -----------------------------------------------------------------------------------------------------------------
  INCLUDE(dirname(__FILE__)."/../php_script/add_edit_02_rights.php");
//rights -----------------------------------------------------------------------------------------------------------------

//ndryshojme funksionin e savit meqe kemi validime ekstra per kete forme -------------------------------------------------
  $but_regj_action = STR_REPLACE("f_app_save", "f_app_save_local_info", $but_regj_action); 
//------------------------------------------------------------------------------------------------------------------------

//$titull_print .= " - ".WebApp::getVar("event_dega_info_mesg");

//LOV --------------------------------------------------------------------------------------------------------------------
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
    $lov["obj_or_label"]  = "label";
    $lov_id_branch        = f_app_lov_default_values($lov);
  //id_branch ------------------------------------------------------------------------------------------------------------

  //event_status --------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"] = "event_status";
    IF ($val_rec['event_status'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['event_status'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    
    IF ($editim_konsultim == 'editim')
       {
        IF (ISSET($post_id) AND ($post_id != ""))
           {
	        $lov["id"]         = "2,3";            //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
	        $lov["field_name"] = WebApp::getVar("event_status_2_mesg").",".WebApp::getVar("event_status_3_mesg"); //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;
           }
       }
    
    $lov["object_name"]   = "event_status";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_event_status     = f_app_lov_default_values($lov);
  //id_event_source --------------------------------------------------------------------------------------------------
//LOV --------------------------------------------------------------------------------------------------------------------

//TITULLI E MBIVENDOSIM  -------------------------------------------------------------------------------------------------
  //$titull_print  = WebApp::getVar("form_alert_titull_mesg");
  $titull_print  = '{{event_mesg}}: '.HTMLSPECIALCHARS($val_rec_event['subject'][0]['vl']).'';
   
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

  //subject ----------------------------------------------------------------------------------------
  /*
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
  $Grid_form['data'][$nr]['value']            = '{{event_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_subject';
  $Grid_form['data'][$nr]['id']               = 'id_subject_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'obj_preview';
  $Grid_form['data'][$nr]['name']             = 'subject';
  $Grid_form['data'][$nr]['value']            = '{{event_mesg}}: '.HTMLSPECIALCHARS($val_rec_event['subject'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_subject';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '250';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  */
  //subject --------------------------------------------------------------------------------------------------------------

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
  $Grid_form['data'][$nr]['value']            = '{{id_branch_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_branch';
  $Grid_form['data'][$nr]['id']               = 'id_branch_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'obj_preview';
  $Grid_form['data'][$nr]['name']             = 'id_branch';
  $Grid_form['data'][$nr]['value']            = $lov_id_branch;
  $Grid_form['data'][$nr]['id']               = 'id_id_branch';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['filter']           = '';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //id_branch ------------------------------------------------------------------------------------------

  //event_status ---------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{event_status_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_event_status';
  $Grid_form['data'][$nr]['id']               = 'id_event_status_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'event_status';
  $Grid_form['data'][$nr]['value']            = $lov_event_status;
  $Grid_form['data'][$nr]['id']               = 'id_event_status';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{event_status_mesg}}"';
  $Grid_form['data'][$nr]['filter']           = 'N';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //event_status ---------------------------------------------------------------------------

  //description ----------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = '{{description_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_description';
  $Grid_form['data'][$nr]['id']               = 'id_description_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_textarea;
  $Grid_form['data'][$nr]['name']             = 'description';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['description'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_notes';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = ' rows="5" valid="0,0,0,0,0,0" etiketa="{{description_mesg}}" style="height:200px;"';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  $Grid_form['data'][$nr]['filter']           = 'N';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //description --------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //mbyllet col 1 ----------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_body_end';

  //buttons --------------------------------------------------------------------------------------------------------------
    INCLUDE(dirname(__FILE__)."/../php_script/add_edit_03_buttons_regj.php");
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

//rifreskojme parentin ---------------------------------------------------------------------------------------------------
  IF ($G_APP_VARS["parent_refresh"] == "Y")
     {
      $script_js_in_page .= '
                             parent.f_app_refresh("'.$arg_webbox.'", "'.$arg_id_form.'");
                            ';
     }
//rifreskojme parentin ---------------------------------------------------------------------------------------------------
?>