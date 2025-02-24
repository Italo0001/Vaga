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

// Fechar a conexão
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="perfil_candidato.css">
    <title>Perfil - Candidato</title>
    <style>
        .btn-edit {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 15px;
            background-color: #f8c200;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .container {
            text-align: center;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
            margin: 40px auto;
            position: relative;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input,
        .form-group p {
            font-size: 18px;
        }
        
        .form-group p {
            margin: 0;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Meu Perfil</h2>
        <button class="btn-edit" onclick="window.location.href='editar_perfil.php'">Editar Perfil</button>

        <!-- Informações do candidato -->
        <div class="form-group">
            <label><strong>Nome:</strong></label>
            <p><?php echo htmlspecialchars($candidato['Nome']); ?></p>
        </div>

        <div class="form-group">
            <label><strong>Data de Nascimento:</strong></label>
            <p><?php echo htmlspecialchars($candidato['data_nascimento']); ?></p>
        </div>

        <div class="form-group">
            <label><strong>Celular:</strong></label>
            <p><?php echo htmlspecialchars($candidato['celular']); ?></p>
        </div>

        <div class="form-group">
            <label><strong>Email:</strong></label>
            <p><?php echo htmlspecialchars($candidato['email']); ?></p>
        </div>

        <div class="form-group">
            <label><strong>Função:</strong></label>
            <p><?php echo htmlspecialchars($candidato['funcao']); ?></p>
        </div>

        <div class="form-group">
            <label><strong>Situação:</strong></label>
            <p><?php echo htmlspecialchars($candidato['situacao']); ?></p>
        </div>

        <div class="form-group">
            <label><strong>PDF Atual:</strong></label>
            <p>
                <?php if (!empty($candidato['pdf'])): ?>
                    <a href="uploads/<?php echo htmlspecialchars($candidato['pdf']); ?>" target="_blank">Visualizar PDF</a>
                <?php else: ?>
                    Nenhum PDF enviado.
                <?php endif; ?>
            </p>
        </div>
    </div>
</body>
</html>
