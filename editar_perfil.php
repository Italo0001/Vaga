<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['candidato_logged_in']) || $_SESSION['candidato_logged_in'] !== true) {
    header("Location: login_candidato.php"); // Redireciona para a página de login
    exit;
}

// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "candidatos";
$conn = new mysqli($servername, $username, $password, $dbname, 3306);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$candidato_id = $_SESSION['candidato_id']; // ID do candidato logado

// Buscar os dados do candidato
$sql = "SELECT * FROM usuario WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $candidato_id);
$stmt->execute();
$result = $stmt->get_result();
$candidato = $result->fetch_assoc();

// Verifica se o formulário foi enviado para editar informações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém os dados do formulário
    $Nome = $_POST['Nome'];
    $data_nascimento = $_POST['data_nascimento'];
    $celular = $_POST['celular'];

    // Atualiza as informações do candidato
    $sql = "UPDATE usuario SET Nome = ?, data_nascimento = ?, celular = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $Nome, $data_nascimento, $celular,$candidato_id);
    $stmt->execute();

    // Verifica se foi fornecida a nova senha
    if (!empty($_POST['nova_senha']) && $_POST['nova_senha'] === $_POST['confirmar_senha']) {
        $nova_senha = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT); // Criptografa a nova senha
        $sql = "UPDATE usuario SET senha = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nova_senha, $candidato_id);
        $stmt->execute();
    }

    // Redireciona após a atualização
    header("Location: perfil_candidato.php");
    exit;
}

// Fechar a conexão
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="perfil_candidato.css">
    <title>Editar Perfil - Candidato</title>
</head>
<body>
    <div class="container">
        <h2>Editar Perfil</h2>

        <!-- Formulário de Edição -->
        <form action="editar_perfil.php" method="POST">
            <div class="form-group">
                <label for="Nome">Nome</label>
                <input type="text" id="Nome" name="Nome" value="<?php echo htmlspecialchars($candidato['Nome']); ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="data_nascimento">Data de Nascimento</label>
                <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($candidato['data_nascimento']); ?>" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="data_nascimento">Celular</label>
                <input type="text" id="celular" name="celular" value="<?php echo htmlspecialchars($candidato['celular']); ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="nova_senha">Nova Senha</label>
                <input type="password" id="nova_senha" name="nova_senha" class="form-control">
            </div>

            <div class="form-group">
                <label for="confirmar_senha">Confirmar Nova Senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control">
            </div>

            <button type="submit" class="btn">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>
