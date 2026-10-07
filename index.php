<?php

include("conexao.php");

/* =========================================================
   BUSCAR CARROS
========================================================= */

$sql = "
    SELECT
        c.*,
        (
            SELECT ic.imagem
            FROM imagens_carros ic
            WHERE ic.carro_id = c.id
            ORDER BY ic.id ASC
            LIMIT 1
        ) AS imagem
    FROM carros c
    ORDER BY c.id DESC
";

$resultado = $conn->query($sql);

$carros = [];

if ($resultado) {
    while ($carro = $resultado->fetch_assoc()) {
        $carros[] = $carro;
    }
}


/* =========================================================
   CARRO DESTAQUE
========================================================= */

$carroDestaque = $carros[0] ?? null;


/* =========================================================
   PEGAR TIPOS DO CARRO
========================================================= */

function pegarTipos($carro)
{
    $tipos = [];

    if (isset($carro['tipos']) && !empty($carro['tipos'])) {

        $dados = json_decode($carro['tipos'], true);

        if (is_array($dados)) {
            $tipos = $dados;
        }
    }

    if (empty($tipos) && isset($carro['categoria']) && !empty($carro['categoria'])) {
        $tipos[] = $carro['categoria'];
    }

    return $tipos;
}


/* =========================================================
   MARCAS EXCLUSIVAS
========================================================= */

$marcasExclusivas = [
    "Ferrari",
    "Lamborghini",
    "Porsche"
];

$exclusivos = [];

foreach ($marcasExclusivas as $marca) {

    $exclusivos[$marca] = null;

    foreach ($carros as $carro) {

        $nome = strtolower($carro['nome']);

        if (strpos($nome, strtolower($marca)) !== false) {
            $exclusivos[$marca] = $carro;
            break;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>BorgesCar Luxury</title>

    <link rel="stylesheet" href="style.css">

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

</head>

<body>


<!-- =========================================================
     TELA DE INTRODUÇÃO
========================================================= -->

<div id="intro">

    <div class="intro-fundo"></div>

    <div class="intro-conteudo">

        <div class="intro-logo">
            BORGES<span>CAR</span>
        </div>

        <div class="intro-linha"></div>

        <p class="intro-mini">
            LUXURY MOTORS
        </p>

        <h1>
            Seu proximo veiculo esta aqui.
        </h1>

        <p class="intro-texto">
            Uma experiência criada para quem não aceita o comum.
        </p>

        <button id="entrarSite">
            ENTRAR NA BORGESCAR
            <span>→</span>
        </button>

    </div>

</div>


<!-- =========================================================
     SITE
========================================================= -->

<div id="site">


<!-- =========================================================
     HEADER
========================================================= -->

<header class="header">

    <div class="logo">
        BORGES<span>CAR</span>
    </div>

    <nav>

        <a href="#inicio">INÍCIO</a>

        <a href="#exclusivos">EXCLUSIVOS</a>

        <a href="#estoque">ESTOQUE</a>

        <a href="#sobre">SOBRE</a>

        <a href="contato.php">CONTATO</a>

    </nav>

</header>


<!-- =========================================================
     HERO
========================================================= -->

<section class="hero" id="inicio">

    <div class="hero-overlay"></div>

    <div class="hero-conteudo">

        <p class="hero-mini">
            BORGESCAR LUXURY
        </p>

        <h1>
            MAIS QUE<br>
            <span>UM CARRO.</span>
        </h1>

        <div class="hero-linha"></div>

        <p>
            Máquinas selecionadas para quem
            não aceita o comum.
        </p>

    </div>

</section>


<!-- =========================================================
     EXPERIÊNCIA
========================================================= -->

<section class="experiencia">

    <div class="experiencia-topo">

        <div>

            <span class="sobre-label">
                BORGESCAR
            </span>

            <h2>
                UMA EXPERIÊNCIA<br>
                <span>FORA DO COMUM.</span>
            </h2>

        </div>

        <p>
            Mais do que vender veículos, a BorgesCar
            seleciona máquinas que representam
            desempenho, exclusividade e personalidade.
        </p>

    </div>


    <div class="experiencia-grid">

        <div class="experiencia-item">

            <span class="numero">01</span>

            <h3>DESEMPENHO</h3>

            <p>
                Potência, tecnologia e engenharia
                para transformar cada quilômetro
                em uma experiência.
            </p>

        </div>


        <div class="experiencia-item">

            <span class="numero">02</span>

            <h3>EXCLUSIVIDADE</h3>

            <p>
                Veículos selecionados para pessoas
                que procuram algo além do convencional.
            </p>

        </div>


        <div class="experiencia-item">

            <span class="numero">03</span>

            <h3>EXPERIÊNCIA</h3>

            <p>
                Um padrão de atendimento inspirado
                no universo dos grandes automóveis.
            </p>

        </div>

    </div>

</section>


<!-- =========================================================
     MARCAS EXCLUSIVAS
========================================================= -->

<section class="exclusivos" id="exclusivos">

    <div class="secao-topo">

        <div>

            <span class="sobre-label">
                SELEÇÃO ESPECIAL
            </span>

            <h2>
                MARCAS<br>
                <span>EXCLUSIVAS.</span>
            </h2>

        </div>

        <p>
            Algumas marcas não precisam de apresentação.
            Elas representam história, desempenho e
            exclusividade.
        </p>

    </div>


    <div class="marcas-grid">

        <?php foreach ($exclusivos as $marca => $carro): ?>

            <div
                class="marca-card"
                <?php if ($carro && !empty($carro['imagem'])): ?>
                    style="background-image: url('<?php echo htmlspecialchars($carro['imagem']); ?>');"
                <?php endif; ?>
            >

                <div class="marca-overlay"></div>

                <div class="marca-conteudo">

                    <span>
                        MARCA EXCLUSIVA
                    </span>

                    <h3>
                        <?php echo htmlspecialchars($marca); ?>
                    </h3>

                    <?php if ($carro): ?>

                        <p>
                            <?php echo htmlspecialchars($carro['nome']); ?>
                        </p>

                        <a
                            href="detalhes.php?id=<?php echo (int)$carro['id']; ?>"
                            class="marca-botao"
                        >
                            CONHECER
                            <strong>→</strong>
                        </a>

                    <?php else: ?>

                        <p>
                            Em breve no estoque.
                        </p>

                    <?php endif; ?>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</section>


<!-- =========================================================
     DESTAQUE
========================================================= -->

<?php if ($carroDestaque): ?>

<section class="destaque">

    <div class="destaque-imagem">

        <?php if (!empty($carroDestaque['imagem'])): ?>

            <img
                src="<?php echo htmlspecialchars($carroDestaque['imagem']); ?>"
                alt="<?php echo htmlspecialchars($carroDestaque['nome']); ?>"
            >

        <?php endif; ?>

    </div>


    <div class="destaque-conteudo">

        <span class="destaque-label">
            DESTAQUE BORGESCAR
        </span>

        <span class="destaque-ano">
            <?php echo htmlspecialchars($carroDestaque['ano']); ?>
        </span>

        <h2>
            <?php echo htmlspecialchars($carroDestaque['nome']); ?>
        </h2>


        <div class="destaque-tipos">

            <?php foreach (pegarTipos($carroDestaque) as $tipo): ?>

                <span>
                    <?php echo htmlspecialchars($tipo); ?>
                </span>

            <?php endforeach; ?>

        </div>


        <p>
            <?php echo htmlspecialchars($carroDestaque['descricao']); ?>
        </p>


        <div class="destaque-preco">

            R$

            <?php
            echo number_format(
                (float)$carroDestaque['preco'],
                2,
                ',',
                '.'
            );
            ?>

        </div>


        <a
            href="detalhes.php?id=<?php echo (int)$carroDestaque['id']; ?>"
            class="btn-destaque"
        >
            CONHECER VEÍCULO
            <span>→</span>
        </a>

    </div>

</section>

<?php endif; ?>


<!-- =========================================================
     ESTOQUE
========================================================= -->

<section class="estoque" id="estoque">

    <div class="estoque-cabecalho">

        <div>

            <span class="sobre-label">
                NOSSO ESTOQUE
            </span>

            <h2>
                ENCONTRE SUA<br>
                <span>PRÓXIMA MÁQUINA.</span>
            </h2>

        </div>

        <p>
            Explore nossa seleção de veículos.
        </p>

    </div>


    <!-- FILTROS -->

    <div class="filtros">

        <div class="campo-busca">

            <span class="icone-lupa">
                ⌕
            </span>

            <input
                type="text"
                id="busca"
                placeholder="Buscar veículo..."
            >

        </div>


        <select id="categoria">

            <option value="">
                Todos os tipos
            </option>

            <option value="esportivo">
                Esportivo
            </option>

            <option value="conversível">
                Conversível
            </option>

            <option value="coupé">
                Coupé
            </option>

            <option value="sedã">
                Sedã
            </option>

            <option value="suv">
                SUV
            </option>

            <option value="hatch">
                Hatch
            </option>

            <option value="picape">
                Picape
            </option>

            <option value="luxo">
                Luxo
            </option>

            <option value="superesportivo">
                Superesportivo
            </option>

            <option value="elétrico">
                Elétrico
            </option>

            <option value="híbrido">
                Híbrido
            </option>

        </select>

    </div>


    <!-- CARROS -->

    <div class="carros-grid" id="listaCarros">

        <?php foreach ($carros as $carro): ?>

            <?php

            $tipos = pegarTipos($carro);

            $tiposTexto = implode(" ", $tipos);

            ?>

            <article
                class="card"
                data-nome="<?php echo strtolower(htmlspecialchars($carro['nome'])); ?>"
                data-categoria="<?php echo strtolower(htmlspecialchars($tiposTexto)); ?>"
            >

                <div class="card-imagem">

                    <?php if (!empty($carro['imagem'])): ?>

                        <img
                            src="<?php echo htmlspecialchars($carro['imagem']); ?>"
                            alt="<?php echo htmlspecialchars($carro['nome']); ?>"
                        >

                    <?php else: ?>

                        <div class="sem-imagem">
                            SEM IMAGEM
                        </div>

                    <?php endif; ?>


                    <div class="card-overlay">

                        <a
                            href="detalhes.php?id=<?php echo (int)$carro['id']; ?>"
                        >
                            VER DETALHES →
                        </a>

                    </div>

                </div>


                <div class="card-conteudo">

                    <div class="card-topo">

                        <span>
                            <?php echo htmlspecialchars($carro['ano']); ?>
                        </span>

                    </div>


                    <h3 class="nome-carro">

                        <?php echo htmlspecialchars($carro['nome']); ?>

                    </h3>


                    <div class="card-tipos">

                        <?php foreach ($tipos as $tipo): ?>

                            <span>
                                <?php echo htmlspecialchars($tipo); ?>
                            </span>

                        <?php endforeach; ?>

                    </div>


                    <p class="descricao">

                        <?php

                        $descricao = $carro['descricao'];

                        if (strlen($descricao) > 100) {
                            $descricao = substr($descricao, 0, 100) . "...";
                        }

                        echo htmlspecialchars($descricao);

                        ?>

                    </p>


                    <div class="card-rodape">

                        <div class="preco">

                            R$

                            <?php

                            echo number_format(
                                (float)$carro['preco'],
                                2,
                                ',',
                                '.'
                            );

                            ?>

                        </div>


                        <a
                            href="detalhes.php?id=<?php echo (int)$carro['id']; ?>"
                            class="btn-detalhes"
                        >
                            →
                        </a>

                    </div>

                </div>

            </article>

        <?php endforeach; ?>

    </div>


    <div
        class="sem-carros"
        id="semCarros"
        style="display:none;"
    >
        Nenhum veículo encontrado.
    </div>

</section>


<!-- =========================================================
     SOBRE
========================================================= -->

<section class="sobre" id="sobre">

    <div class="sobre-conteudo">

        <span class="sobre-label">
            SOBRE A BORGESCAR
        </span>

        <h2>
            O CARRO CERTO<br>
            <span>MUDA TUDO.</span>
        </h2>

        <p>
            A BorgesCar nasceu para reunir veículos
            que despertam algo diferente. Carros que
            não são apenas meios de transporte, mas
            verdadeiras experiências sobre quatro rodas.
        </p>

        <p>
            Nosso estoque combina esportividade,
            luxo, tecnologia e exclusividade em uma
            seleção pensada para quem busca algo especial.
        </p>

        <a href="contato.php" class="btn-sobre">
            FALE CONOSCO →
        </a>

    </div>

</section>


<!-- =========================================================
     FOOTER
========================================================= -->

<footer class="footer">

    <div class="footer-logo">
        BORGES<span>CAR</span>
    </div>

    <p>
        LUXURY MOTORS
    </p>

    <div class="footer-linha"></div>

    <small>
        © <?php echo date("Y"); ?> BorgesCar Luxury.
        Todos os direitos reservados.
    </small>

</footer>


</div>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>

/* =========================================================
   INTRODUÇÃO
========================================================= */

const intro = document.getElementById("intro");
const entrarSite = document.getElementById("entrarSite");

entrarSite.addEventListener("click", function () {

    intro.classList.add("intro-saindo");

    sessionStorage.setItem("borgescarIntro", "true");

    setTimeout(function () {

        intro.style.display = "none";

    }, 1000);

});


/* =========================================================
   PESQUISA E FILTRO
========================================================= */

const busca = document.getElementById("busca");
const categoria = document.getElementById("categoria");
const cards = document.querySelectorAll(".card");
const semCarros = document.getElementById("semCarros");


function filtrarCarros() {

    const texto = busca.value.toLowerCase().trim();
    const tipo = categoria.value.toLowerCase().trim();

    let encontrados = 0;


    cards.forEach(function (card) {

        const nome = card.dataset.nome || "";
        const tipos = card.dataset.categoria || "";

        const combinaNome =
            nome.includes(texto);

        const combinaTipo =
            tipo === "" || tipos.includes(tipo);

        if (combinaNome && combinaTipo) {

            card.style.display = "";

            encontrados++;

        } else {

            card.style.display = "none";

        }

    });


    if (encontrados === 0) {

        semCarros.style.display = "block";

    } else {

        semCarros.style.display = "none";

    }

}


busca.addEventListener("input", filtrarCarros);

categoria.addEventListener("change", filtrarCarros);


/* =========================================================
   ANIMAÇÃO DE ENTRADA
========================================================= */

document.addEventListener("DOMContentLoaded", function () {

    const entrou =
        sessionStorage.getItem("borgescarIntro");

    if (entrou === "true") {

        intro.style.display = "none";

    }

});

</script>


</body>
</html>