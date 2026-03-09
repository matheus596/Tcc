<?php
session_start();

require 'config.php';

// Se já está logado, redireciona para a página inicial
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: index.php');
    exit();
}

$erro = '';

// Verificar se é um POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pegar dados do formulário
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['password'] ?? '';
    $lembrar = isset($_POST['remember']);

    // Validação básica
    if (empty($email) || empty($senha)) {
        $erro = 'Por favor, preencha todos os campos!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido!';
    } else {
        // Conectar ao banco de dados
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($conn->connect_error) {
            $erro = 'Erro na conexão com o banco de dados';
        } else {
            // Preparar consulta para evitar SQL injection
            $stmt = $conn->prepare("SELECT id, username, senha, level FROM usuarios WHERE email = ?");
            
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    $usuario = $result->fetch_assoc();
                    
                    // Verificar a senha
                    if (password_verify($senha, $usuario['senha'])) {
                        // Login bem-sucedido
                        $_SESSION['usuario_id'] = $usuario['id'];
                        $_SESSION['usuario_email'] = $email;
                        $_SESSION['usuario_name'] = $usuario['username'];
                        $_SESSION['loggedin'] = true;
                        $_SESSION['level'] = $usuario['level']; // Salvar level do usuário na sessão

                        // Se marcou "Lembrar-me", criar cookie por 30 dias
                        if ($lembrar) {
                            setcookie('usuario_email', $email, time() + (30 * 24 * 60 * 60), '/');
                        }

                        // Redirecionar para a página inicial
                        header('Location: index.php');
                        exit();
                    } else {
                        $erro = 'E-mail ou senha incorretos!';
                    }
                } else {
                    $erro = 'E-mail ou senha incorretos!';
                }

                $stmt->close();
            } else {
                $erro = 'Erro na consulta ao banco de dados';
            }

            $conn->close();
        }
    }
}

// Preencher email se foi salvo no cookie
$email_preenchido = $_COOKIE['usuario_email'] ?? '';