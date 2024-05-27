<?php
require_once 'cabecalho.php';

session_start();

switch ($_POST) {
    //Login
    case isset($_POST['btnLogin']):
        login();
        break;

        //Cadastrar
    case isset($_POST['btnCadastrar']):
        cadastro();
        break;

        //Atualizar dados do usuário
    case isset($_POST['btnAtualizarDados']):
        atualizar();
        break;

        //Faz o logout
    case isset($_POST['btnSair']):
        session_destroy();
        header("Location: index.php");
        break;

        //Voltar para página principal do Aluno
    case isset($_POST['btnVoltarAluno']):
        header("Location: pagina_aluno.php");
        break;

        //Voltar para página principal do Docente
    case isset($_POST['btnVoltarDocente']):
        header("Location: pagina_professor.php");
        break;
        
        //ADICIONAR AGENDA
    case isset($_POST['btnAdicionarAgenda']):
        adicionarAgenda();
        break;

        //EXCLUIR AGENDA
    case isset($_POST['btnExcluirAgenda']):
        excluirAgenda($_POST['cod']);
        break;
        //ADICIONAR QUESTÃO
    case isset($_POST['btnAdicionarQuestao']):
        adicionarQuestao();
        break;

        // VER QUESTIONARIO
    case isset($_POST['btnVerQuestionario']):
        echo 'opa';
        verQuestionario($_POST['cod']);
        break;

    // EXCLUIR OU ATUALIZAR QUESTAO
    case isset($_POST['btnAtualizarQuestao']):
        atualizarQuestao($_POST['codQuestionario']);
        break;
    case isset($_POST['btnApagarQuestao']):
        apagarQuestao($_POST['codQuestionario']);
        break;

    // ENVIAR RESPOSTA
    case isset($_POST['btnEnviarResposta']):
        enviarResposta();
        break;

    case isset($_POST['btnVoltar']):
        echo 12;
        echo $_SESSION['professor']; 
        if($_SESSION['professor']){
            //header("Location: pagina_professor.php");
        }else{
            //header("Location: pagina_aluno.php");
        }
        break;
    
}

function conectar(){
    require_once 'ConexaoBD.php';

    $conn = new ConexaoBD();
    $conexao = $conn->conectar();


    if ($conexao->connect_errno) {
        die("Falha na conexão: " . $conexao->connect_error);
    }
    return $conexao;
}

function login()
{
   

    $conexao = conectar();
    $nome = $_POST['txtNome'];
    $senha = $_POST['txtSenha'];
    
    $professor = $_POST['professor'];
    //se for on é professor
    if ($professor == 'on') {
        $sql = "SELECT * FROM pessoa p, docente WHERE docente.username =  '" . $nome . "';";
        $resultado = $conexao->query($sql);
        $linha = mysqli_fetch_array($resultado);
        if ($linha != null) {
            if ($linha['senha'] == $senha) {
                $_SESSION['logado'] = $nome;
                $_SESSION['id_pessoa'] = $linha['id_pessoa'];

                //Avisa a página que é um professor
                $_SESSION['professor'] = true;


                header("Location: pagina_professor.php");
            } else {
                echo '
                    <a href="index.php">
                        <h1 class="w3-button w3-teal">Dados inválidos! </h1>
                    </a> 
                    ';
            }
        } else {
            echo '
                <a href="index.php">
                    <h1 class="w3-button w3-teal">Login Inválido! </h1>
                </a> 
                ';
        }

    } else {// ser for aluno
        $sql = "SELECT * FROM aluno WHERE username =  '" . $nome . "';";
        $resultado = $conexao->query($sql);
        $linha = mysqli_fetch_array($resultado);
        if ($linha != null) {
            if ($linha['senha'] == $senha) {
                $_SESSION['logado'] = $nome;
                //Avisa que não é professor
                $_SESSION['professor'] = false;
                $_SESSION['id_pessoa'] = $linha['id_pessoa'];
                header("Location: pagina_aluno.php");

            } else {
                echo '
                    <a href="index.php" class="w3-cyan w3-link w3-display-middle">
                        <h1 class="w3-button w3-teal">Login Inválido! </h1>
                    </a> 
                    ';
            }
        } else {
            echo '
            <a href="index.php"class="w3-link w3-cyan w3-display-middle">
                <h1 class="w3-button w3-teal">Login Inválido! </h1>
            </a> 
            ';
        }
    }
    

    $conexao->close();

}

function cadastro()
{
    session_start();
    $nome = $_POST['txtNome'];
    $senha = $_POST['txtSenha'];
    
    $conexao = conectar();

    $nome = $_POST['txtNome'];
    $sobrenome = $_POST['txtSobrenome'];

    $data = date("Y-m-d", strtotime($_POST["txtData"]));
    $logradouro = $_POST['txtLogradouro'];
    $numero = $_POST['txtNumero'];
    $complemento = $_POST['txtComplemente'];
    $bairro = $_POST['txtBairro'];
    $cidade = $_POST['txtCidade'];
    $uf = $_POST['txtEstado'];
    $cep = $_POST['txtCEP'];
    $username = $_POST['txtUsuario'];
    $senha = $_POST['txtSenha'];

    $sql = "INSERT INTO `pessoa` (`nome`, `sobrenome`, `data_Nascimento`, `logradouro`, `numero`, `bairro`, `complemento`, `cidade`, `uf`, `cep`) 
    VALUES ('$nome', '$sobrenome', '$data', '$logradouro', '$numero', '$bairro', '$complemento', '$cidade', '$uf', '$cep');";
    

    if ($conexao->query($sql) === TRUE) {
        $id = mysqli_insert_id($conexao);

        $sql2 = "INSERT INTO aluno (id_pessoa,username,senha) 
        VALUES ('$id','$username','$senha'); ";

        if ($conexao->query($sql2)) {
            echo '
            <a href="index.php">
                <h1 class="w3-button w3-display-middle  w3-teal">Cadastro feito com Sucesso! </h1>
            </a> 
            ';
            
        } else {
            echo '
            <a href="index.php">
                <h1 class="w3-button w3-display-middle w3-teal">Erro na atualização! </h1>
            </a> 
            ';
        }

    } else {
        echo '
        <a href="index.php">
            <h1 class="w3-display-middle w3-button w3-red">Erro na conexão! </h1>
        </a> 
        ';
    }

    $conexao->close();
}

function atualizar()
{
    $conexao = conectar();
    //Verificando o tipo de usuário
    if(!isset($_SESSION['professor'])){
        echo 'Não logado';
    }else if( $_SESSION['professor'] == true){
        $tabela = 'docente';
    }else{
        $tabela = 'aluno';
    }
    
    $nome = $_POST['txtNome'];
    $senha = $_POST['txtSenha'];
    $sobrenome = $_POST["txtSobrenome"];
    $data = date("Y-m-d", strtotime($_POST["txtData"]));
    $logradouro = $_POST['txtLogradouro'];
    $numero = $_POST['txtNumero'];
    $complemento = $_POST['txtComplemento'];
    $bairro = $_POST['txtBairro'];
    $cidade = $_POST['txtCidade'];
    $uf = $_POST['txtEstado'];
    $cep = $_POST['txtCEP'];
    $username = $_POST['txtUsuario'];
    $senha = $_POST['txtSenha'];
    $id_pessoa = $_SESSION['id_pessoa'];

    $sql = "UPDATE pessoa INNER JOIN $tabela ON $tabela.id_pessoa = pessoa.id SET pessoa.`nome` = ' $nome ', pessoa.`sobrenome` = '$sobrenome',
    pessoa.`data_Nascimento` = '$data',pessoa.numero = '$numero', pessoa.`logradouro` = '$logradouro', pessoa.`bairro` = '$bairro', pessoa.`complemento` = '$complemento',
     pessoa.`cidade` = '$cidade', pessoa.`uf` = '$uf',
     pessoa.`cep` = '$cep', $tabela.username = '$username', $tabela.senha = '$senha' WHERE `pessoa`.`id` = $id_pessoa ;";
    
    $_SESSION['id_pessoa'] = $id_pessoa; 
    if($conexao->query($sql)){
        if(!$_SESSION['professor']){
            echo '
            <a class="w3-button w3-cyan w3-display-middle" href="pagina_aluno.php">
                <h1 class="w3-button w3-teal">Dados atualizados com Sucesso! </h1>
            </a> 
            ';

        }else{
            echo '
            <a class="w3-button w3-cyan w3-display-middle" href="pagina_professor.php">
                <h1 class="w3-button w3-teal">Dados atualizados com Sucesso! </h1>
            </a> 
            ';
        }
    }else{
        echo '
        <a class="w3-button w3-cyan w3-display-middle" href="pagina_aluno.php">
            <h1 class="w3-button w3-teal">Erro na atualização! </h1>
        </a> 
        ';
    }
}


function adicionarAgenda(){
    $dataIni = $_POST['dataIni'];
    $dataFin = $_POST['dataFin'];
    $disciplina = $_POST['disciplina'];
    $numero = $_POST['numeroAgenda'];

    $conexao = conectar();
    //Verificando o tipo de usuário
    
    $sql = "INSERT INTO `agenda` (`disciplina`, `dataInicial`, `dataFinal`, `numAgenda`) 
    VALUES ('$disciplina', '$dataIni', '$dataFin', '$numero')";
    


    if ($conexao->query($sql) === TRUE) {
        $id = mysqli_insert_id($conexao);
        echo '
        <a href="criarAgenda.php"class="w3-display-middle">
            <h1 class="w3-button w3-display-middle w3-teal">Agenda Criada com sucesso! </h1>
        </a> 
        ';
        }else{
            echo '
            <a href="criarAgenda.php" >
                <h1 class="w3-button w3-red">Erro na conexão! </h1>
            </a> 
            ';
        }

}

function excluirAgenda($cod){
    
    $conexao = conectar();
    //Verificando o tipo de usuário
    
    $sql = "DELETE FROM agenda WHERE codAgenda = $cod";
    


    if ($conexao->query($sql) === TRUE) {
        $id = mysqli_insert_id($conexao);
        echo '
        <a href="criarAgenda.php" class="w3-display-middle">
            <h1 class="w3-button w3-teal">Agenda Excluida com sucesso! </h1>
        </a> 
        ';
        }else{
            echo '
            <a href="criarAgenda.php">
                <h1 class="w3-button w3-red">Erro na exclusão! </h1>
            </a> 
            ';
        }
}

function adicionarQuestao(){
    $disciplina = $_POST['txtMateria'];
    $agenda = $_POST['txtAgenda'];
    $pergunta = $_POST['txtPergunta'];
    $o1 = $_POST['txtOpcao1'];
    $o2 = $_POST['txtOpcao2'];
    $o3 = $_POST['txtOpcao3'];
    $o4 = $_POST['txtOpcao4'];

    $conexao = conectar();
    //Verificando o tipo de usuário
    
    $sql = "INSERT INTO `questionario` (`disciplina`, `pergunta`, `codAgenda`, `opcao1`, `opcao2`, `opcao3`, `opcao4`) 
    VALUES ('$disciplina', '$pergunta', '$agenda', '$o1', '$o2', '$o3', '$o4')";
    


    if ($conexao->query($sql) === TRUE) {
        $id = mysqli_insert_id($conexao);
        echo '
        <a href="pagina_professor.php" class="w3-display-middle">
            <h1 class="w3-button w3-teal">Questionário feito com sucesso! </h1>
        </a> 
        ';
        }else{
            echo '
            <a href="pagina_professor.php">
                <h1 class="w3-button w3-red">Erro no processo! </h1>
            </a> 
            ';
        }
}

function verQuestionario($codAgenda){
    
    $_SESSION['codAgenda'] = $codAgenda;
    
    
    //Verificando o tipo de usuário
    if( $_SESSION['professor'] == true){
        header("Location: visualizarQuestionario.php");
    }else{
        header("Location: responderQuestionario.php");
    } 
}

function atualizarQuestao($codQuestionario){
    $disciplina = $_POST['txtMateria'];
    $agenda = $_POST['txtAgenda'];
    $pergunta = $_POST['txtPergunta'];
    $o1 = $_POST['txtOpcao1'];
    $o2 = $_POST['txtOpcao2'];
    $o3 = $_POST['txtOpcao3'];
    $o4 = $_POST['txtOpcao4'];

    $conexao = conectar();
    
    $sql = "UPDATE `questionario` SET `disciplina` = '$disciplina', `pergunta` = '$pergunta', `codAgenda` = '$agenda', 
    `opcao1` = '$o1', `opcao2` = '$o2', `opcao3` = '$o3', `opcao4` = '$o4' WHERE `questionario`.`codQuestionario` = $codQuestionario";
    


    if ($conexao->query($sql) === TRUE) {
        $id = mysqli_insert_id($conexao);
        echo '
        <a href="visualizarQuestionario.php" class="w3-display-middle">
            <h1 class="w3-button w3-teal">Questionário atualizado com sucesso! </h1>
        </a> 
        ';
        }else{
            echo '
            <a href="pagina_professor.php">
                <h1 class="w3-button w3-red">Erro no processo! </h1>
            </a> 
            ';
        }
}

function apagarQuestao($codQuestionario){
    $conexao = conectar();

    $sql = "DELETE FROM questionario WHERE `questionario`.`codQuestionario` = $codQuestionario";
    


    if ($conexao->query($sql) === TRUE) {
        $id = mysqli_insert_id($conexao);
        echo '
        <a href="SelecionarQuestionarios.php" class="w3-display-middle">
            <h1 class="w3-button w3-teal">Questão apagada com sucesso! </h1>
        </a> 
        ';
        }else{
            echo '
            <a href="pagina_professor.php">
                <h1 class="w3-button w3-red">Erro no processo! </h1>
            </a> 
            ';
        }
}

function enviarResposta(){
    $idPessoa = $_SESSION['id_pessoa'];
    $conexao = conectar();
    $codAgenda = $_SESSION['codAgenda'];   
    $nota =0;

    $sql = "SELECT * FROM questionario  WHERE codAgenda = '$codAgenda';";
    $resultado = $conexao->query($sql);
    $qtdQuestoes = 0;
    //vê no banco de dados qual é a resposta certa e confere com a do aluno
    while($row = $resultado->fetch_object()){
        $qtdQuestoes++;
        $resposta = $_POST[$row->codQuestionario];
        $certa = $row->opcao1;
        if($resposta == $certa){
            $nota++;
        }
    }
    $nota = 10*($nota/$qtdQuestoes);
    
    //Resgatando dados
    
    $login = $_SESSION['logado'];
    $sql = "SELECT codAluno FROM  aluno INNER JOIN pessoa p ON aluno.id_pessoa = p.id WHERE aluno.username = '$login'";
    $resultado = $conexao->query($sql);
    $usuario = $resultado->fetch_assoc();
    $codAluno = $usuario['codAluno'];

    $sql = "INSERT INTO `nota` ( `codAluno`, `codAgenda`, `nota`) VALUES ( '$codAluno', '$codAgenda', '$nota')";

    if ($conexao->query($sql) === TRUE) {
        echo "
        <a href='pagina_aluno.php' class='w3-display-middle'>
            <h1 class='w3-button w3-teal'>Nota registrada com Sucesso! Nota: $nota </h1>
        </a> 
        ";
        }else{
            echo '
            <a href="pagina_aluno.php">
                <h1 class="w3-button w3-red">Erro! </h1>
            </a> 
            ';
        }
    
}