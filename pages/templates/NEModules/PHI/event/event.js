function f_app_save_local(arg_webbox, arg_event, arg_id_form) 
  {
   if (document.getElementById(arg_id_form))   
      {
       var form      = document.getElementById(arg_id_form);
       var vars_page = form.vars_page.value;

       var kol_val   = valid_value(arg_id_form);
  
       if (kol_val != "")
          {
           //verifikojme datat -------------------------------------------------------------------------------------------


           //verifikojme datat -------------------------------------------------------------------------------------------
           
           
           GoTo("thisPage?event="+arg_webbox+"."+arg_event+"(var_post="+kol_val+";vars_page="+vars_page+")");
          }
      }
 }

function f_app_save_local_info(arg_webbox, arg_event, arg_id_form) 
  {
   arg_event = "save_info"; //arg_event default vin save prandaj e ndryshojme
   
   if (document.getElementById(arg_id_form))   
      {
       var form      = document.getElementById(arg_id_form);
       var vars_page = form.vars_page.value;

       var kol_val   = valid_value(arg_id_form);
  
       if (kol_val != "")
          {
           GoTo("thisPage?event="+arg_webbox+"."+arg_event+"(var_post="+kol_val+";vars_page="+vars_page+")");
          }
      }
 }

function f_app_refresh(arg_webbox, arg_id_form) 
  {
   if (document.getElementById(arg_id_form))   
      {
       var form      = document.getElementById(arg_id_form);
       var vars_page = form.vars_page.value;

       GoTo("thisPage?event="+arg_webbox+".refresh(vars_page="+vars_page+")");
      }
 }

function f_app_save_local_doc(arg_webbox, arg_event, arg_id_form) 
  {
   arg_event = "save_doc"; //arg_event default vin save prandaj e ndryshojme
   
   var var_id_doc_is_upload = document.getElementById('id_doc_is_upload').value;
   
   if (var_id_doc_is_upload != 'Y')
      {
       alert(alert_upload_doc_mesg);
       return;
      }
   
   if (document.getElementById(arg_id_form))   
      {
       var form      = document.getElementById(arg_id_form);
       var vars_page = form.vars_page.value;

       var kol_val   = valid_value(arg_id_form);
  
       var id_doc_upload_vl = document.getElementById('id_id_doc_upload').value;

       if (kol_val != "")
          {
           GoTo("thisPage?event="+arg_webbox+"."+arg_event+"(id_doc_upload="+id_doc_upload_vl+";var_post="+kol_val+";vars_page="+vars_page+")");
          }
      }
 }

function f_app_del_local_doc(arg_webbox, arg_event, arg_id_form) 
  {
   arg_event = "del_doc"; //arg_event default vin save prandaj e ndryshojme

   if (document.getElementById(arg_id_form))   
      {
       var form      = document.getElementById(arg_id_form);
       var vars_page = form.vars_page.value;

       var aprovim   = confirm(alert_confirm_del_mesg);
  
       if (aprovim)
          {
           GoTo("thisPage?event="+arg_webbox+"."+arg_event+"(vars_page="+vars_page+")");
          }
      }
 }

function f_app_download(arg_id_doc) 
  {
   if (document.getElementById('id_id_doc_upload'))
      {
       var doc_is_upload = document.getElementById('id_doc_is_upload').value;
       
       if (doc_is_upload == 'Y')
          {
           var vl_doc_upload    = document.getElementById('id_id_doc_upload').value;
           var var_url_download = url_download + '&id_sel=' + vl_doc_upload;
           window.location.assign(var_url_download);
          }
       else
          {
           alert(alert_ngarko_dokumentin_mesg);
          }
       
      }
 }

  