<?php
include 'db.php';

$dataAtual = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM pedidos WHERE DATE(data_hora) = ? ORDER BY impresso ASC, id ASC");
$stmt->execute([$dataAtual]);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel de Impressão</title>
    <link  rel="stylesheet" href="style/aten.css" />
    <script>
        setTimeout(function(){
            window.location.reload(1);
        }, 10000);

        function alterarEstadoImpressao(botao, pedido_id) {
            if (botao.classList.contains('botao-nao-impresso')) {
                botao.classList.remove('botao-nao-impresso');
                botao.classList.add('botao-impresso');
                botao.textContent = 'Impresso';
                // Atualiza o estado de impressão no banco de dados
                fetch('atualizar_estado.php', {
                    method: 'POST',
                    body: JSON.stringify({ pedido_id: pedido_id, impresso: 1 })
                });
            } else {
                botao.classList.remove('botao-impresso');
                botao.classList.add('botao-nao-impresso');
                botao.textContent = 'Não Impresso';
                // Atualiza o estado de impressão no banco de dados
                fetch('atualizar_estado.php', {
                    method: 'POST',
                    body: JSON.stringify({ pedido_id: pedido_id, impresso: 0 })
                });
            }
        }
    </script>
</head>
<body>
    <h2>PAINEL DE IMPRESSÃO (<?= date('d/m', strtotime($dataAtual)) ?>)</h2>
    <?php
    $ordemChegada = 0;
    if ($pedidos) {
        foreach ($pedidos as $pedido) {
            $ordemChegada++;
            // Verifica se o pedido está marcado como impresso
            $estadoImpressao = ($pedido['impresso'] == 1) ? 'Impresso' : 'Não Impresso';
            $classeBotao = ($pedido['impresso'] == 1) ? 'botao-impresso' : 'botao-nao-impresso';
            ?>
            <div>
                <p><strong>PST/GRAD:</strong> <?php echo $pedido['post_grad']; ?></p>
                <p><strong>NOME DE GUERRA:</strong> <?php echo $pedido['nome_guerra']; ?></p>
                <p><strong>SEÇÃO:</strong> <?php echo $pedido['secao']; ?></p>
                <p><strong>IMPRESSÃO:</strong> <?php echo $pedido['folha']; ?></p>
                <p><strong>FRENTE/VERSO:</strong> <?php echo $pedido['frente_verso']; ?></p>
                <p><strong>QTD DE CÓPIAS:</strong> <?php echo $pedido['qtd']; ?></p>
                <p><strong>DATA E HORA DO PEDIDO:</strong> <?php echo date('d/m H:i', strtotime($pedido['data_hora'])); ?></p>
                <button class="<?php echo $classeBotao; ?>" onclick="alterarEstadoImpressao(this, <?php echo $pedido['id']; ?>)"><?php echo $estadoImpressao; ?></button>
                <?php
                // Consulta os arquivos relacionados a este pedido
                $stmt = $pdo->prepare("SELECT * FROM arquivos_pedido WHERE pedido_id = ?");
                $stmt->execute([$pedido['id']]);
                $arquivos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($arquivos) {
                    foreach ($arquivos as $arquivo) {
                        ?>
                        <p><a href="<?php echo $arquivo['arquivo_path']; ?>" target="_blank"><?php echo $arquivo['arquivo_nome']; ?></a></p>
                        <?php
                    }
                }
                ?>
                <form action="processa_entrega.php" method="post" style="margin-top: 10px;">
                    <input type="hidden" name="id" value="<?php echo $pedido['id']; ?>">
                    <?php
                    // Adicione um campo hidden para cada arquivo relacionado ao pedido
                    if ($arquivos) {
                        foreach ($arquivos as $arquivo) {
                            ?>
                            <input type="hidden" name="arquivos_id[]" value="<?php echo $arquivo['id']; ?>">
                            <?php
                        }
                    }
                    ?>
                    <button type="submit" class="botao-entregue">Marcar como Entregue</button>
                </form>
            </div>
            <hr>
            <?php
        }
    } else {
        echo "<p>Nenhum pedido para hoje.</p>";
    }
    ?>
</body>
</html>
