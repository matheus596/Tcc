<?php 
session_start();
require 'proteger.php';
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="favicon/android-chrome-512x512.png">
    <title>Faça Sua Denúncia</title>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <form action="enviar-denuncia.php" method="post" enctype="multipart/form-data" class="mx-auto" style="max-width:820px;">
            <h1 class="h3 mb-3 text-center">Faça sua denúncia</h1>
            <p class="text-center text-muted">Preencha os campos abaixo com o máximo de detalhes possível. Você pode optar por enviar anonimamente. Não coloque nomes caso permita que o responsável pela escola entre em contato para saber mais detalhes. A denúncia só aparecerá depois de uma análise prévia.</p>

            <div class="row g-3">

                <div class="col-md-6">
                    <label for="escola" class="form-label">Qual escola</label>
                    <select id="escola" name="escola" class="form-select" required>
                        <option value="" disabled selected>Escolha...</option>
                        <option value="ANTONIO FONTANA">ANTONIO FONTANA</option>
                        <option value="CLOTILDE DE CASTRO BARREIRA PROFA">CLOTILDE DE CASTRO BARREIRA PROFA</option>
                        <option value="EMEI VALTER APARECIDO FRANCISCANI">EMEI VALTER APARECIDO FRANCISCANI</option>
                        <option value="HELENA PUPIM ALBANEZ E M">HELENA PUPIM ALBANEZ E M</option>
                        <option value="JARDIM SAO FRANCISCO">JARDIM SAO FRANCISCO</option>
                        <option value="JOAO E MARIA EMEI">JOAO E MARIA EMEI</option>
                        <option value="JOAO LEAO DE CARVALHO EM">JOAO LEAO DE CARVALHO EM</option>
                        <option value="JOSE AUGUSTO DE CARVALHO DR">JOSE AUGUSTO DE CARVALHO DR</option>
                        <option value="JOSE DOS SANTOS ALMEIDA">JOSE DOS SANTOS ALMEIDA</option>
                        <option value="LEONILDA PEREIRA DE ALMEIDA EMEI">LEONILDA PEREIRA DE ALMEIDA EMEI</option>
                        <option value="LUIZ PIRES BARBOSA PROF ETEC">LUIZ PIRES BARBOSA PROF ETEC</option>
                        <option value="MARIA DOMENICA MORINO IRMA CRECHE">MARIA DOMENICA MORINO IRMA CRECHE</option>
                        <option value="MARIA PAGOTE CONTE ESCOLA DE EDUCACAO ESPECIAL">MARIA PAGOTE CONTE ESCOLA DE EDUCACAO ESPECIAL</option>
                        <option value="MENINO JESUS CRECHE">MENINO JESUS CRECHE</option>
                        <option value="NOSSA SENHORA DAS DORES CASA DA CRIANCA">NOSSA SENHORA DAS DORES CASA DA CRIANCA</option>
                        <option value="OLGA BREVE ALVES PROFA EM">OLGA BREVE ALVES PROFA EM</option>
                        <option value="PRIMEIROS PASSOS JARDIM DA INFANCIA">PRIMEIROS PASSOS JARDIM DA INFANCIA</option>
                        <option value="RACHID JABUR">RACHID JABUR</option>
                        <option value="SANTA CLARA COLEGIO">SANTA CLARA COLEGIO</option>
                        <option value="SANTO HINO">SANTO HINO</option>
                        <option value="SANTOS ANJOS COLEGIO">SANTOS ANJOS COLEGIO</option>
                    </select>
                </div>


                <div class="col-md-6">
                    <label for="local" class="form-label">Onde aconteceu</label>
                    <select id="local" name="local" class="form-select" required>
                        <option value="" disabled selected>Escolha...</option>
                        <option value="pátio">Pátio da escola</option>
                        <option value="sala">Sala de aula</option>
                        <option value="fora">Fora da escola</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="tipo" class="form-label">Tipo de bullying</label>
                    <select id="tipo" name="tipo" class="form-select" required>
                        <option value="" disabled selected>Escolha...</option>
                        <option value="verbal">Verbal</option>
                        <option value="fisico">Físico</option>
                        <option value="social">Social/isolamento</option>
                        <option value="digital">Digital</option>
                        <option value="psicologico">Psicológico</option>
                        <option value="humilhacao">Humilhação pública</option>
                        <option value="racismo">Racismo/xenofobia</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="vitima" class="form-label">Quem foi a vítima</label>
                    <select id="vitima" name="vitima" class="form-select" required>
                        <option value="" disabled selected>Escolha...</option>
                        <option value="aluno">Aluno</option>
                        <option value="professor">Professor</option>
                        <option value="funcionario">Funcionário</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="autor" class="form-label">Quem praticou</label>
                    <select id="autor" name="autor" class="form-select" required>
                        <option value="" disabled selected>Escolha...</option>
                        <option value="aluno">Aluno</option>
                        <option value="professor">Professor</option>
                        <option value="funcionario">Funcionário</option>
                    </select>
                </div>

                <div class="col-12">
                    <label for="titulo" class="form-label">Título (resumo)</label>
                    <input type="text" id="titulo" name="titulo" class="form-control" placeholder="Resumo do ocorrido" required maxlength="100">
                </div>

                <div class="col-12">
                    <label for="descricao" class="form-label">Descreva o ocorrido</label>
                    <textarea id="descricao" name="descricao" class="form-control" rows="6" required minlength="20" aria-describedby="descricaoHelp"></textarea>
                    <div id="descricaoHelp" class="form-text">Detalhe o que ocorreu, quando e quem estava presente.</div>
                </div>

                <div class="col-md-6">
                    <label for="data" class="form-label">Data do ocorrido (se souber)</label>
                    <input type="date" id="data" name="data" class="form-control">
                </div>

                <div class="col-md-6">
                    <label for="anexo" class="form-label">Anexos (opcional)</label>
                    <input class="form-control" type="file" id="anexo" name="anexo" accept="image/*,application/pdf">
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="permitir_contato" name="permitir_contato" value="1">
                        <label class="form-check-label" for="permitir_contato">Permito que a escola entre em contato comigo por e-mail para mais informações.</label>
                    </div>
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end">
                    <button type="reset" class="btn btn-secondary">Limpar</button>
                    <button type="submit" class="btn btn-primary">Enviar denúncia</button>
                </div>
            </div>
        </form>
    </div>

    <footer class="mt-4 text-center">
        <p>© 2026 Denúncias Bullying</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>