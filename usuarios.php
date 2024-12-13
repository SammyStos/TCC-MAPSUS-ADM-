<?php 
session_start(); 
include "db_conn.php"; 

if (isset($_SESSION['id'])) {
    $id = $_SESSION['id']; 

    // Contar o número de usuários
    $sql_count_users = "SELECT COUNT(id) AS total_users FROM usuarios";
    $stmt_count = $conn->prepare($sql_count_users);
    $stmt_count->execute();
    $total_users = $stmt_count->fetchColumn();

    // Definir a coluna padrão para ordenação
    $sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'Nome'; // Definir a coluna a ser ordenada (Nome por padrão)
    $sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';  // Ordem (ASC por padrão)

    // Inicializa a consulta SQL com a base
    $sql_all_users = "SELECT id, Nome, Nome_completo, Email, CPF, CNS, Telefone, Data_cadastro FROM usuarios WHERE 1=1";

    // Array para armazenar parâmetros
    $params = [];

    if (!empty($_GET['nome'])) {
        $sql_all_users .= " AND Nome_completo LIKE ?";
        $params[] = '%' . $_GET['nome'] . '%';
    }

    if (!empty($_GET['email'])) {
        $sql_all_users .= " AND Email LIKE ?";
        $params[] = '%' . $_GET['email'] . '%';
    }

    if (!empty($_GET['cpf'])) {
        $sql_all_users .= " AND CPF LIKE ?";
        $params[] = '%' . $_GET['cpf'] . '%';
    }

    if (!empty($_GET['cns'])) {
        $sql_all_users .= " AND CNS LIKE ?";
        $params[] = '%' . $_GET['cns'] . '%';
    }

    if (!empty($_GET['telefone'])) {
        $sql_all_users .= " AND Telefone LIKE ?";
        $params[] = '%' . $_GET['telefone'] . '%';
    }

    $sql_all_users .= " ORDER BY " . $sort_column . " " . $sort_order;

    // Prepara e executa a consulta
    $stmt_all_users = $conn->prepare($sql_all_users);
    $stmt_all_users->execute($params);
    $usuarios = $stmt_all_users->fetchAll(PDO::FETCH_ASSOC);

    // Verificar se o formulário de exclusão foi enviado
    if (isset($_POST['delete'])) {
        $userId = $_POST['id'];

        // Preparar e executar a consulta para excluir o usuário
        $sql_delete_user = "DELETE FROM usuarios WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete_user);
        
        try {
            $stmt_delete->execute([$userId]);
            echo "<script>alert('Usuário excluído com sucesso!');</script>";
            header("Refresh:0"); 
            exit();
        } catch (PDOException $e) {
            echo "Erro ao excluir o usuário: " . $e->getMessage();
        }
    }

    // Lógica de sessão para usuário logado
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
    if (isset($_POST['edit'])) {
        $userId = $_POST['id'];
        $nome = $_POST['nome'];
        $nome_completo = $_POST['nome_completo'];
        $email = $_POST['email'];
        $cpf = $_POST['cpf'];
        $cns = $_POST['cns'];
        $telefone = $_POST['telefone'];

        $sql_update_user = "UPDATE usuarios SET Nome = ?, Nome_completo = ?, Email = ?, CPF = ?, CNS = ?, Telefone = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update_user);
        $stmt_update->execute([$nome, $nome_completo, $email, $cpf, $cns, $telefone, $userId]);
        header("Refresh:0");
        exit();
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
        .white{
            color: white;
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
            <li><a href="#" class="active" style="background-color: #495057;"><i class='' ><img src="./img/usersb.png" width="20px" alt=""></i> Usuários</a></li>
            <li><a href="hospitais.php"><i class='' ><img src="./img/hosps.png" width="20px" alt=""></i>Hospitais </a></li>
            <li><a href="especialidades.php"><i class='' ><img src="./img/espb.png" width="20px" alt=""></i>Especialidades</a></li>
            <li><a href="lista.php"><i class='' ><img src="./img/histp.png" width="20px" alt=""></i>Histórico de notificações</a></li>

        </ul>
    </section>
    <!-- SIDEBAR -->

    <!-- NAVBAR -->
    <section id="content">
        <nav>
        <div style="width: 1220px; display: inline-block;"></div>
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
            <h1 class="title">Usuários</h1>
            <ul class="breadcrumbs">
                <li><a href="#" style="color: #000;">Home</a></li>
                <li class="divider">/</li>
                <li><a href="#" class="active">Usuários</a></li>
            </ul>

            <div class="info-data">
                <div class="card">
                    <div class="head">
                        <div>
                            <h2><?php echo $total_users; ?></h2>
                            <p>Usuários cadastrados</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="info-data">
                <div class="card">
                    <form method="GET" action="">
                        <label for="nome">Nome:</label>
                        <input type="text" name="nome" id="nome" value="<?php echo isset($_GET['nome']) ? htmlspecialchars($_GET['nome']) : ''; ?>">

                        <label for="email">Email:</label>
                        <input type="text" name="email" id="email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>">
                        
                        <label for="cpf">CPF:</label>
                        <input type="text" name="cpf" id="cpf" value="<?php echo isset($_GET['cpf']) ? htmlspecialchars($_GET['cpf']) : ''; ?>">

                        <label for="cns">CNS:</label>
                        <input type="text" name="cns" id="cns" value="<?php echo isset($_GET['cns']) ? htmlspecialchars($_GET['cns']) : ''; ?>">

                        <label for="telefone">Telefone:</label>
                        <input type="text" name="telefone" id="telefone" value="<?php echo isset($_GET['telefone']) ? htmlspecialchars($_GET['telefone']) : ''; ?>">

                        
                        <button type="submit">Filtrar</button>
                        <button type="button" onclick="window.location.href = 'usuarios.php';">Limpar Filtros</button>
                    </form>
                </div>
            </div>
            



            <!-- Tabela de usuários -->
            <div class="user-list">
                <h2>Lista de Usuários</h2>
                <table>
                    <thead>
                        <tr>
                            <th><a class="white"  href="?sort=id&order=<?php echo ($sort_column == 'id' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">ID</a></th>
                            <th><a class="white"  href="?sort=Nome&order=<?php echo ($sort_column == 'Nome' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Nome</a></th>
                            <th><a class="white"  href="?sort=Nome_completo&order=<?php echo ($sort_column == 'Nome_completo' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Nome Completo</a></th>
                            <th><a class="white"  href="?sort=Email&order=<?php echo ($sort_column == 'Email' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Email</a></th>
                            <th><a class="white"  href="?sort=CPF&order=<?php echo ($sort_column == 'CPF' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">CPF</a></th>
                            <th><a class="white"  href="?sort=CNS&order=<?php echo ($sort_column == 'CNS' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">CNS</a></th>
                            <th><a class="white"  href="?sort=Telefone&order=<?php echo ($sort_column == 'Telefone' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Telefone</a></th>
                            <th><a class="white"  href="?sort=Data_cadastro&order=<?php echo ($sort_column == 'Data_cadastro' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Data de Cadastro</a></th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['Nome']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['Nome_completo']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['Email']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['CPF']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['CNS']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['Telefone']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['Data_cadastro']); ?></td>
                            <td>
                                <button onclick="toggleEditForm(<?php echo $usuario['id']; ?>)">Editar</button>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($usuario['id']); ?>">
                                    <button type="submit" name="delete" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</button>
                                </form>
                            </td>
                        </tr>
                        <tr id="edit-form-<?php echo $usuario['id']; ?>" class="edit-form">
                            <td colspan="9">
                                <form method="POST" action="">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($usuario['id']); ?>">
                                    <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['Nome']); ?>" required>
                                    <input type="text" name="nome_completo" value="<?php echo htmlspecialchars($usuario['Nome_completo']); ?>" required>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['Email']); ?>" required>
                                    <input type="text" name="cpf" value="<?php echo htmlspecialchars($usuario['CPF']); ?>" required>
                                    <input type="text" name="cns" value="<?php echo htmlspecialchars($usuario['CNS']); ?>" required>
                                    <input type="text" name="telefone" value="<?php echo htmlspecialchars($usuario['Telefone']); ?>" required>
                                    <button type="submit" name="edit">Salvar</button>
                                    <button type="button" onclick="toggleEditForm(<?php echo $usuario['id']; ?>)">Cancelar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script>
        function toggleEditForm(id) {
            const form = document.getElementById('edit-form-' + id);
            form.style.display = form.style.display === 'table-row' ? 'none' : 'table-row';
        }
    </script>
</body>
</html>
