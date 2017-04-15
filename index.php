<?php
	header('Content-Type: application/json;charset=utf-8');
	require 'Slim/Slim.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$app->response()->header('Content-Type', 'application/json;charset=utf-8');

	$app->get('/', function () {
		echo "Bem-vindo a API do Sistema de Medicina";
	});
	
	function getConn()
	{
		return new PDO('mysql:host=localhost;dbname=prjmedicina',
		'root',
		'',
		array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
		);
	}

	//Login
	$app->get('/login/:ra/:senha','getLogin');
	//Novo Usuário
	$app->post('/novousuario','postUsuario');
	//Visualizar Prontuários
	$app->get('/prontuario','getProntuario');
	//Novo Prontuário
	$app->post('/novoprontuario/','postProntuario');
	//Editar Prontuário
	$app->put('/editarprontuario/:idPront','putProntuario');
	//Deletar Prontuário
	$app->delete('/deletarprontuario/:idPront','deleteProntuario');
	
	
	function getLogin($ra,$senha){
		$conn = getConn();
		
		$sql = "SELECT * 
		FROM usuario 
		WHERE ra = :ra 
		AND senha = :senha";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam("ra",$ra);
		$stmt->bindParam("senha",$senha);		
		$stmt->execute();
		$login = $stmt->fetchObject();
		echo json_encode($login);
	}
	
		function postUsuario()
	{
		$request = \Slim\Slim::getInstance()->request();
		$usuario = json_decode($request->getBody());
		$sql = "INSERT INTO usuario (ra,senha,nome,email,status_id) values (:ra,:senha,:nome,:email,:status_id) ";
		$conn = getConn();
		$stmt = $conn->prepare($sql);
		$stmt->bindParam("ra",$usuario->ra);
		$stmt->bindParam("senha",$usuario->senha);
		$stmt->bindParam("nome",$usuario->nome);
		$stmt->bindParam("email",$usuario->email);
		$stmt->bindParam("status_id",$usuario->status_id);
		$stmt->execute();
		$usuario->id = $conn->lastInsertId();
		echo json_encode($usuario);
	}
	
	function getProntuario(){
		$conn = getConn();
		
		$sql = 	"SELECT * 
				FROM	prontuario	pr,
						usuario		usu,
						estilo_de_vida	ev,
						exame_fisico	ef,
						anamnese	an
				WHERE	pr.id_usuario 			=		usu.id_usuario
				AND	pr.id_estilo_de_vida	= 		ev.id_estilo_de_vida
				AND	pr.id_exame_fisico		=		ef.id_exame_fisico
				AND	pr.id_anamnese		=		an.id_anamnese";
		
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$pront = $stmt->fetchObject();
		echo json_encode($pront);
	}
	
	$app->run();
?>
























