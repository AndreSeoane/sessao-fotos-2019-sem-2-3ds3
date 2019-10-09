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

// function getConn()
// {
//   return new PDO(
//     'mysql:host=localhost:3307;dbname=db_cameraapp',
//     'root',
//     'admin',
//     array(
//       PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//       PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
//     )
//   );
// }

function getConn()
{
  return new PDO(
    'mysql:host=localhost:3307;dbname=id10755412_db_cameraapp',
    'root',
    'admin',
    array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    )
  );
}

function getUsuarios()
{
  $stmt     = getConn()->query("SELECT * FROM tb_usuario");
  $usuarios = $stmt->fetchAll(PDO::FETCH_OBJ);
  echo "{\"usuarios\":" . json_encode($usuarios, JSON_PRETTY_PRINT) . "}";
}

function getUsuarioID($email, $senha)
{
  $stmt    = getConn()->query("SELECT * FROM tb_usuario WHERE cd_email = '$email' AND cd_senha = '$senha'");
  $usuario = $stmt->fetchAll(PDO::FETCH_OBJ);
  echo "{\"usuario\":" . json_encode($usuario, JSON_PRETTY_PRINT) . "}";
}

function addUsuario()
{
  $request    = \Slim\Slim::getInstance()->request();
  $cd_usuario = mt_rand(1, PHP_INT_MAX);
  $usuario    = json_decode($request->getBody());
  $sql        = "INSERT INTO tb_usuario (cd_usuario, nm_usuario, cd_email, cd_senha) VALUES (:cd_usuario, :nm_usuario, :cd_email, :cd_senha)";
  $conn       = getConn();
  $stmt       = $conn->prepare($sql);
  $stmt->bindParam("cd_usuario", $cd_usuario);
  $stmt->bindParam("nm_usuario", $usuario->nm_usuario);
  $stmt->bindParam("cd_email", $usuario->cd_email);
  $stmt->bindParam("cd_senha", $usuario->cd_senha);
  $stmt->execute();
  $usuario->id = $cd_usuario;
  echo json_encode($usuario, JSON_PRETTY_PRINT);
  /*{
        "nm_usuario": "Gustavo",
        "cd_email"  : "gustavo@holder.com",
        "cd_senha"  : "123123"
    }*/
}

function getFotos($id)
{
  $sql        = "SELECT f.* FROM tb_foto as f JOIN tb_usuario as u ON f.cd_usuario = u.cd_usuario WHERE f.cd_usuario=$id";
  $stmt       = getConn()->query($sql);
  $fotos      = $stmt->fetchAll(PDO::FETCH_OBJ);
  $fotos[0]->im_foto = base64_encode($fotos[0]->im_foto);
  echo "{\"fotos\":" . json_encode($fotos, JSON_PRETTY_PRINT) . "}";
}

function addFoto()
{
  $request = \Slim\Slim::getInstance()->request();
  $cd_foto = mt_rand(1, PHP_INT_MAX);
  $foto    = json_decode($request->getBody());
  $sql     = "INSERT INTO tb_foto (cd_foto, im_foto, cd_usuario) VALUES (:cd_foto, :im_foto, :cd_usuario)";
  $conn    = getConn();
  $stmt    = $conn->prepare($sql);
  $stmt->bindParam("cd_foto", $cd_foto);
  $stmt->bindParam("im_foto", $foto->im_foto);
  $stmt->bindParam("cd_usuario", $foto->cd_usuario);
  $stmt->execute();
  $foto->id = $cd_foto;
  echo json_encode($foto, JSON_PRETTY_PRINT);
  /*{
        "cd_foto"   : "1",
        "im_foto"   : "BASE64-ENCODADO",
        "cd_usuario": "57827606"
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
