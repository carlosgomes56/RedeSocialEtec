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

    try {
        $conecta = new PDO("mysql:host=$servidor;dbname=$banco", $usuario, $senha);
        $conecta->exec("set names utf8"); //permite caracteres latinos.
        $sql = "select tb05_amigos.*, tb01_nome from tb05_amigos 
        inner join tb01_usuarios on (tb05_usuario = tb01_id)
        where tb05_amigo = ?
        and tb05_situacao = 0
        order by tb05_data";
        $consulta = $conecta->prepare($sql);
        $consulta->execute(array($user));
        $resposta["mensagem"] = $consulta->fetchAll();
    } catch (PDOException $e) {
        $resposta["erro"] = $e->getMessage(); // opcional, apenas para teste
    }    
} else {
    $resposta["erro"] = "As credenciais não foram informadas!";
}
$json = json_encode($resposta);
echo ($json);
