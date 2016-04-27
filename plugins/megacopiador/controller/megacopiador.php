<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_model('presupuesto_cliente.php');
require_model('albaran_cliente.php');
require_model('factura_cliente.php');
require_model('pedido_cliente.php');

/**
 * Description of megacopiador
 *
 * @author neora
 */
class megacopiador extends fs_controller
{
    public $documento;
    public $tipo;
    
    public function __construct() {
        parent::__construct(__CLASS__, 'Megacopiador', 'ventas', FALSE, FALSE);
    }
    
    protected function private_core() {
        /// añadimos el botón copiar al presupuesto
        $fsext = new fs_extension();
        $fsext->name = 'copiar_presu';
        $fsext->from = __CLASS__;
        $fsext->to = 'ventas_presupuesto';
        $fsext->type = 'button';
        $fsext->text = 'Copiar';
        $fsext->params = '&presu=TRUE';
        $fsext->save();
        
        $this->documento = FALSE;
        $this->tipo = FALSE;
        if( isset($_REQUEST['id']) )
        {
            if( isset($_REQUEST['presu']) )
            {
                $presu = new presupuesto_cliente();
                $this->documento = $presu->get($_REQUEST['id']);
                $this->tipo = 'presu';
                
                if($this->documento)
                {
                    if( isset($_REQUEST['copiar']) )
                    {
                        $presu = clone $this->documento;
                        $presu->idpresupuesto = NULL;
                        $presu->idpedido = NULL;
                        $presu->fecha = $this->today();
                        $presu->hora = $this->hour();
                        if( $presu->save() )
                        {
                            foreach($this->documento->get_lineas() as $linea)
                            {
                                $newl = clone $linea;
                                $newl->idlinea = NULL;
                                $newl->idpresupuesto = $presu->idpresupuesto;
                                $newl->save();
                            }
                            
                            $this->new_message('<a href="'.$presu->url().'">Documento</a> de '.FS_PRESUPUESTO.' copiado correctamente.');
                        }
                        else
                        {
                            $this->new_error_msg('Error al copiar el documento.');
                        }
                    }
                }
            }
            
        $fsext = new fs_extension();
        $fsext->name = 'copiar_albaran';
        $fsext->from = __CLASS__;
        $fsext->to = 'ventas_albaran';
        $fsext->type = 'button';
        $fsext->text = 'Copiar';
        $fsext->params = '&albaran=TRUE';
        $fsext->save();
            
            if( isset($_REQUEST['albaran']) )
            {
                $albaran = new albaran_cliente();
                $this->documento = $albaran->get($_REQUEST['id']);
                $this->tipo = 'albaran';
                
                if($this->documento)
                {
                    if( isset($_REQUEST['copiar']) )
                    {
                        $albaran = clone $this->documento;
                        $albaran->idpresupuesto = NULL;
                        $albaran->idpedido = NULL;
                        $albaran->ac_cliente = $this->ac_cliente;
                        $albaran->idalbaran = NULL;
                        $albaran->fecha = $this->today();
                        $albaran->hora = $this->hour();
                        $albaran->observaciones = $this->observaciones;
                        if( $albaran->save() )
                        {
                            foreach($this->documento->get_lineas() as $linea)
                            {
                                $newl = clone $linea;
                                $newl->idlinea = NULL;
                                $newl->idalbaran = $albaran->idalbaran;
                                $newl->save();
                            }
                            
                            $this->new_message('<a href="'.$albaran->url().'">Documento</a> de '.FS_ALBARAN.' copiado correctamente.');
                        }
                        else
                        {
                            $this->new_error_msg('Error al copiar el documento.');
                        }
                    }
                }
            }
            
        $fsext = new fs_extension();
        $fsext->name = 'copiar_factura';
        $fsext->from = __CLASS__;
        $fsext->to = 'ventas_factura';
        $fsext->type = 'button';
        $fsext->text = 'Copiar';
        $fsext->params = '&factura=TRUE';
        $fsext->save();
            
            if( isset($_REQUEST['factura']) )
            {
                $factura = new factura_cliente();
                $this->documento = $factura->get($_REQUEST['id']);
                $this->tipo = 'factura';
                
                if($this->documento)
                {
                    if( isset($_REQUEST['copiar']) )
                    {
                        $factura = clone $this->documento;
                        $factura->idpresupuesto = NULL;
                        $factura->idpedido = NULL;
                        $factura->idalbaran = NULL;
                        $factura->idfactura = NULL;
                        $factura->fecha = $this->today();
                        $factura->hora = $this->hour();
                        $factura->observaciones = $this->observaciones;
                        if( $factura->save() )
                        {
                            foreach($this->documento->get_lineas() as $linea)
                            {
                                $newl = clone $linea;
                                $newl->idlinea = NULL;
                                $newl->idfactura = $factura->idfactura;
                                $newl->save();
                            }
                            
                            $this->new_message('<a href="'.$factura->url().'">Documento</a> de '.FS_FACTURA.' copiado correctamente.');
                        }
                        else
                        {
                            $this->new_error_msg('Error al copiar el documento.');
                        }
                    }
                }
            }
            
            $fsext = new fs_extension();
            $fsext->name = 'copiar_pedido';
            $fsext->from = __CLASS__;
            $fsext->to = 'ventas_pedido';
            $fsext->type = 'button';
            $fsext->text = 'Copiar';
            $fsext->params = '&pedido=TRUE';
            $fsext->save();
            
            if( isset($_REQUEST['pedido']) )
            {
                $pedido = new pedido_cliente();
                $this->documento = $pedido->get($_REQUEST['id']);
                $this->tipo = 'pedido';
                
                if($this->documento)
                {
                    if( isset($_REQUEST['copiar']) )
                    {
                        $pedido = clone $this->documento;
                        $pedido->idpresupuesto = NULL;
                        $pedido->idpedido = NULL;
                        $pedido->fecha = $this->today();
                        $pedido->hora = $this->hour();
                        if( $pedido->save() )
                        {
                            foreach($this->documento->get_lineas() as $linea)
                            {
                                $newl = clone $linea;
                                $newl->idlinea = NULL;
                                $newl->idpedido = $pedido->idpedido;
                                $newl->save();
                            }
                            
                            $this->new_message('<a href="'.$pedido->url().'">Documento</a> de '.FS_ALBARAN.' copiado correctamente.');
                        }
                        else
                        {
                            $this->new_error_msg('Error al copiar el documento.');
                        }
                    }
                }
            }
        }
        
    }
    
    public function url() {
        if($this->documento)
        {
            return 'index.php?page='.__CLASS__.'&id='.$_REQUEST['id'].'&'.$this->tipo.'=TRUE';
        }
        else
        {
            return 'index.php?page='.__CLASS__;
        }
    }
}
