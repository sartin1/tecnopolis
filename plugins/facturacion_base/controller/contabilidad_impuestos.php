<?php


require_model('subcuenta.php');
require_model('impuesto.php');

class contabilidad_impuestos extends fs_controller
{
   public $allow_delete;
   public $codsubcuentasop;
   public $codsubcuentarep;
   public $impuesto;
   
   public function __construct()
   {
      parent::__construct(__CLASS__, 'Impuestos', 'contabilidad', FALSE, TRUE);
   }
   
   protected function private_core()
   {
      /// ¿El usuario tiene permiso para eliminar en esta página?
      $this->allow_delete = $this->user->allow_delete_on(__CLASS__);
      
      $this->impuesto = new impuesto();
      
      /// Leemos las subcuentas predeterminadas
      $subcuenta = new subcuenta();
      $this->codsubcuentasop = '';
      $subcuentasop = $subcuenta->get_cuentaesp('IVASOP', $this->empresa->codejercicio);
      if($subcuentasop)
      {
         $this->codsubcuentasop = $subcuentasop->codsubcuenta;
      }
      $this->codsubcuentarep = '';
      $subcuentarep = $subcuenta->get_cuentaesp('IVAREP', $this->empresa->codejercicio);
      if($subcuentarep)
      {
         $this->codsubcuentarep = $subcuentarep->codsubcuenta;
      }
      
      if( isset($_GET['delete']) )
      {
         if(!$this->user->admin)
         {
            $this->new_error_msg('Sólo un administrador puede eliminar impuestos.');
         }
         else
         {
            $impuesto = $this->impuesto->get($_GET['delete']);
            if($impuesto)
            {
               if( $impuesto->delete() )
               {
                  $this->new_message('Impuesto eliminado correctamente.');
               }
               else
                  $this->new_error_msg('Ha sido imposible eliminar el impuesto.');
            }
            else
               $this->new_error_msg('Impuesto no encontrado.');
         }
      }
      else if( isset($_POST['codimpuesto']) )
      {
         $impuesto = $this->impuesto->get($_POST['codimpuesto']);
         if( !$impuesto )
         {
            $impuesto = new impuesto();
            $impuesto->codimpuesto = $_POST['codimpuesto'];
         }
         
         $impuesto->descripcion = $_POST['descripcion'];
         
         $impuesto->codsubcuentarep = NULL;
         if($_POST['codsubcuentarep'] != '')
         {
            $impuesto->codsubcuentarep = $_POST['codsubcuentarep'];
         }
         
         $impuesto->codsubcuentasop = NULL;
         if($_POST['codsubcuentasop'] != '')
         {
            $impuesto->codsubcuentasop = $_POST['codsubcuentasop'];
         }
         
         $impuesto->iva = floatval( $_POST['iva'] );
         $impuesto->recargo = floatval( $_POST['recargo'] );
         
         if( $impuesto->save() )
         {
            $this->new_message("Impuesto ".$impuesto->codimpuesto." guardado correctamente.");
         }
         else
            $this->new_error_msg("¡Error al guardar el impuesto!");
      }
      else if( isset($_GET['set_default']) )
      {
         $this->save_codimpuesto($_GET['set_default']);
      }
   }
}
