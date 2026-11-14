<?php
session_start();
require 'db.php';

$in    = json_decode(file_get_contents('php://input'), true);
$email = trim($in['email'] ?? '');
$pass  = $in['password'] ?? '';

$stmt = $conn->prepare("SELECT id, name, password FROM teachers WHERE email = ? LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

$ok = false;
if ($row) {
    $stored = $row['password'];
    $isHash = strlen($stored) >= 60 && $stored[0] === '$';
    $ok = $isHash ? password_verify($pass, $stored) : hash_equals($stored, $pass);
}

if (!$ok) {
    echo json_encode(['success' => false, 'message' => 'الإيميل أو كلمة المرور غير صحيحة'], JSON_UNESCAPED_UNICODE);
    exit;
}

$_SESSION['teacher_id'] = (int)$row['id'];
echo json_encode(['success' => true]);