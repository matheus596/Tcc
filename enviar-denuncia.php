<?php
session_start();
require 'config.php';

// Verificar se usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

$erro = '';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar e validar dados
    $escola = trim($_POST['escola'] ?? '');
    $local = trim($_POST['local'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $vitima = trim($_POST['vitima'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $data = trim($_POST['data'] ?? '');
    $anonimo = isset($_POST['anonimo']) ? 1 : 0;
    
    // Validação básica
    if (empty($escola)) {
        $erro = 'Por favor, selecione uma escola!';
    } elseif (empty($local) || empty($tipo) || empty($vitima) || empty($autor)) {
        $erro = 'Por favor, preencha todos os campos obrigatórios!';
    } elseif (empty($titulo)) {
        $erro = 'Por favor, escreva um título para a denúncia!';
    } elseif (strlen($titulo) < 5) {
        $erro = 'O título deve ter pelo menos 5 caracteres!';
    } elseif (empty($descricao)) {
        $erro = 'Por favor, descreva o ocorrido!';
    } elseif (strlen($descricao) < 20) {
        $erro = 'A descrição deve ter pelo menos 20 caracteres!';
    } else {
        // Processar arquivo anexo (se houver)
        $nome_arquivo = null;
        
        if (isset($_FILES['anexo']) && $_FILES['anexo']['size'] > 0) {
            $arquivo = $_FILES['anexo'];
            $permitidos = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
            
            // Validar tipo de arquivo
            if (!in_array($arquivo['type'], $permitidos)) {
                $erro = 'Tipo de arquivo não permitido. Use imagens (JPG, PNG, GIF) ou PDF.';
            } elseif ($arquivo['size'] > 5 * 1024 * 1024) { // 5MB
                $erro = 'O arquivo é muito grande. Máximo de 5MB.';
            } else {
                // Criar pasta de uploads se não existir
                if (!file_exists('uploads')) {
                    mkdir('uploads', 0755, true);
                }
                
                // Criar nome único para o arquivo
                $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
                $nome_arquivo = 'denuncia_' . time() . '_' . uniqid() . '.' . $extensao;
                $caminho_arquivo = 'uploads/' . $nome_arquivo;
                
                // Mover arquivo para pasta de uploads
                if (!move_uploaded_file($arquivo['tmp_name'], $caminho_arquivo)) {
                    $erro = 'Erro ao fazer upload do arquivo. Tente novamente.';
                }
            }
        }
        
        // Se não há erro, salvar no banco de dados
        if (empty($erro)) {
            try {
                $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                
                if ($conn->connect_error) {
                    $erro = 'Erro na conexão com o banco de dados';
                } else {
                    // Preparar dados para inserção
                    $usuario_id = $anonimo ? NULL : $_SESSION['usuario_id'];
                    $data_criacao = date('Y-m-d H:i:s');
                    $data_ocorrido = !empty($data) ? $data : NULL;
                    
                    // SQL para inserção
                    $sql = "INSERT INTO denuncias 
                            (usuario_id, titulo, descricao, escola, local, tipo_bullying, vitima, autor, 
                             data_ocorrido, anexo, anonimo, data_criacao, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now(), 'pendente')";
                    
                    $stmt = $conn->prepare($sql);
                    
                    if ($stmt) {
                        $stmt->bind_param(
                            'issssssssssi',
                            $usuario_id,
                            $titulo,
                            $descricao,
                            $escola,
                            $local,
                            $tipo,
                            $vitima,
                            $autor,
                            $data_ocorrido,
                            $nome_arquivo,
                            $anonimo,
                            $data_criacao
                        );
                        
                        if ($stmt->execute()) {
                            $sucesso = true;
                        } else {
                            $erro = 'Erro ao registrar a denúncia. Tente novamente.';
                        }
                        $stmt->close();
                    } else {
                        $erro = 'Erro na preparação da consulta. Tente novamente.';
                    }
                    $conn->close();
                }
            } catch (Exception $e) {
                $erro = 'Erro no servidor: ' . $e->getMessage();
            }
        }
    }
    
    // Se sucesso, redirecionar
    if ($sucesso) {
        header('Location: denuncias.php?sucesso=1');
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
    <title>Resultado da Denúncia</title>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-4">
        <?php if ($sucesso): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <h4 class="alert-heading">Denúncia registrada com sucesso!</h4>
                <p>Obrigado por denunciar. Sua denúncia foi registrada e será analisada pela nossa equipe.</p>
                <a href="denuncias.php" class="btn btn-primary">Ver denúncias</a>
                <a href="faca-sua-denuncia.php" class="btn btn-secondary">Fazer outra denúncia</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php elseif (!empty($erro)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h4 class="alert-heading">Erro ao enviar denúncia</h4>
                <p><?php echo htmlspecialchars($erro); ?></p>
                <a href="faca-sua-denuncia.php" class="btn btn-primary">Voltar</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php endif; ?>
    </div>
    
    <footer class="mt-4 text-center">
        <p>© 2026 Denúncias Bullying</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
