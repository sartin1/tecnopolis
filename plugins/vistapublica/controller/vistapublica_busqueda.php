<?php

/*
 * Copyright (C) 2015-2016  Carlos Garcia Gomez  neorazorx@gmail.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

 require_model('registro_sat.php');
 require_model('detalle_sat.php');
 require_model('agente.php');
 require_model('articulo.php');
 require_model('cliente.php');
 require_model('servicio_cliente.php');
 require_model('estado_servicio.php');
 require_model('detalle_servicio.php');
/**
 * Description of informame_home
 *
 * @author carlos
 */
class vistapublica_busqueda extends fs_controller
{

   public function __construct()
   {
      parent::__construct(__CLASS__, ' ', '');
   }

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

   protected function private_core()
   {

   }

   protected function public_core()
   {
    $this->share_extension();
     $this->template = 'publico/busqueda';
     $this->page_title = $this->empresa->nombrecorto;
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

   public function buscar_cliente()
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

   public function buscar()
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


      if($this->cliente)
      {
         $sql .= $where."codcliente = ".$this->agente->var2str($this->cliente->codcliente);
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

   private function share_extension()
   {
      $extensiones = array(
          array(
              'name' => 'vistapublica_busqueda',
              'page_from' => __CLASS__,
              'page_to' => 'vistapublica_home',
              'type' => 'tab',
              'text' => '<span class="glyphicon glyphicon-file" aria-hidden="true" title="Documentos"></span>',
              'params' => ''
          ),
        );
        foreach($extensiones as $ext)
        {
           $fsext = new fs_extension($ext);
           $fsext->save();
        }
      }
   public function full_url()
   {
      $url = $this->empresa->web;

      if( isset($_SERVER['SERVER_NAME']) )
      {
         if($_SERVER['SERVER_NAME'] == 'localhost')
         {
            $url = 'http://'.$_SERVER['SERVER_NAME'];

            if( isset($_SERVER['REQUEST_URI']) )
            {
               $aux = parse_url( str_replace('/index.php', '', $_SERVER['REQUEST_URI']) );
               $url .= $aux['path'];
            }
         }
      }

      return $url;
   }


}
