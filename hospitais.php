<?php 
session_start(); 
include "db_conn.php"; 

if (isset($_SESSION['id'])) {
    $id = $_SESSION['id']; 

    $sql_count_hosp = "SELECT COUNT(id) AS total_hosp FROM hospitais";
    $stmt_count = $conn->prepare($sql_count_hosp);
    $stmt_count->execute();
    $total_hosp = $stmt_count->fetchColumn();

    $sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'Nome'; // Definir a coluna a ser ordenada (Nome por padrão)
    $sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';  // Ordem (ASC por padrão)


    $sql_all_hosps = "SELECT id, Nome, Usuario, Cidade, Endereco, Bairro, Zona, Telefone, Email, Cnpj, Cep, Bairro, Data_cadastro FROM hospitais WHERE 1=1";

    // Array para armazenar parâmetros
    $params = [];

    if (!empty($_GET['nome'])) {
        $sql_all_hosps .= " AND Nome LIKE ?";
        $params[] = '%' . $_GET['nome'] . '%';
    }

    if (!empty($_GET['email'])) {
        $sql_all_hosps .= " AND Email LIKE ?";
        $params[] = '%' . $_GET['email'] . '%';
    }

    if (!empty($_GET['cep'])) {
        $sql_all_hosps .= " AND Cep LIKE ?";
        $params[] = '%' . $_GET['cep'] . '%';
    }

    if (!empty($_GET['cnpj'])) {
        $sql_all_hosps .= " AND Cnpj LIKE ?";
        $params[] = '%' . $_GET['cnpj'] . '%';
    }

    if (!empty($_GET['telefone'])) {
        $sql_all_hosps .= " AND Telefone LIKE ?";
        $params[] = '%' . $_GET['telefone'] . '%';
    }
    if (!empty($_GET['usuario'])) {
        $sql_all_hosps .= " AND Usuario LIKE ?";
        $params[] = '%' . $_GET['usuario'] . '%';
    }

    if (!empty($_GET['endereco'])) {
        $sql_all_hosps .= " AND Endereco LIKE ?";
        $params[] = '%' . $_GET['endereco'] . '%';
    }

    if (!empty($_GET['cidade'])) {
        $sql_all_hosps .= " AND Cidade LIKE ?";
        $params[] = '%' . $_GET['cidade'] . '%';
    }

    if (!empty($_GET['zona'])) {
        $sql_all_hosps .= " AND Zona LIKE ?";
        $params[] = '%' . $_GET['zona'] . '%';
    }

    if (!empty($_GET['bairro'])) {
        $sql_all_hosps .= " AND Bairro LIKE ?";
        $params[] = '%' . $_GET['bairro'] . '%';
    }

    $sql_all_hosps .= " ORDER BY " . $sort_column . " " . $sort_order;

    // Prepara e executa a consulta
    $stmt_all_hosps = $conn->prepare($sql_all_hosps);
    $stmt_all_hosps->execute($params);
    $hospitais = $stmt_all_hosps->fetchAll(PDO::FETCH_ASSOC);

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

// Processar exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $hospital_id = $_POST['id'];
    $sql_delete = "DELETE FROM hospitais WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->execute([$hospital_id]);
    header("Location: hospitais.php");
    exit();
}

// Processar edição
if (isset($_POST['edit'])) {
    $hospital_id = $_POST['id'];
    $sql_edit = "SELECT * FROM hospitais WHERE id = ?";
    $stmt_edit = $conn->prepare($sql_edit);
    $stmt_edit->execute([$hospital_id]);
    $hospital_data = $stmt_edit->fetch(PDO::FETCH_ASSOC);
}

if (isset($_POST['update'])) {
    $hospital_id = $_POST['id'];
    $hospital_n = $_POST['Nome'];
    $usuario = $_POST['Usuario'];
    $email = $_POST['Email'];
    $cnpj = $_POST['Cnpj'];
    $telefone = $_POST['Telefone'];
    $endereco = $_POST['Endereco'];
    $cidade = $_POST['Cidade'];
    $zona = $_POST['Zona'];
    $cep = $_POST['Cep'];
    $bairro = $_POST['Bairro'];

    $sql_update = "UPDATE hospitais SET Nome = ?, Usuario = ?, Email = ?,  Cnpj = ?, Telefone = ?,Endereco = ?, Cidade = ?, Zona = ?, Cep = ?, Bairro = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->execute([$hospital_n, $usuario, $email, $cnpj, $telefone, $endereco, $cidade, $zona, $cep, $bairro, $hospital_id]);
    header("Location: hospitais.php");
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
    <section id="sidebar">
        <a href="#" class="brand" style="color: #495057;"><img src="img/logop.png" height="55px"> MAPSUS</a>
        <ul class="side-menu">
            <li><a href="indexdash.php"><i class='bx bxs-dashboard icon'></i> Dashboard</a></li>
            <li class="divider" data-text="Dados">Dados</li>
            <li><a href="usuarios.php"><i class='' ><img src="./img/users.png" width="20px" alt=""></i> Usuários</a></li>
            <li><a href="#" class="active" style="background-color: #495057;"><i class='' ><img src="./img/hospsb.png" width="20px" alt=""></i>Hospitais </a></li>
            <li><a href="especialidades.php"><i class='' ><img src="./img/espb.png" width="20px" alt=""></i>Especialidades</a></li>
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
            <h1 class="title">Hospitais</h1>
            <ul class="breadcrumbs">
                <li><a href="#" style="color: #000;">Home</a></li>
                <li class="divider">/</li>
                <li><a href="#" class="active">Hospitais</a></li>
            </ul>

            <div class="info-data">
                <div class="card">
                    <div class="head">
                        <div>
                        <h2><?php echo $total_hosp; ?></h2>
                        <p>Hospitais cadastrados</p>
                    </div>
                    </div>
                </div>
            </div>
            <div class="info-data">
                <div class="card">
                    <form method="GET" action="">
                        <table>
                            <tr>
                                <td>
                                <label for="nome">Nome:</label>
                                </td>
                                <td>
                                <input type="text" name="nome" id="nome" value="<?php echo isset($_GET['nome']) ? htmlspecialchars($_GET['nome']) : ''; ?>">
                                </td>
                                <td>
                                <label for="usuario">Usuario:</label>
                                </td>
                                <td>
                                <input type="text" name="usuario" id="usuario" value="<?php echo isset($_GET['usuario']) ? htmlspecialchars($_GET['usuario']) : ''; ?>">
                                </td>
                                <td>
                                <label for="email">Email:</label>
                                </td>
                                <td>
                                <input type="text" name="email" id="email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>">
                                </td>
                                <td>
                                <label for="cnpj">CNPJ:</label>
                                </td>
                                <td>
                                <input type="text" name="cnpj" id="cnpj" value="<?php echo isset($_GET['cnpj']) ? htmlspecialchars($_GET['cnpj']) : ''; ?>">
                                </td>
                                <td>
                                <label for="telefone">Telefone:</label>
                                </td>
                                <td>
                                <input type="text" name="telefone" id="telefone" value="<?php echo isset($_GET['telefone']) ? htmlspecialchars($_GET['telefone']) : ''; ?>">
                                </td>
                                <td>
                                <label for="endereco">Endereço:</label>
                                </td>
                                <td>
                                <input type="text" name="endereco" id="endereco" value="<?php echo isset($_GET['endereco']) ? htmlspecialchars($_GET['endereco']) : ''; ?>">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <label for="cidade">Cidade:</label>
                                </td>
                                <td>
                                <input type="text" name="cidade" id="cidade" value="<?php echo isset($_GET['cidade']) ? htmlspecialchars($_GET['cidade']) : ''; ?>">
                                </td>
                                <td>
                                <label for="zona">Zona:</label>
                                </td>
                                <td>
                                <input type="text" name="zona" id="zona" value="<?php echo isset($_GET['zona']) ? htmlspecialchars($_GET['zona']) : ''; ?>">
                                </td>
                                <td>
                                <label for="cep">CEP:</label>
                                </td>
                                <td>
                                <input type="text" name="cep" id="cep" value="<?php echo isset($_GET['cep']) ? htmlspecialchars($_GET['cep']) : ''; ?>">
                                </td>
                                <td>
                                <label for="bairro">Bairro:</label>
                                </td>
                                <td>
                                <input type="text" name="bairro" id="bairro" value="<?php echo isset($_GET['bairro']) ? htmlspecialchars($_GET['bairro']) : ''; ?>">
                                </td>
                                <td>
                                <button type="submit">Filtrar</button>
                                </td>
                                <td>
                                <button type="button" onclick="window.location.href = 'hospitais.php';">Limpar Filtros</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>


            <div class="user-list">
                <h2>Lista de Hospitais</h2>
                <table>
                    <thead>
                        <tr>
                            <th><a class="white"  href="?sort=id&order=<?php echo ($sort_column == 'id' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">ID</a></th>
                            <th><a class="white"  href="?sort=Nome&order=<?php echo ($sort_column == 'Nome' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Nome</a></th>
                            <th><a class="white"  href="?sort=Usuario&order=<?php echo ($sort_column == 'Usuario' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Usuario</a></th>
                            <th><a class="white"  href="?sort=Email&order=<?php echo ($sort_column == 'Email' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Email</a></th>
                            <th><a class="white"  href="?sort=Cnpj&order=<?php echo ($sort_column == 'Cnpj' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">CNPJ</a></th>
                            <th><a class="white"  href="?sort=Telefone&order=<?php echo ($sort_column == 'Telefone' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Telefone</a></th>
                            <th><a class="white"  href="?sort=Endereco&order=<?php echo ($sort_column == 'Endereco' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Endereço</a></th>
                            <th><a class="white"  href="?sort=Cidade&order=<?php echo ($sort_column == 'Cidade' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Cidade</a></th>
                            <th><a class="white"  href="?sort=Zona&order=<?php echo ($sort_column == 'Zona' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Zona</a></th>
                            <th><a class="white"  href="?sort=Cep&order=<?php echo ($sort_column == 'Cep' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">CEP</a></th>
                            <th><a class="white"  href="?sort=Bairro&order=<?php echo ($sort_column == 'Bairro' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Bairro</a></th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hospitais as $hospital) : ?>
                            <tr>
                                <td><?php echo $hospital['id']; ?></td>
                                <td><?php echo $hospital['Nome']; ?></td>
                                <td><?php echo $hospital['Usuario']; ?></td>
                                <td><?php echo $hospital['Email']; ?></td>
                                <td><?php echo $hospital['Cnpj']; ?></td>
                                <td><?php echo $hospital['Telefone']; ?></td>
                                <td><?php echo $hospital['Endereco']; ?></td>
                                <td><?php echo $hospital['Cidade']; ?></td>
                                <td><?php echo $hospital['Zona']; ?></td>
                                <td><?php echo $hospital['Cep']; ?></td>
                                <td><?php echo $hospital['Bairro']; ?></td>
                                <td>
                                <button onclick="toggleEditForm(<?php echo $hospital['id']; ?>)">Editar</button>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($hospital['id']); ?>">
                                    <button type="submit" name="delete" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</button>
                                </form>
                            </td>
                            </tr>
                            <tr id="edit-form-<?php echo $hospital['id']; ?>" class="edit-form">
                                <td colspan="9">
                                    <form method="POST" action="">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($hospital['id']); ?>">
                                        <input type="text" name="Nome" value="<?php echo htmlspecialchars($hospital['Nome']); ?>" >
                                        <input type="text" name="Usuario" value="<?php echo htmlspecialchars($hospital['Usuario']); ?>" >
                                        <input type="email" name="Email" value="<?php echo htmlspecialchars($hospital['Email']); ?>" >
                                        <input type="text" name="Cnpj" value="<?php echo htmlspecialchars($hospital['Cnpj']); ?>" >
                                        <input type="text" name="Telefone" value="<?php echo htmlspecialchars($hospital['Telefone']); ?>" >
                                        <input type="text" name="Endereco" value="<?php echo htmlspecialchars($hospital['Endereco']); ?>" >
                                        <input type="text" name="Cidade" value="<?php echo htmlspecialchars($hospital['Cidade']); ?>" >
                                        <input type="text" name="Zona" value="<?php echo htmlspecialchars($hospital['Zona']); ?>" >
                                        <input type="text" name="Cep" value="<?php echo htmlspecialchars($hospital['Cep']); ?>" >
                                        <input type="text" name="Bairro" value="<?php echo htmlspecialchars($hospital['Bairro']); ?>" >
                                        <button type="submit" name="update">Salvar</button>
                                        <button type="button" onclick="toggleEditForm(<?php echo $hospital['id']; ?>)">Cancelar</button>
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
