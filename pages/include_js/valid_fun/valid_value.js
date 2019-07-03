function valid_value(arg)
  {
   //shenime -----------------------------------------------------------------------
     //1. Data postohet e formatuar me formatin MYSQL YYYY-MM-DD
     //2. Eshte regulluar kapja e formes dinamikisht nga emri 
     //3. Eshte regulluar validimi i checkbox dhe i radiove 
     //4. Per checkbox dhe radiove ne rast se gjendet atributi value_off='xxx' 
     //   ateher per elementet e pacekuar postohet vlera e ketij atributi 
     //   ne te kundert elementi postohet vetem kur eshte i cekuar sic ka qene dhe tek versioni i pare
     //5. ne rast se elementi ka atributin vl si vlere e objektit postohet vlera e ketij atributi ne te 
     //   kundert postohet vlera qe ka 'value'; Kjo perdoret ne fushat qe kane mask
     
     //fushat bashkohen me <-> / <_>
     
     //fushat tekst nuk pastrohen nga thonjezat tek dhe cifte

     //6. kapet vlera dhe validohet editori kur eshte inkluduar ne textarea; kjo kuptohet nga atibuti shtese
     //   qe ndodhet tek textarea kur ne te inkludohet editori 
     //   oEdit='oEdit1'; oEdit1 = emri i objektit; //var oEdit1 = new InnovaEditor('oEdit1');
     
   //shenime -----------------------------------------------------------------------

     if (document.getElementById(arg))
        {
         var form = document.getElementById(arg); //formen e kapim nga id
        }
     else
        {
         var form = document.getElementsByName(arg)[0]; //formen e kampim nga emri
        }
     
     var a        = "";
     var kol_id   = "";
     var kol_koka = "";
     
     var element_check      = 0;
     var element_check_name = 0;
     
     var var_isnull         = 0;
     var var_isnumber       = 0;
     var var_isalpha        = 0;
     var var_isdate         = 0;
     var var_isemailaddress = 0;
     var var_isinteger      = 0;
     var var_etiketa        = "";
     var var_valid          = "";
     var var_dt_format      = "";
     var vl_select_multiple = "";
     var value_off          = "";
     var var_oEdit          = "";
     var txt_return         = "";
     
     for(var i=0; i<form.elements.length; i++) 
        {
         if (form.elements[i].type == "file" || form.elements[i].type == "text" || form.elements[i].type == "password" || form.elements[i].type == "textarea" || form.elements[i].type == "select-one" || form.elements[i].type == "select-multiple" || form.elements[i].type == "checkbox" || form.elements[i].type == "radio" || form.elements[i].type == "hidden")
            {
             element_name        = form.elements[i].name;
             //var_etiketa       = form.elements[i].etiketa;
             var_etiketa         = form.elements[i].getAttribute("etiketa"); //edhe per moxilla firefox
             

             if (var_etiketa)
                {
                 var_valid           = form.elements[i].getAttribute("valid"); //edhe per moxilla firefox
                 
                 var_isnull          = var_valid.substr(0,1);
                 var_isnumber        = var_valid.substr(2,1);
                 var_isalpha         = var_valid.substr(4,1);
                 var_isdate          = var_valid.substr(6,1);
                 var_isemailaddress  = var_valid.substr(8,1);
                 var_isinteger       = var_valid.substr(10,1);
             
             if (form.elements[i].type == "text" || form.elements[i].type == "password" || form.elements[i].type == "file")
                { 
                 if (form.elements[i].type == "text")
                    {a = left_right_trim(form.elements[i]);}

                 if(var_isnull == 1)
                   {
                    if (!isNull(form.elements[i], var_etiketa))
                       {return txt_return;}
                   }
                 
                 if(var_isnumber == 1)
                   {
                    if (!isNumber(form.elements[i], var_etiketa))
                       {return txt_return;}
                   }
                 if(var_isnumber == 2)
                   {
                    if (!isNumber_pozitiv(form.elements[i], var_etiketa))
                       {return txt_return;}
                   }
                 if(var_isnumber == 3)
                   {
                    if (!isNumber_pozitiv_and_zero(form.elements[i], var_etiketa))
                       {return txt_return;}
                   }
            
                 
                 if(var_isalpha == 1)
                   {
                    if (!isAlpha(form.elements[i], var_etiketa))
                       {return txt_return;}
                   }
            
                 if(var_isdate == 1)
                   {
                    if (!isDate(form.elements[i], var_etiketa))
                       {return txt_return;}
                   }

                 if(var_isemailaddress == 1)
                   {
                    if (!isEmailAddress(form.elements[i], var_etiketa))
                       {return txt_return;}
                   }

                 if(var_isinteger == 1)
                   {
                    if (!isInteger(form.elements[i], var_etiketa))
                       {return txt_return;}
                   }

                 if(var_isdate == 1)
                   {
                    var_dt_format = "";
                    
                    if (form.elements[i].getAttribute("vl") != undefined)
                       {var_dt_format = form.elements[i].getAttribute("vl");}
                    else
                       {var_dt_format = form.elements[i].value;}
                    
                    if (var_dt_format != "")
                       {var_dt_format = var_dt_format.substr(6,4)+"-"+var_dt_format.substr(3,2)+"-"+var_dt_format.substr(0,2);}
                    kol_id   += '<_>'+var_dt_format;
                   }
                 else
                   {
                    if (form.elements[i].getAttribute("vl") != undefined)
                       {kol_id += '<_>'+form.elements[i].getAttribute("vl");}
                    else
                       {kol_id += '<_>'+form.elements[i].value;}
                   }
                 
                 kol_koka += '<_>'+form.elements[i].name;
                }

             if (form.elements[i].type == "textarea")
                { 
                 var_oEdit = form.elements[i].getAttribute("oEdit"); //shikohet se mos eshte inkluduar editori
                 
                 if (var_oEdit)
                    {
                     var oEdit_sel = new InnovaEditor(var_oEdit);
                     if (var_isnull == 1)
                        {
                         if (f_trim(oEdit_sel.getTextBody()) == "")
                            {
                             alert( alert_isnull_mesg + " " + var_etiketa );
                             oEdit_sel.focus();
                             return txt_return;
                            }
                        }
                     
                     kol_id   += '<_>'+oEdit_sel.getXHTMLBody();
                     kol_koka += '<_>'+form.elements[i].name;
                    }
                 else
                    {
                     //rasti normal i textarea, pra nuk eshte inkluduar ne te editori -----
                     a = left_right_trim(form.elements[i]);

                     if (var_isnull == 1)
                        {
                         if (!isNull(form.elements[i], var_etiketa))
                            {return txt_return;}
                        }
                       
                     kol_id   += '<_>'+form.elements[i].value;
                     kol_koka += '<_>'+form.elements[i].name;
                    }
                }


             if ((form.elements[i].type == "select-one") || (form.elements[i].type == "select-multiple"))
                {
                 if(var_isnull == 1)
                   {
                    if (!isNull(form.elements[i], var_etiketa))
                       {return txt_return;}
                   }

                 
                 //if((var_isnull == 1) && (form.elements[i].options[form.elements[i].selectedIndex].value == 'NULL' || form.elements[i].options[form.elements[i].selectedIndex].value == ''))
                 //  {alert("Ju lutemi plotesoni "+var_etiketa);
                 //   return txt_return;}
                    
                 kol_koka += '<_>'+form.elements[i].name;

                 if (form.elements[i].type == "select-one")
                    {
                     kol_id += '<_>'+form.elements[i].options[form.elements[i].selectedIndex].value;
                    }
                
                 if (form.elements[i].type == "select-multiple")
                    {
                     vl_select_multiple = "";
                     for (var j=0; j < form.elements[i].options.length; j++)
                         {
                          if (form.elements[i].options[j].selected)
                             {vl_select_multiple += ","+form.elements[i].options[j].value;}
                         }
                     if (vl_select_multiple != '')
                        {vl_select_multiple = vl_select_multiple.substr(1);}
                     
                     kol_id += '<_>'+vl_select_multiple;
                    }
                }

             if (form.elements[i].type == "hidden")
                {
                 if(var_isnull == 1)
                   {
                    if (!isNull(form.elements[i], var_etiketa))
                       {return txt_return;}
                   }

                 if (form.elements[i].getAttribute("vl") != undefined)
                    {kol_id += '<_>'+form.elements[i].getAttribute("vl");}
                 else
                    {kol_id += '<_>'+form.elements[i].value;}
                   
                 kol_koka += '<_>'+form.elements[i].name;
                }

             if (form.elements[i].type == "checkbox")
                {
                 //shikohet nese eshte cekuar ndonje nga keta elemente -----
                   if(var_isnull == 1)
                     {
                      element_check      = 0;
                      element_check_name = form.elements[i].name;
                      for(var c=0; c<form.elements.length; c++) 
                         {
                          if (form.elements[c].type == "checkbox" && form.elements[c].name == element_check_name)
                             {
                              if (form.elements[c].checked)
                                 {element_check = 1;}
                             }
                         }

                      if (element_check==0)
                         {alert(alert_isnull_mesg + " " + var_etiketa);
                          return txt_return;}
                     }
                 //shikohet nese eshte cekuar ndonje nga keta elemente -----

                 //kol_koka += '<_>'+form.elements[i].name;
                 if (form.elements[i].checked)
                    {
                     kol_koka += '<_>'+form.elements[i].name;
                     //kol_id += '<_>'+form.elements[i].value;
                     kol_id += '<_>'+form.elements[i].value;
                     element_check = 1;
                    }
                 else
                    {
                     value_off = form.elements[i].getAttribute("value_off"); //edhe per moxilla firefox
                     if (value_off)
                        {
                         kol_koka += '<_>'+form.elements[i].name;
                         kol_id   += '<_>'+value_off;
                        }
                    }

                 }

             if (form.elements[i].type == "radio")
                {
                 //shikohet nese eshte cekuar ndonje nga keta elemente -----
                   if(var_isnull == 1)
                     {
                      element_check      = 0;
                      element_check_name = form.elements[i].name;
                      for(var c=0; c<form.elements.length; c++) 
                         {
                          if (form.elements[c].type == "radio" && form.elements[c].name == element_check_name)
                             {
                              if (form.elements[c].checked)
                                 {element_check = 1;}
                             }
                         }

                      if (element_check==0)
                         {alert(alert_isnull_mesg + " " + var_etiketa);
                          return txt_return;}
                     }
                 //shikohet nese eshte cekuar ndonje nga keta elemente -----

                 //kol_koka += '<_>'+form.elements[i].name;
                 if (form.elements[i].checked)
                    {
                     kol_id   += '<_>'+form.elements[i].value;
                     element_check = 1;
                     kol_koka += '<_>'+form.elements[i].name;
                    }
                 else
                    {
                     value_off = form.elements[i].getAttribute("value_off"); //edhe per moxilla firefox
                     if (value_off)
                        {
                         kol_koka += '<_>'+form.elements[i].name;
                         kol_id   += '<_>'+value_off;
                        }
                    }
                }
              }
            }
	}
     
     kol_id     = kol_id.substr(3);
     kol_koka   = kol_koka.substr(3);
     txt_return = kol_koka+"<->"+kol_id;
     
     txt_return = txt_return.replace(/;/gi, "<pikp>"); 
     txt_return = txt_return.replace(/&/gi, "<_and_>"); 
     txt_return = delete_entities(txt_return);
     
     return txt_return;  
    }

function left_right_trim(theElement)
    {
     var el_vl        = theElement.value;
     theElement.value = el_vl.replace(/^\s+|\s+$/g,'');
  		
     return "aa";
    }

function f_trim(stringToTrim) 
  {return stringToTrim.replace(/^\s+|\s+$/g,"");}

function f_ltrim(stringToTrim) 
  {return stringToTrim.replace(/^\s+/,"");}

function f_rtrim(stringToTrim) 
  {return stringToTrim.replace(/\s+$/,"");}

function delete_entities(fild_value) 
       {
        str_entities = new Array(/%u0391/ig, /%u0392/ig, /%u0393/ig, /%u0394/ig, /%u0395/ig, /%u0396/ig, /%u0397/ig, /%u0398/ig, /%u0399/ig, /%u039A/ig, /%u039B/ig, /%u039C/ig, /%u039D/ig, /%u039E/ig, /%u039F/ig, /%u03A0/ig, /%u03A1/ig, /%u03A3/ig, /%u03A4/ig, /%u03A5/ig, /%u03A6/ig, /%u03A7/ig, /%u03A8/ig, /%u03A9/ig, /%u03B1/ig, /%u03B2/ig, /%u03B3/ig, /%u03B4/ig, /%u03B5/ig, /%u03B6/ig, /%u03B7/ig, /%u03B8/ig, /%u03B9/ig, /%u03BA/ig, /%u03BB/ig, /%u03BC/ig, /%u03BD/ig, /%u03BE/ig, /%u03BF/ig, /%u03C0/ig, /%u03C1/ig, /%u03C2/ig, /%u03C3/ig, /%u03C4/ig, /%u03C5/ig, /%u03C6/ig, /%u03C7/ig, /%u03C8/ig, /%u03C9/ig, /%u03D1/ig, /%u03D2/ig, /%u03D6/ig, /%u2022/ig, /%u2026/ig, /%u2032/ig, /%u2033/ig, /%u203E/ig, /%u2044/ig, /%u2118/ig, /%u2111/ig, /%u211C/ig, /%u2122/ig, /%u2135/ig, /%u2190/ig, /%u2191/ig, /%u2192/ig, /%u2193/ig, /%u2194/ig, /%u21B5/ig, /%u21D0/ig, /%u21D1/ig, /%u21D2/ig, /%u21D3/ig, /%u21D4/ig, /%u2200/ig, /%u2202/ig, /%u2203/ig, /%u2205/ig, /%u2207/ig, /%u2208/ig, /%u2209/ig, /%u220B/ig, /%u220F/ig, /%u2211/ig, /%u2212/ig, /%u2217/ig, /%u221A/ig, /%u221D/ig, /%u221E/ig, /%u2220/ig, /%u2227/ig, /%u2228/ig, /%u2229/ig, /%u222A/ig, /%u222B/ig, /%u2234/ig, /%u223C/ig, /%u2245/ig, /%u2248/ig, /%u2260/ig, /%u2261/ig, /%u2264/ig, /%u2265/ig, /%u2282/ig, /%u2283/ig, /%u2284/ig, /%u2286/ig, /%u2287/ig, /%u2295/ig, /%u2297/ig, /%u22A5/ig, /%u22C5/ig, /%u2308/ig, /%u2309/ig, /%u230A/ig, /%u230B/ig, /%u2329/ig, /%u232A/ig, /%u25CA/ig, /%u2660/ig, /%u2663/ig, /%u2665/ig, /%u2666/ig, /%u0152/ig, /%u0153/ig, /%u0160/ig, /%u0161/ig, /%u0178/ig, /%u02C6/ig, /%u02DC/ig, /%u2002/ig, /%u2003/ig, /%u2009/ig, /%u2013/ig, /%u2014/ig, /%u2018/ig, /%u2019/ig, /%u201A/ig, /%u201C/ig, /%u201D/ig, /%u201E/ig, /%u2020/ig, /%u2021/ig, /%u2030/ig, /%u20AC/ig);
    
        fild_value = escape(fild_value);
    
        for (var i=0; i<str_entities.length; i++) 
            {fild_value=fild_value.replace(str_entities[i], "");}

	    fild_value = unescape(fild_value);
	
	    return fild_value;
       }   
    