<?php
//verifica se existe uma mensagem armazenada
if (isset($_SESSION['mensagem'])):
?>
<p class="mensagem" role="alert">
    <?= $_SESSION['mensagem']; ?> 
</p>
<?php
    //após exibir, remove a mensagem da sessão
    unset($_SESSION['mensagem']);
    endif;
?>