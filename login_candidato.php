<?php
session_start();

// Verifica se já está logado, caso contrário, redireciona para a página de administração
if (isset($_SESSION['candidato_logged_in']) && $_SESSION['candidato_logged_in'] === true) {
    header('Location: perfil_candidato.php'); // Redireciona para a área do perfil
    exit;
}

// Verifica se foi enviado o formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Conectar ao banco de dados
    $servername = "localhost";
    $username = "root";
    $password = "123456";
    $dbname = "candidatos";
    $conn = new mysqli($servername, $username, $password, $dbname, 3306);

    // Verificar se a conexão foi bem-sucedida
    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    // Buscar o candidato no banco de dados
    $sql = "SELECT * FROM usuario WHERE email = ?"; // Tabela de candidatos
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Verifica se o email existe no banco
    if ($result->num_rows > 0) {
        // Se o email existir, verifica a senha
        $row = $result->fetch_assoc();
        if (password_verify($senha, $row['senha'])) { // Verifica a senha criptografada
            $_SESSION['candidato_logged_in'] = true;
            $_SESSION['candidato_id'] = $row['id'];
            header('Location: perfil_candidato.php'); // Redireciona para a área do perfil
            exit;
        } else {
            // Senha incorreta
            $erro = 'Senha incorreta!';
        }
    } else {
        // Email não encontrado
        $erro = 'Email não encontrado!';
    }

    // Fechar a conexão
    $conn->close();
}
?>




<!-- HTML do Formulário de Login -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Login - Candidato</title>
</head>
<body>
    <div class="container">
        <h2>Login - Candidato</h2>

        <!-- Exibindo a mensagem de erro -->
        <?php if (isset($erro)): ?>
            <div class="alert alert-danger"><?php echo $erro; ?></div>
        <?php endif; ?>

        <form action="login_candidato.php" method="POST">
        
        <!-- Campo de Email -->
        <div class="form-group"  style="position: relative;">
            <input type="email" id="email" name="email" class="form-control" placeholder="" required>
            <label for="email">Email</label>
        </div>
        
        <!-- Campo de Senha -->
        <div class="form-group"  style="position: relative;">
            <input type="password" id="senha" name="senha" class="form-control" placeholder="" required>
            <label for="senha">Senha</label>
        </div>
        
        <!-- Botão de Submit -->
        <button type="submit">Entrar</button>
    </div>
</form>


    </div>
</body>
</html>
