<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type");

class Conta {
    private $contas;
    private $file;

    public function __construct() {
        $this->file = 'contas.json';
        if(file_exists($this->file)) {
            $this->contas = json_decode(file_get_contents($this->file), true);
        } else {
            $this->contas = array();
        }
    }

    public function salvarContas() {
        file_put_contents($this->file, json_encode($this->contas));
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

        $this->salvarContas();

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

        $this->salvarContas();

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

        $this->salvarContas();

        return json_encode($response);
    }

    public function reset() {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
        $this->contas = array();
    }
}
?>