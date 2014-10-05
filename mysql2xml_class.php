<?

class MYSQL2XML 
{
	var $SQLStr;
	var $ErrorLog; 	// Log con todos los errores
	var $filename;  // Nombre del fichero xml que se genera
	// la conexión se pasa desde index.php y se genera en dat_url.php
	function MYSQL2XML($SQL, $nombre_fichero,$id_con)
	{
		$this->ErrorLog="OK";
		$this->SQLStr=$SQL;
		$this->filename=$nombre_fichero;
		$this->link=$id_con;
	}
	function doXML()
	{
		if ($this->SQLStr=="")
		{
		  $this->ErrorLog="La consulta SQL no puede ser vac&iacute;a";
		  return -1;
		}
		$resultado = mysql_query($this->SQLStr, $this->link);
		if ($resultado==false)
		{
			$this->ErrorLog="Error en la consulta SQL : ".$this->SQLStr;
			return -1;
		}
		
		$id_fichero=@fopen($this->filename,"w") 
      				or die("<B>El fichero '$this->filename' no se ha 
      				        podido crear.</B><P>");
		
		// Ahora creamos el fichero XML.
		fputs($id_fichero, "<?xml version=\"1.0\"?>\n");
		fputs($id_fichero, "<datosxml>\n");
		
		// Cabecera con el nombre de los campos
		fputs($id_fichero, "	<campos>\n");
		
		$matriz_Campos=array();
		for ($i=0; $i < mysql_num_fields($resultado); $i++) {
			$str = mysql_fetch_field($resultado);
			if ($str)
			{
				fputs($id_fichero, "		<campo>\n");
				fputs($id_fichero, "			<nombre_campo>".
				                    $str->name."</nombre_campo>\n");
				fputs($id_fichero, "			<tipo>".
				                    $str->type."</tipo>\n");
				fputs($id_fichero, "			<longitud>".
				                    $str->max_length."</longitud>\n");
				fputs($id_fichero, "		</campo>\n");
				$matriz_Campos[]=$str->name;
			}	
		} // end del for
		
		fputs($id_fichero, "	</campos>\n");
		
		// Ahora leemos los datos.
		fputs($id_fichero, "	<datos>\n");
		while ($row = mysql_fetch_array ($resultado))
		{
			fputs($id_fichero, "		<registro>\n");
			for ($j=0; $j<$i; $j++)
			{
				fputs($id_fichero, "			<".
				      $matriz_Campos[$j].">".$row[$j].
				      "</".$matriz_Campos[$j].">\n");
			}// end del for

			fputs($id_fichero, "		</registro>\n");

		}// end del  while
		
		fputs($id_fichero, "	</datos>\n");
		fputs($id_fichero, "</datosxml>");
		
		// Liberamos recursos de la memoria.
		mysql_free_result($resultado);	
		mysql_close ($this->link);
		fclose($id_fichero);
	} // end de la  función doXML

} // end de la clase MYSQL2XML

?>