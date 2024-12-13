<?php 
session_start(); 
include "conexao.php"; 
$sql = "SELECT id, nome, email, data, lida FROM notificacoes";
$result = $conn->query($sql);


if (isset($_SESSION['id'])) {
    $id = $_SESSION['id']; 

   

    // Verifica se o usuário está logado
    if (isset($_SESSION['fname']) && isset($_SESSION['pp'])) {
        $fname = htmlspecialchars($_SESSION['fname']); 
        $pp = htmlspecialchars($_SESSION['pp']); 
        $pp_path = 'upload/' . $pp;
        if (!file_exists($pp_path)) {
            $pp_path = 'upload/default-pp.png'; 
        }
    } else {
        $sql = "SELECT fname, pp FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        try {
            $stmt->execute([$id]); 
            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch(); 
                $fname = htmlspecialchars($user['fname']); 
                $pp = htmlspecialchars($user['pp']);
                $_SESSION['fname'] = $fname;
                $_SESSION['pp'] = $pp;

                $pp_path = 'upload/' . $pp;
                if (!file_exists($pp_path)) {
                    $pp_path = 'upload/default-pp.png'; 
                }
            } else {
                header("Location: ../indexdash.php");
                exit();
            }
        } catch (PDOException $e) {
            echo "Erro ao buscar dados do usuário: " . $e->getMessage();
            exit();
        }
    }
} else {
    header("Location: ../indexdash.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>MAPSUS</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        color: #333;
    }
    
    .user-list {
        margin-top: 20px;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .user-list h2 {
        margin-bottom: 20px;
        font-size: 24px;
        color: #495057;
    }

    .user-list table {
        width: 100%;
        border-collapse: collapse;
    }

    .user-list th, .user-list td {
        padding: 12px;
        text-align: left;
        border: 1px solid #ddd;
        font-size: 14px;
    }

    .user-list th {
        background-color: #495057;
        color: white;
        border-bottom: 2px solid #495057;
    }

    .user-list tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .user-list tr:hover {
        background-color: #e9ecef;
    }

    .user-list td {
        transition: background-color 0.3s;
    }
    button {
            background-color: #dc3545; /* Vermelho */
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #c82333; /* Vermelho escuro */
        }
</style>
</head>
<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand" style="color: #495057;"><img src="img/logop.png" height="55px"> MAPSUS</a>
        <ul class="side-menu">
            <li><a href="indexdash.php"><i class='bx bxs-dashboard icon'></i> Dashboard</a></li>
            <li class="divider" data-text="Dados">Dados</li>
            <li><a href="usuarios.php"><i class='' ><img src="./img/users.png" width="20px" alt=""></i> Usuários</a></li>
            <li><a href="hospitais.php"><i class='' ><img src="./img/hosps.png" width="20px" alt=""></i>Hospitais </a></li>
            <li><a href="especialidades.php"><i class='' ><img src="./img/espb.png" width="20px" alt=""></i>Especialidades</a></li>
            <li><a href="#" class="active" style="background-color: #495057;"><i class='' ><img src="./img/histpb.png" width="20px" alt=""></i>Histórico de notificações</a></li>
        </ul>
    </section>
    <!-- SIDEBAR -->

    <!-- NAVBAR -->
    <section id="content">
        <nav>
        <div style="width: 1210px; display: inline-block;"></div>
            <div class="profile" style="display: flex; align-items: center;">
                <img src="<?php echo $pp_path; ?>" alt="Profile Picture" style="cursor: pointer; width: 40px; height: 40px; border-radius: 50%;">
                <div style="margin-left: 10px;">
                    <p style="font-size: 12px; color: #666;">Bem-vindo, <strong><?php echo $fname; ?></strong>!</p>
                </div>
            </div>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <h1 class="title">Histórico de notificações</h1>
            <ul class="breadcrumbs">
                <li><a href="#" style="color: #000;">Home</a></li>
                <li class="divider">/</li>
                <li><a href="#" class="active">Histórico de notificações</a></li>
            </ul>
    <?php if ($result->num_rows > 0): ?>
        <div class="user-list">
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Data</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nome']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['data']; ?></td>
                    <td>
                        <?php
                        if ($row['lida'] == 1) {
                            echo "Aprovado";
                        } elseif ($row['lida'] == 2) {
                            echo "Rejeitado";
                        } else {
                            echo "Pendente";
                        }
                        ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        </div>
    <?php else: ?>
        <p>Não há cadastros registrados.</p>
    <?php endif; ?>

            

            

        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->
</body>
</html>