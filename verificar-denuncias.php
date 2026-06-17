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
    $status_permitidos = ['triagem', 'analise', 'resolvido'];
    $novo_status = in_array($_POST['novo_status'] ?? '', $status_permitidos, true)
        ? $_POST['novo_status']
        : 'triagem';
    $resolucao = trim($_POST['resolucao'] ?? '');

    if ($novo_status !== 'resolvido') {
        $resolucao = '';
    }

    if ($novo_status === 'resolvido' && $resolucao === '') {
        $_SESSION['erro_status'] = 'Descreva o que foi feito para resolver a denúncia.';
        header('Location: verificar-denuncias.php');
        exit();
    }

    $check = $conn->query("SHOW COLUMNS FROM denuncias LIKE 'resolucao'");
    if ($check && $check->num_rows === 0) {
        $conn->query("ALTER TABLE denuncias ADD COLUMN resolucao TEXT DEFAULT NULL");
    }

    $stmt_update = $conn->prepare("UPDATE denuncias SET status = ?, resolucao = ? WHERE id = ?");
    $stmt_update->bind_param("ssi", $novo_status, $resolucao, $id_denuncia);
    $stmt_update->execute();
    $stmt_update->close();

    $_SESSION['mensagem_status'] = 'Status atualizado com sucesso.';
    header('Location: verificar-denuncias.php');
    exit();
}

// DELETAR DENÚNCIA
if (isset($_POST['deletar_id'])) {

    if (isset($_SESSION['level']) && $_SESSION['level'] >= 2) {

        $id_denuncia_deletar = (int) $_POST['deletar_id'];
        $motivo = trim($_POST['motivo'] ?? '');

        if ($motivo === '') {
            header("Location: " . $_SERVER['PHP_SELF'] . "?msg=motivo_obrigatorio");
            exit();
        }

        try {

            $stmt_info = $conn->prepare("SELECT usuario_id, titulo FROM denuncias WHERE id = ?");
            $stmt_info->bind_param("i", $id_denuncia_deletar);
            $stmt_info->execute();
            $resultado = $stmt_info->get_result();

            if ($resultado && $resultado->num_rows > 0) {
                $denuncia = $resultado->fetch_assoc();
                $usuario_id_da_denuncia = (int) $denuncia['usuario_id'];
                $titulo = $denuncia['titulo'];

                $sql_notificacao = "INSERT INTO notificacoes (usuario_id, mensagem) VALUES (?, ?)";
                $stmt_notificacao = $conn->prepare($sql_notificacao);
                $mensagem = "Sua denúncia \"$titulo\" foi removida. Motivo: $motivo";
                $stmt_notificacao->bind_param("is", $usuario_id_da_denuncia, $mensagem);
                $stmt_notificacao->execute();
                $stmt_notificacao->close();
            }

            $stmt_info->close();

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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="favicon/android-chrome-512x512.png">

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
        $contadores = ['todos' => 0, 'triagem' => 0, 'analise' => 0, 'resolvido' => 0];

        if ($conn->connect_error) {
            echo '<div class="alert alert-danger">Erro na conexão com o banco de dados</div>';
        } else {
            $nivel_usuario = (int) ($_SESSION['level'] ?? 1);
            $escola_usuario = trim($_SESSION['usuario_escola'] ?? '');
            $busca = trim($_GET['busca'] ?? '');
            $busca_param = "%$busca%";
            $filtro_status = $_GET['status_filtro'] ?? 'todos';
            $status_permitidos = ['todos', 'triagem', 'analise', 'resolvido'];
            if (!in_array($filtro_status, $status_permitidos, true)) {
                $filtro_status = 'todos';
            }

            $stats_sql = "SELECT status, COUNT(*) AS total FROM denuncias WHERE 1=1";
            if ($nivel_usuario === 2 && $escola_usuario !== '') {
                $stats_sql .= " AND escola = '" . $conn->real_escape_string($escola_usuario) . "'";
            }
            $stats_sql .= " GROUP BY status";
            $stats_result = $conn->query($stats_sql);

            if ($stats_result) {
                while ($stat = $stats_result->fetch_assoc()) {
                    $status = $stat['status'] ?? '';
                    if (isset($contadores[$status])) {
                        $contadores[$status] = (int) $stat['total'];
                    }
                    $contadores['todos'] += (int) $stat['total'];
                }
            }
        }
        ?>

        <!-- Formulário de Busca -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="busca" class="form-label">Pesquisar denúncias</label>
                        <input type="text" class="form-control" id="busca" name="busca" placeholder="Digite título, descrição ou escola..." value="<?php echo htmlspecialchars($_GET['busca'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="status_filtro" class="form-label">Filtrar por status</label>
                        <select id="status_filtro" name="status_filtro" class="form-select">
                            <option value="todos" <?php echo $filtro_status === 'todos' ? 'selected' : ''; ?>>Todos</option>
                            <option value="triagem" <?php echo $filtro_status === 'triagem' ? 'selected' : ''; ?>>Em triagem</option>
                            <option value="analise" <?php echo $filtro_status === 'analise' ? 'selected' : ''; ?>>Em análise</option>
                            <option value="resolvido" <?php echo $filtro_status === 'resolvido' ? 'selected' : ''; ?>>Resolvido</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                        <a href="verificar-denuncias.php" class="btn btn-secondary">Limpar</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-primary h-100">
                    <div class="card-body text-center">
                        <h6 class="card-title text-primary">Total</h6>
                        <h3 class="mb-0"><?php echo $contadores['todos']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-warning h-100">
                    <div class="card-body text-center">
                        <h6 class="card-title text-warning">Em triagem</h6>
                        <h3 class="mb-0"><?php echo $contadores['triagem']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-info h-100">
                    <div class="card-body text-center">
                        <h6 class="card-title text-info">Em análise / Resolvido</h6>
                        <h3 class="mb-0"><?php echo $contadores['analise'] + $contadores['resolvido']; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <?php

        if ($conn->connect_error) {

            echo '<div class="alert alert-danger">Erro na conexão com o banco de dados</div>';

        } else {

            if (isset($_SESSION['mensagem_status'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['mensagem_status']) . '</div>';
                unset($_SESSION['mensagem_status']);
            }

            if (isset($_SESSION['erro_status'])) {
                echo '<div class="alert alert-warning">' . htmlspecialchars($_SESSION['erro_status']) . '</div>';
                unset($_SESSION['erro_status']);
            }

            if (isset($_GET['msg'])) {

                if ($_GET['msg'] == 'denuncia_deletada') {
                    echo '<div class="alert alert-success">Denúncia deletada com sucesso!</div>';
                } elseif ($_GET['msg'] == 'erro_deletar') {
                    echo '<div class="alert alert-danger">Erro ao deletar denúncia.</div>';
                } elseif ($_GET['msg'] == 'sem_permissao_deletar') {
                    echo '<div class="alert alert-warning">Você não tem permissão para deletar denúncias.</div>';
                } elseif ($_GET['msg'] == 'motivo_obrigatorio') {
                    echo '<div class="alert alert-warning">É obrigatório informar o motivo da remoção.</div>';
                }

            }

            // Montar query com filtro opcional
            $sql = "
                SELECT 
                    d.id,
                    d.titulo,
                    d.descricao,
                    d.escola,
                    d.local,
                    d.tipo_bullying,
                    d.vitima,
                    d.autor,
                    d.data_criacao,
                    d.status,
                    d.resolucao,
                    d.anexo,
                    d.permitir_contato,
                    d.usuario_id,
                    u.email
                FROM denuncias d
                LEFT JOIN usuarios u ON d.usuario_id = u.id";

            $condicoes = [];
            $tipos = '';
            $parametros = [];

            if ($nivel_usuario === 2 && $escola_usuario !== '') {
                $condicoes[] = 'd.escola = ?';
                $tipos .= 's';
                $parametros[] = $escola_usuario;
            }

            if (!empty($busca)) {
                $condicoes[] = "(d.titulo LIKE ? OR d.descricao LIKE ? OR d.escola LIKE ?)";
                $tipos .= 'sss';
                $parametros[] = $busca_param;
                $parametros[] = $busca_param;
                $parametros[] = $busca_param;
            }

            if ($filtro_status !== 'todos') {
                $condicoes[] = 'd.status = ?';
                $tipos .= 's';
                $parametros[] = $filtro_status;
            }

            if (!empty($condicoes)) {
                $sql .= ' WHERE ' . implode(' AND ', $condicoes);
            }

            $sql .= " ORDER BY d.data_criacao DESC";

            $stmt = $conn->prepare($sql);

            if (!empty($parametros)) {
                $stmt->bind_param($tipos, ...$parametros);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {

                if (!empty($busca)) {
                    echo '<div class="alert alert-info">Encontrados ' . $result->num_rows . ' resultado(s) para "' . htmlspecialchars($busca) . '"</div>';
                }

                while ($row = $result->fetch_assoc()) {

                    $cor = "bg-secondary";
                    $label_status = ucfirst($row['status'] ?? '');

                    if ($row['status'] == "triagem") { $cor = "bg-warning text-dark"; $label_status = "Em triagem"; }
                    if ($row['status'] == "analise") { $cor = "bg-info text-dark"; $label_status = "Em análise"; }
                    if ($row['status'] == "resolvido") { $cor = "bg-success"; $label_status = "Resolvido"; }
        ?>

                    <div class="card mb-4">

                        <div class="card-header d-flex justify-content-between align-items-center">

                            <h5 class="card-title mb-0">
                                <?php echo htmlspecialchars($row['titulo']); ?>
                            </h5>

                            <span class="badge <?php echo $cor; ?>">
                                <?php echo htmlspecialchars($label_status); ?>
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
                                    <?php if ($row['permitir_contato'] == 1): ?>
                                        <div class="mb-2">
                                            <span class="badge bg-success">Permite contato</span>
                                            <br><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Não permite contato</span>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex gap-2 align-items-center">

                                    <!-- DROPDOWN STATUS -->

                                    <form method="POST" class="d-flex flex-column gap-2 align-items-end">

                                        <input type="hidden" name="status_id" value="<?php echo $row['id']; ?>">

                                        <select name="novo_status"
                                                class="form-select form-select-sm">

                                            <option value="triagem"
                                                <?php if ($row['status'] == "triagem") echo "selected"; ?>>
                                                Em triagem
                                            </option>

                                            <option value="analise"
                                                <?php if ($row['status'] == "analise") echo "selected"; ?>>
                                                Em análise
                                            </option>

                                            <option value="resolvido"
                                                <?php if ($row['status'] == "resolvido") echo "selected"; ?>>
                                                Resolvido
                                            </option>

                                        </select>

                                        <textarea name="resolucao"
                                                  class="form-control form-control-sm"
                                                  rows="2"
                                                  placeholder="Descreva o que foi feito para resolver esta denúncia"
                                                  style="min-width: 260px;"><?php echo htmlspecialchars($row['resolucao'] ?? ''); ?></textarea>

                                        <?php if (!empty($row['anexo'])): ?>
                                            <a href="uploads/<?php echo htmlspecialchars($row['anexo']); ?>"
                                               class="btn btn-sm btn-outline-secondary"
                                               target="_blank"
                                               rel="noopener noreferrer">Ver anexo</a>
                                        <?php endif; ?>

                                        <button type="submit" class="btn btn-sm btn-primary">Salvar status</button>

                                    </form>

                                    <?php if (isset($_SESSION['level']) && $_SESSION['level'] >= 2): ?>

                                        <form method="POST"
                                              onsubmit="return confirmarExclusao(<?php echo $row['id']; ?>);"
                                              class="d-flex flex-column gap-2 align-items-end">

                                            <input type="hidden" name="deletar_id" value="<?php echo $row['id']; ?>">
                                            <input type="text"
                                                   name="motivo"
                                                   class="form-control form-control-sm"
                                                   placeholder="Descreva o motivo da remoção"
                                                   maxlength="255"
                                                   style="min-width: 260px;"
                                                   required>

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

                if (!empty($busca)) {
                    echo '<div class="alert alert-warning">Nenhuma denúncia encontrada para "' . htmlspecialchars($busca) . '"</div>';
                } else {
                    echo '<div class="alert alert-info">Nenhuma denúncia registrada ainda.</div>';
                }

            }

            $stmt->close();
        }

        $conn->close();
        ?>

    </div>

    <footer class="mt-4 text-center">
        <p>© 2026 Denúncias Bullying</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>