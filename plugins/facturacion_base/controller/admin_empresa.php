<?php


require_once 'extras/phpmailer/class.phpmailer.php';
require_once 'extras/phpmailer/class.smtp.php';
require_model('almacen.php');
require_model('cuenta_banco.php');
require_model('divisa.php');
require_model('ejercicio.php');
require_model('forma_pago.php');
require_model('pais.php');
require_model('serie.php');

class admin_empresa extends fs_controller
{
   public $almacen;
   public $cuenta_banco;
   public $divisa;
   public $ejercicio;
   public $email_plantillas;
   public $forma_pago;
   public $impresion;
   public $logo;
   public $pais;
   public $serie;
   
   public function __construct()
   {
      parent::__construct(__CLASS__, 'Empresa', 'admin', TRUE, TRUE);
   }
   
   protected function private_core()
   {
      $this->almacen = new almacen();
      $this->cuenta_banco = new cuenta_banco();
      $this->divisa = new divisa();
      $this->ejercicio = new ejercicio();
      $this->forma_pago = new forma_pago();
      $this->pais = new pais();
      $this->serie = new serie();
      
      $fsvar = new fs_var();
      
      /// obtenemos los datos de configuración de impresión
      $this->impresion = array(
          'print_ref' => '1',
          'print_dto' => '1',
          'print_alb' => '0',
          'print_formapago' => '1'
      );
      $this->impresion = $fsvar->array_get($this->impresion, FALSE);
      
      /// obtenemos los datos de las plantillas de emails
      $this->email_plantillas = array(
          'mail_factura' => "Buenos días, le adjunto su #DOCUMENTO#.\n#FIRMA#",
          'mail_albaran' => "Buenos días, le adjunto su #DOCUMENTO#.\n#FIRMA#",
          'mail_pedido' => "Buenos días, le adjunto su #DOCUMENTO#.\n#FIRMA#",
          'mail_presupuesto' => "Buenos días, le adjunto su #DOCUMENTO#.\n#FIRMA#",
      );
      $this->email_plantillas = $fsvar->array_get($this->email_plantillas, FALSE);
      
      if( isset($_POST['cifnif']) )
      {
         /// guardamos los datos de la empresa
         $this->empresa->nombre = $_POST['nombre'];
         $this->empresa->nombrecorto = $_POST['nombrecorto'];
         $this->empresa->cifnif = $_POST['cifnif'];
         $this->empresa->administrador = $_POST['administrador'];
         $this->empresa->codpais = $_POST['codpais'];
         $this->empresa->provincia = $_POST['provincia'];
         $this->empresa->ciudad = $_POST['ciudad'];
         $this->empresa->direccion = $_POST['direccion'];
         $this->empresa->codpostal = $_POST['codpostal'];
         $this->empresa->telefono = $_POST['telefono'];
         $this->empresa->fax = $_POST['fax'];
         $this->empresa->web = $_POST['web'];
         $this->empresa->email = $_POST['email'];
         $this->empresa->lema = $_POST['lema'];
         $this->empresa->horario = $_POST['horario'];
         $this->empresa->contintegrada = isset($_POST['contintegrada']);
         $this->empresa->codejercicio = $_POST['codejercicio'];
         $this->empresa->codserie = $_POST['codserie'];
         $this->empresa->coddivisa = $_POST['coddivisa'];
         $this->empresa->codpago = $_POST['codpago'];
         $this->empresa->codalmacen = $_POST['codalmacen'];
         $this->empresa->pie_factura = $_POST['pie_factura'];
         $this->empresa->recequivalencia = isset($_POST['recequivalencia']);
         
         /// configuración de email
         $this->empresa->email_config['mail_password'] = $_POST['mail_password'];
         $this->empresa->email_config['mail_bcc'] = $_POST['mail_bcc'];
         $this->empresa->email_config['mail_firma'] = $_POST['mail_firma'];
         $this->empresa->email_config['mail_host'] = $_POST['mail_host'];
         $this->empresa->email_config['mail_port'] = intval($_POST['mail_port']);
         $this->empresa->email_config['mail_enc'] = strtolower($_POST['mail_enc']);
         $this->empresa->email_config['mail_user'] = $_POST['mail_user'];
         $this->empresa->email_config['mail_low_security'] = isset($_POST['mail_low_security']);
         
         if( $this->empresa->save() )
         {
            /// guardamos las opciones por defecto de almacén y forma de pago
            $this->save_codalmacen($_POST['codalmacen']);
            $this->save_codpago($_POST['codpago']);
            
            $this->new_message('Datos guardados correctamente.');
            if(!$this->empresa->contintegrada)
            {
               $this->new_message(' ');
            }
            
            $step = $fsvar->simple_get('install_step');
            if($step == 2)
            {
               $step = 3;
               $fsvar->simple_save('install_step', $step);
            }
            
            if($step == 3 AND $this->empresa->contintegrada)
            {
               $this->new_message('Recuerda que tienes que <a href="index.php?page=contabilidad_ejercicio&cod='.
                       $this->empresa->codejercicio.'">importar los datos del ejercicio</a>.');
            }
            
            $this->mail_test();
         }
         else
            $this->new_error_msg ('Error al guardar los datos.');
         
         /// guardamos los datos de impresión
         $this->impresion['print_ref'] = ( isset($_POST['print_ref']) ? 1 : 0 );
         $this->impresion['print_dto'] = ( isset($_POST['print_dto']) ? 1 : 0 );
         $this->impresion['print_alb'] = ( isset($_POST['print_alb']) ? 1 : 0 );
         $this->impresion['print_formapago'] = ( isset($_POST['print_formapago']) ? 1 : 0 );
         $fsvar->array_save($this->impresion);
         
         /// guardamos las plantillas de emails
         $this->email_plantillas['mail_factura'] = $_POST['mail_factura'];
         $this->email_plantillas['mail_albaran'] = $_POST['mail_albaran'];
         if( isset($_POST['mail_pedido']) )
         {
            $this->email_plantillas['mail_pedido'] = $_POST['mail_pedido'];
            $this->email_plantillas['mail_presupuesto'] = $_POST['mail_presupuesto'];
         }
         $fsvar->array_save($this->email_plantillas);
      }
      else if( isset($_POST['logo']) )
      {
         if( is_uploaded_file($_FILES['fimagen']['tmp_name']) )
         {
            $this->delete_logo();
            
            if( substr( strtolower($_FILES['fimagen']['name']), -3) == 'png' )
            {
               copy($_FILES['fimagen']['tmp_name'], "tmp/".FS_TMP_NAME."logo.png");
            }
            else
            {
               copy($_FILES['fimagen']['tmp_name'], "tmp/".FS_TMP_NAME."logo.jpg");
            }
            
            $this->new_message('Logotipo guardado correctamente.');
         }
      }
      else if( isset($_GET['delete_logo']) )
      {
         $this->delete_logo();
      }
      else if( isset($_GET['delete_cuenta']) ) /// eliminar cuenta bancaria
      {
         $cuenta = $this->cuenta_banco->get($_GET['delete_cuenta']);
         if($cuenta)
         {
            if( $cuenta->delete() )
            {
               $this->new_message('Cuenta bancaria eliminada correctamente.');
            }
            else
               $this->new_error_msg('Imposible eliminar la cuenta bancaria.');
         }
         else
            $this->new_error_msg('Cuenta bancaria no encontrada.');
      }
      else if( isset($_POST['iban']) ) /// añadir/modificar cuenta bancaria
      {
         if( isset($_POST['codcuenta']) )
         {
            $cuentab = $this->cuenta_banco->get($_POST['codcuenta']);
         }
         else
         {
            $cuentab = new cuenta_banco();
         }
         
         $cuentab->descripcion = $_POST['descripcion'];
         $cuentab->iban = $_POST['iban'];
         $cuentab->swift = $_POST['swift'];
         
         if( isset($_POST['codsubcuenta']) )
         {
            $cuentab->codsubcuenta = $_POST['codsubcuenta'];
         }
         
         if( $cuentab->save() )
         {
            $this->new_message('Cuenta bancaria guardada correctamente.');
         }
         else
            $this->new_error_msg('Imposible guardar la cuenta bancaria.');
      }
      
      $this->logo = FALSE;
      if( file_exists('tmp/'.FS_TMP_NAME.'logo.png') )
      {
         $this->logo = 'tmp/'.FS_TMP_NAME.'logo.png';
      }
      else if( file_exists('tmp/'.FS_TMP_NAME.'logo.jpg') )
      {
         $this->logo = 'tmp/'.FS_TMP_NAME.'logo.jpg';
      }
   }
   
   private function mail_test()
   {
      if( $this->empresa->can_send_mail() )
      {
         /// Es imprescindible OpenSSL para enviar emails con los principales proveedores
         if( extension_loaded('openssl') )
         {
            $mail = new PHPMailer();
            $mail->Timeout = 3;
            $mail->isSMTP();
            $mail->SMTPAuth = TRUE;
            $mail->SMTPSecure = $this->empresa->email_config['mail_enc'];
            $mail->Host = $this->empresa->email_config['mail_host'];
            $mail->Port = intval($this->empresa->email_config['mail_port']);
            $mail->Username = $this->empresa->email;
            if($this->empresa->email_config['mail_user'] != '')
            {
               $mail->Username = $this->empresa->email_config['mail_user'];
            }
            
            $mail->Password = $this->empresa->email_config['mail_password'];
            $mail->From = $this->empresa->email;
            $mail->FromName = $this->user->nick;
            $mail->CharSet = 'UTF-8';
            
            $mail->Subject = 'TEST';
            $mail->AltBody = 'TEST';
            $mail->WordWrap = 50;
            $mail->msgHTML('TEST');
            $mail->isHTML(TRUE);
            
            $SMTPOptions = array();
            if($this->empresa->email_config['mail_low_security'])
            {
               $SMTPOptions = array(
                   'ssl' => array(
                       'verify_peer' => false,
                       'verify_peer_name' => false,
                       'allow_self_signed' => true
                   )
               );
            }
            
            if( !$mail->smtpConnect($SMTPOptions) )
            {
               $this->new_error_msg('No se ha podido conectar por email. ¿La contraseña es correcta?');
               
               if($mail->Host == 'smtp.gmail.com')
               {
                  $this->new_error_msg('Aunque la contraseña de gmail sea correcta, en ciertas '
                          . 'situaciones los servidores de gmail bloquean la conexión. '
                          . 'Para superar esta situación debes crear y usar una '
                          . '<a href="https://support.google.com/accounts/answer/185833?hl=es" '
                          . 'target="_blank">contraseña de aplicación</a>');
               }
               else
               {
                  $this->new_error_msg("");
               }
            }
         }
         else
         {
            $this->new_error_msg('No se encuentra la extensión OpenSSL,'
                    . ' imprescindible para enviar emails.');
         }
      }
   }
   
   private function delete_logo()
   {
      if( file_exists('tmp/'.FS_TMP_NAME.'logo.png') )
      {
         unlink('tmp/'.FS_TMP_NAME.'logo.png');
         $this->new_message('Logotipo borrado correctamente.');
      }
      else if( file_exists('tmp/'.FS_TMP_NAME.'logo.jpg') )
      {
         unlink('tmp/'.FS_TMP_NAME.'logo.jpg');
         $this->new_message('Logotipo borrado correctamente.');
      }
   }
}
