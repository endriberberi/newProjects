function f_change_cases(arg_id) 
  {
   var form = document.skeda;

   var var_total_cases = 0;
   
   for(var i=0; i<form.elements.length; i++) 
      {
       if (form.elements[i].type == "text")
          {
           
           if (form.elements[i].getAttribute("id_elm_total") == arg_id)
              {
               var var_cases_vl = parseInt(form.elements[i].value);
               if (var_cases_vl > 0)
                  {
                   var_total_cases = var_total_cases + var_cases_vl;
                  }
              }
          }
      }
  
   if (document.getElementById(arg_id))
      {
       document.getElementById(arg_id).value = var_total_cases;
      }
  }

function f_change_week_date_start(arg_id) 
  {
   var var_arg_id_array  = arg_id.split('.'); 
   var var_arg_id_dt     = var_arg_id_array[2] + '-' + var_arg_id_array[1] + '-' + var_arg_id_array[0];
   
   var someDate          = new Date(var_arg_id_dt);
   var numberOfDaysToAdd = 6;

   someDate.setDate(someDate.getDate() + numberOfDaysToAdd);

   var dd = someDate.getDate();
   var mm = someDate.getMonth() + 1;
   var y  = someDate.getFullYear();

   if (isNaN(dd) || isNaN(mm) || isNaN(y))
      {
       var someFormattedDate = '';
       var var_form_number   = '';
      }
   else
      {
       if (dd < 10)
          {
           dd = '0' + dd;
          } 
   
       if (mm < 10)
          {
           mm = '0' + mm;
          }

       var someFormattedDate = dd + '.'+ mm + '.' + y;
      
       var form_number_dt    = new Date(var_arg_id_dt);
       var var_form_number   = ISO8601_week_no(form_number_dt);
      }

   if (document.getElementById('id_week_date_end'))
      {
       document.getElementById('id_week_date_end').value = someFormattedDate;
      }

   if (document.getElementById('id_form_number'))
      {
       document.getElementById('id_form_number').value = var_form_number;
      }
  }

function ISO8601_week_no(dt) 
  {
     var tdt  = new Date(dt.valueOf());
     var dayn = (dt.getDay() + 6) % 7;
     
     tdt.setDate(tdt.getDate() - dayn + 3);
     
     var firstThursday = tdt.valueOf();
     
     tdt.setMonth(0, 1);
     
     if (tdt.getDay() !== 4) 
        {
         tdt.setMonth(0, 1 + ((4 - tdt.getDay()) + 7) % 7);
        }
     
     return 1 + Math.ceil((firstThursday - tdt) / 604800000);
  }

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
             //id_date_receipt_from_reporting_entity ---------------------------------------------------------------------
               var var_elm_sel  = document.getElementById('id_date_receipt_from_reporting_entity');
               var var_date_sel = var_elm_sel.value;
               var com          = compared_date(var_date_sel, data_sot);
    
               if (com == "1")
                  {
                   var var_alert = date_receipt_from_reporting_entity_mesg + ' ' + jo_me_e_madhe_se_sot_mesg;
                   alert(var_alert);
                   var_elm_sel.focus();
                   return;
                  }
             //id_date_receipt_from_reporting_entity ---------------------------------------------------------------------

             //id_date_receipt_in_center ---------------------------------------------------------------------------------
               var var_elm_sel  = document.getElementById('id_date_receipt_in_center');
               var var_date_sel = var_elm_sel.value;
               var com          = compared_date(var_date_sel, data_sot);
    
               if (com == "1")
                  {
                   var var_alert = date_receipt_in_center_mesg + ' ' + jo_me_e_madhe_se_sot_mesg;
                   alert(var_alert);
                   var_elm_sel.focus();
                   return;
                  }
             //id_date_receipt_in_center ---------------------------------------------------------------------------------
           
             //id_week_date_start ----------------------------------------------------------------------------------------
               var var_elm_sel  = document.getElementById('id_week_date_start');
               var var_date_sel = var_elm_sel.value;
               var com          = compared_date(var_date_sel, data_sot);
    
               if (com == "1")
                  {
                   var var_alert = week_date_start_mesg + ' ' + jo_me_e_madhe_se_sot_mesg;
                   alert(var_alert);
                   var_elm_sel.focus();
                   return;
                  }
             //id_week_date_start ----------------------------------------------------------------------------------------

             //verifikojme nese week_date_start eshte e hene -------------------------------------------------------------
               var var_week_date_start     = document.getElementById('id_week_date_start').value;
               var var_week_date_start_arr = var_week_date_start.split('.'); 
               var var_week_date_start_dt  = var_week_date_start_arr[2] + '-' + var_week_date_start_arr[1] + '-' + var_week_date_start_arr[0];
               var var_week_date_startDate = new Date(var_week_date_start_dt);
               var var_day                 = var_week_date_startDate.getDay(); //getDay()	Get the weekday as a number (0-6)
             
               if (var_day != 1)
                  {
                   alert(dita_nuk_eshte_e_hene_mesg);
                   document.getElementById('id_week_date_start').focus();
                   return;
                  }
             //verifikojme nese week_date_start eshte e hene -------------------------------------------------------------

             //id_week_date_end ------------------------------------------------------------------------------------------
               var var_elm_sel  = document.getElementById('id_week_date_end');
               var var_date_sel = var_elm_sel.value;
               var com          = compared_date(var_date_sel, data_sot);
    
               if (com == "1")
                  {
                   var var_alert = week_date_end_mesg + ' ' + jo_me_e_madhe_se_sot_mesg;
                   alert(var_alert);
                   document.getElementById('id_week_date_start').focus();
                   return;
                  }
             //id_week_date_end ------------------------------------------------------------------------------------------
           
             //id_date_receipt_from_reporting_entity id_week_date_end ----------------------------------------------------
               var var_dt1  = document.getElementById('id_date_receipt_from_reporting_entity').value;
               var var_dt2  = document.getElementById('id_week_date_end').value;
               var com      = compared_date(var_dt1, var_dt2);
    
               if (com == "2")
                  {
                   var var_alert = date_receipt_from_reporting_entity_mesg + ' ' + jo_me_e_vogel_se_mesg + ' ' + week_date_end_mesg;
                   alert(var_alert);
                   document.getElementById('id_date_receipt_from_reporting_entity').focus();
                   return;
                  }
             //id_date_receipt_from_reporting_entity id_week_date_end ----------------------------------------------------
             
             //id_date_receipt_in_center id_week_date_end ----------------------------------------------------------------
               var var_dt1  = document.getElementById('id_date_receipt_in_center').value;
               var var_dt2  = document.getElementById('id_week_date_end').value;
               var com      = compared_date(var_dt1, var_dt2);
    
               if (com == "2")
                  {
                   var var_alert = date_receipt_in_center_mesg + ' ' + jo_me_e_vogel_se_mesg + ' ' + week_date_end_mesg;
                   alert(var_alert);
                   document.getElementById('id_date_receipt_in_center').focus();
                   return;
                  }
             //id_date_receipt_in_center id_week_date_end ----------------------------------------------------------------
           
             //id_date_receipt_from_reporting_entity id_date_receipt_in_center -------------------------------------------
               var var_dt1  = document.getElementById('id_date_receipt_from_reporting_entity').value;
               var var_dt2  = document.getElementById('id_date_receipt_in_center').value;
               var com      = compared_date(var_dt1, var_dt2);
    
               if (com == "1")
                  {
                   var var_alert = date_receipt_from_reporting_entity_mesg + ' ' + jo_me_e_madhe_se_mesg + ' ' + date_receipt_in_center_mesg;
                   alert(var_alert);
                   document.getElementById('id_date_receipt_from_reporting_entity').focus();
                   return;
                  }
             //id_date_receipt_from_reporting_entity id_date_receipt_in_center -------------------------------------------
             
             //id_total_number_doctors id_number_doctors_reported --------------------------------------------------------
               var var_total_number_doctors    = parseInt(document.getElementById('id_total_number_doctors').value);
               var var_number_doctors_reported = parseInt(document.getElementById('id_number_doctors_reported').value);
               
               if (var_number_doctors_reported > var_total_number_doctors)
                  {
                   var var_alert = number_doctors_reported_mesg + ' ' + jo_me_e_madhe_se_mesg + ' ' + total_number_doctors_mesg;
                   alert(var_alert);
                   document.getElementById('id_number_doctors_reported').focus();
                   return;
                  }
             //id_total_number_doctors id_number_doctors_reported --------------------------------------------------------

           //verifikojme datat -------------------------------------------------------------------------------------------
           
           
           GoTo("thisPage?event="+arg_webbox+"."+arg_event+"(var_post="+kol_val+";vars_page="+vars_page+")");
          }
      }
 }

  