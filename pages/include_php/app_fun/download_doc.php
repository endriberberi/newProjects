<?
set_time_limit(0);
//INI_SET("memory_limit", "256M");

//parametrat -------------------------------------------------------------------------------------------------
  $id_sel = $_GET["id_sel"];
//parametrat -------------------------------------------------------------------------------------------------

//validohet post_id ------------------------------------------------------------------------------------------
  IF (ISSET($id_sel) AND ($id_sel != ""))
     {
      $id_sel_nem_id = f_app_decrypt($id_sel, DESK_KEY, DESK_IV);
     }
  ELSE
     {
      HEADER('Location: ' . APP_URL);
      EXIT;
     }

  $id_sel_nem_id_arr = EXPLODE("|", $id_sel_nem_id);
  $post_id           = $id_sel_nem_id_arr[0];
  $post_id_nem       = $id_sel_nem_id_arr[1];

//------------------------------------------------------------------------------------------------------------
//KUSH NA THERET ---------------------------------------------------------------------------------------------
  IF (!ISSET($post_id_nem) OR ($post_id_nem == ""))
     {
      HEADER('Location: ' . APP_URL);
      EXIT;
     }

  IF (!ISSET($post_id) OR ($post_id == ""))
     {
      HEADER('Location: ' . APP_URL);
      EXIT;
     }
//KUSH NA THERET ---------------------------------------------------------------------------------------------

//UNSERIALIZE ------------------------------------------------------------------------------------------------
  $post_id_arr  = UNSERIALIZE($post_id);
  $post_id_doc  = $post_id_arr["id_doc"];
  $post_preview = $post_id_arr["preview"];
//UNSERIALIZE ------------------------------------------------------------------------------------------------

//LEXOJME SKEDARIN -------------------------------------------------------------------------------------------
  $sql = "SELECT file_name,
                 file_type,
                 file_size,
                 file_path
            FROM phi_docs 
           WHERE id_doc = '".ValidateVarFun::f_real_escape_string($post_id_doc)."'
         ";
  
  $rs = WebApp::execQuery($sql);
  IF (!$rs->EOF())
     {
      $file_name      = $rs->Field("file_name");
      $file_type      = $rs->Field("file_type");
      $file_size      = $rs->Field("file_size");
      $file_path      = $rs->Field("file_path");

      $file_name_disk = PATH_ROOT_DOCS.$file_path;
      
      IF (IS_FILE($file_name_disk))
         {
          header("Content-type: ".$file_type);
          header("Content-Length: ".$file_size);

          IF (
              (STRTOUPPER(SUBSTR($file_name, -4)) == '.GIF') OR 
              (STRTOUPPER(SUBSTR($file_name, -4)) == '.PNG') OR 
              (STRTOUPPER(SUBSTR($file_name, -4)) == '.JPG') OR 
              (STRTOUPPER(SUBSTR($file_name, -4)) == '.BMP') OR
              (STRTOUPPER(SUBSTR($file_name, -4)) == '.MP4') OR
              (STRTOUPPER(SUBSTR($file_name, -4)) == '.MP3') OR
              (STRTOUPPER(SUBSTR($file_name, -4)) == '.PDF') 
             )
             {
              //japim mundesin qe ta hapim ne browser
              IF ($post_preview == "Y")
                 {
                  header("Content-Disposition:inline; filename=".$file_name);
                 }
              ELSE
                 {
                  header("Content-Disposition:attachment; filename=".$file_name);
                 }
             }
          ELSE
             {
              header("Content-Disposition:attachment; filename=".$file_name);
             }

          //lexohet permbajtja e skedarit --------------------------------------------------------------
            //LEXIMI SI NJE I TER ----------------------------------------------------------------------
              //$fd             = fopen($file_name_disk, 'rb');
              //IF ($fd === false) return false;
              //$file_data = fread($fd, FILESIZE($path_file_read));
              //fclose($fd);
              //ECHO $file_data;
            //LEXIMI SI NJE I TER ----------------------------------------------------------------------

            //LEXIMI ME PORCIONE -----------------------------------------------------------------------
              $handle = FOPEN($file_name_disk, "rb") or die("Couldn't get handle");
              IF ($handle) 
                 {
                  WHILE (!feof($handle)) 
                        {
                         $buffer = fgets($handle, 4096);
                         ECHO $buffer; // Process buffer here..
                        }
            
                  FCLOSE($handle);
                 }
            //LEXIMI ME PORCIONE -----------------------------------------------------------------------
          //lexohet permbajtja e skedarit --------------------------------------------------------------
         }
     }
//LEXOJME SKEDARIN -------------------------------------------------------------------------------------
?>