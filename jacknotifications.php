<?php
/*
	Jack Notifications V1.1 - 27/11/14
*/
class JackNotifications{
    var $settings = array();
	var $db_link = null;
	
    public function __construct(){
        $this->settings['secret']             = "A_BIG_BIG_SECRET_FOR_MY_APPLICATION";
        $this->settings['node_srv']           = "localhost";
        $this->settings['port_socketio']      = 9090;
        $this->settings['port_notifications'] = 9091;
		
		$this->db_link =  mysql_connect('localhost', 'root', '');
		mysql_select_db('jack_notifications', $this->db_link);
    }
	
    /* Connect */
    public function conectar( $room_id ){
        while ($this->_exist_token($token = $this->_generate_token()));
        $this->_insert_token($room_id, $token);
        return $token;
    }
	
    /* Senders (depends what do you need)*/
    function guardar_notificacion( $content, $room_id ){
        $rs = mysql_query("INSERT INTO core_notifications (room_id,content,date) VALUES('$room_id','$content',NOW());");
		$notification_id = mysql_insert_id();
		return $notification_id;
    } 
	
	function enviar_notificacion_id($notification_id){
        $json_data = array(
			"action" => "send_id",
            "secret" => $this->settings['secret'],
            "notification_id" => $notification_id
        );
        return $this->_enviar_notificacion($json_data);
    }
	
	function enviar_notificacion($content, $room_id){
        $json_data = array(
			"action" => "send",
            "secret" => $this->settings['secret'],
            "content" => $content,
            "room_id" => $room_id
        );
        return $this->_enviar_notificacion($json_data);
    }
	
	/* Master sender */
    function _enviar_notificacion($json_data){
        $tamano   = 2048;
        $socket   = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $conexion = socket_connect($socket, $this->settings['node_srv'], $this->settings['port_notifications']);
        if ($conexion) {
            $buffer = json_encode($json_data);
            socket_write($socket, $buffer);
            $respuesta = "";
            while ($respuesta .= socket_read($socket, $tamano)) {
                if (strpos($respuesta, "\0"))
                    break;
            }
        } else {
            socket_close($socket);
            return false;
        }
        socket_close($socket);
        $respuesta = json_decode(str_replace("\0", "", $respuesta));
		
        if ($respuesta->response == "NOTIFICATION_SENT") {
            return true;
        }else{
			return false;
		}
    }
	
    /* Local functions */
    function _generate_token(){
        return hash("sha256", mt_rand() . "_" . mt_rand());
    }
	
	function _exist_user( $room_id ){
		$result = mysql_query("SELECT ntoken_id FROM core_notifications_token WHERE room_id='$room_id';");
		if($result && mysql_num_rows($result)){
			return true;
		}else{
			return false;
		}
	}
	
	function _exist_token( $token ){
		$result = mysql_query("SELECT ntoken_id FROM core_notifications_token WHERE token='$token';");
		if($result && mysql_num_rows($result)){
			return true;
		}else{
			return false;
		}
	}
	
    function _insert_token( $room_id, $token){
		$rs = mysql_query("INSERT INTO core_notifications_token (room_id,token,record) VALUES('$room_id','$token',NOW());");
		return $rs;
	}
	
	function _update_token( $room_id, $token){
		$rs = mysql_query("UPDATE core_notifications_token SET token='$token',record=NOW() WHERE room_id='$room_id';");
		return $rs;
	}
	
	function _dbclose(){
		mysql_close( $this->db_link );
	}
}