<?php
// Arquivo para proteger páginas que precisam de login
// Use assim no topo da página (ANTES de qualquer HTML): require 'proteger.php';
// Presume que session_start() já foi chamado no arquivo principal

// Verificar se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Se não está logado, redirecionar para login
    header('Location: login.php');
    exit();
}?>