function f_app_save_local(arg_webbox, arg_event, arg_id_form) 
  {
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
