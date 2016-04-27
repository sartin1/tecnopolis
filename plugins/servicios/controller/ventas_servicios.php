<?php

require_model('registro_sat.php');
require_model('detalle_sat.php');
require_model('agente.php');
require_model('articulo.php');
require_model('cliente.php');
require_model('servicio_cliente.php');
require_model('estado_servicio.php');
require_model('detalle_servicio.php');

class ventas_servicios extends fs_controller
{
   public $agente;
   public $articulo;
   public $buscar_lineas;
   public $cliente;
   public $codagente;
   public $codserie;
   public $desde;
   public $hasta;
   public $lineas;
   public $num_resultados;
   public $offset;
   public $order;
   public $resultados;
   public $serie;
   public $estados;
   public $total_resultados;
   public $total_resultados_txt;
   public $editable;
   public $registro_sat;
   public $detalle_sat;
   public $servicios_setup;

   public function __construct()
   {
      parent::__construct(__CLASS__, 'Servicios a clientes', 'ventas');
   }

   protected function private_core()
   {
      //cargamos configuración de servicios
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
            'cal_inicio' => "09:00",
            'usar_direccion'=> 0
         ),
         FALSE
      );

       /*Cargamos traduccion*/
       $fsvar = new fs_var();
       $this->st = $fsvar->array_get(
         array(
            'st_servicio' => "Servicio",
            'st_servicios' => "Servicios",
            'st_material' => "Material",
            'st_material_estado' => "Estado del material entregado",
            'st_accesorios' => "Accesorios que entrega",
            'st_descripcion' => "Descripción de la averia",
            'st_solucion' => "Solución"
         ),
         FALSE
      );
      $this->avisosat = '0';
      if( class_exists('registro_sat') )
      {
         $this->avisosat = '1';
      }

      if( isset($_GET['importar']) )
      {
         $this->importar_sat();
      }

      $servicio = new servicio_cliente();
      $this->agente = new agente();
      $this->serie = new serie();
      $this->estados = new estado_servicio();
      $this->estado = '';
      $this->offset = 0;
      if( isset($_REQUEST['offset']) )
      {
         $this->offset = intval($_REQUEST['offset']);
      }

      $this->order = 'fecha DESC';
      if( isset($_GET['order']) )
      {
         if($_GET['order'] == 'fecha_desc')
         {
            $this->order = 'fecha DESC';
         }
         else if($_GET['order'] == 'fecha_asc')
         {
            $this->order = 'fecha ASC';
         }
         else if($_GET['order'] == 'codigo_desc')
         {
            $this->order = 'idservicio DESC';
         }
         else if($_GET['order'] == 'codigo_asc')
         {
            $this->order = 'idservicio ASC';
         }
         else if($_GET['order'] == 'prioridad_desc')
         {
            $this->order = 'prioridad DESC';
         }
         else if($_GET['order'] == 'prioridad_asc')
         {
            $this->order = 'prioridad ASC';
         }

         setcookie('ventas_serv_order', $this->order, time()+FS_COOKIES_EXPIRE);
      }
      else if( isset($_COOKIE['ventas_serv_order']) )
      {
         $this->order = $_COOKIE['ventas_serv_order'];
      }

      if( isset($_POST['buscar_lineas']) )
      {
         $this->buscar_lineas();
      }
      else if( isset($_REQUEST['buscar_cliente']) )
      {
         $this->buscar_cliente();
      }
      else if( isset($_GET['ref']) )
      {
         $this->template = 'extension/ventas_servicios_articulo';

         $articulo = new articulo();
         $this->articulo = $articulo->get($_GET['ref']);

         $linea = new linea_servicio_cliente();
         $this->resultados = $linea->all_from_articulo($_GET['ref'], $this->offset);
      }
      else
      {
         $this->share_extension();
         $this->cliente = FALSE;
         $this->codagente = '';
         $this->codserie = '';
         $this->desde = '';
         $this->hasta = '';
         $this->num_resultados = '';
         $this->total_resultados = '';
         $this->total_resultados_txt = '';
         $this->editable ='';

         if( isset($_POST['delete']) )
         {
            $this->delete_servicio();
         }
         else
         {
            if( isset($_REQUEST['codcliente']) )
            {
               if($_REQUEST['codcliente'] != '')
               {
                  $cli0 = new cliente();
                  $this->cliente = $cli0->get($_REQUEST['codcliente']);
               }
            }

            if( isset($_REQUEST['codagente']) )
            {
               $this->codagente = $_REQUEST['codagente'];
            }

            if( isset($_REQUEST['estado']) )
            {
               $this->estado = $_REQUEST['estado'];
            }

            if( isset($_REQUEST['editable']) )
            {
               $this->editable = $_REQUEST['editable'];
            }

            if( isset($_REQUEST['codserie']) )
            {
               $this->codserie = $_REQUEST['codserie'];
               $this->desde = $_REQUEST['desde'];
               $this->hasta = $_REQUEST['hasta'];
            }
         }
         $this->buscar();
      }

   }

   private function buscar_cliente()
   {
      /// desactivamos la plantilla HTML
      $this->template = FALSE;

      $cli0 = new cliente();
      $json = array();
      foreach($cli0->search($_REQUEST['buscar_cliente']) as $cli)
      {
         $json[] = array('value' => $cli->nombre, 'data' => $cli->codcliente);
      }

      header('Content-Type: application/json');
      echo json_encode( array('query' => $_REQUEST['buscar_cliente'], 'suggestions' => $json) );
   }

  public function buscar_lineas()
   {
      /// cambiamos la plantilla HTML
      $this->template = 'ajax/ventas_lineas_servicios';

      $this->buscar_lineas = $_POST['buscar_lineas'];
      $linea = new linea_servicio_cliente();

      if( isset($_POST['codcliente']) )
      {
         $this->lineas = $linea->search_from_cliente2($_POST['codcliente'], $this->buscar_lineas, $_POST['buscar_lineas_o'], $this->offset);
      }
      else
      {
         $this->lineas = $linea->search($this->buscar_lineas, $this->offset);
      }
   }

   private function delete_servicio()
   {
      $serv = new servicio_cliente();
      $serv1 = $serv->get($_POST['delete']);
      if ($serv1)
      {
         if ($serv1->delete())
         {
            $this->new_message(ucfirst(FS_SERVICIO) . ' ' . $serv1->codigo . " borrado correctamente.");
         }
         else
            $this->new_error_msg("¡Imposible borrar el " . FS_SERVICIO . "!");
      }
      else
         $this->new_error_msg("¡" . ucfirst(FS_SERVICIO) . " no encontrado!");
   }

   private function share_extension()
   {
      /// añadimos las extensiones para clientes, agentes y artículos
      $extensiones = array(
          array(
              'name' => 'servicios_cliente',
              'page_from' => __CLASS__,
              'page_to' => 'ventas_cliente',
              'type' => 'button',
              'text' => '<span class="glyphicon glyphicon-list" aria-hidden="true"></span> &nbsp; '.ucfirst(FS_SERVICIO),
              'params' => ''
          ),
          array(
              'name' => 'servicios_agente',
              'page_from' => __CLASS__,
              'page_to' => 'admin_agente',
              'type' => 'button',
              'text' => '<span class="glyphicon glyphicon-list" aria-hidden="true"></span> &nbsp; '.ucfirst(FS_SERVICIO) . ' de cliente',
              'params' => ''
          ),
          array(
              'name' => 'servicios_articulo',
              'page_from' => __CLASS__,
              'page_to' => 'ventas_articulo',
              'type' => 'tab_button',
              'text' => '<span class="glyphicon glyphicon-list" aria-hidden="true"></span> &nbsp; '.ucfirst(FS_SERVICIO) . ' de cliente',
              'params' => ''
          ),
      );
      foreach ($extensiones as $ext)
      {
         $fsext0 = new fs_extension($ext);
         if (!$fsext0->save())
         {
            $this->new_error_msg('Imposible guardar los datos de la extensión ' . $ext['name'] . '.');
         }
      }
   }

   private function buscar()
   {
      $this->resultados = array();
      $this->num_resultados = 0;
      $query = $this->agente->no_html( strtolower($this->query) );
      $sql = " FROM servicioscli ";
      $where = 'WHERE ';

      if($this->query != '')
      {
         $sql .= $where;
         if( is_numeric($query) )
         {
            $sql .= "(codigo LIKE '%".$query."%' OR numero2 LIKE '%".$query."%' OR observaciones LIKE '%".$query."%'"
                    . "OR material LIKE '%".$query."%'"
                    . "OR material_estado LIKE '%".$query."%'"
                    . "OR accesorios LIKE '%" . $query . "%'"
                    . "OR descripcion LIKE '%" . $query . "%'"
                    . "OR solucion LIKE '%" . $query . "%'";

            if ($this->servicios_setup['usar_direccion'])
            {
               $sql .= " OR direccion LIKE '%" . $query . "%'";
            }

            $sql .= ")";
         }
         else
         {
            $sql .= "(lower(codigo) LIKE '%" . $query . "%' OR lower(numero2) LIKE '%" . $query . "%' "
                    . "OR lower(observaciones) LIKE '%" . str_replace(' ', '%', $query) . "%'"
                    . "OR lower(material) LIKE '%" . $query . "%'"
                    . "OR lower(material_estado) LIKE '%".$query."%'"
                    . "OR lower(accesorios) LIKE '%".$query."%'"
                    . "OR lower(descripcion) LIKE '%".$query."%'"
                    . "OR lower(solucion) LIKE '%".$query."%'";

            if ($this->servicios_setup['usar_direccion'])
            {
               $sql .= " OR lower(direccion) LIKE '%" . $query . "%'";
            }

            $sql .= ")";
         }
         $where = ' AND ';
      }

      if($this->codagente != '')
      {
         $sql .= $where."codagente = ".$this->agente->var2str($this->codagente);
         $where = ' AND ';
      }

      if($this->estado != '')
      {
         $sql .= $where."idestado = ".$this->estado;
         $where = ' AND ';
      }

      if($this->cliente)
      {
         $sql .= $where."codcliente = ".$this->agente->var2str($this->cliente->codcliente);
         $where = ' AND ';
      }

      if($this->codserie != '')
      {
         $sql .= $where."codserie = ".$this->agente->var2str($this->codserie);
         $where = ' AND ';
      }

      if($this->desde != '')
      {
         $sql .= $where."fecha >= ".$this->agente->var2str($this->desde);
         $where = ' AND ';
      }

      if($this->hasta != '')
      {
         $sql .= $where."fecha <= ".$this->agente->var2str($this->hasta);
         $where = ' AND ';
      }

      if($this->editable != '')
      {
         $sql .= $where."idalbaran IS NULL";
         $where = ' AND ';
      }

      $data = $this->db->select("SELECT COUNT(idservicio) as total".$sql);
      if($data)
      {
         $this->num_resultados = intval($data[0]['total']);

         $data2 = $this->db->select_limit("SELECT *".$sql." ORDER BY ".$this->order, FS_ITEM_LIMIT, $this->offset);
         if($data2)
         {
            foreach($data2 as $d)
            {
               $this->resultados[] = new servicio_cliente($d);
            }
         }

         $data2 = $this->db->select("SELECT SUM(total) as total".$sql);
         if($data2)
         {
            $this->total_resultados = floatval($data2[0]['total']);
            $this->total_resultados_txt = 'Suma total de los resultados:';
         }
      }
   }

   private function importar_sat()
   {
      $this->registro_sat = new registro_sat();
      $this->detalle_sat = new detalle_sat();
      $this->cliente = new cliente();
      $importados = 0;
      $importados_det = 0;
      $data = $this->db->select("SELECT * FROM registros_sat;");
      if($data)
      {
         foreach($data as $d)
         {
            $servicio = $this->registro_sat->get($d['nsat']);
            if($servicio)
            {
               $servicio = new servicio_cliente();
               $servicio->numero2 = "SAT_".$d['nsat'];
               $servicio->fecha = $d['fentrada'];
               if(isset($d['fcomienzo']))
               {
                   $servicio->fechainicio = Date('d-m-Y H:i', strtotime($d['fcomienzo']));
               }
               if(isset($d['ffin']))
               {
                   $servicio->fechafin = Date('d-m-Y H:i', strtotime($d['ffin']));
               }
               //obtenemos ejercicio
               $eje0 = new ejercicio();
               $ejercicio = $eje0->get_by_fecha($d['fentrada']);
               $servicio->codejercicio = $ejercicio->codejercicio;

               $servicio->material = $d['modelo'];
               $servicio->descripcion = $d['averia'];
               $servicio->accesorios = $d['accesorios'];
               $servicio->codcliente = $d['codcliente'];
               $servicio->observaciones  = $d['observaciones'];
               $servicio->codagente = $d['codagente'];
               $servicio->idestado = '1';
               $servicio->prioridad = $d['prioridad'];
               //obtenemos cliente
               $cliente0 = new cliente();
               $cliente = $cliente0->get($d['codcliente']);
               $servicio->nombrecliente = $cliente->nombre;

               $servicio->codserie = $this->empresa->codserie;
               $servicio->codpago = $this->empresa->codpago;

               if( $servicio->save() )
               {
                  $importados++;
               }

               //Importamos Detalles:
               $data2 = $this->db->select("SELECT * FROM detalles_sat WHERE nsat=".$d['nsat'].";");
               if($data2)
                {
                   foreach($data2 as $d2)
                   {
                      $detalle = $this->detalle_sat->get($d2['id']);
                        if($detalle)
                        {
                            $detalle = new detalle_servicio();
                            $detalle->idservicio = $servicio->idservicio;
                            $detalle->descripcion = $d2['descripcion'];
                            $detalle->fecha= $d2['fecha'];
                            if( $detalle->save() )
                                {
                                   $importados_det++;
                                }
                        }
                   }
                }

            }
         }
      }

      $this->new_message($importados.' registros SAT importados.');
      $this->new_message($importados_det.' detalles SAT importados.');
      $this->avisosat = '2';
   }

    public function paginas()
   {
      $codcliente = '';
      if($this->cliente)
      {
         $codcliente = $this->cliente->codcliente;
      }
      $total = $this->num_resultados;

      $url = $this->url()."&query=".$this->query
                 ."&codserie=".$this->codserie
                 ."&codagente=".$this->codagente
                 ."&estado=".$this->estado
                 ."&codcliente=".$codcliente
                 ."&desde=".$this->desde
                 ."&hasta=".$this->hasta
                 ."&offset=".($this->offset-FS_ITEM_LIMIT);

      $paginas = array();
      $i = 0;
      $num = 0;
      $actual = 1;

      /// añadimos todas la página
      while($num < $total)
      {
         $paginas[$i] = array(
             'url' => $url."&offset=".($i*FS_ITEM_LIMIT),
             'num' => $i + 1,
             'actual' => ($num == $this->offset)
         );

         if($num == $this->offset)
         {
            $actual = $i;
         }

         $i++;
         $num += FS_ITEM_LIMIT;
      }

      /// ahora descartamos
      foreach($paginas as $j => $value)
      {
         $enmedio = intval($i/2);

         /**
          * descartamos todo excepto la primera, la última, la de enmedio,
          * la actual, las 5 anteriores y las 5 siguientes
          */
         if( ($j>1 AND $j<$actual-5 AND $j!=$enmedio) OR ($j>$actual+5 AND $j<$i-1 AND $j!=$enmedio) )
         {
            unset($paginas[$j]);
         }
      }

      if( count($paginas) > 1 )
      {
         return $paginas;
      }
      else
      {
         return array();
      }
   }
}
