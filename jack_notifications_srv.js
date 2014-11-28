/*
	Jack Notifications V1.1 - Server
	Sistema de notificaciones en tiempo real PHP, nodejs
*/
var io = require('socket.io').listen(9090);
var mysql = require('mysql');

var mysql_client = mysql.createConnection({
  user: 'root',
  password: '',
  host: 'localhost',
  port: '3306',
  database: 'jack_notifications',
  //socketPath: '/var/run/mysqld/mysqld.sock' //if you have problems with mysql
});

mysql_client.connect(function(err, conn) {
    if(err) {
         console.log('MySQL connection error: ', err);
         process.exit(1);
    }
});

var secret_key = "A_BIG_BIG_SECRET_FOR_MY_APPLICATION";

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
			if(typeof json_data.action != "undefined"){
			
				if(json_data.action == "send_id"){
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
								var room_id = results[0].room_id;
								io.to("room_"+room_id).emit('actualizar_notificaciones', results[0].content);
								socket.write(JSON.stringify({response:"NOTIFICATION_SENT"}) + "\0");
							}else{
								socket.write(JSON.stringify({response:"ERROR"}) + "\0");
							}
						}
					);
				}else if(json_data.action == "send"){
					var room = io.nsps["/"].adapter.rooms["room_"+json_data.room_id];
					if(typeof room != "undefined" && Object.keys(room).length >= 1 ){
						io.to("room_"+json_data.room_id).emit('actualizar_notificaciones', json_data.content);
						socket.write(JSON.stringify({response:"NOTIFICATION_SENT"}) + "\0");
					}else{
						socket.write(JSON.stringify({response:"NOTIFICATION_FAIL"}) + "\0");
					}
				}
			}else{
				socket.write(JSON.stringify({response:"ACCESS_DENIED"}) + "\0");
			}
		}else{
			socket.write(JSON.stringify({response:"ACCESS_DENIED"}) + "\0");
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
					socket.room_id = 'room_'+results[0].room_id;
					socket.join('room_'+results[0].room_id);
					
					//DELETE TOKEN?
					
					io.to("room_"+results[0].room_id).emit("conectar", true);
				}
			}
		);
    });
});
 
