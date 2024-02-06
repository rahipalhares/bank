<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type");

session_start(); // Inicia a sessão

class Conta {
    private $contas;

    public function __construct() {
        if(!isset($_SESSION['contas'])) {
            $_SESSION['contas'] = array();
        }
        $this->contas = &$_SESSION['contas']; // Referência à variável de sessão
    }

    public function criarConta($accountId) {
        if (!isset($this->contas[$accountId])) {
            $this->contas[$accountId] = array("balance" => 0);
            return true;  // Conta criada com sucesso
        } else {
            return false;  // A conta já existe
        }
    }

    public function obterSaldo($accountId) {
        if(isset($this->contas[$accountId]["balance"])) {
            return $this->contas[$accountId]["balance"];
        }else{
            return false;
        }
    }

    public function adicionarSaldo($accountId, $amount) {
        if (!isset($this->contas[$accountId])) {
            $contaCriada = $this->criarConta($accountId);
            if (!$contaCriada) {
                return false;  // Falha ao criar a conta
            }
        }
        $this->contas[$accountId]["balance"] += $amount; // Saldo adicionado com sucesso

        $response = array(
            "destination" => array(
                "id" => $accountId,
                "balance" => $this->contas[$accountId]["balance"]
            )
        );
        return json_encode($response);
    }

    public function realizarSaque($accountId, $amount) {
        // Verifica se a conta existe
        if (!isset($this->contas[$accountId])) {
            return false;
        }

        // Realiza o saque
        $this->contas[$accountId]["balance"] -= $amount;

        // Retorna o saldo atualizado
        $response = array(
            "origin" => array(
                "id" => $accountId,
                "balance" => $this->contas[$accountId]["balance"]
            )
        );
        return json_encode($response);
    }

    public function transferirSaldo($origin, $amount, $destination) {
        // Verifica se a conta de origem existe
        if (!isset($this->contas[$origin])) {
            // Retorna se a conta não existir
            return false;
        }

        // Verifica se a conta de destino existe, se não, cria uma
        if (!isset($this->contas[$destination])) {
            $contaCriada = $this->criarConta($destination);
            if (!$contaCriada) {
                return false;  // Falha ao criar a conta
            }
        }

        // Realiza a transferência
        $this->contas[$origin]["balance"] -= $amount;
        $this->contas[$destination]["balance"] += $amount;

        // Retorna os saldos atualizados
        $response = array(
            "origin" => array(
                "id" => $origin,
                "balance" => $this->contas[$origin]["balance"]
            ),
            "destination" => array(
                "id" => $destination,
                "balance" => $this->contas[$destination]["balance"]
            )
        );
        return json_encode($response);
    }

    public function reset() {
        session_destroy();
        $this->contas = array();
    }
}
?>