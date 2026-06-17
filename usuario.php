<?php 
session_start();
require 'proteger.php';
require 'config.php';
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Plataforma para registro e informação sobre denúncias de bullying. Saiba mais sobre nossa missão e equipe.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="favicon/android-chrome-512x512.png">
    <title>Minha Conta — Denúncias Bullying</title>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h1>Minha Conta</h1>
        
        <div class="row">
            <div class="col-md-6">
                <h3>Informações da Conta</h3>
                <p><strong>Nome de Usuário:</strong> <?php echo htmlspecialchars($_SESSION['usuario_name']); ?></p>
                <p><strong>E-mail:</strong> <?php echo htmlspecialchars($_SESSION['usuario_email']); ?></p>
            </div>
            <div class="col-md-6">
                <h3>Alterar Senha</h3>
                <?php if (isset($_SESSION['erro_senha'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['erro_senha']; unset($_SESSION['erro_senha']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['sucesso_senha'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['sucesso_senha']; unset($_SESSION['sucesso_senha']); ?></div>
                <?php endif; ?>
                <form action="alterar-senha.php" method="post">
                    <div class="mb-3">
                        <label for="senha_atual" class="form-label">Senha Atual</label>
                        <input type="password" class="form-control" id="senha_atual" name="senha_atual" required>
                    </div>
                    <div class="mb-3">
                        <label for="nova_senha" class="form-label">Nova Senha</label>
                        <input type="password" class="form-control" id="nova_senha" name="nova_senha" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-primary">Alterar Senha</button>
                </form>
            </div>
        </div>
        
        <h3 class="mt-4">Minhas Denúncias</h3>
        <?php if (isset($_SESSION['mensagem_denuncia'])): ?>
            <div class="alert alert-info"><?php echo $_SESSION['mensagem_denuncia']; unset($_SESSION['mensagem_denuncia']); ?></div>
        <?php endif; ?>
        
        <?php
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            echo '<div class="alert alert-danger">Erro na conexão com o banco</div>';
        } else {
            $stmt = $conn->prepare("SELECT id, titulo, descricao, data_criacao, data_ocorrido, status FROM denuncias WHERE usuario_id = ? ORDER BY data_criacao DESC");
            $stmt->bind_param("i", $_SESSION['usuario_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="card mb-3">';
                    echo '<div class="card-header d-flex justify-content-between align-items-center">';
                    echo '<span>' . htmlspecialchars($row['titulo']) . '</span>';
                    echo '<form action="deletar-denuncia.php" method="post" onsubmit="return confirm(\'Tem certeza que deseja excluir esta denúncia?\');" style="margin:0;">';
                    echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
                    echo '<button type="submit" class="btn btn-danger btn-sm">Deletar</button>';
                    echo '</form>';
                    echo '</div>';
                    echo '<div class="card-body">';
                    echo '<p>' . htmlspecialchars($row['descricao']) . '</p>';
                    echo '<small>Data do ocorrido: ' . (!empty($row['data_ocorrido']) ? date('d/m/Y', strtotime($row['data_ocorrido'])) : 'Não informada') . ' | Criada em: ' . date('d/m/Y H:i', strtotime($row['data_criacao'])) . ' | Status: ' . htmlspecialchars($row['status']) . '</small>';
                    echo '</div></div>';
                }
            } else {
                echo '<p>Você ainda não fez nenhuma denúncia.</p>';
            }
            $stmt->close();
            $conn->close();
        }
        ?>
        
        <a class="nav-link" href="logout.php">Sair</a>
    </div>

    <footer class="mt-4 text-center">
        <p>© 2026 Denúncias Bullying</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
