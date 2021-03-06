<?php


require_model('asiento.php');
require_model('subcuenta.php');

/**
 * La línea de un asiento.
 * Se relaciona con un asiento y una subcuenta.
 */
class partida extends fs_model
{
   public $idpartida;
   public $idasiento;
   public $idsubcuenta;
   public $codsubcuenta;
   public $idconcepto;
   public $concepto;
   public $idcontrapartida;
   public $codcontrapartida;
   public $punteada;
   public $tasaconv;
   public $coddivisa;
   public $haberme;
   public $debeme;
   public $recargo;
   public $iva;
   public $baseimponible;
   public $factura;
   public $codserie;
   public $tipodocumento;
   public $documento;
   public $cifnif;
   public $haber;
   public $debe;
   
   public $numero;
   public $fecha;
   public $saldo;
   public $sum_debe;
   public $sum_haber;
   
   public function __construct($p=FALSE)
   {
      parent::__construct('co_partidas', 'plugins/facturacion_base/');
      if($p)
      {
         $this->idpartida = $this->intval($p['idpartida']);
         $this->idasiento = $this->intval($p['idasiento']);
         $this->idsubcuenta = $this->intval($p['idsubcuenta']);
         $this->codsubcuenta = $p['codsubcuenta'];
         $this->idconcepto = $this->intval($p['idconcepto']);
         $this->concepto = $p['concepto'];
         $this->idcontrapartida = $p['idcontrapartida'];
         $this->codcontrapartida = $p['codcontrapartida'];
         $this->punteada = $this->str2bool($p['punteada']);
         $this->tasaconv = $p['tasaconv'];
         $this->coddivisa = $p['coddivisa'];
         $this->haberme = floatval($p['haberme']);
         $this->debeme = floatval($p['debeme']);
         $this->recargo = floatval($p['recargo']);
         $this->iva = floatval($p['iva']);
         $this->baseimponible = floatval($p['baseimponible']);
         $this->factura = $this->intval($p['factura']);
         $this->codserie = $p['codserie'];
         $this->tipodocumento = $p['tipodocumento'];
         $this->documento = $p['documento'];
         $this->cifnif = $p['cifnif'];
         $this->debe = floatval($p['debe']);
         $this->haber = floatval($p['haber']);
      }
      else
      {
         $this->idpartida = NULL;
         $this->idasiento = NULL;
         $this->idsubcuenta = NULL;
         $this->codsubcuenta = NULL;
         $this->idconcepto = NULL;
         $this->concepto = '';
         $this->idcontrapartida = NULL;
         $this->codcontrapartida = NULL;
         $this->punteada = FALSE;
         $this->tasaconv = 1;
         $this->coddivisa = NULL;
         $this->haberme = 0;
         $this->debeme = 0;
         $this->recargo = 0;
         $this->iva = 0;
         $this->baseimponible = 0;
         $this->factura = NULL;
         $this->codserie = NULL;
         $this->tipodocumento = NULL;
         $this->documento = NULL;
         $this->cifnif = NULL;
         $this->debe = 0;
         $this->haber = 0;
      }
      
      $this->numero = 0;
      $this->fecha = Date('d-m-Y');
      $this->saldo = 0;
      $this->sum_debe = 0;
      $this->sum_haber = 0;
   }
   
   protected function install()
   {
      return '';
   }
   
   public function url()
   {
      if( is_null($this->idasiento) )
      {
         return 'index.php?page=contabilidad_asientos';
      }
      else
         return 'index.php?page=contabilidad_asiento&id='.$this->idasiento;
   }
   
   public function get_subcuenta()
   {
      $subcuenta = new subcuenta();
      return $subcuenta->get( $this->idsubcuenta );
   }
   
   public function subcuenta_url()
   {
      $subc = $this->get_subcuenta();
      if($subc)
      {
         return $subc->url();
      }
      else
         return '#';
   }
   
   public function get_contrapartida()
   {
      if( is_null($this->idcontrapartida) )
      {
         return FALSE;
      }
      else
      {
         $subc = new subcuenta();
         return $subc->get( $this->idcontrapartida );
      }
   }
   
   public function contrapartida_url()
   {
      $subc = $this->get_contrapartida();
      if($subc)
      {
         return $subc->url();
      }
      else
         return '#';
   }
   
   public function get($id)
   {
      $partida = $this->db->select("SELECT * FROM ".$this->table_name." WHERE idpartida = ".$id.";");
      if( $partida )
      {
         return new partida($partida[0]);
      }
      else
         return FALSE;
   }
   
   public function exists()
   {
      if( is_null($this->idpartida) )
      {
         return FALSE;
      }
      else
         return $this->db->select("SELECT * FROM ".$this->table_name." WHERE idpartida = ".$this->var2str($this->idpartida).";");
   }
   
   public function save()
   {
      $this->concepto = $this->no_html($this->concepto);
      $this->documento = $this->no_html($this->documento);
      $this->cifnif = $this->no_html($this->cifnif);
      
      if( $this->exists() )
      {
         $sql = "UPDATE ".$this->table_name." SET idasiento = ".$this->var2str($this->idasiento)
                 .", idsubcuenta = ".$this->var2str($this->idsubcuenta)
                 .", codsubcuenta = ".$this->var2str($this->codsubcuenta)
                 .", idconcepto = ".$this->var2str($this->idconcepto)
                 .", concepto = ".$this->var2str($this->concepto)
                 .", idcontrapartida = ".$this->var2str($this->idcontrapartida)
                 .", codcontrapartida = ".$this->var2str($this->codcontrapartida)
                 .", punteada = ".$this->var2str($this->punteada)
                 .", tasaconv = ".$this->var2str($this->tasaconv)
                 .", coddivisa = ".$this->var2str($this->coddivisa)
                 .", haberme = ".$this->var2str($this->haberme)
                 .", debeme = ".$this->var2str($this->debeme)
                 .", recargo = ".$this->var2str($this->recargo)
                 .", iva = ".$this->var2str($this->iva)
                 .", baseimponible = ".$this->var2str($this->baseimponible)
                 .", factura = ".$this->var2str($this->factura)
                 .", codserie = ".$this->var2str($this->codserie)
                 .", tipodocumento = ".$this->var2str($this->tipodocumento)
                 .", documento = ".$this->var2str($this->documento)
                 .", cifnif = ".$this->var2str($this->cifnif)
                 .", debe = ".$this->var2str($this->debe)
                 .", haber = ".$this->var2str($this->haber)
                 ."  WHERE idpartida = ".$this->var2str($this->idpartida).";";
         
         if( $this->db->exec($sql) )
         {
            $subc = $this->get_subcuenta();
            if($subc)
            {
               $subc->save(); /// guardamos la subcuenta para actualizar su saldo
            }
            return TRUE;
         }
         else
            return FALSE;
      }
      else
      {
         $sql = "INSERT INTO ".$this->table_name." (idasiento,idsubcuenta,codsubcuenta,idconcepto,
            concepto,idcontrapartida,codcontrapartida,punteada,tasaconv,coddivisa,haberme,debeme,recargo,iva,
            baseimponible,factura,codserie,tipodocumento,documento,cifnif,debe,haber) VALUES
                   (".$this->var2str($this->idasiento)
                 .",".$this->var2str($this->idsubcuenta)
                 .",".$this->var2str($this->codsubcuenta)
                 .",".$this->var2str($this->idconcepto)
                 .",".$this->var2str($this->concepto)
                 .",".$this->var2str($this->idcontrapartida)
                 .",".$this->var2str($this->codcontrapartida)
                 .",".$this->var2str($this->punteada)
                 .",".$this->var2str($this->tasaconv)
                 .",".$this->var2str($this->coddivisa)
                 .",".$this->var2str($this->haberme)
                 .",".$this->var2str($this->debeme)
                 .",".$this->var2str($this->recargo)
                 .",".$this->var2str($this->iva)
                 .",".$this->var2str($this->baseimponible)
                 .",".$this->var2str($this->factura)
                 .",".$this->var2str($this->codserie)
                 .",".$this->var2str($this->tipodocumento)
                 .",".$this->var2str($this->documento)
                 .",".$this->var2str($this->cifnif)
                 .",".$this->var2str($this->debe)
                 .",".$this->var2str($this->haber).");";
         
         if( $this->db->exec($sql) )
         {
            $this->idpartida = $this->db->lastval();
            
            $subc = $this->get_subcuenta();
            if($subc)
            {
               $subc->save(); /// guardamos la subcuenta para actualizar su saldo
            }
            return TRUE;
         }
         else
            return FALSE;
      }
   }
   
   public function delete()
   {
      if( $this->db->exec("DELETE FROM ".$this->table_name." WHERE idpartida = ".$this->var2str($this->idpartida).";") )
      {
         $subc = $this->get_subcuenta();
         if($subc)
         {
            $subc->save(); /// guardamos la subcuenta para actualizar su saldo
         }
         
         return TRUE;
      }
      else
         return FALSE;
   }
   
   public function all_from_subcuenta($id, $offset=0)
   {
      $plist = array();
      $sql = "SELECT a.numero,a.fecha,p.idpartida,p.debe,p.haber FROM co_asientos a, co_partidas p"
              ." WHERE a.idasiento = p.idasiento AND p.idsubcuenta = ".$this->var2str($id)
              ." ORDER BY a.numero ASC, p.idpartida ASC;";
      
      $ordenadas = $this->db->select($sql);
      if( $ordenadas )
      {
         $partida = new partida();
         $i = 0;
         $saldo = 0;
         $sum_debe = 0;
         $sum_haber = 0;
         foreach($ordenadas as $po)
         {
            $saldo += floatval($po['debe']) - floatval($po['haber']);
            $sum_debe += floatval($po['debe']);
            $sum_haber += floatval($po['haber']);
            if( $i >= $offset AND $i < ($offset+FS_ITEM_LIMIT) )
            {
               $aux = $partida->get($po['idpartida']);
               if( $aux )
               {
                  $aux->numero = intval($po['numero']);
                  $aux->fecha = Date('d-m-Y', strtotime($po['fecha']));
                  $aux->saldo = $saldo;
                  $aux->sum_debe = $sum_debe;
                  $aux->sum_haber = $sum_haber;
                  $plist[] = $aux;
               }
            }
            $i++;
         }
      }
      
      return $plist;
   }
   
   public function all_from_asiento($id)
   {
      $plist = array();
      $sql = "SELECT * FROM ".$this->table_name." WHERE idasiento = "
              .$this->var2str($id)." ORDER BY codsubcuenta ASC;";
      
      $partidas = $this->db->select($sql);
      if($partidas)
      {
         foreach($partidas as $p)
         {
            $plist[] = new partida($p);
         }
      }
      
      return $plist;
   }
   
   public function full_from_subcuenta($id)
   {
      $plist = array();
      $sql = "SELECT a.numero,a.fecha,p.idpartida FROM co_asientos a, co_partidas p"
              ." WHERE a.idasiento = p.idasiento AND p.idsubcuenta = ".$this->var2str($id)
              ." ORDER BY a.numero ASC, p.idpartida ASC";
      
      $saldo = 0;
      $sum_debe = 0;
      $sum_haber = 0;
      
      $partida = new partida();
      $offset = 0;
      $data = $this->db->select_limit($sql, 100, $offset);
      while($data)
      {
         foreach($data as $po)
         {
            $aux = $partida->get($po['idpartida']);
            if( $aux )
            {
               $aux->numero = intval($po['numero']);
               $aux->fecha = Date('d-m-Y', strtotime($po['fecha']));
               $saldo += $aux->debe - $aux->haber;
               $sum_debe += $aux->debe;
               $sum_haber += $aux->haber;
               $aux->saldo = $saldo;
               $aux->sum_debe = $sum_debe;
               $aux->sum_haber = $sum_haber;
               $plist[] = $aux;
            }
            
            $offset++;
         }
         
         $data = $this->db->select_limit($sql, 100, $offset);
      }
      
      return $plist;
   }
   
   public function full_from_ejercicio($eje, $offset=0, $limit=FS_ITEM_LIMIT)
   {
      $sql = "SELECT a.numero,a.fecha,s.codsubcuenta,s.descripcion,p.concepto,p.debe,p.haber"
              ." FROM co_asientos a, co_subcuentas s, co_partidas p"
              ." WHERE a.codejercicio = ".$this->var2str($eje)
              ." AND p.idasiento = a.idasiento AND p.idsubcuenta = s.idsubcuenta"
              ." ORDER BY a.numero ASC, p.codsubcuenta ASC";
      
      $data = $this->db->select_limit($sql, $limit, $offset);
      if($data)
      {
         return $data;
      }
      else
         return array();
   }
   
   public function count_from_subcuenta($id)
   {
      $sql = "SELECT a.numero,a.fecha,p.idpartida FROM co_asientos a, co_partidas p"
              ." WHERE a.idasiento = p.idasiento AND p.idsubcuenta = ".$this->var2str($id)
              ." ORDER BY a.numero ASC, p.idpartida ASC;";
      
      $ordenadas = $this->db->select($sql);
      if($ordenadas)
      {
         return count($ordenadas);
      }
      else
         return 0;
   }
   
   public function totales_from_subcuenta($id)
   {
      $totales = array( 'debe' => 0, 'haber' => 0, 'saldo' => 0 );
      $sql = "SELECT COALESCE(SUM(debe), 0) as debe,COALESCE(SUM(haber), 0) as haber"
              ." FROM ".$this->table_name." WHERE idsubcuenta = ".$this->var2str($id).";";
      
      $resultados = $this->db->select($sql);
      if( $resultados )
      {
         $totales['debe'] = floatval($resultados[0]['debe']);
         $totales['haber'] = floatval($resultados[0]['haber']);
         $totales['saldo'] = floatval($resultados[0]['debe']) - floatval($resultados[0]['haber']);
      }
      
      return $totales;
   }
   
   public function totales_from_ejercicio($cod)
   {
      $totales = array( 'debe' => 0, 'haber' => 0, 'saldo' => 0 );
      $sql = "SELECT COALESCE(SUM(p.debe), 0) as debe,COALESCE(SUM(p.haber), 0) as haber"
              ." FROM co_partidas p, co_asientos a"
              ." WHERE p.idasiento = a.idasiento AND a.codejercicio = ".$this->var2str($cod).";";
      
      $resultados = $this->db->select($sql);
      if( $resultados )
      {
         $totales['debe'] = floatval($resultados[0]['debe']);
         $totales['haber'] = floatval($resultados[0]['haber']);
         $totales['saldo'] = floatval($resultados[0]['debe']) - floatval($resultados[0]['haber']);
      }
      
      return $totales;
   }
   
   public function totales_from_subcuenta_fechas($id, $fechaini, $fechafin, $excluir=FALSE)
   {
      $totales = array( 'debe' => 0, 'haber' => 0, 'saldo' => 0 );
      
      if($excluir)
      {
         $resultados = $this->db->select("SELECT COALESCE(SUM(p.debe), 0) as debe,
            COALESCE(SUM(p.haber), 0) as haber FROM co_partidas p, co_asientos a
            WHERE p.idasiento = a.idasiento AND p.idsubcuenta = ".$this->var2str($id)."
               AND a.fecha BETWEEN ".$this->var2str($fechaini)." AND ".$this->var2str($fechafin)."
               AND p.idasiento NOT IN ('".implode("','", $excluir)."');");
      }
      else
      {
         $resultados = $this->db->select("SELECT COALESCE(SUM(p.debe), 0) as debe,
            COALESCE(SUM(p.haber), 0) as haber FROM co_partidas p, co_asientos a
            WHERE p.idasiento = a.idasiento AND p.idsubcuenta = ".$this->var2str($id)."
               AND a.fecha BETWEEN ".$this->var2str($fechaini)." AND ".$this->var2str($fechafin).";");
      }
      
      if( $resultados )
      {
         $totales['debe'] = floatval($resultados[0]['debe']);
         $totales['haber'] = floatval($resultados[0]['haber']);
         $totales['saldo'] = floatval($resultados[0]['debe']) - floatval($resultados[0]['haber']);
      }
      
      return $totales;
   }
}
