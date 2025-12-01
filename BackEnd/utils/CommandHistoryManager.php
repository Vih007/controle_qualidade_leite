<?php

// O CommandHistoryManager lida com a serialização/desserialização dos comandos na sessão.

class CommandHistoryManager {
    private const HISTORY_KEY = 'command_history';
    
    // Inicia a sessão e a pilha de histórico
    private static function init() {
        if (session_status() === PHP_SESSION_NONE) {
             session_start();
        }
        if (!isset($_SESSION[self::HISTORY_KEY]) || !is_array($_SESSION[self::HISTORY_KEY])) {
            $_SESSION[self::HISTORY_KEY] = [];
        }
    }

    /**
     * Adiciona um comando executado à pilha, serializando o objeto.
     * @param Command $command O objeto de comando executado.
     */
    public static function push(Command $command): void {
        self::init();
        // Armazena o objeto serializado
        $_SESSION[self::HISTORY_KEY][] = serialize($command);
    }

    /**
     * Remove e retorna o último comando executado, desserializando-o.
     * @return Command|null O último objeto de comando ou null se a pilha estiver vazia.
     */
    public static function pop(): ?Command {
        self::init();
        if (empty($_SESSION[self::HISTORY_KEY])) {
            return null;
        }
        
        // Remove o último elemento e desserializa
        $serializedCommand = array_pop($_SESSION[self::HISTORY_KEY]);
        
        // IMPORTANTE: O PHP deve ser capaz de encontrar a classe do comando (autoload)
        return unserialize($serializedCommand);
    }
    
    /**
     * Retorna a quantidade de comandos na pilha.
     */
     public static function count(): int {
        self::init();
        return count($_SESSION[self::HISTORY_KEY]);
     }
}