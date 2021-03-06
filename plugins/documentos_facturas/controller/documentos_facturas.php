<?php


require_model('albaran_cliente.php');
require_model('albaran_proveedor.php');
require_model('documento_factura.php');
require_model('factura_cliente.php');
require_model('factura_proveedor.php');
require_model('pedido_cliente.php');
require_model('pedido_proveedor.php');
require_model('presupuesto_cliente.php');

/**
 * Description of documentos_facturas
 *
 * @author carlos
 */
class documentos_facturas extends fs_controller
{
   public $documentos;
   
   public function __construct()
   {
      parent::__construct(__CLASS__, 'Documentos', 'ventas', FALSE, FALSE);
   }
   
   protected function private_core()
   {
      $this->share_extension();
      
      $this->check_documentos();
      $this->documentos = array();
      
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
            $doc = new documento_factura();
            $doc->ruta = 'documentos/'.$nuevon;
            $doc->nombre = $_FILES['fdocumento']['name'];
            $doc->tamano = filesize(getcwd().'/'.$doc->ruta);
            $doc->usuario = $this->user->nick;
            
            if($_GET['folder'] == 'facturascli')
            {
               $doc->idfactura = $_GET['id'];
            }
            else if($_GET['folder'] == 'albaranescli')
            {
               $doc->idalbaran = $_GET['id'];
            }
            else if($_GET['folder'] == 'pedidoscli')
            {
               $doc->idpedido = $_GET['id'];
            }
            else if($_GET['folder'] == 'presupuestoscli')
            {
               $doc->idpresupuesto = $_GET['id'];
            }
            else if($_GET['folder'] == 'facturasprov')
            {
               $doc->idfacturaprov = $_GET['id'];
            }
            else if($_GET['folder'] == 'albaranesprov')
            {
               $doc->idalbaranprov = $_GET['id'];
            }
            else if($_GET['folder'] == 'pedidosprov')
            {
               $doc->idpedidoprov = $_GET['id'];
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
      $doc0 = new documento_factura();
      
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
              'name' => 'documentos_facturascli',
              'page_from' => __CLASS__,
              'page_to' => 'ventas_factura',
              'type' => 'tab',
              'text' => '<span class="glyphicon glyphicon-file" aria-hidden="true" title="Documentos"></span>',
              'params' => '&folder=facturascli'
          ),
          array(
              'name' => 'documentos_albaranescli',
              'page_from' => __CLASS__,
              'page_to' => 'ventas_albaran',
              'type' => 'tab',
              'text' => '<span class="glyphicon glyphicon-file" aria-hidden="true" title="Documentos"></span>',
              'params' => '&folder=albaranescli'
          ),
          array(
              'name' => 'documentos_pedidoscli',
              'page_from' => __CLASS__,
              'page_to' => 'ventas_pedido',
              'type' => 'tab',
              'text' => '<span class="glyphicon glyphicon-file" aria-hidden="true" title="Documentos"></span>',
              'params' => '&folder=pedidoscli'
          ),
          array(
              'name' => 'documentos_presupuestoscli',
              'page_from' => __CLASS__,
              'page_to' => 'ventas_presupuesto',
              'type' => 'tab',
              'text' => '<span class="glyphicon glyphicon-file" aria-hidden="true" title="Documentos"></span>',
              'params' => '&folder=presupuestoscli'
          ),
          array(
              'name' => 'documentos_facturasprov',
              'page_from' => __CLASS__,
              'page_to' => 'compras_factura',
              'type' => 'tab',
              'text' => '<span class="glyphicon glyphicon-file" aria-hidden="true" title="Documentos"></span>',
              'params' => '&folder=facturasprov'
          ),
          array(
              'name' => 'documentos_albaranesprov',
              'page_from' => __CLASS__,
              'page_to' => 'compras_albaran',
              'type' => 'tab',
              'text' => '<span class="glyphicon glyphicon-file" aria-hidden="true" title="Documentos"></span>',
              'params' => '&folder=albaranesprov'
          ),
          array(
              'name' => 'documentos_pedidosprov',
              'page_from' => __CLASS__,
              'page_to' => 'compras_pedido',
              'type' => 'tab',
              'text' => '<span class="glyphicon glyphicon-file" aria-hidden="true" title="Documentos"></span>',
              'params' => '&folder=pedidosprov'
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
      $doc = new documento_factura();
      if($_GET['folder'] == 'facturascli')
      {
         /// comprobamos los albaranes relacionados con esta factura
         $alba = new albaran_cliente();
         foreach($alba->all_from_factura($_GET['id']) as $alb)
         {
            foreach( $doc->all_from('idalbaran', $alb->idalbaran) as $d )
            {
               $d->idfactura = $_GET['id'];
               $d->save();
            }
         }
         
         return $doc->all_from('idfactura', $_GET['id']);
      }
      else if($_GET['folder'] == 'albaranescli')
      {
         if( class_exists('pedido_cliente') )
         {
            /// comprobamos los pedidos relacionados con este albarán
            $pedi = new pedido_cliente();
            foreach($pedi->all_from_albaran($_GET['id']) as $ped)
            {
               foreach( $doc->all_from('idpedido', $ped->idpedido) as $d )
               {
                  $d->idalbaran = $_GET['id'];
                  $d->save();
               }
            }
         }
         
         return $doc->all_from('idalbaran', $_GET['id']);
      }
      else if($_GET['folder'] == 'pedidoscli')
      {
         /// comprobamos los presupuestos relacionados con este pedido
         $presu = new presupuesto_cliente();
         foreach($presu->all_from_pedido($_GET['id']) as $pre)
         {
            foreach( $doc->all_from('idpresupuesto', $pre->idpresupuesto) as $d )
            {
               $d->idpedido = $_GET['id'];
               $d->save();
            }
         }
         
         return $doc->all_from('idpedido', $_GET['id']);
      }
      else if($_GET['folder'] == 'presupuestoscli')
      {
         return $doc->all_from('idpresupuesto', $_GET['id']);
      }
      else if($_GET['folder'] == 'facturasprov')
      {
         /// comprobamos los albaranes relacionados con esta factura
         $alba = new albaran_proveedor();
         foreach($alba->all_from_factura($_GET['id']) as $alb)
         {
            foreach( $doc->all_from('idalbaranprov', $alb->idalbaran) as $d )
            {
               $d->idfacturaprov = $_GET['id'];
               $d->save();
            }
         }
         
         return $doc->all_from('idfacturaprov', $_GET['id']);
      }
      else if($_GET['folder'] == 'albaranesprov')
      {
         if( class_exists('pedido_proveedor') )
         {
            /// comprobamos los pedidos relacionados con este albarán
            $pedi = new pedido_proveedor();
            foreach($pedi->all_from_albaran($_GET['id']) as $ped)
            {
               foreach( $doc->all_from('idpedidoprov', $ped->idpedido) as $d )
               {
                  $d->idalbaranprov = $_GET['id'];
                  $d->save();
               }
            }
         }
         
         return $doc->all_from('idalbaranprov', $_GET['id']);
      }
      else if($_GET['folder'] == 'pedidosprov')
      {
         return $doc->all_from('idpedidoprov', $_GET['id']);
      }
      else
      {
         return array();
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
                     $doc = new documento_factura();
                     $doc->ruta = 'documentos/'.$nuevon;
                     $doc->nombre = (string)$f;
                     $doc->tamano = filesize(getcwd().'/'.$doc->ruta);
                     $doc->usuario = $this->user->nick;
                     
                     if($_GET['folder'] == 'facturascli')
                     {
                        $doc->idfactura = $_GET['id'];
                     }
                     else if($_GET['folder'] == 'facturasprov')
                     {
                        $doc->idfacturaprov = $_GET['id'];
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
