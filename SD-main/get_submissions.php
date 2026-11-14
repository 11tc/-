<?php
session_start();
require 'db.php';

$teacherId = $_SESSION['teacher_id'] ?? 0;
if (!$teacherId) {
    http_response_code(401);
    echo '[]';
    exit;
}

$stmt = $conn->prepare("
    SELECT s.id, st.name AS student, st.id AS sid, s.title,
           s.description AS `desc`, DATE(s.created_at) AS date,
           s.file_path AS fileUrl, s.status, s.grade,
           s.max_grade AS maxGrade, s.notes
    FROM submissions s
    JOIN students st ON st.id = s.student_id
    WHERE s.teacher_id = ?
    ORDER BY s.created_at DESC
");
$stmt->bind_param('i', $teacherId);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($rows as &$r) {
    $r['fileName'] = basename($r['fileUrl'] ?? '');
    $r['grade']    = $r['grade'] !== null ? (float)$r['grade'] : null;
    $r['notes']    = $r['notes'] ?? '';
    $r['desc']     = $r['desc'] ?? '';
}

echo json_encode($rows, JSON_UNESCAPED_UNICODE);