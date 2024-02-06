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
    // Lida com a requisição POST
    $post_data = json_decode(file_get_contents('php://input'), true);

    // Depósito
    if($post_data['type'] == 'deposit') {
        $destination = $post_data['destination'];
        $amount = $post_data['amount'];

        // Adiciona o saldo à conta
        $response = $conta->adicionarSaldo($destination, $amount);

        // Define o código de status da resposta para 201
        http_response_code(201);
        echo $response;
        

    // Saque
    } elseif($post_data['type'] == 'withdraw') {
        $origin = $post_data['origin'];
        $amount = $post_data['amount'];
        $saldo = $conta->obterSaldo($origin);

        // Verificar se conta existe
        if($saldo === false) {
            // Define o código de status da resposta para 404
            http_response_code(404);
            echo 0;

        } elseif ($saldo < $amount) {      

            // Define o código de status da resposta para 409 se o saldo for insuficiente
            http_response_code(409);
            echo 0;
        } else {

            // Realiza o saque
            $response = $conta->realizarSaque($origin, $amount);

            // Define o código de status da resposta para 201
            http_response_code(201);
            echo $response;
        }        

    // Transferência
    } elseif($post_data['type'] == 'transfer') {
        $origin = $post_data['origin'];
        $amount = $post_data['amount'];
        $destination = $post_data['destination'];
        $saldo = $conta->obterSaldo($origin);

        // Verificar se conta existe
        if($saldo === false) {
            // Define o código de status da resposta para 404
            http_response_code(404);
            echo 0;

        } elseif($saldo < $amount) {      

            // Define o código de status da resposta para 409 se o saldo for insuficiente
            http_response_code(409);
            echo 0;
        } else {

            // Realiza a transferência
            $response = $conta->transferirSaldo($origin, $amount, $destination);

            // Define o código de status da resposta para 201
            http_response_code(201);
            echo $response;
        }
    }
}
?>