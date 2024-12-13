<?php 
session_start();
include "db_conn.php";

// Verifica se o usuário está logado
if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];

    // Verifica se os dados foram enviados via POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Coleta os dados do formulário
        $fname = htmlspecialchars($_POST['fname']);
        $uname = htmlspecialchars($_POST['uname']);
        $old_pp = $_POST['old_pp'];
        $new_pp = $_FILES['pp']['name'];

        // Lógica para o upload da nova foto de perfil
        if ($new_pp) {
            $target_dir = "upload/";
            $target_file = $target_dir . basename($_FILES["pp"]["name"]);
            move_uploaded_file($_FILES["pp"]["tmp_name"], $target_file);
        } else {
            $new_pp = $old_pp;  // Se não houver nova foto, mantém a antiga
        }

        // Atualiza os dados no banco de dados
        $sql = "UPDATE users SET fname = ?, username = ?, pp = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$fname, $uname, $new_pp, $id]);

        // Atualiza os dados na sessão
        $_SESSION['fname'] = $fname;
        $_SESSION['pp'] = $new_pp;

        // Redireciona para a mesma página com uma mensagem de sucesso
        header("Location: edit.php?success=Perfil atualizado com sucesso!");
        exit();
    }

    // Recupera os dados do usuário
    $sql = "SELECT fname, username, pp FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    // Se o usuário não for encontrado
    if (!$user) {
        header("Location: indexdash.php");
        exit();
    }

    $fname = htmlspecialchars($user['fname']);
    $pp = htmlspecialchars($user['pp']);
    $pp_path = 'upload/' . $pp;

    if (!file_exists($pp_path)) {
        $pp_path = 'upload/default-pp.png'; // Define imagem padrão se não houver foto
    }

} else {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <style>
        .botao {
            border-radius: 10px;
            width: 420px;
            height: 45px;
            cursor: pointer;
            border: 0;
            background-color: #495057;
            box-shadow: #495057 0 0 8px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-size: 15px;
            transition: all 0.5s ease;
            color: white;
        }

        .botao:hover {
            letter-spacing: 3px;
            background-color: #495057;
            color: hsl(0, 0%, 100%);
            box-shadow: #495057 0px 7px 29px 0px;
        }

        .botao:active {
            letter-spacing: 3px;
            background-color: #495057;
            color: hsl(0, 0%, 100%);
            box-shadow: #495057 0px 0px 0px 0px;
            transform: translateY(10px);
            transition: 100ms;
        }

        #upload {
            background-color: #495057;
            color: white;
            padding: 0.5rem;
            font-family: sans-serif;
            border-radius: 0.3rem;
            cursor: pointer;
            margin-top: 1rem;
            width: 200px;
        }
        #upload:hover {
            background-color: #495057;
            color: hsl(0, 0%, 100%);
            box-shadow: #495057 0px 7px 29px 0px;
        }

        #file-chosen {
            margin-left: 0.3rem;
            font-family: sans-serif;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand" style="color: #495057;"><img src="img/logop.png" height="55px"> MAPSUS</a>
        <ul class="side-menu">
            <li><a href="indexdash.php" class="active" style="background-color: #495057;"><i class='bx bxs-dashboard icon'></i> Dashboard</a></li>
            <li class="divider" data-text="Dados">Dados</li>
            <li><a href="usuarios.php"><img src="./img/users.png" width="20px" alt=""> Usuários</a></li>
            <li><a href="hospitais.php"><img src="./img/hosps.png" width="20px" alt="">Hospitais</a></li>
            <li><a href="especialidades.php"><i class='' ><img src="./img/espb.png" width="20px" alt=""></i>Especialidades</a></li>
            <li><a href="lista.php"><img src="./img/histp.png" width="20px" alt="">Histórico de notificações</a></li>
        </ul>
    </section>
    <!-- SIDEBAR -->

    <!-- NAVBAR -->
    <section id="content">
        <nav>
            
        <div style="width: 1180px; display: inline-block;"></div>

            <div class="profile" style="display: flex; align-items: center;">
                <img src="<?php echo $pp_path; ?>" alt="Profile Picture" style="cursor: pointer; width: 40px; height: 40px; border-radius: 50%;">
                <div style="margin-left: 10px;">
                    <p style="font-size: 12px; color: #666;">Bem-vindo, <strong><?php echo $fname; ?></strong>!</p>
                </div>
                <ul class="profile-link">
                    <li><a href="edit.php"><i class='bx bxs-cog'></i> Perfil</a></li>
                    <li><a href="index.php"><i class='bx bxs-log-out-circle'></i> Sair</a></li>
                </ul>
            </div>
        </nav>

        <!-- Formulário de Edição -->
        <div class="d-flex justify-content-center align-items-center vh-100">
            <form class="shadow w-450 p-3" action="edit.php" method="post" enctype="multipart/form-data">
                <h4 class="display-4 fs-1">Editar perfil</h4><br>

                <!-- Exibe erro -->
                <?php if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_GET['error']; ?>
                    </div>
                <?php } ?>

                <!-- Exibe sucesso -->
                <?php if (isset($_GET['success'])) { ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $_GET['success']; ?>
                    </div>
                <?php } ?>

                <div class="mb-3">
                    <label class="form-label">Nome completo</label>
                    <input type="text" class="form-control" name="fname" value="<?php echo $fname; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nome de usuário</label>
                    <input type="text" class="form-control" name="uname" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Foto de perfil</label>
                    <input type="file" class="form-control" name="pp">
                    <img src="upload/<?= htmlspecialchars($user['pp']); ?>" class="rounded-circle" style="width: 70px; height: 70px;">
                    <input type="hidden" name="old_pp" value="<?= htmlspecialchars($user['pp']); ?>">
                </div>

                <button type="submit" class="botao">Atualizar</button>
            </form>
        </div>
    </section>
    <!-- NAVBAR -->

    <script src="script.js"></script>
</body>
</html>
