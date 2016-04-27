<?php


require_model('albaran_cliente.php');
require_model('albaran_proveedor.php');
require_model('articulo.php');
require_model('asiento.php');
require_once 'plugins/facturacion_base/extras/libromayor.php';
require_once 'plugins/facturacion_base/extras/inventarios_balances.php';

class facturacion_base_cron
{
   public function __construct(&$db)
   {
      $alb_cli = new albaran_cliente();
      echo "Ejecutando tareas para los ".FS_ALBARANES." de cliente...\n";
      $alb_cli->cron_job();
      
      $alb_pro = new albaran_proveedor();
      echo "Ejecutando tareas para los ".FS_ALBARANES." de proveedor...\n";
      $alb_pro->cron_job();
      
      $articulo = new articulo();
      echo "Ejecutando tareas para los artículos...";
      $articulo->cron_job();
      
      $asiento = new asiento();
      echo "\nEjecutando tareas para los asientos...\n";
      $asiento->cron_job();
      
      if(FS_LIBROS_CONTABLES)
      {
         $libro = new libro_mayor();
         echo "Generamos el libro mayor para cada subcuenta y el libro diario para cada ejercicio...";
         $libro->cron_job();
         
         $inventarios_balances = new inventarios_balances($db);
         echo "\nGeneramos el libro de inventarios y balances para cada ejercicio...";
         $inventarios_balances->cron_job();
      }
      else
      {
         $libro = new libro_mayor();
         echo "Comprobamos algunas subcuentas...";
         $libro->cron_job();
      }
   }
}

new facturacion_base_cron($db);