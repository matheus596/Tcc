<?php
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">Início</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav">
                <a class="nav-link" href="faca-sua-denuncia.php">Faça sua denúncia</a>
                <a class="nav-link" href="denuncias.php">Denúncias</a>
                <a class="nav-link" href="como-funciona-o-site.php">Como o site funciona</a>
                <a class="nav-link" href="sobre-nos.php">Sobre nós</a>
                <?php if (isset($_SESSION['level']) && $_SESSION['level'] >= 2): ?>
                    <a class="nav-link" href="verificar-denuncias.php">Analisar Denúncias</a>
                <?php endif; ?>
                <a class="nav-link" href="notificacoes.php">Notificações</a>
                <a class="nav-link" href="usuario.php">Minha conta</a>
                <a class="nav-link" href="logout.php">Sair</a>
            </div>
        </div>
    </div>
</nav>
<?php
} else {
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">Início</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav">
                <a class="nav-link" href="faca-sua-denuncia.php">Faça sua denúncia</a>
                <a class="nav-link" href="denuncias.php">Denúncias</a>
                <a class="nav-link" href="como-funciona-o-site.php">Como o site funciona</a>
                <a class="nav-link" href="sobre-nos.php">Sobre nós</a>
                <a class="nav-link" href="login.php">Login</a>
                <a class="nav-link" href="cadastro.php">Cadastro</a>
            </div>
        </div>
    </div>
</nav>
<?php
}
?>