<?
//size per etiketat dhe objektet e formes --------
  $width_form = 6;
  $width_lab  = 3;
  IF ($width_lab == 12)
     {
      $width_obj = 12; 
     }
  ELSE
     {
      $width_obj = 12 - $width_lab; 
     }
//size per etiketat dhe objektet e formes --------

$arg_id_form = "id_skeda";
$arg_webbox  = $WEBBOX_SEL;

IF (ISSET($G_APP_VARS["id_sel"]) AND ($G_APP_VARS["id_sel"] != ""))
   {
    //KEMI ARRDHUR NGA NGJARJA ADD/Modify DHE ID ESHTE E PA ENKRYPTUAR
    $id_sel_nem_id = $G_APP_VARS["id_sel"];
   }
ELSE
   {
    $id_sel_nem_id = f_app_decrypt($G_APP_VARS["post_id"], DESK_KEY, DESK_IV);
   }
   
$id_sel_nem_id_arr = EXPLODE("|", $id_sel_nem_id);
$post_id           = $id_sel_nem_id_arr[0];
$post_id_nem       = $id_sel_nem_id_arr[1];

//validojme nese kjo id i perket ketij nemi ------------------------------------------------------------------------------
  IF ($post_id_nem != $NEM_ID_SEL)
     {
      $post_id = ""; //RETURN;
     }
//validojme nese kjo id i perket ketij nemi ------------------------------------------------------------------------------
?>