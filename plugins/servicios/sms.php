<?php

class sms extends fs_controller
{
	public function __construct()
	{
		parent::__construct(__CLASS__, 'SMS a cliente', 'Ventas', false, false);
	}
}