<?php

include("conexao.php");

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Veículo não encontrado.");
}


/*
 * Busca o carro
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
 * Busca imagens
 */

$stmt = $conn->prepare(
    "SELECT imagem
     FROM imagens_carros
     WHERE carro_id = ?
     ORDER BY id ASC"
);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$resultadoImagens = $stmt->get_result();

$imagens = [];

while ($img = $resultadoImagens->fetch_assoc()) {

    $imagens[] = $img['imagem'];
}

$stmt->close();


/*
 * Opcionais
 */

$opcionais = json_decode(
    $carro['opcionais'] ?? '[]',
    true
);

if (!is_array($opcionais)) {
    $opcionais = [];
}


if (count($imagens) === 0) {

    $imagens[] = "https://via.placeholder.com/1200x800?text=BorgesCar";
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
<?= htmlspecialchars($carro['nome']); ?> | BorgesCar
</title>

<link
    rel="stylesheet"
    href="detalhes.css"
>

<link
    rel="preconnect"
    href="https://fonts.googleapis.com"
>

<link
    rel="preconnect"
    href="https://fonts.gstatic.com"
>

<link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
    rel="stylesheet"
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


<section class="banner">

<img
    src="<?= htmlspecialchars($imagens[0]); ?>"
    alt="<?= htmlspecialchars($carro['nome']); ?>"
>

<div class="overlay"></div>

<div class="texto-banner">

<h1>
<?= htmlspecialchars($carro['nome']); ?>
</h1>

<p>
Luxury Motors • BorgesCar
</p>

</div>

</section>


<section class="principal">


<div class="galeria">


<div class="imagem-principal">

    <img 
        src="<?= htmlspecialchars($imagens[0]); ?>" 
        id="fotoPrincipal"
        alt="<?= htmlspecialchars($carro['nome']); ?>"
    >

    <!-- SETA ESQUERDA -->
    <button 
        type="button"
        class="seta esquerda"
        onclick="imagemAnterior()"
    >
        &#10094;
    </button>

    <!-- SETA DIREITA -->
    <button 
        type="button"
        class="seta direita"
        onclick="proximaImagem()"
    >
        &#10095;
    </button>

</div>

</div>


<div class="informacoes">


<h1>
<?= htmlspecialchars($carro['nome']); ?>
</h1>


<div class="preco">

R$

<?= number_format(
    $carro['preco'],
    0,
    ",",
    "."
); ?>

</div>


<div class="avaliacao">

⭐⭐⭐⭐⭐ 4.9

</div>


<p class="descricao">

<?= nl2br(
    htmlspecialchars($carro['descricao'])
); ?>

</p>


<div class="botoes">


<a
    class="comprar"
    href="comprar.php?id=<?= $carro['id']; ?>"
>

COMPRAR AGORA

</a>


<a
    class="vendedor"
    href="contato.php"
>

💬 Conversar com vendedor

</a>


</div>


<div class="garantias">

<div>
✔ Veículo Periciado
</div>

<div>
✔ Garantia BorgesCar
</div>

<div>
✔ Revisões em dia
</div>

<div>
✔ Aceitamos Troca
</div>

<div>
✔ Financiamento
</div>

<div>
✔ Entrega Nacional
</div>

</div>

</div>

</section>


<section class="ficha">

<h2>
Ficha Técnica
</h2>


<table>


<tr>
<td>Marca</td>
<td>
<?= htmlspecialchars(
    explode(" ", $carro['nome'])[0]
); ?>
</td>
</tr>


<tr>
<td>Ano</td>
<td>
<?= htmlspecialchars($carro['ano']); ?>
</td>
</tr>


<tr>
<td>Motor</td>
<td>
<?= htmlspecialchars($carro['motor']); ?>
</td>
</tr>


<tr>
<td>Potência</td>
<td>
<?= htmlspecialchars($carro['potencia']); ?>
</td>
</tr>


<tr>
<td>Torque</td>
<td>
<?= htmlspecialchars($carro['torque']); ?>
</td>
</tr>


<tr>
<td>Câmbio</td>
<td>
<?= htmlspecialchars($carro['cambio']); ?>
</td>
</tr>


<tr>
<td>Tração</td>
<td>
<?= htmlspecialchars($carro['tracao']); ?>
</td>
</tr>


<tr>
<td>Combustível</td>
<td>
<?= htmlspecialchars($carro['combustivel']); ?>
</td>
</tr>


<tr>
<td>Velocidade Máxima</td>
<td>
<?= htmlspecialchars($carro['velocidade']); ?>
</td>
</tr>


<tr>
<td>0 a 100 km/h</td>
<td>
<?= htmlspecialchars($carro['zero100']); ?>
</td>
</tr>


<tr>
<td>Quilometragem</td>
<td>
<?= htmlspecialchars($carro['km']); ?>
</td>
</tr>


<tr>
<td>Cor</td>
<td>
<?= htmlspecialchars($carro['cor']); ?>
</td>
</tr>


</table>

</section>


<section class="opcionais">

<h2>
Itens de Série
</h2>


<div class="lista-opcionais">

<?php foreach ($opcionais as $item): ?>

<div class="item">

<span>✔</span>

<p>
<?= htmlspecialchars($item); ?>
</p>

</div>

<?php endforeach; ?>

</div>

</section>


<?php if (!empty($carro['video'])): ?>

<section class="video">

<h2>
Vídeo do Veículo
</h2>

<div class="video-box">

<iframe
    width="100%"
    height="500"
    src="<?= htmlspecialchars($carro['video']); ?>"
    title="Vídeo do veículo"
    frameborder="0"
    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
    allowfullscreen>
</iframe>

</div>

</section>

<?php endif; ?>


<section class="diferenciais">

<h2>
Por que comprar na BorgesCar?
</h2>


<div class="cards">


<div class="card">

<h3>
Garantia
</h3>

<p>
Todos os veículos possuem procedência e garantia.
</p>

</div>


<div class="card">

<h3>
Financiamento
</h3>

<p>
Parcelamento facilitado com as melhores taxas.
</p>

</div>


<div class="card">

<h3>
Atendimento
</h3>

<p>
Especialistas prontos para ajudar na escolha.
</p>

</div>


<div class="card">

<h3>
Entrega
</h3>

<p>
Entrega segura para todo o território nacional.
</p>

</div>


</div>

</section>


<section class="semelhantes">

<h2>
Você também pode gostar
</h2>


<div class="carros">

<?php

$sql = "SELECT
            c.*,
            (
                SELECT imagem
                FROM imagens_carros
                WHERE carro_id = c.id
                ORDER BY id ASC
                LIMIT 1
            ) AS imagem
        FROM carros c
        WHERE c.id != ?
        ORDER BY c.id DESC
        LIMIT 4";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$resultadoSemelhantes = $stmt->get_result();

?>


<?php while ($c = $resultadoSemelhantes->fetch_assoc()): ?>


<div class="carro">

<img
    src="<?= htmlspecialchars($c['imagem']); ?>"
    alt="<?= htmlspecialchars($c['nome']); ?>"
>


<h3>
<?= htmlspecialchars($c['nome']); ?>
</h3>


<p>

R$

<?= number_format(
    $c['preco'],
    0,
    ",",
    "."
); ?>

</p>


<a
    href="detalhes.php?id=<?= $c['id']; ?>"
    class="btn"
>

Ver veículo

</a>

</div>


<?php endwhile; ?>

</div>

</section>


<footer>

<div class="rodape">

<h2>
BORGES<span>CAR</span>
</h2>

<p>
Luxury Motors
</p>

<p>
Os carros mais exclusivos do Brasil.
</p>

<p>
© <?= date("Y"); ?>
BorgesCar.
Todos os direitos reservados.
</p>

</div>

</footer>


<script>

let imagens = <?= json_encode($imagens); ?>;

let imagemAtual = 0;

const fotoPrincipal = document.getElementById("fotoPrincipal");


function mostrarImagem() {

    fotoPrincipal.src = imagens[imagemAtual];

}


function proximaImagem() {

    imagemAtual++;

    if (imagemAtual >= imagens.length) {

        imagemAtual = 0;

    }

    mostrarImagem();

}


function imagemAnterior() {

    imagemAtual--;

    if (imagemAtual < 0) {

        imagemAtual = imagens.length - 1;

    }

    mostrarImagem();

}

</script>


</body>

</html>