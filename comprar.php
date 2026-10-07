<?php

include("conexao.php");

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Veículo não encontrado.");
}


/*
 * Busca o veículo
 */

$stmt = $conn->prepare(
    "SELECT * FROM carros WHERE id = ?"
);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Veículo não encontrado.");
}

$carro = $resultado->fetch_assoc();

$stmt->close();


/*
 * Busca imagem
 */

$stmt = $conn->prepare(
    "SELECT imagem
     FROM imagens_carros
     WHERE carro_id = ?
     ORDER BY id ASC
     LIMIT 1"
);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$resultadoImagem = $stmt->get_result();

$imagem = "";

if ($resultadoImagem->num_rows > 0) {

    $imagem =
        $resultadoImagem->fetch_assoc()['imagem'];
}

$stmt->close();


/*
 * Processa compra
 */

if (isset($_POST['comprar'])) {

    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $cpf = trim($_POST['cpf']);
    $cidade = trim($_POST['cidade']);
    $pagamento = trim($_POST['pagamento']);

    /*
     * O veículo e o preço vêm do banco,
     * NÃO do formulário.
     */

    $veiculo = $carro['nome'];
    $valor = $carro['preco'];


    $sql = "INSERT INTO compras
    (
        nome_cliente,
        email,
        telefone,
        cpf,
        cidade,
        pagamento,
        veiculo,
        valor
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";


    $stmt = $conn->prepare($sql);


    $stmt->bind_param(
        "sssssssd",
        $nome,
        $email,
        $telefone,
        $cpf,
        $cidade,
        $pagamento,
        $veiculo,
        $valor
    );


    if ($stmt->execute()) {

        $compra_id = $conn->insert_id;

        header(
            "Location: sucesso.php?id=" . $compra_id
        );

        exit;

    } else {

        $erro = "Erro ao registrar a compra.";
    }


    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
BorgesCar | Finalizar Compra
</title>

<link
    rel="stylesheet"
    href="comprar.css"
>

</head>


<body>


<header>

<div class="logo">
BORGES<span>CAR</span>
</div>


<nav>

<a href="index.php">
Início
</a>

<a href="index.php#estoque">
Estoque
</a>

<a href="contato.php">
Contato
</a>

</nav>

</header>


<section class="compra">

<div class="container">


<div class="lado-esquerdo">


<?php if ($imagem): ?>

<img
    src="<?= htmlspecialchars($imagem); ?>"
    alt="<?= htmlspecialchars($carro['nome']); ?>"
>

<?php endif; ?>


<h2>
<?= htmlspecialchars($carro['nome']); ?>
</h2>


<h1>

R$

<?= number_format(
    $carro['preco'],
    2,
    ",",
    "."
); ?>

</h1>


<p>
Veículo de luxo com garantia BorgesCar.
Atendimento exclusivo e entrega em todo o Brasil.
</p>


</div>


<div class="lado-direito">


<h2>
Preencher dados
</h2>


<?php if (!empty($erro)): ?>

<div class="erro">
<?= htmlspecialchars($erro); ?>
</div>

<?php endif; ?>


<form method="POST">


<label>
Nome Completo
</label>

<input
    type="text"
    name="nome"
    required
>


<label>
Email
</label>

<input
    type="email"
    name="email"
    required
>


<label>
Telefone
</label>

<input
    type="text"
    name="telefone"
    required
>


<label>
CPF
</label>

<input
    type="text"
    name="cpf"
    required
>


<label>
Cidade
</label>

<input
    type="text"
    name="cidade"
    required
>


<label>
Veículo
</label>

<input
    type="text"
    value="<?= htmlspecialchars($carro['nome']); ?>"
    readonly
>


<label>
Valor
</label>

<input
    type="text"
    value="R$ <?= number_format(
        $carro['preco'],
        2,
        ",",
        "."
    ); ?>"
    readonly
>


<label>
Forma de Pagamento
</label>


<select name="pagamento">

<option value="PIX">
PIX
</option>

<option value="Financiamento">
Financiamento
</option>

<option value="Cartão de Crédito">
Cartão de Crédito
</option>

<option value="TED">
TED
</option>

<option value="À Vista">
À Vista
</option>

</select>


<button
    type="submit"
    name="comprar"
    class="btn-comprar"
>

FINALIZAR COMPRA

</button>


<a
    href="contato.php"
    class="botao-vendedor"
>

💬 Conversar com o Vendedor

</a>


</form>

</div>

</div>

</section>


<footer>

BorgesCar © <?= date("Y"); ?>
- Luxury Motors

</footer>


</body>

</html>