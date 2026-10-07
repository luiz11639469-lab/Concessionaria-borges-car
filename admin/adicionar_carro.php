<?php

session_start();

include("../conexao.php");

$mensagem = "";
$tipo = "";


/*
|--------------------------------------------------------------------------
| CONVERTER LINK DO YOUTUBE
|--------------------------------------------------------------------------
*/

function converterYoutube($url)
{
    $url = trim($url);

    if (empty($url)) {
        return "";
    }

    $videoId = "";

    /*
     * Formato:
     * youtube.com/watch?v=ID
     */

    if (
        preg_match(
            '/youtube\.com\/watch\?v=([^&]+)/',
            $url,
            $matches
        )
    ) {

        $videoId = $matches[1];

    }

    /*
     * Formato:
     * youtu.be/ID
     */

    elseif (
        preg_match(
            '/youtu\.be\/([^?]+)/',
            $url,
            $matches
        )
    ) {

        $videoId = $matches[1];

    }

    /*
     * Formato:
     * youtube.com/shorts/ID
     */

    elseif (
        preg_match(
            '/youtube\.com\/shorts\/([^?]+)/',
            $url,
            $matches
        )
    ) {

        $videoId = $matches[1];

    }

    /*
     * Formato já convertido:
     * youtube.com/embed/ID
     */

    elseif (
        preg_match(
            '/youtube\.com\/embed\/([^?]+)/',
            $url,
            $matches
        )
    ) {

        $videoId = $matches[1];

    }


    if (!empty($videoId)) {

        return "https://www.youtube.com/embed/" . $videoId;

    }


    return "";
}


/*
|--------------------------------------------------------------------------
| CADASTRAR CARRO
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $preco = floatval($_POST["preco"] ?? 0);
    $descricao = trim($_POST["descricao"] ?? "");

    /*
    |--------------------------------------------------------------------------
    | CATEGORIA
    |--------------------------------------------------------------------------
    */

    $categoria = trim($_POST["categoria"] ?? "");

    $ano = trim($_POST["ano"] ?? "");
    $motor = trim($_POST["motor"] ?? "");
    $potencia = trim($_POST["potencia"] ?? "");
    $torque = trim($_POST["torque"] ?? "");
    $cambio = trim($_POST["cambio"] ?? "");
    $tracao = trim($_POST["tracao"] ?? "");
    $combustivel = trim($_POST["combustivel"] ?? "");
    $velocidade = trim($_POST["velocidade"] ?? "");
    $zero100 = trim($_POST["zero100"] ?? "");
    $km = trim($_POST["km"] ?? "");
    $cor = trim($_POST["cor"] ?? "");
    $categoria = $_POST['categoria'];

    $videoOriginal = trim($_POST["video"] ?? "");

    /*
    |--------------------------------------------------------------------------
    | CONVERTE AUTOMATICAMENTE O YOUTUBE
    |--------------------------------------------------------------------------
    */

    $video = converterYoutube($videoOriginal);


    /*
    |--------------------------------------------------------------------------
    | OPCIONAIS
    |--------------------------------------------------------------------------
    */

    $opcionaisTexto = trim($_POST["opcionais"] ?? "");

    $opcionais = [];

    if (!empty($opcionaisTexto)) {

        $linhas = explode("\n", $opcionaisTexto);

        foreach ($linhas as $linha) {

            $linha = trim($linha);

            if (!empty($linha)) {

                $opcionais[] = $linha;

            }

        }

    }

    $opcionaisJSON = json_encode(
        $opcionais,
        JSON_UNESCAPED_UNICODE
    );


    /*
    |--------------------------------------------------------------------------
    | VALIDAÇÃO
    |--------------------------------------------------------------------------
    */

    if (
        empty($nome) ||
        $preco <= 0 ||
        empty($descricao) ||
        empty($categoria)
    ) {

        $mensagem = "Preencha os campos obrigatórios.";
        $tipo = "erro";

    }

    elseif (
        !empty($videoOriginal) &&
        empty($video)
    ) {

        $mensagem = "O link do YouTube informado é inválido.";
        $tipo = "erro";

    }

    else {

        /*
        |--------------------------------------------------------------------------
        | INSERIR CARRO
        |--------------------------------------------------------------------------
        */

        $sql = "INSERT INTO carros
        (
            nome,
            preco,
            descricao,
            categoria,
            ano,
            motor,
            potencia,
            torque,
            cambio,
            tracao,
            combustivel,
            velocidade,
            zero100,
            km,
            cor,
            opcionais,
            video
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


        $stmt = $conn->prepare($sql);


        if (!$stmt) {

            die(
                "Erro no banco de dados: " .
                $conn->error
            );

        }


        $stmt->bind_param(
            "sdsssssssssssssss",
            $nome,
            $preco,
            $descricao,
            $categoria,
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
            $opcionaisJSON,
            $video
        );


        if ($stmt->execute()) {

            $carro_id = $stmt->insert_id;

            $stmt->close();


            /*
            |--------------------------------------------------------------------------
            | SALVAR IMAGENS
            |--------------------------------------------------------------------------
            */

            if (
                isset($_POST["imagens"]) &&
                is_array($_POST["imagens"])
            ) {

                foreach (
                    $_POST["imagens"] as $imagem
                ) {

                    $imagem = trim($imagem);

                    if (!empty($imagem)) {

                        $stmtImagem = $conn->prepare(
                            "INSERT INTO imagens_carros
                            (carro_id, imagem)
                            VALUES (?, ?)"
                        );


                        $stmtImagem->bind_param(
                            "is",
                            $carro_id,
                            $imagem
                        );


                        $stmtImagem->execute();

                        $stmtImagem->close();

                    }

                }

            }


            /*
            |--------------------------------------------------------------------------
            | REDIRECIONAR
            |--------------------------------------------------------------------------
            */

            header(
                "Location: carros.php?sucesso=1"
            );

            exit;

        } else {

            $mensagem =
                "Erro ao cadastrar o veículo: " .
                $stmt->error;

            $tipo = "erro";

            $stmt->close();

        }

    }

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

<title>Adicionar Carro | BorgesCar</title>

<style>

* {
    box-sizing: border-box;
}


body {

    margin: 0;

    font-family: Arial, sans-serif;

    background: #111;

    color: white;

}


.container {

    width: 90%;

    max-width: 1100px;

    margin: 40px auto;

}


h1 {

    color: #fff;

    margin-bottom: 30px;

}


.formulario {

    background: #1b1b1b;

    padding: 30px;

    border-radius: 12px;

}


.grupo {

    margin-bottom: 20px;

}


.grupo label {

    display: block;

    margin-bottom: 8px;

    font-weight: bold;

}


input,
textarea,
select {

    width: 100%;

    padding: 13px;

    border-radius: 6px;

    border: 1px solid #444;

    background: #292929;

    color: white;

    font-size: 15px;

}


textarea {

    min-height: 120px;

    resize: vertical;

}


.duas-colunas {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 20px;

}


.botao {

    display: inline-block;

    padding: 14px 25px;

    background: #c40000;

    color: white;

    border: none;

    border-radius: 6px;

    cursor: pointer;

    font-weight: bold;

    margin-top: 10px;

}


.botao:hover {

    background: #e00000;

}


.voltar {

    display: inline-block;

    margin-bottom: 25px;

    color: #ccc;

    text-decoration: none;

}


.voltar:hover {

    color: white;

}


.sucesso {

    padding: 15px;

    background: #164d25;

    border-radius: 6px;

    margin-bottom: 20px;

}


.erro {

    padding: 15px;

    background: #641d1d;

    border-radius: 6px;

    margin-bottom: 20px;

}


.explicacao {

    margin-top: 8px;

    font-size: 13px;

    color: #aaa;

}


.imagem {

    margin-bottom: 10px;

}


/*
|--------------------------------------------------------------------------
| CATEGORIA
|--------------------------------------------------------------------------
*/

.categoria-select {

    border: 1px solid #555;

    cursor: pointer;

}


.categoria-select option {

    background: #292929;

    color: white;

}


.categoria-info {

    margin-top: 8px;

    font-size: 13px;

    color: #aaa;

}


@media(max-width: 700px) {

    .duas-colunas {

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
    ← Voltar para Gerenciar Carros
</a>


<h1>
    🚗 Adicionar Novo Carro
</h1>


<?php if (!empty($mensagem)): ?>

<div class="<?= $tipo ?>">

    <?= htmlspecialchars($mensagem) ?>

</div>

<?php endif; ?>


<div class="formulario">


<form method="POST">


<!-- NOME -->

<div class="grupo">

<label>
    Nome do veículo
</label>

<input
    type="text"
    name="nome"
    required
>

</div>


<!-- PREÇO -->

<div class="grupo">

<label>
    Preço
</label>

<input
    type="number"
    name="preco"
    step="0.01"
    required
>

</div>


<label>Tipo de carro</label>

<select name="categoria" required>

    <option value="">Selecione o tipo</option>

    <option value="Conversível">Conversível</option>

    <option value="Sedã">Sedã</option>

    <option value="Esportivo">Esportivo</option>

    <option value="Picape">Picape</option>

    <option value="SUV">SUV</option>

    <option value="Hatch">Hatch</option>

    <option value="Cupê">Cupê</option>

    <option value="Perua">Perua</option>

    <option value="Minivan">Minivan</option>

    <option value="Superesportivo">Superesportivo</option>

    <option value="Elétrico">Elétrico</option>

    <option value="Híbrido">Híbrido</option>

    <option value="Outros">Outros</option>

</select>

<!-- DADOS DO VEÍCULO -->

<div class="duas-colunas">


<div class="grupo">

<label>
    Ano
</label>

<input
    type="text"
    name="ano"
>

</div>


<div class="grupo">

<label>
    Quilometragem
</label>

<input
    type="text"
    name="km"
>

</div>


<div class="grupo">

<label>
    Motor
</label>

<input
    type="text"
    name="motor"
>

</div>


<div class="grupo">

<label>
    Potência
</label>

<input
    type="text"
    name="potencia"
>

</div>


<div class="grupo">

<label>
    Torque
</label>

<input
    type="text"
    name="torque"
>

</div>


<div class="grupo">

<label>
    Câmbio
</label>

<input
    type="text"
    name="cambio"
>

</div>


<div class="grupo">

<label>
    Tração
</label>

<input
    type="text"
    name="tracao"
>

</div>


<div class="grupo">

<label>
    Combustível
</label>

<input
    type="text"
    name="combustivel"
>

</div>


<div class="grupo">

<label>
    Velocidade máxima
</label>

<input
    type="text"
    name="velocidade"
>

</div>


<div class="grupo">

<label>
    0 a 100 km/h
</label>

<input
    type="text"
    name="zero100"
>

</div>


<div class="grupo">

<label>
    Cor
</label>

<input
    type="text"
    name="cor"
>

</div>


</div>


<!-- DESCRIÇÃO -->

<div class="grupo">

<label>
    Descrição
</label>

<textarea
    name="descricao"
    required
></textarea>

</div>


<!-- OPCIONAIS -->

<div class="grupo">

<label>
    Opcionais
</label>

<textarea
    name="opcionais"
    placeholder="Um opcional por linha&#10;Bancos esportivos&#10;Freios de carbono&#10;Som premium"
></textarea>

</div>


<!-- VÍDEO -->

<div class="grupo">

<label>
    🎥 Vídeo do YouTube
</label>

<input
    type="text"
    name="video"
    placeholder="Cole aqui o link do YouTube"
>

<div class="explicacao">

    Você pode colar o link normal, por exemplo:

    <br><br>

    https://www.youtube.com/watch?v=ABC123

    <br>

    ou

    <br>

    https://youtu.be/ABC123

    <br><br>

    O sistema converterá automaticamente para o formato do vídeo.

</div>

</div>


<!-- IMAGENS -->

<div class="grupo">

<label>
    📷 Imagens do veículo
</label>


<input
    type="text"
    name="imagens[]"
    class="imagem"
    placeholder="URL da imagem 1"
>


<input
    type="text"
    name="imagens[]"
    class="imagem"
    placeholder="URL da imagem 2"
>


<input
    type="text"
    name="imagens[]"
    class="imagem"
    placeholder="URL da imagem 3"
>


<input
    type="text"
    name="imagens[]"
    class="imagem"
    placeholder="URL da imagem 4"
>


<input
    type="text"
    name="imagens[]"
    class="imagem"
    placeholder="URL da imagem 5"
>
<input
    type="text"
    name="imagens[]"
    class="imagem"
    placeholder="URL da imagem 6"
>

<input
    type="text"
    name="imagens[]"
    class="imagem"
    placeholder="URL da imagem 7"
>
<input
    type="text"
    name="imagens[]"
    class="imagem"
    placeholder="URL da imagem 8"
>
<input
    type="text"
    name="imagens[]"
    class="imagem"
    placeholder="URL da imagem 9"
>


<input
    type="text"
    name="imagens[]"
    class="imagem"
    placeholder="URL da imagem 10"
>


<input
    type="text"
    name="imagens[]"
    class="imagem"
    placeholder="URL da imagem 11"
>

<input
    type="text"
    name="imagens[]"
    class="imagem"
    placeholder="URL da imagem 12"
>

<input
    type="text"
    name="imagens[]"
    class="imagem"
    placeholder="URL da imagem 13"
>

<input
    type="text"
    name="imagens[]"
    class="imagem"
    placeholder="URL da imagem 14"
>

<input
    type="text"
    name="imagens[]"
    class="imagem"
    placeholder="URL da imagem 15"
>




<div class="explicacao">

   

</div>

</div>


<!-- BOTÃO -->

<button
    type="submit"
    class="botao"
>

    CADASTRAR CARRO

</button>


</form>

</div>

</div>


</body>

</html>