<?php
	/*
		Jack Notifications V1.1 - Conectar
		Sistema de notificaciones en tiempo real PHP, nodejs
	*/
	require_once("jacknotifications.php");
	@session_start();
	$user_id = $_SESSION['user_id'];
	
	if(!empty($user_id)){
		$jn = new JackNotifications();
		$token = $jn->conectar( $user_id );
		$jn->_dbclose();
		
		echo json_encode( array("token"=>$token) );
	}else
		echo json_encode( array("token"=>"") );