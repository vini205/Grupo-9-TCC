
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
    echo 'Não logado';
}
$login = $_SESSION['logado'];

$sql = "SELECT disciplina FROM  docente INNER JOIN pessoa p ON docente.id_pessoa = p.id WHERE docente.username = '$login'";
    
$resultado = $conexao->query($sql);
$professor = $resultado->fetch_assoc();


?>
<a href="pagina_professor.php" class="w3-display-topleft">
    <i class="fa fa-arrow-circle-left w3-large w3-teal w3-button w3-xxlarge"></i>     
</a> 

    <div class="w3-padding w3-content  w3-text-grey w3-third w3-margin " onclick="myFunction()">
    <h1 class="w3-center w3-teal w3-round-large w3-margin">Criar agenda</h1>
        <form action="Action.php" class="w3-content w3-margin" method='post'>

            <label class="w3-text-teal" style="font-weight: bold;">Disciplina</label>
            <input name="disciplina" value="<?= $professor['disciplina']?>" class="w3-input w3-light-grey w3-border"><br>

            <label for="Agenda" class="w3-text-teal w3-label" style="font-weight: bold;"> Agenda</label>
            <input name="numeroAgenda" required type="number" class="w3-input w3-light-grey w3-border"><br>
            
            <label class="w3-text-teal"  style="font-weight: bold;">Data Inicial</label>
            <input name="dataIni" required type="date" class="w3-input w3-light-grey w3-border"><br>

            <label class="w3-text-teal" style="font-weight: bold;">Data Final</label>
            <input name="dataFin" type="date" required class="w3-input w3-light-grey w3-border"><br>
            
            <button name="btnAdicionarAgenda" class="w3-button w3-teal w3-cell w3-round-large w3-right w3-margin-right"> 
                <i class="w3-xxlarge fa fa-user-plus"></i> Adicionar
            </button>
        </form>
</div>



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
            <th>Excluir</th>
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
                            <button name="btnExcluirAgenda" class="w3-button w3-block w3-teal
                            w3-cell w3-round-large"> <i class="fa fa-user-times"></i> </button></td>
                            </form>';
        echo '</tr>';
        }
        ?>


        </table>

    </div>
</div>

<?php require_once ('rodape.php'); ?>