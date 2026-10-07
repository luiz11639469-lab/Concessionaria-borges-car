<?php

include("admin_protecao.php");
include("../conexao.php");


/*
 * Excluir imagem
 */

if (isset($_GET['imagem'])) {

    $imagem_id = intval($_GET['imagem']);
    $carro_id = intval($_GET['carro'] ?? 0);

    if ($imagem_id > 0) {

        $stmt = $conn->prepare(
            "DELETE FROM imagens_carros
             WHERE id = ?"
        );

        $stmt->bind_param(
            "i",
            $imagem_id
        );

        $stmt->execute();

        $stmt->close();
    }

    header(
        "Location: editar_carro.php?id=" . $carro_id
    );

    exit;
}


/*
 * Excluir carro
 */

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {

    header("Location: carros.php");
    exit;
}


$stmt = $conn->prepare(
    "DELETE FROM carros
     WHERE id = ?"
);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$stmt->close();


header("Location: carros.php");

exit;

?>