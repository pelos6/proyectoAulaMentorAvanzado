<html>
<body>
<?
include ("dat_url.php");
?>
 <TABLE border='0' width='100%' bgcolor='#00A8A8'> 
     <TR><TD><font color='#EDFEDF'>Gesti�n de Filtrado Web</font></TD></TR>
	 <TR><TD ><font color='#EDFEDF'>Ventana de Consulta de Url filtradas</font></TD></TR>
</table>

<TABLE border='0' width='100%' >
     <TR align="center">
	 <TD width='50%'>
		<form action=<?$_SERVER['PHP_SELF']?>?ope=bus_url method="POST" name="form1">
				Comprobar Direcci�n Web
                <input type="TEXT" size="100" value="" name="url_bus">
                <input type="SUBMIT" value="Buscar" name="boton_buscar">
		</form>
	 </TD>
	 <td width='50%' align='center'> 
	 <img src='images/usuario.jpg'   width='130' height='121' alt='logo de Gesti�n de URL' title='Filtrado de URL'/>	</td>
	 </TR>
</table> 

 <?
 // chequeando los valores de entrada para la busqueda
            if (!isset($_REQUEST["url_bus"]))$url_bus="";
            else {$url_bus=$_REQUEST["url_bus"];}
  //Si no llega operaci�n la operaci�n por defecto es nulo 
            if (!isset($_REQUEST["ope"]))$ope="";
            else {$ope=$_REQUEST["ope"];}
			// Si llega operaci�n conecto, monto la sentencia SQL , la ejecuto y libero recursos
			if (strlen($ope)>0) {
				if (strlen($url_bus)==0) {
                        echo(" <font color=red>&iexcl;ERROR!</font> La opci�n de busqueda necesita un patr�n de busqueda");}
				else {
					$id_con = con();
					$tex_sen = "select count(*) from url_usu where cod_usu =".$_COOKIE['cod_usu']." and des_url = '".$url_bus."' ";
					$dat=eje_sen($id_con,$tex_sen);
					$fil = mysql_fetch_row($dat);
					if ($fil[0]==0){
						echo "la URL ".$url_bus." NO est� entre las filtradas para el centro ".$_COOKIE['centro'];
					} else
					{
						echo "la URL ".$url_bus." est� entre las filtradas para el centro ".$_COOKIE['centro'];
					}
					mysql_free_result($dat);
					clo($id_con);
				}
			}
 ?>
 </table>
 <br>
 <p align="center">
  <a href='index.php' > P�gina inicial</a> </p>
  </body>
  </html>