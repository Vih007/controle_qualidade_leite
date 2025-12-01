<?php

interface Command {
    /**
     * Executa a operação.
     * @return bool
     */
    public function execute(): bool;

    /**
     * Reverte a operação.
     * @return bool
     */
    public function undo(): bool;

    /**
     * Registra a operação para fins de auditoria.
     * A lógica de log é delegada, mas a chamada é garantida no execute().
     * @return bool
     */
    public function log(): bool;
}