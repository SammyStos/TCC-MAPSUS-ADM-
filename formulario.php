<?php
session_start();
include "db_conn.php";

if (isset($_POST['enviar'])) {
    $nome = htmlspecialchars($_POST['nome']);
    $email = htmlspecialchars($_POST['email']);
    $mensagem = htmlspecialchars($_POST['mensagem']);
    $usuario_id = $_SESSION['id']; 

    $sql = "INSERT INTO notificacoes (usuario_id, nome, email, mensagem) VALUES (?, ?, ?, ?)";
    $msg = $conn->prepare($sql);
    $msg->execute([$usuario_id, $nome, $email, $mensagem]);

    
    echo "<p>Notificação enviada com sucesso!</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Notificação</title>
</head>
<style>
    
    </style>
<body>
    <h1>Enviar Notificação</h1>
    <form method="post" action="">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required><br><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <label for="mensagem">Mensagem:</label>
        <textarea id="mensagem" name="mensagem" required></textarea><br><br>
        <button type="submit" name="enviar">Enviar Notificação</button>
    </form>
</body>
</html>
