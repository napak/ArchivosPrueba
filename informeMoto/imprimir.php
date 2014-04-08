<?php 
// :::::::::::::::::::::::::::::::::::::: Variables AUTOMATICAS
session_start(); 
include("./includes/MPDF57/mpdf.php");
$fechaRecibida = $_POST["datetime"]; //Variable recibida desde la web
//$fechaRecibida = "2014-01-13"; //Variable Manual de Pruebas
//$profesorRecibido = $_POST["profesor"]; //Variable recibida desde la web
$profesorRecibido = "Profesor 1"; //Variable Manual de Pruebas
$anyoRecibido = substr($fechaRecibida, 0,4);
$mesRecibido = quitaCeros(substr($fechaRecibida, 5,2));
$diaRecibido = quitaCeros(substr($fechaRecibida, 8,2));
// :::::::::::::::::::::::::::::::::::::: Variables MANUALES
$arrayMesesURL = array("enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre","diciembre"); //Meses del Año en URL
// :::::::::::::::::::::::::::::::::::::: Ejecucion de Funciones
buscarDatos($diaRecibido,$arrayMesesURL[$mesRecibido-1],$anyoRecibido,$profesorRecibido);


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


 function buscarDatos ($dia,$mes,$anyo,$profesor){
	/**
 	* Esta funcion se encarga de ir a buscar los datos que queremos y los mete en un array
 	* para que la siguiente funcion construya una tabla de ella.
 	*/
	$arrayCampos = array(); //Array de la primera columna
	$campos = wire('pages')->get('/practicas/'.$anyo.'/'.$mes.'/'.$dia.'/resultados/')->children;
	foreach ($campos as $campo ) {
		$idDato= $campo->title;
		if ($idDato!="Profesor") {
		array_push($arrayCampos, $idDato);	
		}
	};

	$arrayMotos = array(); //Array para sacar todas las motos
	$arrayMotosNombre = array();
	$motos = wire('pages')->get('/practicas/'.$anyo.'/'.$mes.'/'.$dia.'/')->children;
	foreach ($motos as $moto ) {
		$idMoto= $moto->name;
		if ($idMoto!="Horarios") {
		array_push($arrayMotos, $idMoto);	
		array_push($arrayMotosNombre, $moto->motoIdentificador);	
		}
	};

	//echo "Profesor que Buscamos: ".$profesor."<br>";
	$arrayHoras = array();
	$arrayAlumnos = array();
	$arrayMotoPractica = array();
	$arrayPermisoAlumno = array();
	$arrayCodigoAlumno = array();
	$anyadirArray = "No";
	for ($i=0; $i <count($arrayMotos); $i++) { 
		$practicas = wire('pages')->get('/practicas/'.$anyo.'/'.$mes.'/'.$dia.'/'.$arrayMotos[$i].'/')->children;
			foreach ($practicas as $practica ) {
			if ($practica->template=="enlaceProfesor") {
				//echo "Template Profesor<br>";
				//echo "Profesor encontrado: ".$practica->enlaceProfesor->profesorNombre.".<br>";
				if ($practica->enlaceProfesor->profesorNombre==$profesor) {
					$anyadirArray = "Si";
					//echo "MOTO: ".$arrayMotos[$i]."<br>";
				} else {
					$anyadirArray = "No";
				}
				//echo "¿Es el profesor que buscamos?: ".$anyadirArray."<br>";
			} else {
				//echo "Template hora<br>";
				if ($anyadirArray == "Si") {
					//echo "Agregamos Datos al Array<br>";
				//echo "Hora: ".$idAlumnoTitle." - Alumno: ".$idAlumnoNombre."<br>";
				array_push($arrayHoras, $practica->title);
				array_push($arrayAlumnos, $practica->horaAlumno->alumnoNombre);
				array_push($arrayPermisoAlumno, $practica->horaAlumno->alumnoPermiso);
				array_push($arrayCodigoAlumno, $practica->horaAlumno->alumnoNumero);
				array_push($arrayMotoPractica, $arrayMotos[$i]);
				}
				
			};
		};
};
$table = '
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title></title>
<link rel="stylesheet" href="/cubel/moto/site/templates/css/bootstrap_imprimir.css" />
</head>
<body>

<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">'.$profesor.'</div>
  <div class="panel-body">
    <p>Fecha: '.$dia. ' de '.$mes. ' de '.$anyo.'</p>
  </div>';
//$table = '<table  border=1 cellspacing=2 cellpadding=0 style=\"border-collapse: collapse\" bordercolor=\"000000\" width=\"".$widthT."%\">';
 $table .= '<table class="table">';
$table .= '<tr><td>Horarios</td><td>Nº Alumno</td><td>Nombre</td><td>Moto</td><td>Permiso</td></tr>';
for ($a=0; $a < count($arrayCampos) ; $a++) { 
	$table .= '<tr><td>'.$arrayCampos[$a].'</td>';
	for ($o=0; $o <count($arrayHoras) ; $o++) { 
		if ($arrayCampos[$a]==$arrayHoras[$o]) {
			$table .='<td>'.$arrayCodigoAlumno[$o].'</td>';
			$table .='<td>'.$arrayAlumnos[$o].'</td>';
			$table .='<td>'.$arrayMotoPractica[$o].'</td>';
			$table .='<td>'.$arrayPermisoAlumno[$o].'</td>';
			$table .='</tr>';
		}
	}
}
$table .= '</table></body></html>';
//echo $table;


// :::::::::::::::::::::::::::::::::::::: Pie de Página
$piePag = "<table  border=1 cellspacing=2 cellpadding=0 style=\"border-collapse: collapse\" bordercolor=\"000000\" width=\"".$widthT."%\">
<tr>
<td width=8%>AM</td><td></td><td></td><td></td><td>A2.CC</td><td></td><td></td><td></td><td>A.CC</td><td></td><td></td><td></td></tr>
<tr>
<td width=8%>A1.CC</td><td></td><td></td><td></td><td>A2.CA</td><td></td><td></td><td></td><td>A.CA</td><td></td><td></td><td></td></tr>
<tr>
<td width=8%>A1.CA</td><td></td><td></td><td></td><td>AUT.CC</td><td></td><td></td><td></td><td>A1.AUT</td><td></td><td></td><td></td></tr>
<tr>
<td width=8%>AM.MAN</td><td></td><td></td><td></td><td>AUT.CA</td><td></td><td></td><td></td><td>A1.MAN</td><td></td><td></td><td></td></tr>
</table>
<br>Observaciones:<br><br><br><br><br>";

//imprimir($table,$piePag);
echo $table;







/*
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
	*/
};




















/*
// :::::::::::::::::::::::::::::::::::::: Parametros de la tabla
$fechaMostrar = $_POST["datetime"]; //Variable recibida desde la web
$practicas = $pages->get("template=practicas")->children;
$widthT = 100; //Ancho de la tabla en %
$arrayN = array("practicaFechaInicio", "practicaFechaFin", "practicaAlumno", "practicaPermiso", "practicaTipoCircuito", "practicaEscuela", "practicaMoto"); //Columnas a mostrar de la BASE de Datos
$arrayN2 = array("Firma Alumno"); //Columnas sin BD -> Para campos Vacios
$arrayT = array("50", "50", "50", "50", "50", "50", "50", "75"); //Ancho de cada columna
*/
//Creacion de la tabla
/* INFO Variables
* $i -> Variable Estandar usada en FOR. Como tenemos dos FOR al segundo usamos la siguiente Variable.
* $i2 -> Segunda Variable usada en el FOR 2
*/
/*
// :::::::::::::::::::::::::::::::::::::: Cabeceras de la Tabla y asignación de Tamaños
$tabla = "<table  border=1 cellspacing=2 cellpadding=0 style=\"border-collapse: collapse\" bordercolor=\"000000\" width=\"".$widthT."%\"><tr>";
for ($i = 0; $i <= count($arrayN)-1; $i++) {
	$nombreCol = $fields->get("name='".$arrayN[$i]."'")->label;
	$tabla .= "<td width=\"".$arrayT[$i]."px\">".$nombreCol."</td>";
};
for ($i2 = 0; $i2 <= count($arrayN2)-1; $i2++) {
	$tabla .= "<td width=\"".$arrayT[count($arrayN)+$i2]."px\">".$arrayN2[$i2]."</td>";
};
$tabla .= "</tr>";
// :::::::::::::::::::::::::::::::::::::: Insertado de Datos en la Tabla
foreach ($practicas as $practica ) {
	//IF para comprobar fecha, en caso de no ser la fecha proporcionada por la web, no se insertará.
	$t = $practica->practicaFechaInicio;
	$check = substr($t, 0, -6); //Eliminamos la fecha para dejar solo la hora
	if ($check == $fechaMostrar){
			$tabla .= "<tr>";
			$fHora = substr($practica->practicaFechaInicio, -5);
			$tabla .= "<td>".$fHora."</td>"; // Hora Inicio
			$fHora = substr($practica->practicaFechaFin, -5);
			$tabla .= "<td>".$fHora."</td>"; // Hora Fin
			$codigoAlumno = $pages->get("id=\"".$practica->practicaAlumno."\"")->alumnoNumero; //Codigo del Alumno
			$tabla .= "<td>".$codigoAlumno."<br>".$pages->get("id=\"".$practica->practicaAlumno."\"")->alumnoNombre."</td>"; // Nombre Alumno
			$tabla .= "<td>".$pages->get("id=\"".$practica->practicaPermiso."\"")->title."</td>"; // Tipo de Permiso
			$tabla .= "<td>".$pages->get("id=\"".$practica->practicaTipoCircuito."\"")->title."</td>"; // Tipo de Circuito
			$tabla .= "<td>".$pages->get("id=\"".$practica->practicaEscuela."\"")->title."</td>"; // Escuela
			$tabla .= "<td>".$pages->get("id=\"".$practica->practicaMoto."\"")->title."</td>"; // Moto
			$tabla .= "<td>"."</td>";	// Cambpo vacio para la firma del Alumno
			$tabla .= "</tr>";
	};
};
$tabla .= "</table>";
$con = $tabla;
// :::::::::::::::::::::::::::::::::::::: Pie de Página
$piePag = "<table  border=1 cellspacing=2 cellpadding=0 style=\"border-collapse: collapse\" bordercolor=\"000000\" width=\"".$widthT."%\">
<tr>
<td width=8%>AM</td><td></td><td></td><td></td><td>A2.CC</td><td></td><td></td><td></td><td>A.CC</td><td></td><td></td><td></td></tr>
<tr>
<td width=8%>A1.CC</td><td></td><td></td><td></td><td>A2.CA</td><td></td><td></td><td></td><td>A.CA</td><td></td><td></td><td></td></tr>
<tr>
<td width=8%>A1.CA</td><td></td><td></td><td></td><td>AUT.CC</td><td></td><td></td><td></td><td>A1.AUT</td><td></td><td></td><td></td></tr>
<tr>
<td width=8%>AM.MAN</td><td></td><td></td><td></td><td>AUT.CA</td><td></td><td></td><td></td><td>A1.MAN</td><td></td><td></td><td></td></tr>
</table>
<br>Observaciones:<br><br><br><br><br>";
*/
/**
* IMPRIMIR PDF
*
*/
//if(isset($_POST['impr'])){
	//imprimir($con1);
//}

//imprimir($con,$piePag);
function imprimir($contenido,$pie){
	ob_end_clean();
	$mpdf=new mPDF('utf-8','A4');
	$mpdf->debug = true;
	//$mpdf->SetHTMLHeader($contenido);
	$mpdf->SetHTMLFooter($pie);
	$mpdf->WriteHTML($contenido);
	$mpdf->Output();
//$mpdf->SetHTMLHeader($cabecera);
//$mpdf->SetHTMLFooter($pie);
//$mpdf->WriteHTML($cuerpo);


};
// Boton Imprimir
//echo "<form action=\"\" method=\"post\" target=\"_blank\"><input type=\"submit\" value=\"Imprimir\" name=\"impr\" /> </form>";
//include("./foot.inc"); 
?>