<?php
session_start();
include '../DB.php';

// Cek role Sales (case insensitive)
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'sales') {
    die("Unauthorized access");
}

$salesId = $_SESSION['id'];
$selectedUserId = isset($_GET['to']) ? (int)$_GET['to'] : null;

// Ambil daftar user dengan role customer dan manager (case insensitive)
$userQuery = "SELECT id, username, role FROM users WHERE LOWER(role) IN ('customer', 'manager')";
$usersResult = mysqli_query($conn, $userQuery);
if (!$usersResult) {
    die("Query error: " . mysqli_error($conn));
}

// Kirim pesan dengan prepared statement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'], $_POST['receiver_id'])) {
    $message = $_POST['message'];
    $receiverId = (int)$_POST['receiver_id'];
    $timestamp = date('Y-m-d H:i:s');

    $stmt = mysqli_prepare($conn, "INSERT INTO messages (senderId, receiverId, message, createdAt, updatedAt) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iisss", $salesId, $receiverId, $message, $timestamp, $timestamp);

    if (mysqli_stmt_execute($stmt)) {
        // Redirect untuk mencegah resubmission
        header("Location: ?to=$receiverId");
        exit;
    } else {
        echo "<p style='color:red;'>Gagal mengirim pesan: " . mysqli_error($conn) . "</p>";
    }
}

// Ambil chat antara sales dan selected user dengan prepared statement
$chatMessages = [];
if ($selectedUserId) {
    $chatQuery = "
        SELECT m.*, u.username AS sender_name
        FROM messages m
        JOIN users u ON m.senderId = u.id
        WHERE (m.senderId = ? AND m.receiverId = ?)
           OR (m.senderId = ? AND m.receiverId = ?)
        ORDER BY m.createdAt ASC
    ";
    $stmt = mysqli_prepare($conn, $chatQuery);
    mysqli_stmt_bind_param($stmt, "iiii", $salesId, $selectedUserId, $selectedUserId, $salesId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $chatMessages = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sales Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            margin: 0;
        }
        .sidebar {
            width: 250px;
            background: #343a40;
            color: white;
            padding: 20px;
            box-sizing: border-box;
        }
        .sidebar h2 {
            margin-top: 0;
        }
        .sidebar a {
            color: white;
            display: block;
            margin: 10px 0;
            text-decoration: none;
            padding: 10px;
            background: #495057;
            border-radius: 5px;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #007bff;
        }
        .chat-container {
            flex: 1;
            padding: 20px;
            background: #f8f9fa;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }
        .chat-box {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            background: white;
            padding: 15px;
            border-radius: 5px;
        }
        .message {
            margin-bottom: 10px;
        }
        .message strong {
            color: #007bff;
        }
        form {
            display: flex;
            gap: 10px;
        }
        input[type="text"] {
            flex: 1;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            padding: 10px 20px;
            border: none;
            background: #28a745;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div style="position: fixed; top: 10px; left: 10px;">
        <a href="../index.php" style="
            display: inline-block;
            padding: 8px 12px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 700px;
        ">Kembali</a>
    </div>

    <div class="sidebar">
        <h2>Customers & Managers</h2>
        <?php while ($row = mysqli_fetch_assoc($usersResult)) { ?>
            <a href="?to=<?= $row['id'] ?>" class="<?= $selectedUserId == $row['id'] ? 'active' : '' ?>">
                <?= htmlspecialchars($row['username']) ?> (<?= htmlspecialchars($row['role']) ?>)
            </a>
        <?php } ?>
    </div>
    <div class="chat-container">
        <?php if ($selectedUserId): ?>
            <div class="chat-box">
                <?php foreach ($chatMessages as $msg) { ?>
                    <div class="message">
                        <strong><?= htmlspecialchars($msg['sender_name']) ?>:</strong>
                        <?= htmlspecialchars($msg['message']) ?>
                        <div style="font-size: 12px; color: #888;"><?= $msg['createdAt'] ?></div>
                    </div>
                <?php } ?>
            </div>
            <form method="post">
                <input type="hidden" name="receiver_id" value="<?= $selectedUserId ?>">
                <input type="text" name="message" placeholder="Tulis pesan..." required>
                <button type="submit">Kirim</button>
            </form>
        <?php else: ?>
            <h3>Pilih customer atau manager dari kiri untuk mulai chat.</h3>
        <?php endif; ?>
    </div>
    <script>
const selectedSalesId = <?= json_encode($selectedSalesId) ?>;
const chatBox = document.querySelector('.chat-box');
let lastMessageId = 0;

function appendMessages(messages) {
    messages.forEach(msg => {
        if (msg.id > lastMessageId) lastMessageId = msg.id;

        const div = document.createElement('div');
        div.classList.add('message');

        const strong = document.createElement('strong');
        strong.textContent = msg.sender_name + ': ';
        strong.style.color = '#007bff';

        const text = document.createTextNode(msg.message);
        const timestamp = document.createElement('div');
        timestamp.style.fontSize = '12px';
        timestamp.style.color = '#888';
        timestamp.textContent = msg.createdAt;

        div.appendChild(strong);
        div.appendChild(text);
        div.appendChild(timestamp);

        chatBox.appendChild(div);
    });
    chatBox.scrollTop = chatBox.scrollHeight;
}

async function longPoll() {
    if (!selectedSalesId) return;

    try {
        const res = await fetch(`get_messages_longpoll.php?to=${selectedSalesId}&last_id=${lastMessageId}`, {cache: "no-store"});
        if (!res.ok) throw new Error('Failed to fetch messages');
        const messages = await res.json();

        if (messages.length > 0) {
            appendMessages(messages);
        }

        // Jalankan lagi long polling
        longPoll();
    } catch (error) {
        console.error(error);
        // Kalau error, coba lagi setelah delay
        setTimeout(longPoll, 3000);
    }
}

// Start long polling saat halaman siap
longPoll();
</script>

</body>
</html>
