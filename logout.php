<?php
session_start();

// Destruir a sessão
session_destroy();

// Remover cookie se existir
if (isset($_COOKIE['usuario_email'])) {
    setcookie('usuario_email', '', time() - 3600, '/');
}

// Redirecionar para a página inicial
header('Location: index.php');
exit();?>