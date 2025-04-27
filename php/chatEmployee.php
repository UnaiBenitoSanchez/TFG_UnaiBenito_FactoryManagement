<?php
include '../db_connect.php';
session_start();  
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factory Chat</title>
    <!-- Navbar -->
    <link rel="stylesheet" href="../css/navbar.css">

    <!-- Style -->
    <style>
        /* Navbar */
        .nav-logout-inline {
            color: white;
            background-color: rgb(203, 35, 35);
            text-decoration: none;
            transition: color 0.3s, background-color 0.3s;
            padding: 8px 15px;
            border-radius: 5px;
        }

        .nav-logout-inline:hover {
            background-color: rgb(255, 90, 90);
            color: #fff;
        }

        body {
            background: linear-gradient(45deg, #F7F9F9, #BED8D4, #78D5D7, #63D2FF, #2081C3);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            font-family: 'Poppins', sans-serif;
            margin: 0;
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }

            25% {
                background-position: 100% 50%;
            }

            50% {
                background-position: 0% 100%;
            }

            75% {
                background-position: 100% 100%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .card {
            background-color: rgba(48, 63, 159, 0.9);
            border-radius: 15px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
            margin: 20px auto;
            width: 80%;
            height: 600px;
            padding: 20px;
            text-align: center;
            color: #fff;
            border: 2px solid #2081C3;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .card h1 {
            font-size: 22px;
            font-weight: bold;
            color: #BED8D4;
            margin-bottom: 15px;
        }

        #chat-box {
            flex: 1;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 10px;
            overflow-y: auto;
            margin-bottom: 10px;
            border: 1px solid #2081C3;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .message {
            max-width: 70%;
            padding: 10px;
            border-radius: 15px;
            word-break: break-word;
            display: inline-block;
            font-size: 14px;
            line-height: 1.4;
        }

        .message-wrapper {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .message-wrapper.my-message-wrapper {
            align-items: flex-end;
        }

        .message-wrapper .sender-name {
            font-weight: bold;
            color: rgb(195, 165, 32);
            font-size: 14px;
            margin-bottom: 5px;
            letter-spacing: 1px;
            cursor: pointer;
            transition: color 0.3s, transform 0.3s;
            text-align: right;
        }

        .my-message {
            align-self: flex-end;
            background-color: #63D2FF;
            color: white;
            border-bottom-right-radius: 0;
            text-align: right;
        }

        .other-message {
            align-self: flex-start;
            background-color: #BED8D4;
            color: #333;
            border-bottom-left-radius: 0;
        }

        #chat-box {
            flex: 1;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 10px;
            overflow-y: auto;
            margin-bottom: 10px;
            border: 1px solid #2081C3;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .input-group {
            display: flex;
        }

        #message-input {
            width: calc(100% - 90px);
            padding: 10px;
            border-radius: 8px;
            border: none;
            margin-right: 10px;
        }

        #send-button {
            padding: 10px 20px;
            background-color: #63D2FF;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        #send-button:hover {
            background-color: #2081C3;
        }
    </style>

    <script>
        function toggleNavbar() {
            var navbarNav = document.getElementById('navbarNav');
            navbarNav.classList.toggle('show');
        }

        $(document).ready(function() {
            $('#errorModal').hide();

            function openModal() {
                $('#errorModal').fadeIn();
            }

            function closeModal() {
                $('#errorModal').fadeOut();
            }

            $('a').on('click', function(event) {
                let link = $(this).attr('href');

                if (link.includes('employee_dashboard.php') || link.includes('../logout.php') || link.includes('chatEmployee.php')) {
                    return;
                }

                event.preventDefault();
                openModal();
            });

            window.closeModal = closeModal;
        });
    </script>

</head>

<body>

    <nav class="navbar">
        <a class="navbar-brand" href="landing_page.php">TFG_UnaiBenitoSánchez</a>
        <button class="navbar-toggler" onclick="toggleNavbar()">☰</button>
        <ul class="navbar-nav" id="navbarNav">
            <li class="nav-item">
                <a class="nav-link" href="employee_dashboard.php">Products from your factory</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="graphics.php">Production graphics</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./predict_view.php">Demand prediction</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="factory.php">Your factory</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="chatEmployee.php">Chat</a>
            </li>
            <li class="nav-item" style="margin-top: 8px;">
                <a class="nav-logout-inline" href="../logout.php">Logout</a>
            </li>
        </ul>
    </nav>

    <div class="card">
        <h1>Factory Live Chat</h1>

        <div id="chat-box"></div>

        <div class="input-group">
            <input type="text" id="message-input" placeholder="Write a message..." />
            <button id="send-button">Send</button>
        </div>
    </div>

    <script>
        let username = "<?php echo $_SESSION['user_email']; ?>";
        let nameuser = "<?php echo $_SESSION['employee_user']; ?>";

        let socket = new WebSocket('ws://localhost:8080');
        socket.binaryType = "text";

        let chatBox = document.getElementById('chat-box');
        let messageInput = document.getElementById('message-input');
        let sendButton = document.getElementById('send-button');

        let sentMessages = new Set();

        socket.addEventListener('message', async function(event) {
            let data;
            if (event.data instanceof Blob) {
                data = await event.data.text();
            } else {
                data = event.data;
            }

            let messageData = JSON.parse(data);
            if (sentMessages.has(messageData.text)) {
                sentMessages.delete(messageData.text);
                return;
            }

            let messageWrapper = document.createElement('div');
            messageWrapper.classList.add('message-wrapper');

            let senderName = document.createElement('div');
            senderName.classList.add('sender-name');
            senderName.textContent = messageData.user;

            let message = document.createElement('div');
            message.classList.add('message', 'other-message');
            message.textContent = messageData.text;

            messageWrapper.appendChild(senderName);
            messageWrapper.appendChild(message);
            chatBox.appendChild(messageWrapper);
            chatBox.scrollTop = chatBox.scrollHeight;
        });

        function sendMessage() {
            if (messageInput.value.trim() !== '') {
                let msg = messageInput.value.trim();

                let messageData = {
                    user: nameuser,
                    text: msg
                };

                socket.send(JSON.stringify(messageData));
                sentMessages.add(msg);

                let messageWrapper = document.createElement('div');
                messageWrapper.classList.add('message-wrapper', 'my-message-wrapper');

                let senderName = document.createElement('div');
                senderName.classList.add('sender-name');
                senderName.textContent = nameuser;

                let myMessage = document.createElement('div');
                myMessage.classList.add('message', 'my-message');
                myMessage.textContent = msg;

                messageWrapper.appendChild(senderName);
                messageWrapper.appendChild(myMessage);
                chatBox.appendChild(messageWrapper);

                chatBox.scrollTop = chatBox.scrollHeight;
                messageInput.value = '';
            }
        }

        sendButton.addEventListener('click', sendMessage);

        messageInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        });
    </script>

</body>

</html>