<?php
require('conexao.php');

$usuarios = $banco->query("SELECT id_usuario, senha FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);

foreach ($usuarios as $user) {
    // Só atualizar se a senha não for um hash 
    if (strlen($user['senha']) < 60) { 
        $novaHash = password_hash($user['senha'], PASSWORD_DEFAULT);

        $stmt = $banco->prepare("UPDATE usuarios SET senha = :senha WHERE id_usuario = :id");
        $stmt->bindValue(':senha', $novaHash);
        $stmt->bindValue(':id', $user['id_usuario']);
        $stmt->execute();
    }
}

echo "Senhas atualizadas para hash!";
