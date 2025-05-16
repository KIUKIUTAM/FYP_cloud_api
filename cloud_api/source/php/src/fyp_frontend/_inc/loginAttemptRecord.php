<?php
// Ensure no whitespace before <?php
require_once 'db.inc.php';

function setCount($ipAddr, $count, $b_result) {
    try {
        $pdo = dbconnect();
        $sql = 'UPDATE loginrecord SET lastLogin = UNIX_TIMESTAMP(), result = :result, retryCount = :count WHERE ipAddr = :ip';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ip', $ipAddr);
        $stmt->bindParam(':result', $b_result);
        $stmt->bindParam(':count', $count);
        return $stmt->execute();
    } catch(PDOException $e) {
        error_log($e->getMessage());
        return false;
    }
}

function addRecord($ipAddr, $b_result) {
    try {
        $pdo = dbconnect();
        $sql = 'INSERT INTO loginrecord(ipAddr, lastLogin, result, retryCount) VALUES (:ip, UNIX_TIMESTAMP(), :result, 1)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ip', $ipAddr);
        $stmt->bindParam(':result', $b_result);
        return $stmt->execute();
    } catch(PDOException $e) {
        error_log($e->getMessage());
        return false;
    }
}

function updateLoginRecord($ipAddr, $b_result) {
    try {
        $pdo = dbconnect();
        $sql = 'SELECT lastLogin, result, retryCount FROM loginrecord WHERE ipAddr = :ip';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ip', $ipAddr);
        $stmt->execute();
        $dbResponse = $stmt->fetch();

        if($stmt->rowCount()) {
            $lastLogin = intval($dbResponse['lastLogin']);
            date_default_timezone_set('Asia/Hong_Kong');
            
            if (time() <= $lastLogin + 60) {
                if ($dbResponse['retryCount'] >= 5) {
                    return false;
                } else {
                    $newCount = ($b_result === 1) ? 1 : ($dbResponse['retryCount'] + 1);
                    return setCount($ipAddr, $newCount, $b_result);
                }
            } else {
                return setCount($ipAddr, 1, $b_result);
            }
        } else {
            return addRecord($ipAddr, $b_result);
        }
    } catch(PDOException $e) {
        error_log($e->getMessage());
        return false;
    }
}