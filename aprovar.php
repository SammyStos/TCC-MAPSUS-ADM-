<?php
ob_start();
session_start();
include 'conexao.php'; 
require_once('src/PHPMailer.php');
require_once('src/SMTP.php');
require_once('src/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


function gerarUsuario($nome) {
    return strtolower(preg_replace('/\s+/', '', $nome)) . rand(100, 999);
}

function gerarSenha() {
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*'), 0, 6);
}

function enviarEmail($email, $assunto, $corpoHtml, $corpoTexto) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mapsus2024@gmail.com'; 
        $mail->Password = 'xptxcmywybxwnphw';     
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('mapsus2024@gmail.com', 'Mapsus');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body = $corpoHtml;
        $mail->AltBody = $corpoTexto;
        $mail->send();
    } catch (Exception $e) {
        echo "Erro ao enviar e-mail: {$mail->ErrorInfo}";
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $acao = $_GET['acao']; 

    $sql = "SELECT nome, email FROM notificacoes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($hospital, $email);
    $stmt->fetch();
    $stmt->close();

    if ($acao == "aceitar") {
        $sql = "SELECT COUNT(*) FROM hospitais WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($emailExistente);
        $stmt->fetch();
        $stmt->close();

        if ($emailExistente > 0) {
            echo "Erro: Este e-mail já está registrado.";
            exit();
        }

        $usuario = gerarUsuario($hospital);
        $senha = gerarSenha();

        $sql = "INSERT INTO hospitais (Nome, Email, Usuario, Senha) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $hospital, $email, $usuario, $senha);

        if ($stmt->execute()) {
            $corpoHtml = "
                <h1>Cadastro aprovado!</h1>
                <p>Olá, $hospital!</p>
                <p>Seu acesso foi liberado. Aqui estão suas credenciais:</p>
                <p><strong>Usuário:</strong> $usuario</p>
                <p><strong>Senha:</strong> $senha</p>
            ";
            $corpoTexto = "Cadastro aprovado! Usuário: $usuario, Senha: $senha";
            enviarEmail($email, 'Acesso Aprovado', $corpoHtml, $corpoTexto);

            $sql = "UPDATE notificacoes SET lida = 1 WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            header("Location: indexdash.php?status=aprovado");
            exit();
        } else {
            echo "Erro ao registrar hospital.";
        }
    } elseif ($acao == "recusar") {
        $corpoHtml = "
            <h1>Cadastro recusado</h1>
            <p>Olá, $hospital!</p>
            <p>Infelizmente, seu cadastro foi recusado.</p>
        ";
        $corpoTexto = "Cadastro recusado";
        enviarEmail($email, 'Cadastro Recusado', $corpoHtml, $corpoTexto);

        $sql = "UPDATE notificacoes SET lida = 2 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        header("Location: indexdash.php?status=recusado");
        exit();
    }
}
$conn->close();
ob_end_flush();
?>
