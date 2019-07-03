<?
//WebApp::addVar("max_width", "750");

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
$post_id_doc    = $post_id_arr[2];

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
  $kushti_where         = 'WHERE id_event = "'.ValidateVarFun::f_real_escape_string($post_id_event).'" AND id_branch = "'.ValidateVarFun::f_real_escape_string($post_id_branch).'" AND id_doc = "'.ValidateVarFun::f_real_escape_string($post_id_doc).'"';
  $tab['tab_name']      = 'phi_docs';            //*emri i tabeles ku do behet select
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
  
  $ins_record_user      = f_app_record_user ($val_rec['record_user'][0]['vl']);
  $ins_record_timestamp = $val_rec['record_timestamp'][0]['vlf_dt'];

  //$upd_record_user      = f_app_record_user ($val_rec['record_user_upd'][0]['vl']);
  //$upd_record_timestamp = $val_rec['record_timestamp_upd'][0]['vlf_dt'];
//kapim tabelen phi_event_branch ------------------------------------------------------------------------------
   
//rights -----------------------------------------------------------------------------------------------------------------
  INCLUDE(dirname(__FILE__)."/../php_script/add_edit_02_rights.php");
//rights -----------------------------------------------------------------------------------------------------------------

//ndryshojme funksionin e savit meqe kemi validime ekstra per kete forme -------------------------------------------------
  $but_regj_action = STR_REPLACE("f_app_save", "f_app_save_local_doc", $but_regj_action); 
  $but_del_action  = STR_REPLACE("f_app_del",  "f_app_del_local_doc",  $but_del_action); 
//------------------------------------------------------------------------------------------------------------------------

IF ($post_id_doc == 0)
   {
    $dega_disabled             = '';
    $doc_is_upload             = 'N';
    $val_rec['share'][0]['vl'] = 'N';
   }
ELSE
   {
    $dega_disabled = ' disabled';
    $doc_is_upload = 'Y';
   }

//$titull_print .= " - ".WebApp::getVar("event_dega_docs_mesg"); //e ka titullin dritarja

//DEGA REGJISTRUESE E EVENTIT --------------------------------------------------------------------------------------------
  IF ($editim_konsultim == 'editim')
     {
      IF ($post_id_doc == 0)
         {
          IF (($user_dega_qendra["edit_branch_nr"] == "1") AND !ISSET($val_rec['id_branch'][0]['vl']))
             {
              $val_rec['id_branch'][0]['vl'] = $user_dega_qendra["edit_branch_ids"];
              $post_id_branch                = $user_dega_qendra["edit_branch_ids"];
             }
          ELSE
             {
              //E BAROZOJME ME DEGEN E EVENTIT ---------------------------------------------------------------------------
              $val_rec['id_branch'][0]['vl'] = $val_rec_event['id_branch'][0]['vl'];
              $post_id_branch                = $val_rec_event['id_branch'][0]['vl'];
             }
         }
     }
//DEGA REGJISTRUESE E EVENTIT --------------------------------------------------------------------------------------------

//MABJME NE VARIABLE ATRIBUTET E DOKUMENTIT ------------------------------------------------------------------------------
  $doc_attrib["id_doc"]          = $post_id_doc;
  $doc_attrib["id_event"]        = $post_id_event;
  $doc_attrib["id_doc_category"] = 1; // 1 = kategoria event
  IF ($post_id_branch > 0)
     {
      $doc_attrib["id_branch"]   = $post_id_branch;
     }
  ELSE
     {
      $doc_attrib["id_branch"]   = -1; //perdorim degen temp 
     }
  $id_doc_ser = SERIALIZE($doc_attrib);
  $id_sel     = f_app_encrypt($id_doc_ser.'|'.$NEM_ID_SEL, DESK_KEY, DESK_IV);
//MABJME NE VARIABLE ATRIBUTET E DOKUMENTIT ------------------------------------------------------------------------------

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
    $lov["obj_or_label"]  = $obj_or_label;
    
    IF ($editim_konsultim == 'editim')
       {
        $lov["filter"]    = "WHERE id_branch IN (".$user_dega_qendra["edit_branch_ids"].") ";
       }
       
    $lov_id_branch        = f_app_lov_default_values($lov);
  //id_branch ------------------------------------------------------------------------------------------------------------

  //share ----------------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"] = "share";
    IF ($val_rec['share'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['share'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "share";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_share = f_app_lov_default_values($lov);
  //share ----------------------------------------------------------------------------------------------------------------
//LOV --------------------------------------------------------------------------------------------------------------------

//TITULLI E MBIVENDOSIM  -------------------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['type']             = 'hidden';
  $Grid_form['data'][$nr]['name']             = 'id_doc_upload';
  $Grid_form['data'][$nr]['value']            = $id_sel;
  $Grid_form['data'][$nr]['id']               = 'id_id_doc_upload';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'hidden';
  $Grid_form['data'][$nr]['name']             = 'doc_is_upload';
  $Grid_form['data'][$nr]['value']            = $doc_is_upload;
  $Grid_form['data'][$nr]['id']               = 'id_doc_is_upload';
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
  $Grid_form['data'][$nr]['width']            = 7;
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
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'id_branch';
  $Grid_form['data'][$nr]['value']            = $lov_id_branch;
  $Grid_form['data'][$nr]['id']               = 'id_id_branch';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{id_branch_mesg}}"'.$dega_disabled;
  $Grid_form['data'][$nr]['filter']           = 'Y';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  //$nr = $nr + 1;
  //$Grid_form['data'][$nr]['type']             = 'row_end';
  //id_branch ------------------------------------------------------------------------------------------

  //share ---------------------------------------------------------------------------
  //$nr = $nr + 1;
  //$Grid_form['data'][$nr]['type']             = 'row_start';
  //$Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 5;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{share_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_share';
  $Grid_form['data'][$nr]['id']               = 'id_share_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'share';
  $Grid_form['data'][$nr]['value']            = $lov_share;
  $Grid_form['data'][$nr]['id']               = 'id_share';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{share_mesg}}"';
  $Grid_form['data'][$nr]['filter']           = 'N';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //share ----------------------------------------------------------------------------------

  //description ----------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{description_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_description';
  $Grid_form['data'][$nr]['id']               = 'id_description_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'description';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['description'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_description';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '250';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{description_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //description ----------------------------------------------------------------------------------------------------------

  //docs -----------------------------------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{ngarko_dokumentin_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_ngarko_dokumentin';
  $Grid_form['data'][$nr]['id']               = 'id_ngarko_dokumentin_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 4;


  $file_name_print = '';
  IF ($post_id_doc > 0)
     {
      $Grid_form['data'][$nr]['value']        = '{{dokumenti_mesg}}:';
     
      IF ($val_rec['file_size'][0]['vl'] > 1048576)
         {
          $file_size_print = ROUND($val_rec['file_size'][0]['vl'] / 1048576, 2)." MB";
         }
      ELSE
         {
          $file_size_print = ROUND($val_rec['file_size'][0]['vl'] / 1024,    2)." KB";
         }

      $file_name_print = $val_rec['file_name'][0]['vl'].' ('.$file_size_print.')';
     }
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '<a id="id_link_doc_name" onclick="javascript:f_app_download(\'id_id_doc_upload\');">'.HTMLSPECIALCHARS($file_name_print).'</a>';
  $Grid_form['data'][$nr]['for']              = 'id_doc_name';
  $Grid_form['data'][$nr]['id']               = 'id_doc_name_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 8;

  //download doc --------------------------------------------------------------------------------------------
    IF ($post_id_doc > 0)
       {
        $Grid_form['data'][$nr]['link']              = 'Y';
        $Grid_form['data'][$nr]['link_att']          = 'href="javascript:f_app_download(\'id_id_doc_upload\');"';
        $Grid_form['data'][$nr]['link_data_modal']   = 'N';
        $Grid_form['data'][$nr]['link_data_title']   = WebApp::getVar("download_mesg");
        $Grid_form['data'][$nr]['link_data_url']     = $data_url_sel;
        $Grid_form['data'][$nr]['has_icon']          = 'Y';
        $Grid_form['data'][$nr]['icon_type']         = 'icon_download';
        //$Grid_form['data'][$nr]['link_modal_iframe'] = 'true';
        //$Grid_form['data'][$nr]['link_modal_size']   = 'modal-lg'; //link_modal_width mer perparesi
        //$Grid_form['data'][$nr]['link_modal_width']  = '800';
        //$Grid_form['data'][$nr]['link_modal_height'] = '850';
       }
  //download doc --------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'dropzone';
  $Grid_form['data'][$nr]['value']            = '';
  $Grid_form['data'][$nr]['id']               = 'id_dropzone_doc_upload';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['data_upload_url']  = $data_url_upload;
  $Grid_form['data'][$nr]['data_allow']       = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //docs -----------------------------------------------------------------------------------------------------------------


  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //mbyllet col 1 --------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_body_end';

  //buttons --------------------------------------------------------------------------------------------------------------
    //INCLUDE(dirname(__FILE__)."/../php_script/add_edit_03_buttons_regj.php");
      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'form_footer_start';
      $Grid_form['data'][$nr]['other_attributes'] = '';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'row_start';
      $Grid_form['data'][$nr]['other_attributes'] = '';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'col_start';
      $Grid_form['data'][$nr]['width']            = '6';
      $Grid_form['data'][$nr]['other_attributes'] = '';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'button';
      $Grid_form['data'][$nr]['button_type']      = 'submit';
      $Grid_form['data'][$nr]['name']             = 'but_regj';
      $Grid_form['data'][$nr]['value']            = $but_regj_label;
      $Grid_form['data'][$nr]['id']               = 'id_but_regj';
      $Grid_form['data'][$nr]['other_attributes'] = $but_regj_action.$but_regj_disabled;
      $Grid_form['data'][$nr]['primary']          = 'primary';
      $Grid_form['data'][$nr]['action_type']      = 'save';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'col_end';

      IF ($post_id_doc > 0)
         {
          $nr = $nr + 1;
          $Grid_form['data'][$nr]['type']             = 'col_start';
          $Grid_form['data'][$nr]['width']            = '6';
          $Grid_form['data'][$nr]['other_attributes'] = '';

          $nr = $nr + 1;
          $Grid_form['data'][$nr]['type']             = 'button';
          $Grid_form['data'][$nr]['button_type']      = 'button';
          $Grid_form['data'][$nr]['name']             = 'but_del';
          $Grid_form['data'][$nr]['value']            = '{{fshi_mesg}}';
          $Grid_form['data'][$nr]['id']               = 'id_but_add';
          $Grid_form['data'][$nr]['other_attributes'] = $but_del_action.$but_del_disabled.' style="float: right;"';
          $Grid_form['data'][$nr]['primary']          = 'default';
          $Grid_form['data'][$nr]['action_type']      = 'del';

          $nr = $nr + 1;
          $Grid_form['data'][$nr]['type']             = 'col_end';
         }

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'row_end';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'form_footer_end';
    
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