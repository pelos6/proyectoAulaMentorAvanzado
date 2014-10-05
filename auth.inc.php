<?

/*****************
Para que este script funcione, hay que escribir al principio del script
que se desea proteger las siguientes líneas:

include ("auth.inc.php");
init();

y al final del todo, la siguiente línea:

echo showSalida();

*****************/

$DBHost="localhost";
$DBUser="admin";
$DBPass="aliaj33";
    
if (isset($_COOKIE['user'])) $user=$_COOKIE['user'];
else $user='';
    
if (isset($_COOKIE['pass'])) $pass=$_COOKIE['pass'];
else $pass='';
    
// Esta función activa el buffer de salida y no permite
// que se muestre ningún texto en la pantalla.
function init() {
    ob_start();
}
    
// Esta función muestra la salida de todo al final del script.
function showSalida() {
    global $user, $pass;
    // Si el usuario ha sido verificado...
    if (authUser($user, $pass, $nivel, $des_cen, false)) {  
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
function authUser($userStr, $passStr, &$nivel, &$des_cen, $acceso) {
    /* Intentamos establecer una conexión con el servidor.*/
    $id_conexion = @mysql_connect($DBHost, $DBUser, $DBPass);
    // Si la BD no está activa, no permitimos el paso del usuario.
    if (!$id_conexion) {
        echo "<CENTER><H3>No se ha podido establecer la
                 conexi&oacute;n.<P>Compruebe si est&aacute;
                 activado el servidor de bases de datos MySQL.
                 </H3></CENTER>";
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
        $SQL = "SELECT niv_usu,des_cen FROM usu WHERE nom_usu='$userStr'";
        $datos = mysql_query($SQL);
        $row = mysql_fetch_array($datos);
        $nivel= $row[0];
		$des_cen= $row[1];
    }
    mysql_close($id_conexion);
    return $resultado;
}

// Esta función muestra la pantalla donde se pregunta el login.
function showLoginPage() {
  return "
     <TABLE border='0' width='100%' height='100%'>
     <TR><TD width='33%' height='2%'>Gestión de Filtrado Web</TD></TR>
	 <TR><TD width='33%' height='2%'>Ventana de Logeo</TD></TR>
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
     <TR><TD width='33%' height='33%'></TD><TR>
     </TABLE>";
}

function login($SELF, $userStr, $passStr) {
    if (authUser($userStr,$passStr, $nivel,$des_cen, true)) {
            setcookie("user", $userStr);
            setcookie("pass", $passStr);
			setcookie("nivel", $nivel);
			setcookie("centro", $des_cen);
            header("Location: http://localhost".$SELF);
    } else return "<font color=red>&iexcl;ERROR!</font> Login o password incorrecto";
}

function logout() {
    setcookie("user", "");
    setcookie("pass", "");
    setcookie("nivel", "");
    setcookie("centro", "");
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

?> 