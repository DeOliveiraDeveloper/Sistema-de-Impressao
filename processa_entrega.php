<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $arquivos_id = $_POST['arquivos_id'];

    // Excluir os arquivos do sistema de arquivos
    foreach ($arquivos_id as $arquivo_id) {
        $stmt = $pdo->prepare("SELECT arquivo_path FROM arquivos_pedido WHERE id = ?");
        $stmt->execute([$arquivo_id]);
        $arquivo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($arquivo && file_exists($arquivo['arquivo_path'])) {
            unlink($arquivo['arquivo_path']);
        }
    }

    // Excluir os arquivos do banco de dados
    $stmt = $pdo->prepare("DELETE FROM arquivos_pedido WHERE id IN (" . implode(',', $arquivos_id) . ")");
    $stmt->execute();

    // Excluir o pedido do banco de dados
    $stmt = $pdo->prepare("DELETE FROM pedidos WHERE id = ?");
    $stmt->execute([$id]);

    // Redirecionar de volta para atendente.php
    header('Location: atendente.php');
    exit;
}
?>
