<?php
// Inclui as funções de operações
include 'funcoes.php';

// Cria uma nova instância da classe Conta
$conta = new Conta();

// Obtém o método da requisição e a URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Remove a query string da URI (se houver)
$uri = explode('?', $uri)[0];


if($method == 'POST') {
    // Chama o método reset para limpar os valores armazenados
    $conta->reset();

    // Define o código de status da resposta para 200
    http_response_code(200);
    echo "OK";
}
?>