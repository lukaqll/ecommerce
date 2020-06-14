<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;

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

	exit;
});

//---------------------------rota ao deslogar----------------------------------------

$app->get('/admin/logout', function(){ 

	User::logout();

	header("Location: /admin/login"); // volta para paf de login admin
	exit;
});
 ?>