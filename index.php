<?php 

require_once("vendor/autoload.php");

use \Slim\Slim; // namespace
use \Hcode\Page; // namespace

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() { // executa essa funcao na pag inicail '/'

	$page = new Page(); //abre o header

	$page->setTpl("index"); //carrega o conteudo com o '__contruct'. o '__destruct' é atomatico quando acaba
  
}); // basicamente juntando as tags 'head', 'body' e 'h1'

$app->run();

 ?>