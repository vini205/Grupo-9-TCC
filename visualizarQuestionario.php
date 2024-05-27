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

$sql = "SELECT * FROM questionario  WHERE codAgenda = '$codAgenda';";
$resultado = $conexao->query($sql);
?>
<a href="pagina_professor.php" class="w3-display-top-left">
    <i class="fa fa-arrow-circle-left w3-large w3-teal w3-button w3-xxlarge"></i>     
</a> 
<h1 class="w3-center w3-teal w3-round-large w3-margin">Questionário </h1>

<div class="w3-padding w3-content w3-text-grey w3-margin   " style="margin:auto !important;">
<h1 class="w3-center w3-teal w3-round-large w3-margin">Questões: </h1>
     <div class=" w3-content w3-padding w3-text-grey ">
         
         
         
         <?php 
         $counter = 1;
    while($row = $resultado->fetch_object()){//Embaralhando questionário
        echo "<form action='atualizarQuestoes.php' class='w3-border w3-content w3-margin w3-padding' method='post' >";
        echo "<label class='w3-label w3-padding w3-margin w3- w3-teal' for='questao'>Questão $counter: $row->pergunta </label><br>";
        $embaralhar = [
            ["<input type='radio' class='w3-padding w3-radio w3-margin ' id='op1' value='$row->opcao1' name='$row->codQuestionario'>","<label for='op1' > $row->opcao1</label>"],
            ["<input type='radio' class='w3-padding w3-radio w3-margin ' id='op2' value='$row->opcao2' name='$row->codQuestionario'>","<label for='op2' > $row->opcao2</label>"],
            ["<input type='radio' class='w3-padding w3-radio w3-margin ' id='op3' value='$row->opcao3' name='$row->codQuestionario'>","<label for='op3' > $row->opcao3</label>"],
            ["<input type='radio' class='w3-padding w3-radio w3-margin ' id='op4' value='$row->opcao4' name='$row->codQuestionario'>","<label for='op4' > $row->opcao4</label>"]
        ];
        shuffle($embaralhar);
        for($i = 0; $i<sizeof($embaralhar);$i++){
            
            echo $embaralhar[$i][0];
            echo $embaralhar[$i][1];
            echo '<br>';
        }
        
        echo "<input type='text' value='$row->codQuestionario' name='codQuestionario' hidden>";
        echo '<button name="btnAtualizarQuestionario" class="w3-red w3-button">Atualizar ou Apagar Questão</button>';
        echo "</form>";
        $counter++;
    };

?>
        </div>


    
</div>