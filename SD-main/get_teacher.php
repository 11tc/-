<?php
session_start();
require 'db.php';

$teacherId = $_SESSION['teacher_id'] ?? 0;
if (!$teacherId) {
    http_response_code(401);
    echo '{}';
    exit;
}

$stmt = $conn->prepare("SELECT name FROM teachers WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $teacherId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

echo json_encode(['name' => $row['name'] ?? ''], JSON_UNESCAPED_UNICODE);