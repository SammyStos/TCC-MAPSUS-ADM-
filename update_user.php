<?php
session_start();
include "db_conn.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['Nome'];
    $nome_completo = $_POST['Nome_completo'];
    $email = $_POST['Email'];
    $cpf = $_POST['CPF'];
    $cns = $_POST['CNS'];
    $telefone = $_POST['Telefone'];

    $sql_update_user = "UPDATE usuarios SET Nome = ?, Nome_completo = ?, Email = ?, CPF = ?, CNS = ?, Telefone = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update_user);

    try {
        $stmt_update->execute([$nome, $nome_completo, $email, $cpf, $cns, $telefone, $id]);
        echo "<script>alert('Usuário atualizado com sucesso!'); window.location.href='usuarios.php';</script>";
    } catch (PDOException $e) {
        echo "Erro ao atualizar usuário: " . $e->getMessage();
    }
} else {
    header("Location: usuarios.php");
    exit();
}
