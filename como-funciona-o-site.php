<?php session_start(); ?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="favicon/android-chrome-512x512.png">
    <title>Como funciona o site</title>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h1>Como funciona o site</h1>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Cadastro e login</h5>
                <p class="card-text">Para usar o site, você precisa criar uma conta com e-mail e senha. Depois de entrar no sistema, você terá acesso ao formulário de denúncia e às páginas de acompanhamento.</p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Enviar denúncia</h5>
                <p class="card-text">No formulário de denúncia, você informa a escola, o local, o tipo de bullying, a vítima, o autor e descreve o ocorrido. Também é possível anexar uma imagem ou um arquivo PDF para complementar a denúncia.</p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Contato por e-mail</h5>
                <p class="card-text">Se você marcar a opção de permitir contato por e-mail, a escola poderá usar seu endereço para solicitar mais informações. Caso não escolha essa opção, a denúncia será registrada sem esse contato adicional.</p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Análise das denúncias</h5>
                <p class="card-text">As denúncias são avaliadas pela equipe responsável. Elas passam por uma triagem para verificar os fatos, depois entram em análise, onde o responsável acompanha o caso e, quando o caso for resolvido, registra a resolução.</p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Privacidade e segurança</h5>
                <p class="card-text">Somente usuários autenticados podem enviar denúncias. Os dados de login e da denúncia são protegidos no sistema. O e-mail só aparece se você autorizar o contato. Usuários comuns não têm acesso ao seu e-mail; apenas o responsável pela escola e a administração podem visualizá-lo.</p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Níveis de acesso</h5>
                <p class="card-text">Usuários comuns podem enviar denúncias e acompanhar seu status, mas não têm acesso a informações sensíveis, como e-mails. Usuários com nível mais alto acessam a página de análise, onde podem revisar e atualizar o status das denúncias.</p>
            </div>
        </div>
    </div>

    <footer class="mt-4 text-center">
        <p>© 2026 Denúncias Bullying</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
