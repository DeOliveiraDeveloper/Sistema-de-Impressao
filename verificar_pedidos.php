<?php
include 'db.php';

// Verifica se há novos pedidos
$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM pedidos WHERE DATE(data_hora) = ?");
$stmt->execute([date('Y-m-d')]);
$totalPedidos = $stmt->fetchColumn();

if ($totalPedidos > count($_SESSION['pedidos'])) {
    $_SESSION['pedidos'] = $totalPedidos;
    echo 'novo_pedido';
} else {
    echo 'sem_pedido';
}
?>
