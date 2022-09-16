<?php


if ( !array_key_exists( 'HTTP_X_TOKEN', $_SERVER ) ) {

	die;
}

$url = 'https://'.$_SERVER['HTTP_HOST'].'/auth';

$ch = curl_init( $url );
curl_setopt( $ch, CURLOPT_HTTPHEADER, [
	"X-Token: {$_SERVER['HTTP_X_TOKEN']}",
]);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
$ret = curl_exec( $ch );

if ( curl_errno($ch) != 0 ) {
	die ( curl_error($ch) );
}

if ( $ret !== 'true' ) {
	http_response_code( 403 );

	die;
}


/* An array of the resources that are allowed to be used in the API. */
$allowedResourceTypes = [
	'books',
	'authors',
	'genres',
];

/* Getting the value of the key 'resource_type' from the  array. */
$resourceType = $_GET['resource_type'];

if ( !in_array( $resourceType, $allowedResourceTypes ) ) {
	header( 'Status-Code: 400' );
	echo json_encode(
		[
			'error' => "$resourceType is un unkown",
		]
	);
	
	die;
}

/* Creating an array of books. */
$books = [
	1 => [
		'titulo' => 'Lo que el viento se llevo',
		'id_autor' => 2,
		'id_genero' => 2,
	],
	2 => [
		'titulo' => 'La Iliada',
		'id_autor' => 1,
		'id_genero' => 1,
	],
	3 => [
		'titulo' => 'La Odisea',
		'id_autor' => 1,
		'id_genero' => 1,
	],
];

// Se indica al cliente que lo que recibirá es un json
header('Content-Type: application/json');

// Levantamos el id del recurso buscado
// utilizando un operador ternario
$resourceId = array_key_exists('resource_id', $_GET) ? $_GET['resource_id'] : '';

// Generamos la respuesta asumiendo que el pedido es correcto
switch (strtoupper($_SERVER['REQUEST_METHOD'])) {
  case 'GET';
    if( empty($resourceId)){
      echo json_encode($books);
    }else{
      if(array_key_exists( $resourceId, $books )){
        echo json_encode($books[$resourceId]);
      }
    }
    break;

	case 'POST':
		$json = file_get_contents('php://input');
		$books[] = json_decode($json,true);
		//echo array_keys($books)[count($books)-1];
		end($books);         // move the internal pointer to the end of the array
		$key = key($books);  // fetches the key of the element pointed to by the internal pointer
		echo json_encode($books[$key]);
 		break;

	case 'PUT':
		// Validamos que el recurso buscado exista
		if (!empty($resourceId) && array_key_exists($resourceId,$books)) {
			// Tomamos la entrada curda
			$json = file_get_contents('php://input');

			// Tansformamos el json recibido a un nuevo elemento
			$books[$resourceId] = json_decode($json,true);

			echo json_encode($books[$resourceId]);
		}
		break;

	case 'DELETE':
		// Validamos que el recurso buscado exista
		if (!empty($resourceId) && array_key_exists($resourceId,$books)) {
			unset($books[$resourceId]);
			echo json_encode($books);
		}
		break;
}



// Inicio el servidor en la terminal 1
// php -S localhost:8000 server.php

// Terminal 2 ejecutar 
// curl http://localhost:8000 -v
// curl http://localhost:8000/\?resource_type\=books
// curl http://localhost:8000/\?resource_type\=books | jq