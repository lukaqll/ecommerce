<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;
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

 ?>