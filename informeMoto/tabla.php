<?php
session_start(); 
if (!isset($_SESSION["fechaBusqueda"]) || $_SESSION["fechaBusqueda"] == ""){ 
	//En caso de no existir variable o que este vacia, reenvia a la pagina donde se selecciona el dia.
	echo header("Location: /cubel/moto/buscador/comprobador/"); //Cambiar en version Online.
}else{ 
	$fechaRecibida = $_SESSION["fechaBusqueda"]; 
};
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title></title>
<link rel="stylesheet" href="<?php echo $config->urls->templates ?>css/bootstrap.css" />
</head>
<body>

<?php
// :::::::::::::::::::::::::::::::::::::: Variables AUTOMATICAS
$_SESSION["fechaBusqueda"] = "";//Vaciamos la variable por si se vuelve a cargar la pagina (Evita errores)
$anyoRecibido = substr($fechaRecibida, 0,4);
$mesRecibido = quitaCeros(substr($fechaRecibida, 5,2));
$diaRecibido = quitaCeros(substr($fechaRecibida, 8,2));
// :::::::::::::::::::::::::::::::::::::: Variables MANUALES
$arrayMesesURL = array("enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre","diciembre"); //Meses del Año en URL
// :::::::::::::::::::::::::::::::::::::: Ejecucion de Funciones
buscarDatos($diaRecibido,$arrayMesesURL[$mesRecibido-1],$anyoRecibido);
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// :::::::::::::::::::::::::::::::::::::: FUNCIONES
function quitaCeros ($numero){
	/**
 	* Funcion para quitar los 0 en los dias del 01/09
 	*/
 	$digito1 = substr($numero, 0,1);
 	$digito2 = substr($numero, 1,1);
 	if ($digito1=="0") {
 		$resultado = $digito2;
 	} else {
 		$resultado = $numero;
 	};
 	return $resultado;
 };
 function buscarDatos ($dia,$mes,$anyo){
	/**
 	* Esta funcion se encarga de ir a buscar los datos que queremos y los mete en un array
 	* para que la siguiente funcion construya una tabla de ella.
 	*/
	$datosTabla = array(); //Array final
	$datos = wire('pages')->get('/practicas/'.$anyo.'/'.$mes.'/'.$dia.'/')->children;
	$arrayDatos = array(); //Array con todos los hijos de 'X' dia
	$arrayDatosURL = array(); //Array con todos los hijos de 'X' dia pero la URL
	foreach ($datos as $dato ) {
		$idDatoURL= $dato->name;
		array_push($arrayDatosURL, $idDatoURL);
		$idDato= $dato->title;
		array_push($arrayDatos, $idDato);
	};
	//Matriculas de motos
	for ($i=0; $i < count($arrayDatos); $i++) { 
		$datosTemp = array(); //Array con datos temporales
		array_push($datosTemp, $arrayDatos[$i]);//Se añaden datos al array con datos temporales
		$datosPracticas = wire('pages')->get('/practicas/'.$anyo.'/'.$mes.'/'.$dia.'/'.$arrayDatosURL[$i].'/')->children;
	$arrayDatosPracticas = array(); //Array con los datos de las practicas
	foreach ($datosPracticas as $datoPractica ) {
		//AQUI SE DEBERIA HACER UN IF PARA DECIR QUE SI LA VARIABLE ES 0 MUESTRE EL TITLE, SI ES SUPERIOR, MOSTRAR EL CONTENIDO
		//POR AHORA SE HA CREADO OTRA SOLUCION EN LA SIGUIENTE FUNCION, PERO NO SE DESCARTA QUE ESTA SEA LA OPCION BUENA.
		if ($i==0) {
			$idDatoPractica= $datoPractica->title;
		} else {
			if ($idDatoPractica= $datoPractica->template=="enlaceProfesor") {
				$idDatoPractica= $datoPractica->enlaceProfesor->profesorNombre;
			} else {
				$idDatoPractica= $datoPractica->horaAlumno->alumnoNombre;			
			}
		
		}
		array_push($arrayDatosPracticas, $idDatoPractica);
	};
	//Contenido interno de cada moto (Profesor, hora, hora, hora...)
	for ($e=0; $e < count($arrayDatosPracticas); $e++) { 
		array_push($datosTemp, $arrayDatosPracticas[$e]);//Añadiendo mas datos temporales al Array
	};//FIN FOR CONTENIDO INTERNO
	array_push($datosTabla, $datosTemp); //Añadimos todo el Array temporal al Array Final
	};//FIN FOR MOTOS
	//Creacion de la tabla
	crearTabla($datosTabla); // Se llama a la funcion pasandole el Array de Arrays.
};

function crearTabla($arrayTabla){
	/**
 	* Esta funcion se encarga transformar el array recibido
 	* para imprimir una tabla con sus datos en pantalla.
 	*/
 	$tabla = '<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">Resultados</div>
  <div class="panel-body">
    <p>...</p>
  </div>';
 	//$tabla .= '<table class="table" border=1 cellspacing=2 cellpadding=0 style="border-collapse: collapse" bordercolor="000000" width="'.$widthT.'%">';
 	$tabla .= '<table class="table">';
 	for ($i=0; $i < count($arrayTabla[0]); $i++) { 
 		$tabla.= '<tr>';
 		for ($e=0; $e < count($arrayTabla) ; $e++) { 
 			//AQUI HAY UNA POSIBLE SOLUCION AL PROBLEMA DE LA FUNCION ANTERIOR
 			//if ($e==0) {
 				$tabla.= '<td>'.$arrayTabla[$e][$i].'</td>';
 			//} else {
 			//	$tabla.= '<td>'.$arrayTabla[$e][$i].'</td>';
 				//$tabla.= '<td>'." fictico ".'</td>';
 			//};
	}; //FIN FOR COLUMNAS
	$tabla.= '</tr>';
	};//FIN FOR FILA
	$tabla.= '</table></div>';
	echo $tabla; //Mostramos informacion.
	$datosTabla = array(); //Array final
	//$datos = wire('pages')->get('/practicas/2014/enero/11/ibr1-9083glf/profesor1/')->enlaceProfesor;
	//$datos = wire('page')->find('pages_id=1095')->data;
	//field_profesornombre
	//echo "<br>->>".$datos."<<-<br>"; 
	$arrayDatos = array(); //Array con todos los hijos de 'X' dia
	$arrayDatosURL = array(); //Array con todos los hijos de 'X' dia pero la URL
	foreach ($datos as $dato ) {
		$idDatoURL= $dato->name;
		echo "<br>".$dato."<br>";
		echo "<br>".$dato->name."<br>";
		array_push($arrayDatosURL, $idDatoURL);
		$idDato= $dato->title;
		array_push($arrayDatos, $idDato);
	};
};
?>
</body>
</html>
