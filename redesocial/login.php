<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // limpa o cache
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

$servidor = 'localhost';
$usuario  = 'aluno';
$senha    = 'etec@147';
$banco    = 'aluno_QTS_redesocial';

if(isset($_POST["login"]) and isset($_POST["senha"])) {
    $login = $_POST["login"];
    $password = $_POST["senha"];
    try {
        $conecta = new PDO("mysql:host=$servidor;dbname=$banco", $usuario, $senha);
        $conecta->exec("set names utf8"); //permite caracteres latinos.
        $consulta = $conecta->prepare('SELECT * FROM tb01_usuarios WHERE (tb01_email = ? or tb01_celular = ? or tb01_usuario = ?) and tb01_senha = md5(?)');
        $consulta->execute(array($login, $login, $login, $password));
        $registro = $consulta->fetchAll();
        if($registro) {
            $resposta["status"] = 200;
            $resposta["usuario"] = $registro;
        } else {
            $resposta["status"] = 203;
            $resposta["erro"] = "Usuário ou senha inválido!";
        }
    } catch (PDOException $e) {
        $resposta["status"] = 201;
        $resposta["erro"] = $e->getMessage(); // opcional, apenas para teste
    }    
} else {
    $resposta["status"] = 202;
    $resposta["erro"] = "As credenciais não foram informadas!";
}
$json = json_encode($resposta);
echo ($json);
