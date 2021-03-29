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
    //$foto = $_POST["foto"];
    $texto = $_POST["texto"];

    $dir = "fotos/"; 
    // recebendo o arquivo multipart 
    $arquivo = $_FILES["foto"];
    $nomeimagem = $arquivo["name"];
    // Move o arquivo da pasta temporaria de upload para a pasta de destino 
    move_uploaded_file($arquivo["tmp_name"], "$dir/".$nomeimagem);

    try {
        $conecta = new PDO("mysql:host=$servidor;dbname=$banco", $usuario, $senha);
        $conecta->exec("set names utf8"); //permite caracteres latinos.
        $consulta = $conecta->prepare('INSERT INTO tb02_postagem VALUES (0, ?, ?, ?, now())');
        $consulta->execute(array($user, $nomeimagem, $texto));
        $resposta["mensagem"] = "Registro inserido com sucesso!";
    } catch (PDOException $e) {
        $resposta["erro"] = $e->getMessage(); // opcional, apenas para teste
    }    
} else {
    $resposta["erro"] = "As credenciais não foram informadas!";
}
$json = json_encode($resposta);
echo ($json);
