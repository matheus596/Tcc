<?php session_start(); ?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="favicon/android-chrome-512x512.png">

    <title>Denúncias Bullying</title>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <?php if (isset($_GET['cadastro']) && $_GET['cadastro'] == 'sucesso'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Cadastro realizado com sucesso! Faça o login para continuar.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Corpo -->
    <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="FotoInicio.jpg" class="d-block w-100" alt="Imagem de abertura do site">
            </div>
            <div class="carousel-item">
                <img src="carrossel1.webp" class="d-block w-100" alt="Pessoa escrevendo denúncia">
            </div>
            <div class="carousel-item">
                <img src="carrossel2.webp" class="d-block w-100" alt="Ilustração sobre combate ao bullying">
            </div>
        </div>
    </div>

    <div class="container mt-4" id="home">
        <p class="mt-3 text-center fs-2">
            A indiferença é a forma mais cruel de violência.
        </p>

        <p class="text-end" style="margin-right: 10%;">
            Zygmunt Bauman
        </p>

        <div class="home-actions d-flex justify-content-center mt-4">
            <a href="faca-sua-denuncia.php" class="btn btn-primary btn-lg py-3">Faça sua denúncia</a>
        </div>

        <div class="home-actions d-flex flex-wrap justify-content-center gap-3 mt-3">
            <a href="denuncias.php" class="btn btn-outline-primary btn-lg">Denúncias</a>
            <a href="como-funciona-o-site.php" class="btn btn-outline-primary btn-lg">Como funciona o site</a>
        </div>

        <div class="card text-center mt-4">
            <div class="card-header">
                Bullying
            </div>
            <div class="card-body">
                <h5 class="card-title">Entenda o Bullying</h5>
                <p class="card-text">Bullying é um conjunto de violências que se repetem por algum período. Geralmente, são agressões verbais, físicas e psicológicas que humilham, intimidam e traumatizam a vítima. Os danos causados pelo bullying podem ser profundos, como a depressão, distúrbios comportamentais e até o suicídio. Geralmente, acontece na escola.</p>
                <a href="https://portal.mec.gov.br/component/tags/tag/34487" class="btn btn-primary">Saiba mais</a>
            </div>
        </div>
    </div>

    <footer>
        <p>© 2026 Denúncias Bullying</p>
    </footer>

    <!-- Bootstrap 5 JS (já inclui Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

