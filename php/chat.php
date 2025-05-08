<?php
include '../db_connect.php';
session_start();

$user_email = $_SESSION['user_email'];
$user_factory_id = null;

if (isset($_SESSION['employee_user'])) {
    $query = "SELECT fe.factory_id_factory 
              FROM employee e
              JOIN factory_employee fe ON e.id_employee = fe.employee_id_employee
              WHERE e.email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $user_email);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $user_factory_id = $result['factory_id_factory'];
    }
} elseif (isset($_SESSION['boss_user'])) {
    $query = "SELECT fb.factory_id_factory 
              FROM boss b
              JOIN factory_boss fb ON b.id_boss_factory = fb.boss_id_boss_factory
              WHERE b.email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $user_email);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $user_factory_id = $result['factory_id_factory'];
    }
}
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

    <!-- JavaScript -->
    <script src="../js/chatConfigs.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/session.css">
    <link rel="stylesheet" href="../css/chat.css">

</head>

<body>

    <nav class="navbar" style="height: 46px;">
        <a class="navbar-brand" href="landing_page.php">TFG_UnaiBenitoSánchez</a>
        <button class="navbar-toggler" onclick="toggleNavbar()">☰</button>
        <ul class="navbar-nav" id="navbarNav">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Products from your factory</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="graphics.php">Production graphics</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./predict_view.php">Demand prediction</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="employees_table.php">Employees table</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="factory.php">Your factory</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="chat.php">Chat</a>
            </li>
            <li class="nav-item">
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
        let userFactoryId = "<?php echo $user_factory_id; ?>";
        let chatBox = document.getElementById('chat-box');
        let messageInput = document.getElementById('message-input');
        let sendButton = document.getElementById('send-button');
        let messagesRef = database.ref('factory_chat/messages');

        let displayedMessages = {};

        console.log("Chat iniciado para:", nameuser, "de la fábrica:", userFactoryId);

        // Show messages
        function displayMessage(user, text, timestamp, messageId) {
            console.log("Showing message:", {
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

            messageWrapper.append(senderName);
            messageWrapper.append(message);
            messageWrapper.append(timeElement);
            chatBox.append(messageWrapper);

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
                    email: username,
                    factory_id: userFactoryId
                }).then(() => {
                    messageInput.value = '';
                }).catch((error) => {
                    console.error("Error sending message:", error);
                    alert("Error sending message. More details in console.");
                });
            }
        }

        // Configure listeners of Firebase
        function setupFirebaseListeners() {
            messagesRef.off();

            // Load new messages filtered by factory
            messagesRef.orderByChild('factory_id').equalTo(userFactoryId).limitToLast(50).once('value')
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

            // Listen new messages filtered by factory
            messagesRef.orderByChild('factory_id').equalTo(userFactoryId).limitToLast(100).on('child_added', (snapshot) => {
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

    <?php include '../controller/session.php'; ?>

</body>

</html>