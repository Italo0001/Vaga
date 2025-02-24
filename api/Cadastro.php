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

// Variável para armazenar mensagens
$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebendo os dados do formulário
    $nome = isset($_POST['Nome']) ? $_POST['Nome'] : '';
    $email = isset($_POST['Email']) ? $_POST['Email'] : '';
    $dataDeNascimento = isset($_POST['DataDeNascimento']) ? $_POST['DataDeNascimento'] : '';
    $celular = isset($_POST['Celular']) ? $_POST['Celular'] : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
    $funcao = isset($_POST['Funcao']) ? $_POST['Funcao'] : '';

    // Prevenindo injeção de SQL
    $nome = $conn->real_escape_string($nome);
    $email = $conn->real_escape_string($email);
    $dataDeNascimento = $conn->real_escape_string($dataDeNascimento);
    $celular = $conn->real_escape_string($celular);
    $senha = $conn->real_escape_string($senha);
    $funcao = $conn->real_escape_string($funcao);

    // Verificando se os campos obrigatórios estão preenchidos
    if (empty($nome) || empty($email) || empty($dataDeNascimento) || empty($celular) || empty($senha) || empty($funcao)) {
        $mensagem = "Todos os campos devem ser preenchidos!";
        $mensagem_tipo = "erro";  // Tipo de mensagem de erro
    } else {
        // Verificando se o email já está cadastrado
        $sql_check_email = "SELECT * FROM usuario WHERE email = ?";
        $stmt = $conn->prepare($sql_check_email);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result_email = $stmt->get_result();

        if ($result_email->num_rows > 0) {
            $mensagem = "Este e-mail já está cadastrado!";
            $mensagem_tipo = "erro";  // Tipo de mensagem de erro
        } else {
            // Criptografando a senha
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            // Lidar com o upload do arquivo PDF
            if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
                $file_type = mime_content_type($_FILES['pdf']['tmp_name']);
                if ($file_type != 'application/pdf') {
                    $mensagem = "Por favor, envie um arquivo PDF válido!";
                    $mensagem_tipo = "erro";  // Tipo de mensagem de erro
                } else {
                    $pdf_data = file_get_contents($_FILES['pdf']['tmp_name']);
                }
            } else {
                $pdf_data = null;  // Caso o arquivo não tenha sido enviado
            }

            // Se não houver erro de PDF, tentamos inserir os dados no banco
            if (!isset($mensagem_tipo)) {
                // Criar query SQL para inserir os dados
                $sql = "INSERT INTO usuario (nome, email, data_nascimento, celular, senha, pdf, funcao, situacao)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

                // Inserir "Andamento" como valor da situação
                $situacao = 'Andamento';

                // Usando "s" para dados string e "b" para o arquivo binário (PDF)
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssss", $nome, $email, $dataDeNascimento, $celular, $senha_hash, $pdf_data, $funcao, $situacao);

                // Executando a query
                if ($stmt->execute()) {
                    $mensagem = "Candidatura concluída. Aguarde o retorno, boa sorte!";
                    $mensagem_tipo = "sucesso";  // Tipo de mensagem de sucesso
                } else {
                    $mensagem = "Erro ao cadastrar: " . $stmt->error;
                    $mensagem_tipo = "erro";  // Tipo de mensagem de erro
                }
            }
        }
    }
}

// Fechar a conexão
$conn->close();
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="stylesheet" href="Cadastrar.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastrar</title>
</head>
<body>
    <div class="Cadastro">
        <h3>Cadastrar</h3>
        

        <!-- Exibindo a mensagem de erro ou sucesso -->
        <?php if (isset($mensagem)): ?>
            <div class="alert alert-<?= $mensagem_tipo; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
        
        <form action="Cadastro.php" method="POST" enctype="multipart/form-data">
            <!-- Nome -->
            <div style="position: relative;">
                <input type="text" id="Nome" name="Nome" placeholder="" required>
                <label for="Nome">Nome</label>
            </div>

            <div style="position: relative;">
                <input type="funcao" id="Funcao" name="Funcao" placeholder="" required>
                <label for="Funcao">Função</label>
            </div>

            <!-- E-mail -->
            <div style="position: relative;">
                <input type="email" id="Email" name="Email" placeholder="" required>
                <label for="Email">Email</label>
            </div>

            <!-- Data de Nascimento -->
            <div style="position: relative;">
                <input type="date" id="DataDeNascimento" name="DataDeNascimento" required>
                <label for="DataDeNascimento">Data de Nascimento</label>
            </div>

            <!-- Celular -->
            <div style="position: relative;">
                <input type="tel" id="Celular" name="Celular" placeholder="" required>
                <label for="Celular">Celular</label>
            </div>

            <!-- Senha -->
            <div style="position: relative;">
                <input type="password" id="senha" name="senha" placeholder="" required>
                <label for="senha">Criar Senha</label>
            </div>



            <div style="position: relative; margin-top: 10%; margin-bottom: 0;">
                <h5>Por favor, anexe seu currículo em PDF</h5>
            </div>

            <!-- Upload de PDF -->
            <div style="position: relative;">
                <label for="pdf">Anexar Currículo:</label>
                <input type="file" name="pdf" accept="application/pdf"><br>

            </div>
       
            <!-- Botão de envio -->
            <button type="submit">Cadastrar</button>
        </form>
    </div>
</body>
</html>
