<?php if(!class_exists('raintpl')){exit;}?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="es" xml:lang="es" >
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <title></title>
      <meta name="description" content="" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <link rel="shortcut icon" href="view/img/favicon.ico" />
      <link href="view/css/bootstrap-yeti.min.css" rel="stylesheet" />
      <link href="view/css/custom.css" rel="stylesheet" />
      <script type="text/javascript" src="view/js/jquery.min.js"></script>
      <script type="text/javascript" src="view/js/bootstrap.min.js"></script>
      <script type="text/javascript">
         $(document).ready(function() {
            $("#b_feedback").click(function(event) {
               event.preventDefault();
               $("#modal_feedback").modal('show');
               document.f_feedback.feedback_text.focus();
            });
         });
      </script>
   </head>
   <body>
      <?php if( $fsc->get_errors() ){ ?>

      <div class="alert alert-danger hidden-print">
         <ul><?php $loop_var1=$fsc->get_errors(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?><li><?php echo $value1;?></li><?php } ?></ul>
      </div>
      <?php } ?>

      
      <?php if( $fsc->get_messages() ){ ?>

      <div class="alert alert-success hidden-print">
         <ul><?php $loop_var1=$fsc->get_messages(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?><li><?php echo $value1;?></li><?php } ?></ul>
      </div>
      <?php } ?>

      
      <?php if( $fsc->get_advices() ){ ?>

      <div class="alert alert-info hidden-print">
         <ul><?php $loop_var1=$fsc->get_advices(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?><li><?php echo $value1;?></li><?php } ?></ul>
      </div>
      <?php } ?>

      
      <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("feedback") . ( substr("feedback",-1,1) != "/" ? "/" : "" ) . basename("feedback") );?>

      
      <div class="container">
         <div class="row">
            <div class="col-sm-12">
               <div class="page-header">
                  <h1>
                     <span class="glyphicon glyphicon-exclamation-sign"></span>
                     Error
                  </h1>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-sm-6">
               <p class="help-block">
                  Ha sido imposible conectar a la base de datos. Bien porque ya
                  no existe, el servidor ha sido desconectado o has excedido la
                  cuota del hosting.
               </p>
               
            </div>
            <div class="col-sm-6">
               <div class="thumbnail">
                  <img src="view/img/fuuu_face.png" alt="FUUUUUUUUUU"/>
               </div>
            </div>
         </div>
      </div>
   </body>
</html>