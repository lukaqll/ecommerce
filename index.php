<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim; // namespace
use \Hcode\Page; // namespace
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app = new Slim();


$app->config('debug', true);

$app->get('/', function() { // executa essa funcao na pag inicail '/'

	$page = new Page(); //abre o header

	$page->setTpl("index"); //carrega o conteudo com o '__contruct'. o '__destruct' é atomatico quando acaba
  
}); // basicamente juntando as tags 'head', 'body' e 'h1'

//----------------------- rota pag admin--------------------------------------------

$app->get('/admin', function() {

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index"); 
  
});

//------------------------- rota admin login------------------------------------------

$app->get('/admin/login', function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("login");

});

//------------------------- verificar login admin-------------------------------------

$app->post('/admin/login', function(){

	User::login($_POST["login"], $_POST["password"]);

	header("location: /admin");

	/*
	if(isset($_SESSION["noexists"])){
		echo $_SESSION["noexists"];
		unset($_SESSION["noexists"]);
	}

	*/

	exit;
});

//---------------------------rota ao deslogar----------------------------------------

$app->get('/admin/logout', function(){ 

	User::logout();

	header("Location: /admin/login"); // volta para paf de login admin
	exit;
});

//----------------------------lista de cadastros---------------------------------------

$app->get("/admin/users",function(){

	User::verifyLogin();

	$users = User::listAll();

	$page = new PageAdmin();

	$page -> setTpl("users", array("users" => $users));
});

//------------------------------rota de criação de cadastro----------------------------

$app->get("/admin/users/create",function(){

	User::verifyLogin();

	$page = new PageAdmin();

	$page-> setTpl("users-create");
});

//--------------------------------delete e usuario-----------------------------------

$app->get("/admin/users/:iduser/delete", function($iduser){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;

});

//----------------------criação de novo cadastro--------------------------------------
$app->post("/admin/users/create", function () {

 	User::verifyLogin();

	$user = new User();

 	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

 	$_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [

 		"cost"=>12

 	]);

 	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
 	exit;

});

//------------------------------update de cadastro--------------------------------

$app->get("/admin/users/:iduser",function($iduser){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();

	$page-> setTpl("users-update", array(
		"user"=>$user->getValues()
	));
});

//--------------------------jogando os updates no banco-----------------------------------------

$app->post("/admin/users/:iduser", function($iduser){

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

	$user->get((int)$iduser); // carrega os daods

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;
});

//--------------------rota Esqueci minha senha -----------------------------

$app->get("/admin/forgot", function(){

	$page = new PageAdmin([
	"header"=>false,
	"footer"=>false
	]);

	$page->setTpl("forgot");
});

$app->post("/admin/forgot", function(){

	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;
});

$app->get("/admin/forgot/sent", function(){

	$page = new PageAdmin([
	"header"=>false,
	"footer"=>false
	]);

	$page->setTpl("forgot-sent");
});

$app->get("/admin/forgot/reset", function(){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
	"header"=>false,
	"footer"=>false
	]);

	$page->setTpl("forgot-reset", array( "name"=>$user["desperson"], "code"=>$_GET["code"] ));

});

$app->post("/admin/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($user["idrecovery"]);

	$users = new User();

	$users->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, ["cost"=>12]);

	$users->setPassword($password);

	$page = new PageAdmin([
	"header"=>false,
	"footer"=>false
	]);

	$page->setTpl("forgot-reset-success");



});



$app->run();

 ?>