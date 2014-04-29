<?php
	/*
		Jack Notifications V1.0 - Conectar
		Sistema de notificaciones en tiempo real PHP, nodejs
	*/
	
	$enlace =  mysql_connect('localhost', 'root', '');
	mysql_select_db('mi_base_de_datos', $enlace);
	
	/*Genera un token SHA256*/
	function _generate_token(){
		return hash("sha256", mt_rand() ."_". mt_rand() );
	}

	/*Verifica si el usuario ya existe*/
	function _exist_user( $user_id ){
		$result = mysql_query("SELECT ntoken_id FROM core_notifications_token WHERE user_id='$user_id';");
		if($result && mysql_num_rows($result)){
			return true;
		}else{
			return false;
		}
	}
	
	/*Verifica si el token ya existe*/
	function _exist_token( $token ){
		$result = mysql_query("SELECT ntoken_id FROM core_notifications_token WHERE token='$token';");
		if($result && mysql_num_rows($result)){
			return true;
		}else{
			return false;
		}
	}
	
	/*Inserta el token de conexión*/
	function _insert_token( $user_id, $token){
		return mysql_query("INSERT INTO core_notifications_token (user_id,token,record) VALUES('$user_id','$token',NOW());");
	}
	
	/*Actualiza el token de conexión de un usuario*/
	function _update_token( $user_id, $token){
		return mysql_query("UPDATE core_notifications_token SET token='$token',record=NOW() WHERE user_id='$user_id';");
	}
	
	@session_start();
	$user_id = $_SESSION['user_id'];
	
	if(!empty($user_id)){
		while(_exist_token( $token = _generate_token() )); //Generar token único
		if( _exist_user( $user_id ) ){
			_update_token( $user_id, $token );
		}else{
			_insert_token( $user_id, $token );
		}
		mysql_close( $enlace );
		
		echo json_encode( array("token"=>$token) );
	}else
		echo json_encode( array("token"=>"") );