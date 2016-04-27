<?php if(!class_exists('raintpl')){exit;}?>    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("header") . ( substr("header",-1,1) != "/" ? "/" : "" ) . basename("header") );?>


<script type="text/javascript" src="plugins/servicios/view/jscolor/jscolor.js"></script>
<script type="text/javascript" src="plugins/servicios/view/js/collapse.js"></script>
<script type="text/javascript" src="plugins/servicios/view/js/transition.js"></script>
<script type="text/javascript" src="plugins/servicios/view/js/Moment.js"></script>
<script type="text/javascript" src="plugins/servicios/view/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="plugins/servicios/view/js/bootstrap-datetimepicker.es.js"></script>
<link rel="stylesheet" href="plugins/servicios/view/css/bootstrap-datetimepicker.css" />
<script type="text/javascript">
    function eliminar_estado(id)
    {
        if (confirm("¿Realmente desea eliminar el estado " + id + "?"))
            window.location.href = '<?php echo $fsc->url();?>&delete_estado=' + id + '#estados';
    }
    function check(id) {
        if ($("#" + id).is(":checked"))
        {
            $("#checked_" + id).prop("disabled", false);
            $("#checked_" + id).prop("checked", true);
        } else
        {
            $("#checked_" + id).prop("checked", false);
            $("#checked_" + id).prop("disabled", true);
        }
    }
    ;
    function mostrar_estados(mostrar)
    {
        if (mostrar)
        {
            $("#div_estados").show();
        } else
        {
            $("#div_estados").hide();
        }
    }
    $(document).ready(function () {
        if (window.location.hash.substring(1) == 'estados')
        {
            $('#tab_opciones a[href="#estados"]').tab('show');
            mostrar_estados(true);
        }
    });
</script>

<br/>

<div class="container">
    <div class="row">
        <div class="col-xs-6">
            <a href="<?php echo $fsc->url();?>" class="btn btn-sm btn-default" title="Recargar la página">
                <span class="glyphicon glyphicon-refresh"></span>
            </a>
            <a href="index.php?page=ventas_servicios" class="btn btn-sm btn-default">
                <span class="glyphicon glyphicon-arrow-left"></span> &nbsp; Atrás
            </a>
        </div>
        <div class="col-xs-6 text-right">
            <h2 style="margin-top: 0px;">Opciones</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <ul id="tab_opciones" class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#general" aria-controls="general" role="tab" data-toggle="tab" onclick="mostrar_estados(false)">
                        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                        <span class="hidden-xs">&nbsp; General</span>
                    </a>
                </li>
                <li role="presentation" style="">
                    <a href="#opciones-campos" aria-controls="opciones-campos" role="tab" data-toggle="tab" onclick="mostrar_estados(false)">
                        <span class="glyphicon glyphicon-check" aria-hidden="true"></span>
                        <span class="hidden-xs">&nbsp; Casillas</span>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#estados" aria-controls="estados" role="tab" data-toggle="tab" onclick="mostrar_estados(true)">
                        <span class="glyphicon glyphicon-tags" aria-hidden="true"></span>
                        <span class="hidden-xs">&nbsp; Estados</span>
                    </a>
                </li>
                <li role="presentation" style=""> 
                    <a href="#traducciones" aria-controls="traducciones" role="tab" data-toggle="tab" onclick="mostrar_estados(false)">
                        <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
                        <span class="hidden-xs">&nbsp; Traducciones</span>
                    </a>
                </li>
                <li role="presentation" style="display: none">
                    <a href="#calendario" aria-controls="calendario" role="tab" data-toggle="tab" onclick="mostrar_estados(false)">
                        <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                        <span class="hidden-xs">&nbsp; Calendario</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <form action="<?php echo $fsc->url();?>" method="post">
        <input type="hidden" name="servicios_setup" value="TRUE"/>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="general">
                <div class="row">
                    <div class="col-sm-4">
                        <br/>
                        <div class="form-group">
                            <label>
                                <input type="number" name="diasfin" value="<?php echo $fsc->servicios_setup['servicios_diasfin'];?>" class="form-control" autocomplete="off"/>
                                Días estimados para la finalización de los servicios.
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                       <br/>
                       <div class="checkbox">
                          <label>
                             <input type="checkbox" name="usar_direccion" value="TRUE"<?php if( $fsc->servicios_setup['usar_direccion'] ){ ?> checked="checked"<?php } ?>/>
                            Usar dirección cliente en búsquedas
                          </label>
                       </div>
                    </div>
                    <div class="col-sm-4 text-right">
                       <br/>
                       <button class="btn btn-sm btn-primary" type="submit" onclick="this.disabled = true;
                          this.form.submit();">
                          <span class="glyphicon glyphicon-floppy-disk"></span> &nbsp; Guardar
                       </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            Condiciones:
                            <textarea name="condiciones" class="form-control" rows="10"><?php echo $fsc->servicios_setup['servicios_condiciones'];?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="opciones-campos">
                <div class="row">
                    <div class="col-sm-12">
                        <p class="help-block">
                            Selecciona qué casillas quieres utilizar y cuales de ellas quieres
                            que sean requeridas (imprescindibles) para guardar.
                        </p>
                    </div>
                    <div class="col-sm-4">
                        <div class="panel panel-default">
                            <div class="panel-heading text-center">Material</div>
                            <div class="panel-body">
                                <div class="checkbox">
                                    <label>
                                        <input id="material" onclick="check(this.id)" type="checkbox" name="servicios_mostrar_material" value="TRUE"<?php if( $fsc->servicios_setup['servicios_mostrar_material'] ){ ?> checked="checked"<?php } ?>/>
                                        Mostrar Text Area Material
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input id="checked_material" type="checkbox" name="servicios_material" value="TRUE"<?php if( $fsc->servicios_setup['servicios_material'] ){ ?> checked="checked"<?php } ?><?php if( !$fsc->servicios_setup['servicios_mostrar_material'] ){ ?> disabled<?php } ?>/>
                                        Material requereido
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="panel panel-default">
                            <div class="panel-heading text-center">Estado Material</div>
                            <div class="panel-body">
                                <div class="checkbox">
                                    <label>
                                        <input id="matestado" onclick="check(this.id)" type="checkbox" name="servicios_mostrar_material_estado" value="TRUE"<?php if( $fsc->servicios_setup['servicios_mostrar_material_estado'] ){ ?> checked="checked"<?php } ?>/>
                                        Mostrar Text Area Estado material
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input id="checked_matestado" type="checkbox" name="servicios_material_estado" value="TRUE"<?php if( $fsc->servicios_setup['servicios_material_estado'] ){ ?> checked="checked"<?php } ?> <?php if( !$fsc->servicios_setup['servicios_mostrar_material_estado'] ){ ?> disabled<?php } ?>/>
                                        Estado material requerido
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="panel panel-default">
                            <div class="panel-heading text-center">Accesorios</div>
                            <div class="panel-body">
                                <div class="checkbox">
                                    <label>
                                        <input id="accesorios" onclick="check(this.id)" type="checkbox" name="servicios_mostrar_accesorios" value="TRUE"<?php if( $fsc->servicios_setup['servicios_mostrar_accesorios'] ){ ?> checked="checked"<?php } ?>/>
                                        Mostrar Text Area Accesorios
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input id="checked_accesorios" type="checkbox" name="servicios_accesorios" value="TRUE"<?php if( $fsc->servicios_setup['servicios_accesorios'] ){ ?> checked="checked"<?php } ?><?php if( !$fsc->servicios_setup['servicios_mostrar_accesorios'] ){ ?> disabled<?php } ?>/>
                                        Accesorios requerido
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-heading text-center">Descripción</div>
                            <div class="panel-body">
                                <div class="checkbox">
                                    <label>
                                        <input id="descripcion" onclick="check(this.id)" type="checkbox" name="servicios_mostrar_descripcion" value="TRUE"<?php if( $fsc->servicios_setup['servicios_mostrar_descripcion'] ){ ?> checked="checked"<?php } ?>/>
                                        Mostrar Text Area Descripción
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input id="checked_descripcion" type="checkbox" name="servicios_descripcion" value="TRUE"<?php if( $fsc->servicios_setup['servicios_descripcion'] ){ ?> checked="checked"<?php } ?><?php if( !$fsc->servicios_setup['servicios_mostrar_descripcion'] ){ ?> disabled<?php } ?>/>
                                        Descripción requerido
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-heading text-center">Solución</div>
                            <div class="panel-body">
                                <div class="checkbox">
                                    <label>
                                        <input id="solucion" onclick="check(this.id)" type="checkbox" name="servicios_mostrar_solucion" value="TRUE"<?php if( $fsc->servicios_setup['servicios_mostrar_solucion'] ){ ?> checked="checked"<?php } ?>/>
                                        Mostrar Text Area Solución
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input id="checked_solucion" type="checkbox" name="servicios_solucion" value="TRUE"<?php if( $fsc->servicios_setup['servicios_solucion'] ){ ?> checked="checked"<?php } ?><?php if( !$fsc->servicios_setup['servicios_mostrar_solucion'] ){ ?> disabled<?php } ?>/>
                                        Solución requerido
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="panel panel-default">
                            <div class="panel-heading text-center">Fecha Inicio</div>
                            <div class="panel-body">
                                <div class="checkbox">
                                    <label>
                                        <input id="inicio" onclick="check(this.id)" type="checkbox" name="servicios_mostrar_fechainicio" value="TRUE"<?php if( $fsc->servicios_setup['servicios_mostrar_fechainicio'] ){ ?> checked="checked"<?php } ?>/>
                                        Mostrar Fecha de inicio
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input id="checked_inicio" type="checkbox" name="servicios_fechainicio" value="TRUE"<?php if( $fsc->servicios_setup['servicios_fechainicio'] ){ ?> checked="checked"<?php } ?><?php if( !$fsc->servicios_setup['servicios_mostrar_fechainicio'] ){ ?> disabled<?php } ?>/>
                                        Fecha inicio requerido
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="panel panel-default">
                            <div class="panel-heading text-center">Fecha fin</div>
                            <div class="panel-body">
                                <div class="checkbox">
                                    <label>
                                        <input id="fin" onclick="check(this.id)" type="checkbox" name="servicios_mostrar_fechafin" value="TRUE"<?php if( $fsc->servicios_setup['servicios_mostrar_fechafin'] ){ ?> checked="checked"<?php } ?>/>
                                        Mostrar Fecha de fin
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input id="checked_fin" type="checkbox" name="servicios_fechafin" value="TRUE"<?php if( $fsc->servicios_setup['servicios_fechafin'] ){ ?> checked="checked"<?php } ?><?php if( !$fsc->servicios_setup['servicios_mostrar_fechafin'] ){ ?> disabled<?php } ?>/>
                                        Fecha fin requerido
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="panel panel-default">
                            <div class="panel-heading text-center">Garantia</div>
                            <div class="panel-body">
                                <div class="checkbox">
                                    <label>
                                        <input id="garantia" onclick="check(this.id)" type="checkbox" name="servicios_mostrar_garantia" value="TRUE"<?php if( $fsc->servicios_setup['servicios_mostrar_garantia'] ){ ?> checked="checked"<?php } ?>/>
                                        Mostrar  de garantia
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input id="checked_garantia" type="checkbox" name="servicios_garantia" value="TRUE"<?php if( $fsc->servicios_setup['servicios_garantia'] ){ ?> checked="checked"<?php } ?><?php if( !$fsc->servicios_setup['servicios_mostrar_garantia'] ){ ?> disabled<?php } ?>/>
                                        Garantia requerido
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-right">
                        <button class="btn btn-sm btn-primary" type="submit" onclick="this.disabled = true; this.form.submit();">
                            <span class="glyphicon glyphicon-floppy-disk"></span> &nbsp; Guardar
                        </button>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="traducciones">
                <div class="row">
                    <div class="col-sm-12">
                        <br/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <span class="text-uppercase">Servicios:</span>
                            <input type="text" name="st_servicios" value="<?php echo $fsc->servicios_setup['st_servicios'];?>" class="form-control" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <span class="text-uppercase">Servicio:</span>
                            <input type="text" name="st_servicio" value="<?php echo $fsc->servicios_setup['st_servicio'];?>" class="form-control" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <span class="text-uppercase">Material</span>
                            <input type="text" name="st_material" value="<?php echo $fsc->servicios_setup['st_material'];?>" class="form-control" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <span class="text-uppercase">Estado Material</span>
                            <input type="text" name="st_material_estado" value="<?php echo $fsc->servicios_setup['st_material_estado'];?>" class="form-control" autocomplete="off"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <span class="text-uppercase">Accesorios</span>
                            <input type="text" name="st_accesorios" value="<?php echo $fsc->servicios_setup['st_accesorios'];?>" class="form-control" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <span class="text-uppercase">Descripción</span>
                            <input type="text" name="st_descripcion" value="<?php echo $fsc->servicios_setup['st_descripcion'];?>" class="form-control" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <span class="text-uppercase">Solución</span>
                            <input type="text" name="st_solucion" value="<?php echo $fsc->servicios_setup['st_solucion'];?>" class="form-control" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <span class="text-uppercase">Fecha de inicio</span>
                            <input type="text" name="st_fechainicio" value="<?php echo $fsc->servicios_setup['st_fechainicio'];?>" class="form-control" autocomplete="off"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <span class="text-uppercase">Fecha Fin</span>
                            <input type="text" name="st_fechafin" value="<?php echo $fsc->servicios_setup['st_fechafin'];?>" class="form-control" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <span class="text-uppercase">Garantía</span>
                            <input type="text" name="st_garantia" value="<?php echo $fsc->servicios_setup['st_garantia'];?>" class="form-control" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="col-sm-3 text-right">
                        <br/>
                        <button class="btn btn-sm btn-primary" type="submit" onclick="this.disabled = true;
                          this.form.submit();">
                            <span class="glyphicon glyphicon-floppy-disk"></span> &nbsp; Guardar
                        </button>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="calendario">
                <div class="row">
                    <div class="col-sm-12">
                        <br/>
                        Para el correcto funcionamiento del calendario debes tener activado <b>fecha de inicio y fecha de finalización</b> en las casillas.<br/>
                        Si no quieres usar la finaliación; desactiva la casilla de fecha de finalización y lo que hará es crear un evento desde el inicio + fracción del tiempo que elijas.<br/>
                        <h3>Uso</h3>
                        Para que el calendario esté activo mínimo debes tener fechainicio ( en tab casillas  ) activado.
                        El formato de introducción manual es: dd-mm-aaaa hh:mm. Si no es ese formato no dejará introducir valores.
                        <br/><br/>
                    </div>
                </div>
                <script type="text/javascript">
                    $(function () {
                        $('[data-toggle="tooltip"]').tooltip()

                        $('#cal_inicio').datetimepicker({
                            language: 'es',
                            format: 'HH:mm',
                            orientation: "left",
                            pickDate: false,
                        });
                        $('#cal_fin').datetimepicker({
                            language: 'es',
                            format: 'HH:mm',
                            orientation: "left",
                            pickDate: false,
                        });

                    });
                </script>
                <div class="row">
                    <div class='col-sm-3'>
                        <div class="form-group">
                            Hora de inicio del calendario
                            <div class='input-group date' id='cal_inicio'>
                                <input type='text' data-toggle="tooltip" data-placement="bottom" title="Hora : hh:mm" name="cal_inicio" pattern="([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]" value="<?php echo $fsc->servicios_setup['cal_inicio'];?>" class="form-control"/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class='col-sm-3'>
                        <div class="form-group">
                            Hora de fin del calendario
                            <div class='input-group date' id='cal_fin'>
                                <input type='text' data-toggle="tooltip" data-placement="bottom" title="Hora : hh:mm" name="cal_fin" pattern="([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]" value="<?php echo $fsc->servicios_setup['cal_fin'];?>" class="form-control"/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            Fracción de tiempo entre horas
                            <input type="number" name="cal_intervalo" value="<?php echo $fsc->servicios_setup['cal_intervalo'];?>" class="form-control" autocomplete="off" min="1" max="30"/>
                            De 1 a 30 minutos
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-right">
                        <br/>
                        <button class="btn btn-sm btn-primary" type="submit">
                            <span class="glyphicon glyphicon-floppy-disk"></span> &nbsp; Guardar
                        </button>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="estados"></div>
        </div>
    </form>
    <div class="row">
        <div id="div_estados" class="col-sm-12" style="display: none;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-left">Estado</th>
                            <th class="text-left">Descripción</th>
                            <th class="text-left">Color</th>
                            <th class="text-center">¿Activo?</th>
                            <th class="text-center">¿Generar <?php  echo FS_ALBARAN;?>?</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <?php $loop_var1=$fsc->estado->all(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?>

                    <form action="<?php echo $fsc->url();?>#estados" method="post" class="form">
                        <input type="hidden" name="id_estado" value="<?php echo $value1->id;?>"/>
                        <tr>
                            <td><div class="form-control"><?php echo $value1->id;?></div></td>
                            <td><input type="text" name="descripcion" value="<?php echo $value1->descripcion;?>" class="form-control" autocomplete="off"/></td>
                            <td><input type="text" name="color" value="<?php echo $value1->color;?>" class="form-control color" autocomplete="off" maxlength="6"/></td>
                            <td class="text-center">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="activo" value="TRUE"<?php if( $value1->activo ){ ?> checked="checked"<?php } ?>/>
                                    </label>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="albaran" value="TRUE"<?php if( $value1->albaran ){ ?> checked="checked"<?php } ?>/>
                                    </label>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="btn-group">
                                    <?php if( $fsc->allow_delete ){ ?>

                                    <?php if( $fsc->estado->tiene_servicios($value1->id) ){ ?>

                                    <a class="btn btn-sm btn-warning" href="index.php?page=ventas_servicios&estado=<?php echo $value1->id;?>" title="No lo puedes eliminar porque tiene servicios asociados">
                                        <span class="glyphicon glyphicon-lock"></span>
                                    </a>
                                    <?php }else{ ?>

                                    <a class="btn btn-sm btn-danger" href="#" onclick="eliminar_estado('<?php echo $value1->id;?>')" title="Eliminar">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </a>
                                    <?php } ?>

                                    <?php } ?>

                                    <button type="submit" class="btn btn-sm btn-primary" onclick="this.disabled = true;this.form.submit();" title="Guardar">
                                        <span class="glyphicon glyphicon-floppy-disk"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </form>
                    <?php } ?>

                    <form action="<?php echo $fsc->url();?>#estados" method="post" class="form">
                        <tr class="bg-info">
                            <td><input type="text" name="id_estado" value="<?php echo $fsc->estado->get_nuevo_id();?>" class="form-control" autocomplete="off"/></td>
                            <td><input type="text" name="descripcion" value="" class="form-control" autocomplete="off"/></td>
                            <td><input type="text" name="color" value="FFFFFF" class="form-control color" autocomplete="off" maxlength="6"/></td>
                            <td class="text-center">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="activo" value="TRUE" checked="checked"/>
                                    </label>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="albaran" value="TRUE"/>
                                    </label>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-sm btn-primary" onclick="this.disabled = true;this.form.submit();" title="Guardar">
                                        <span class="glyphicon glyphicon-floppy-disk"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </form>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("footer") . ( substr("footer",-1,1) != "/" ? "/" : "" ) . basename("footer") );?>

