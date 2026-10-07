<?php

session_start();

if(!isset($_SESSION['admin'])){
    header("Location:login.php");
    exit;
}

include("../conexao.php");

$sql = "SELECT * FROM compras ORDER BY id DESC";

$resultado = $conn->query($sql);

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

<meta charset="UTF-8">

<title>Compras</title>

<style>

body{

font-family:Arial;
background:#111;
color:white;

}

table{

width:100%;
border-collapse:collapse;

}

th{

background:#b30000;
padding:12px;

}

td{

padding:10px;
border:1px solid #444;
text-align:center;

}

tr:nth-child(even){

background:#222;

}

a{

color:white;

}

</style>

</head>

<body>

<h1>Compras Realizadas</h1>

<table>

<tr>

<th>ID</th>
<th>Cliente</th>
<th>Email</th>
<th>Telefone</th>
<th>CPF</th>
<th>Cidade</th>
<th>Veículo</th>
<th>Valor</th>
<th>Pagamento</th>
<th>Data</th>

</tr>

<?php

while($linha = $resultado->fetch_assoc()){

?>

<tr>

<td><?= $linha['id']; ?></td>

<td><?= $linha['nome_cliente']; ?></td>

<td><?= $linha['email']; ?></td>

<td><?= $linha['telefone']; ?></td>

<td><?= $linha['cpf']; ?></td>

<td><?= $linha['cidade']; ?></td>

<td><?= $linha['veiculo']; ?></td>

<td>R$ <?= number_format($linha['valor'],2,",","."); ?></td>

<td><?= $linha['pagamento']; ?></td>

<td><?= date("d/m/Y H:i",strtotime($linha['data_compra'])); ?></td>

</tr>

<?php } ?>

</table>

<br>

<a href="painel.php">⬅ Voltar</a>

</body>

</html>