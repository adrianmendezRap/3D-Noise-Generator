<?php
/*
Pruebas rendimiento:
$row = 240; $cols = 320;
1 Frame : 123ms
2 Frames: 405ms
3 Frames: 690ms
4 Frames: 980ms
5 Frames: 1260ms
*/
// Fuck limits
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 0);

// Variables array
$cols = 256;
$row = 144;
$frames = 15;

// Valores a tener en cuenta
$minval = 0; // Como minimo puede ser 0
$maxval = 200; // Como maximo puede ser 255
$maxder = 24; // Entre 8 y 30 (puede subir hasta 255)

// Array de salida
$output = array();
$output["row"] = $row;
$output["cols"] = $cols;
$output["frames"] = $frames;
$output["current_frame"] = 0;
$output["reset_frames"] = 0;

// Por cada frame generamos el array 2D
for ($frame_counter=0; $frame_counter < $frames; $frame_counter++) {

	// Si es la primera ejecucion del frame generamos el array para almacenar el array 2D
	if (!isset($output[$frame_counter])){
		$output[$frame_counter] = array();
	}

	// Por cada una de las filas del array 2D generamos los valores de sus celdas
	for ($row_counter=0; $row_counter <= $row - 1; $row_counter++) {

		// Si es la primera ejecucion de la fila generamos el array para almacenar sus celdas
		if (!isset($output[$frame_counter][$row_counter])){
			$output[$frame_counter][$row_counter] = array();
		}

		// Por cada una de las celdas generamos el valor a añadir en ellas
		for ($col_counter=0; $col_counter <= $cols - 1; $col_counter++) {

			// Generamos una nueva modificacion aleatoria a aplicar en la media teniendo en cuenta la maxima derivacion
			$new_der_r = rand( (-1 * $maxder) , $maxder );
			$new_der_g = rand( (-1 * $maxder) , $maxder );
			$new_der_b = rand( (-1 * $maxder) , $maxder );

			// Obtenemos la media de los valores colindates y pasados de la celda a generar

			// Los valores colindates son: el anterior en la misma columna (X=-1), los tres superiores de la columna anterior (Y=-1,X=-1; Y=-1,X=0; Y=-1,X=1)
			// y los 9 valores colindates en el frame anterior
			// (F=-1,Y=-1,X=-1; F=-1,Y=-1,X=0; F=-1,Y=-1,X=1;  F=-1,Y=0,X=-1; F=-1,Y=0,X=0; F=-1,Y=0,X=1;  F=-1,Y=1,X=-1; F=-1,Y=1,X=0; F=-1,Y=1,X=1)
			// Tambien revisamos que no nos salgamos del array 3D revisando que ningun valor sea negativo (puesto que los array comienzan en 0)

			// Iniciamos las variables para almacenar los sumatorios asi como el contador de sumas realizadas
			$sum_val_r = 0;
			$sum_val_g = 0;
			$sum_val_b = 0;
			$count_sum = 0;

			// Primero revisamos si estamos en el inicio de la columna revisando que el valor anterior sea superior a 0
			// En ese caso lo consideramos como bueno y lo añadimos a la suma global
			// (X=-1)
			if ( $col_counter - 1 >= 0 ){

				$sum_val_r = $sum_val_r + $output[$frame_counter][$row_counter][$col_counter - 1]["R"];
				$sum_val_g = $sum_val_g + $output[$frame_counter][$row_counter][$col_counter - 1]["G"];
				$sum_val_b = $sum_val_b + $output[$frame_counter][$row_counter][$col_counter - 1]["B"];
				$count_sum = $count_sum + 1;

			}


			// Luego revisamos los tres valores de la columna superior revisando previamente que actualmente no estamos generando la primera columna de la tabla
			if ($row_counter - 1 >= 0){
				// Tambien comprobamos que la celda actual no sea la primera de la columna para evitar acceder a valores fuera del array 3D
				if ($col_counter - 1 >= 0){
					// Y=-1,X=-1 
					$sum_val_r = $sum_val_r + $output[$frame_counter][$row_counter - 1][$col_counter - 1]["R"];
					$sum_val_g = $sum_val_g + $output[$frame_counter][$row_counter - 1][$col_counter - 1]["G"];
					$sum_val_b = $sum_val_b + $output[$frame_counter][$row_counter - 1][$col_counter - 1]["B"];
					$count_sum = $count_sum + 1;

				}

				// Añadimos el valor de la casilla superior a la actual
				// Y=-1,X=0
				$sum_val_r = $sum_val_r + $output[$frame_counter][$row_counter - 1][$col_counter]["R"];
				$sum_val_g = $sum_val_g + $output[$frame_counter][$row_counter - 1][$col_counter]["G"];
				$sum_val_b = $sum_val_b + $output[$frame_counter][$row_counter - 1][$col_counter]["B"];
				$count_sum = $count_sum + 1;

				// Y por ultimo comprobamos que la celda actual no sea la ultima de la columna para evitar acceder a valores fuera del array 3D
				if ($col_counter + 1 <= $cols - 1 ){
					// Y=-1,X=1
					$sum_val_r = $sum_val_r + $output[$frame_counter][$row_counter - 1][$col_counter + 1]["R"];
					$sum_val_g = $sum_val_g + $output[$frame_counter][$row_counter - 1][$col_counter + 1]["G"];
					$sum_val_b = $sum_val_b + $output[$frame_counter][$row_counter - 1][$col_counter + 1]["B"];
					$count_sum = $count_sum + 1;

				}

			}


			// Por ultimo obtenemos los valores del frame anterior revisando primero que exista para poder obtener los valores
			if ( $frame_counter - 1 >= 0 ){

				// Primero revisamos si estamos en el inicio de la tabla revisando que el valor anterior sea superior a 0
				if ( $row_counter - 1 >= 0 ){

					if ($col_counter - 1 >= 0){
						// F=-1,Y=-1,X=-
						$sum_val_r = $sum_val_r + $output[$frame_counter - 1][$row_counter - 1][$col_counter - 1]["R"];
						$sum_val_g = $sum_val_g + $output[$frame_counter - 1][$row_counter - 1][$col_counter - 1]["G"];
						$sum_val_b = $sum_val_b + $output[$frame_counter - 1][$row_counter - 1][$col_counter - 1]["B"];
						$count_sum = $count_sum + 1;
					}

					// F=-1,Y=-1,X=0
					$sum_val_r = $sum_val_r + $output[$frame_counter - 1][$row_counter - 1][$col_counter]["R"];
					$sum_val_g = $sum_val_g + $output[$frame_counter - 1][$row_counter - 1][$col_counter]["G"];
					$sum_val_b = $sum_val_b + $output[$frame_counter - 1][$row_counter - 1][$col_counter]["B"];
					$count_sum = $count_sum + 1;

					if ($col_counter + 1 <= $cols - 1){
						// F=-1,Y=-1,X=1
						$sum_val_r = $sum_val_r + $output[$frame_counter - 1][$row_counter - 1][$col_counter + 1]["R"];
						$sum_val_g = $sum_val_g + $output[$frame_counter - 1][$row_counter - 1][$col_counter + 1]["G"];
						$sum_val_b = $sum_val_b + $output[$frame_counter - 1][$row_counter - 1][$col_counter + 1]["B"];
						$count_sum = $count_sum + 1;
					}
				}



				if ($col_counter - 1 >= 0){
					// F=-1,Y=0,X=-1
					$sum_val_r = $sum_val_r + $output[$frame_counter - 1][$row_counter][$col_counter - 1]["R"];
					$sum_val_g = $sum_val_g + $output[$frame_counter - 1][$row_counter][$col_counter - 1]["G"];
					$sum_val_b = $sum_val_b + $output[$frame_counter - 1][$row_counter][$col_counter - 1]["B"];
					$count_sum = $count_sum + 1;
				}

				// F=-1,Y=0,X=0
				$sum_val_r = $sum_val_r + $output[$frame_counter - 1][$row_counter][$col_counter]["R"];
				$sum_val_g = $sum_val_g + $output[$frame_counter - 1][$row_counter][$col_counter]["G"];
				$sum_val_b = $sum_val_b + $output[$frame_counter - 1][$row_counter][$col_counter]["B"];
				$count_sum = $count_sum + 1;

				if ($col_counter + 1 <= $cols - 1){
					// F=-1,Y=0,X=1
					$sum_val_r = $sum_val_r + $output[$frame_counter - 1][$row_counter][$col_counter + 1]["R"];
					$sum_val_g = $sum_val_g + $output[$frame_counter - 1][$row_counter][$col_counter + 1]["G"];
					$sum_val_b = $sum_val_b + $output[$frame_counter - 1][$row_counter][$col_counter + 1]["B"];
					$count_sum = $count_sum + 1;
				}



				// Primero revisamos si estamos en el inicio de la tabla revisando que el valor anterior sea superior a 0
				if ( $row_counter + 1 <= $row - 1){

					if ($col_counter - 1 >= 0){
						// F=-1,Y=-1,X=-1
						$sum_val_r = $sum_val_r + $output[$frame_counter - 1][$row_counter + 1][$col_counter - 1]["R"];
						$sum_val_g = $sum_val_g + $output[$frame_counter - 1][$row_counter + 1][$col_counter - 1]["G"];
						$sum_val_b = $sum_val_b + $output[$frame_counter - 1][$row_counter + 1][$col_counter - 1]["B"];
						$count_sum = $count_sum + 1;
					}

					// F=-1,Y=-1,X=0
					$sum_val_r = $sum_val_r + $output[$frame_counter - 1][$row_counter + 1][$col_counter]["R"];
					$sum_val_g = $sum_val_g + $output[$frame_counter - 1][$row_counter + 1][$col_counter]["G"];
					$sum_val_b = $sum_val_b + $output[$frame_counter - 1][$row_counter + 1][$col_counter]["B"];
					$count_sum = $count_sum + 1;

					if ($col_counter + 1 <= $cols - 1){
						// F=-1,Y=-1,X=1
						$sum_val_r = $sum_val_r + $output[$frame_counter - 1][$row_counter + 1][$col_counter + 1]["R"];
						$sum_val_g = $sum_val_g + $output[$frame_counter - 1][$row_counter + 1][$col_counter + 1]["G"];
						$sum_val_b = $sum_val_b + $output[$frame_counter - 1][$row_counter + 1][$col_counter + 1]["B"];
						$count_sum = $count_sum + 1;
					}
				}

			}



			// Una vez finalizada la obtencion de los valores revisamos el contador de sumas realizadas
			// Si sale 0 significa que estamos en el primer pixel a general asi que le damos unos valores random como inicio
			if ($count_sum == 0){
				$output[$frame_counter][$row_counter][$col_counter]["R"] = rand($minval, $maxval);
				$output[$frame_counter][$row_counter][$col_counter]["G"] = rand($minval, $maxval);
				$output[$frame_counter][$row_counter][$col_counter]["B"] = rand($minval, $maxval);
			// En caso contrario generamos la media de los valores sumados y le añadimos la derivacion
			}else{
				$temp_val_r = round($sum_val_r / $count_sum) + $new_der_r;
				$temp_val_g = round($sum_val_g / $count_sum) + $new_der_g;
				$temp_val_b = round($sum_val_b / $count_sum) + $new_der_b;

				// Revisamos que no nos salimos del valor maximo RGB
				if ( $temp_val_r < $minval){
					$temp_val_r = $minval;
				}elseif ($temp_val_r > $maxval) {
					$temp_val_r = $maxval;
				}

				if ( $temp_val_g < $minval){
					$temp_val_g = $minval;
				}elseif ($temp_val_g > $maxval) {
					$temp_val_g = $maxval;
				}

				if ( $temp_val_b < $minval){
					$temp_val_b = $minval;
				}elseif ($temp_val_b > $maxval) {
					$temp_val_b = $maxval;
				}


				$output[$frame_counter][$row_counter][$col_counter]["R"] = $temp_val_r;
				$output[$frame_counter][$row_counter][$col_counter]["G"] = $temp_val_g;
				$output[$frame_counter][$row_counter][$col_counter]["B"] = $temp_val_b;
			}

		}
	}

}


echo json_encode($output, JSON_FORCE_OBJECT);