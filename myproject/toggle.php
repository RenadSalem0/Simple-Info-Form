<?php
require 'config.php';

try {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception("Invalid user ID");
    }

    $id = (int)$_GET['id'];
    $conn->begin_transaction();

    $stmt = $conn->prepare("SELECT COALESCE(status, 0) as current_status FROM users WHERE id = ? FOR UPDATE");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("User not found");
    }

    $row = $result->fetch_assoc();
    $newStatus = $row['current_status'] ? 0 : 1;

    $update = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $update->bind_param("ii", $newStatus, $id);
    $update->execute();
    $conn->commit();

    echo $newStatus; 
} catch (Exception $e) {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->rollback();
    }
    http_response_code(400);
    echo "0"; 
}
?>
