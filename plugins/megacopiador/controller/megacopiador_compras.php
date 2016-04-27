<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_model('pedido_proveedor.php');
require_model('factura_proveedor.php');
require_model('albaran_proveedor.php');

/**
 * Description of megacopiador
 *
 * @author neora
 */
class megacopiador_compras extends fs_controller
{
    public $documento;
    public $tipo;
    
    public function __construct() {
        parent::__construct(__CLASS__, 'Megacopiador Compras', 'ventas', FALSE, FALSE);
    }
    
    protected function private_core() {
        /// añadimos el botón copiar al presupuesto
        $fsext = new fs_extension();
        $fsext->name = 'copiar_pedido';
        $fsext->from = __CLASS__;
        $fsext->to = 'compras_pedido';
        $fsext->type = 'button';
        $fsext->text = 'Copiar';
        $fsext->params = '&pedido=TRUE';
        $fsext->save();
        
        $this->documento = FALSE;
        $this->tipo = FALSE;
        if( isset($_REQUEST['id']) )
        {
            if( isset($_REQUEST['pedido']) )
            {
                $pedido = new pedido_proveedor();
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
            
            $fsext = new fs_extension();
            $fsext->name = 'copiar_factura';
            $fsext->from = __CLASS__;
            $fsext->to = 'compras_factura';
            $fsext->type = 'button';
            $fsext->text = 'Copiar';
            $fsext->params = '&factura=TRUE';
            $fsext->save();
            
            if( isset($_REQUEST['factura']) )
            {
                $factura = new factura_proveedor();
                $this->documento = $factura->get($_REQUEST['id']);
                $this->tipo = 'factura';
                
                if($this->documento)
                {
                    if( isset($_REQUEST['copiar']) )
                    {
                        $factura = clone $this->documento;
                        $factura->idalbaran = NULL;
                        $factura->idfactura = NULL;
                        $factura->fecha = $this->today();
                        $factura->hora = $this->hour();
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
            $fsext->name = 'copiar_albaran';
            $fsext->from = __CLASS__;
            $fsext->to = 'compras_albaran';
            $fsext->type = 'button';
            $fsext->text = 'Copiar';
            $fsext->params = '&albaran=TRUE';
            $fsext->save();
            
            if( isset($_REQUEST['albaran']) )
            {
                $albaran = new albaran_proveedor();
                $this->documento = $albaran->get($_REQUEST['id']);
                $this->tipo = 'albaran';
                
                if($this->documento)
                {
                    if( isset($_REQUEST['copiar']) )
                    {
                        $albaran = clone $this->documento;
                        $albaran->idalbaran = NULL;
                        $albaran->fecha = $this->today();
                        $albaran->hora = $this->hour();
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
