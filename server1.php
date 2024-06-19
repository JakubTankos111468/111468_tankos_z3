<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//php server2.php start
    use Workerman\Worker;
    use Workerman\Lib\Timer;
    use Workerman\Connection\TcpConnection;

    require_once __DIR__ . '/vendor/autoload.php';
 


    $num_players = 0;
    $ballDX = 0;
    $ballDY = 0;
    $player_num = 0;
    $player_nums = array();
    $top1 = 90;
    $top2 = 90;
    $left3 = 140;
    $left4 = 140;
    $count = 0;
    $numberOfBounces = 0;
    $player1Lives = 3;
    $player2Lives = 3;
    $player3Lives = 3;
    $player4Lives = 3;
    $ballSpeed = 0.1;

    $clients = [];

    function sendPlayerNumber($connection){
        $time = date('h:i:s'); 
        $obj = new stdClass();
        $obj->playerNum = $GLOBALS['player_nums'][$connection->id];
        $obj->msg = "The server time is: {$time}";
        $obj->num = $GLOBALS['count'];
        $obj->ballDX = $GLOBALS['ballDX'];      
        $obj->ballDY = $GLOBALS['ballDY'];
        $obj->topPaddle1 = $GLOBALS['top1'];
        $obj->topPaddle2 = $GLOBALS['top2'];
        $obj->leftPaddle3 = $GLOBALS['left3'];
        $obj->leftPaddle4 = $GLOBALS['left4'];
        $obj->numberOfBounces= $GLOBALS['numberOfBounces'];
        $obj->player1Lives = $GLOBALS['player1Lives'];
        $obj->player2Lives = $GLOBALS['player2Lives'];
        $obj->player3Lives = $GLOBALS['player3Lives'];
        $obj->player4Lives = $GLOBALS['player4Lives'];

        $connection->send(json_encode($obj));
    }
    

    function generateUniqueId() {
        return uniqid('conn_', true);
    }



    // SSL context.
    $context = [
        'ssl' => [
            'local_cert'  => '/home/xtankos/webte_fei_stuba_sk.pem',
            'local_pk'    => '/home/xtankos/webte.fei.stuba.sk.key',
            'verify_peer' => false,
        ]
    ];
    
    // Create A Worker and Listens 9000 port, use Websocket protocol
    $ws_worker = new Worker("websocket://0.0.0.0:9000", $context);
    
    // Enable SSL. WebSocket+SSL means that Secure WebSocket (wss://). 
    // The similar approaches for Https etc.
    $ws_worker->transport = 'ssl';
 
    // 4 processes
    $ws_worker->count = 1;
    
    // Add a Timer to Every worker process when the worker process start
    $ws_worker->onWorkerStart = function($ws_worker)
    {   $GLOBALS['userdata']=0;
        $GLOBALS['dirX'] = 0;
        $GLOBALS['dirY'] = 0;      
        // Timer every 5 seconds
        Timer::add(0.001, function()use($ws_worker)
        {          
        
            if($GLOBALS['dirX'] == 0 && $GLOBALS['ballDX'] <= 345){
                $GLOBALS['ballDX'] = $GLOBALS['ballDX'] + $GLOBALS['ballSpeed'];

                if($GLOBALS['dirX'] == 0 && $GLOBALS['ballDX'] > 299 && $GLOBALS['ballDX'] < 305
                && $GLOBALS['ballDY'] > $GLOBALS['top2']-20  && $GLOBALS['ballDY'] < $GLOBALS['top2']+110  && count($GLOBALS['clients']) > 1 && $GLOBALS['player2Lives'] >=1){
                    $GLOBALS['dirX'] = 1;
                    $GLOBALS['numberOfBounces']++;
                }

                if($GLOBALS['ballDX'] > 345){
                    $GLOBALS['dirX'] = 1;
                    $GLOBALS['numberOfBounces']++;
                }
                
            }
            else if(($GLOBALS['dirX'] == 1 && $GLOBALS['ballDX'] >= 0)){
                $GLOBALS['ballDX'] = $GLOBALS['ballDX'] - $GLOBALS['ballSpeed'];
                if($GLOBALS['dirX'] == 1 && $GLOBALS['ballDX'] > 46 && $GLOBALS['ballDX'] < 52 
                && $GLOBALS['ballDY'] > $GLOBALS['top1']-20  && $GLOBALS['ballDY'] < $GLOBALS['top1']+110 && $GLOBALS['player1Lives'] >=1){
                    $GLOBALS['dirX'] = 0;
                    $GLOBALS['numberOfBounces']++;
                }
                if($GLOBALS['ballDX'] < 0){
                    $GLOBALS['dirX'] = 0;
                    $GLOBALS['numberOfBounces']++;
                }
            }
           
            if($GLOBALS['dirY'] == 0 && $GLOBALS['ballDY'] <= 285){
                $GLOBALS['ballDY'] = $GLOBALS['ballDY'] + $GLOBALS['ballSpeed'];

                if($GLOBALS['dirY'] == 0 && $GLOBALS['ballDY'] > 254 && $GLOBALS['ballDY'] < 260
                && $GLOBALS['ballDX'] > $GLOBALS['left3']-30 && $GLOBALS['ballDX'] < $GLOBALS['left3']+100  && count($GLOBALS['clients']) > 2 && $GLOBALS['player3Lives'] >=1){
                    $GLOBALS['dirY'] = 1;
                    $GLOBALS['numberOfBounces']++;
                }

                if($GLOBALS['ballDY'] > 285){
                    $GLOBALS['dirY'] = 1;
                    $GLOBALS['numberOfBounces']++;
                }
            }
            else if($GLOBALS['dirY'] == 1 && $GLOBALS['ballDY'] > 0){
                $GLOBALS['ballDY'] = $GLOBALS['ballDY'] - $GLOBALS['ballSpeed'];

                if($GLOBALS['dirY'] == 1 && $GLOBALS['ballDY'] > 26 && $GLOBALS['ballDY'] < 32
                && $GLOBALS['ballDX'] > $GLOBALS['left3']-30 && $GLOBALS['ballDX'] < $GLOBALS['left3']+100 && count($GLOBALS['clients']) > 3 && $GLOBALS['player4Lives'] >=1){
                    $GLOBALS['dirY'] = 0;
                    $GLOBALS['numberOfBounces']++;
                }

                if($GLOBALS['ballDY'] <= 0){
                    $GLOBALS['dirY'] = 0;
                    $GLOBALS['numberOfBounces']++;
                }
            }
            

            
           if ($GLOBALS['ballDX'] <=0 && $GLOBALS['ballDY'] > 40  && $GLOBALS['ballDY'] < 220 && $GLOBALS['player1Lives'] >= 1){
                $GLOBALS['ballDX'] = 150;
                $GLOBALS['ballDY'] = 155; 
                $GLOBALS['numberOfBounces']--;
                $GLOBALS['player1Lives']--;

           }
           
           
           if ($GLOBALS['ballDX'] >= 340 && $GLOBALS['ballDY'] > 40  && $GLOBALS['ballDY'] < 220 && count($GLOBALS['clients']) > 1  && $GLOBALS['player2Lives'] >= 1){
                $GLOBALS['ballDX'] = 150;
                $GLOBALS['ballDY'] = 155;
                $GLOBALS['numberOfBounces']--;
                $GLOBALS['player2Lives']--;

           }
               
            if ($GLOBALS['ballDY'] >=285 && $GLOBALS['ballDX'] > 80  && $GLOBALS['ballDX'] < 260 && count($GLOBALS['clients']) > 2  && $GLOBALS['player3Lives'] >= 1){
                $GLOBALS['ballDX'] = 150;
                $GLOBALS['ballDY'] = 155;
                $GLOBALS['numberOfBounces']--; 
                $GLOBALS['player3Lives']--;
               }

            
               if ($GLOBALS['ballDY'] <=0 && $GLOBALS['ballDX'] > 80  && $GLOBALS['ballDX'] < 260 && count($GLOBALS['clients']) > 3  && $GLOBALS['player4Lives'] >= 1){
                $GLOBALS['ballDX'] = 150;
                $GLOBALS['ballDY'] = 155;
                $GLOBALS['numberOfBounces']--;
                $GLOBALS['player4Lives']--;
               }

               $GLOBALS['ballSpeed'] = $GLOBALS['ballSpeed'] + 0.00001;
          // Iterate over connections and send the time          
          foreach($ws_worker->connections as $connection)
            {
                sendPlayerNumber($connection);
            }            
        });

        
        
        $lastMessageTime = time(); // set the initial last message time
        $interval = 1; // set the interval to 1 second
        
        // Add an event handler for when a client sends a message
        $ws_worker->onMessage = function (TcpConnection $connection, $data) use (&$clients, &$lastMessageTime, $interval) {
            $currentTime = time(); // get the current time
            $timeSinceLastMessage = $currentTime - $lastMessageTime; // calculate the time since the last message was received
        
            if ($timeSinceLastMessage >= 0.01) { // check if the interval has passed
                $lastMessageTime = $currentTime; // update the last message time
        
                // process the message as before
                $message = json_decode($data, true);
        
                if (isset($message['type']) && $message['type'] === 'paddle_position' && isset($message['paddle1']['top']) && isset($message['paddle2']['top']) && isset($message['paddle3']['left']) && isset($message['paddle4']['left'])) {
                    if ($connection->id === 1) {
                      $GLOBALS['top1'] = $message['paddle1']['top'];
                      //echo "PADDLE 1: " . $message['paddle1']['top'] . "\n";
                    }if ($connection->id === 2) {
                      $GLOBALS['top2'] = $message['paddle2']['top'];
                      //echo "PADDLE 2: " . $message['paddle2']['top'] . "\n";
                    }
                    if ($connection->id === 3) {
                        $GLOBALS['left3'] = $message['paddle3']['left'];
                        //echo "PADDLE 3: " . $message['paddle3']['left'] . "\n";
                      }
                      if ($connection->id === 4) {
                        $GLOBALS['left4'] = $message['paddle4']['left'];
                        //echo "PADDLE 4: " . $message['paddle4']['left'] . "\n";
                      }
                      
                    
                    foreach ($GLOBALS['clients'] as $client) {
                      if ($client->id === 1 && $connection->id === 1) {
                        $client->send(json_encode([
                          'type' => 'paddle_position',
                          'paddle1' => [
                            'top' => $GLOBALS['top1']
                          ]
                        ]));
                      }if ($client->id === 2 && $connection->id === 2) {
                        $client->send(json_encode([
                          'type' => 'paddle_position',
                          'paddle2' => [
                            'top' => $GLOBALS['top2']
                          ]
                        ]));
                      }
                      if ($client->id === 3 && $connection->id === 3) {
                        $client->send(json_encode([
                          'type' => 'paddle_position',
                          'paddle3' => [
                            'left' => $GLOBALS['left3']
                          ]
                        ]));
                      }
                      if ($client->id === 4 && $connection->id === 4) {
                        $client->send(json_encode([
                          'type' => 'paddle_position',
                          'paddle4' => [
                            'left' => $GLOBALS['left4']
                          ]
                        ]));
                      }
                    }
                  } else {
                    echo "Received invalid message from client " . $connection->id . ": " . $data . "\n";
                  }
                  
                  
            }
        };
        
        

    };

    $ws_worker->onConnect = function($connection){

    $connection->onWebSocketConnect = function($connection)
    {

    $GLOBALS['clients'][$connection->id] = $connection;
    echo "New client connected: " . $connection->id . "\n";
    $GLOBALS['count'] = $GLOBALS['count'] + 1;

    $GLOBALS['player_num']++;
  
    $GLOBALS['player_nums'][$connection->id] = $GLOBALS['player_num'];


    };
};

    
    $ws_worker->onClose = function($connection)
    {

        unset($GLOBALS['clients'][$connection->id]);
        echo "Client disconnected: " . $connection->id . "\n";

        $GLOBALS['count'] = $GLOBALS['count'] - 1;

        $GLOBALS['num_players'] = $GLOBALS['num_players'] - 1;
        unset( $GLOBALS['player_nums'][$connection->id]);
     
    };


    Worker::runAll();

$ws_worker->run();

    
    
?>
    