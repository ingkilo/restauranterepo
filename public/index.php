<?php

require __DIR__ . '/../vendor/autoload.php';
date_default_timezone_set('Europe/Berlin');

$app = new \Slim\App(['settings' => ['displayErrorDetails' => true]]);
// Instantiate the app

// Set up dependencies
function getDB(){
	/*
	$dbHost = "localhost:3307";//nombre database
	$dbName = "mirestaurante";//nombre database
	$dbUser = "root";//nombre usuario database
	$dbPass = "";//password database
	*/
	
	$dbHost = "localhost:3307";//nombre database
	$dbName = "mirestaurante";//nombre database
	$dbUser = "root";//nombre usuario database
	$dbPass = "";//password database
	
	$mysql_conn_string = "mysql:host=$dbHost;dbname=$dbName";
	$dbConection = new PDO($mysql_conn_string, $dbUser, $dbPass);
	$dbConection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbConection;

}

$app->add(function($request, $response, $next) {
    $route = $request->getAttribute("route");

    $methods = [];

    if (!empty($route)) {
        $pattern = $route->getPattern();

        foreach ($this->router->getRoutes() as $route) {
            if ($pattern === $route->getPattern()) {
                $methods = array_merge_recursive($methods, $route->getMethods());
            }
        }
        //Methods holds all of the HTTP Verbs that a particular route handles.
    } else {
        $methods[] = $request->getMethod();
    }
    
    $response = $next($request, $response);


    return $response->withHeader("Access-Control-Allow-Methods", implode(",", $methods));
});

$app->get('/pedido/all', function($request,$response, $args){
	try{
		$db = getDB();
		$sth = $db->prepare("SELECT * FROM pedido pe where pe.est_ped = 1");
		$sth->execute();
		$categorias = $sth->fetchAll(PDO::FETCH_ASSOC);
		if($categorias){
			$response = $response->withJson($categorias);
			$db =null;
		}else{
			$response->write('{"error":""}');
		}
	}catch(PDOExeption $e){
		$response->write('{"error": "texto:'.$e->getMessage().'"}');
	}
	return $response;
});

$app->get('/pedido/datospedido/all', function($request,$response, $args){
	try{
		$db = getDB();
		$sth = $db->prepare("SELECT * FROM datos_pedido");
		$sth->execute();
		$categorias = $sth->fetchAll(PDO::FETCH_ASSOC);
		if($categorias){
			$response = $response->withJson($categorias);
			$db =null;
		}else{
			$response->write('{"error":""}');
		}
	}catch(PDOExeption $e){
		$response->write('{"error": "texto:'.$e->getMessage().'"}');
	}
	return $response;
});


$app->get('/pedido/datospedido/id/{id}', function($request,$response, $args){
	try{
		$db = getDB();
		$sth = $db->prepare("SELECT PR.NOMBRE_PRO AS NOMBRE,  DP.CANTIDAD AS CANTIDAD, (DP.CANTIDAD*PR.VALOR) AS TOTAL FROM DATOS_PEDIDO DP, PRODUCTO PR WHERE DP.PEDIDO =:id AND DP.PRODUCTO= PR.ID_PRO");
		$sth->bindParam(":id", $args["id"], PDO::PARAM_INT);
		$sth->execute();
		$categorias = $sth->fetchAll(PDO::FETCH_ASSOC);
		if($categorias){
			$response = $response->withJson($categorias);
			$db =null;
		}else{
			$response->write('{"error":""}');
		}
	}catch(PDOExeption $e){
		$response->write('{"error": "texto:'.$e->getMessage().'"}');
	}
	return $response;
});

$app->put('/update', function($request,$response){
	try{
		$data = $request->getParams();
		$db = getDB();
		$sth = $db->prepare("UPDATE PEDIDO PE SET PE.EST_PED = 2 WHERE PE.ID_PED = ?");
		$sth->execute(array($data["id"]));
		$response->write('{"error":"ok"}');
		$db =null;
	}catch(PDOExeption $e){
		$response->write('{"error": "texto:'.$e->getMessage().'"}');
	}
	return $response;
});

$app->post('/add', function($request,$response, $args){
	try{
		$data = $request->getParams();
		$db = getDB();
		$sth = $db->prepare("INSERT producto (descripcion) VALUES (?)");
		$sth->execute(array($data["descripcion"]));
		$categorias = $sth->fetchAll(PDO::FETCH_ASSOC);
		$response->write('{"ok":"ok"}');
	}catch(PDOExeption $e){
		//$response = write('{"error":{"texto":'.$e->getMessage().'}}');
		$response->write('{"error": "texto:'.$e->getMessage().'"}');
	}
	return $response;
});


$app->get('/preparar/all', function($request,$response, $args){
	try{
		$db = getDB();
		$sth = $db->prepare("SELECT * FROM pedido pe where pe.est_ped = 2");
		$sth->execute();
		$categorias = $sth->fetchAll(PDO::FETCH_ASSOC);
		if($categorias){
			$response = $response->withJson($categorias);
			$db =null;
		}else{
			$response->write('{"error":""}');
		}
	}catch(PDOExeption $e){
		$response->write('{"error": "texto:'.$e->getMessage().'"}');
	}
	return $response;
});

$app->put('/preparar/update', function($request,$response){
	try{
		$data = $request->getParams();
		$db = getDB();
		$sth = $db->prepare("UPDATE PEDIDO PE SET PE.EST_PED = 3 WHERE PE.ID_PED = ?");
		$sth->execute(array($data["id"]));
		$response->write('{"error":"ok"}');
		$db =null;
	}catch(PDOExeption $e){
		$response->write('{"error": "texto:'.$e->getMessage().'"}');
	}
	return $response;
});

$app->get('/entrega/all', function($request,$response, $args){
	try{
		$db = getDB();
		$sth = $db->prepare("SELECT * FROM pedido pe where pe.est_ped = 3");
		$sth->execute();
		$categorias = $sth->fetchAll(PDO::FETCH_ASSOC);
		if($categorias){
			$response = $response->withJson($categorias);
			$db =null;
		}else{
			$response->write('{"error":""}');
		}
	}catch(PDOExeption $e){
		$response->write('{"error": "texto:'.$e->getMessage().'"}');
	}
	return $response;
});

$app->get('/completo/all', function($request,$response, $args){
	try{
		$db = getDB();
		$sth = $db->prepare("SELECT * FROM pedido pe where pe.est_ped = 2");
		$sth->execute();
		$categorias = $sth->fetchAll(PDO::FETCH_ASSOC);
		if($categorias){
			$response = $response->withJson($categorias);
			$db =null;
		}else{
			$response->write('{"error":""}');
		}
	}catch(PDOExeption $e){
		$response->write('{"error": "texto:'.$e->getMessage().'"}');
	}
	return $response;
});
// Run app
$app->run();