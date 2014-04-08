<?php
// :::::::::::::::::::::::::::::::::::::: Variables AUTOMATICAS
session_start(); 
//$fechaRecibida = $_POST["datetime"]; //Variable recibida desde la web
$fechaRecibida = "2014-01-21"; //Variable Manual de Pruebas
$_SESSION["fechaBusqueda"] = $fechaRecibida;
$anyoRecibido = substr($fechaRecibida, 0,4);
$mesRecibido = quitaCeros(substr($fechaRecibida, 5,2));
$diaRecibido = quitaCeros(substr($fechaRecibida, 8,2));
// :::::::::::::::::::::::::::::::::::::: Variables MANUALES
$arrayMesesURL = array("enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre","diciembre"); //Meses del Año en URL
// :::::::::::::::::::::::::::::::::::::: Ejecucion de Funciones
comprobarDia($diaRecibido,$arrayMesesURL[$mesRecibido-1],$anyoRecibido);
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// :::::::::::::::::::::::::::::::::::::: FUNCIONES
function comprobarDia($dia,$mes,$anyo){
	/**
 	* Esta funcion se encarga de comprobar si existe datos en el dia seleccionado.
 	* En caso de tener datos, ira al generador de Informes, en caso contrario, creara el contenido.
 	*/
 	$contenido = wire('pages')->get('/practicas/'.$anyo.'/'.$mes.'/'.$dia.'/')->children;
$hijos = array(); //Array con todos los hijos
foreach ($contenido as $hijo ) {
	$id = $hijo->title;
	array_push($hijos, $id);
};
if (count($hijos) == 0) {
	echo "No hay datos...<br>";
	clonarMotoBase ($dia,$mes,$anyo);
} else {
	echo header("Location: /cubel/moto/buscador/comprobador/tabla/");//Cambiar en version Online
};
};
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
function clonarMotoBase ($dia,$mes,$anyo){
	/**
 	* Esta funcion se encarga de crear los datos necesarios para el dia seleccionado.
 	* Una vez creados, volvera a llamar a la funcion 'comprobarDia' para que haga otra
 	* la comprobacion y de ser aceptada, mostrara el informe.
 	*/
	echo "Creando Datos...<br>";
	$motos = wire('pages')->get("template=Motos")->children;
	$arrayMotos = array(); //Array con todas las motos
	$arrayMotosURL = array(); //Array con todas las motos
	foreach ($motos as $moto ) {
		$idMoto = $moto->motoIdentificador;
		array_push($arrayMotos, $idMoto);
		$URLMoto = $moto->name;
		array_push($arrayMotosURL, $URLMoto);
	};
	$hijosMotoBase = wire('pages')->get('/gestor/moto-base/')->children;
	$arrayMotoBase = array(); //Array con todos los Profesores
	$arrayMotoBaseURL = array(); //Array con todos los Profesores
	foreach ($hijosMotoBase as $hijoMB ) {
		$idHijo= $hijoMB->title;
		array_push($arrayMotoBase, $idHijo);
		$URLHijo = $hijoMB->name;
		array_push($arrayMotoBaseURL, $URLHijo);
	};
	//Creamos las motos
	//echo "-------- Creado Motos<br>";
	for ($c_moto = 0; $c_moto <= count($arrayMotos)-1; $c_moto++) {
	$page_moto = new Page(); // Creamos un objeto de $page
	$page_moto->template = 'moto'; // Decimos que Template usaremos
	$temp = $c_dia+1;
	$page_moto->parent = wire('pages')->get('/practicas/'.$anyo.'/'.$mes.'/'.$dia.'/'); // Le inidicamos quien sera el padre 
	$page_moto->name = $arrayMotosURL[$c_moto]; // indicamos el nombre de la pagina
	$page_moto->title = $arrayMotos[$c_moto]; // indicamos el titulo de la pagina
	// guardamos
	$page_moto->save(); 
	//Creamos las MOTOBASE
	for ($c_MB = 0; $c_MB <= count($arrayMotoBase)-1; $c_MB++) {
	$page_MB = new Page(); // Creamos un objeto de $page
	if ($arrayMotoBase[$c_MB]=="Profesor") {
		$page_MB->template = 'enlaceProfesor'; // Decimos que Template usaremos
	} else {
	$page_MB->template = 'hora'; // Decimos que Template usaremos
};
	$page_MB->parent = wire('pages')->get('/practicas/'.$anyo.'/'.$mes.'/'.$dia.'/'.$arrayMotosURL[$c_moto].'/'); // Le inidicamos quien sera el padre 
	$page_MB->name = $arrayMotoBaseURL[$c_MB]; // indicamos el nombre de la pagina
	$page_MB->title = $arrayMotoBase[$c_MB]; // indicamos el titulo de la pagina
	// guardamos
	$page_MB->save(); 
}; //FIN FOR MOTOBASE
}; //FIN FOR MOTOS
echo "Datos creados correctamente!<br>Llamando a los Resultados...<br>";
comprobarDia($dia,$mes,$anyo);
};
?>