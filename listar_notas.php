<?php require_once('cabecalho.php');

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

if(!isset($_SESSION['codAgenda'])){
    echo 'ERROR';
}
$codAgenda = $_SESSION['codAgenda'];
$codAluno = $_SESSION['codAluno'];

$sql = "SELECT * FROM nota INNER JOIN agenda ON nota.codAgenda WHERE nota.codAluno = $codAluno ;";
$resultado = $conexao->query($sql);
?>
<a href="pagina_aluno.php" class="w3-display-top-left">
    <i class="fa fa-arrow-circle-left w3-large w3-teal w3-button w3-xxlarge"></i>     
</a> 
<h1 class="w3-center w3-teal w3-round-large w3-margin">Minhas notas </h1>

<div class="w3-padding w3-content w3-text-grey w3-margin   " style="margin:auto !important;">
<h1 class="w3-center w3-teal w3-round-large w3-margin">Tabelas de notas: </h1>
     <div class=" w3-content w3-padding w3-text-grey ">
         
     <table class="w3-table-all w3-centered">
        <thead>
            <tr class="w3-center w3-teal">
            <th>Número da Agenda</th>
            <th>Disciplina</th>
            <th>Nota</th>
            </tr>
        </thead>
        <?php
        
        while($row = $resultado->fetch_object()) {
        echo '<tr>';
        echo '<td style="width: 1%;">'.$row->numAgenda.'</td>';
        echo '<td style="width: 30%;">'.$row->disciplina.'</td>';
        echo '<td style="width: 30%;">'.$row->nota.'</td>';
        echo '</tr>';
        }
        ?>


        </table>
   
        </div>


    
</div>