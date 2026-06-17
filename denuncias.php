<?php
session_start();
require 'config.php';

$nivel = $_SESSION['level'] ?? 1;
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
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="container mt-4">

    <h1>Denúncias Registradas</h1>

    <!-- Formulário de Busca -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-9">
                    <label for="busca" class="form-label">Pesquisar denúncias</label>
                    <input type="text" class="form-control" id="busca" name="busca" placeholder="Digite título, descrição ou escola..." value="<?php echo htmlspecialchars($_GET['busca'] ?? ''); ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">Pesquisar</button>
                    <a href="denuncias.php" class="btn btn-secondary">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <?php

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {

        echo '<div class="alert alert-danger">Erro na conexão com o banco de dados</div>';

    } else {

        // Pegar termo de busca se existir
        $busca = trim($_GET['busca'] ?? '');
        $busca_param = "%$busca%";

        // Montar query com filtro opcional
        $sql = "
            SELECT 
                d.titulo,
                d.descricao,
                d.escola,
                d.local,
                d.tipo_bullying,
                d.vitima,
                d.autor,
                d.data_criacao,
                d.data_ocorrido,
                d.status,
                d.resolucao,
                d.usuario_id,
                d.anexo,
                d.permitir_contato,
                u.username,
                u.email
            FROM denuncias d
            LEFT JOIN usuarios u ON d.usuario_id = u.id
            WHERE 1=1";

        // Usuários de nível 1 veem apenas denúncias em análise ou resolvidas
        if ($nivel < 2) {
            $sql .= " AND d.status IN ('analise', 'resolvido')";
        }

        if (!empty($busca)) {
            $sql .= " AND (d.titulo LIKE ? OR d.descricao LIKE ? OR d.escola LIKE ?)";
        }

        $sql .= " ORDER BY d.data_criacao DESC";

        $stmt = $conn->prepare($sql);

        if (!empty($busca)) {
            $stmt->bind_param("sss", $busca_param, $busca_param, $busca_param);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            if (!empty($busca)) {
                echo '<div class="alert alert-info">Encontrados ' . $result->num_rows . ' resultado(s) para "' . htmlspecialchars($busca) . '"</div>';
            }

            while ($row = $result->fetch_assoc()) {
    ?>

                <div class="card mb-4">

                    <div class="card-header">
                        <h5 class="card-title">
                            <?php echo htmlspecialchars($row['titulo']); ?>
                        </h5>
                    </div>

                    <div class="card-body">

                        <p class="card-text">
                            <?php echo htmlspecialchars($row['descricao']); ?>
                        </p>

                        <div class="row">

                            <div class="col-md-6">
                                <?php if ($nivel >= 2): ?>
                                <strong>Escola:</strong> <?php echo htmlspecialchars($row['escola']); ?><br>
                                <?php endif; ?>
                                <strong>Local:</strong> <?php echo htmlspecialchars($row['local']); ?><br>
                                <strong>Tipo:</strong> <?php echo htmlspecialchars($row['tipo_bullying']); ?>
                            </div>

                            <div class="col-md-6">
                                <strong>Vítima:</strong> <?php echo htmlspecialchars($row['vitima']); ?><br>
                                <strong>Autor:</strong> <?php echo htmlspecialchars($row['autor']); ?><br>
                                <strong>Data do ocorrido:</strong> <?php echo !empty($row['data_ocorrido']) ? date('d/m/Y', strtotime($row['data_ocorrido'])) : 'Não informada'; ?><br>
                                <strong>Criada em:</strong> <?php echo date('d/m/Y H:i', strtotime($row['data_criacao'])); ?><br>
                                <strong>Status:</strong>
                                <?php
                                    $status_labels = [
                                        'triagem'   => ['label' => 'Em triagem', 'badge' => 'bg-warning text-dark'],
                                        'analise'   => ['label' => 'Em análise', 'badge' => 'bg-info text-dark'],
                                        'resolvido' => ['label' => 'Resolvido',  'badge' => 'bg-success'],
                                    ];
                                    $s = $row['status'];
                                    if (isset($status_labels[$s])):
                                ?>
                                    <span class="badge <?php echo $status_labels[$s]['badge']; ?>">
                                        <?php echo $status_labels[$s]['label']; ?>
                                    </span>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($s); ?>
                                <?php endif; ?>

                                <?php if ($row['status'] === 'resolvido' && !empty($row['resolucao'])): ?>
                                    <br><strong>O que foi feito:</strong> <?php echo htmlspecialchars($row['resolucao']); ?>
                                <?php endif; ?>
                            </div>

                        </div>

                        <?php if ($nivel >= 2): ?>
                            <p class="text-muted mt-2">
                                Usuário: <?php echo htmlspecialchars($row['username']); ?>
                                <?php if ($row['permitir_contato'] == 1): ?>
                                    <br><span class="badge bg-success">Permite contato</span>
                                    <br><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a>
                                <?php else: ?>
                                    <br><span class="badge bg-secondary">Não permite contato</span>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>

                        <?php
                        if (!empty($row['anexo'])) {

                            $ext = strtolower(pathinfo($row['anexo'], PATHINFO_EXTENSION));
                        ?>

                            <div class="mt-3">

                                <?php if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>

                                    <img
                                        src="uploads/<?php echo htmlspecialchars($row['anexo']); ?>"
                                        class="img-fluid rounded"
                                        style="max-width:400px;"
                                    >

                                <?php elseif ($ext === 'pdf'): ?>

                                    <a
                                        class="btn btn-primary mt-2"
                                        target="_blank"
                                        href="uploads/<?php echo htmlspecialchars($row['anexo']); ?>"
                                    >
                                        Ver PDF
                                    </a>

                                <?php endif; ?>

                            </div>

                        <?php } ?>

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