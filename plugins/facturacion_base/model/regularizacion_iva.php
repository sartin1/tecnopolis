<?php

require_model('asiento.php');
require_model('partida.php');

/**
 * Una regularización de IVA.
 */
class regularizacion_iva extends fs_model
{
   /**
    * Clave primaria.
    * @var type 
    */
   public $idregiva;
   
   /**
    * ID del asiento generado.
    * @var type 
    */
   public $idasiento;
   public $codejercicio;
   public $fechaasiento;
   public $fechafin;
   public $fechainicio;
   public $periodo;
   
   public function __construct($r = FALSE)
   {
      parent::__construct('co_regiva', 'plugins/facturacion_base/');
      if($r)
      {
         $this->idregiva = $this->intval($r['idregiva']);
         $this->idasiento = $this->intval($r['idasiento']);
         $this->codejercicio = $r['codejercicio'];
         $this->fechaasiento = Date('d-m-Y', strtotime($r['fechaasiento']));
         $this->fechafin = Date('d-m-Y', strtotime($r['fechafin']));
         $this->fechainicio = Date('d-m-Y', strtotime($r['fechainicio']));
         $this->periodo = $r['periodo'];
      }
      else
      {
         $this->idregiva = NULL;
         $this->idasiento = NULL;
         $this->codejercicio = NULL;
         $this->fechaasiento = NULL;
         $this->fechafin = NULL;
         $this->fechainicio = NULL;
         $this->periodo = NULL;
      }
   }
   
   protected function install()
   {
      return '';
   }
   
   public function url()
   {
      if( is_null($this->idregiva) )
      {
         return 'index.php?page=contabilidad_regusiva';
      }
      else
         return 'index.php?page=contabilidad_regusiva&id='.$this->idregiva;
   }
   
   public function asiento_url()
   {
      if( is_null($this->idasiento) )
      {
         return 'index.php?page=contabilidad_asientos';
      }
      else
         return 'index.php?page=contabilidad_asiento&id='.$this->idasiento;
   }
   
   public function ejercicio_url()
   {
      if( is_null($this->codejercicio) )
      {
         return 'index.php?page=contabilidad_ejercicios';
      }
      else
         return 'index.php?page=contabilidad_ejercicio&cod='.$this->codejercicio;
   }
   
   public function get_partidas()
   {
      if( isset($this->idasiento) )
      {
         $partida = new partida();
         return $partida->all_from_asiento($this->idasiento);
      }
      else
         return FALSE;
   }
   
   /*
    * Devuelve la regularización de IVA correspondiente a esa fecha,
    * es decir, la regularización cuya fecha de inicio sea anterior
    * a la fecha proporcionada y su fecha de fin sea posterior a la fecha
    * proporcionada. Así puedes saber si el periodo sigue abierto para poder
    * facturar.
    */
   public function get_fecha_inside($fecha)
   {
      $sql = "SELECT * FROM ".$this->table_name." WHERE fechainicio <= ".$this->var2str($fecha)
              ." AND fechafin >= ".$this->var2str($fecha).";";
      
      $data = $this->db->select($sql);
      if($data)
      {
         return new regularizacion_iva($data[0]);
      }
      else
         return FALSE;
   }
   
   public function get($id)
   {
      $data = $this->db->select("SELECT * FROM ".$this->table_name." WHERE idregiva = ".$this->var2str($id).";");
      if($data)
      {
         return new regularizacion_iva($data[0]);
      }
      else
         return FALSE;
   }
   
   public function exists()
   {
      if( is_null($this->idregiva) )
      {
         return FALSE;
      }
      else
      {
         return $this->db->select("SELECT * FROM ".$this->table_name
                 ." WHERE idregiva = ".$this->var2str($this->idregiva).";");
      }
   }
   
   public function test()
   {
      return TRUE;
   }
   
   public function save()
   {
      if( $this->exists() )
      {
         $sql = "UPDATE ".$this->table_name." SET codejercicio = ".$this->var2str($this->codejercicio)
                 .", fechaasiento = ".$this->var2str($this->fechaasiento)
                 .", fechafin = ".$this->var2str($this->fechafin)
                 .", fechainicio = ".$this->var2str($this->fechainicio)
                 .", idasiento = ".$this->var2str($this->idasiento)
                 .", periodo = ".$this->var2str($this->periodo)
                 ."  WHERE idregiva = ".$this->var2str($this->idregiva).";";
         
         return $this->db->exec($sql);
      }
      else
      {
         $sql = "INSERT INTO ".$this->table_name." (codejercicio,fechaasiento,fechafin,
            fechainicio,idasiento,periodo) VALUES (".$this->var2str($this->codejercicio)
                 .",".$this->var2str($this->fechaasiento)
                 .",".$this->var2str($this->fechafin)
                 .",".$this->var2str($this->fechainicio)
                 .",".$this->var2str($this->idasiento)
                 .",".$this->var2str($this->periodo).");";
         
         if( $this->db->exec($sql) )
         {
            $this->idregiva = $this->db->lastval();
            return TRUE;
         }
         else
            return FALSE;
      }
   }
   
   public function delete()
   {
      /// si hay un asiento asociado lo eliminamos
      if( isset($this->idasiento) )
      {
         $asiento = new asiento();
         $as0 = $asiento->get($this->idasiento);
         if($as0)
         {
            $as0->delete();
         }
      }
      
      return $this->db->exec("DELETE FROM ".$this->table_name." WHERE idregiva = ".$this->var2str($this->idregiva).";");
   }
   
   public function all()
   {
      $reglist = array();
      
      $data = $this->db->select("SELECT * FROM ".$this->table_name." ORDER BY fechafin DESC;");
      if($data)
      {
         foreach($data as $r)
         {
            $reglist[] = new regularizacion_iva($r);
         }
      }
      
      return $reglist;
   }
}
