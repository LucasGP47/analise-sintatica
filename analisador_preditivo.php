<?php

class AnalisadorPreditivo {
    private $tokens;
    private $index;
    private $tokenAtual;

    public function __construct($tokens) {
        $this->tokens = $tokens;
        $this->index = 0;
        $this->tokenAtual = $this->tokens[$this->index];
    }

    private function proximoToken() {
        $this->index++;
        if ($this->index < count($this->tokens)) {
            $this->tokenAtual = $this->tokens[$this->index];
        }
    }

    private function verifica($tokenEsperado) {
        if ($this->tokenAtual === $tokenEsperado) {
            $this->proximoToken();
            return true;
        }
        return false;
    }

    public function analisarPrograma() {
        if ($this->verifica('programa')) {
            if ($this->verifica('id')) {
                if ($this->verifica('{')) {
                    $this->analisarBloco();
                    if ($this->verifica('}')) {
                        echo "Programa válido!\n";
                    } else {
                        $this->erro("'}' esperado");
                    }
                } else {
                    $this->erro("'{' esperado");
                }
            } else {
                $this->erro("Identificador esperado");
            }
        } else {
            $this->erro("'programa' esperado");
        }
    }

    private function analisarBloco() {
        while ($this->tokenAtual !== '}' && $this->index < count($this->tokens)) {
            $this->analisarComando();
        }
    }

    private function analisarComando() {
        switch ($this->tokenAtual) {
            case 'int':
            case 'float':
            case 'char':
                $this->analisarDeclaracao();
                break;
            case 'id':
                $this->analisarAtribuicao();
                break;
            case 'if':
                $this->analisarSelecao();
                break;
            case 'while':
            case 'for':
                $this->analisarRepeticao();
                break;
            case 'scanf':
                $this->analisarLeitura();
                break;
            case 'printf':
                $this->analisarImpressao();
                break;
            case 'return':
                $this->analisarRetorno();
                break;
            default:
                $this->erro("Comando não reconhecido");
                break;
        }
    }

    private function analisarDeclaracao() {
        $this->verifica($this->tokenAtual); 
        if ($this->verifica('id') && $this->verifica(';')) {
            echo "Declaração válida\n";
        } else {
            $this->erro("Erro na declaração de variável");
        }
    }

    private function analisarAtribuicao() {
        $this->verifica('id');
        if ($this->verifica('=') && $this->analisarExpressao() && $this->verifica(';')) {
            echo "Atribuição válida\n";
        } else {
            $this->erro("Erro na atribuição");
        }
    }

    private function analisarLeitura() {
        if ($this->verifica('scanf') && $this->verifica('(') && $this->verifica('string') && $this->verifica(')') && $this->verifica(';')) {
            echo "Leitura válida\n";
        } else {
            $this->erro("Erro no comando de leitura");
        }
    }

    private function analisarImpressao() {
        if ($this->verifica('printf') && $this->verifica('(') && $this->verifica('string') && $this->verifica(')') && $this->verifica(';')) {
            echo "Impressão válida\n";
        } else {
            $this->erro("Erro no comando de impressão");
        }
    }

    private function analisarSelecao() {
        if ($this->verifica('if') && $this->verifica('(') && $this->analisarExpressao() && $this->verifica(')') && $this->verifica('{')) {
            $this->analisarBloco();
            if ($this->verifica('}')) {
                echo "Comando if válido\n";
            } else {
                $this->erro("Erro no comando if");
            }
        }
    }

    private function analisarRepeticao() {
        if ($this->verifica('while') && $this->verifica('(') && $this->analisarExpressao() && $this->verifica(')') && $this->verifica('{')) {
            $this->analisarBloco();
            if ($this->verifica('}')) {
                echo "Comando while válido\n";
            } else {
                $this->erro("Erro no comando while");
            }
        } elseif ($this->verifica('for') && $this->verifica('(')) {
            $this->analisarAtribuicao();
            $this->analisarExpressao();
            if ($this->verifica(';')) {
                $this->analisarAtribuicao();
                if ($this->verifica(')') && $this->verifica('{')) {
                    $this->analisarBloco();
                    if ($this->verifica('}')) {
                        echo "Comando for válido\n";
                    } else {
                        $this->erro("Erro no comando for");
                    }
                }
            }
        }
    }

    private function analisarExpressao() {
        return $this->verifica('id') || $this->verifica('numero');
    }

    private function erro($mensagem) {
        echo "Erro de sintaxe: $mensagem\n";
        exit(1);
    }
}

$tokens = ['programa', 'id', '{', 'int', 'id', ';', 'id', '=', 'id', ';', 'if', '(', 'id', ')', '{', '}', '}', '}'];

$analisador = new AnalisadorPreditivo($tokens);
$analisador->analisarPrograma();

?>