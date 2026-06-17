<?php
session_start();
require 'proteger.php';
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $denuncia_id = intval($_POST['id']);
    $usuario_id = $_SESSION['usuario_id'];

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        $_SESSION['mensagem_denuncia'] = "Erro na conexão com o banco de dados.";
    } else {
        // Verifica se a denúncia pertence ao usuário logado antes de deletar
        $stmt = $conn->prepare("DELETE FROM denuncias WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("ii", $denuncia_id, $usuario_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['mensagem_denuncia'] = "Denúncia excluída com sucesso!";
            } else {
                $_SESSION['mensagem_denuncia'] = "Erro: Denúncia não encontrada ou você não tem permissão para excluí-la.";
            }
        } else {
            $_SESSION['mensagem_denuncia'] = "Erro ao tentar excluir a denúncia.";
        }

        $stmt->close();
        $conn->close();
    }
} else {
    $_SESSION['mensagem_denuncia'] = "Requisição inválida.";
}

// Redireciona de volta para usuario.php
header("Location: usuario.php");
exit();
?>
