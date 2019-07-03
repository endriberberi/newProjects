// isAlpha -------------------------------------------------------------------------------
  function isAlpha(theElement, theElementName)
  {
    var s = theElement.value;
    var filter=/^[a-zA-Z]{1,}$/;
    if (s.length == 0 ) return true;
    if (filter.test(s))  
         return true;
    else  
           alert(alert_isalpha_mesg);
      theElement.focus(); 
      return false;
  }
//----------------------------------------------------------------------------------------


// isDate --------------------------------------------------------------------------------
  function isDate(theElement, theElementName)
  {
   var DayArray =new Array(31,28,31,30,31,30,31,31,30,31,30,31);
   //var MonthArray = new Array("JAN","FEB","MAR","APR","MAY","JUN","JUL","AUG","SEP","OCT","NOV","DEC");
   var MonthArray = new Array("01","02","03","04","05","06","07","08","09","10","11","12");
   var thisYear = null;
   var thisMon = null;
   var thisDay = null;
   var today = null;
   inpDate = theElement.value;
    if (inpDate.length == 0 ) return true;
   thisDay = inpDate.substr(0,2);
   //thisMonth = inpDate.substr(3,3).toUpperCase();
   thisMonth = inpDate.substr(3,2).toUpperCase();
   //thisYear = inpDate.substr(7,2);
   thisYear = inpDate.substr(6,4);
   //var filter=/^[0-9]{2}-[a-zA-Z]{3}-[0-9]{2}$/;
   if (inpDate.substr(2,1) != "." || inpDate.substr(5,1) != ".")
      { alert(alert_isdate1_mesg); 
         theElement.focus(); 
         return false; 
      } 
   
   var filter=/^[0-9]{2}.[0-9]{2}.[0-9]{4}$/;
    if (! filter.test(inpDate)) 
    {   //alert("Please enter Date in DD-MON-YY Format !"); 
        alert(alert_isdate1_mesg); 
         theElement.focus(); 
         return false; 
    } 
    //var filter=/JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC/ ;
    var filter=/01|02|03|04|05|06|07|08|09|10|11|12/ ;
    if (! filter.test(thisMonth))
    {
       alert(alert_isdate2_mesg);
       theElement.focus(); 
       return false;
    }
    N=Number(thisYear);
    if ( ( N%4==0 && N%100 !=0 ) || ( N%400==0 ) ) 
    {
      DayArray[1]=29;   
    }
    for(var ctr=0; ctr<=11; ctr++)
    {
     if (MonthArray[ctr]==thisMonth)
     {
        if (thisDay<= DayArray[ctr] && thisDay >0 )
             return true;
         else
         {
             alert(alert_isdate3_mesg); 
             theElement.focus(); 
             return false; 
         }
      }
     }
  }
//----------------------------------------------------------------------------------------

//krahasim i dy datave -------------------------------------------------------------------
  //1. argumentat e tipit date duhet te vijne ne formatin dd.mm.yyyy
  //RETURN:
  	//0 	ne rast se njeri prej argumentave eshte bosh
    //1		ne rsat se arg_dt1 > arg_dt2
    //= 	ne rast se arg_dt1 = arg_dt2
    //2		ne rast se arg_dt1 < arg_dt2
  
  function compared_date(arg_dt1, arg_dt2)
    {
     if ((arg_dt1 == "") || (arg_dt2 == ""))
        {return "0";}
    
     var dt1_array = arg_dt1.split("."); 
     var dt1       = dt1_array[2]+dt1_array[1]+dt1_array[0];
    
     var dt2_array = arg_dt2.split("."); 
     var dt2       = dt2_array[2]+dt2_array[1]+dt2_array[0];
     
     if (parseInt(dt1) > parseInt(dt2))
        {return "1";}
     
     if (dt1 == dt2)
        {return "=";}

     if (parseInt(dt1) < parseInt(dt2))
        {return "2";}
    }

  function compared_date_in_day(arg_dt1, arg_dt2)
    {
     if ((arg_dt1 == "") || (arg_dt2 == ""))
        {return "0";}
    
     var dt1_array = arg_dt1.split("."); 
     var dt1       = dt1_array[2]+"/"+dt1_array[1]+"/"+dt1_array[0];
     var date1     = new Date(dt1);
     
     var dt2_array = arg_dt2.split("."); 
     var dt2       = dt2_array[2]+"/"+dt2_array[1]+"/"+dt2_array[0];
     var date2     = new Date(dt2);
     
     var timeDiff = Math.abs(date2.getTime() - date1.getTime());
	 var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
     
     return diffDays;
    }
//krahasim i dy datave -------------------------------------------------------------------


// isEmailAddress ------------------------------------------------------------------------
  function isEmailAddress(theElement, theElementName)
  {
    var s = theElement.value;
    var filter=/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (s.length == 0 ) return true;
    if (filter.test(s))  
         return true;
    else  
      alert(alert_isemail_mesg);
      theElement.focus(); 
      return false;
  }
//----------------------------------------------------------------------------------------

// isInteger -----------------------------------------------------------------------------
  function isInteger(theElement, theElementName)
  {
    var s = Math.abs(theElement.value);
    var filter=/(^\d+$)/;
    if (s.length == 0 ) return true;
    if (filter.test(s))  
         return true;
    else  
           alert(alert_isinteger_mesg);
      theElement.focus(); 
      return false;
  }
//----------------------------------------------------------------------------------------

// isNull ---------------------------------------------------------------------------------
function isNull( field , fieldName) {
    selected = 0;
    fieldIsNull = 0;
    if ( field.type == "file" ||
         field.type == "text" ||
         field.type == "password" ||
         field.type == "textarea" ) {
      if ( field.value == "" )
        fieldIsNull = 1;
    } 
    else if ( field.type == "select-one" ) 
    {
        if ( field.options[field.selectedIndex].value == "")
          fieldIsNull = 1;
    } 
    else if ( field.type == "select-multiple" ) 
    {
        fieldIsNull = 1;
        for ( i = 0; i < field.length; i++ )
            {
             if ( field.options[i].selected )
                {
                 if ((field.options[i].value != "") && (field.options[i].value != "NULL"))
                    {
                     fieldIsNull = 0;
                    }
                }
           }
    } 
    else if ( field.type == "undefined" ||
                field.type == "checkbox"  ||
                field.type == "radio" ) {
        fieldIsNull = 1;
        for ( i = 0; i < field.length; i++ )
          if ( field[i].checked )
            fieldIsNull = 0;
    }
    
    if ( fieldIsNull ) 
    {
        if ( isNull.arguments.length  == 1 )
           alert( alert_isnull_mesg );
        else
           alert( alert_isnull_mesg + " " + fieldName );
        
        if ( field.type == "file" ||
             field.type == "text" ||
             field.type == "textarea"  ||
             field.type == "password"  ||
             field.type == "select-one"  ||
             field.type == "select-multiple" )
          field.focus();
       return false;
    }
    return true;
  }
//----------------------------------------------------------------------------------------

// isNumber ------------------------------------------------------------------------------
  function isNumber(theElement, etiketa)
  {
    s = theElement.value;
    if (isNaN(Math.abs(theElement.value)) && (s.charAt(0) != "#"))
    {
        if ( isNumber.arguments.length  < 1 ) 
           alert(alert_isnumber1_mesg);
        else  
        { 
           for (var i=0; (i <= s.length && s.charAt(i) != "."); )
           {
            if (((s.charAt(i) >= 0) && (s.charAt(i) <= 9)) ||
                 (s.charAt(i) == "," && i != 0 && i != s.length-1) || (s.charAt(i) == ".") )
                   i++; 
            else 
             { 
               //alert( "Vlera e " + etiketa +  " duhet të jetë një numër në formatin e caktuar" ); 
               alert(alert_isnumber1_mesg);
               theElement.focus(); 
               return false; 
             } 
           } 
          if (s.charAt(i) == ".") 
           { 
  	     for (i++;i <= s.length; ) 
             { 
              if (((s.charAt(i) >= 0) && (s.charAt(i) <= 9))) 
                i++; 
              else 
              { 
               //alert( "Vlera e " + etiketa +  " duhet të jetë një numër në formatin e caktuar" ); 
               alert(alert_isnumber1_mesg);
               theElement.focus(); 
               return false;
              } 
             } 
           } 
         } 
    } 
    return true;
  }
//----------------------------------------------------------------------------------------

// isNumber ------------------------------------------------------------------------------
  function isNumber_pozitiv(theElement, etiketa)
  {
    s = theElement.value;
    if (isNaN(Math.abs(theElement.value)) && (s.charAt(0) != "#"))
    {
        if ( isNumber_pozitiv.arguments.length  < 1 ) 
           alert(alert_isnumber1_mesg);
        else  
        { 
           for (var i=0; (i <= s.length && s.charAt(i) != "."); )
           {
            if (((s.charAt(i) >= 0) && (s.charAt(i) <= 9)) ||
                 (s.charAt(i) == "," && i != 0 && i != s.length-1) || (s.charAt(i) == ".") )
                   i++; 
            else 
             { 
               //alert( "Vlera e " + etiketa +  " duhet të jetë një numër në formatin e caktuar" ); 
               alert(alert_isnumber1_mesg);
               theElement.focus(); 
               return false; 
             } 
           } 
          if (s.charAt(i) == ".") 
           { 
  	     for (i++;i <= s.length; ) 
             { 
              if (((s.charAt(i) >= 0) && (s.charAt(i) <= 9))) 
                i++; 
              else 
              { 
               //alert( "Vlera e " + etiketa +  " duhet të jetë një numër në formatin e caktuar" ); 
               alert(alert_isnumber1_mesg);
               theElement.focus(); 
               return false;
              } 
             } 
           } 
         } 
    } 
    
    if (s != "")
       {
        if(s <= 0)
          {
           alert(alert_isnumber_pozitiv_mesg);
           theElement.focus(); 
           return false;
          }
       }
    return true;
  }
//-----------------------------------------------------------------------------------------

// isNumber ------------------------------------------------------------------------------
  function isNumber_pozitiv_and_zero(theElement, etiketa)
  {
    s = theElement.value;
    if (isNaN(Math.abs(theElement.value)) && (s.charAt(0) != "#"))
    {
        if ( isNumber_pozitiv_and_zero.arguments.length  < 1 ) 
           alert(alert_isnumber1_mesg);
        else  
        { 
           for (var i=0; (i <= s.length && s.charAt(i) != "."); )
           {
            if (((s.charAt(i) >= 0) && (s.charAt(i) <= 9)) ||
                 (s.charAt(i) == "," && i != 0 && i != s.length-1) || (s.charAt(i) == ".") )
                   i++; 
            else 
             { 
               //alert( "Vlera e " + etiketa +  " duhet të jetë një numër në formatin e caktuar" ); 
               alert(alert_isnumber1_mesg);
               theElement.focus(); 
               return false; 
             } 
           } 
          if (s.charAt(i) == ".") 
           { 
  	     for (i++;i <= s.length; ) 
             { 
              if (((s.charAt(i) >= 0) && (s.charAt(i) <= 9))) 
                i++; 
              else 
              { 
               //alert( "Vlera e " + etiketa +  " duhet të jetë një numër në formatin e caktuar" ); 
               alert(alert_isnumber1_mesg);
               theElement.focus(); 
               return false;
              } 
             } 
           } 
         } 
    } 
    
    if (s != "")
       {
        if(s < 0)
          {
           alert(alert_isnumber_pozitiv_zero_mesg);
           theElement.focus(); 
           return false;
          }
       }
    return true;
  }
//-----------------------------------------------------------------------------------------


function f_app_pastro(arg_id_form) 
  {
   if (document.getElementById(arg_id_form))   
      {
       var form = document.getElementById(arg_id_form);
       var nr;
       nr = -1;
       for(var i=0; i<form.elements.length; i++) 
          {
           if ((form.elements[i].type == "text" || form.elements[i].type == "textarea" || form.elements[i].type == "select-one" || form.elements[i].type == "select-multiple") && !(form.elements[i].disabled == true))
              {
               if (nr == -1)
                  {
                   nr = i;
                  }
             
               form.elements[i].value = "";
               
               if (form.elements[i].type == "select-one")
                  {
                   //mund te kemi fileter keshtu qe trigerojme ndryshimin
                   if (form.elements[i].id)
                      {
                       var id_elm_select = '#' + form.elements[i].id;
                       $(id_elm_select).trigger('change.select2');
                       
                       //select2 has the placeholder parameter. Use that one
                       //$("#state").select2({
					   //   placeholder: "Choose a Country"
                       //  });
                      }
                  }
              }
          }
     
       if (nr != -1)
          {
           form.elements[nr].focus();
          }
     }
  }

function f_app_search(arg_webbox, arg_event, arg_id_form) 
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

function f_app_back(arg_webbox, arg_event, arg_id_form) 
  {
   if (document.getElementById(arg_id_form))   
      {
       var form      = document.getElementById(arg_id_form);
       var vars_page = form.vars_page.value;

       GoTo("thisPage?event="+arg_webbox+"."+arg_event+"(vars_page="+vars_page+")");
      }
 }

function f_app_add_edit(arg_webbox, arg_event, arg_id_form, arg_post_id) 
  {
   if (document.getElementById(arg_id_form))   
      {
       var form      = document.getElementById(arg_id_form);
       var vars_page = form.vars_page.value;

       GoTo("thisPage?event="+arg_webbox+"."+arg_event+"(post_id="+arg_post_id+";vars_page="+vars_page+")");
      }
 }

function f_app_var_post(arg_webbox, arg_event, arg_id_form, arg_vlera) 
  {
   if (document.getElementById(arg_id_form))   
      {
       var form      = document.getElementById(arg_id_form);
       var vars_page = form.vars_page.value;

       GoTo("thisPage?event="+arg_webbox+"."+arg_event+"(var_post="+arg_vlera+";vars_page="+vars_page+")");
      }
 }

function f_app_col_order(arg_webbox, arg_event, arg_id_form, arg_order_by_indx, arg_order_by) 
  {
   if (document.getElementById(arg_id_form))   
      {
       var form      = document.getElementById(arg_id_form);
       var vars_page = form.vars_page.value;

       GoTo("thisPage?event="+arg_webbox+"."+arg_event+"(order_by_indx="+arg_order_by_indx+";order_by="+arg_order_by+";vars_page="+vars_page+")");
      }
  }

function f_app_save(arg_webbox, arg_event, arg_id_form) 
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

function f_app_del(arg_webbox, arg_event, arg_id_form) 
  {
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


function f_app_filter_listbox(arg_id) 
{
 if (document.getElementById(arg_id))
    {
     var parent_elm = document.getElementById(arg_id);
     var parent_val = parent_elm.value;

     if (parent_elm.getAttribute("id_obj_child") && parent_elm.getAttribute("js_data_array"))
        {
         var id_obj_child_sel  = parent_elm.getAttribute("id_obj_child");
         var js_data_array_sel = parent_elm.getAttribute("js_data_array");
         
         if (document.getElementById(id_obj_child_sel))
            {
             var child_elm = document.getElementById(id_obj_child_sel);
             
             if (f_app_array_js[js_data_array_sel])
                {
                 var var_id_et = f_app_array_js[js_data_array_sel];
                 
                 //pastrojme elementet child -------------------------------
                   child_elm.options.length = 0;
                 //---------------------------------------------------------
                 
                 //shtojme tek elementi child rreshtin e pare --------------
                   id_sel = "";
                   et_sel = "";
                   child_elm.options[0] = new Option(et_sel, id_sel);
                 //---------------------------------------------------------
                 
                 //popullojme elementin child me vlerat e filtruara --------
                   var jj = 0;
                   var nen_grupi_array = var_id_et.split('_Y_'); 
                   
                   for (var j=0; j<nen_grupi_array.length; j++)
                       {
                        var id_et  = nen_grupi_array[j].split('_X_'); 
                        var id_gr  = id_et[0];
                        var id_sel = id_et[1];
                        var et_sel = id_et[2];
      
                        if (id_gr == parent_val)
                           {
                            jj = jj + 1;
                            child_elm.options[jj] = new Option(et_sel, id_sel);
                           }
                      }
                 //---------------------------------------------------------
                 
                 //trigerojme ndryshimin e childit se mos kemi kaskade -----
                   if (typeof child_elm.onchange === "function") 
                      { 
				       child_elm.onchange();
                      }
                 //---------------------------------------------------------
                }
            }
        }
    }
}

function f_app_set_value(arg_id) 
{
 if (document.getElementById(arg_id))
    {
     var parent_elm = document.getElementById(arg_id);
     var parent_val = parent_elm.value;

     if (parent_elm.getAttribute("id_obj_child") && parent_elm.getAttribute("js_data_array"))
        {
         var id_obj_child_sel  = parent_elm.getAttribute("id_obj_child");
         var js_data_array_sel = parent_elm.getAttribute("js_data_array");
         
         if (document.getElementById(id_obj_child_sel))
            {
             var child_elm = document.getElementById(id_obj_child_sel);
             
             if (f_app_array_js[js_data_array_sel])
                {
                 var var_id_et = f_app_array_js[js_data_array_sel];
                 
                 //pastrojme elementet child -------------------------------
                   child_elm.value = "";
                 //---------------------------------------------------------
                 
                 //popullojme elementin child me vlerat e filtruara --------
                   var nen_grupi_array = var_id_et.split('_Y_'); 
                   for (var j=0; j<nen_grupi_array.length; j++)
                       {
                        var id_et  = nen_grupi_array[j].split('_X_'); 
                        var id_gr  = id_et[0];
                        var id_sel = id_et[1];
                        //var et_sel = id_et[2];

                        if (id_gr == parent_val)
                           {
                            child_elm.value = id_sel;
                           }
                      }
                 //---------------------------------------------------------
                 
                 //trigerojme ndryshimin e childit se mos kemi kaskade -----
                   if (typeof child_elm.onchange === "function") 
                      { 
				       child_elm.onchange();
                      }
                 //---------------------------------------------------------
                }
            }
        }
    }
}

function f_app_listbox_elements(arg_id) 
{
 if (document.getElementById(arg_id))
    {
     var elm_sel = document.getElementById(arg_id);

     if (elm_sel.getAttribute("js_data_array"))
        {
         var js_data_array_sel = elm_sel.getAttribute("js_data_array");
         
         if (f_app_array_js[js_data_array_sel])
            {
             var var_id_et = f_app_array_js[js_data_array_sel];
             
             //pastrojme elementet -------------------------------------
               elm_sel.options.length = 0;
             //---------------------------------------------------------
             
             //popullojme elementin me vlerat --------------------------
               var elm_array = var_id_et.split('_Y_'); 
               
               for (var j=0; j<elm_array.length; j++)
                   {
                    if (elm_array[j] == "")
                       {
                        //shtojme element bosh 
                        var id_sel = "";
                        var et_sel = "";
                       }
                    else
                       {
                        var id_et  = elm_array[j].split('_X_'); 
                        var id_sel = id_et[0];
                        var et_sel = id_et[1];
                       }

                    elm_sel.options[j] = new Option(et_sel, id_sel);
                  }
             //---------------------------------------------------------
            }
        }
    }
}

function f_app_return(arg_elm_id, arg_id, arg_name, arg_id_parent)
  {
   if (document.getElementById(arg_elm_id) && (arg_id != ''))
      {
       var elm_sel  = document.getElementById(arg_elm_id);
       var elm_name = elm_sel.name;
   
       var var_vlera_u_gjet = 'N';
       
       var i;
       for (i = 0; i < elm_sel.length; i++) 
           {
            //alert(elm_sel.options[i].text);
            options_vl = elm_sel.options[i].value;
            
            if (options_vl == arg_id)
               {
                var_vlera_u_gjet = 'Y';
               }
           }       
       
       //vendosim ne fillim vleren e prindit -------------------------------------------
         if (elm_sel.getAttribute("id_obj_parent") && (arg_id_parent != ''))
            {
             var id_obj_parent_sel = elm_sel.getAttribute("id_obj_parent");
             var elm_parent_sel    = document.getElementById(id_obj_parent_sel);
             elm_parent_sel.value  = arg_id_parent;

             //trigerojme ndryshimin ------------------------------
               var id_elm_select = '#' + id_obj_parent_sel;
               $(id_elm_select).trigger('change.select2');
             //----------------------------------------------------

             if ((var_vlera_u_gjet == 'N') && elm_parent_sel.getAttribute("js_data_array"))
                {
                 var js_data_array_sel = elm_parent_sel.getAttribute("js_data_array");
         
                 if (f_app_array_js[js_data_array_sel])
                    {
                     var var_id_et                     = f_app_array_js[js_data_array_sel] + '_Y_' + arg_id_parent + '_X_' + arg_id + '_X_' + arg_name; //_Y_15_X_149_X_Qendër Bulgarec (Qendër Shëndetësore)
                     f_app_array_js[js_data_array_sel] = var_id_et;
                    }
                }
            }
       //vendosim ne fillim vleren e prindit -------------------------------------------

       if (var_vlera_u_gjet == 'N')
          {
           //shtojme vleren ne liste
           i = i + 1;
           elm_sel.options[i] = new Option(arg_name, arg_id);
          }
       
       elm_sel.value = arg_id;

       //trigerojme ndryshimin ------------------------------
         var id_elm_select = '#' + arg_elm_id;
         $(id_elm_select).trigger('change.select2');
       //----------------------------------------------------
      }
  }

function f_app_return_laboratory(arg_elm_id, arg_id, arg_name, arg_id_parent)
  {
   //funksion i dedikuar per laboratorin ----------------------------
   //rifreskojme array qe formojme elementet ------------------------
     var js_data_array_sel = 'id_reporting_entity_laboratory';
     
     if (f_app_array_js[js_data_array_sel])
        {
         var var_vlera_u_gjet = 'N';
         var var_id_et = f_app_array_js['id_reporting_entity_laboratory'];
   
         //popullojme elementin child me vlerat e filtruara --------
           var nen_grupi_array = var_id_et.split('_Y_'); 
           for (var j=0; j<nen_grupi_array.length; j++)
               {
                var id_et  = nen_grupi_array[j].split('_X_'); 
                var id_gr  = id_et[0];
                var id_sel = id_et[1];
                //var et_sel = id_et[2];

                if (id_gr == arg_id)
                   {
                    var_vlera_u_gjet = 'Y';
                   }
              }

           if (var_vlera_u_gjet == 'N')
              {
               var var_id_et                     = f_app_array_js[js_data_array_sel] + '_Y_' + arg_id + '_X_' + arg_name; //_Y_149_X_laborator test
               f_app_array_js[js_data_array_sel] = var_id_et;
              
               //shtojme kete element ne gjithe listat e laburatoreve qe mund te jene shtuar
                 var elm_labs = document.getElementsByName("lab_id_reporting_entity_laboratory");

                 for (var j=0; j<elm_labs.length; j++)
                     {
                      //shtojme vleren ne liste
                      i                      = elm_labs[j].length;
                      elm_labs[j].options[i] = new Option(arg_name, arg_id);
                     }
              }
          
          //vendosim vleren e zgjedhur tek laboratoret bosh ---------
             var elm_labs = document.getElementsByName("lab_id_reporting_entity_laboratory");

             for (var j=0; j<elm_labs.length; j++)
                 {
                  //shtojme vleren ne liste
                  if (elm_labs[j].value == '')
                     {
                      elm_labs[j].value = arg_id;
                     }
                 }
          //vendosim vleren e zgjedhur tek laboratoret bosh ---------
        }
   //rifreskojme array qe formojme elementet ------------------------
  }
