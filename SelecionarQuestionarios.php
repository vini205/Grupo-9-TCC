<?php require_once ('cabecalho.php');

session_start();
require_once 'ConexaoBD.php';
$conn = new ConexaoBD();
$conexao = $conn->conectar();
if( $conexao->connect_errno){
    die("Falha na conexão: " . $conexao->connect_error);
}

// Recuperando o nome do professor com base no login da sessão
if(!isset($_SESSION['logado'])){
    header("Location: usuarioNaoLogado.php");
}

$sql = "SELECT * FROM questionario q INNER JOIN agenda a ON q.codAgenda WHERE q.codAgenda = a.codAgenda;"
?>
<form action="Action.php" class="w3-display-top-left">
    <button name="btnVoltar">
        <i class="fa fa-arrow-circle-left w3-large w3-teal w3-button w3-xxlarge"></i>     
    </button>
</form>

<h1 class="w3-center w3-teal w3-round-large w3-margin">Selecione a agenda</h1>

<div class="w3-padding w3-content w3-text-grey w3-third w3-margin " style="margin:auto;">
<h1 class="w3-center w3-teal w3-round-large w3-margin">Lista de agendas</h1>
     <div class=" w3-content w3-text-grey">
    <div class="w3-container">
        <table class="w3-table-all w3-centered">
        <thead>
            <tr class="w3-center w3-teal">
            <th>Número da Agenda</th>
            <th>Disciplina</th>
            <th>Data Inicial</th>
            <th>Data Final</th>
            <th>Selecionar</th>
            </tr>
        </thead>
        <?php
        $sql = "SELECT numAgenda, disciplina,dataInicial, dataFinal,codAgenda FROM  agenda;";
        $resultado = $conexao->query($sql);
        
        while($row = $resultado->fetch_object()) {
        echo '<tr>';
        echo '<td style="width: 1%;">'.$row->numAgenda.'</td>';
        echo '<td style="width: 30%;">'.$row->disciplina.'</td>';
        echo '<td style="width: 30%;">'.$row->dataInicial.'</td>';
        echo '<td style="width: 30%;">'.$row->dataFinal.'</td>';
        echo '<td style="width: 5%;"> <form action="Action.php" method="post">
                            <input type="hidden" name="cod" value="'.$row->codAgenda.'">
                            <button name="btnVerQuestionario" class="w3-button w3-block w3-teal
                            w3-cell w3-round-large"> <i class="fa fa-arrow-right"></i> </button></td>
                            </form>';
        echo '</tr>';
        }
        ?>


        </table>

    </div>
</div>