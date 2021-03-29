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
    $pesquisa = "";
    if(isset($_POST["pesquisa"])) {
        $pesquisa = $_POST["pesquisa"];
    }
    try {
        $conecta = new PDO("mysql:host=$servidor;dbname=$banco", $usuario, $senha);
        $conecta->exec("set names utf8"); //permite caracteres latinos.
        $sql = "SELECT tb01_id, tb01_nome, tb01_usuario,
        ifnull((select tb05_situacao from tb05_amigos where tb05_usuario=? and tb05_amigo=tb01_id),-1) convite_feito,
        ifnull((select tb05_situacao from tb05_amigos where tb05_usuario=tb01_id and tb05_amigo=? and tb05_situacao=0),-1) convite_recebido
   FROM tb01_usuarios WHERE tb01_id <> ?";
        if($pesquisa <> "") {
            $sql .= " AND tb01_nome LIKE ?";
        }
        $sql .= " ORDER BY convite_recebido desc, convite_feito, tb01_nome";
        $consulta = $conecta->prepare($sql);
        if($pesquisa <> "") {
            $consulta->execute(array($user, $user, $user, "%$pesquisa%"));
        } else {
            $consulta->execute(array($user, $user, $user));
        }
        $resposta["mensagem"] = $consulta->fetchAll();
    } catch (PDOException $e) {
        $resposta["erro"] = $e->getMessage(); // opcional, apenas para teste
    }    
} else {
    $resposta["erro"] = "As credenciais não foram informadas!";
}
$json = json_encode($resposta);
echo ($json);
