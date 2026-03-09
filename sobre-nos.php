<?php session_start(); ?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Plataforma para registro e informação sobre denúncias de bullying. Saiba mais sobre nossa missão e equipe.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="/favicon/android-chrome-512x512.png">
    <title>Sobre Nós — Denúncias Bullying</title>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <main role="main" aria-label="Conteúdo principal">
            <section class="mb-4">
                <h1 class="h3 mb-3 text-center">Sobre nós</h1>
                <p class="lead text-center">Somos estudantes da ETEC desenvolvendo esta plataforma para ajudar no registro e na conscientização sobre o bullying nas escolas.</p>
            </section>

            <section class="mb-4">
                <h2 class="h5">Nossa missão</h2>
                <p>Oferecer um canal seguro para registro de ocorrências e fornecer informações que auxiliem alunos, famílias e educadores a prevenir e enfrentar o bullying.</p>
            </section>

            <section class="mb-4">
                <h2 class="h5">Equipe</h2>
                <p>Projeto desenvolvido por um grupo de alunos da ETEC com foco em privacidade, usabilidade e orientação para encaminhamentos responsáveis.</p>
            </section>

            <section class="mb-4">
                <h2 class="h5">Contato</h2>
                <p>Para dúvidas ou parcerias, entre em contato: <a href="mailto:emanuelzinho@gmail.com">emanuelzinho@gmail.com</a></p>
                <p class="mt-2"><a href="faca-sua-denuncia.php" class="btn btn-primary">Fazer denúncia</a></p>
            </section>
        </main>
    </div>

    <footer class="mt-4 text-center">
        <p>© 2026 Denuncias Bullying</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>