<?php
	header('Content-Type: application/json;charset=utf-8');
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: Content-Type");
	
	require 'Slim/Slim.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$app->response()->header('Content-Type', 'application/json;charset=utf-8');

	$app->get('/', function () {
		echo "Bem-vindo a API do Sistema de Medicina";
	});
	
	function getConn()
	{
		/*return new PDO('mysql:host=localhost;dbname=prjmedicina',
		'root',
		'',*/
		
		return new PDO('mysql:host=mysql.hostinger.com.br;dbname=u593040281_prj',
		'u593040281_root',
		'uninove10',
		
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
	
	//retorna_professores
	$app->get('/professores','getProfessores');
	function getProfessores(){
				
		$sql = 	"SELECT 	id,
							nome
				FROM 		usuario 
				WHERE 		upper(status_id) 	=	 'PROFESSOR'";
		
		$stmt = getConn()->query($sql);
		$professores = $stmt->fetchAll(PDO::FETCH_OBJ);
		echo json_encode($professores);
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
	
	
	//Prontuários_tcc
	$app->get('/prontuariotcc/:id_usuario','getProntuarioTcc');
	function getProntuarioTcc($id_usuario){
		$conn = getConn();
		
		$sql = 	"SELECT	* 
				FROM	PRONTUARIO_TCC
				WHERE	ID_USUARIO 			=		:id_usuario";
		
		$stmt = $conn->prepare($sql);
		$stmt -> bindParam("id_usuario",$id_usuario);
		$stmt->execute();
		$pront = $stmt->fetchObject();
		echo json_encode($pront);
	}
	
	//Prontuários_Tcc
	$app->get('/prontuariotccnotificacao/:id','getProntuarioTccNotificacao');
	function getProntuarioTccNotificacao($id){
		$conn = getConn();
		
		$sql = 	"SELECT	* 
				FROM		PRONTUARIO_TCC
				WHERE		ID 			=		:id";
		
		$stmt = $conn->prepare($sql);
		$stmt -> bindParam("id",$id);
		$stmt->execute();
		$pront = $stmt->fetchObject();
		echo json_encode($pront);
	}
	
	//Prontuário_Tcc
	$app->post('/novoprontuariotcc','postProntuarioTcc');
	function postProntuarioTcc(){
		$request = \Slim\Slim::getInstance()->request();
		$prontuariotcc = json_decode($request->getBody());
		$sql = "INSERT INTO	prontuario_tcc (
										usuario_ra,
										sexo,
										idade,
										peso,
										altura,
										fuma_frequentemente,
										fuma_doente,
										consome_alcool,
										queixa_paciente,
										diagnostico,
										id_professor,
										id_usuario
									)
							values 	(	
										:usuario_ra,
										:sexo,
										:idade,
										:peso,
										:altura,
										:fuma_frequentemente,
										:fuma_doente,
										:consome_alcool,
										:queixa_paciente,
										:diagnostico,
										:id_professor,
										:id_usuario
									) ";
		$conn = getConn();
		$stmt = $conn->prepare($sql);
		$stmt->bindParam("usuario_ra",	$prontuariotcc->usuario_ra);
		$stmt->bindParam("sexo",		$prontuariotcc->sexo);
		$stmt->bindParam("idade",		$prontuariotcc->idade);
		$stmt->bindParam("peso",		$prontuariotcc->peso);
		$stmt->bindParam("altura",		$prontuariotcc->altura);
		$stmt->bindParam("fuma_frequentemente",$prontuariotcc->fuma_frequentemente);
		$stmt->bindParam("fuma_doente",	$prontuariotcc->fuma_doente);
		$stmt->bindParam("consome_alcool",	$prontuariotcc->consome_alcool);
		$stmt->bindParam("queixa_paciente",	$prontuariotcc->queixa_paciente);
		$stmt->bindParam("diagnostico",		$prontuariotcc->diagnostico);
		$stmt->bindParam("id_professor",	$prontuariotcc->id_professor);
		$stmt->bindParam("id_usuario",		$prontuariotcc->id_usuario);
		$stmt->execute();
		$prontuariotcc->id = $conn->lastInsertId();
		echo json_encode($prontuariotcc);
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
	
	//Notificacao
	$app->get('/qtdnotificacao/:id_professor','getQtdNotificacao');
	function getQtdNotificacao($id_professor){
		$conn = getConn();
		
		$sql = 	"SELECT 	count(*)  Qtd_Notificacoes
				FROM 		notificacao 
				WHERE 		id_professor = :id_professor
				AND		status_notificacao = 'P'";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam("id_professor",$id_professor);		
		$stmt->execute();
		$notificacao = $stmt->fetchObject();
		echo json_encode($notificacao);
	}
	
	//Notificacao
	$app->get('/notificacao/:id_professor','getNotificacao');
	function getNotificacao($id_professor){
		$conn = getConn();
		
		$sql = 	"SELECT 	usu.nome,
							pront.id
				FROM 		notificacao 	noti,
							usuario		usu,
							prontuario_tcc	pront
				WHERE 		noti.id_professor				= 	:id_professor
				AND		upper(noti.status_notificacao) 	= 	'P'
				AND		noti.id_aluno					=	usu.id
				AND		noti.id_prontuario				=	pront.id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam("id_professor",$id_professor);		
		$stmt->execute();
		$notificacao = $stmt->fetchAll(PDO::FETCH_OBJ);
		echo json_encode($notificacao);
	}
	
	//Notificacao
	$app->post('/novanotificacao','postNotificacao');
	function postNotificacao(){
		$request = \Slim\Slim::getInstance()->request();
		$notificacao = json_decode($request->getBody());
		$sql = 	"INSERT INTO	notificacao
								(
									id_aluno,
									id_professor,
									id_prontuario,
									status_notificacao
								)
						values	(
									:id_aluno,
									:id_professor,
									:id_prontuario,
									:status_notificacao
								)";
		$conn = getConn();
		$stmt = $conn->prepare($sql);
		$stmt->bindParam("id_aluno",$notificacao->id_aluno);
		$stmt->bindParam("id_professor",$notificacao->id_professor);
		$stmt->bindParam("id_prontuario",$notificacao->id_prontuario);
		$stmt->bindParam("status_notificacao",$notificacao->status_notificacao);
		$stmt->execute();
		$notificacao->id = $conn->lastInsertId();
		echo json_encode($notificacao);
	}
	
	//Notificacao
	$app->put('/editarnotificacao/:id','putNotificacao');
	function putNotificacao($id){
		$request = \Slim\Slim::getInstance()->request();
		$notificacao = json_decode($request->getBody());
		
		$sql =	"UPDATE	notificacao
				SET			status_notificacao	=	'V'
				WHERE		id				=	:id";

		$conn = getConn();
		$stmt = $conn->prepare($sql);
		$stmt->bindParam("id",$id);
		$stmt->execute();
		
		echo json_encode($notificacao);
	}
	
	//comentario
	$app->get('/comentario/:id_prontuario','getComentario');
	function getComentario($id_prontuario){
		$conn = getConn();
		
		$sql = 	"SELECT 	comentario
				FROM 		comentario 
				WHERE 		id_prontuario = :id_prontuario";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam("id_prontuario",$id_prontuario);		
		$stmt->execute();
		$notificacao = $stmt->fetchObject();
		echo json_encode($notificacao);
	}
	
	//comentario
	$app->post('/novocomentario','postComentario');
	function postComentario(){
		$request = \Slim\Slim::getInstance()->request();
		$comentario = json_decode($request->getBody());
		$sql = "INSERT INTO	comentario	(	
											comentario,
											id_notificacao,
											id_prontuario
										)
							values		(
											:comentario,
											:id_notificacao,
											:id_prontuario
										)";
		$conn = getConn();
		$stmt = $conn->prepare($sql);
		$stmt->bindParam("comentario",$comentario->comentario);
		$stmt->bindParam("id_notificacao",$comentario->id_notificacao);
		$stmt->bindParam("id_prontuario",$comentario->id_prontuario);
		$stmt->execute();
		$comentario->id = $conn->lastInsertId();
		echo json_encode($comentario);
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
		$stmt->bindParam("data_",$prontuario->data_);
		$stmt->bindParam("data_edicao",$prontuario->data_edicao);
		$stmt->bindParam("id",$id);
		$stmt->execute();
		
		echo json_encode($prontuario);
	}
	
	//Prontuário
	$app->delete('/deletarprontuario/:id','deleteProntuario');
	function deleteProntuario($id){
		$conn = getConn();

		//estilo_de_vida
		$sql = 	"DELETE  
				FROM		estilo_de_vida 
				WHERE 		id	=	(
										SELECT		id_estilo_de_vida
										FROM		prontuario 
										WHERE 		id	=	:id
									)";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam("id",$id);
		$stmt->execute();
		
		//exame_fisico
		$sql = 	"DELETE  
				FROM		exame_fisico 
				WHERE 		id	=	(
										SELECT		id_exame_fisico
										FROM		prontuario 
										WHERE 		id	=	:id
									)";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam("id",$id);
		$stmt->execute();
		
		//anamnese
		$sql = 	"DELETE  
				FROM		anamnese 
				WHERE 		id	=	(
										SELECT		id_anamnese
										FROM		prontuario 
										WHERE 		id	=	:id
									)";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam("id",$id);
		$stmt->execute();
		
		//prontuario
		$sql = 	"DELETE 
				FROM		prontuario 
				WHERE 		id	=	:id";
				
		$conn = getConn();
		$stmt = $conn->prepare($sql);
		$stmt->bindParam("id",$id);
		$stmt->execute();
		
		echo "Prontuário Apagado";
	}
	$app->run();
?>
























