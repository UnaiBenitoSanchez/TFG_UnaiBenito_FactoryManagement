<?php
include '../db_connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factory Chat - Boss</title>
    <!-- Navbar -->
    <link rel="stylesheet" href="../css/navbar.css">

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>

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

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>

    <script>
        // Configuration of Firebase
        const firebaseConfig = {
            apiKey: "AIzaSyAJYqhxwF5kIqLRtdAAYFCwc7EUBGl23fw",
            authDomain: "gestionfabricas.firebaseapp.com",
            databaseURL: "https://gestionfabricas-default-rtdb.europe-west1.firebasedatabase.app",
            projectId: "gestionfabricas",
            storageBucket: "gestionfabricas.appspot.com",
            messagingSenderId: "498818502316",
            appId: "1:498818502316:web:f0be8009c7ba25198fd909",
            measurementId: "G-959F79765K"
        };

        // Initialization of Firebase
        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }
        const database = firebase.database();

        // Variables
        let username = "<?php echo $_SESSION['user_email']; ?>";
        let nameuser = "<?php echo $_SESSION['employee_user'] ?? $_SESSION['boss_user'] ?? 'Usuario'; ?>";
        let chatBox = document.getElementById('chat-box');
        let messageInput = document.getElementById('message-input');
        let sendButton = document.getElementById('send-button');
        let messagesRef = database.ref('factory_chat/messages');

        let displayedMessages = {};

        console.log("Chat iniciado para:", nameuser);

        // Show messages
        function displayMessage(user, text, timestamp, messageId) {
            console.log("Mostrando mensaje:", {
                user,
                text,
                timestamp,
                messageId
            }); 

            let isCurrentUser = user === nameuser;

            let messageWrapper = document.createElement('div');
            messageWrapper.classList.add('message-wrapper');
            if (isCurrentUser) {
                messageWrapper.classList.add('my-message-wrapper');
            }

            let senderName = document.createElement('div');
            senderName.classList.add('sender-name');
            senderName.textContent = user;

            let message = document.createElement('div');
            message.classList.add('message');
            message.classList.add(isCurrentUser ? 'my-message' : 'other-message');
            message.textContent = text;

            let timeElement = document.createElement('div');
            timeElement.style.fontSize = '10px';
            timeElement.style.color = '#ccc';
            timeElement.style.marginTop = '2px';
            timeElement.style.textAlign = isCurrentUser ? 'right' : 'left';

            if (timestamp) {
                let date = new Date(timestamp);
                timeElement.textContent = date.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            messageWrapper.appendChild(senderName);
            messageWrapper.appendChild(message);
            messageWrapper.appendChild(timeElement);
            chatBox.appendChild(messageWrapper);

            chatBox.scrollTop = chatBox.scrollHeight;
        }

        // Send messages
        function sendMessage() {
            let messageText = messageInput.value.trim();
            if (messageText !== '') {
                let timestamp = Date.now();

                console.log("Sending message:", messageText);

                messagesRef.push({
                    user: nameuser,
                    text: messageText,
                    timestamp: timestamp,
                    email: username
                }).then(() => {
                    messageInput.value = '';
                }).catch((error) => {
                    console.error("Error sendiong message:", error);
                    alert("Error sendiong message. More details in console.");
                });
            }
        }

        // Configure listeners of Firebase
        function setupFirebaseListeners() {
            messagesRef.off();

            // Load new messages
            messagesRef.limitToLast(50).once('value')
                .then((snapshot) => {
                    chatBox.innerHTML = '';
                    displayedMessages = {};

                    snapshot.forEach((childSnapshot) => {
                        let messageId = childSnapshot.key;
                        let messageData = childSnapshot.val();

                        if (!displayedMessages[messageId]) {
                            displayMessage(
                                messageData.user,
                                messageData.text,
                                messageData.timestamp,
                                messageId
                            );
                            displayedMessages[messageId] = true;
                        }
                    });
                })
                .catch((error) => {
                    console.error("Error loading messages:", error);
                });

            // Listen new messages
            messagesRef.limitToLast(100).on('child_added', (snapshot) => {
                let messageId = snapshot.key;
                let messageData = snapshot.val();

                if (!displayedMessages[messageId]) {
                    displayMessage(
                        messageData.user,
                        messageData.text,
                        messageData.timestamp,
                        messageId
                    );
                    displayedMessages[messageId] = true;
                }
            }, (error) => {
                console.error("Error listening messages:", error);
            });
        }

        // Event listeners
        sendButton.addEventListener('click', sendMessage);
        messageInput.addEventListener('keypress', (event) => {
            if (event.key === 'Enter') {
                sendMessage();
            }
        });

        // Initialize chat
        setupFirebaseListeners();

        // Verify connection
        let connectedRef = database.ref(".info/connected");
        connectedRef.on("value", (snapshot) => {
            console.log(snapshot.val() ? "Connected to Firebase" : "Disconnected from Firebase");
        });

        window.addEventListener('beforeunload', () => {
            messagesRef.off();
            connectedRef.off();
        });
    </script>

</body>

</html>