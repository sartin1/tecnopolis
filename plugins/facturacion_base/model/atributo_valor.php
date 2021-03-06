<?php


class atributo_valor extends fs_model
{
   public $id;
   public $codatributo;
   public $valor;
   
   public function __construct($a = FALSE)
   {
      parent::__construct('atributos_valores');
      if($a)
      {
         $this->id = $this->intval($a['id']);
         $this->codatributo = $a['codatributo'];
         $this->valor = $a['valor'];
      }
      else
      {
         $this->id = NULL;
         $this->codatributo = NULL;
         $this->valor = NULL;
      }
   }
   
   protected function install()
   {
      return '';
   }
   
   public function url()
   {
      return 'index.php?page=ventas_atributos&cod='.$this->codatributo;
   }
   
   public function nombre()
   {
      $nombre = '';
      
      $data = $this->db->select("SELECT * FROM atributos WHERE codatributo = ".$this->var2str($this->codatributo).";");
      if($data)
      {
         $nombre = $data[0]['nombre'];
      }
      
      return $nombre;
   }
   
   public function get($id)
   {
      $data = $this->db->select("SELECT * FROM atributos_valores WHERE id = ".$this->var2str($id).";");
      if($data)
      {
         return new atributo_valor($data[0]);
      }
      else
      {
         return FALSE;
      }
   }
   
   public function exists()
   {
      if( is_null($this->id) )
      {
         return FALSE;
      }
      else
      {
         return $this->db->select("SELECT * FROM atributos_valores WHERE id = ".$this->var2str($this->id).";");
      }
   }
   
   public function save()
   {
      $this->valor = $this->no_html($this->valor);
      
      if( $this->exists() )
      {
         $sql = "UPDATE atributos_valores SET valor = ".$this->var2str($this->valor)
                 .", codatributo = ".$this->var2str($this->codatributo)
                 ."  WHERE id = ".$this->var2str($this->id).";";
      }
      else
      {
         if( is_null($this->id) )
         {
            $this->id = 1;
            
            $data = $this->db->select("SELECT MAX(id) as max FROM atributos_valores;");
            if($data)
            {
               $this->id = 1 + intval($data[0]['max']);
            }
         }
         
         $sql = "INSERT INTO atributos_valores (id,codatributo,valor) VALUES "
                 . "(".$this->var2str($this->id)
                 . ",".$this->var2str($this->codatributo)
                 . ",".$this->var2str($this->valor).");";
      }
      
      return $this->db->exec($sql);
   }
   
   public function delete()
   {
      return $this->db->exec("DELETE FROM atributos_valores WHERE id = ".$this->var2str($this->id).";");
   }
   
   public function all()
   {
      $lista = array();
      
      $data = $this->db->select("SELECT * FROM atributos_valores ORDER BY codatributo DESC;");
      if($data)
      {
         foreach($data as $d)
         {
            $lista[] = new atributo_valor($d);
         }
      }
      
      return $lista;
   }
   
   public function all_from_atributo($cod)
   {
      $lista = array();
      $sql = "SELECT * FROM atributos_valores WHERE codatributo = ".$this->var2str($cod)
              ." ORDER BY valor ASC;";
      
      $data = $this->db->select($sql);
      if($data)
      {
         foreach($data as $d)
         {
            $lista[] = new atributo_valor($d);
         }
      }
      
      return $lista;
   }
}
