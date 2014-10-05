<?

// Definición del contenido del fichero xml.
// ¡ Es importante escribir los elementos en mayúsculas !
// Estas matrices se acaba de rellenar más abajo con los nombres de los campos.
$begin_array = array(
	"DATOSXML"     => "<HR><H2>LECTURA DE DATOS</H2>",
	"CAMPOS"       => "<HR><H3>CAMPOS</H3>",
        "CAMPO"        => "<UL>",
        "NOMBRE_CAMPO" => "<LI>Nombre: <font color=blue><b>",
        "TIPO"         => "<LI>Tipo: <font color=blue><b>",
        "LONGITUD"     => "<LI>Longitud: <font color=blue><b>",
        "DATOS"        => "<H3>DATOS</H3>",
        "REGISTRO"     => "<P>");
			  

$end_array = array(
        "DATOSXML"     => "<BR>",
        "CAMPOS"       => "<BR>",
        "CAMPO"        => "</UL>",
        "NOMBRE_CAMPO" => "</b></font></LI>",
        "TIPO"         => "</b></font></LI>",
        "LONGITUD"     => "</b></font></LI>",
        "DATOS"        => "",
        "REGISTRO"     => "<BR>");

class XML 
{
	var $filename; // Nombre del fichero xml del que se lee
	
	// Constructor de la clase
	function XML($nombre_fichero)
	{
		$this->filename=$nombre_fichero;
	}
	
	function read_xml(){
		$nombre_campo=false;
		function startElement($parser, $name, $attrs){
			global $begin_array, $nombre_campo;
			if ($htmlexpr = $begin_array[$name]) {
				// Esto sirve para acabar de rellenar la matriz con los campos.
				$nombre_campo=($name=="NOMBRE_CAMPO"); 		
				print "$htmlexpr";
			}
		}

		function endElement($parser, $name){
			global $end_array;
			if ($htmlexpr = $end_array[$name]) {print "$htmlexpr";}
		}

		function characterData($parser, $data) {
			global $begin_array, $end_array, $nombre_campo;
			if ($nombre_campo){
				$begin_array+=array(strtoupper($data)  => "Contenido campo <b>$data</b>: <font color=blue><b>");
				$end_array+=array(strtoupper($data)  => "</b></font><BR>");
				$nombre_campo=false;
			}
			print $data;
		}
		
		// Creamos un objeto xml_parser.
		$xml_parser = xml_parser_create();
		
		// Opciones del objeto xml: no tener en cuenta las minúsculas.
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, true);
		
		// Manejadores de elemento inicial y final: qué tiene que hacer.
		xml_set_element_handler($xml_parser, "startElement", "endElement");
		
		// Manejador para los datos
		xml_set_character_data_handler($xml_parser, "characterData");

		if (!($fp = fopen($this->filename, "r"))) 
			{die("Error! no se puede abrir el fichero: ".$this->filename);}
	
		// Leemos el fichero en bloques de 4KB.
		while ($data = fread($fp, 4096)) {
			if (!xml_parse($xml_parser, $data, feof($fp))) {
				die(sprintf("Error XML : %s en la l&iacute;nea %d",
						xml_error_string(xml_get_error_code($xml_parser)),
						xml_get_current_line_number($xml_parser)));
			}
		}
		echo "<HR>";
	
		// Liberamos memoria.
		xml_parser_free($xml_parser);
	
	} // end de la función read_xml

} // end de la clase XML

?>
