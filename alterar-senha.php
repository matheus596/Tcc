<?php
session_start();
require 'proteger.php';
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha_atual = $_POST['senha_atual'];
    $nova_senha = $_POST['nova_senha'];

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        $_SESSION['erro_senha'] = 'Erro na conexão';
        header('Location: usuario.php');
        exit();
    }

    $stmt = $conn->prepare("SELECT senha FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($senha_atual, $user['senha'])) {
            if (strlen($nova_senha) >= 6) {
                $nova_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $stmt2 = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
                $stmt2->bind_param("si", $nova_hash, $_SESSION['usuario_id']);
                $stmt2->execute();
                $_SESSION['sucesso_senha'] = 'Senha alterada com sucesso!';
            } else {
                $_SESSION['erro_senha'] = 'Nova senha deve ter pelo menos 6 caracteres.';
            }
        } else {
            $_SESSION['erro_senha'] = 'Senha atual incorreta.';
        }
    }
    $conn->close();
    header('Location: usuario.php');
    exit();
}
?>