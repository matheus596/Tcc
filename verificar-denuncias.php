<?php
session_start();
require 'config.php';
require 'verificar-nivel.php';
require 'proteger.php';

verificarAcesso(2);

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// ATUALIZAR STATUS
if (isset($_POST['status_id']) && isset($_POST['novo_status'])) {

    $id_denuncia = (int) $_POST['status_id'];
    $novo_status = $_POST['novo_status'];

    $stmt_update = $conn->prepare("UPDATE denuncias SET status = ? WHERE id = ?");
    $stmt_update->bind_param("si", $novo_status, $id_denuncia);
    $stmt_update->execute();
    $stmt_update->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// DELETAR DENÚNCIA
if (isset($_POST['deletar_id'])) {

    if (isset($_SESSION['level']) && $_SESSION['level'] >= 2) {

        $id_denuncia_deletar = (int) $_POST['deletar_id'];

        try {

            $stmt_delete = $conn->prepare("DELETE FROM denuncias WHERE id = ?");
            $stmt_delete->bind_param("i", $id_denuncia_deletar);
            $stmt_delete->execute();
            $stmt_delete->close();

            header("Location: " . $_SERVER['PHP_SELF'] . "?msg=denuncia_deletada");
            exit();

        } catch (Exception $e) {

            header("Location: " . $_SERVER['PHP_SELF'] . "?msg=erro_deletar");
            exit();

        }

    } else {

        header("Location: " . $_SERVER['PHP_SELF'] . "?msg=sem_permissao_deletar");
        exit();

    }
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

    <script>
        function confirmarExclusao(id) {
            return confirm("Tem certeza que deseja deletar esta denúncia (ID: " + id + ")? Esta ação é irreversível.");
        }
    </script>

</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container mt-4">

        <h1>Denúncias Registradas</h1>

        <?php

        if ($conn->connect_error) {

            echo '<div class="alert alert-danger">Erro na conexão com o banco de dados</div>';

        } else {

            if (isset($_GET['msg'])) {

                if ($_GET['msg'] == 'denuncia_deletada') {
                    echo '<div class="alert alert-success">Denúncia deletada com sucesso!</div>';
                } elseif ($_GET['msg'] == 'erro_deletar') {
                    echo '<div class="alert alert-danger">Erro ao deletar denúncia.</div>';
                } elseif ($_GET['msg'] == 'sem_permissao_deletar') {
                    echo '<div class="alert alert-warning">Você não tem permissão para deletar denúncias.</div>';
                }

            }

            $stmt = $conn->prepare("
                SELECT 
                    id,
                    titulo,
                    descricao,
                    escola,
                    local,
                    tipo_bullying,
                    vitima,
                    autor,
                    data_criacao,
                    anonimo,
                    status 
                FROM denuncias 
                ORDER BY data_criacao DESC
            ");

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {

                    $cor = "bg-secondary";

                    if ($row['status'] == "analise") $cor = "bg-warning";
                    if ($row['status'] == "verificado") $cor = "bg-primary";
                    if ($row['status'] == "resolvido") $cor = "bg-success";
                    if ($row['status'] == "rejeitado") $cor = "bg-danger";
        ?>

                    <div class="card mb-4">

                        <div class="card-header d-flex justify-content-between align-items-center">

                            <h5 class="card-title mb-0">
                                <?php echo htmlspecialchars($row['titulo']); ?>
                            </h5>

                            <span class="badge <?php echo $cor; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>

                        </div>

                        <div class="card-body">

                            <p class="card-text">
                                <?php echo htmlspecialchars($row['descricao']); ?>
                            </p>

                            <div class="row">

                                <div class="col-md-6">

                                    <strong>Escola:</strong>
                                    <?php echo htmlspecialchars($row['escola']); ?><br>

                                    <strong>Local:</strong>
                                    <?php echo htmlspecialchars($row['local']); ?><br>

                                    <strong>Tipo:</strong>
                                    <?php echo htmlspecialchars($row['tipo_bullying']); ?>

                                </div>

                                <div class="col-md-6">

                                    <strong>Vítima:</strong>
                                    <?php echo htmlspecialchars($row['vitima']); ?><br>

                                    <strong>Autor:</strong>
                                    <?php echo htmlspecialchars($row['autor']); ?><br>

                                    <strong>Data:</strong>
                                    <?php echo date('d/m/Y H:i', strtotime($row['data_criacao'])); ?>

                                </div>

                            </div>

                            <div class="mt-3 d-flex justify-content-between align-items-center">

                                <div>

                                    <?php if ($row['anonimo'] == 1): ?>

                                        <span class="text-muted">
                                            <em>Denúncia anônima</em>
                                        </span>

                                    <?php endif; ?>

                                </div>

                                <div class="d-flex gap-2 align-items-center">

                                    <!-- DROPDOWN STATUS -->

                                    <form method="POST">

                                        <input type="hidden" name="status_id" value="<?php echo $row['id']; ?>">

                                        <select name="novo_status"
                                                class="form-select form-select-sm"
                                                onchange="this.form.submit()">

                                            <option value="analise"
                                                <?php if ($row['status'] == "analise") echo "selected"; ?>>
                                                Em análise
                                            </option>

                                            <option value="verificado"
                                                <?php if ($row['status'] == "verificado") echo "selected"; ?>>
                                                Verificado
                                            </option>

                                            <option value="resolvido"
                                                <?php if ($row['status'] == "resolvido") echo "selected"; ?>>
                                                Resolvido
                                            </option>

                                            <option value="rejeitado"
                                                <?php if ($row['status'] == "rejeitado") echo "selected"; ?>>
                                                Rejeitado
                                            </option>

                                        </select>

                                    </form>

                                    <?php if (isset($_SESSION['level']) && $_SESSION['level'] >= 2): ?>

                                        <form method="POST"
                                              onsubmit="return confirmarExclusao(<?php echo $row['id']; ?>);">

                                            <input type="hidden" name="deletar_id" value="<?php echo $row['id']; ?>">

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger">
                                                Deletar
                                            </button>

                                        </form>

                                    <?php endif; ?>

                                </div>

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