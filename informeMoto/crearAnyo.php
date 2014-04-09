<?php
crearMesesDias($_POST["fechaCreacion"]);//Indicamos el Año a crear Automaticamente
//crearMesesDias("2014");//Indicamos el Año a crear manualmente
function crearMesesDias($anyo){
	//echo "Espere...<br>";
	// :::::::::::::::::::::::::::::::::::::: Variables Manuales
	$arrayMeses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre","Diciembre"); //Meses del Año
	$arrayMesesURL = array("enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre","diciembre"); //Meses del Año en URL
	$arrayDiasMes = array("31", "29", "31", "30", "31", "30", "31", "31", "30", "31", "30","31"); //Cantidad Dias
	// :::::::::::::::::::::::::::::::::::::: FIN VARIABLES ::::::::::::::::::::::::::::::::::::::
	// :::::::::::::::::::::::::::::::::::::: 
	if ($_SESSION['mes']==0){
	//Creamos año
	//echo "Creando año '".$anyo."'<br>";
	$page_anyo = new Page(); // Creamos un objeto de $page
	$page_anyo->template = 'anyo'; // Decimos que Template usaremos
	$page_anyo->parent = wire('pages')->get('/practicas/'); // Le inidicamos quien sera el padre 
	$page_anyo->name = $anyo; // indicamos el nombre de la pagina
	$page_anyo->title = $anyo; // indicamos el titulo de la pagina
	// guardamos
	$page_anyo->save(); 
	};

	//Creamos los meses
	for ($c_mes = $_SESSION['mes']; $c_mes <= count($arrayMeses)-1; $c_mes++) {
	//echo "-- Creando mes '".$arrayMeses[$c_mes]."' en año '".$anyo."'<br>";
	$page_mes = new Page(); // Creamos un objeto de $page
	$page_mes->template = 'mes'; // Decimos que Template usaremos
	$page_mes->parent = wire('pages')->get('/practicas/'.$anyo.'/'); // Le inidicamos quien sera el padre 
	$page_mes->name = $arrayMesesURL[$c_mes]; // indicamos el nombre de la pagina
	$page_mes->title = $arrayMeses[$c_mes]; // indicamos el titulo de la pagina
	// guardamos
	$page_mes->save(); 
	

	//Creamos los dias por mes 
	//echo "---- Creando '".$arrayDiasMes[$c_mes]." dias' para el mes de '".$arrayMeses[$c_mes]."'<br>";
	for ($c_dia = 0; $c_dia <= $arrayDiasMes[$c_mes]-1; $c_dia++) {
	$page_dia = new Page(); // Creamos un objeto de $page
	$page_dia->template = 'dia'; // Decimos que Template usaremos
	$page_dia->parent = wire('pages')->get('/practicas/'.$anyo.'/'.$arrayMesesURL[$c_mes].'/'); // Le inidicamos quien sera el padre 
	$page_dia->name = $c_dia+1.; // indicamos el nombre de la pagina
	$page_dia->title = $c_dia+1; // indicamos el titulo de la pagina
	//echo "------ Creado dia '".$page_dia->title."'<br>";
	// guardamos
	$page_dia->save(); 
	}; //FIN FOR DIAS
	}; //FIN FOR TODO
	echo "Finalizado con Exito!<br>";
};
?>