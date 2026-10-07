<?php
session_start();

if(isset($_POST['entrar'])){

    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    if($usuario == "felipe" && $senha == "123456"){

        $_SESSION['admin'] = true;

        header("Location:painel.php");
        exit;

    }else{

        $erro = "Usuário ou senha inválidos.";

    }

}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Login - BorgesCar</title>

<style>

body{
background:#111;
font-family:Arial;
display:flex;
justify-content:center;
align-items:center;
height:100vh;
}

.login{
background:#222;
padding:40px;
border-radius:10px;
width:350px;
}

h2{
color:white;
text-align:center;
}

input{
width:100%;
padding:12px;
margin:10px 0;
}

button{
width:100%;
padding:12px;
background:#b30000;
color:white;
border:none;
cursor:pointer;
}

p{
color:red;
text-align:center;
}

</style>

</head>

<body>

<div class="login">

<h2>Painel BorgesCar</h2>

<?php if(isset($erro)) echo "<p>$erro</p>"; ?>

<form method="POST">

<input type="text" name="usuario" placeholder="Usuário">

<input type="password" name="senha" placeholder="Senha">

<button name="entrar">Entrar</button>

</form>

</div>

</body>
</html>