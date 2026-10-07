<?php

include("conexao.php");

$mensagem = "";
$tipo_mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["enviar"])) {

    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $telefone = trim($_POST["telefone"]);
    $assunto = trim($_POST["assunto"]);
    $texto = trim($_POST["mensagem"]);

    // Verifica se os campos obrigatórios foram preenchidos
    if (empty($nome) || empty($email) || empty($texto)) {

        $mensagem = "Preencha todos os campos obrigatórios.";
        $tipo_mensagem = "erro";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensagem = "Digite um e-mail válido.";
        $tipo_mensagem = "erro";

    } else {

        // Inserção segura no banco de dados
        $sql = "INSERT INTO contatos 
                (nome, email, telefone, assunto, mensagem) 
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if ($stmt) {

            $stmt->bind_param(
                "sssss",
                $nome,
                $email,
                $telefone,
                $assunto,
                $texto
            );

            if ($stmt->execute()) {

                $mensagem = "Mensagem enviada com sucesso!";
                $tipo_mensagem = "sucesso";

                // Limpa os campos depois do envio
                $nome = "";
                $email = "";
                $telefone = "";
                $assunto = "";
                $texto = "";

            } else {

                $mensagem = "Erro ao enviar a mensagem.";
                $tipo_mensagem = "erro";
            }

            $stmt->close();

        } else {

            $mensagem = "Erro ao conectar com o banco de dados.";
            $tipo_mensagem = "erro";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contato - BorgesCar</title>

    <link rel="stylesheet" href="contato.css">

</head>

<body>

<header>

    <div class="logo">
        BORGES<span>CAR</span>
    </div>

    <nav>

        <a href="index.php">Início</a>

        <a href="index.php#estoque">Estoque</a>

        <a href="comprar.php">Compras</a>

        <a href="contato.php">Contato</a>

    </nav>

</header>


<section class="contato">

    <!-- INFORMAÇÕES -->

    <div class="informacoes">

        <h1>Entre em Contato</h1>

        <p>
            Estamos prontos para ajudá-lo a adquirir
            o carro dos seus sonhos.
        </p>


        <div class="card-info">

            <h3>📍 Endereço</h3>

            <p>
                Av. Brasil, 1000 - Belo Horizonte - MG
            </p>

        </div>


        <div class="card-info">

            <h3>📞 Telefone</h3>

            <p>
                (39) 99965-8178
            </p>

        </div>


        <div class="card-info">

            <h3>📧 Email</h3>

            <p>
                contato@borgescar.com
            </p>

        </div>


        <div class="card-info">

            <h3>🕒 Atendimento</h3>

            <p>
                Segunda à Sexta<br>
                08:00 às 18:00
            </p>

        </div>

    </div>


    <!-- FORMULÁRIO -->

    <div class="formulario">

        <h2>Envie uma Mensagem</h2>


        <?php if ($mensagem != ""): ?>

            <div class="<?php echo $tipo_mensagem; ?>">

                <?php echo htmlspecialchars($mensagem); ?>

            </div>

        <?php endif; ?>


        <form method="POST" action="contato.php">

            <input
                type="text"
                name="nome"
                placeholder="Nome Completo"
                value="<?php echo htmlspecialchars($nome ?? ''); ?>"
                required
            >


            <input
                type="email"
                name="email"
                placeholder="Email"
                value="<?php echo htmlspecialchars($email ?? ''); ?>"
                required
            >


            <input
                type="text"
                name="telefone"
                placeholder="Telefone"
                value="<?php echo htmlspecialchars($telefone ?? ''); ?>"
            >


            <input
                type="text"
                name="assunto"
                placeholder="Assunto"
                value="<?php echo htmlspecialchars($assunto ?? ''); ?>"
            >


            <textarea
                name="mensagem"
                placeholder="Digite sua mensagem..."
                required
            ><?php echo htmlspecialchars($texto ?? ''); ?></textarea>


            <button
                type="submit"
                name="enviar"
            >
                ENVIAR MENSAGEM
            </button>

        </form>

    </div>

</section>


<footer>

    BorgesCar © <?php echo date("Y"); ?>

</footer>

</body>

</html>