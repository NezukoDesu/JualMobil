<?php
session_start();
include '../DB.php';

// Pastikan user sudah login dan role adalah Customer (case insensitive)
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'customer') {
    die("Unauthorized access");
}

$customerId = $_SESSION['id'];
$selectedSalesId = isset($_GET['to']) ? (int)$_GET['to'] : null;

// Ambil daftar sales (role 'sales' lowercase)
$salesUsers = mysqli_query($conn, "SELECT id, username FROM users WHERE LOWER(role) = 'sales'");
if (!$salesUsers) {
    die("Query error: " . mysqli_error($conn));
}

// Kirim pesan jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'], $_POST['receiver_id'])) {
    $message = $_POST['message'];
    $receiverId = (int)$_POST['receiver_id'];
    $timestamp = date('Y-m-d H:i:s');

    $stmt = mysqli_prepare($conn, "INSERT INTO messages (senderId, receiverId, message, createdAt, updatedAt) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iisss", $customerId, $receiverId, $message, $timestamp, $timestamp);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ?to=$receiverId");
        exit;
    } else {
        echo "<p style='color:red;'>Gagal mengirim pesan: " . mysqli_error($conn) . "</p>";
    }
}

// Ambil chat jika sales dipilih
$chatMessages = [];
if ($selectedSalesId) {
    $chatQuery = "
        SELECT m.*, u.username AS sender_name
        FROM messages m
        JOIN users u ON m.senderId = u.id
        WHERE (m.senderId = ? AND m.receiverId = ?)
           OR (m.senderId = ? AND m.receiverId = ?)
        ORDER BY m.createdAt ASC
    ";
    $stmt = mysqli_prepare($conn, $chatQuery);
    mysqli_stmt_bind_param($stmt, "iiii", $customerId, $selectedSalesId, $selectedSalesId, $customerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $chatMessages = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Chat dengan Sales - JualMobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 admin-page">
    <?php include('../Layouts/navbar.php'); ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto bg-white/90 backdrop-blur-md rounded-xl shadow-xl min-h-[600px] flex">
            <!-- Sales List Sidebar -->
            <div class="w-64 bg-gray-50 p-4 border-r border-gray-200">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Daftar Sales</h2>
                <div class="space-y-2">
                    <?php while ($row = mysqli_fetch_assoc($salesUsers)): ?>
                        <a href="?to=<?= $row['id'] ?>" 
                           class="block p-3 rounded-lg transition-colors duration-200 
                                  <?= $selectedSalesId == $row['id'] 
                                      ? 'bg-blue-100 text-blue-800' 
                                      : 'hover:bg-gray-100' ?>">
                            <i class="fas fa-user-tie mr-2"></i>
                            <?= htmlspecialchars($row['username']) ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="flex-1 flex flex-col">
                <?php if ($selectedSalesId): ?>
                    <!-- Messages -->
                    <div class="flex-1 p-4 overflow-y-auto space-y-4" id="chat-box">
                        <?php foreach ($chatMessages as $msg): ?>
                            <div class="flex <?= $msg['senderId'] == $customerId ? 'justify-end' : 'justify-start' ?>">
                                <div class="max-w-[70%] <?= $msg['senderId'] == $customerId 
                                    ? 'bg-blue-600 text-white' 
                                    : 'bg-gray-100 text-gray-800' ?> rounded-lg px-4 py-2">
                                    <div class="font-medium text-sm mb-1">
                                        <?= htmlspecialchars($msg['sender_name']) ?>
                                    </div>
                                    <div><?= htmlspecialchars($msg['message']) ?></div>
                                    <div class="text-xs mt-1 <?= $msg['senderId'] == $customerId 
                                        ? 'text-blue-100' 
                                        : 'text-gray-500' ?>">
                                        <?= date('H:i', strtotime($msg['createdAt'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Message Input -->
                    <form method="post" class="p-4 border-t border-gray-200">
                        <div class="flex gap-2">
                            <input type="hidden" name="receiver_id" value="<?= $selectedSalesId ?>">
                            <input type="text" name="message" 
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Tulis pesan..." required>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Kirim
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="flex-1 flex items-center justify-center text-gray-500">
                        <div class="text-center">
                            <i class="fas fa-comments text-6xl mb-4"></i>
                            <p>Pilih sales untuk memulai chat</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
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
