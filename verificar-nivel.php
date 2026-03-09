<?php

/**
 * Função para proteger páginas baseada no nível
 * @param int $nivel_minimo O nível necessário (1, 2 ou 3)
 */
function verificarAcesso($nivel_minimo) {
    // 1. Verifica se o usuário está logado
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php?erro=logue-se");
        exit();
    }

    // 2. Verifica se o nível do usuário é suficiente
    // (Assume que você salvou $_SESSION['level'] no momento do login)
    if ($_SESSION['level'] < $nivel_minimo) {
        die("Acesso Negado: Você precisa de nível $nivel_minimo para ver esta página.");
    }
}
