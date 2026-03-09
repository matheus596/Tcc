<?php session_start(); ?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="/favicon/android-chrome-512x512.png">
    <title>Login</title>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <form action="login-script.php" method="post" class="mx-auto" style="max-width:420px;">
            <h1 class="h3 mb-3">Entrar</h1>

            <div class="mb-3">
                <label for="email" class="form-label">Endereço de e-mail</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="seu@exemplo.com" aria-describedby="emailHelp" required autofocus>
                <div id="emailHelp" class="form-text">Nunca compartilharemos seu e-mail com ninguém.</div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Digite sua senha" required minlength="6">
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Lembrar-me</label>
            </div>

            <button type="submit" class="btn btn-primary">Entrar</button>
            <p class="mt-2">Não tem conta? <a href="cadastro.php">Cadastre‑se</a></p>
        </form>
    </div>

    <footer class="mt-4 text-center">
        <p>© 2026 Denúncias Bullying</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>