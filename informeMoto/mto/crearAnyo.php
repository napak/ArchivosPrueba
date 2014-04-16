<?php
/**
 * NOTA:
 * No se ha comprobado el tiempo que puede tardar en hacer todo el proceso.
 */
set_time_limit(180); //Para que el servidor no piense que esta bloqueado y asi evitar que corte el proceso.
//crearMesesDias($_POST["fechaCreacion"]);//Indicamos el Año a crear Automaticamente
crearMesesDias("2014");//Indicamos el Año a crear manualmente
function crearMesesDias($anyo){
	// :::::::::::::::::::::::::::::::::::::: Variables Manuales
	$arrayMeses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre","Diciembre"); //Meses del Año
	$arrayDiasMes = array("31", "29", "31", "30", "31", "30", "31", "31", "30", "31", "30","31"); //Cantidad Dias
	// :::::::::::::::::::::::::::::::::::::: FIN VARIABLES ::::::::::::::::::::::::::::::::::::::
	// :::::::::::::::::::::::::::::::::::::: 
	/**
 	* Primero de todo se prepararan los arrays que se usaran despues para registrar nuevos datos
 	*/
	/**
 	* Listamos los centros deportivos
 	*/
	$arrayCentros = array(); // Creamos el Array Con los Centros
	$centros = wire('pages')->get('/centros/')->children; // Buscamos los Hijos de la Ruta Centros
	foreach ($centros as $centro ) {
		array_push($arrayCentros, $centro->name);	//Añadimos cada Centro al Array
	};
	/**
 	* Listamos los Horarios
 	*/
 	$arrayHorarios = array(); // Creamos el Array Con los Horarios
	$horas = wire('pages')->get('/configuracion/horarios/')->children; // Buscamos los Hijos de la Ruta Horarios
	foreach ($horas as $hora ) {
		array_push($arrayHorarios, $hora->name);	//Añadimos cada Hora al Array
	};
	/**
 	* Creacion de Año, Mes y Dia para cada una de las pistas.
 	* Para realizarlo primero tiene que listar los Deportes que hay en cada centro y
 	* las pistas que hay en cada Deporte.
 	*/
 		for ($a=0; $a<count($arrayCentros)-1 ; $a++) { 
 			$deportes = wire('pages')->get('/centros/'.$arrayCentros[$a].'/')->children; // Buscamos los Hijos de la Ruta del Centro
			foreach ($deportes as $deporte ) {
				$pistas = wire('pages')->get('/centros/'.$arrayCentros[$a].'/'.$deporte->name.'/')->children; // Buscamos los pistas
				foreach ($pistas as $pista ) {
					//Creamos año
					$page_anyo = new Page(); // Creamos un objeto de $page
					$page_anyo->template = 'anyo'; // Decimos que Template usaremos
					$page_anyo->parent = wire('pages')->get('/centros/'.$arrayCentros[$a].'/'.$deporte->name.'/'.$pista->name.'/'); // Le inidicamos quien sera el padre 
					$page_anyo->name = $anyo; // indicamos el nombre de la pagina
					$page_anyo->title = $anyo; // indicamos el titulo de la pagina
					// guardamos
					$page_anyo->save(); 
					//Creamos los meses
					for ($b =0; $b <= count($arrayMeses)-1; $b++) {
						$page_mes = new Page(); // Creamos un objeto de $page
						$page_mes->template = 'mes'; // Decimos que Template usaremos
						$page_mes->parent = wire('pages')->get('/centros/'.$arrayCentros[$a].'/'.$deporte->name.'/'.$pista->name.'/'.$anyo.'/'); // Le inidicamos quien sera el padre 
						$page_mes->name = strtolower($arrayMeses[$b]); // indicamos el nombre en minusculas.
						$page_mes->title = $arrayMeses[$b]; // indicamos el titulo de la pagina.
						// guardamos
						$page_mes->save(); 
						//Creamos los dias por mes 
						for ($c = 0; $c <= $arrayDiasMes[$b]-1; $c++) {
							$page_dia = new Page(); // Creamos un objeto de $page
							$page_dia->template = 'dia'; // Decimos que Template usaremos
							$page_dia->parent = wire('pages')->get('/centros/'.$arrayCentros[$a].'/'.$deporte->name.'/'.$pista->name.'/'.$anyo.'/'.strtolower($arrayMeses[$b]).'/'); // Le inidicamos quien sera el padre 

							$page_dia->name = $c+1; // indicamos el nombre de la pagina
							$page_dia->title = $c+1; // indicamos el titulo de la pagina
							$url = $page_dia->title;
							// guardamos
							$page_dia->save(); 
							//Creamos los Horarios
							for ($d = 0; $d <= count($arrayHorarios)-1; $d++) {
								$page_horario = new Page(); // Creamos un objeto de $page
								$page_horario->template = 'hora'; // Decimos que Template usaremos
								$page_horario->parent = wire('pages')->get('/centros/'.$arrayCentros[$a].'/'.$deporte->name.'/'.$pista->name.'/'.$anyo.'/'.strtolower($arrayMeses[$b]).'/'.$url.'/'); // Le inidicamos quien sera el padre 
								$page_horario->name = $arrayHorarios[$d]; // indicamos el nombre de la pagina
								$page_horario->title = $arrayHorarios[$d]; // indicamos el titulo de la pagina
								// guardamos
								$page_horario->save(); 
							}; //FIN FOR HORARIOS
						}; //FIN FOR DIAS
					}; //FIN FOR MESES
				};//FIN FOREACH PISTAS
			}; //FIN FOREACH DEPORTES
 		}; // FIN FOR CENTROS
	echo "Finalizado con Exito!<br>";
};
?>