<?
// separando las funciones de la pantalla
require ("dat_bic.php");
// que se gestiona en esta p�gina
define("que","ran");
var_que(que);
?>
<html>
    <LINK HREF="tubici.css" REL="stylesheet" TYPE="text/css">
    <head>
        <title>Proyecto TuBici - Gesti�n de <?echo(ucfirst(strtolower($tit_plu)))?></title>
    </head>
    <body>
        <table id="titulo">
            <tbody><tr>
                    <th>
                        GESTI�N DE <?echo($tit_plu)?>
                    </th>
                </tr>
            </tbody>
        </table><p>
        </p><center><p>
		inicio de c�digo mandado desde base de datos <br>
                <?php
                $id_con = con();
                $val = " <table  id='titulo'>
            <tbody>
                <tr >
                    <th>
                        TuBici
                    </th>
                </tr></tbody>
        </table>";
                $tex="select tex_ran from ran where cod_ran = 2 ";
                $dat =@mysql_query($tex,$id_con) or die("<H3>No se ha podido realizar la consulta $id_con
                <P> $tex -- MySQL.".mysql_errno()."  ".mysql_error()."</H3>");
                $fil = mysql_fetch_row($dat);
                $val = $fil[0];
                echo $val;
                ?>
		<br>fin de c�digo mandado desde base de datos 
                <a href="index.php"> P�gina inicial</a>
        </center>
    </body>
</html>


