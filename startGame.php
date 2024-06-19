<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<script>
		$(document).ready(function () {
			var ws = new WebSocket("wss://site230.webte.fei.stuba.sk:9000");

			ws.onopen = function () { log("Connection established"); };
			ws.onerror = function (error) { log("Unknown WebSocket Error " + JSON.stringify(error)); };
			ws.onmessage = function (e) {  var data = JSON.parse(e.data);
                                     console.log((data));
                                     log("< " + data.msg); 
                                     document.getElementById("number").innerHTML = data.num + "<br>";   
                                  };
			ws.onclose = function () { log("Connection closed - Either the host or the client has lost connection"); }

			function log(m) {
				$("#log").append(m + "<br />");
			}

			function send() {
				$Msg = $("#msg");
				if ($Msg.val() == "") return alert("Textarea is empty");

				try {
    					ws.send($Msg.val()); log('> Sent to server:' + $Msg.val());
    				} catch (exception) {
    					log(exception);
    				}
    				$Msg.val("");
			}

			$("#send").click(send);
			$("#msg").on("keydown", function (event) {
				if (event.keyCode == 13) send();
			});
			$("#quit").click(function () {
				log("Connection closed");
				ws.close(); ws = null;
			});      
		});

		
    </script>

    <style>

#number {
    	width: 60px;  
    	font-size: 115%; 
      text-align: center; 
    	background: lightblue;
    	padding: 5px;   
    }
        </style>

<body>
    <button>start game</button>
    <button>log off</button>


    <div id="number">&nbsp;</div>
</body>
</html>