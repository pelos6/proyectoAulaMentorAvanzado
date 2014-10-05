<?php

include ("dat_url.php");

init();

require('mysql2xml_class.php');
require('xml_class.php');
/*
Uso Funciones de Control de Salida. Como en el Ejemplo 3 
*/
/**** Empieza la protección del script. ****/
 echo" 

 <TABLE border='0' width='100%' bgcolor='#00A8A8'> 
     <TR><TD><font color='#EDFEDF'>Gestión de Filtrado Web</font></TD></TR>
	 <TR><TD ><font color='#EDFEDF'>Ventana de Menú</font></TD></TR>
</table>


 <FORM action''>
<TABLE border='0' width='100%'>
     <TR>
	 <TD width='50%'>  
		<INPUT type=hidden name=operacion value='logout'>
		<INPUT type=submit value='Cerrar sesión'></TD>
	 <td width='50%' align='center'> 
	 <img src='images/usuario.jpg'   width='130' height='121' alt='logo de Gestión de URL' title='Filtrado de URL'/>	</td>
	 </TR>
</table> 
</FORM> 
<br><br>
<table border='0' width='80%' align='center'>
	 <form action='con_url.php' method='POST' name='for_con'>
	 <TR>
		 <td><input type='SUBMIT' value='Consulta URLs filtradas' name='boton_buscar' title='Consultar URLs filtradas'></td>
	 </TR>
	 </form>
	<form action='ges_url.php' method='POST' name='for_ges'>
	 <TR>
		 <td><input type='SUBMIT' value='Gestión URLs filtradas' name='boton_gestion' title='Gestionar URLs filtradas'></td>
	 </TR>
	 </form>
	<form action=".$_SERVER['PHP_SELF']."?ope=gen_xml method='POST' name='for_xml'>
	 <TR>
		 <td><input type='SUBMIT' value='Generar XML de URLs filtradas' name='bot_xml' title='Generar XML de URLs filtradas'></td>
	 </TR>
	 </form>
</table>
 "
;

    if (!isset($_REQUEST["ope"]))$ope="lis_url";
    else {$ope=$_REQUEST["ope"];}
	
if (isset($_COOKIE['user'])) {
    echo '<P>Has accedido con el usuario(encriptado) con código : '.$_COOKIE['cod_usu'];
	echo '<BR>y tu centro es: '.$_COOKIE['centro'];
	echo '<BR>';
	if ((strlen($ope)>0) && ($ope=='gen_xml')){
	$nom_fic="filtrosDe".$_COOKIE['centro'].".xml";
	$id_con = con();
	// Creamos un objeto MYSQL2XML. 
	$el_XML=new MYSQL2XML("SELECT * from url_usu where cod_usu = '".$_COOKIE['cod_usu']."'", $nom_fic,$id_con);
		//Generamos el documento XML.
		$el_XML->doXML();	
		if (($el_XML->ErrorLog)=='OK'){
			echo "El fichero XML $nom_fic con los datos del centro ".$_COOKIE['centro']." se ha generado correctamente";
		} else {
			echo "Error al generar el XML del centro ".$_COOKIE['centro']." " .$el_XML->ErrorLog." ";
		}
	}	
	}   
/**** Finaliza la protección del script. ****/
echo showSalida();

?> 