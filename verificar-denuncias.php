<?php
session_start();
require 'config.php';
require 'verificar-nivel.php';
require 'proteger.php';

verificarAcesso(2); // Apenas usuários com nível 2 ou superior

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// LÓGICA PARA ATUALIZAR O STATUS
if (isset($_POST['verificar_id'])) {
    $id_denuncia = (int)$_POST['verificar_id'];
    $stmt_update = $conn->prepare("UPDATE denuncias SET status = 'verificado' WHERE id = ?");
    $stmt_update->bind_param("i", $id_denuncia);
    $stmt_update->execute();
    $stmt_update->close();
    // Recarrega a página para atualizar a lista
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Denúncias</title>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h1>Denúncias Registradas</h1>

        <?php
        if ($conn->connect_error ) {
            echo '<div class="alert alert-danger">Erro na conexão com o banco de dados</div>';
        } else {
            // Consulta corrigida: WHERE antes do ORDER BY
            // Removi o filtro de status fixo para você ver as já verificadas também, 
            // mas se quiser ver SÓ as pendentes, use: WHERE status = 'pendente'
            $stmt = $conn->prepare("SELECT id, titulo, descricao, escola, local, tipo_bullying, vitima, autor, data_criacao, anonimo, status FROM denuncias ORDER BY data_criacao DESC");
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($row['titulo']); ?></h5>
                            <span class="badge <?php echo ($row['status'] == 'verificado') ? 'bg-success' : 'bg-warning'; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><?php echo htmlspecialchars($row['descricao']); ?></p>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Escola:</strong> <?php echo htmlspecialchars($row['escola']); ?>  

                                    <strong>Local:</strong> <?php echo htmlspecialchars($row['local']); ?>  

                                    <strong>Tipo:</strong> <?php echo htmlspecialchars($row['tipo_bullying']); ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Vítima:</strong> <?php echo htmlspecialchars($row['vitima']); ?>  

                                    <strong>Autor:</strong> <?php echo htmlspecialchars($row['autor']); ?>  

                                    <strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($row['data_criacao'])); ?>
                                </div>
                            </div>

                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <?php if ($row['anonimo'] == 1): ?>
                                        <span class="text-muted"><em>Denúncia anônima</em></span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- BOTÃO DE VERIFICAÇÃO -->
                                <?php if ($row['status'] !== 'verificado'): ?>
                                    <form method="POST">
                                        <input type="hidden" name="verificar_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-success">Marcar como Verificado</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-success">✅ Verificada</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="alert alert-info">Nenhuma denúncia registrada ainda.</div>';
            }
            $stmt->close();
        }
        $conn->close();
        ?>
    </div>

    <footer class="mt-4 text-center">
        <p>© 2026 Denúncias Bullying</p>
    </footer>
</body>
</html>
