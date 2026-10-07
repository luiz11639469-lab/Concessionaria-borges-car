```php
<?php

include("admin_protecao.php");
include("../conexao.php");


$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    die("Veículo inválido.");
}


/*
|--------------------------------------------------------------------------
| BUSCAR CARRO
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT * FROM carros WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Veículo não encontrado.");
}

$carro = $resultado->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| BUSCAR IMAGENS
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT * FROM imagens_carros
     WHERE carro_id = ?
     ORDER BY id ASC"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$resultadoImagens = $stmt->get_result();

$imagens = [];

while ($imagem = $resultadoImagens->fetch_assoc()) {
    $imagens[] = $imagem;
}

$stmt->close();


/*
|--------------------------------------------------------------------------
| DECODIFICAR OPCIONAIS
|--------------------------------------------------------------------------
*/

$opcionais = json_decode(
    $carro['opcionais'] ?? '[]',
    true
);

if (!is_array($opcionais)) {
    $opcionais = [];
}


/*
|--------------------------------------------------------------------------
| DECODIFICAR TIPOS
|--------------------------------------------------------------------------
*/

$tipos = json_decode(
    $carro['tipos'] ?? '[]',
    true
);

if (!is_array($tipos)) {
    $tipos = [];
}


/*
|--------------------------------------------------------------------------
| TIPOS DISPONÍVEIS
|--------------------------------------------------------------------------
*/

$tiposDisponiveis = [
    "Esportivo",
    "Conversível",
    "Coupé",
    "Sedã",
    "SUV",
    "Hatch",
    "Perua",
    "Picape",
    "Luxo",
    "Superesportivo",
    "Hiperesportivo",
    "Elétrico",
    "Híbrido",
    "Off-road"
];


$mensagem = "";


/*
|--------------------------------------------------------------------------
| ATUALIZAR
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST['nome'] ?? '');
    $preco = (float)($_POST['preco'] ?? 0);
    $descricao = trim($_POST['descricao'] ?? '');
    $ano = trim($_POST['ano'] ?? '');
    $motor = trim($_POST['motor'] ?? '');
    $potencia = trim($_POST['potencia'] ?? '');
    $torque = trim($_POST['torque'] ?? '');
    $cambio = trim($_POST['cambio'] ?? '');
    $tracao = trim($_POST['tracao'] ?? '');
    $combustivel = trim($_POST['combustivel'] ?? '');
    $velocidade = trim($_POST['velocidade'] ?? '');
    $zero100 = trim($_POST['zero100'] ?? '');
    $km = trim($_POST['km'] ?? '');
    $cor = trim($_POST['cor'] ?? '');
    $video = trim($_POST['video'] ?? '');


    /*
    |--------------------------------------------------------------------------
    | OPCIONAIS
    |--------------------------------------------------------------------------
    */

    $opcionaisTexto = trim(
        $_POST['opcionais'] ?? ''
    );

    $opcionaisArray = [];

    if ($opcionaisTexto !== '') {

        foreach (
            explode("\n", $opcionaisTexto)
            as $item
        ) {

            $item = trim($item);

            if ($item !== '') {
                $opcionaisArray[] = $item;
            }
        }
    }

    $opcionaisJson = json_encode(
        $opcionaisArray,
        JSON_UNESCAPED_UNICODE
    );


    /*
    |--------------------------------------------------------------------------
    | TIPOS
    |--------------------------------------------------------------------------
    */

    $tiposSelecionados = $_POST['tipos'] ?? [];

    if (!is_array($tiposSelecionados)) {
        $tiposSelecionados = [];
    }


    /*
     * Remove valores vazios
     */

    $tiposSelecionados = array_filter(
        $tiposSelecionados,
        function ($tipo) {
            return trim($tipo) !== '';
        }
    );


    /*
     * Remove duplicados
     */

    $tiposSelecionados = array_values(
        array_unique($tiposSelecionados)
    );


    /*
     * Converte para JSON
     */

    $tiposJson = json_encode(
        $tiposSelecionados,
        JSON_UNESCAPED_UNICODE
    );


    /*
    |--------------------------------------------------------------------------
    | ATUALIZAR CARRO
    |--------------------------------------------------------------------------
    */

    $sql = "UPDATE carros SET

        nome = ?,
        preco = ?,
        descricao = ?,
        ano = ?,
        motor = ?,
        potencia = ?,
        torque = ?,
        cambio = ?,
        tracao = ?,
        combustivel = ?,
        velocidade = ?,
        zero100 = ?,
        km = ?,
        cor = ?,
        opcionais = ?,
        tipos = ?,
        video = ?

        WHERE id = ?";


    $stmt = $conn->prepare($sql);


    if (!$stmt) {
        die(
            "Erro ao preparar atualização: "
            . $conn->error
        );
    }


    $stmt->bind_param(
        "sdsssssssssssssssi",

        $nome,
        $preco,
        $descricao,
        $ano,
        $motor,
        $potencia,
        $torque,
        $cambio,
        $tracao,
        $combustivel,
        $velocidade,
        $zero100,
        $km,
        $cor,
        $opcionaisJson,
        $tiposJson,
        $video,
        $id
    );


    if (!$stmt->execute()) {

        die(
            "Erro ao atualizar veículo: "
            . $stmt->error
        );
    }


    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | ADICIONAR NOVAS IMAGENS
    |--------------------------------------------------------------------------
    */

    $imagensTexto = trim(
        $_POST['imagens_novas'] ?? ''
    );


    if ($imagensTexto !== '') {

        $stmtImagem = $conn->prepare(
            "INSERT INTO imagens_carros
            (carro_id, imagem)
            VALUES (?, ?)"
        );


        foreach (
            explode("\n", $imagensTexto)
            as $imagem
        ) {

            $imagem = trim($imagem);

            if ($imagem !== '') {

                $stmtImagem->bind_param(
                    "is",
                    $id,
                    $imagem
                );

                $stmtImagem->execute();
            }
        }


        $stmtImagem->close();
    }


    /*
    |--------------------------------------------------------------------------
    | REDIRECIONAR
    |--------------------------------------------------------------------------
    */

    header(
        "Location: editar_carro.php?id="
        . $id
        . "&ok=1"
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| MENSAGEM
|--------------------------------------------------------------------------
*/

if (isset($_GET['ok'])) {

    $mensagem =
        "Veículo atualizado com sucesso!";
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
    Editar Carro | BorgesCar
</title>


<style>

* {
    box-sizing: border-box;
}


body {

    margin: 0;

    background: #0b0b0b;

    color: white;

    font-family: Arial, sans-serif;
}


.container {

    width: 90%;

    max-width: 1000px;

    margin: 40px auto;
}


.voltar {

    color: white;

    text-decoration: none;

    display: inline-block;

    margin-bottom: 20px;
}


h1 {

    margin-bottom: 30px;
}


.formulario {

    background: #151515;

    padding: 30px;

    border-radius: 10px;

    border: 1px solid #252525;
}


.campo {

    margin-bottom: 20px;
}


label {

    display: block;

    margin-bottom: 8px;

    font-weight: bold;

    color: #eeeeee;
}


input,
textarea,
select {

    width: 100%;

    padding: 13px;

    box-sizing: border-box;

    background: #0c0c0c;

    border: 1px solid #333;

    color: white;

    border-radius: 5px;

    outline: none;
}


input:focus,
textarea:focus,
select:focus {

    border-color: #d00000;
}


textarea {

    min-height: 110px;

    resize: vertical;
}


/* =========================================================
   TIPOS
========================================================= */

.tipos-container {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 12px;

    margin-top: 10px;
}


.tipo {

    position: relative;
}


.tipo input {

    position: absolute;

    opacity: 0;

    pointer-events: none;
}


.tipo label {

    display: block;

    background: #0c0c0c;

    border: 1px solid #333;

    padding: 13px;

    border-radius: 5px;

    text-align: center;

    cursor: pointer;

    font-size: 14px;

    transition: 0.2s;
}


.tipo label:hover {

    border-color: #d00000;
}


/* Quando selecionado */

.tipo input:checked + label {

    background: #700000;

    border-color: #d00000;

    color: white;
}


.explicacao {

    color: #888;

    font-size: 13px;

    margin-top: 8px;

    line-height: 1.5;
}


/* =========================================================
   BOTÃO
========================================================= */

button {

    background: #d00000;

    color: white;

    padding: 14px 25px;

    border: none;

    border-radius: 5px;

    cursor: pointer;

    font-weight: bold;

    transition: 0.2s;
}


button:hover {

    background: #f00000;

    transform: translateY(-2px);
}


/* =========================================================
   SUCESSO
========================================================= */

.sucesso {

    background: #126b32;

    padding: 15px;

    margin: 20px 0;

    border-radius: 5px;
}


/* =========================================================
   IMAGENS
========================================================= */

.imagens {

    display: grid;

    grid-template-columns:
        repeat(auto-fit, minmax(180px, 1fr));

    gap: 15px;

    margin: 20px 0;
}


.imagem {

    background: #222;

    padding: 10px;

    border-radius: 6px;
}


.imagem img {

    width: 100%;

    height: 120px;

    object-fit: cover;

    border-radius: 4px;
}


.imagem a {

    display: block;

    margin-top: 10px;

    color: #ff3333;

    text-decoration: none;
}


.imagem a:hover {

    text-decoration: underline;
}


/* =========================================================
   RESPONSIVO
========================================================= */

@media (max-width: 700px) {

    .tipos-container {

        grid-template-columns: 1fr 1fr;
    }

}


@media (max-width: 450px) {

    .tipos-container {

        grid-template-columns: 1fr;
    }

}

</style>

</head>


<body>


<div class="container">


<a
    href="carros.php"
    class="voltar"
>
    ← Voltar para estoque
</a>


<h1>

    Editar
    <?= htmlspecialchars($carro['nome']); ?>

</h1>


<?php if ($mensagem): ?>

<div class="sucesso">

    <?= htmlspecialchars($mensagem); ?>

</div>

<?php endif; ?>


<div class="formulario">


<form method="POST">


<input
    type="hidden"
    name="id"
    value="<?= $id; ?>"
>


<!-- =====================================================
     NOME
===================================================== -->

<div class="campo">

<label>
    Modelo do veículo
</label>

<input
    type="text"
    name="nome"
    value="<?= htmlspecialchars($carro['nome']); ?>"
    required
>

</div>


<!-- =====================================================
     PREÇO
===================================================== -->

<div class="campo">

<label>
    Preço
</label>

<input
    type="number"
    step="0.01"
    name="preco"
    value="<?= htmlspecialchars($carro['preco']); ?>"
    required
>

</div>


<!-- =====================================================
     ANO
===================================================== -->

<div class="campo">

<label>
    Ano
</label>

<input
    type="text"
    name="ano"
    value="<?= htmlspecialchars($carro['ano']); ?>"
>

</div>


<!-- =====================================================
     DESCRIÇÃO
===================================================== -->

<div class="campo">

<label>
    Descrição
</label>

<textarea
    name="descricao"
><?= htmlspecialchars($carro['descricao']); ?></textarea>

</div>


<!-- =====================================================
     TIPOS DO VEÍCULO
===================================================== -->

<div class="campo">

<label>
    Tipos do veículo
</label>


<div class="tipos-container">


<?php foreach ($tiposDisponiveis as $tipo): ?>


<div class="tipo">

<input
    type="checkbox"
    id="tipo_<?= md5($tipo); ?>"
    name="tipos[]"
    value="<?= htmlspecialchars($tipo); ?>"
    <?= in_array($tipo, $tipos, true)
        ? 'checked'
        : ''
    ?>
>


<label
    for="tipo_<?= md5($tipo); ?>"
>

    <?= htmlspecialchars($tipo); ?>

</label>

</div>


<?php endforeach; ?>


</div>


<p class="explicacao">

    Você pode selecionar vários tipos.
    Por exemplo: Esportivo + Coupé + Luxo.

</p>

</div>


<!-- =====================================================
     MOTOR
===================================================== -->

<div class="campo">

<label>
    Motor
</label>

<input
    type="text"
    name="motor"
    value="<?= htmlspecialchars($carro['motor']); ?>"
>

</div>


<!-- =====================================================
     POTÊNCIA
===================================================== -->

<div class="campo">

<label>
    Potência
</label>

<input
    type="text"
    name="potencia"
    value="<?= htmlspecialchars($carro['potencia']); ?>"
>

</div>


<!-- =====================================================
     TORQUE
===================================================== -->

<div class="campo">

<label>
    Torque
</label>

<input
    type="text"
    name="torque"
    value="<?= htmlspecialchars($carro['torque']); ?>"
>

</div>


<!-- =====================================================
     CÂMBIO
===================================================== -->

<div class="campo">

<label>
    Câmbio
</label>

<input
    type="text"
    name="cambio"
    value="<?= htmlspecialchars($carro['cambio']); ?>"
>

</div>


<!-- =====================================================
     TRAÇÃO
===================================================== -->

<div class="campo">

<label>
    Tração
</label>

<input
    type="text"
    name="tracao"
    value="<?= htmlspecialchars($carro['tracao']); ?>"
>

</div>


<!-- =====================================================
     COMBUSTÍVEL
===================================================== -->

<div class="campo">

<label>
    Combustível
</label>

<input
    type="text"
    name="combustivel"
    value="<?= htmlspecialchars($carro['combustivel']); ?>"
>

</div>


<!-- =====================================================
     VELOCIDADE
===================================================== -->

<div class="campo">

<label>
    Velocidade máxima
</label>

<input
    type="text"
    name="velocidade"
    value="<?= htmlspecialchars($carro['velocidade']); ?>"
>

</div>


<!-- =====================================================
     0-100
===================================================== -->

<div class="campo">

<label>
    0 a 100 km/h
</label>

<input
    type="text"
    name="zero100"
    value="<?= htmlspecialchars($carro['zero100']); ?>"
>

</div>


<!-- =====================================================
     KM
===================================================== -->

<div class="campo">

<label>
    Quilometragem
</label>

<input
    type="text"
    name="km"
    value="<?= htmlspecialchars($carro['km']); ?>"
>

</div>


<!-- =====================================================
     COR
===================================================== -->

<div class="campo">

<label>
    Cor
</label>

<input
    type="text"
    name="cor"
    value="<?= htmlspecialchars($carro['cor']); ?>"
>

</div>


<!-- =====================================================
     VÍDEO
===================================================== -->

<div class="campo">

<label>
    Vídeo
</label>

<input
    type="text"
    name="video"
    value="<?= htmlspecialchars($carro['video']); ?>"
>

</div>


<!-- =====================================================
     OPCIONAIS
===================================================== -->

<div class="campo">

<label>
    Opcionais
</label>

<textarea
    name="opcionais"
><?php

echo htmlspecialchars(
    implode("\n", $opcionais)
);

?></textarea>

<p class="explicacao">
    Coloque um opcional por linha.
</p>

</div>


<!-- =====================================================
     IMAGENS ATUAIS
===================================================== -->

<h2>
    Imagens atuais
</h2>


<div class="imagens">


<?php if (count($imagens) > 0): ?>


<?php foreach ($imagens as $imagem): ?>


<div class="imagem">


<img
    src="<?= htmlspecialchars($imagem['imagem']); ?>"
    alt="Imagem do veículo"
>


<a
    href="excluir_carro.php?imagem=<?= $imagem['id']; ?>&carro=<?= $id; ?>"
    onclick="return confirm('Excluir esta imagem?');"
>
    Excluir imagem
</a>


</div>


<?php endforeach; ?>


<?php else: ?>


<p>
    Nenhuma imagem cadastrada.
</p>


<?php endif; ?>


</div>


<!-- =====================================================
     NOVAS IMAGENS
===================================================== -->

<div class="campo">

<label>
    Adicionar novas imagens
</label>

<textarea
    name="imagens_novas"
    placeholder="Cole uma URL por linha"
></textarea>

</div>


<!-- =====================================================
     SALVAR
===================================================== -->

<button type="submit">

    SALVAR ALTERAÇÕES

</button>


</form>


</div>


</div>


</body>

</html>
```
