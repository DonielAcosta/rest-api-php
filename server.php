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

header( 'Content-Type: application/json' );

// LEVANTAMOS EL ID DEL RECURSO BUSCADO
$resourceId = array_key_exists('resource_id', $_GET) ? $_GET['resource_id'] : '';

/* Checking the request method and then executing the code for that method. */
$method = $_SERVER['REQUEST_METHOD'];

switch ( strtoupper( $method ) ) {
  case 'GET';
    if( empty($resourceId)){
      echo json_encode($books);
    }else{
      if(array_key_exists( $resourceId, $books )){
        echo json_encode($books[$resourceId]);
      }else{
        http_response_code(404);
      }
    }
    break;
  case 'POST':
    $json = file_get_contents('php://input');

    $books[] =json_decode($json, true);
    // emitimos hacia la salida de la ultima clave del array
    echo array_keys($books)[count($books) -1 ];
    // echo json_encode($books);
    break;
  case 'PUT':
    //validar que el recurso exista
    if( !empty($resourceId) && array_key_exists($resourceId, $books)){
      $json = file_get_contents('php://input');

      $books[$resourceId] =json_decode($json, true);
      echo json_encode($books);

    }
      break;
  case 'DELETE':
    if( !empty($resourceId) && array_key_exists($resourceId, $books)){
      unset($books[$resourceId]);
    }
    echo json_encode($books);
    
    break;
}
