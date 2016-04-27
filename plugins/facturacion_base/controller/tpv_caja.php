<?php


require_model('almacen.php');
require_model('caja.php');
require_model('serie.php');
require_model('terminal_caja.php');

class tpv_caja extends fs_controller
{
   public $allow_delete;
   public $almacen;
   public $caja;
   public $offset;
   public $resultados;
   public $serie;
   public $terminal;
   public $terminales;
   
   public function __construct()
   {
      parent::__construct(__CLASS__, 'Configuración', 'Cajero', FALSE, TRUE);
   }
   
   protected function private_core()
   {
      /// ¿El usuario tiene permiso para eliminar en esta página?
      $this->allow_delete = $this->user->allow_delete_on(__CLASS__);
      
      $this->almacen = new almacen();
      $this->caja = new caja();
      $this->serie = new serie();
      $this->terminal = new terminal_caja();
      $terminal = new terminal_caja();
      
      if( isset($_POST['nuevot']) ) /// nuevo terminal
      {
         $terminal->codalmacen = $_POST['codalmacen'];
         $terminal->codserie = $_POST['codserie'];
         
         $terminal->codcliente = NULL;
         if($_POST['codcliente'] != '')
         {
            $terminal->codcliente = $_POST['codcliente'];
         }
         
         $terminal->anchopapel = intval($_POST['anchopapel']);
         $terminal->comandoapertura = $_POST['comandoapertura'];
         $terminal->comandocorte = $_POST['comandocorte'];
         $terminal->num_tickets = intval($_POST['num_tickets']);
         
         if( $terminal->save() )
         {
            $this->new_message('Terminal añadido correctamente. Ya puedes usarlo desde el TPV.');
         }
         else
            $this->new_error_msg('Error al guardar los datos.');
      }
      else if( isset($_POST['idt']) ) /// editar terminal
      {
         $t2 = $terminal->get($_POST['idt']);
         if($t2)
         {
            $t2->codalmacen = $_POST['codalmacen'];
            $t2->codserie = $_POST['codserie'];
            
            $t2->codcliente = NULL;
            if($_POST['codcliente'] != '')
            {
               $t2->codcliente = $_POST['codcliente'];
            }
            
            $t2->anchopapel = intval($_POST['anchopapel']);
            $t2->comandoapertura = $_POST['comandoapertura'];
            $t2->comandocorte = $_POST['comandocorte'];
            $t2->num_tickets = intval($_POST['num_tickets']);
            
            if( $t2->save() )
            {
               $this->new_message('Datos guardados correctamente.');
            }
            else
               $this->new_error_msg('Error al guardar los datos.');
         }
         else
            $this->new_error_msg('Terminal no encontrado.');
      }
      else if( isset($_GET['deletet']) ) /// eliminar terminal
      {
         if($this->user->admin)
         {
            $t2 = $terminal->get($_GET['deletet']);
            if($t2)
            {
               if( $t2->delete() )
               {
                  $this->new_message('Terminal eliminado correctamente.');
               }
               else
                  $this->new_error_msg('Error al eliminar el terminal.');
            }
            else
               $this->new_error_msg('Terminal no encontrado.');
         }
         else
            $this->new_error_msg("Tienes que ser administrador para poder eliminar terminales.");
      }
      else if( isset($_GET['delete']) ) /// eliminar caja
      {
         if($this->user->admin)
         {
            $caja2 = $this->caja->get($_GET['delete']);
            if($caja2)
            {
               if( $caja2->delete() )
               {
                  $this->new_message("Caja eliminada correctamente.");
               }
               else
                  $this->new_error_msg("¡Imposible eliminar la caja!");
            }
            else
               $this->new_error_msg("Caja no encontrada.");
         }
         else
            $this->new_error_msg("Tienes que ser administrador para poder eliminar cajas.");
      }
      else if( isset($_GET['cerrar']) )
      {
         if($this->user->admin)
         {
            $caja2 = $this->caja->get($_GET['cerrar']);
            if($caja2)
            {
               $caja2->fecha_fin = Date('d-m-Y H:i:s');
               if( $caja2->save() )
               {
                  $this->new_message("Caja cerrada correctamente.");
               }
               else
                  $this->new_error_msg("¡Imposible cerrar la caja!");
            }
            else
               $this->new_error_msg("Caja no encontrada.");
         }
         else
            $this->new_error_msg("Tienes que ser administrador para poder cerrar cajas.");
      }
      
      $this->offset = 0;
      if( isset($_GET['offset']) )
      {
         $this->offset = intval($_GET['offset']);
      }
      
      $this->resultados = $this->caja->all($this->offset);
      $this->terminales = $terminal->all();
   }
   
   public function anterior_url()
   {
      $url = '';
      
      if($this->offset > 0)
      {
         $url = $this->url()."&offset=".($this->offset-FS_ITEM_LIMIT);
      }
      
      return $url;
   }
   
   public function siguiente_url()
   {
      $url = '';
      
      if( count($this->resultados) == FS_ITEM_LIMIT )
      {
         $url = $this->url()."&offset=".($this->offset+FS_ITEM_LIMIT);
      }
      
      return $url;
   }
}
