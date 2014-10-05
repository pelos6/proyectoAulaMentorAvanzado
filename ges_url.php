<html>
<body>
<?
include ("dat_url.php");
?>

 <TABLE border='0' width='100%' bgcolor='#00A8A8'> 
     <TR><TD><font color='#EDFEDF'>Gestión de Filtrado Web</font></TD></TR>
	 <TR><TD ><font color='#EDFEDF'>Ventana de Gestión de Url filtradas</font></TD></TR>
</table>

<TABLE border='0' width='100%'>
     <TR>
		 <TD width='50%'>  	 <form action=<?$_SERVER['PHP_SELF']?>?ope=nue_url method="POST" name="form2">
			<input type="SUBMIT" value="Nueva URL a filtrar" name="alta">
		</form>
		</TD>
			 <td width='50%' align='center'> 
			 <img src='images/usuario.jpg'   width='130' height='121' alt='logo de Gestión de URL' title='Filtrado de URL'/>	
		</TD>
	 </TR>
</table> 
<?
 // chequeando los datos para el alta y la edición
            if (!isset($_REQUEST["cod_usu"]))$cod_usu="";
            else {$cod_usu=$_REQUEST["cod_usu"];}
            if (!isset($_REQUEST["cod_url"]))$cod_url="";
            else {$cod_url=$_REQUEST["cod_url"];}
			if (!isset($_REQUEST["des_url"]))$des_url="";
            else {$des_url=$_REQUEST["des_url"];}
			if (!isset($_REQUEST["id"]))$id="";//la primary key del registro a tratar
            else {$id=$_REQUEST["id"];}
  //Si no llega operación la operación por defecto es listas las url
    if (!isset($_REQUEST["ope"]))$ope="lis_url";
    else {$ope=$_REQUEST["ope"];}
	//echo("url borrar/editar:".$id."-operacion:".$ope."-<br>");
	// Si llega operación conecto, monto la sentencia SQL , la ejecuto y libero recursos
	if (strlen($ope)>0) {
	$id_con = con();
	$tex_sen = "select cod_usu, cod_url, des_url from url_usu where cod_usu =".$_COOKIE['cod_usu'];
		switch ($ope) {
                case "lis_url"://me conecto, monto la sentencia SQL , la ejecuto y libero recursos
					lis_url($id_con,$tex_sen,$ope);
                    break;
                case "nue_url"://pantalla para nuevo registro
                    nue_url();
                    break;
                case "nue_alt_url"://control de datos y alta articulo
                    if (strlen($des_url)==0) {echo("No se puede realizar la operación. Se debe indicar una URL ");}
                    else {
                        $tex_alt_url = "insert into url_usu values(".$_COOKIE['cod_usu']." , ".nex_cod_url()." , '".$des_url."')";
                        eje_sen($id_con,$tex_alt_url);
                        lis_url($id_con,$tex_sen,$ope);}
                    break;
                case "del_url"://borrar una url
                    if (strlen($id)==0) {echo("No se puede borrar una URL si no sabemos el código");}
                    else {
                        $tex_del_url="delete from url_usu where cod_usu = ".$_COOKIE['cod_usu']." and  cod_url = $id ";
                        eje_sen($id_con,$tex_del_url);
                        lis_url($id_con,$tex_sen,$ope);}
                    break;
                case "edi_url"://pantalla para editar un registro
                    if (strlen($id)==0) {echo("No se puede editar una url si no sabemos el código");}
                    edi_url($id_con,$id);
                    break;
                case "nue_edi_url"://control de datos y edición de un registro
                    if (strlen($des_url)==0) {echo("No se puede realizar la operación. Se debe indicar una URL ");}
                    else {
                        $tex_edi_url = "update url_usu set des_url= '".$des_url."' where cod_usu = ".$_COOKIE['cod_usu']." and cod_url = $id ";
						eje_sen($id_con,$tex_edi_url);
                        lis_url($id_con,$tex_sen);}
                    break;
                default:
                    echo("Error Grave. Llega operacion no controlada $ope");
            }	
		clo($id_con);
		}
 ?>
 </table>
 <br>
 <p align="center">
  <a href='index.php' > Página inicial</a> </p>
  </body>
  </html>
