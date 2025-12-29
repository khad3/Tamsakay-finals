
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FEU QR Code Scanner</title>
    <style>
        /* Basic styling for page layout */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 0;
            height: 100vh;
            background-color: #9be7f1;
            text-align: center;
        }
        
        /* Cloud animations and styling */
        .cloud {
            position: absolute;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            opacity: 0.8;
            animation: moveClouds 20s linear infinite;
        }

        .cloud1 { width: 200px; height: 100px; top: 10%; left: -20%; }
        .cloud2 { width: 250px; height: 120px; top: 30%; left: -30%; }
        .cloud3 { width: 300px; height: 150px; top: 60%; left: -40%; }

        @keyframes moveClouds {
            0% { transform: translateX(0); }
            100% { transform: translateX(100vw); }
        }

        .container { width: 100%; max-width: 500px; z-index: 1; }
        .container h1 {
            font-size: 2em;
            background-image: linear-gradient(270deg, #FF0000, #FF7F00, #FFFF00, #00FF00, #00FFFF, #0000FF, #4B0082, #9400D3);
            background-size: 400%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient-animation 5s linear infinite;
        }

        @keyframes gradient-animation { 0%, 100% { background-position: 0% 50%; } }

        .section {
            background-color: #fff;
            padding: 50px 30px;
            border-radius: 0.25em;
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.25);
        }

        #my-qr-reader { padding: 20px; border-radius: 8px; }
        video { width: 100%; border-radius: 0.25em; }

        .modal {
            display: none;
            position: fixed;
            z-index: 2;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover { color: red; text-decoration: none; cursor: pointer; }
    </style>
</head>

<body>
    <div class="cloud cloud1"></div>
    <div class="cloud cloud2"></div>
    <div class="cloud cloud3"></div>

    <div class="container">
        <h1>Tamsakay Scanner</h1>
        <div class="section">
           <div id="my-qr-reader"></div>
       </div>
   </div>

   <div id="myModal" class="modal">
       <div class="modal-content">
           <span class="close">&times;</span>
           <p id="qr-result-text"></p>
           <button id="open-url-button" style="display:none;">Open Link</button>
       </div>
   </div>

   <script src="https://unpkg.com/html5-qrcode"></script>

   <script>
       document.addEventListener("DOMContentLoaded", function () {
           function onScanSuccess(decodeText, decodeResult) {
               const qrResultText = document.getElementById('qr-result-text');
               const openUrlButton = document.getElementById('open-url-button');

               if (isValidUrl(decodeText)) {
                   qrResultText.innerText = "Your QR code contains a link.";
                   openUrlButton.style.display = "block";
                   openUrlButton.onclick = () => window.open(decodeText);
               } else {
                   qrResultText.innerText = "Your QR code is : " + decodeText + " incorrect. please try again!";
                   openUrlButton.style.display = "none";
               }

               document.getElementById("myModal").style.display = "block";
           }

           function isValidUrl(string) {
               try {
                   new URL(string);
                   return true;
               } catch (_) {
                   return false;
               }
           }

           let htmlScanner = new Html5QrcodeScanner("my-qr-reader", { fps: 10, qrbox: 250 });
           htmlScanner.render(onScanSuccess, (errorMessage) => console.warn(`QR Code scan error: ${errorMessage}`));

           document.querySelector(".close").onclick = () => document.getElementById("myModal").style.display = "none";
           window.onclick = (event) => {
               if (event.target == document.getElementById("myModal")) {
                   document.getElementById("myModal").style.display = "none";
               }
           };
       });
   </script>
</body>
</html>
