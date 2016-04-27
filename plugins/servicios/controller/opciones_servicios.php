<?php


require_model('estado_servicio.php');

/**
 * Description of opciones_servicios
 *
 * @author carlos
 */
class opciones_servicios extends fs_controller
{
   public $allow_delete;
   public $estado;
   public $maps_api_key;
   public $servicios_setup;
   public $st;

   public function __construct()
   {
      parent::__construct(__CLASS__, 'Opciones', 'Servicios', FALSE, FALSE);
   }

   protected function private_core()
   {
      /// ¿El usuario tiene permiso para eliminar en esta página?
      $this->allow_delete = $this->user->allow_delete_on(__CLASS__);

      $this->check_menu();
      $this->share_extensions();

      $this->estado = new estado_servicio();

      /// cargamos la configuración
      $fsvar = new fs_var();
      $this->servicios_setup = $fsvar->array_get(
         array(
            'servicios_diasfin' => 10,
            'servicios_material' => 0,
            'servicios_mostrar_material' => 0,
            'servicios_material_estado' => 0,
            'servicios_mostrar_material_estado' => 0,
            'servicios_accesorios' => 0,
            'servicios_mostrar_accesorios' => 0,
            'servicios_descripcion' => 0,
            'servicios_mostrar_descripcion' => 0,
            'servicios_solucion' => 0,
            'servicios_mostrar_solucion' => 0,
            'servicios_fechafin' => 0,
            'servicios_mostrar_fechafin' => 0,
            'servicios_fechainicio' => 0,
            'servicios_mostrar_fechainicio' => 0,
            'servicios_mostrar_garantia' => 0,
            'servicios_garantia' => 0,
            'servicios_condiciones' => "Condiciones de servicio",
            'st_servicio' => "Servicio",
            'st_servicios' => "Servicios",
            'st_material' => "Material",
            'st_material_estado' => "Estado del material entregado",
            'st_accesorios' => "Accesorios que entrega",
            'st_descripcion' => "Descripción de la averia",
            'st_solucion' => "Solución",
            'st_fechainicio' => "Fecha de inicio",
            'st_fechafin' => "Fecha de finalización",
            'st_garantia' => "Garantía",
            'cal_inicio' => "09:00",
            'cal_fin' => "20:00",
            'cal_intervalo' => "30",
            'usar_direccion'=> 0
         ),
         FALSE
      );

      if( isset($_POST['servicios_setup']) )
      {
         $this->servicios_setup['servicios_diasfin'] = intval($_POST['diasfin']);
         $this->servicios_setup['servicios_material'] = ( isset($_POST['servicios_material']) ? 1 : 0 );
         $this->servicios_setup['servicios_material_estado'] = ( isset($_POST['servicios_material_estado']) ? 1 : 0 );
         $this->servicios_setup['servicios_accesorios'] = ( isset($_POST['servicios_accesorios']) ? 1 : 0 );
         $this->servicios_setup['servicios_descripcion'] = ( isset($_POST['servicios_descripcion']) ? 1 : 0 );
         $this->servicios_setup['servicios_solucion'] = ( isset($_POST['servicios_solucion']) ? 1 : 0 );
         $this->servicios_setup['servicios_fechafin'] = ( isset($_POST['servicios_fechafin']) ? 1 : 0 );
         $this->servicios_setup['servicios_fechainicio'] = ( isset($_POST['servicios_fechainicio']) ? 1 : 0 );
         $this->servicios_setup['servicios_garantia'] = ( isset($_POST['servicios_garantia']) ? 1 : 0 );
         $this->servicios_setup['servicios_mostrar_material'] = ( isset($_POST['servicios_mostrar_material']) ? 1 : 0 );
         $this->servicios_setup['servicios_mostrar_material_estado'] = ( isset($_POST['servicios_mostrar_material_estado']) ? 1 : 0 );
         $this->servicios_setup['servicios_mostrar_accesorios'] = ( isset($_POST['servicios_mostrar_accesorios']) ? 1 : 0 );
         $this->servicios_setup['servicios_mostrar_descripcion'] = ( isset($_POST['servicios_mostrar_descripcion']) ? 1 : 0 );
         $this->servicios_setup['servicios_mostrar_solucion'] = ( isset($_POST['servicios_mostrar_solucion']) ? 1 : 0 );
         $this->servicios_setup['servicios_mostrar_fechafin'] = ( isset($_POST['servicios_mostrar_fechafin']) ? 1 : 0 );
         $this->servicios_setup['servicios_mostrar_fechainicio'] = ( isset($_POST['servicios_mostrar_fechainicio']) ? 1 : 0 );
         $this->servicios_setup['servicios_mostrar_garantia'] = ( isset($_POST['servicios_mostrar_garantia']) ? 1 : 0 );
         $this->servicios_setup['servicios_condiciones'] = $fsvar->no_html($_POST['condiciones']);
         $this->servicios_setup['st_servicio'] = $_POST['st_servicio'];
         $this->servicios_setup['st_servicios'] = $_POST['st_servicios'];
         $this->servicios_setup['st_material'] = $_POST['st_material'];
         $this->servicios_setup['st_material_estado'] = $_POST['st_material_estado'];
         $this->servicios_setup['st_accesorios'] = $_POST['st_accesorios'];
         $this->servicios_setup['st_descripcion'] = $_POST['st_descripcion'];
         $this->servicios_setup['st_solucion'] = $_POST['st_solucion'];
         $this->servicios_setup['st_fechainicio'] = $_POST['st_fechainicio'];
         $this->servicios_setup['st_fechafin'] = $_POST['st_fechafin'];
         $this->servicios_setup['st_garantia'] = $_POST['st_garantia'];
         $this->servicios_setup['cal_inicio'] = $_POST['cal_inicio'];
         $this->servicios_setup['cal_fin'] = $_POST['cal_fin'];
         $this->servicios_setup['cal_intervalo'] = $_POST['cal_intervalo'];
         $this->servicios_setup['usar_direccion'] = $_POST['usar_direccion'];

         if( $fsvar->array_save($this->servicios_setup) )
         {
            $this->new_message('Datos guardados correctamente.');
         }
         else
            $this->new_error_msg('Error al guardar los datos.');
      }
      else if( isset($_GET['delete_estado']) )
      {
         $estado = $this->estado->get($_GET['delete_estado']);
         if($estado)
         {
            if( $estado->delete() )
            {
               $this->new_message('Estado eliminado correctamente.');
            }
            else
               $this->new_error_msg('Error al eliminar el estado.');
         }
         else
            $this->new_error_msg('Estado no encontrado.');
      }
      else if( isset($_POST['id_estado']) )
      {
         $estado = $this->estado->get($_POST['id_estado']);
         if(!$estado)
         {
            $estado = new estado_servicio();
            $estado->id = intval($_POST['id_estado']);
         }
         $estado->descripcion = $_POST['descripcion'];
         $estado->color = $_POST['color'];
         $estado->activo = isset($_POST['activo']);
         $estado->albaran = isset($_POST['albaran']);

         if( $estado->save() )
         {
            $this->new_message('Estado guardado correctamente.');
         }
         else
            $this->new_error_msg('Error al guardar el estado.');
      }
   }

   private function share_extensions()
   {
      $fsext = new fs_extension();
      $fsext->name = 'opciones_servicios';
      $fsext->from = __CLASS__;
      $fsext->to = 'ventas_servicios';
      $fsext->type = 'button';
      $fsext->text = '<span class="glyphicon glyphicon-cog" aria-hidden="true">'
              . '</span><span class="hidden-xs">&nbsp; Opciones</span>';
      $fsext->save();
   }

   private function check_menu()
   {
      if( !$this->page->get('ventas_servicios') )
      {
         if( file_exists(__DIR__) )
         {
            /// activamos las páginas del plugin
            foreach( scandir(__DIR__) as $f)
            {
               if( is_string($f) AND strlen($f) > 0 AND !is_dir($f) AND $f != __CLASS__.'.php' )
               {
                  $page_name = substr($f, 0, -4);

                  require_once __DIR__.'/'.$f;
                  $new_fsc = new $page_name();

                  if( !$new_fsc->page->save() )
                  {
                     $this->new_error_msg("Imposible guardar la página ".$page_name);
                  }

                  unset($new_fsc);
               }
            }
         }
         else
         {
            $this->new_error_msg('No se encuentra el directorio '.__DIR__);
         }

         $this->load_menu(TRUE);
      }
   }
}
