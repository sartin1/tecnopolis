<?php

require_once("Smsc.php");   


class sms extends fs_controller
{
	public function __construct()
	{
		parent::__construct(__CLASS__, 'SMS a cliente', 'Ventas', false, false);
	}
        
	protected function private_core()
	{

		$this->share_extension();

	try 
        {
            $user = 'cosouthbattle';
            $apikey = '14352_fcf6e6e35ad5163ecb5d9c4d5256b9ac';
            $smsc = new Smsc($user, $apikey);

            // Estado del servicio
            echo 'El servicio esta '.($smsc->getEstado()?'Online':'Offline').'. ';

            // Saldo
            echo 'Quedan: '.$smsc->getSaldo().' sms. ';

            // Enviar SMS

             @$smsc->addNumero($_POST['addNumero']);
                     
             @$smsc->setMensaje($_POST['setMensaje']);
                   

            if ($smsc->enviar())
                echo 'Mensaje enviado.';
            } 
            catch (Exception $e) 
            {   
                echo '';
            }
	
	}

	private function share_extension()
	{
		$sms = new fs_extension();
		$sms->name= 'tab_sms';
		$sms->from =__CLASS__;
		$sms->to = 'ventas_servicio';
		$sms->type = 'tab';
		$sms->text = 'Mensajes de Texto';
		$sms->save();
	}
}