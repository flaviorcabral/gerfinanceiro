<?php

class Util
{

    public static $util;

    //Retorna instancia
    public static function getUtil()
    {
        if(!isset(self::$util)){

            self::$util = new Util();

            return self::$util;
        }
    }

    //Traduz data para persistencia no banco
    function traduz_data_para_banco($data)
    {
        if ($data == "") {
            return "";
        }

        $dados = explode("-", $data);

        $data_mysql = "{$dados[2]}-{$dados[1]}-{$dados[0]}";

        return $data_mysql;
    }

    //Traduz data para exibição
    function traduz_data_para_exibir($data)
    {
        if ($data == "" OR $data == "00-00-0000") {
            return "";
        }

        $dados = explode("-", $data);

        $data_exibir = "{$dados[2]}/{$dados[1]}/{$dados[0]}";

        return $data_exibir;
    }

    //Prepara corpo do email de nova senha
    function preparar_corpo_email_novasenha()
    {
        ob_start();
        include "template_email_novasenha.php";

        $corpo = ob_get_contents();

        ob_end_clean();

        return $corpo;
    }

    function preparar_corpo_email_c()
    {
        ob_start();
        include "template_email_c.php";

        $corpo = ob_get_contents();

        ob_end_clean();

        return $corpo;
    }

    function montar_email()
    {

        $corpo = "
        <html>
            <head>
                <meta charset=\"utf-8\" />
                <title>Gerenciador de Tarefas</title>
                <link rel=\"stylesheet\" href=\"tarefas.css\" type=\"text/css\" />
            </head>
            <body>
                <h1>Tarefa: {$tarefa['nome']}</h1>

                <p><strong>Concluída:</strong> " . traduz_concluida($tarefa['concluida']) . "</p>
                <p><strong>Descrição:</strong> " . nl2br($tarefa['descricao']) . "</p>
                <p><strong>Prazo:</strong> " . traduz_data_para_exibir($tarefa['prazo']) . "</p>
                <p><strong>Prioridade:</strong> " . traduz_prioridade($tarefa['prioridade']) . "</p>

                {$tem_anexos}

            </body>
        </html>
    ";
    }

    function formatar($string, $tipo = "")
    {

        //$string = ereg_replace("[^0-9]", "", $string);
        $string = preg_replace("/[^0-9]/", "", $string);

        if (!$tipo) {
            switch (strlen($string)) {
                case 10:
                    $tipo = 'fone';
                    break;
                case 8:
                    $tipo = 'cep';
                    break;
                case 11:
                    $tipo = 'cpf';
                    break;
                case 14:
                    $tipo = 'cnpj';
                    break;
            }
        }
        switch ($tipo) {
            case 'fone':
                $string = '(' . substr($string, 0, 2) . ') ' . substr($string, 2, 4) .
                    '-' . substr($string, 6);
                break;
            case 'cep':
                $string = substr($string, 0, 5) . '-' . substr($string, 5, 3);
                break;
            case 'cpf':
                //$string = substr($string, 0, 3) . '.' . substr($string, 3, 3) .
                //    '.' . substr($string, 6, 3) . '-' . substr($string, 9, 2);
                break;
            case 'cnpj':
                //$string = substr($string, 0, 2) . '.' . substr($string, 2, 3) .
                //    '.' . substr($string, 5, 3) . '/' .
                //    substr($string, 8, 4) . '-' . substr($string, 12, 2);
                break;
            case 'rg':
                $string = substr($string, 0, 2) . '.' . substr($string, 2, 3) .
                    '.' . substr($string, 5, 3);
                break;
        }
        return $string;
    }

    function formata($string, $tipo = "")
    {

        //$string = ereg_replace("[^0-9]", "", $string);
        $string = preg_replace("/[^0-9]/", "", $string);

        if (!$tipo) {
            switch (strlen($string)) {
                case 10:
                    $tipo = 'fone';
                    break;
                case 8:
                    $tipo = 'cep';
                    break;
                case 11:
                    $tipo = 'cpf';
                    break;
                case 14:
                    $tipo = 'cnpj';
                    break;
            }
        }
        switch ($tipo) {
            case 'fone':
                $string = '(' . substr($string, 0, 2) . ') ' . substr($string, 2, 4) .
                    '-' . substr($string, 6);
                break;
            case 'cep':
                $string = substr($string, 0, 5) . '-' . substr($string, 5, 3);
                break;
            case 'cpf':
                $string = substr($string, 0, 3) . '.' . substr($string, 3, 3) .
                    '.' . substr($string, 6, 3) . '-' . substr($string, 9, 2);
                break;
            case 'cnpj':
                $string = substr($string, 0, 2) . '.' . substr($string, 2, 3) .
                    '.' . substr($string, 5, 3) . '/' .
                    substr($string, 8, 4) . '-' . substr($string, 12, 2);
                break;
            case 'rg':
                $string = substr($string, 0, 2) . '.' . substr($string, 2, 3) .
                    '.' . substr($string, 5, 3);
                break;
        }
        return $string;
    }

    function checauf($uf)
    {
        if (!empty($uf)) {
            $uf = strtoupper($uf);
            $flag = false;
            $aUF = array("AC", "AL", "AM", "AP", "BA", "CE", "DF", "ES", "GO", "MA", "MG", "MS", "MT", "PA", "PB", "PE", "PI", "PR", "RJ", "RN", "RO", "RR", "RS", "SC", "SE", "SP", "TO");
            if (in_array($uf, $aUF)) {
                $flag = true;
            }
        } else {
            $flag = true;
        }
        return ($flag);
    }

    //Pegar Ip da maquina
    function getIp()
    {

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {

            $ip = $_SERVER['HTTP_CLIENT_IP'];

        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

        } else {

            $ip = $_SERVER['REMOTE_ADDR'];

        }

        return $ip;

    }

    Function Busca_ultimo_dia_mes($mes, $ano)
    {

        $mes++;
        If ($mes < 10) {
            $mes = "0" . $mes;
        }

        $data1 = $ano . "-" . $mes . "-01";
        $data2 = date('Y-m-d', strtotime("-1 days", strtotime($data1)));
        $aData = explode("-", $data2);
        $dia = $aData[2];
        return ($dia);
    }

    Function BuscaNumeroMes($mes)
    {
        $numero = 0;
        $mes = substr($mes, 0, 3);

        switch ($mes) {
            case 'Jan':
                $numero = "01";
                break;
            case 'Fev':
                $numero = "02";
                break;
            case 'Mar':
                $numero = "03";
                break;
            case 'Abr':
                $numero = "04";
                break;
            case 'Mai':
                $numero = "05";
                break;
            case 'Jun':
                $numero = "06";
                break;
            case 'Jul':
                $numero = "07";
                break;
            case 'Ago':
                $numero = "08";
                break;
            case 'Set':
                $numero = "09";
                break;
            case 'Out':
                $numero = "10";
                break;
            case 'Nov':
                $numero = "11";
                break;
            case 'Dez':
                $numero = "12";
                break;
            default:
                break;
        };

        return ($numero);
    }

    function mask($val, $mask)
    {
        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k]))
                    $maskared .= $val[$k++];
            } else {
                if (isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }

    function enviar_email($paraquem, $dequem, $assunto, $corpo)
    {

        //include "lib/PHPMailer/PHPMailerAutoload.php";

        //require 'lib/PHPMailer/PHPMailerAutoload.php';

        include("../../lib/PHPMailer/class.phpmailer.php");
        include("../../lib/PHPMailer/class.smtp.php");

        $email = new PHPMailer(); // Esta é a criação do objeto
        $email->CharSet = 'UTF-8';
        $email->isSMTP();
        $email->Host = "mail.everdeeninformatica.com.br";
        $email->Port = 587;
        $email->SMTPSecure = 'TSL';
        $email->SMTPAuth = true;
        $email->Username = "suporte@everdeeninformatica.com.br";
        $email->Password = "palio2001";
        $email->setFrom($dequem, "Everdeen Informática");

        // Digitar o e-mail do destinatário;
        $email->addAddress($paraquem);

        // Digitar o assunto do e-mail;
        $email->Subject = $assunto;

        //Escrever o corpo do e-mail;
        //$corpo = preparar_corpo_email($tarefa, $anexos);
        $email->msgHTML($corpo);

        // Usar a opção de enviar o e-mail.
        $email->send();
        //if ($email->send()){
        //echo "enviado";
        //}
        //else {
        //echo "Mensagem de erro: " . $email->ErrorInfo;
        //}

        return;
    }

    function get_post_action($name)
    {
        $aDados = array();
        $params = func_get_args();

        //print_r($params);

        foreach ($params as $name) {
            if (isset($_POST[$name])) {
                return $name;
            }
        }
    }

    function e_numero($val)
    {
        $flag = false;
        if (is_numeric($val)) {
            $flag = true;
        }
        return $flag;
    }

    function Traz_Data_Por_Extenso($hoje = null)
    {

        if (empty($hoje)) {
            $hoje = getdate();
        }

        $meses = array(1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril", 5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto", 9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro");

        $diasdasemana = array(1 => "Segunda-Feira", 2 => "Terça-Feira", 3 => "Quarta-Feira", 4 => "Quinta-Feira", 5 => "Sexta-Feira", 6 => "Sábado", 0 => "Domingo");

        //$hoje = getdate();

        $dia = $hoje["mday"];

        $mes = $hoje["mon"];

        $nomemes = $meses[$mes];

        $ano = $hoje["year"];

        $diadasemana = $hoje["wday"];

        $nomediadasemana = $diasdasemana[$diadasemana];

        $data = "$nomediadasemana, $dia de $nomemes de $ano";

        return ($data);
    }

    function Traz_Mes_Data($hoje = null)
    {

        $meses = array(1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril", 5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto", 9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro");

        if (empty($hoje)) {
            $hoje = getdate();
        }

        $dia = $hoje["mday"];

        $mes = $hoje["mon"];

        $nomemes = $meses[$mes];

        return ($nomemes);
    }

    function validaCPF($cpf)
    {
        return true;
        // Verifiva se o número digitado contém todos os digitos
        //$cpf = str_pad(ereg_replace('[^0-9]', '', $cpf), 11, '0', STR_PAD_LEFT);
        $cpf = str_pad(preg_replace('/[^0-9]/', '', $cpf), 11, '0', STR_PAD_LEFT);

        // Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
        if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
            //return false;
            return true;
        } else {   // Calcula os números para verificar se o CPF é verdadeiro
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }

                $d = ((10 * $d) % 11) % 10;

                if ($cpf{$c} != $d) {
                    //return false;
                    return true;
                }
            }

            return true;
        }
    }

    function validaCNPJ($cnpj)
    {
        return true;
        // Deixa o CNPJ com apenas números
        //$cnpj = preg_replace( '/[^0-9]/', '', $cnpj );
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        // Garante que o CNPJ é uma string
        $cnpj = (string)$cnpj;

        // O valor original
        $cnpj_original = $cnpj;

        // Captura os primeiros 12 números do CNPJ
        $primeiros_numeros_cnpj = substr($cnpj, 0, 12);

        /**
         * Multiplicação do CNPJ
         *
         * @param string $cnpj Os digitos do CNPJ
         * @param int $posicoes A posição que vai iniciar a regressão
         * @return int O
         *
         */
        if (!function_exists('multiplica_cnpj')) {
            function multiplica_cnpj($cnpj, $posicao = 5)
            {
                // Variável para o cálculo
                $calculo = 0;

                // Laço para percorrer os item do cnpj
                for ($i = 0; $i < strlen($cnpj); $i++) {
                    // Cálculo mais posição do CNPJ * a posição
                    $calculo = $calculo + ($cnpj[$i] * $posicao);

                    // Decrementa a posição a cada volta do laço
                    $posicao--;

                    // Se a posição for menor que 2, ela se torna 9
                    if ($posicao < 2) {
                        $posicao = 9;
                    }
                }
                // Retorna o cálculo
                return $calculo;
            }
        }

        // Faz o primeiro cálculo
        $primeiro_calculo = multiplica_cnpj($primeiros_numeros_cnpj);

        // Se o resto da divisão entre o primeiro cálculo e 11 for menor que 2, o primeiro
        // Dígito é zero (0), caso contrário é 11 - o resto da divisão entre o cálculo e 11
        $primeiro_digito = ($primeiro_calculo % 11) < 2 ? 0 : 11 - ($primeiro_calculo % 11);

        // Concatena o primeiro dígito nos 12 primeiros números do CNPJ
        // Agora temos 13 números aqui
        $primeiros_numeros_cnpj .= $primeiro_digito;

        // O segundo cálculo é a mesma coisa do primeiro, porém, começa na posição 6
        $segundo_calculo = multiplica_cnpj($primeiros_numeros_cnpj, 6);
        $segundo_digito = ($segundo_calculo % 11) < 2 ? 0 : 11 - ($segundo_calculo % 11);

        // Concatena o segundo dígito ao CNPJ
        $cnpj = $primeiros_numeros_cnpj . $segundo_digito;

        // Verifica se o CNPJ gerado é idêntico ao enviado
        if ($cnpj === $cnpj_original) {
            return true;
        }
    }

    function GerarSenha($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false)
    {
        $lmin = 'abcdefghijklmnopqrstuvwxyz';
        $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '1234567890';
        $simb = '!@#$%*-';
        $retorno = '';
        $caracteres = '';
        $caracteres .= $lmin;
        if ($maiusculas) $caracteres .= $lmai;
        if ($numeros) $caracteres .= $num;
        if ($simbolos) $caracteres .= $simb;
        $len = strlen($caracteres);
        for ($n = 1; $n <= $tamanho; $n++) {
            $rand = mt_rand(1, $len);
            $retorno .= $caracteres[$rand - 1];
        }
        return $retorno;
    }

    function EnviarEmail($paraquem, $dequem, $assunto, $corpo)
    {

        //include "lib/PHPMailer/PHPMailerAutoload.php";

        //require 'lib/PHPMailer/PHPMailerAutoload.php';

        include("../../lib/PHPMailer/class.phpmailer.php");
        include("../../lib/PHPMailer/class.smtp.php");

        //$dequem="dataagro@dataagro.com.br";

        $aPara = ConsultarDados("parametros", "", "", "select * from parametros");
        $demaile = $aPara[0]["demaile"];
        $desenhe = $aPara[0]["desenhe"];

        $email = new PHPMailer(); // Esta é a criação do objeto
        $email->CharSet = 'UTF-8';
        $email->isSMTP();
        $email->Host = "mail.rcx5plasticos.com.br";
        $email->Port = 587;
        $email->SMTPSecure = 'TSL';
        $email->SMTPAuth = true;
        //$email->Username = "dataagro@dataagro.com.br";
        $email->Username = $demaile;
        $email->Password = $desenhe;
        $email->setFrom($dequem, "H2O Hybrid");

        // Digitar o e-mail do destinatário;
        $email->addAddress($paraquem);

        // Digitar o assunto do e-mail;
        $email->Subject = $assunto;

        //Escrever o corpo do e-mail;
        //$corpo = preparar_corpo_email($tarefa, $anexos);
        $email->msgHTML($corpo);

        // Usar a opção de enviar o e-mail.
        $email->send();
        //if ($email->send()){
        //    echo "enviado";
        //}
        //else {
        //    echo "Mensagem de erro: " . $email->ErrorInfo;
        //}

        return;
    }

    function enviarSenha()
    {
        $demail= $_POST ["demail"];					// email para o qual será enviada a nova senha
        $desenh= GerarSenha(); 	// gera automaticamente uma nova senha

        // envia para o e-mail
        $paraquem= $demail;
        $dequem = "suporte@rcx5plasticos.com.br";
        $assunto = "Aliança Auto Mecânica : Sua nova senha";
        //$corpo = "cpf: ".$_POST["cdusua"]." - email: ".$_POST["demail"]." - ".$datahoje;
        $corpo="Olá, segue sua nova senha conforme solicitado: ".$desenh;

        $this->EnviarEmail($paraquem, $dequem, $assunto, $corpo);

        // grava a nova senha
        $desenh= md5($desenh);
        $this->GravarNovaSenha($demail,$desenh);

        $aDados = ConsultarDados("m_usuarios", "demail", $demail,"select * from m_usuarios where flativ ='S' and demail = "."'{$demail}'");
        $cdusua="99999999999";

        if (count($aDados) > 0 ) {
            $cdusua=$aDados[0]["cdusua"];
        }

        $delog = "Geração automática de senha e envio de nova senha para o e-mail informado: Esqueceu a Senha";
        GravarLog($cdusua, $delog);

        // apresenta mensagem enviada
        $demens = "Uma nova senha foi enviada para o e-mail informado!";
        $detitu = "Aliança Auto Mecânica | Nova Senha";
        $devolt = "index.html";
        header('Location: mensagem.php?demens='.$demens.'&detitu='.$detitu.'&devolt='.$devolt);
    }

    function RetirarMascara($key, $tipo)
    {
        if (empty($key) == true) {
            //$key=str_replace(".","",$key);
            //$key=str_replace("/","",$key);
            //$key=str_replace("-","",$key);
            if ($tipo == "cpf") {
                $key = str_pad(preg_replace('/[^0-9]/', '', $key), 11, '0', STR_PAD_LEFT);
                //$key=str_pad($key, 11, "0", STR_PAD_LEFT);
            } Else {
                $key = str_pad(preg_replace('/[^0-9]/', '', $key), 14, '0', STR_PAD_LEFT);
            }
        }
        return ($key);
    }

    //Gera informação log do sistema
    function geraLogSistema($cdusua, $delog)
    {
        $dtlog = date('Y-m-d H:i:s');
        $iplog = $this->getIp();

        $aNomes = array();
        $aNomes[] = "cdusua";
        $aNomes[] = "dtlog";
        $aNomes[] = "delog";
        $aNomes[] = "iplog";
        $aNomes[] = "flativ";

        $aDados = array();
        $aDados[] = $cdusua;
        $aDados[] = $dtlog;
        $aDados[] = $delog;
        $aDados[] = $iplog;
        $aDados[] = "S";

        $sql="insert into "."logs"." (";
        $campos="";

        $total=count($aNomes)-1;

        for ($i=0 ; $i < count($aNomes) ; $i++ ) {

            $campos=$campos.$aNomes[$i];

            if ($i < $total) {
                $campos=$campos.", ";
            }
        }

        $sql=$sql.$campos.") values (";

        $campos="";

        for ($x =0 ; $x < count($aDados) ; $x++ ) {

            $campo="'".$aDados[$x]."'";

            if ($x < $total) {
                $campos=$campos.$campo.", ";
            } Else {
                $campos=$campos.$campo.")";
            }
        }

        $sql=$sql.$campos;

        $log = new logsistema();

        if($log->salvarLog($sql)){

            return true;

        }

        return false;
    }

    function BuscaChaveArray($aDados, $chave)
    {
        $aMatriz = array();
        foreach ($aDados as $surname => $names) {
            if (in_array($chave, $names)) {
                $aMatriz = $names;
                return $aMatriz;
            }
        }
        return $aMatriz;
    }

    function data_info()
    {
        $dia = date("j") - 1;
        $hora = date("H") - 3;
        $minuto = date("i");
        $segundo = date("s");

        $semana = array(0 => "Domingo", 1 => "Segunda", 2 => "Terça", 3 => "Quarta", 4 => "Quinta", 5 => "Sexta", 6 => "Sábado");
        $mes = array(1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril", 5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto", 9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro");

        $ano = date("Y");
        $data_completa = date("d/m/y");
        $hora_completa = $hora . ":" . $minuto . ":" . $segundo;
        $misc = $semana[date("w")] . ", " . date("j") . " de " . $mes[date("n")] . " de " . date("Y");
        return;
    }

    function data($data, $formato = 12)
    {
        $hora = $formato == 12 ? "h" : "H";
        $am_pm = (date("H", strtotime($data)) < 12) ? " AM" : " PM";
        $am_pm = $formato == 24 ? "" : $am_pm;
        if (date('d/m/Y', strtotime($data)) == date('d/m/Y')) {
            return date("$hora:i", strtotime($data)) . $am_pm;
        } else if (date('m/Y', strtotime($data)) == date('m/Y') && date("d", strtotime($data)) == date("d") - 1) {
            return "Ontem às " . date("$hora:i", strtotime($data)) . $am_pm;
        } else if (date('m/Y', strtotime($data)) == date('m/Y') && date("d", strtotime($data)) == date("d") + 1) {
            return "Amanha às " . date("$hora:i", strtotime($data)) . $am_pm;
        } else {
            return date("d/m/Y", strtotime($data));
        }
    }

    //Trata entrada de valores
    function trataEntradaValor($valor)
    {
        $valor = str_replace(".", "", $valor); // Primeiro tira os pontos
        $valor = str_replace(",", "", $valor); // Depois tira a vírgula

        if (is_numeric($valor)) { //Testa valor de entrada se numero
            return $valor;
        } else {
            return '';
        }
    }

    //Trata saida de valores para exibição
    function trataSaidaValor($valor)
    {
        $total = strlen($valor); // Pega tamanho da variavel valor
        $subtrair = $total - 2;
        $final = number_format(substr($valor, 0, $subtrair) . '.' . substr($valor, $subtrair, $total), 2, ',', '.'); // Formata saida valor

        return $final;
    }

}

?>