<?php 
session_start(); 
include "db_conn.php"; 

if (isset($_SESSION['id'])) {
    $id = $_SESSION['id']; 

    $sql_count_esps = "SELECT COUNT(id) AS total_esps FROM especialidades";
    $stmt_count = $conn->prepare($sql_count_esps);
    $stmt_count->execute();
    $total_esps = $stmt_count->fetchColumn();

    $sql_count_users = "SELECT COUNT(id) AS total_users FROM usuarios";
    $stmt_count = $conn->prepare($sql_count_users);
    $stmt_count->execute();
    $total_users = $stmt_count->fetchColumn();
  
    $sql_count_hosp = "SELECT COUNT(id) AS total_hosp FROM hospitais";
    $stmt_count = $conn->prepare($sql_count_hosp);
    $stmt_count->execute();
    $total_hosp = $stmt_count->fetchColumn();

    $sql_count_notifications = "SELECT COUNT(id) AS total_notificacoes FROM notificacoes WHERE lida = 0 AND usuario_id = ?";
    $stmt_notifications = $conn->prepare($sql_count_notifications);
    $stmt_notifications->execute([$id]);
    $total_notificacoes = $stmt_notifications->fetchColumn();

    $sql = "SELECT 
            MONTH(Data_cadastro) AS mes, 
            COUNT(usuarios.id) AS total_usuarios,
            COALESCE(hospitais_por_mes.total_hospitais, 0) AS total_hospitais
        FROM usuarios
        LEFT JOIN (
            SELECT 
                MONTH(Data_cadastro) AS mes_hospitais, 
                COUNT(*) AS total_hospitais
            FROM hospitais
            WHERE YEAR(Data_cadastro) = YEAR(CURDATE())
            GROUP BY MONTH(Data_cadastro)
        ) AS hospitais_por_mes ON MONTH(Data_cadastro) = hospitais_por_mes.mes_hospitais
        WHERE YEAR(Data_cadastro) = YEAR(CURDATE())
        GROUP BY MONTH(Data_cadastro)";
    $result = $conn->query($sql);

    $dados = [];

    if ($result->rowCount() > 0) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $dados[] = [$row['mes'], (int)$row['total_usuarios'], (int)$row['total_hospitais']];
        }
    }
    

    
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
    <link rel="stylesheet" href="css/table.css">
	<title>MAPSUS</title>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        // Carregar o pacote corechart do Google Charts
        google.charts.load('current', {packages: ['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
    var dadosPHP = <?php echo json_encode($dados); ?>;
    var data = new google.visualization.DataTable();

    // Adicionar colunas
    data.addColumn('string', 'Mês');
    data.addColumn('number', 'Usuários');
    data.addColumn('number', 'Hospitais');

    // Converter números dos meses para nomes legíveis
    var mesesNomes = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
    var dadosFormatados = dadosPHP.map(function(item) {
        return [mesesNomes[item[0] - 1], item[1], item[2]];
    });

    // Adicionar linhas ao DataTable
    data.addRows(dadosFormatados);

    // Opções do gráfico
    var options = {
        title: 'Número de Usuários e Hospitais cadastrados em cada Mês',
        width: 1000,
        height: 600,
        hAxis: {title: 'Mês'},
        vAxis: {title: 'Usuários e Hospitais',
            format: '0'
            //ticks: [0, 10, 20, 30, 40, 50] caso queira o intervalo certo de cada numero
        },
        seriesType: 'bars', // Tipo de gráfico
        colors: ['#1c1c1c', '#495057']
        
    };

    // Renderizar o gráfico
    var chart = new google.visualization.ComboChart(document.getElementById('graficoUsuarios'));
    chart.draw(data, options);
}
    </script>
</head>
<style>
.progress {
   background-color: #495057; /* Cor de fundo da barra */
}
.notificacoes-list {
    position: absolute;
    top: 50px;
    right: 20px;
    background-color: white;
    border: 1px solid #ccc;
    padding: 10px;
    display: none;
    z-index: 1000;
}
.notificacoes-list p {
    margin: 0;
    padding: 5px 0;
    border-bottom: 1px solid #ddd;
}
</style>
<body>
	<!-- SIDEBAR -->
     
	<section id="sidebar">
	<a href="#" class="brand" style="color: #495057;"><img src="img/logop.png" height="55px"> MAPSUS</a>
		<ul class="side-menu">
			<li><a href="indexdash.php" class="active" style="background-color: #495057;"><i class='bx bxs-dashboard icon'></i> Dashboard</a></li>
			<li class="divider" data-text="Dados">Dados</li>
			<li><a href="usuarios.php"><i class='' ><img src="./img/users.png" width="20px" alt=""></i> Usuários</a></li>
            <li><a href="hospitais.php"><i class='' ><img src="./img/hosps.png" width="20px" alt=""></i>Hospitais </a></li>
            <li><a href="especialidades.php"><i class='' ><img src="./img/espb.png" width="20px" alt=""></i>Especialidades</a></li>
            <li><a href="lista.php"><i class='' ><img src="./img/histp.png" width="20px" alt=""></i>Histórico de notificações</a></li>
		</ul>
	</section>
	<!-- SIDEBAR -->

	<!-- NAVBAR -->
	<section id="content">
		<nav>
        <div style="width: 1160px; display: inline-block;"></div>
            
            <button id="notificationButton" class="notification-button">
            <i class='bx bxs-bell' style="font-size: 24px; color: black;"></i>

            </button> 
            <div id="notificationModal" class="modal" style= "">

        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="cadastrop">Cadastros Pendentes</h2>
            <?php
            include 'conexao.php';
            $sql = "SELECT id, nome, email, data FROM notificacoes WHERE lida = 0"; 
            $result = $conn->query($sql);
            if ($result->num_rows > 0): ?>
                <table border="1" style="width: 100%;">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['nome']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['data']; ?></td>
                            <td>
                                <div class="button-group">
                                <button class="accept" onclick="window.location.href='aprovar.php?id=<?php echo $row['id']; ?>&acao=aceitar'">Aceitar</button>
                                <button class="reject" onclick="window.location.href='aprovar.php?id=<?php echo $row['id']; ?>&acao=recusar'">Recusar</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <br>
                <p>Não há cadastros pendentes.</p>
            <?php endif; ?>
        </div>
    </div>
			
			
			<div class="profile" style="display: flex; align-items: center;">
            <img src="<?php echo $pp_path; ?>" class="img" alt="Profile Picture" style="cursor: pointer; width: 40px; height: 40px; border-radius: 50%;">

                <div style="margin-left: 10px;">
                    <p style="font-size: 12px; color: #666;">Bem-vindo, <strong><?php echo $fname; ?></strong>!</p>
                </div>
                <ul class="profile-link">
                    <li><a href="edit.php"><i class='bx bxs-cog'></i> Perfil</a></li>
                    <li><a href="index.php"><i class='bx bxs-log-out-circle'></i> Sair</a></li>
                </ul>
            </div>
		</nav>
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
			<h1 class="title">Dashboard</h1>
			<ul class="breadcrumbs">
				<li><a href="#" style="color: #000;">Home</a></li>
				<li class="divider">/</li>
				<li><a href="#" class="active">Dashboard</a></li>
			</ul>
			<div class="info-data">
				<div class="card">
					<div class="head">
						<div>
							<h2><?php echo $total_users; ?></h2>
							<p>Usuários cadastrados</p>
						</div>
					</div>
					<span class="progress" data-value="40%"></span>
				</div>
				<div class="card">
					<div class="head">
						<div>
							<h2><?php echo $total_hosp; ?></h2>
							<p>Hospitais cadastrados</p>
						</div>
					</div>
					<span class="progress" data-value="60%"></span>
				</div>
                <div class="card">
					<div class="head">
						<div>
							<h2><?php echo $total_esps; ?></h2>
							<p>Especialidades cadastradas</p>
						</div>
					</div>
					<span class="progress" data-value="60%"></span>
				</div>
			</div>

            <div class="info-data">
                <div class="card">
                    <div class="head">
                        <div id="graficoUsuarios"></div>       
                    </div>
                </div>
            </div>
		</main>
		<!-- MAIN -->

		
	</section>
	<!-- NAVBAR -->

	<script src="script.js"></script>

	<script>
		var modal = document.getElementById("notificationModal");
        var btn = document.getElementById("notificationButton");
        var span = document.getElementsByClassName("close")[0];

        btn.onclick = function() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        
	</script>
</body>
</html>