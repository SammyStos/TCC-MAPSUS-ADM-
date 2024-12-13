<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Criar conta</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" href="css/index.css">
</head>
<style>.botao {
  border-radius: 10px;
  width: 420px;
  
  height: 45px;
  
  cursor: pointer;
  border: 0;
  background-color: #495057;
  box-shadow: rgb(0 0 0 / 5%) 0 0 8px;
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
#borda{
	
	border-radius: 15px;
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

#file-chosen{
  margin-left: 0.3rem;
  font-family: sans-serif;
}
/* Estilos gerais */
body {
    margin: 0;
    font-family: Arial, sans-serif;
}

/* Navbar */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: white;
    padding: 10px 20px;
	box-shadow: #495057 0px 7px 29px 0px;
	
}

.navbar-logo {
    display: flex;
    align-items: center;
}

.navbar-logo img {
    height: 60px;
    margin-right: 10px;
}

.navbar-text {
    color: #495057;
    font-size: 1.7rem;
	font-weight: bold;
}

.navbar-menu {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
}

.navbar-menu li {
    margin-left: 20px;
}

.navbar-menu a {
    color: #fff;
    text-decoration: none;
    font-size: 1rem;
    transition: color 0.3s;
}

.navbar-menu a:hover {
    color: #f0a500;
}
</style>
<body>
<nav class="navbar">
        <div class="navbar-logo">
            <img src="img/logop.png" alt="Logo">
            <span class="navbar-text">MAPSUS</span>
        </div>
        
    </nav>
    <div class="d-flex justify-content-center align-items-center vh-100">
    	
    	<form class="shadow w-450 p-3" 
    	      action="php/signup.php" 
    	      method="post"
    	      enctype="multipart/form-data">

    		<h4 class="display-4  fs-1">Criar conta</h4><br>
    		<?php if(isset($_GET['error'])){ ?>
    		<div class="alert alert-danger" role="alert">
			  <?php echo $_GET['error']; ?>
			</div>
		    <?php } ?>

		    <?php if(isset($_GET['success'])){ ?>
    		<div class="alert alert-success" role="alert">
			  <?php echo $_GET['success']; ?>
			</div>
		    <?php } ?>
		  
		  <div class="input-container">
				<input placeholder="Nome" class="input-field" type="text" name="fname" 
				value="<?php echo (isset($_GET['fname']))?$_GET['fname']:"" ?>">
				<label for="input-field" class="input-label">Nome</label>
				<span class="input-highlight"></span>
 			</div>


		  
			<div class="input-container">
				<input placeholder="Nome de usuário" class="input-field" type="text" name="uname" 
				value="<?php echo (isset($_GET['uname']))?$_GET['uname']:"" ?>">
				<label for="input-field" class="input-label">Nome de usuário</label>
				<span class="input-highlight"></span>
 			</div>

		  
		  <div class="input-container">
			<input placeholder="Senha" class="input-field" type="password" name="pass">
			<label for="input-field" class="input-label">Senha</label>
			<span class="input-highlight"></span>
			</div>

		  <div class="mb-3">
		    <label class="form-label">Foto de perfil</label>
		    <input type="file" 
		           class="form-control"
		           name="pp">
		  </div>
			

		  
		  <button type="submit" class="botao">Criar</button>
		  <br>
		  <a>Já tem conta?: <a href="login.php" class="link-secondary">Logar</a></a>
		</form>
    </div>
</body>
</html>