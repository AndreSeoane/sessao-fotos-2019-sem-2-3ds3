<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT');
require '../Slim/Slim.php';
\Slim\Slim::registerAutoloader();

//instancie o objeto
$app = new \Slim\Slim();
$app->response()->header('Content-Type', 'application/json;charset=utf-8;');

//defina a rota
$app->get('/', function () {
    echo "CameraApp";
});

$app->get('/getUsuarios', 'getUsuarios');
$app->get('/getUsuarioID/:email/:senha', 'getUsuarioID');
$app->post('/addUsuario', 'addUsuario');
$app->get('/getFotos/:id', 'getFotos');
$app->post('/addFoto', 'addFoto');

//rode a aplicação Slim 
$app->run();

function getConn()
{
    return new PDO(
        'mysql:host=localhost:3307;dbname=db_cabine_fotos',
        'root',
        'admin',
        array(

            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        )
    );
}
// 
function getUsuarios()
{
    $stmt     = getConn()->query("SELECT * FROM tb_usuario");
    $usuarios = $stmt->fetchAll(PDO::FETCH_OBJ);
    echo "{\"usuarios\":" . json_encode($usuarios, JSON_PRETTY_PRINT) . "}";
}

function getUsuarioID($email, $senha)
{
    $stmt    = getConn()->query("SELECT * FROM tb_usuario WHERE ds_email = '$email' AND ds_senha = '$senha'");
    $usuario = $stmt->fetchAll(PDO::FETCH_OBJ);
    echo "{\"usuario\":" . json_encode($usuario, JSON_PRETTY_PRINT) . "}";
}

function addUsuario()
{
    $request    = \Slim\Slim::getInstance()->request();

    $usuario    = json_decode($request->getBody());
    $sql        = "INSERT INTO tb_usuario (nm_usuario, ds_login, ds_email, ds_senha) VALUES (:nm_usuario, :ds_login, :ds_email, :ds_senha)";
    $conn       = getConn();
    $stmt       = $conn->prepare($sql);
    $stmt->bindParam("nm_usuario", $usuario->nm_usuario);
    $stmt->bindParam("ds_login", $usuario->ds_login);
    $stmt->bindParam("ds_email", $usuario->ds_email);
    $stmt->bindParam("ds_senha", $usuario->ds_senha);
    $stmt->execute();

    echo true;

    /*{
        "nm_usuario": "Gustavo",
        "ds_login": "gustavozz",
        "ds_email"  : "gustavo@holder.com",
        "ds_senha"  : "123123"
    }*/
}

function getFotos($idSessao)
{
    $sql        = "SELECT * FROM tb_fotos as f WHERE cd_sessao = $idSessao";
    $stmt       = getConn()->query($sql);
    $fotos      = $stmt->fetchAll(PDO::FETCH_OBJ);
    foreach ($fotos as $linha => $valor) {
        $fotos[$linha]->ds_foto = base64_encode($valor->ds_foto);
    }
    echo "{\"fotos\":" . json_encode($fotos, JSON_PRETTY_PRINT) . "}";
}

function addFoto()
{
    $request = \Slim\Slim::getInstance()->request();
    
    $foto    = json_decode($request->getBody());
    // $foto->ds_foto = base64_decode($foto->ds_foto);
    $sql     = "INSERT INTO tb_fotos (cd_sessao, ds_foto) VALUES (:cd_sessao, :ds_foto)";
    $conn    = getConn();
    $stmt    = $conn->prepare($sql);
    $stmt->bindParam("cd_sessao", $foto->cd_sessao);
    $stmt->bindParam("ds_foto", $foto->ds_foto);
    $stmt->execute();
    echo json_encode($foto, JSON_PRETTY_PRINT);
    /*{
        "cd_sessao"   : "1",
        "ds_foto"   : "BASE64-ENCODADO"
    }*/
}

// function updProdutos($id)
// {
//   $request = \Slim\Slim::getInstance()->request();
//   $produto = json_decode($request->getBody());
//   $sql = "UPDATE produtos SET nome=:nome,preco=:preco,dataInclusao=:dataInclusao,idCategoria=:idCategoria WHERE id=:id";
//   $conn = getConn();
//   $stmt = $conn->prepare($sql);
//   $stmt->bindParam("nome", $produto->nome);
//   $stmt->bindParam("preco", $produto->preco);
//   $stmt->bindParam("dataInclusao", $produto->dataInclusao);
//   $stmt->bindParam("idCategoria", $produto->idCategoria);
//   $stmt->bindParam("id", $id);
//   $stmt->execute();
//   echo json_encode($produto, JSON_PRETTY_PRINT);
// }
// function deleteProduto($id)
// {
//   $sql = "DELETE FROM produtos WHERE id=:id";
//   $conn = getConn();
//   $stmt = $conn->prepare($sql);
//   $stmt->bindParam("id", $id);
//   $stmt->execute();
//   echo "{'message':'Produto apagado'}";
// }
