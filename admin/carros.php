<?php

include("admin_protecao.php");
include("../conexao.php");

$sql = "SELECT * FROM carros ORDER BY id DESC";

$resultado = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Gerenciar Carros | BorgesCar</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #0b0b0b;
    color: white;
}

header {
    background: #111;
    padding: 25px 50px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #333;
}

.logo {
    font-size: 28px;
    font-weight: bold;
}

.logo span {
    color: #e00000;
}

header a {
    color: white;
    text-decoration: none;
}

.container {
    width: 90%;
    max-width: 1200px;
    margin: 40px auto;
}

.topo {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.topo h1 {
    margin: 0;
}

.btn-adicionar {
    background: #d00000;
    color: white;
    padding: 14px 22px;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
}

.carro {
    background: #151515;
    border: 1px solid #292929;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;

    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.carro-info h2 {
    margin: 0 0 10px;
}

.preco {
    color: #e00000;
    font-size: 20px;
    font-weight: bold;
}

.botoes {
    display: flex;
    gap: 10px;
}

.btn-editar,
.btn-excluir {
    padding: 10px 16px;
    border-radius: 5px;
    text-decoration: none;
    color: white;
}

.btn-editar {
    background: #444;
}

.btn-excluir {
    background: #a00000;
}

@media(max-width:700px) {

    .carro {
        flex-direction: column;
        align-items: flex-start;
    }

    .topo {
        flex-direction: column;
        align-items: flex-start;
        gap: 20px;
    }

}

</style>

</head>

<body>

<header>

    <div class="logo">
        BORGES<span>CAR</span>
    </div>

    <a href="painel.php">
        ← Voltar ao painel
    </a>

</header>

<div class="container">

    <div class="topo">

        <h1>Gerenciar Estoque</h1>

        <a
            href="adicionar_carro.php"
            class="btn-adicionar"
        >
            + ADICIONAR CARRO
        </a>

    </div>

    <?php if ($resultado->num_rows == 0): ?>

        <p>Nenhum carro cadastrado.</p>

    <?php endif; ?>


    <?php while ($carro = $resultado->fetch_assoc()): ?>

        <div class="carro">

            <div class="carro-info">

                <h2>
                    <?= htmlspecialchars($carro['nome']); ?>
                </h2>

                <div class="preco">

                    R$
                    <?= number_format(
                        $carro['preco'],
                        0,
                        ",",
                        "."
                    ); ?>

                </div>

                <p>
                    Ano:
                    <?= htmlspecialchars($carro['ano']); ?>
                </p>

            </div>

            <div class="botoes">

                <a
                    class="btn-editar"
                    href="editar_carro.php?id=<?= $carro['id']; ?>"
                >
                    EDITAR
                </a>

                <a
                    class="btn-excluir"
                    href="excluir_carro.php?id=<?= $carro['id']; ?>"
                    onclick="return confirm('Tem certeza que deseja excluir este veículo?');"
                >
                    EXCLUIR
                </a>

            </div>

        </div>

    <?php endwhile; ?>

</div>

</body>

</html>