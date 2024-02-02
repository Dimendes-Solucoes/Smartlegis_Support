const WebSocket = require('ws');

var conn = new WebSocket('ws://localhost:6002');

conn.onopen = function(e) {
    console.log("Connection established!");
    conn.send('Hello World!');
};

conn.onmessage = function(e) {
    console.log(e.data);
};



