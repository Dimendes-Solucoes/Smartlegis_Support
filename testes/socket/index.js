const WebSocket = require('ws');

var conn = new WebSocket('wss://devapp.appvote.smartlegis.net.br/ws');

conn.onopen = function(e) {
    console.log("Connection established!");
    conn.send('Hello World!');
};

conn.onmessage = function(e) {
    console.log(e.data);
};
