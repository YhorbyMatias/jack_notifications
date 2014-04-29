/*
	Jack Notifications V1.0 - Server
	Sistema de notificaciones en tiempo real PHP, nodejs
*/
var io = require('socket.io').listen(9090);
var mysql = require('mysql');

var mysql_client = mysql.createConnection({
  user: 'root',
  password: '',
  host: 'localhost',
  port: '3306',
});
mysql_client.query('USE mi_base_de_datos');

var secret_key = "SOQI6N3m8lApYX3MyUbpm7ZBSCzagCmtAKCPMhSKbom";

/*
	Envio de notificaciones desde socket
	secret - secret key
	notification_id - id de la notificación a enviar
*/
require('net').createServer(function (socket) {
    socket.on('data', function (data) {
		var dt = data.toString();
		var json_data = eval("("+dt+")");
		if(typeof json_data.secret !="undefined" && secret_key == json_data.secret){
			mysql_client.query(
				'SELECT * FROM core_notifications WHERE notification_id = ?',
				[json_data.notification_id]
				,
				function(err, results, fields) {
					if (err) {
						console.log("Error: " + err.message);
						throw err;
					}
					if(results.length){
						var user_id = results[0].user_id;
						io.sockets.in("room_"+user_id).emit('actualizar_notificaciones', "Notificacion de Servidor a "+user_id);
						socket.write("NOTIFICATION_SENT\0");
					}else{
						socket.write("ERROR\0");
					}
				}
			);
		}else{
			socket.write("ACCESS_DENIED\0");
		}
    });
}).listen(9091);

/*
	Conexión
	token - Token de conexión por usuario
*/
io.sockets.on("connection", function(socket)
{
	socket.on("notification_connect", function( token ){
		console.log("Connect");
		mysql_client.query(
			'SELECT * FROM core_notifications_token WHERE token=?',
			[token]
			,
			function(err, results, fields) {
				if (err) {
					console.log("Error: " + err.message);
					throw err;
				}
			 
				if(results.length){
					socket.room = 'room_'+results[0].user_id;
					socket.user_id = results[0].user_id;
					socket.join('room_'+results[0].user_id);
					
					//Aquí eliminar el token ???
					
					io.sockets.emit("conectar", true);
				}
			}
		);
    });
});
 
