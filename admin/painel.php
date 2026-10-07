<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo</title>

    <link rel="stylesheet" href="painel.css">
</head>

<body>

    <div class="container">

    <div class="topo">

        <h1>
            BORGES<span>CAR</span>
        </h1>

        <p>Painel Administrativo</p>

    </div>


    <div class="cards">

        <a href="compras.php" class="card">

            <div class="icone">🛒</div>

            <h2>Compras</h2>

            <p>
                Visualizar todas as compras realizadas.
            </p>

        </a>


        <a href="mensagens.php" class="card">

            <div class="icone">📩</div>

            <h2>Mensagens</h2>

            <p>
                Visualizar todas as mensagens enviadas pelos clientes.
            </p>

        </a>


        <a href="../index.php" class="card">

            <div class="icone">🚘</div>

            <h2>Site</h2>

            <p>
                Acessar o site da concessionária.
            </p>

        </a>


        <a href="logout.php" class="card sair">

            <div class="icone">🚪</div>

            <h2>Sair</h2>

            <p>
                Encerrar sessão do administrador.
            </p>

        </a>

    </div>

</div>

<div class="card">

    <h2>🚗</h2>

    <h3>Gerenciar Carros</h3>

    <p>
        Adicione, edite ou exclua veículos do estoque.
    </p>

    <a href="carros.php">
        GERENCIAR ESTOQUE
    </a>

</div>

</body>

</html>