<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postGrad = $_POST['post_grad'];
    $nomeGuerra = $_POST['nome_guerra'];
    $secao = $_POST['secao'];
    $folha = $_POST['folha'];
    $frenteVerso = $_POST['frente_verso'];
    $arquivos = $_FILES['arquivos'];
    $qtd = $_POST['qtd'];

    $diretorioUpload = "uploads/";

    // Insere o pedido na tabela pedidos
    $stmt = $pdo->prepare("INSERT INTO pedidos (post_grad, nome_guerra, secao, folha, frente_verso, qtd) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$postGrad, $nomeGuerra, $secao, $folha, $frenteVerso, $qtd]);
    $pedidoId = $pdo->lastInsertId();

    // Insere os arquivos na tabela arquivos_pedido
    foreach ($arquivos['tmp_name'] as $index => $tmp_name) {
        $nomeArquivo = $arquivos['name'][$index];
        $arquivoPath = $diretorioUpload . basename($nomeArquivo);

        if (move_uploaded_file($tmp_name, $arquivoPath)) {
            $stmt = $pdo->prepare("INSERT INTO arquivos_pedido (pedido_id, arquivo_nome, arquivo_path) VALUES (?, ?, ?)");
            $stmt->execute([$pedidoId, $nomeArquivo, $arquivoPath]);
        }
    }

    echo "Arquivos enviados com sucesso!";
    echo '<meta http-equiv="refresh" content="5;url=index.html">';
}
?>
