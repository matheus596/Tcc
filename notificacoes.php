<?php
session_start();
require 'proteger.php';
require 'config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="favicon/android-chrome-512x512.png">
    <title>Notificações</title>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h1>Notificações</h1>
        <p class="text-muted">Veja aqui as mensagens recebidas sobre suas denúncias.</p>

        <?php
        if ($conn->connect_error) {
            echo '<div class="alert alert-danger">Erro na conexão com o banco de dados.</div>';
        } else {
            $stmt = $conn->prepare("SELECT id, mensagem, data_criacao FROM notificacoes WHERE usuario_id = ? ORDER BY id DESC");
            $stmt->bind_param("i", $_SESSION['usuario_id']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="card mb-3">';
                    echo '  <div class="card-body">';
                    echo '    <p class="mb-1">' . htmlspecialchars($row['mensagem']) . '</p>';
                    echo '    <small class="text-muted">Recebida em ' . date('d/m/Y H:i', strtotime($row['data_criacao'])) . '</small>';
                    echo '  </div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="alert alert-info">Você ainda não recebeu notificações.</div>';
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
