<?php

class documento_agente extends fs_model
{
   public $id;
   public $codagente;
   public $ruta;
   public $nombre;
   public $fecha;
   public $hora;
   public $tamano;
   public $usuario;
   
   private $file_exist;
   
   public function __construct($d = FALSE)
   {
      parent::__construct('documentosage');
      if($d)
      {
         $this->id = $this->intval($d['id']);
         $this->ruta = $d['ruta'];
         $this->nombre = $d['nombre'];
         $this->fecha = date('d-m-Y', strtotime($d['fecha']));
         $this->hora = date('h:i:s', strtotime($d['hora']));
         $this->tamano = intval($d['tamano']);
         $this->usuario = $d['usuario'];
         $this->codagente = $this->intval($d['codagente']);
      }
      else
      {
         $this->id = NULL;
         $this->ruta = NULL;
         $this->nombre = NULL;
         $this->fecha = date('d-m-Y');
         $this->hora = date('h:i:s');
         $this->tamano = 0;
         $this->usuario = NULL;
         $this->codagente = NULL;
      }
   }
   
   protected function install()
   {
      return '';
   }
   
   public function file_exists()
   {
      if( !isset($this->file_exist) )
      {
         $this->file_exist = file_exists($this->ruta);
      }
      
      return $this->file_exist;
   }
   
   public function tamano()
   {
      $bytes = $this->tamano;
      $decimals = 2;
      $sz = 'BKMGTP';
      $factor = floor((strlen($bytes) - 1) / 3);
      return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$sz[$factor];
   }
   
   public function get($id)
   {
      $data = $this->db->select("SELECT * FROM documentosage WHERE id = ".$this->var2str($id).";");
      if($data)
      {
         return new documento_agente($data[0]);
      }
      else
         return FALSE;
   }
   
   public function exists()
   {
      if( is_null($this->id) )
      {
         return FALSE;
      }
      else
         return $this->db->select("SELECT * FROM documentosage WHERE id = ".$this->var2str($this->id).";");
   }
   
   public function save()
   {
      if( $this->exists() )
      {
         $sql = "UPDATE documentosage SET ruta = ".$this->var2str($this->ruta)
                 .", nombre = ".$this->var2str($this->nombre)
                 .", fecha = ".$this->var2str($this->fecha)
                 .", hora = ".$this->var2str($this->hora)
                 .", tamano = ".$this->var2str($this->tamano)
                 .", usuario = ".$this->var2str($this->usuario)
                 .", codagente = ".$this->var2str($this->codagente)
                 ." WHERE id = ".$this->var2str($this->id).";";
         
         return $this->db->exec($sql);
      }
      else
      {
         $sql = "INSERT INTO documentosage (ruta,nombre,fecha,hora,tamano,usuario,"
                 . "codagente"
                 . ") VALUES (".$this->var2str($this->ruta)
                 . ",".$this->var2str($this->nombre)
                 . ",".$this->var2str($this->fecha)
                 . ",".$this->var2str($this->hora)
                 . ",".$this->var2str($this->tamano)
                 . ",".$this->var2str($this->usuario)
                 . ",".$this->var2str($this->codagente).");";
         
         if( $this->db->exec($sql) )
         {
            $this->id = $this->db->lastval();
            return TRUE;
         }
         else
            return FALSE;
      }
   }
   
   public function delete()
   {
      return $this->db->exec("DELETE FROM documentosage WHERE id = ".$this->var2str($this->id).";");
   }
   
   /**
    * Devuelve todos los documentos relacionados.
    * @param type $tipo
    * @param type $id
    * @return \documento_servicio
    */
   public function all_from($tipo, $id)
   {
      $lista = array();
      $sql = "SELECT * FROM documentosage WHERE ".$tipo." = ".$this->var2str($id).";";
      
      $data = $this->db->select($sql);
      if($data)
      {
         foreach($data as $d)
         {
            $lista[] = new documento_agente($d);
         }
      }
      
      return $lista;
   }
}
