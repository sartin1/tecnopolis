<?php

require_model('servicio_cliente.php');
require_model('documento_servicio.php');

/**
 * Description of documentos_facturas
 *
 * @author carlos
 */
class documentos_serviciosimp extends fs_controller
{
   public $documentos1;
   
   public function __construct()
   {
      parent::__construct(__CLASS__, 'Documentos', 'ventas', FALSE, FALSE);
   }
   
   protected function private_core()
   {
      $this->share_extension();
      
      $this->check_documentos();
      $this->documentos1 = array();
      
      if( isset($_GET['folder']) AND isset($_GET['id']) )
      {
         if( isset($_POST['upload']) )
         {
            $this->upload_documento();
         }
         else if( isset($_GET['delete']) )
         {
            $this->delete_documento();
         }
         
         $this->documentos = $this->get_documentos();
      }
   }
   
   private function upload_documento()
   {
      if( is_uploaded_file($_FILES['fdocumento']['tmp_name']) )
      {
         $nuevon = $this->random_string(6).'_'.$_FILES['fdocumento']['name'];
         
         if( copy($_FILES['fdocumento']['tmp_name'], 'documentos/'.$nuevon) )
         {
            $doc = new documento_servicio();
            $doc->ruta = 'documentos/'.$nuevon;
            $doc->nombre = $_FILES['fdocumento']['name'];
            $doc->tamano = filesize(getcwd().'/'.$doc->ruta);
            $doc->usuario = $this->user->nick;
            
            
            if($_GET['folder'] == 'servicioscli')
            {
               $doc->idservicio = $_GET['id'];
            }
            
            if( $doc->save() )
            {
               $this->new_message('Documentos añadido correctamente.');
            }
            else
            {
               $this->new_error_msg('Error al asignar el archivo.');
               @unlink($doc->ruta);
            }
         }
         else
         {
            $this->new_error_msg('Error al mover el archivo.');
         }
      }
   }
   
   private function delete_documento()
   {
      $doc0 = new documento_servicio();
      
      $documento = $doc0->get($_GET['delete']);
      if($documento)
      {
         if( $documento->delete() )
         {
            $this->new_message('Documento eliminado correctamente.');
            @unlink($documento->ruta);
         }
         else
         {
            $this->new_error_msg('Error al eliminar el documento.');
         }
      }
      else
      {
         $this->new_error_msg('Documento no encontrado.');
      }
   }
   
   private function share_extension()
   {
      $extensiones = array(
         
          array(
              'name' => 'documentos_servicioscli',
              'page_from' => __CLASS__,
              'page_to' => 'imprimir_rapido_horizontal',
              'type' => 'tab',
              'text' => '<span class="glyphicon glyphicon-file" aria-hidden="true" title="Documentos"></span>',
              'params' => '&folder=servicioscli'
          ),
          
      );
      foreach($extensiones as $ext)
      {
         $fsext = new fs_extension($ext);
         $fsext->save();
      }
   }
   
   public function url()
   {
      if( isset($_GET['folder']) AND isset($_GET['id']) )
      {
         return 'index.php?page='.__CLASS__.'&folder='.$_GET['folder'].'&id='.$_GET['id'];
      }
      else
         return parent::url();
   }
   
   private function get_documentos()
   {
      $doc = new documento_servicio();
      if($_GET['folder'] == 'servicioscli')
      {
         return $doc->all_from('idservicio', $_GET['id']);
      }
   }
   
   private function check_documentos()
   {
      if( !file_exists('documentos') )
      {
         mkdir('documentos');
      }
      
      if( isset($_GET['folder']) AND isset($_GET['id']) )
      {
         /// comprobamos la antigua rura
         $folder = 'tmp/'.FS_TMP_NAME.'documentos_facturas/'.$_GET['folder'].'/'.$_GET['id'];
         if( file_exists($folder) )
         {
            foreach( scandir($folder) as $f )
            {
               if($f != '.' AND $f != '..')
               {
                  /// movemos a la nueva ruta
                  $nuevon = $this->random_string(6).'_'.(string)$f;
                  if( rename($folder.'/'.$f, 'documentos/'.$nuevon) )
                  {
                     $doc = new documento_servicio();
                     $doc->ruta = 'documentos/'.$nuevon;
                     $doc->nombre = (string)$f;
                     $doc->tamano = filesize(getcwd().'/'.$doc->ruta);
                     $doc->usuario = $this->user->nick;
                     
                     if($_GET['folder'] == 'servicioscli')
                     {
                        $doc->idservicio = $_GET['id'];
                     }
                    
                     
                     if( !$doc->save() )
                     {
                        $this->new_error_msg('Error al mover el archivo.');
                     }
                  }
                  else
                  {
                     $this->new_error_msg('Error al mover el archivo a la nueva ruta.');
                  }
               }
            }
         }
      }
   }
}
