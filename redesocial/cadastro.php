<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // limpa o cache
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

$servidor = 'localhost';
$usuario  = 'aluno';
$senha    = 'etec@147';
$banco    = 'aluno_QTS_redesocial';


if(isset($_POST["email"])) {
    $email = $_POST["email"];
    $celular = $_POST["celular"];
    $nome = $_POST["nome"];
    $user = $_POST["usuario"];
    $password = $_POST["senha"];
    $situacao = 1;
    try {
        $conecta = new PDO("mysql:host=$servidor;dbname=$banco", $usuario, $senha);
        $conecta->exec("set names utf8"); //permite caracteres latinos.
        $consulta = $conecta->prepare('INSERT INTO tb01_usuarios VALUES (?, ?, ?, ?, ?, md5(?), ?)');
        $consulta->execute(array(0, $email, $celular, $nome, $user, $password, $situacao));
        $resposta["mensagem"] = "Registro inserido com sucesso!";
    } catch (PDOException $e) {
        $resposta["erro"] = $e->getMessage(); // opcional, apenas para teste
    }    
} else {
    $resposta["erro"] = "As credenciais não foram informadas!";
}
$json = json_encode($resposta);
echo ($json);
