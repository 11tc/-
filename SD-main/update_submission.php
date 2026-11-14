<?php
session_start();
require 'db.php';

$teacherId = $_SESSION['teacher_id'] ?? 0;
$in = json_decode(file_get_contents('php://input'), true);

$id     = (int)($in['id'] ?? 0);
$status = $in['status'] ?? '';
$grade  = (isset($in['grade']) && $in['grade'] !== '') ? (float)$in['grade'] : null;
$notes  = trim($in['notes'] ?? '');

if (!$teacherId || !$id || !in_array($status, ['pending', 'accepted', 'rejected'])) {
    http_response_code(400);
    echo json_encode(['ok' => false]);
    exit;
}

$stmt = $conn->prepare("UPDATE submissions SET status = ?, grade = ?, notes = ? WHERE id = ? AND teacher_id = ?");
$stmt->bind_param('sdsii', $status, $grade, $notes, $id, $teacherId);
$stmt->execute();

echo json_encode(['ok' => true]);