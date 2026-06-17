<?php
session_start();
require 'config.php';

// apenas aceitar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Método inválido');
}

// 1. Conecta ao banco
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// 2. Pega os dados do formulário
$email = trim($_POST['email']);
$senha = $_POST['password'];
$confirma = $_POST['confirmPassword'];

// 3. Validações
if (empty($email) || empty($senha)) {
    $_SESSION['erro'] = 'Preencha todos os campos!';
    header('Location: cadastro.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['erro'] = 'E-mail inválido!';
    header('Location: cadastro.php');
    exit();
}

if (strlen($senha) < 6) {
    $_SESSION['erro'] = 'A senha precisa ter pelo menos 6 caracteres!';
    header('Location: cadastro.php');
    exit();
}

if ($senha !== $confirma) {
    $_SESSION['erro'] = 'As senhas não coincidem!';
    header('Location: cadastro.php');
    exit();
}

// 4. Verifica se o e-mail já existe
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $_SESSION['erro'] = 'Este e-mail já está cadastrado!';
    header('Location: cadastro.php');
    exit();
}
$stmt->close();

// 5. Gera username único
function gerarUsername($conn) {
    $base = 'user_';

    do {
        $numero = rand(1000, 9999);
        $username = $base . $numero;

        $stmt2 = $conn->prepare("SELECT id FROM usuarios WHERE username = ?");
        $stmt2->bind_param("s", $username);
        $stmt2->execute();
        $resultado = $stmt2->get_result();
        $existe = $resultado->num_rows > 0;
        $stmt2->close();
    } while ($existe);

    return $username;
}

$username = gerarUsername($conn);

// 6. Criptografa a senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// 7. Salva no banco
$stmt = $conn->prepare("
    INSERT INTO usuarios (email, senha, username) 
    VALUES (?, ?, ?)
");
$stmt->bind_param("sss", $email, $senha_hash, $username);

if ($stmt->execute()) {
    // Cadastro feito! Redireciona para o login
    header("Location: index.php?cadastro=sucesso");
    exit();
} else {
    $_SESSION['erro'] = 'Erro ao cadastrar. Tente novamente.';
    header('Location: cadastro.php');
    exit();
}

$stmt->close();
$conn->close();
?>