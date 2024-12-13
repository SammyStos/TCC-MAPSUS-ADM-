<?php 
session_start(); 
include "db_conn.php"; 

if (isset($_SESSION['id'])) {
    $id = $_SESSION['id']; 

    $sql_count_esps = "SELECT COUNT(id) AS total_esps FROM especialidades";
    $stmt_count = $conn->prepare($sql_count_esps);
    $stmt_count->execute();
    $total_esps = $stmt_count->fetchColumn();

    $sql_all_esps = "SELECT id, Nome FROM especialidades";
    $stmt_all_esps = $conn->prepare($sql_all_esps);
    $stmt_all_esps->execute();
    $especialidades = $stmt_all_esps->fetchAll(PDO::FETCH_ASSOC); 

    // Verificando se o usuário está logado e recuperando dados
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

    // Lógica de atualização de usuário
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $especialidade_id = $_POST['id'];
    $especialidade_n = $_POST['Nome'];
    
    $sql_update_esp = "UPDATE especialidades SET Nome = ? WHERE id = ?";

    $stmt_update = $conn->prepare($sql_update_esp);
    $stmt_update->execute([$especialidade_n, $especialidade_id]);
    header("Refresh:0");
    exit();
}

} else {
    header("Location: ../indexdash.php");
    exit();
}

// Processar exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $especialidade_id = $_POST['id'];
    $sql_delete = "DELETE FROM especialidades WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->execute([$especialidade_id]);
    header("Location: especialidades.php");
    exit();
}



// Processar inserção de nova especialidade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $especialidade_n = $_POST['Nome'];
    $sql_insert = "INSERT INTO especialidades (Nome) VALUES (?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->execute([$especialidade_n]);
    header("Location: especialidades.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>MAPSUS</title>
    <style>
        /* Estilos gerais */
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
            background-color: #dc3545; 
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #c82333; 
        }

        .edit-form {
            display: none;
        }

        .add-form {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .add-form input {
            padding: 10px;
            margin-bottom: 10px;
            width: 200px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .add-form button {
            background-color: #28a745;
        }

        .add-form button:hover {
            background-color: #218838;
        }
        .edit-form {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Sidebar e Navbar -->
    <section id="sidebar">
        <a href="#" class="brand" style="color: #495057;"><img src="img/logop.png" height="55px"> MAPSUS</a>
        <ul class="side-menu">
            <li><a href="indexdash.php"><i class='bx bxs-dashboard icon'></i> Dashboard</a></li>
            <li class="divider" data-text="Dados">Dados</li>
            <li><a href="usuarios.php"><i class='' ><img src="./img/users.png" width="20px" alt=""></i> Usuários</a></li>
            <li><a href="hospitais.php"><i class='' ><img src="./img/hosps.png" width="20px" alt=""></i>Hospitais </a></li>
            <li><a href="especialidades.php" class="active" style="background-color: #495057;"><i class='' ><img src="./img/esp.png" width="20px" alt=""></i>Especialidades</a></li>
            <li><a href="lista.php"><i class='' ><img src="./img/histp.png" width="20px" alt=""></i>Histórico de notificações</a></li>
        </ul>
    </section>

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

        <main>
        <h1 class="title">Especialidades</h1>
			<ul class="breadcrumbs">
				<li><a href="#" style="color: #000;">Home</a></li>
				<li class="divider">/</li>
				<li><a href="#" class="active">Especialidades</a></li>
			</ul>
            <br>
            
            
            <!-- Formulário de Inserção -->
            <div class="add-form">
                
                <form action="" method="POST">
                    <label for="Nome">Nova Especialidade:</label>
                    <input type="text" name="Nome" id="Nome" placeholder="Digite o nome da especialidade" required>
                    <button type="submit" name="add">Adicionar Especialidade</button>
                </form>
            </div>

            <!-- Tabela de Especialidades -->
            <div class="user-list">
                <h2>Especialidades</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($especialidades as $especialidade): ?>
                            <tr>
                                <td><?php echo $especialidade['id']; ?></td>
                                <td><?php echo htmlspecialchars($especialidade['Nome']); ?></td>
                                <td>
    <button type="button" onclick="toggleEditForm(<?php echo $especialidade['id']; ?>)">Editar</button>
    <form action="especialidades.php" method="POST" style="display:inline-block;">
        <input type="hidden" name="id" value="<?php echo $especialidade['id']; ?>">
        <button type="submit" name="delete">Excluir</button>
    </form>
</td>


                                    
                                </td>
                            </tr>
                            <tr id="edit-form-<?php echo $especialidade['id']; ?>" class="edit-form">
                            <td colspan="9">
                                <form method="POST" action="">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($especialidade['id']); ?>">
                                    <input type="text" name="Nome" value="<?php echo htmlspecialchars($especialidade['Nome']); ?>" required>
                                    <button type="submit" name="edit">Salvar</button>
                                    <button type="button" onclick="toggleEditForm(<?php echo $especialidade['id']; ?>)">Cancelar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        
                    </tbody>
                </table>
            </div>
        </main>
    </section>
    <script>
    function toggleEditForm(id) {
        const form = document.getElementById('edit-form-' + id);
        form.style.display = form.style.display === 'table-row' ? 'none' : 'table-row';
    }
</script>

</body>
</html>
