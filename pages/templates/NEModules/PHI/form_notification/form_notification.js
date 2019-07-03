function f_app_save_local(arg_webbox, arg_event, arg_id_form) 
  {
   if (document.getElementById(arg_id_form))   
      {
       var form      = document.getElementById(arg_id_form);
       var vars_page = form.vars_page.value;

       var kol_val   = valid_value(arg_id_form);
  
       if (kol_val != "")
          {
           //verifikojme datat ---------------------------------------------------------------------------
             //id_date_of_completion ---------------------------------------------------------------------
               var var_elm_sel  = document.getElementById('id_date_of_completion');
               var var_date_sel = var_elm_sel.value;
               var com          = compared_date(var_date_sel, data_sot);
    
               if (com == "1")
                  {
                   var var_alert = date_receipt_from_reporting_entity_mesg + ' ' + jo_me_e_madhe_se_sot_mesg;
                   alert(var_alert);
                   var_elm_sel.focus();
                   return;
                  }
             //id_date_of_completion ---------------------------------------------------------------------

             //id_person_birthday id_date_of_completion --------------------------------------------------
               var var_dt1  = document.getElementById('id_person_birthday').value;
               var var_dt2  = document.getElementById('id_date_of_completion').value;
               var com      = compared_date(var_dt1, var_dt2);
    
               if (com == "1")
                  {
                   var var_alert = person_birthday_mesg + ' ' + jo_me_e_madhe_se_mesg + ' ' + date_of_completion_mesg;
                   alert(var_alert);
                   document.getElementById('id_person_birthday').focus();
                   return;
                  }
             //id_person_birthday id_date_of_completion --------------------------------------------------

             //id_date_clinical_signs id_date_of_completion --------------------------------------------------
               var var_dt1  = document.getElementById('id_date_clinical_signs').value;
               var var_dt2  = document.getElementById('id_date_of_completion').value;
               var com      = compared_date(var_dt1, var_dt2);
    
               if (com == "1")
                  {
                   var var_alert = date_clinical_signs_mesg + ' ' + jo_me_e_madhe_se_mesg + ' ' + date_of_completion_mesg;
                   alert(var_alert);
                   document.getElementById('id_date_clinical_signs').focus();
                   return;
                  }
             //id_date_clinical_signs id_date_of_completion --------------------------------------------------

             //id_date_of_visit id_date_of_completion --------------------------------------------------
               var var_dt1  = document.getElementById('id_date_of_visit').value;
               var var_dt2  = document.getElementById('id_date_of_completion').value;
               var com      = compared_date(var_dt1, var_dt2);
    
               if (com == "1")
                  {
                   var var_alert = date_of_visit_mesg + ' ' + jo_me_e_madhe_se_mesg + ' ' + date_of_completion_mesg;
                   alert(var_alert);
                   document.getElementById('id_date_of_visit').focus();
                   return;
                  }
             //id_date_of_visit id_date_of_completion --------------------------------------------------

             //id_date_hospitalization id_date_of_completion --------------------------------------------------
               var var_dt1  = document.getElementById('id_date_hospitalization').value;
               var var_dt2  = document.getElementById('id_date_of_completion').value;
               var com      = compared_date(var_dt1, var_dt2);
    
               if (com == "1")
                  {
                   var var_alert = date_hospitalization_mesg + ' ' + jo_me_e_madhe_se_mesg + ' ' + date_of_completion_mesg;
                   alert(var_alert);
                   document.getElementById('id_date_hospitalization').focus();
                   return;
                  }
             //id_date_hospitalization id_date_of_completion --------------------------------------------------

             //id_dead_date id_date_of_completion --------------------------------------------------
               var var_dt1  = document.getElementById('id_dead_date').value;
               var var_dt2  = document.getElementById('id_date_of_completion').value;
               var com      = compared_date(var_dt1, var_dt2);
    
               if (com == "1")
                  {
                   var var_alert = dead_date_mesg + ' ' + jo_me_e_madhe_se_mesg + ' ' + date_of_completion_mesg;
                   alert(var_alert);
                   document.getElementById('id_dead_date').focus();
                   return;
                  }
             //id_dead_date id_date_of_completion --------------------------------------------------

           //verifikojme datat ---------------------------------------------------------------------------

           GoTo("thisPage?event="+arg_webbox+"."+arg_event+"(var_post="+kol_val+";vars_page="+vars_page+")");
          }
      }
 }


function f_add_lab() 
{
var table = document.getElementById('id_tab_lab');

tr_nr  = tr_nr + 1;
var tr = document.createElement("tr");
tr.setAttribute('id', 'id_tab_lab_tr_'+tr_nr);

tab_td = new Array();
inp_el = new Array();

i     = -1;
el_el = new Array();
el_at = new Array();
el_vl = new Array();


//removetr_id ------------------------------------------------------------------------------------------------------------------
i = i + 1;
el_el[i] = "img";
el_at[i] = new Array();
el_vl[i] = new Array();

j = 0;
el_at[i][j] = "onclick";
el_vl[i][j] = "javascript:removetr_id('id_tab_lab_tr_"+tr_nr+"');";

j = j + 1;
el_at[i][j] = "src";
el_vl[i][j] = var_url_graphics+"del.gif";

j = j + 1;
el_at[i][j] = "border";
el_vl[i][j] = "0";

j = j + 1;
el_at[i][j] = "title";
el_vl[i][j] = fshi_mesg;
//-----------------------------------------------------------------------------------------------------------------------------------

//no_registry ------------------------------------------------------------------------------------------------------------------
i = i + 1;
el_el[i] = "input";
el_at[i] = new Array();
el_vl[i] = new Array();

j = 0;
el_at[i][j] = "id";
el_vl[i][j] = "id_lab_no_registry"+tr_nr;

j = j + 1;
el_at[i][j] = "name";
el_vl[i][j] = "lab_no_registry";

j = j + 1;
el_at[i][j] = "type";
el_vl[i][j] = "text";

j = j + 1;
el_at[i][j] = "value";
el_vl[i][j] = "";

j = j + 1;
el_at[i][j] = "class";
el_vl[i][j] = elm_class_name;

j = j + 1;
el_at[i][j] = "maxlength";
el_vl[i][j] = "30";

j = j + 1;
el_at[i][j] = "valid";
el_vl[i][j] = "1,0,0,0,0,0";

j = j + 1;
el_at[i][j] = "etiketa";
el_vl[i][j] = nr_regjistrit_mesg;
//-----------------------------------------------------------------------------------------------------------------------------------

//date_of_sampling ------------------------------------------------------------------------------------------------------------------
i = i + 1;
el_el[i] = "input";
el_at[i] = new Array();
el_vl[i] = new Array();

j = 0;
el_at[i][j] = "id";
el_vl[i][j] = "id_lab_date_of_sampling"+tr_nr;

j = j + 1;
el_at[i][j] = "name";
el_vl[i][j] = "lab_date_of_sampling";

j = j + 1;
el_at[i][j] = "type";
el_vl[i][j] = "text";

j = j + 1;
el_at[i][j] = "value";
el_vl[i][j] = "";

j = j + 1;
el_at[i][j] = "class";
el_vl[i][j] = elm_class_name+" datepicker daterange-basic";

j = j + 1;
el_at[i][j] = "maxlength";
el_vl[i][j] = "10";

j = j + 1;
el_at[i][j] = "valid";
el_vl[i][j] = "1,0,0,1,0,0";

j = j + 1;
el_at[i][j] = "etiketa";
el_vl[i][j] = dt_mostres_mesg;
//-----------------------------------------------------------------------------------------------------------------------------------

//id_type_of_sample -----------------------------------------------------------------------------------------------------------------
i = i + 1;
el_el[i] = "select";
el_at[i] = new Array();
el_vl[i] = new Array();

j = 0;
el_at[i][j] = "id";
el_vl[i][j] = "id_lab_id_type_of_sample"+tr_nr;

j = j + 1;
el_at[i][j] = "name";
el_vl[i][j] = "lab_id_type_of_sample";

j = j + 1;
el_at[i][j] = "class";
el_vl[i][j] = elm_class_name;

j = j + 1;
el_at[i][j] = "valid";
el_vl[i][j] = "1,0,0,0,0,0";

j = j + 1;
el_at[i][j] = "etiketa";
el_vl[i][j] = lloji_mostres_mesg;

j = j + 1;
el_at[i][j] = "js_data_array";
el_vl[i][j] = "id_type_of_sample";
//-----------------------------------------------------------------------------------------------------------------------------------

//id_analysis -----------------------------------------------------------------------------------------------------------------
i = i + 1;
el_el[i] = "select";
el_at[i] = new Array();
el_vl[i] = new Array();

j = 0;
el_at[i][j] = "id";
el_vl[i][j] = "id_lab_id_analysis"+tr_nr;

j = j + 1;
el_at[i][j] = "name";
el_vl[i][j] = "lab_id_analysis";

j = j + 1;
el_at[i][j] = "class";
el_vl[i][j] = elm_class_name;

j = j + 1;
el_at[i][j] = "valid";
el_vl[i][j] = "1,0,0,0,0,0";

j = j + 1;
el_at[i][j] = "etiketa";
el_vl[i][j] = testi_mesg;

j = j + 1;
el_at[i][j] = "js_data_array";
el_vl[i][j] = "id_analysis";
//-----------------------------------------------------------------------------------------------------------------------------------

//id_analysis_result -----------------------------------------------------------------------------------------------------------------
i = i + 1;
el_el[i] = "select";
el_at[i] = new Array();
el_vl[i] = new Array();

j = 0;
el_at[i][j] = "id";
el_vl[i][j] = "id_lab_id_analysis_result"+tr_nr;

j = j + 1;
el_at[i][j] = "name";
el_vl[i][j] = "lab_id_analysis_result";

j = j + 1;
el_at[i][j] = "class";
el_vl[i][j] = elm_class_name;

j = j + 1;
el_at[i][j] = "valid";
el_vl[i][j] = "1,0,0,0,0,0";

j = j + 1;
el_at[i][j] = "etiketa";
el_vl[i][j] = rezultati_mesg;

j = j + 1;
el_at[i][j] = "js_data_array";
el_vl[i][j] = "id_analysis_result";
//-----------------------------------------------------------------------------------------------------------------------------------

//date_of_result ------------------------------------------------------------------------------------------------------------------
i = i + 1;
el_el[i] = "input";
el_at[i] = new Array();
el_vl[i] = new Array();

j = 0;
el_at[i][j] = "id";
el_vl[i][j] = "id_lab_date_of_result"+tr_nr;

j = j + 1;
el_at[i][j] = "name";
el_vl[i][j] = "lab_date_of_result";

j = j + 1;
el_at[i][j] = "type";
el_vl[i][j] = "text";

j = j + 1;
el_at[i][j] = "value";
el_vl[i][j] = "";

j = j + 1;
el_at[i][j] = "class";
el_vl[i][j] = elm_class_name+" datepicker daterange-basic";

j = j + 1;
el_at[i][j] = "maxlength";
el_vl[i][j] = "10";

j = j + 1;
el_at[i][j] = "valid";
el_vl[i][j] = "1,0,0,1,0,0";

j = j + 1;
el_at[i][j] = "etiketa";
el_vl[i][j] = data_e_rezultatit_mesg;
//-----------------------------------------------------------------------------------------------------------------------------------

//id_reporting_entity_laboratory -----------------------------------------------------------------------------------------------------------------
i = i + 1;
el_el[i] = "select";
el_at[i] = new Array();
el_vl[i] = new Array();

j = 0;
el_at[i][j] = "id";
el_vl[i][j] = "id_lab_id_reporting_entity_laboratory"+tr_nr;

j = j + 1;
el_at[i][j] = "name";
el_vl[i][j] = "lab_id_reporting_entity_laboratory";

j = j + 1;
el_at[i][j] = "class";
el_vl[i][j] = elm_class_name + " " + elm_class_filter;

j = j + 1;
el_at[i][j] = "data-placeholder";
el_vl[i][j] = "";

j = j + 1;
el_at[i][j] = "valid";
el_vl[i][j] = "1,0,0,0,0,0";

j = j + 1;
el_at[i][j] = "etiketa";
el_vl[i][j] = laboratori_mesg;

j = j + 1;
el_at[i][j] = "js_data_array";
el_vl[i][j] = "id_reporting_entity_laboratory";
//-----------------------------------------------------------------------------------------------------------------------------------

 for (var i=0; i<el_el.length; i++)
     {
      tab_td[i] = document.createElement("td");
      //tab_td[i].setAttribute("class",     obj_cl[i]);
      //tab_td[i].setAttribute("className", obj_cl[i]);
      
      inp_el[i] = document.createElement(el_el[i]);
      for (var j=0; j<el_at[i].length; j++)
          {
           if ((el_at[i][j] == "onblur") || (el_at[i][j] == "onfocus") || (el_at[i][j] == "onclick") || (el_at[i][j] == "onchange"))
              {
               if (el_at[i][j] == "onblur")
                  {inp_el[i].onblur = new Function(el_vl[i][j]);}

               if (el_at[i][j] == "onfocus")
                  {inp_el[i].onfocus = new Function(el_vl[i][j]);}

               if (el_at[i][j] == "onclick")
                  {inp_el[i].onclick = new Function(el_vl[i][j]);}
                  
               if (el_at[i][j] == "onchange")
                  {inp_el[i].onchange = new Function(el_vl[i][j]);}
              }
           else
              {
               inp_el[i].setAttribute(el_at[i][j], el_vl[i][j]);
              }
          }
     }

 table.appendChild(tr);
 for (var i=0; i<el_el.length; i++)
 //for (var i=0; i<7; i++)
     {
      tr.appendChild(tab_td[i]);
      
      if ((i==2) || (i==6))
         {
          //fusha te tipit date 
          var_el = document.createElement("label");
          var_el.setAttribute("class", "input");

          var_el_ico = document.createElement("i");
          var_el_ico.setAttribute("class", "icon-prepend icon-calendar22");

          tab_td[i].appendChild(var_el);
          var_el.appendChild(var_el_ico);
          var_el.appendChild(inp_el[i]);
         }
      else if (i==7)
         {
          //fusha te tipit date 
          var_el = document.createElement("label");
          var_el.setAttribute("class", "select");

          tab_td[i].appendChild(var_el);
          var_el.appendChild(inp_el[i]);
         }
      else
         {
          tab_td[i].appendChild(inp_el[i]);
         }
      
      /*
      if (obj[i] == "hidden")
         {
          var tdText = document.createTextNode('\u00a0');
          tab_td[i].appendChild(tdText);
         }
      */
     }

//PER POPULLIMIN E ELEMENTEVE SELECT -------------------------------------------
 for (var i=0; i<el_el.length; i++)
     {
      if (el_el[i] == "select")
         {
          elm_id_sel   = "";

          for (var j=0; j<el_at[i].length; j++)
              {
               if (el_at[i][j] == "id")
                  {
                   elm_id_sel = el_vl[i][j];
                  }
              }
      
          //vetem kur kemi ListBox popullojme vlerat ---------------------------
            f_app_listbox_elements(elm_id_sel);
         //vetem kur kemi ListBox popullojme vlerat ----------------------------
        }
    }
//PER POPULLIMIN E ELEMENTEVE SELECT -------------------------------------------

//nese kemi shtuar elemente select qe kane nevoje per filtrim ------------------
  if ((elm_class_filter) && (elm_class_filter != ""))
     {
      modules.push(elm_class_filter, 'datepicker');
     }
// -----------------------------------------------------------------------------
}  

//fshi rreshtin --------------------------------------------------------------------------
  function removetr_id(tr_id)
   {
    var aprovim = confirm(del_row_confirm_mesg);
    if (aprovim)
       {
        var tr = document.getElementById(tr_id);
        tr.parentNode.removeChild(tr);
       }
   }

//fshi rreshtin --------------------------------------------------------------------------

