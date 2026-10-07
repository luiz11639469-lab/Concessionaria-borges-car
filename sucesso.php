<?php

include("conexao.php");

$id = $_GET['id'];

$sql = "SELECT * FROM compras WHERE id='$id'";

$resultado = $conn->query($sql);

$compra = $resultado->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<title>Compra Finalizada</title>

<style>

body{

background:#111;
font-family:Arial;
color:white;

}

.caixa{

width:700px;
margin:50px auto;
background:#1d1d1d;
padding:40px;
border-radius:15px;

}

h1{

color:#00d26a;
text-align:center;

}

p{

font-size:18px;
margin:12px 0;

}

.botao{

display:inline-block;
margin-top:30px;
padding:15px 30px;
background:#c40000;
color:white;
text-decoration:none;
border-radius:8px;

}

</style>

</head>

<body>

<div class="caixa">

<h1>✅ Compra realizada com sucesso!</h1>

<hr><br>

<p><b>Cliente:</b> <?= $compra['nome_cliente']; ?></p>

<p><b>Email:</b> <?= $compra['email']; ?></p>

<p><b>Telefone:</b> <?= $compra['telefone']; ?></p>

<p><b>CPF:</b> <?= $compra['cpf']; ?></p>

<p><b>Cidade:</b> <?= $compra['cidade']; ?></p>

<p><b>Veículo:</b> <?= $compra['veiculo']; ?></p>

<p><b>Pagamento:</b> <?= $compra['pagamento']; ?></p>

<p><b>Valor:</b>

R$ <?= number_format($compra['valor'],2,",","."); ?>

</p>

<p><b>Data:</b>

<?= date("d/m/Y H:i",strtotime($compra['data_compra'])); ?>

</p>

<a class="botao" href="index.php">

Voltar ao Site

</a>

</div>

</body>

</html>