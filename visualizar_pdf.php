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

// Verificando se o parâmetro 'id' foi passado
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Consultando os dados binários do PDF
    $sql = "SELECT pdf FROM usuario WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($pdf);

    // Verificando se encontrou o PDF
    if ($stmt->fetch() && $pdf) {
        // Definindo os cabeçalhos corretos para mostrar o PDF no navegador
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="visualizar.pdf"');
        echo $pdf; // Exibe o PDF armazenado no banco
    } else {
        echo "Nenhum PDF encontrado.";
    }

    $stmt->close();
}

$conn->close();
?>
