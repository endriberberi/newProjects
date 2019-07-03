<?
$html_csv = "";
$new_line = "
";
$detect_order_str  = "UTF-8, ISO-8859-1";

      IF ($content_title != "")
         {
          $exp_filename = $content_title.".csv";
          $exp_titull   = $content_title;
         }
      ELSE
         {
          $exp_filename = "Book1.csv";
          $exp_titull   = "";
         }
      
      $row_start = 0;
      IF (ISSET($data_arr[0]["properties"]) AND IS_ARRAY($data_arr[0]["properties"]))
         {
          $row_start = 1;
          
          IF (ISSET($data_arr[0]["properties"]["exp_file_name"]) AND ($data_arr[0]["properties"]["exp_file_name"] != ""))
             {
              $exp_filename = $data_arr[0]["properties"]["exp_file_name"].".csv";
             }

          IF (ISSET($data_arr[0]["properties"]["exp_titull"]) AND ($data_arr[0]["properties"]["exp_titull"] != ""))
             {
              $exp_titull = $data_arr[0]["properties"]["exp_titull"];
             }
         }
      
      //koka -----------------------------------------------------------------------------------------------------
        IF ($exp_titull != "")
           {
            $str_character_set = mb_detect_encoding($exp_titull, $detect_order_str);

            IF ($str_character_set == "UTF-8")
               {
                $exp_titull = mb_convert_encoding($exp_titull, "ISO-8859-1", "UTF-8"); //konvertojme ne ISO
               }

            IF (STRISTR($exp_titull, ','))
               {
                $exp_titull = '"'.$exp_titull.'"';
               }
            
            $html_csv .= $exp_titull.$new_line;
           }
      //koka -----------------------------------------------------------------------------------------------------

      //popullojme objektin --------------------------------------------------------------------------------------
        FOR ($i=$row_start; $i < count($data_arr); $i++)
            {
             $row_arr = $data_arr[$i];
             $row_txt = '';
             
             FOR ($j=0; $j < count($row_arr); $j++)
                 {
                  $cel_sel = $row_arr[$j];
                  
                  IF (IS_ARRAY($cel_sel))
                     {
                      $cel_vl = $cel_sel['vl'];
                     }
                  ELSE
                     {
                      $cel_vl = $cel_sel;
                     }
                  
                  IF ($cel_vl != '')
                     {
                      $cel_vl = STR_REPLACE(array("\r\n","\r","\n"), " ", $cel_vl); 
                      
                      $str_character_set = mb_detect_encoding($cel_vl, $detect_order_str);
 
                      IF ($str_character_set == "UTF-8")
                         {
                          $cel_vl = mb_convert_encoding($cel_vl, "ISO-8859-1", "UTF-8"); //konvertojme ne ISO
                         }

                      IF (STRISTR($cel_vl, ','))
                         {
                          $cel_vl = '"'.$cel_vl.'"';
                         }
                     }
                     
                  $row_txt .= ','.$cel_vl;
                 }
             
             $html_csv .= SUBSTR($row_txt, 1).$new_line;
            }
      //popullojme objektin --------------------------------------------------------------------------------------

      IF ($output_browser == "Y")
         {
          header("Content-type: application/vnd.ms-excel");
          header("Content-disposition: attachment; filename=".$exp_filename);

          header('Cache-Control: max-age=0');
          // If you're serving to IE 9, then the following may be needed
          header('Cache-Control: max-age=1');

          // If you're serving to IE over SSL, then the following may be needed
          header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
          header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
          header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
          header ('Pragma: public'); // HTTP/1.0

          print $html_csv;
         }
?>