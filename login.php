<?php
	/* 
		Simula una sesión de usuario según el GET['u']
		Ej: http://localhost/jack_notifications/login.php?u=1
	*/
	@session_start();
	$_SESSION['user_id'] = $_GET['u'];