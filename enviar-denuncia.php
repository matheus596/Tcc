<?php
session_start();
require 'config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar se usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

$erro = '';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $escola = trim($_POST['escola'] ?? '');
    $local = trim($_POST['local'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $vitima = trim($_POST['vitima'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $data = trim($_POST['data'] ?? '');
    $anonimo = isset($_POST['anonimo']) ? 1 : 0;

    // Validação
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

        $nome_arquivo = null;

        // Upload de arquivo
        if (isset($_FILES['anexo']) && $_FILES['anexo']['size'] > 0) {

            $arquivo = $_FILES['anexo'];
            $permitidos = ['image/jpeg','image/png','image/gif','application/pdf'];

            if (!in_array($arquivo['type'], $permitidos)) {
                $erro = 'Tipo de arquivo não permitido.';
            } elseif ($arquivo['size'] > 5 * 1024 * 1024) {
                $erro = 'Arquivo muito grande. Máximo 5MB.';
            } else {

                if (!file_exists('uploads')) {
                    mkdir('uploads', 0755, true);
                }

                $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
                $nome_arquivo = 'denuncia_' . time() . '_' . uniqid() . '.' . $extensao;
                $caminho_arquivo = 'uploads/' . $nome_arquivo;

                if (!move_uploaded_file($arquivo['tmp_name'], $caminho_arquivo)) {
                    $erro = 'Erro ao enviar arquivo.';
                }
            }
        }

        if (empty($erro)) {

            try {

                $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

                if ($conn->connect_error) {
                    $erro = 'Erro na conexão com o banco';
                } else {

                    $usuario_id = $anonimo ? NULL : $_SESSION['usuario_id'];
                    $data_ocorrido = !empty($data) ? $data : NULL;

                    $sql = "INSERT INTO denuncias 
                    (usuario_id, titulo, descricao, escola, local, tipo_bullying, vitima, autor,
                     data_ocorrido, anexo, anonimo, data_criacao, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pendente')";

                    $stmt = $conn->prepare($sql);

                    if ($stmt) {

                        $stmt->bind_param(
                            "isssssssssi",
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
                            $anonimo
                        );

                        if ($stmt->execute()) {
                            $sucesso = true;
                        } else {
                            $erro = 'Erro ao registrar denúncia.';
                        }

                        $stmt->close();

                    } else {
                        $erro = 'Erro na preparação da query.';
                    }

                    $conn->close();
                }

            } catch (Exception $e) {
                $erro = 'Erro no servidor: ' . $e->getMessage();
            }
        }
    }

    if ($sucesso) {
        header('Location: denuncias.php?sucesso=1');
        exit();
    }
}
?>