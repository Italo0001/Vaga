<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro de Login</title>
</head>
<body>

    <h3>Erro no login</h3>

    <?php
    // Exibe a mensagem de erro, se existir
    if (isset($_SESSION['error_message'])) {
        echo "<p style='color: red;'>" . $_SESSION['error_message'] . "</p>";
        unset($_SESSION['error_message']);  // Limpa a mensagem de erro após exibição
    }
    ?>

    <a href="index.php">Voltar</a> <!-- Redireciona de volta para a tela de login -->

</body>
</html>
