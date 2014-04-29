<?php
	/*
		Jack Notifications V1.0 - Enviar Notificación
		Sistema de notificaciones en tiempo real PHP, nodejs
	*/

	/* Envía una notificación a travéz de un socket desde PHP a nodejs por el puerto 9091 */
	function _send_notification( $notification_id ){
		$host="127.0.0.1";
		$puerto=9091;
		$tamano=2048;
		$secret_key = "SOQI6N3m8lApYX3MyUbpm7ZBSCzagCmtAKCPMhSKbom";
		$json_data = array(
			"secret"=>$secret_key,
			"notification_id"=>$notification_id
		);

		$socket=socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
		$conexion=socket_connect($socket,$host,$puerto);
		if($conexion){
			$buffer=json_encode($json_data);
			socket_write($socket,$buffer);
			$respuesta = "";
			while($respuesta.=socket_read($socket,$tamano)){
				if(strpos($respuesta,"\0")) break;
			}
		}else{
			return false;
		}
		socket_close($socket);
		$respuesta = str_replace("\0", "", $respuesta);
		if($respuesta == "NOTIFICATION_SENT"){
			return true;
		}else{
			return false;
		}
	}
	
	@session_start();
	$user_id = $_SESSION['user_id'];
	$notification_id = $_GET['id'];

	if(!empty($user_id)){
		echo json_encode( array("enviado"=> _send_notification( $notification_id ) ) );
	}else{
		echo json_encode( array("enviado"=> "" ) );
	}