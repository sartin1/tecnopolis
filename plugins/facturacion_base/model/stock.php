<?php


require_model('almacen.php');
require_model('articulo.php');

/**
 * La cantidad en inventario de un artículo en un almacén concreto.
 */
class stock extends fs_model
{
   /**
    * Clave primaria.
    * @var type 
    */
   public $idstock;
   
   public $codalmacen;
   
   public $referencia;
   
   public $nombre;
   
   public $cantidad;
   
   public $reservada;
   
   public $disponible;
   
   public $pterecibir;
   
   public $stockmin;
   
   public $stockmax;
   
   public $cantidadultreg;
   
   public $ubicacion;
   
   public function __construct($s=FALSE)
   {
      parent::__construct('stocks', 'plugins/facturacion_base/');
      if($s)
      {
         $this->idstock = $this->intval($s['idstock']);
         $this->codalmacen = $s['codalmacen'];
         $this->referencia = $s['referencia'];
         $this->nombre = $s['nombre'];
         $this->cantidad = floatval($s['cantidad']);
         $this->reservada = floatval($s['reservada']);
         $this->disponible = floatval($s['disponible']);
         $this->pterecibir = floatval($s['pterecibir']);
         $this->stockmin = floatval($s['stockmin']);
         $this->stockmax = floatval($s['stockmax']);
         $this->cantidadultreg = floatval($s['cantidadultreg']);
         $this->ubicacion = $s['ubicacion'];
      }
      else
      {
         $this->idstock = NULL;
         $this->codalmacen = NULL;
         $this->referencia = NULL;
         $this->nombre = '';
         $this->cantidad = 0;
         $this->reservada = 0;
         $this->disponible = 0;
         $this->pterecibir = 0;
         $this->stockmin = 0;
         $this->stockmax = 0;
         $this->cantidadultreg = 0;
         $this->ubicacion = NULL;
      }
   }
   
   protected function install()
   {
      /**
       * La tabla stocks tiene claves ajenas a artículos y almacenes,
       * por eso creamos un objeto de cada uno, para forzar la comprobación
       * de las tablas.
       */
      new almacen();
      new articulo();
      
      return '';
   }
   
   public function set_cantidad($c = 0)
   {
      $this->cantidad = floatval($c);
      
      if($this->cantidad < 0 AND !FS_STOCK_NEGATIVO)
      {
         $this->cantidad = 0;
      }
      
      $this->disponible = $this->cantidad - $this->reservada;
   }
   
   public function sum_cantidad($c = 0)
   {
      $this->cantidad += floatval($c);
      
      if($this->cantidad < 0 AND !FS_STOCK_NEGATIVO)
      {
         $this->cantidad = 0;
      }
      
      $this->disponible = $this->cantidad - $this->reservada;
   }
   
   public function get($id)
   {
      $data = $this->db->select("SELECT * FROM ".$this->table_name." WHERE idstock = ".$this->var2str($id).";");
      if($data)
      {
         return new stock($data[0]);
      }
      else
         return FALSE;
   }
   
   public function get_by_referencia($ref)
   {
      $data = $this->db->select("SELECT * FROM ".$this->table_name." WHERE referencia = ".$this->var2str($ref).";");
      if($data)
      {
         return new stock($data[0]);
      }
      else
         return FALSE;
   }
   
   public function exists()
   {
      if( is_null($this->idstock) )
      {
         return FALSE;
      }
      else
         return $this->db->select("SELECT * FROM ".$this->table_name." WHERE idstock = ".$this->var2str($this->idstock).";");
   }
   
   public function save()
   {
      if( $this->exists() )
      {
         $sql = "UPDATE ".$this->table_name." SET codalmacen = ".$this->var2str($this->codalmacen)
                 .", referencia = ".$this->var2str($this->referencia)
                 .", nombre = ".$this->var2str($this->nombre)
                 .", cantidad = ".$this->var2str($this->cantidad)
                 .", reservada = ".$this->var2str($this->reservada)
                 .", disponible = ".$this->var2str($this->disponible)
                 .", pterecibir = ".$this->var2str($this->pterecibir)
                 .", stockmin = ".$this->var2str($this->stockmin)
                 .", stockmax = ".$this->var2str($this->stockmax)
                 .", cantidadultreg = ".$this->var2str($this->cantidadultreg)
                 .", ubicacion = ".$this->var2str($this->ubicacion)
                 ."  WHERE idstock = ".$this->var2str($this->idstock).";";
         
         return $this->db->exec($sql);
      }
      else
      {
         $sql = "INSERT INTO ".$this->table_name." (codalmacen,referencia,nombre,cantidad,reservada,
            disponible,pterecibir,stockmin,stockmax,cantidadultreg,ubicacion) VALUES 
                   (".$this->var2str($this->codalmacen)
                 .",".$this->var2str($this->referencia)
                 .",".$this->var2str($this->nombre)
                 .",".$this->var2str($this->cantidad)
                 .",".$this->var2str($this->reservada)
                 .",".$this->var2str($this->disponible)
                 .",".$this->var2str($this->pterecibir)
                 .",".$this->var2str($this->stockmin)
                 .",".$this->var2str($this->stockmax)
                 .",".$this->var2str($this->cantidadultreg)
                 .",".$this->var2str($this->ubicacion).");";
         
         if( $this->db->exec($sql) )
         {
            $this->idstock = $this->db->lastval();
            return TRUE;
         }
         else
            return FALSE;
      }
   }
   
   public function delete()
   {
      return $this->db->exec("DELETE FROM ".$this->table_name." WHERE idstock = ".$this->var2str($this->idstock).";");
   }
   
   public function all_from_articulo($ref)
   {
      $stocklist = array();
      
      $data = $this->db->select("SELECT * FROM ".$this->table_name." WHERE referencia = ".$this->var2str($ref)." ORDER BY codalmacen ASC;");
      if($data)
      {
         foreach($data as $s)
         {
            $stocklist[] = new stock($s);
         }
      }
      
      return $stocklist;
   }
   
   public function total_from_articulo($ref, $codalmacen = FALSE)
   {
      $num = 0;
      $sql = "SELECT SUM(cantidad) as total FROM ".$this->table_name." WHERE referencia = ".$this->var2str($ref);
      
      if($codalmacen)
      {
         $sql .= " AND codalmacen = ".$this->var2str($codalmacen);
      }
      
      $data = $this->db->select($sql);
      if($data)
      {
         $num = floatval($data[0]['total']);
      }
      
      return $num;
   }
   
   public function count()
   {
      $num = 0;
      
      $data = $this->db->select("SELECT COUNT(idstock) as total FROM ".$this->table_name.";");
      if($data)
      {
         $num = intval($data[0]['total']);
      }
      
      return $num;
   }
   
   public function count_by_articulo()
   {
      $num = 0;
      
      $data = $this->db->select("SELECT COUNT(DISTINCT referencia) as total FROM ".$this->table_name.";");
      if($data)
      {
         $num = intval($data[0]['total']);
      }
      
      return $num;
   }
}
