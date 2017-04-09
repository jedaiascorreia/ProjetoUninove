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
	
	//Retorna Informações do Usuário
	$app->get('/usuario/usuario','getUsuario');
	//Adiciona Usuario
	$app->post('/usuario/insere','addUsuario');
	//Login
	$app->get('/usuario/login-ra=:ra','getLogin');
	//Editando Login
	$app->put('/usuario/editar-id=:id','editaUsuario');
	
	
	
	function getUsuario(){
		$stmt = getConn()->query("SELECT * FROM usuario");
		$categorias = $stmt->fetchAll(PDO::FETCH_OBJ);
		echo "{categorias:".json_encode($categorias)."}";
	}
	
	function addUsuario()
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
	
	function getLogin($ra){
		$conn = getConn();
		$sql = "SELECT * FROM usuario WHERE ra = :ra";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam("ra",$ra);
		$stmt->execute();
		$login = $stmt->fetchObject();
		echo json_encode($login);
	}
	
	function editaUsuario($id){
		$request = \Slim\Slim::getInstance()->request();
		$novousu = json_decode($request->getBody());
		$sql = "UPDATE usuario SET ra=:ra,senha=:senha,nome=:nome,email=:email,status_id=:status_id WHERE id=:id";
		$conn = getConn();
		$stmt = $conn->prepare($sql);
		$stmt->bindParam("ra",$novousu->ra);
		$stmt->bindParam("senha",$novousu->senha);
		$stmt->bindParam("nome",$novousu->nome);
		$stmt->bindParam("email",$novousu->email);
		$stmt->bindParam("status_id",$novousu->status_id);
		$stmt->bindParam("id",$id);
		$stmt->execute();

		echo json_encode($novousu);
	}
	$app->run();
?>
























