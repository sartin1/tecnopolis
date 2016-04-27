<?php

/**
 * Un concepto predefinido para una partida (la línea de un asiento contable).
 */
class concepto_partida extends fs_model
{
   public $idconceptopar;
   public $concepto;
   
   public function __construct($c = FALSE)
   {
      parent::__construct('co_conceptospar', 'plugins/facturacion_base/');
      if($c)
      {
         $this->idconceptopar = $c['idconceptopar'];
         $this->concepto = $c['concepto'];
      }
      else
      {
         $this->idconceptopar = NULL;
         $this->concepto = NULL;
      }
   }
   
   protected function install()
   {
      return "";
   }
   
   public function get($id)
   {
      $concepto = $this->db->select("SELECT * FROM ".$this->table_name." WHERE idconceptopar = ".$this->var2str($id).";");
      if($concepto)
         return new concepto_partida($concepto[0]);
      else
         return FALSE;
   }

   public function exists()
   {
      if( is_null($this->idconceptopar) )
         return FALSE;
      else
         return $this->db->select("SELECT * FROM ".$this->table_name."
            WHERE idconceptopar = ".$this->var2str($this->idconceptopar).";");
   }
   
   public function test()
   {
      $this->concepto = $this->no_html($this->concepto);
      return TRUE;
   }
   
   public function save()
   {
      return FALSE;
   }
   
   public function delete()
   {
      return $this->db->exec("DELETE FROM ".$this->table_name." WHERE idconceptopar = ".$this->var2str($this->idconceptopar).";");
   }
   
   public function all()
   {
      $concelist = array();
      $conceptos = $this->db->select("SELECT * FROM ".$this->table_name." ORDER BY idconceptopar ASC;");
      if($conceptos)
      {
         foreach($conceptos as $c)
            $concelist[] = new concepto_partida($c);
      }
      return $concelist;
   }
}
