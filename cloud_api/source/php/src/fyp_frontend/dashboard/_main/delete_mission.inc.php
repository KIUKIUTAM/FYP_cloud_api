<?php
require_once '../../_inc/db.inc.php'; 
$pdo = dbconnect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $missionId = (int) $_POST['id'];

    $pdo->beginTransaction();

    // Delete the mission
    $stmt = $pdo->prepare("DELETE FROM missiontable WHERE mission_id = ?");
    $stmt->execute([$missionId]);

    $pdo->commit();
}
?>