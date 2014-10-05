<?php
/* Archivo para la gestión de url
/*****************
Para que este script funcione, hay que escribir al principio del script
que se desea proteger las siguientes líneas:

include ("dat_url.php");
init();

y al final del todo, la siguiente línea:

echo showSalida();

*****************/
  /*  
	$ser="localhost:3306";
    $usu="root";
    $cla="javier"; 
$DBHost="localhost";
$DBUser="admin";
$DBPass="aliaj33";
*/
/*
$ser="localhost:3306";
$usu_bbdd="root";
$cla="";
$clave="Distinto o extinto"; 
*/
$ser="localhost:3306";
$usu_bbdd="root";
$cla="javier";
$clave="javier";

  
    
// Abrimos el módulo del algoritmo que vamos a utilizar MCRYPT_TripleDES
// e indicamos el modo MCRYPT_MODE_ECB.
$td = mcrypt_module_open(MCRYPT_TripleDES, "", MCRYPT_MODE_ECB, "");

// Creamos un vector que sirve de semilla para iniciar el proceso de encriptación.
$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size ($td), MCRYPT_RAND);

// Inicializamos los buferes antes de la encriptación.
mcrypt_generic_init($td, $clave	, $iv);
    
if (isset($_COOKIE['user'])) $user = trim(mdecrypt_generic($td, $_COOKIE['user']));
else $user='';
    
if (isset($_COOKIE['pass'])) $pass = trim(mdecrypt_generic($td, $_COOKIE['pass']));
else $pass='';
     
  
/* if (isset($_COOKIE['user'])) $user=$_COOKIE['user'];
else $user='';
    
if (isset($_COOKIE['pass'])) $pass=$_COOKIE['pass'];
else $pass=''; */
    
// Esta función activa el buffer de salida y no permite
// que se muestre ningún texto en la pantalla.
function init() {
	// se para la salida por pantalla del echo
    ob_start();
}
    
// Esta función muestra la salida de todo al final del script.
function showSalida() {
    global $user, $pass;
    // Si el usuario ha sido verificado...
    if (authUser($user, $pass, $des_cen, $cod_usu, false)) {  
        $c = ob_get_contents();
        ob_end_clean();
        return $c;
    } else {  // Si el usuario no ha sido verificado...
        $c = ob_get_contents();
        ob_end_clean();
        return showLoginPage();
    }
}

// Esta función comprueba que el usuario dado existe.
function authUser($userStr, $passStr, &$des_cen, &$cod_usu, $acceso) {
    /* Intentamos establecer una conexión con el servidor.*/
    $id_conexion = @mysql_connect($ser, $usu_bbdd ,$cla);
    // Si la BD no está activa, no permitimos el paso del usuario.
    if (!$id_conexion) {
        echo "<CENTER><H3>No se ha podido establecer la
                 conexi&oacute;n  <P>Compruebe si est&aacute;
                 activado el servidor de bases de datos MySQL.
                 </H3></CENTER>";
	echo "$ser, $usu_bbdd,$cla"; 
          return false;
      }
                
    /* Intentamos seleccionar la base de datos "urlFilter	".
       Si no se consigue, se informa de ello y se indica cuál es el
         motivo del fallo con el número y el mensaje de error.*/
    if (!mysql_select_db("urlFilter")) {
        printf("<CENTER><H3>No se ha podido seleccionar la base de
                 datos \"urlFilter\": <P>%s",'Error nº '.
                 mysql_errno().'.-'.mysql_error());
        return false;
    }
    
    $SQL = "SELECT * FROM usu WHERE nom_usu='$userStr'";
    $datos = mysql_query($SQL);
    $row = mysql_fetch_array($datos);
    $resultado = ($row['nom_usu']!="")
                      && ($passStr == $row['pas']);
        
    if ($resultado && $acceso ) {
        $SQL = "UPDATE usu SET ult_acc=now()
             WHERE nom_usu='$userStr'";
        mysql_query($SQL);
        $SQL = "SELECT des_cen,cod_usu FROM usu WHERE nom_usu='$userStr'";
        $datos = mysql_query($SQL);
        $row = mysql_fetch_array($datos);
		$des_cen= $row[0];
	    $cod_usu= $row[1];
    }
    mysql_close($id_conexion);
    return $resultado;
}

// Esta función muestra la pantalla donde se pregunta el login.
function showLoginPage() {
  return "
   <TABLE border='0' width='100%' bgcolor='#00A8A8'> 
     <TR><TD><font color='#EDFEDF'>Gestión de Filtrado Web</font></TD></TR>
	 <TR><TD ><font color='#EDFEDF'>Ventana de Login</font></TD></TR>
</table>
     <TABLE border='0' width='100%' height='100%'>
     <TR><TD width='33%' height='33%'></TD>
             <TD valign='top'>
             <FORM action=''>
               <INPUT type=hidden name=operacion value='login'>
               <TABLE border='0' width='100%'>
               <TR><TD colspan=2><U><B>Identif&iacute;cate para
                           acceder</B></U></TD></TR>
               <TR><TD>Usuario:</TD><TD>
                           <INPUT name=usuario></TD></TR>
               <TR><TD>Password:</TD><TD>
                          <INPUT type=password name=clave></TD></TR>
               <TR><TD align=center colspan=2>
                          <INPUT type=submit value=Acceder></TD></TR>
               </TABLE>
             </FORM>
             </TD>
             <TD width='33%' height='33%'></TD>
     </TR>
     </TABLE>";
}

function login($SELF, $userStr, $passStr) {
	global $td, $clave;
    if (authUser($userStr,$passStr, $des_cen,$cod_usu, true)) {
	    setcookie("user", mcrypt_generic($td, $userStr));
        setcookie("pass", mcrypt_generic($td, $passStr)); 
		setcookie("centro", $des_cen);
		setcookie("cod_usu", $cod_usu);
        header("Location: http://localhost".$SELF);
    } else return "<font color=red>&iexcl;ERROR!</font> Login o password incorrecto";
}

function logout() {
    setcookie("user", "");
    setcookie("pass", "");
    setcookie("centro", "");
	setcookie("cod_usu", "");
    header("Location: index.php");
}

if (isset($_REQUEST['operacion'])) {
    switch ($_REQUEST['operacion']) {
    case "login":
            print login($HTTP_SERVER_VARS['PHP_SELF'], $_REQUEST['usuario'], $_REQUEST['clave']);
        break;
    case "logout":
        print logout();
        break;
    }
}
//Conectando a la base de datos
function con() {
    global $id_con;
/* Si la conexión No ha podido establecer, se informa de ello */
    $id_con = @mysql_connect($ser, $usu_bbdd, $cla)
        or die("<H3>No se ha podido establecer la conexión.
                  <P>Compruebe si está activado el servidor de bases de
                  datos MySQL.</H3>");
    // seleccionamos la base de datos
    mysql_select_db("urlFilter",$id_con);
    return $id_con;
}
// ejecuta la sentencia
function eje_sen($id_con,$tex_sen) {
    $dat =@mysql_query($tex_sen,$id_con) or die("<H3>No se ha podido realizar la consulta $id_con
        <P> $tex_sen -- MySQL.".mysql_errno()."  ".mysql_error()."</H3>");
    return $dat;
}
function clo($id_con) {
    $l_cle=@mysql_close($id_con) or die("<H3>No se ha podido cerrar la base de datos
                  <P> $id_con -- MySQL.</H3>");
}
function nex_cod_url(){
$id_con = con();
$tex = "select ifnull(max(cod_url),0) + 1 from url_usu where cod_usu = ".$_COOKIE['cod_usu']." ";
$dat=eje_sen($id_con, $tex);
$fil = mysql_fetch_row($dat);
return $fil[0];
}
//lista las url filtradas de un usuario
function lis_url($id_con,$con) {
    echo ("<table width=\"650\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\" align=\"center\">
                <tbody><tr>
                        <th bgcolor=\"Silver\"><font color=\"olive\">Código URL</font></th>
                        <th bgcolor=\"Silver\"><font color=\"olive\">Descripción URL </font></th>
                    </tr>");
      $dat=eje_sen($id_con,$con);
    while($fil = mysql_fetch_row($dat)) {
        /* $con_usu="select cod_usu from usu where usu.cod_bic =".$fil[0];
        $dat_usu=eje_sen($id_con,$con_usu); */
                echo(" <tr align='center'>
                            <td><font size=\"-1\"><b>".$fil[1]."</b></font></td>
                            <td><font size=\"-1\"><b>".$fil[2]."</b></font></td>
                             <td>
                                <table cellspacing=\"0\" cellpadding=\"3\" border=\"0\">
                                    <tbody>
                                        <tr>");
                                            echo(" <td>
                                                <a href=".$_SERVER['PHP_SELF']."?ope=edi_url&amp;id=".$fil[1].">Editar</a>
                                            </td>
                                            <td>
                                                <a href=".$_SERVER['PHP_SELF']."?ope=del_url&amp;id=".$fil[1].">Borrar</a>
                                            </td> ");
                                       echo( "</tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
				");
    }
    mysql_free_result($dat);
}
function nue_url() {
    echo ("
</TR></TABLE ><P><A NAME='ancla'></A><FONT color='olive'><h4><u>Nueva URL</u></h4></FONT>
<FORM name='form9' method='post' action=\"ges_url.php?ope=nue_alt_url\">
 <TABLE BORDER='0' cellspacing='10' cellpadding='0' align='center' width='600'>
    <TR>
        <TD bgcolor='silver' align=center width=140>
    	<FONT color='olive'>cod_url </FONT>
    	</TD>
        <TD>".nex_cod_url()." 
        </TD>
    </TR>
    <TR>
        <TD bgcolor='silver' align=center width=140>
        <FONT color='olive'>Des url</FONT>
        </TD>
        <TD>
		<input type='text' name='des_url' size='30' value = \"\" maxlength='30'>
        </TD>
    </TR>
 </TABLE>
<CENTER>
<INPUT type='hidden' NAME='id' value = '-1'>
                <INPUT TYPE='SUBMIT' NAME='pulsa' VALUE=\"Alta Url\">
</CENTER></FORM>
");

}
function edi_url($id_con,$id) {
    $con="select cod_usu,cod_url,des_url from url_usu where cod_usu = ".$_COOKIE['cod_usu']." and cod_url = $id";
    $dat=eje_sen($id_con,$con);
    $inf = mysql_fetch_row($dat);
    echo ("</TR></TABLE ><P><A NAME='ancla'></A><FONT color='olive'><h4><u>Modificar URL</u></h4></FONT>
              <FORM name='form9' method='post' action=".$_SERVER['PHP_SELF']."?ope=nue_edi_url>
    <TABLE BORDER='0' cellspacing='10' cellpadding='0' align='center' width='600'>
        <TR>
            <TD bgcolor='silver' align=center width=140>
                <FONT color='olive'>codigo url</FONT>
            </TD>
            <TD>".$inf[1]."
            </TD>
        </TR>
       <TR>
            <TD bgcolor='silver' align=center width=140>
                <FONT color='olive'>des url</FONT>
            </TD>
            <TD>
                <input type='text' name='des_url' size='25' value = \"".$inf[2]."\" maxlength='50'>
            </TD>
        </TR>

    </TABLE>
    <CENTER>
    <INPUT type='hidden' NAME='id' value = ".$inf[1].">
          <INPUT TYPE='SUBMIT' NAME='pulsa' VALUE=\"Modificar url\">
    </CENTER>
</FORM>");
    mysql_free_result($dat);

}

?> 