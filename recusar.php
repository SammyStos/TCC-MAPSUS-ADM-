<?php
include 'conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
 
    $sql = "UPDATE notificacoes SET lida = 2 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: indexdash.php"); 
    } else {
        echo "Erro ao recusar cadastro.";
    }

    $stmt->close();
}
$conn->close();
?>
