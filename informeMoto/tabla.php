<?php
// :::::::::::::::::::::::::::::::::::::: Variables AUTOMATICAS
if (!isset($_SESSION["fechaBusqueda"]) || $_SESSION["fechaBusqueda"] == ""){ 
	//En caso de no existir variable o que este vacia, reenvia a la pagina donde se selecciona el dia.
	echo header("Location: /cubel/moto/buscador/comprobador/"); //Cambiar en version Online
}else{ 
	$fechaRecibida = $_SESSION["fechaBusqueda"]; 
};
//echo $fechaRecibida;
$_SESSION["fechaBusqueda"] = "";
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
	$arrayDatos = array(); //Array con todos los Profesores
	$arrayDatosURL = array(); //Array con todos los Profesores
	foreach ($datos as $dato ) {
		$idDatoURL= $dato->name;
		array_push($arrayDatosURL, $idDatoURL);
		$idDato= $dato->title;
		array_push($arrayDatos, $idDato);
	};
	//Matriculas de motos
	for ($i=0; $i < count($arrayDatos); $i++) { 
		$datosTemp = array(); //Array con datos temporales
		array_push($datosTemp, $arrayDatos[$i]);//Array con datos temporales
		$datosPracticas = wire('pages')->get('/practicas/'.$anyo.'/'.$mes.'/'.$dia.'/'.$arrayDatosURL[$i].'/')->children;
	$arrayDatosPracticas = array(); //Array con todos los Profesores
	foreach ($datosPracticas as $datoPractica ) {
		//AQUI SE DEBERIA HACER UN IF PARA DECIR QUE SI LA VARIABLE ES 0 MUESTRE EL TITLE, SI ES SUPERIOR, MOSTRAR EL CONTENIDO
		$idDatoPractica= $datoPractica->title;
		echo "<br>".wire('pages')->get("template=".$datoPractica->name)."<br>";
		array_push($arrayDatosPracticas, $idDatoPractica);
	};
	//Contenido interno de cada moto (Profesor, hora, hora, hora...)
	for ($e=0; $e < count($arrayDatosPracticas); $e++) { 
		array_push($datosTemp, $arrayDatosPracticas[$e]);//Array con datos temporales
	};//FIN FOR CONTENIDO INTERNO
	array_push($datosTabla, $datosTemp);//Añadimos array temporal al array Final
	};//FIN FOR MOTOS
	//Creacion de la tabla
	crearTabla($datosTabla); // Se llama a la funcion pasandole el Array de Arrays.
};

function crearTabla($arrayTabla){
	/**
 	* Esta funcion se encarga transformar el array recibido
 	* para imprimir una tabla con sus datos en pantalla.
 	*/
 	$tabla = '<table  border=1 cellspacing=2 cellpadding=0 style="border-collapse: collapse" bordercolor="000000" width="'.$widthT.'%">';
 	for ($i=0; $i < count($arrayTabla[0]); $i++) { 
 		$tabla.= '<tr>';
 		for ($e=0; $e < count($arrayTabla) ; $e++) { 
 			if ($e==0) {
 				$tabla.= '<td>'.$arrayTabla[$e][$i].'</td>';
 			} else {
 				$tabla.= '<td>'." fictico ".'</td>';
 			};
	}; //FIN FOR COLUMNAS
	$tabla.= '</tr>';
	};//FIN FOR FILA
	$tabla.= '</table>';
	echo $tabla;
};
?>