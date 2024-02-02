var conn = new ab.Session('wss://devapp.appvote.smartlegis.net.br/ws',
    function () {
        conn.subscribe('tenantdev', function (topic, data) {
            console.log(data);
            console.log(topic);
            // This is where you would add the new article to the DOM (beyond the scope of this tutorial)
            console.log('New article published to category');
        });
    },
    function () {
        console.warn('WebSocket connection closed');
    },
    { 'skipSubprotocolCheck': true }
);

