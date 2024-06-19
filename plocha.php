<!DOCTYPE html>
<html>
<head>
  <meta charset='utf-8'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <title>PONG</title>
</head>
<style>
  #game-canvas {
    display: block;
    margin: auto;
  }
</style>
<body>
  <canvas id="game-canvas" width="500" height="500"></canvas>
  <script>
    const canvas = document.getElementById('game-canvas');
    const ctx = canvas.getContext('2d');

    function drawBorders() {
      ctx.lineWidth = 20;
      ctx.strokeStyle = 'grey'; 
      ctx.fillStyle = '#ffffff'; 
      ctx.beginPath(); 
      ctx.moveTo(10, 10); 
      ctx.lineTo(490, 10); 
      ctx.moveTo(490, 0); 
      ctx.lineTo(490, 100); 
      ctx.moveTo(10, 0); 
      ctx.lineTo(10, 100); 
      ctx.stroke(); 
      ctx.fill(); 

      ctx.beginPath();
      ctx.moveTo(10, 490);
      ctx.lineTo(490, 490); 
      ctx.moveTo(490, 500); 
      ctx.lineTo(490, 400);
      ctx.moveTo(10, 500); 
      ctx.lineTo(10, 400); 
      ctx.stroke(); 
      ctx.fill(); 

      ctx.strokeStyle = 'red';
      ctx.lineWidth = 10;
      ctx.beginPath();
      ctx.moveTo(470, redLineTopY);
      ctx.lineTo(470, redLineBottomY);
      ctx.stroke();

    }

    let redLineTopY = 200;
    let redLineBottomY = 300;

    document.addEventListener("keydown", event => {
      if (event.code === "ArrowUp") {
        redLineTopY -= 10;
        redLineBottomY -= 10;
      }
      if (event.code === "ArrowDown") {
        redLineTopY += 10;
        redLineBottomY += 10;
      }
    });

    function drawRedLines() {
      ctx.strokeStyle = 'red';
      ctx.lineWidth = 10;
      ctx.beginPath();
      ctx.moveTo(40, redLineTopY);
      ctx.lineTo(40, redLineBottomY);
      ctx.stroke();

     
    }

    
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const radius = 5;

    function drawBall() {
      ctx.beginPath();
      ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
      ctx.stroke();
    }

    function draw() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      drawBorders();
      drawRedLines();
      drawBall();
      requestAnimationFrame(draw);
    }

    draw();
  </script>
</body>
</html>
