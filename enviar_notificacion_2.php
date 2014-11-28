<?php
	/*
		Jack Notifications V1.1 - Send notification (require save the notification)
		Sistema de notificaciones en tiempo real PHP, nodejs
	*/
	require_once("jacknotifications.php");
	
	@session_start();
	$user_id = $_SESSION['user_id'];

	if(!empty($user_id)){
		$room_id = $_GET['u']; //Filtrar
		$jn = new JackNotifications();
		$enviado = $jn->enviar_notificacion("HERE YOUR FILTERED MESSAGE (THIS IS A EXAMPLE) ".date("d-m-Y H:i:s"), $room_id );
		$jn->_dbclose();
		
		echo json_encode( array("enviado"=> $enviado ) );
	}else{
		echo json_encode( array("enviado"=> "" ) );
	}