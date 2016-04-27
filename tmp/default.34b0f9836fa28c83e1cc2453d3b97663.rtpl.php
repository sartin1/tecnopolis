<?php if(!class_exists('raintpl')){exit;}?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="es" xml:lang="es" >
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <title>Tecnópolis</title>
   <meta name="description" content="" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <link rel="shortcut icon" href="view/img/favicon.ico" />
   <link rel="stylesheet" href="view/css/bootstrap-yeti.min.css" />
   <script type="text/javascript" src="view/js/jquery.min.js"></script>
   <script type="text/javascript" src="view/js/bootstrap.min.js"></script>
   <script type="text/javascript" src="view/js/jquery.ui.shake.js"></script>
   <script type="text/javascript">
      $(document).ready(function() {
         <?php if( $fsc->get_errors() ){ ?>

         $("#box_login").shake();
         <?php } ?>

         
         document.f_login.user.focus();
         
         $("#b_feedback").click(function(event) {
            event.preventDefault();
            $("#modal_feedback").modal('show');
            document.f_feedback.feedback_text.focus();
         });
         $("#b_new_password").click(function(event) {
            event.preventDefault();
            $("#modal_new_password").modal('show');
            document.f_new_password.user.focus();
         });
      });
   </script>
</head>
<body>
   <nav class="navbar navbar-default" role="navigation" style="margin: 0px;">
      <div class="container-fluid">
         <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
               <span class="sr-only">Menú</span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">
               <span class="glyphicon glyphicon-home"></span> &nbsp; Tecnópolis
            </a>
         </div>
         
         <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
               <li style="display: none">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="Ayuda">
                     <span class="glyphicon glyphicon-question-sign hidden-xs"></span>
                     <span class="visible-xs">Ayuda</span>
                  </a>
                  <ul class="dropdown-menu">
                     <li><a href="<?php  echo FS_COMMUNITY_URL;?>/index.php?page=community_questions" target="_blank">Preguntas</a></li>
                     <li><a href="<?php  echo FS_COMMUNITY_URL;?>/index.php?page=community_errors" target="_blank">Errores</a></li>
                     <li><a href="<?php  echo FS_COMMUNITY_URL;?>/index.php?page=community_ideas" target="_blank">Sugerencias</a></li>
                     <li><a href="<?php  echo FS_COMMUNITY_URL;?>/index.php?page=community_colabora" target="_blank">Colabora</a></li>
                     <li class="divider"></li>
                     <li><a href="#" id="b_feedback"><span class="glyphicon glyphicon-send"></span> &nbsp; Informar...</a></li>
                  </ul>
               </li>
            </ul>
         </div>
      </div>
   </nav>
   
   <?php if( $fsc->get_errors() ){ ?>

   <div class="alert alert-danger">
      <ul><?php $loop_var1=$fsc->get_errors(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?><li><?php echo $value1;?></li><?php } ?></ul>
   </div>
   <?php } ?>

   <?php if( $fsc->get_messages() ){ ?>

   <div class="alert alert-success">
      <ul><?php $loop_var1=$fsc->get_messages(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?><li><?php echo $value1;?></li><?php } ?></ul>
   </div>
   <?php } ?>

   <?php if( $fsc->get_advices() ){ ?>

   <div class="alert alert-info">
      <ul><?php $loop_var1=$fsc->get_advices(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?><li><?php echo $value1;?></li><?php } ?></ul>
   </div>
   <?php } ?>

   
   <form name="f_login" action="index.php?nlogin=<?php echo $nlogin;?>" method="post" class="form" role="form">
      <div class="container">
         <div class="row">
            <div class="col-md-6 col-md-offset-3">
               <div class="page-header">
                  <h1>
                     <span class="glyphicon glyphicon-user"></span> &nbsp; Iniciar sesión
                  </h1>
               </div>
               <div class="well well-sm">
                  <br/>
                  <div class="container-fluid">
                     <div class="row">
                        
                        <div class="col-sm-12">
                           <div class="form-group">
                              <input type="text" name="user" class="form-control input-lg" maxlength="12" placeholder="Usuario" autocomplete="off"/>
                           </div>
                           <div class="form-group">
                              <input type="password" class="form-control input-lg" name="password" maxlength="32" placeholder="Contraseña"/>
                              <p class="help-block">
                                 <a href="#" id="b_new_password">¿Has olvidado la contraseña?</a>
                              </p>
                           </div>
                           <button class="btn btn-block btn-primary" type="submit" onclick="this.disabled=true;this.form.submit();">
                              <span class="glyphicon glyphicon-log-in"></span> &nbsp; Iniciar sesión
                           </button>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </form>
   
   <div class="visible-md visible-lg" style="margin-bottom: 100px;"></div>
   
   <div class="modal" id="modal_new_password">
      <div class="modal-dialog">
         <div class="modal-content">
            <form name="f_new_password" action="index.php" method="post" class="form" role="form">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">
                     <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                  </button>
                  <h4 class="modal-title">¿Has olvidado la contraseña?</h4>
               </div>
               <div class="modal-body">
                  <div class="form-group">
                     <label>Usuario</label>
                     <input type="text" name="user" class="form-control" maxlength="12" placeholder="Usuario" autocomplete="off"/>
                  </div>
                  <div class="form-group">
                     <label>Nueva contraseña</label>
                     <input type="password" class="form-control" name="new_password" maxlength="32" placeholder="Nueva contraseña" required=""/>
                     <input type="password" class="form-control" name="new_password2" maxlength="32" placeholder="Repite la nueva contraseña" required=""/>
                  </div>
                  <div class="form-group">
                     <label>Contraseña de la base de datos</label>
                     <input type="password" class="form-control" name="db_password" placeholder="Contraseña de la base de datos" required=""/>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="submit" class="btn btn-sm btn-warning">
                     <span class="glyphicon glyphicon-wrench"></span> &nbsp; Cambiar
                  </button>
               </div>
            </form>
         </div>
      </div>
   </div>
   
   <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("feedback") . ( substr("feedback",-1,1) != "/" ? "/" : "" ) . basename("feedback") );?>

   
   <hr style="margin-top: 50px;"/>
   
   <div class="container-fluid" style="margin-bottom: 10px;">
      <div class="row">
         <?php if( FS_DB_HISTORY ){ ?>

         <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="panel panel-default">
               <div class="panel-heading">
                  <h3 class="panel-title">Consultas SQL:</h3>
               </div>
               <div class="panel-body">
                  <ol style="font-size: 11px; margin: 0px; padding: 0px 0px 0px 20px;">
                  <?php $loop_var1=$fsc->get_db_history(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?><li><?php echo $value1;?></li><?php } ?>

                  </ol>
               </div>
            </div>
         </div>
         <?php } ?>

      </div>
      <div class="row">
         <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
            <small>
               Creado por <a target="_blank" href="">Miguel San Martin</a>.
            </small>
         </div>
         <div class="col-lg-4 col-md-4 col-sm-4 hidden-xs text-center">
            <span class="label label-default">Consultas: <?php echo $fsc->selects();?></span>
            <span class="label label-default">Transacciones: <?php echo $fsc->transactions();?></span>
         </div>
         <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 text-right">
            <span class="label label-default">
               <span class="glyphicon glyphicon-time" aria-hidden="true" title="Página procesada en <?php echo $fsc->duration();?>"></span>
               &nbsp; <?php echo $fsc->duration();?>

            </span>
         </div>
      </div>
   </div>
</body>
</html>