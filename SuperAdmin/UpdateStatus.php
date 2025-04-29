<?php
session_start();
include('../DB.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Super Admin') {
    echo 'unauthorized'; 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
    $id = $_POST['id']; 
    $status = $_POST['status']; 
    
    $conn->begin_transaction(); 

    try {
        // Update status request nya
        $stmt = $conn->prepare("UPDATE requests SET status = ?, updatedAt = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
        
        // klo statusnya disetujui, kurangi stok mobil
        if ($status === 'Disetujui') {
            $stmt = $conn->prepare("
                UPDATE mobil m 
                JOIN requests r ON m.id = r.itemId 
                SET m.stok = m.stok - 1 
                WHERE r.id = ? AND m.stok > 0
            ");
            $stmt->bind_param('i', $id);
            $stmt->execute();

            // cek klo stok ternyata ga cukup (stok udah 0), kirim error
            if ($stmt->affected_rows === 0) {
                throw new Exception("Stok tidak mencukupi");
            }
        }

        $conn->commit();
        echo 'success';
    } catch (Exception $e) {
        $conn->rollback();
        echo 'error: ' . $e->getMessage(); // tampilin pesan error nya
    }
} else {
    echo 'invalid request'; // klo request ga valid, kasih tahu
}
?>
