<?php session_start(); ?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

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
                <img src="carrossel1.jfif" class="d-block w-100" alt="Pessoa escrevendo denúncia">
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

    <div class="d-grid gap-2 col-3 mx-auto">
        <button type="button" class="btn btn-primary" style="font-size: 26px; display: inline-block; text-align: center;" onclick="location.href='faca-sua-denuncia.php'">Faça sua Denuncia</button>
    </div>

    <div class="d-flex justify-content-center gap-3 mt-3">
        <button style="min-width: 150px; max-width: 250px;" type="button" class="btn" onclick="location.href='denuncias.php'">Denúncias</button>
        <button style="min-width: 150px; max-width: 250px;" type="button" class="btn" onclick="location.href='como-funciona-o-site.php'">Como funciona o site</button>
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
    <!-- Bootstrap 5 JS (já inclui Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>