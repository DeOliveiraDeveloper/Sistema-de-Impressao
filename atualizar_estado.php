<?php
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$pedido_id = $data['pedido_id'];
$impresso = $data['impresso'];

$stmt = $pdo->prepare("UPDATE pedidos SET impresso = ? WHERE id = ?");
$stmt->execute([$impresso, $pedido_id]);
?>
