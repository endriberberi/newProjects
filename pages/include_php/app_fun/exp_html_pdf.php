<?
      IF ($content_title != "")
         {
          $exp_filename = $content_title;
          $exp_titull   = $content_title;
         }
      ELSE
         {
          $exp_filename = "Doc";
          $exp_titull   = "";
         }

      IF ($tipi_exp == 3)
         {
          $prapashtesa   = "html";
          $exp_filename .= ".html";
         }
      ELSE
         {
          $prapashtesa   = "pdf";
          $exp_filename .= ".pdf";
         }

      $row_start = 0;
      IF (ISSET($data_arr[0]["properties"]) AND IS_ARRAY($data_arr[0]["properties"]))
         {
          $row_start = 1;
          
          IF (ISSET($data_arr[0]["properties"]["exp_file_name"]) AND ($data_arr[0]["properties"]["exp_file_name"] != ""))
             {
              $exp_filename = $data_arr[0]["properties"]["exp_file_name"].".".$prapashtesa;
             }

          IF (ISSET($data_arr[0]["properties"]["exp_titull"]) AND ($data_arr[0]["properties"]["exp_titull"] != ""))
             {
              $exp_titull = $data_arr[0]["properties"]["exp_titull"];
             }
         }

      $tr_class_sel       = '2';
      $style_tr_pdf_odd   = ' class="odd"';  // style="border: 1px solid #ddd;"
      $style_tr_pdf_even  = ' class="even"'; // style="border: 1px solid #ddd;"
      
      $html_pdf_print     = '';
      
      //koka -----------------------------------------------------------------------------------------------------
        IF ($exp_titull != "")
           {
            $html_pdf_print .= '<h1>'.$exp_titull.'</h1>';
           }
      //koka -----------------------------------------------------------------------------------------------------

      //popullojme objektin --------------------------------------------------------------------------------------
        IF ($prapashtesa == "pdf")
           {
            INI_SET("memory_limit", "256M");
            
            IF ((count($data_arr) > 1000) AND (count($data_arr) <= 2000))
               {
                INI_SET("memory_limit", "512M");
               }

            IF ((count($data_arr) > 2000) AND (count($data_arr) <= 5000))
               {
                INI_SET("memory_limit", "1024M");
               }

            IF ((count($data_arr) > 5000) AND (count($data_arr) <= 10000))
               {
                INI_SET("memory_limit", "2048M");
               }

            IF (count($data_arr) > 10000)
               {
                INI_SET("memory_limit", "3072M");
               }
          }
        
        $html_pdf_print .= '<table cellspacing="0" cellpadding="2" class="tab_lista">';

        FOR ($i=$row_start; $i < count($data_arr); $i++)
            {
             $row_arr = $data_arr[$i];
             
             IF ($i > $row_start)
                {
                 $i_para       = $i - 1;
                 $row_arr_para = $data_arr[$i_para];
                 
                 IF (count($row_arr_para) != count($row_arr))
                    {
                     $html_pdf_print .= '</table>';
                     $html_pdf_print .= '<br>';
                     $html_pdf_print .= '<table cellspacing="0" cellpadding="2" class="tab_lista">';

                     $tr_class_sel    = '2';
                    }
                }

             IF ($tr_class_sel == '2')
                {
                 $tr_class_sel = '1';
                 $style_tr_pdf = $style_tr_pdf_odd;
                }
             ELSE
                {
                 $tr_class_sel = '2';
                 $style_tr_pdf = $style_tr_pdf_even;
                }

             $html_pdf_print .= '<tr'.$style_tr_pdf.'>';
             
             FOR ($j=0; $j < count($row_arr); $j++)
                 {
                  $cel_sel = $row_arr[$j];
                  
                  IF (IS_ARRAY($cel_sel))
                     {
                      $cel_vl      = $cel_sel['vl'];
                      $cel_tag     = $cel_sel['tag'];
                      $cel_vlf     = $cel_sel['vlf'];
                      $cel_bold    = $cel_sel['bold'];
                      $cel_colspan = $cel_sel['colspan'];
                      $cel_align   = $cel_sel['align'];
                      $cel_style   = $cel_sel['style'];
                     }
                  ELSE
                     {
                      $cel_vl      = $cel_sel;
                      $cel_tag     = '';
                      $cel_vlf     = '';
                      $cel_bold    = '';
                      $cel_colspan = '';
                      $cel_align   = '';
                      $cel_style   = '';
                     }

                  IF ($cel_vlf != '')
                     {
                      $cel_vl = $cel_vlf;
                     }
                  
                  $b1 = ""; $b11 = "";
                  IF ($cel_bold == 'Y')
                     {
                      $b1 = "<b>"; $b11 = "</b>";
                     }

                  $cel_colspan_sel = "";
                  IF (($cel_colspan != "") AND ($cel_colspan > 1))
                     {
                      $cel_colspan_sel = ' colspan="'.$cel_colspan.'"';
                     }
                  
                  $cel_align_sel = "";
                  IF ($cel_align != "")
                     {
                      $cel_align_sel = ' align="'.$cel_align.'"';
                     }
                  
                  $cel_style_sel = ' style="'.$border_style.'"';
                  IF ($cel_style != "")
                     {
                      $cel_style_sel = ' style="'.$border_style.' '.$cel_style.'"';
                     }

                  $tag_sel = 'td';
                  IF ($cel_tag == 'th')
                     {
                      $tag_sel = 'th';
                     }

                  $html_pdf_print .= '<'.$tag_sel.$cel_colspan_sel.$cel_align_sel.$cel_style_sel.'>'.$b1.$cel_vl.$b11.'</'.$tag_sel.'>';

                 }
             
             $html_pdf_print .= '</tr>';
            }
        
        $html_pdf_print .= '</table>';
      //popullojme objektin ----------------------------------------------------------------------------------------------

      $app_header_mesg    = WebApp::getVar("app_header_mesg");
      $app_contact_1_mesg = WebApp::getVar("app_contact_1_mesg");
      $app_contact_2_mesg = WebApp::getVar("app_contact_2_mesg");
      $app_contact_3_mesg = WebApp::getVar("app_contact_3_mesg");
	  $faqe_mesg          = WebApp::getVar("_page");

      IF ($tipi_exp == 3)
         {
          IF ($output_browser == "Y")
             {
              header('Cache-Control: max-age=0');
              // If you're serving to IE 9, then the following may be needed
              header('Cache-Control: max-age=1');

              // If you're serving to IE over SSL, then the following may be needed
              header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
              header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
              header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
              header ('Pragma: public'); // HTTP/1.0

              header('Content-Type: text/html;');
              header('Content-Disposition: attachment;filename="'.$exp_filename.'"');
	          
              $html_print  = '<html>';
              $html_print .= '<head>';
              $html_print .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
              $html_print .= '<title>'.$exp_titull.'</title>';
              $html_print .= '<link rel="stylesheet" type="text/css" href="'.APP_URL.'include_php/app_fun/print_pdf.css">';
              $html_print .= '</head>';
              $html_print .= '<body>';
              $html_print .= '
                              <table border="0" border="0" width="1100" cellspacing="0" cellpadding="0">
                               <tr>
                                <td width="340"><img src="'.APP_URL.'graphics/logo.png" border="0"></td>
                                <td align="center"><h1 style="color: #009371">'.$app_header_mesg.'</h1></td>
                                <td width="340" align="right">'.$app_contact_1_mesg.'<br>'.$app_contact_2_mesg.'<br>'.$app_contact_3_mesg.'</td>
                               </tr>
                               <tr>
                                <td colspan="3" height="5"><img src="'.APP_URL.'graphics/spacer.png" border="0" height="5" width="1"></td>
                               </tr>
                               <tr>
                                <td colspan="3" bgcolor="#bb2127" height="2"><img src="'.APP_URL.'graphics/spacer.png" border="0" height="2" width="1"></td>
                               </tr>
                              </table>
                             ';

              $html_print .= $html_pdf_print;
              $html_print .= '</body>';
              $html_print .= '</html>';

              print $html_print;
             }
         }

      IF ($tipi_exp == 4)
         {
          //html e konvertojme ne pdf
          IF ($output_browser == "Y")
             {
              include_once EASY_PATH."lib_classes/mpdf60/mpdf.php";
              
              //function mPDF($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=9, $orientation='P') {

              $V_L_sel = "-L";
                   
              $mpdf = new mPDF('c','A4'.$V_L_sel, 0, '', 10, 10, 20, 20, 5, 5);
              $mpdf->SetCreator($app_header_mesg);

              $mpdf->SetDisplayPreferences('/FitWindow/');
              $mpdf->SetDisplayMode('real');

	          
	          $headerHtml_bac = '
                              <table border="0" width="100%" border="0" cellspacing="0" cellpadding="0">
                               <tr>
                                <td width="340"><img src="'.APP_PATH.'graphics/logo.png" border="0"></td>
                                <td align="center"><h1 color="#009371">'.$app_header_mesg.'</h1></td>
                                <td width="340" align="right">'.$app_contact_1_mesg.'<br>'.$app_contact_2_mesg.'<br>'.$app_contact_3_mesg.'</td>
                               </tr>
                               <tr>
                                <td colspan="3" height="5"><img src="'.APP_PATH.'graphics/spacer.png" border="0" height="5" width="1"></td>
                               </tr>
                               <tr>
                                <td colspan="3" bgcolor="#bb2127" height="2"><img src="'.APP_PATH.'graphics/spacer.png" border="0" height="2" width="1"></td>
                               </tr>
                              </table>
                             ';

              // HEADER
              $headerHtml = '<div class="pdf_header">
                              <table border="0" width="100%" border="0" cellspacing="0" cellpadding="0">
                               <tr>
                                <td><img src="'.APP_PATH.'graphics/logo_pdf.png" border="0"></td>
                                <td width="100%" align="right" class="pdf_header_text">'.$app_header_mesg.'</td>
                               </tr>
                              </table>
				             </div>';

              // FOOTER
              $footerHtml = '<div class="pdf_footer">
					           <div class="pdf_footer_text">
					              '.$app_contact_1_mesg.'<br>'.$app_contact_2_mesg.' '.$app_contact_3_mesg.'
					           </div>	
					           <div class="pdf_footer_pager">'.$faqe_mesg.' {PAGENO} / {nbpg}</div>
				             </div>';			

              $html_print .= '<div>'.$html_pdf_print.'</div>';

              $css1 = file_get_contents(APP_PATH.'include_php/app_fun/print_pdf.css');
              $mpdf->WriteHTML($css1,1);

              $mpdf->setHTMLFooter($footerHtml);
              $mpdf->setHTMLHeader($headerHtml);
              $mpdf->WriteHTML($html_print);

              $pageTitle = $exp_filename;

              //header('Content-Type: application/pdf');
              //header('Content-Disposition: attachment;filename="'.$exp_filename.'"');
              //header('Cache-Control: max-age=0');
              //If you're serving to IE 9, then the following may be needed
              //header('Cache-Control: max-age=1');

              // If you're serving to IE over SSL, then the following may be needed
              header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
              header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
              header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
              header ('Pragma: public'); // HTTP/1.0

              $mpdf->Output($pageTitle,'D');
             }
         }
?>