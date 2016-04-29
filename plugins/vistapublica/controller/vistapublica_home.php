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
class vistapublica_home extends fs_controller
{

   public function __construct()
   {
      parent::__construct(__CLASS__, 'Home', 'Público');
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
     $this->template = 'publico/portada';
     $this->page_title = $this->empresa->nombrecorto;


      $this->agente = new agente();
      $this->estado = '';

      if( isset($_REQUEST['buscar_cliente']) )
      {
         $this->buscar_cliente();
      }
      else if( isset($_REQUEST['buscar_cliente1']) )
      {
         $this->buscar_cliente1();
      }
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

         $this->buscar();
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

   public function buscar_cliente1()
   {
      /// desactivamos la plantilla HTML
      $this->template = FALSE;

      $cli0 = new cliente();
      $json = array();
      foreach($cli0->search($_REQUEST['buscar_cliente1']) as $cli)
      {
         $json[] = array('value' => $cli->nombre, 'data' => $cli->codcliente);
      }

      header('Content-Type: application/json');
      echo json_encode( array('query' => $_REQUEST['buscar_cliente1'], 'suggestions' => $json) );
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
