<?php
require_once __DIR__ . '/../backend/Database.php';

$db = db_get_connection();
$stmt = $db->prepare("
    UPDATE stores s
    JOIN users u ON s.owner_id = u.id
    SET s.contact_phone = u.phone
    WHERE (s.contact_phone IS NULL OR s.contact_phone = '')
      AND (u.phone IS NOT NULL AND u.phone != '')
");
$stmt->execute();
echo "Updated " . $stmt->rowCount() . " stores with owner's phone number.\n";

$stmt2 = $db->prepare("
    UPDATE stores s
    JOIN users u ON s.owner_id = u.id
    SET s.contact_email = u.email
    WHERE (s.contact_email IS NULL OR s.contact_email = '')
      AND (u.email IS NOT NULL AND u.email != '')
");
$stmt2->execute();
echo "Updated " . $stmt2->rowCount() . " stores with owner's email.\n";
