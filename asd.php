<?php 
// Creamos un directorio o carpeta  

$ruta1 = "plugin";
mkdir($ruta1);

$file = fopen('plugin/mifichero.txt', 'r');
fclose($file);
?> 
