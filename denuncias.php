<?php
session_start();
require 'config.php';
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="/favicon/android-chrome-512x512.png">
    <title>Denúncias</title>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h1>Denúncias Registradas</h1>

        <?php
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($conn->connect_error) {
            echo '<div class="alert alert-danger">Erro na conexão com o banco de dados</div>';
        } else {
            // Preparar consulta para buscar denúncias
            $stmt = $conn->prepare("SELECT titulo, descricao, escola, local, tipo_bullying, vitima, autor, data_criacao, anonimo, status, usuario_id FROM denuncias ORDER BY data_criacao DESC");
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Loop para exibir cada denúncia
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['titulo']); ?></h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><?php echo htmlspecialchars($row['descricao']); ?></p>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Escola:</strong> <?php echo htmlspecialchars($row['escola']); ?><br>
                                    <strong>Local:</strong> <?php echo htmlspecialchars($row['local']); ?><br>
                                    <strong>Tipo:</strong> <?php echo htmlspecialchars($row['tipo_bullying']); ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Vítima:</strong> <?php echo htmlspecialchars($row['vitima']); ?><br>
                                    <strong>Autor:</strong> <?php echo htmlspecialchars($row['autor']); ?><br>
                                    <strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($row['data_criacao'])); ?>
                                   <strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?><br>
                                </div>
                            </div>

                            <?php if ($row['anonimo'] == 1): ?>
                                <p class="text-muted mt-2"><em>Denúncia anônima</em></p>
                            <?php else: ?>
                               <?php echo htmlspecialchars($row['usuario_id']); ?><br>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="alert alert-info">Nenhuma denúncia registrada ainda.</div>';
            }

            $stmt->close();
            $conn->close();
        }
        ?>
    </div>

    <footer class="mt-4 text-center">
        <p>© 2026 Denúncias Bullying</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>