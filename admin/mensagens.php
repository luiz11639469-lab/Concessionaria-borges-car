<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include("../conexao.php");

$sql = "SELECT * FROM contatos ORDER BY id DESC";

$resultado = $conn->query($sql);

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Mensagens - BorgesCar</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {

    font-family: Arial, sans-serif;

    background: #111;

    color: white;

    padding: 30px;

}

h1 {

    margin-bottom: 25px;

    font-size: 32px;

}

table {

    width: 100%;

    border-collapse: collapse;

    background: #181818;

}

th {

    background: #b30000;

    padding: 14px;

    border: 1px solid #444;

    white-space: nowrap;

}

td {

    padding: 12px;

    border: 1px solid #444;

    text-align: center;

}

tr:nth-child(even) {

    background: #222;

}

tr:hover {

    background: #2d2d2d;

}

.mensagem {

    max-width: 300px;

    min-width: 200px;

    text-align: left;

    white-space: normal;

    word-wrap: break-word;

}

.assunto {

    max-width: 200px;

    white-space: normal;

    word-wrap: break-word;

}

.voltar {

    display: inline-block;

    margin-top: 25px;

    padding: 12px 20px;

    background: #b30000;

    color: white;

    text-decoration: none;

    border-radius: 6px;

    transition: 0.3s;

}

.voltar:hover {

    background: red;

}

.sem-mensagens {

    text-align: center;

    padding: 30px;

    color: #aaa;

}

/* Responsividade */

.tabela-container {

    width: 100%;

    overflow-x: auto;

}

@media (max-width: 900px) {

    body {

        padding: 15px;

    }

    h1 {

        font-size: 25px;

    }

    table {

        min-width: 1100px;

    }

}

</style>

</head>

<body>

<h1>📩 Mensagens Recebidas</h1>


<div class="tabela-container">

<table>

<tr>

<th>ID</th>

<th>Nome</th>

<th>Email</th>

<th>Telefone</th>

<th>Assunto</th>

<th>Mensagem</th>

<th>Data</th>

</tr>


<?php

if ($resultado && $resultado->num_rows > 0) {

    while ($linha = $resultado->fetch_assoc()) {

?>

<tr>

<td>
<?= htmlspecialchars($linha['id']); ?>
</td>

<td>
<?= htmlspecialchars($linha['nome']); ?>
</td>

<td>
<?= htmlspecialchars($linha['email']); ?>
</td>

<td>
<?= htmlspecialchars($linha['telefone']); ?>
</td>

<td class="assunto">
<?= htmlspecialchars($linha['assunto']); ?>
</td>

<td class="mensagem">
<?= nl2br(htmlspecialchars($linha['mensagem'])); ?>
</td>

<td>

<?php

if (!empty($linha['data_envio'])) {

    echo date(
        "d/m/Y H:i",
        strtotime($linha['data_envio'])
    );

} else {

    echo "-";

}

?>

</td>

</tr>

<?php

    }

} else {

?>

<tr>

<td colspan="7" class="sem-mensagens">

Nenhuma mensagem recebida ainda.

</td>

</tr>

<?php

}

?>

</table>

</div>


<a href="painel.php" class="voltar">
⬅ Voltar para o Painel
</a>


</body>

</html>