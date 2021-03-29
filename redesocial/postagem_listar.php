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
        $consulta = $conecta->prepare('SELECT tb02_postagem.*, tb01_nome,
        (SELECT count(*) FROM tb04_curtidas WHERE tb04_postagem = tb02_id) curtidas,
        (SELECT count(*) FROM tb03_comentarios WHERE tb03_postagem = tb02_id) comentarios,
        (select GROUP_CONCAT(CONCAT(\'<b>\',tb01_usuario,\'</b>: \',tb03_texto) SEPARATOR \'<br>\')  comentarios
            from tb03_comentarios
            inner join tb01_usuarios on (tb03_usuario = tb01_id)
            where tb03_postagem = tb02_id 
            order by tb03_data desc limit 5) comentarios_texto
      FROM tb02_postagem
      INNER JOIN tb01_usuarios ON (tb01_id = tb02_usuario)
      INNER JOIN tb05_amigos 
            ON ((tb05_usuario = tb02_usuario AND tb05_amigo = ?  AND tb05_situacao = 1) 
            OR (tb05_amigo = tb02_usuario AND tb05_usuario = ?  AND tb05_situacao = 1))

      UNION 

      SELECT tb02_postagem.*, tb01_nome,
        (SELECT count(*) FROM tb04_curtidas WHERE tb04_postagem = tb02_id) curtidas,
        (SELECT count(*) FROM tb03_comentarios WHERE tb03_postagem = tb02_id) comentarios,
        (select GROUP_CONCAT(CONCAT(\'<b>\',tb01_usuario,\'</b>: \',tb03_texto) SEPARATOR \'<br>\')  comentarios
            from tb03_comentarios
            inner join tb01_usuarios on (tb03_usuario = tb01_id)
            where tb03_postagem = tb02_id 
            order by tb03_data desc limit 5) comentarios_texto
      FROM tb02_postagem
      INNER JOIN tb01_usuarios ON (tb01_id = tb02_usuario)
WHERE tb02_usuario = ?

      ORDER BY tb02_data DESC');
        $consulta->execute(array($user, $user, $user));
        $resposta["mensagem"] = $consulta->fetchAll();
    } catch (PDOException $e) {
        $resposta["erro"] = $e->getMessage(); // opcional, apenas para teste
    }    
} else {
    $resposta["erro"] = "As credenciais não foram informadas!";
}
$json = json_encode($resposta);
echo ($json);
