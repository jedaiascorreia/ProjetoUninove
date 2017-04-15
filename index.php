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
	
	//Usuário
	$app->post('/novousuario','postUsuario');
	function postUsuario(){
		$request = \Slim\Slim::getInstance()->request();
		$usuario = json_decode($request->getBody());
		
		$sql = "INSERT INTO	usuario (
									ra,
									senha,
									nome,
									email,
									status_id)
							values (
									:ra,
									:senha,
									:nome,
									:email,
									:status_id) ";
									
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
	
	//Prontuários
	$app->get('/prontuario/:usuario_id','getProntuario');
	function getProntuario($usuario_id){
		$conn = getConn();
		
		$sql = 	"SELECT * 
				FROM	prontuario	pr,
						estilo_de_vida	ev,
						exame_fisico	ef,
						anamnese	an
				WHERE	pr.usuario_id 			=		:usuario_id
				AND	pr.id_estilo_de_vida	= 		ev.id
				AND	pr.id_exame_fisico		=		ef.id
				AND	pr.id_anamnese		=		an.id";
		
		$stmt = $conn->prepare($sql);
		$stmt -> bindParam("usuario_id",$usuario_id);
		$stmt->execute();
		$pront = $stmt->fetchObject();
		echo json_encode($pront);
	}
	
	
	//Prontuário
	$app->post('/novoprontuario','postProntuario');
	function postProntuario(){
		$request = \Slim\Slim::getInstance()->request();
		$prontuario = json_decode($request->getBody());
		$sql = "INSERT INTO	prontuario (
										num_prontuario,
										usuario_ra,
										nome_medico,
										sexo,
										idade,
										peso,
										altura,
										comentario_final,
										usuario_id,
										id_estilo_de_vida,
										id_exame_fisico,
										id_anamnese,
										data_,
										data_edicao
									)
							values 	(	
										:num_prontuario,
										:usuario_ra,
										:nome_medico,
										:sexo,
										:idade,
										:peso,
										:altura,
										:comentario_final,
										:usuario_id,
										:id_estilo_de_vida,
										:id_exame_fisico,
										:id_anamnese,
										:data_,
										:data_edicao
									) ";
		$conn = getConn();
		$stmt = $conn->prepare($sql);
		$stmt->bindParam("num_prontuario",$prontuario->num_prontuario);
		$stmt->bindParam("usuario_ra",$prontuario->usuario_ra);
		$stmt->bindParam("nome_medico",$prontuario->nome_medico);
		$stmt->bindParam("sexo",$prontuario->sexo);
		$stmt->bindParam("idade",$prontuario->idade);
		$stmt->bindParam("peso",$prontuario->peso);
		$stmt->bindParam("altura",$prontuario->altura);
		$stmt->bindParam("comentario_final",$prontuario->comentario_final);
		$stmt->bindParam("usuario_id",$prontuario->usuario_id);
		$stmt->bindParam("id_estilo_de_vida",$prontuario->id_estilo_de_vida);
		$stmt->bindParam("id_exame_fisico",$prontuario->id_exame_fisico);
		$stmt->bindParam("id_anamnese",$prontuario->id_anamnese);
		$stmt->bindParam("data_",$prontuario->data_);
		$stmt->bindParam("data_edicao",$prontuario->data_edicao);
		$stmt->execute();
		$prontuario->id = $conn->lastInsertId();
		echo json_encode($prontuario);
	}
	
	//Prontuário
	$app->put('/editarprontuario/:id','putProntuario');
	function putProntuario($id){
		$request = \Slim\Slim::getInstance()->request();
		$prontuario = json_decode($request->getBody());
		
		$sql =	"UPDATE	prontuario
				SET			num_prontuario	=	:num_prontuario,
							usuario_ra		=	:usuario_ra,
							nome_medico 	=	:nome_medico,
							sexo	 		=	:sexo,
							idade 			=	:idade,
							peso 			= 	:peso,
							altura 			=	:altura,
							comentario_final	=	:comentario_final,
							usuario_id 		=	:usuario_id,
							id_estilo_de_vida 	=	:id_estilo_de_vida,
							id_exame_fisico	=	:id_exame_fisico,
							id_anamnese 		=	:id_anamnese,
							data_ 			= 	:data_,
							data_edicao 		= 	:data_edicao
				WHERE		id				=	:id";
		
		$conn = getConn();
		$stmt = $conn->prepare($sql);
		$stmt->bindParam("num_prontuario",$prontuario->num_prontuario);
		$stmt->bindParam("usuario_ra",$prontuario->usuario_ra);
		$stmt->bindParam("nome_medico",$prontuario->nome_medico);
		$stmt->bindParam("sexo",$prontuario->sexo);
		$stmt->bindParam("idade",$prontuario->idade);
		$stmt->bindParam("peso",$prontuario->peso);
		$stmt->bindParam("altura",$prontuario->altura);
		$stmt->bindParam("comentario_final",$prontuario->comentario_final);
		$stmt->bindParam("usuario_id",$prontuario->usuario_id);
		$stmt->bindParam("id_estilo_de_vida",$prontuario->id_estilo_de_vida);
		$stmt->bindParam("id_anamnese",$prontuario->id_anamnese);
		$stmt->bindParam("data_",$prontuario->data_);
		$stmt->bindParam("data_edicao",$prontuario->data_edicao);
		$stmt->bindParam("id",$prontuario->id);
		$stmt->execute();
		
		echo json_encode($prontuario);
	}
	
	//Prontuário
	//$app->delete('/deletarprontuario/:idPront','deleteProntuario');
	$app->run();
?>
























