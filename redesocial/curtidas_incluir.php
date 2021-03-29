<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // limpa o cache
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

$servidor = 'localhost';
$usuario  = 'aluno';
$senha    = 'etec@147';
$banco    = 'aluno_QTS_redesocial';


if(isset($_POST["usuario"])) {
    $user = $_POST["usuario"];
    $postagem = $_POST["postagem"];
    try {
        $conecta = new PDO("mysql:host=$servidor;dbname=$banco", $usuario, $senha);
        $conecta->exec("set names utf8"); //permite caracteres latinos.

        $consulta = $conecta->prepare('INSERT INTO tb04_curtidas VALUES (0, ?, ?, now())');
        $consulta->execute(array($postagem, $user));
        $resposta["mensagem"] = "Registro inserido com sucesso!";
    } catch (PDOException $e) {
        $resposta["erro"] = $e->getMessage(); // opcional, apenas para teste
    }    
} else {
    $resposta["erro"] = "As credenciais não foram informadas!";
}
$json = json_encode($resposta);
echo ($json);
