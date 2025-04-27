const WebSocket = require('ws'); 
const wss = new WebSocket.Server({ port: 8080 }); 

wss.on('connection', (ws) => {
    console.log('Un nuevo cliente se ha conectado');

    ws.on('message', (message) => {
        const messageData = JSON.parse(message);  

        console.log('Mensaje recibido: ', messageData);

        wss.clients.forEach((client) => {
            if (client !== ws && client.readyState === WebSocket.OPEN) {
                client.send(JSON.stringify(messageData)); 
            }
        });
    });

    ws.on('close', () => {
        console.log('A client disconnected');
    });
});

console.log('Server WebSocket listening on ws://localhost:8080');
