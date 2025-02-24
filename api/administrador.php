<?php
// Configuração do banco de dados
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "candidatos";
$port = 3306;

// Criando a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificando a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Exibindo mensagem, caso exista
if (isset($_GET['mensagem'])) {
    echo "<div class='alert alert-" . $_GET['tipo'] . "'>" . $_GET['mensagem'] . "</div>";
}

// Função para excluir usuário
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $sql = "DELETE FROM usuario WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: administrador.php?mensagem=Usuário excluído com sucesso!&tipo=sucesso");
    } else {
        header("Location: administrador.php?mensagem=Erro ao excluir usuário!&tipo=erro");
    }
}

// Função para atualizar a situação do usuário
if (isset($_GET['atualizar_situacao'])) {
    $id = $_GET['id'];
    $situacao = $_GET['situacao'];
    $sql = "UPDATE usuario SET situacao = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $situacao, $id);
    if ($stmt->execute()) {
        header("Location: administrador.php?&tipo=sucesso");
    } else {
        header("Location: administrador.php?mensagem=Erro ao atualizar situação!&tipo=erro");
    }
}

// Consultando todos os usuários
$sql = "SELECT * FROM usuario";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração - Usuários</title>
    <link rel="stylesheet" href="administrador.css">
</head>
<body>

<div class="container mt-5">
    <h2>Lista de Candidatos</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Data de Nascimento</th>
                <th>Função</th>
                <th>Situação</th>
                <th></th>
                <th>Currículo</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo isset($row['Nome']) ? $row['Nome'] : 'Nome não disponível'; ?></td>
                    <td><?php echo isset($row['email']) ? $row['email'] : 'Email não disponível'; ?></td>
                    <td><?php echo isset($row['data_nascimento']) ? $row['data_nascimento'] : 'Data não disponível'; ?></td>
                    <td><?php echo isset($row['funcao']) ? $row['funcao'] : 'Função não disponível'; ?></td>
                    <td>
                        <form method="get" action="administrador.php" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <select name="situacao" class="form-control form-control-sm" style="display: inline-block; width: auto;">
                                <option value="Andamento" <?php echo ($row['situacao'] == 'Andamento') ? 'selected' : ''; ?>>Andamento</option>
                                <option value="Convocado para entrevista" <?php echo ($row['situacao'] == 'Convocado para entrevista') ? 'selected' : ''; ?>>Convocado para entrevista</option>
                            </select>

                            <td class="text-right">
                                <button type="submit" name="atualizar_situacao" value="1" class="btn btn-primary btn-sm">Atualizar</button>
                            </td>

                        </form>
                    </td>

      
                    <td>
                        <?php if ($row['pdf']) { ?>
                            <a href="visualizar_pdf.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm" target="_blank">Visualizar PDF</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
// Fechar a conexão
$conn->close();
?>
