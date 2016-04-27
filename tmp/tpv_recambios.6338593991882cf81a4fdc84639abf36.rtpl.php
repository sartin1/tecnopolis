<?php if(!class_exists('raintpl')){exit;}?><?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("header") . ( substr("header",-1,1) != "/" ? "/" : "" ) . basename("header") );?>


<?php if( $fsc->agente ){ ?>

   <?php if( $fsc->caja ){ ?>

      <?php if( $fsc->caja->codagente!=$fsc->user->codagente ){ ?>

      <div class="thumbnail">
         <img src='view/img/big_lock.png' alt="acceso denegado"/>
      </div>
      <?php }elseif( !$fsc->cliente_s ){ ?>

      <div class="alert alert-danger">No hay clientes. Debes añadir al menos uno.</div>
      <div class="thumbnail">
         <img src='view/img/fuuu_face.png' alt="acceso denegado"/>
      </div>
      <?php }else{ ?>

         <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("tpv_recambios2") . ( substr("tpv_recambios2",-1,1) != "/" ? "/" : "" ) . basename("tpv_recambios2") );?>

      <?php } ?>

   <?php }elseif( $fsc->terminal ){ ?>

   <script type="text/javascript">
      $(document).ready(function() {
         document.f_caja.d_inicial.select();
      });
   </script>
   <form name="f_caja" action="<?php echo $fsc->url();?>" method="post" class="form">
      <input type="hidden" name="terminal" value="<?php echo $fsc->terminal->id;?>"/>
      <div class="container">
         <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
               <div class="page-header">
                  <h1>Terminal <?php echo $fsc->terminal->id;?></h1>
                  
               </div>
               <div class="well">
                  <h2>¿Cuanto dinero hay en la caja?</h2>
                  <div class="form-group">
                     <input class="form-control" type="text" name="d_inicial" value="0.00" autocomplete="off"/>
                  </div>
                  <div class="text-right">
                     <button class="btn btn-sm btn-primary" type="submit" onclick="this.disabled=true;this.form.submit();">
                        <span class="glyphicon glyphicon-floppy-disk"></span> &nbsp; Guardar
                     </button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </form>
   <?php }else{ ?>

   <div class="container">
      <div class="row">
         <div class="col-sm-12">
            <div class="page-header">
               <h1>Elige un terminal para empezar</h1>
               <p class="help-block">
                  Una vez abierta la caja de un terminal, ningún otro usuario podrá
                  usarlo hasta que se cierre la caja.
               </p>
            </div>
         </div>
      </div>
      <div class="row">
         <?php $loop_var1=$fsc->results; $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?>

         <div class="col-sm-6">
            <a href="<?php echo $fsc->url();?>&terminal=<?php echo $value1->id;?>" class="btn btn-block btn-default">
               <span class="glyphicon glyphicon-print" aria-hidden="true"></span> &nbsp; Terminal <?php echo $value1->id;?>

            </a>
         </div>
         <?php }else{ ?>

         <div class="col-sm-12">
            <a href="index.php?page=tpv_caja#terminales" class="btn btn-block btn-info">
               <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span> &nbsp; Administrar terminales
            </a>
         </div>
         <?php } ?>

      </div>
   </div>
   <?php } ?>

<?php }else{ ?>

<div class="thumbnail">
   <img src='view/img/big_lock.png' alt="Acceso denegado"/>
</div>
<?php } ?>


<?php if( $fsc->terminal ){ ?>

<div class="hidden">
   <img src="http://localhost:10080?terminal=<?php echo $fsc->terminal->id;?>" alt="remote-printer"/>
</div>
<?php } ?>


<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("footer") . ( substr("footer",-1,1) != "/" ? "/" : "" ) . basename("footer") );?>