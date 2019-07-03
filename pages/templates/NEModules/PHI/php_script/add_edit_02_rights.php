<?
//AKSIONI INS/UPD NE DB KA DESHTUAR KESHTU QE MUNDOHEMI TE NXJERIM EDHE NJEHERE VLERAT QE KA MBUSHUR USERI ---------------
  IF (ISSET($G_APP_VARS["kol_val_post"]) AND IS_ARRAY($G_APP_VARS["kol_val_post"]))
     {
      RESET($G_APP_VARS["kol_val_post"]);
      WHILE (LIST($key, $val) = EACH($G_APP_VARS["kol_val_post"])) 
            {
             IF (PREG_MATCH('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/i', $val))
                {
                 //KEMI TE BEJME ME DATE NE FORMATIN MYSQL E KONVERTOJME
                 $val_rec[$key][0]['vlf'] = SUBSTR($val, 8, 2).".".SUBSTR($val, 5, 2).".".SUBSTR($val, 0, 4); 
                }
             
             $val_rec[$key][0]['vl'] = $val;
            }
     }
//------------------------------------------------------------------------------------------------------------------------

//gjenerojme edhe njehere vars_page ku tashme kemi edhe id qe po modifikojme ---------------------------------------------
  $vars_page_kol[] = "id_pk";
  $vars_page_val[] = $post_id;
  $vars_page       = f_app_vars_page_encrypt($vars_page_kol, $vars_page_val);
//gjenerojme edhe njehere vars_page ku tashme kemi edhe id qe po modifikojme ---------------------------------------------

$yll          = '';
$obj_or_label = 'label';
$titull_print = '';

//variablat e editimit ---------------------------------------------------------------------------------------------------
  IF ($editim_konsultim == 'editim')
     {
      $yll                  = '*';
      $obj_or_label         = 'object';

      $titull_print         = '{{regjistrim_i_ri_mesg}}';
      $but_regj_label       = '{{add_mesg}}';

      $but_regj_disabled    = ' disabled';
      $but_regj_action      = ' onclick="javascript:void(0);"';

      $but_add_disabled     = ' disabled';
      $but_add_action       = ' onclick="javascript:void(0);"';
      
      $but_del_disabled     = ' disabled';
      $but_del_action       = ' onclick="javascript:void(0);"';

      $but_back_action      = ' onclick="javascript:f_app_back(\''.$arg_webbox.'\', \'back\', \''.$arg_id_form.'\');"'; //aksioni back

      IF ($post_id != "")
         {
          $titull_print     = '{{perditesim_mesg}}';
          $but_regj_label   = '{{update_mesg}}';

          //shto rekord te ri --------------------------------------------------------------------------------------------
            IF (ISSET($nem_rights[$NEM_ID_SEL]["101"]) AND ($nem_rights[$NEM_ID_SEL]["101"] != ""))
               {
                $id_add           = f_app_encrypt('|'.$NEM_ID_SEL, DESK_KEY, DESK_IV);
                $but_add_disabled = '';
                $but_add_action   = ' onclick="javascript:f_app_add_edit(\''.$arg_webbox.'\', \'add_edit\', \''.$arg_id_form.'\', \''.$id_add.'\');"'; //aksioni add/edit
               }
          //--------------------------------------------------------------------------------------------------------------

          IF (ISSET($nem_rights[$NEM_ID_SEL]["102"]) AND ($nem_rights[$NEM_ID_SEL]["102"] != ""))
             {
              $but_regj_disabled  = '';
              $but_regj_action    = ' onclick="javascript:f_app_save(\''.$arg_webbox.'\', \'save\', \''.$arg_id_form.'\');"'; //ka te drejte te modifikoje
             }

          IF (ISSET($nem_rights[$NEM_ID_SEL]["103"]) AND ($nem_rights[$NEM_ID_SEL]["103"] != ""))
             {
              $but_del_disabled = '';
              $but_del_action   = ' onclick="javascript:f_app_del(\''.$arg_webbox.'\', \'del\', \''.$arg_id_form.'\');"'; //ka te drejte te fshije
             }
         }
      ELSE
         {
          IF (ISSET($nem_rights[$NEM_ID_SEL]["101"]) AND ($nem_rights[$NEM_ID_SEL]["101"] != ""))
             {
              $but_regj_disabled = '';
              $but_regj_action   = ' onclick="javascript:f_app_save(\''.$arg_webbox.'\', \'save\', \''.$arg_id_form.'\');"'; //ka te drejte te modifikoje
             }
         }
     }
  ELSE
     {
      $titull_print = $content_title;
     }
//variablat e editimit ---------------------------------------------------------------------------------------------------

//tipet e objekteve ------------------------------------------------------------------------------------------------------
  IF ($obj_or_label == 'label')
     {
      $obj_type_text            = 'obj_preview';
      $obj_type_select          = 'obj_preview';
      $obj_type_select_multiple = 'obj_preview';
      $obj_type_textarea        = 'obj_preview';
      $obj_type_date            = 'obj_preview';
      $obj_type_radio           = 'obj_preview';
     }
  ELSE
     {
      $obj_type_text            = 'text';
      $obj_type_select          = 'select';
      $obj_type_select_multiple = 'select-multiple';
      $obj_type_textarea        = 'textarea';
      $obj_type_date            = 'date';
      $obj_type_radio           = 'radio';
     }
//tipet e objekteve ------------------------------------------------------------------------------------------------------

?>