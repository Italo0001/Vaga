<?php
session_start();

// Verifica se já está logado, caso contrário, redireciona para a página de administração
if (isset($_SESSION['adm_logged_in']) && $_SESSION['adm_logged_in'] === true) {
    header('Location: administrador.php'); // Redireciona para a área do perfil
    exit;
}

// Verifica se foi enviado o formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['login_adm'];
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

    // Buscar o administrador no banco de dados
    $sql = "SELECT * FROM adm WHERE login_adm = ?"; // Tabela de administradores
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email); // Usando o email como parâmetro
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Verifica se o email existe no banco
    if ($result->num_rows > 0) {
        // Se o email existir, verifica a senha
        $row = $result->fetch_assoc();
        if ($senha === $row['senha']) { // Compara a senha diretamente, sem criptografia
            $_SESSION['adm_logged_in'] = true; // Sessão do administrador
            $_SESSION['adm_email'] = $row['login_adm']; // Usando o email na sessão
            header('Location: administrador.php'); // Redireciona para a área administrativa
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
    <title>Login - Administrador</title>
</head>
<body>
    <div class="container">
        <h2>Login - Administrador</h2>

        <!-- Exibindo a mensagem de erro -->
        <?php if (isset($erro)): ?>
            <div class="alert alert-danger"><?php echo $erro; ?></div>
        <?php endif; ?>

        <form action="login_adm.php" method="POST">
        
        <!-- Campo de Email -->
        <div class="form-group"  style="position: relative;">
            <input type="login_adm" id="login_adm" name="login_adm" class="form-control" placeholder="" required>
            <label for="login_adm">Email</label>
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
