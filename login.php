<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login</title>
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
#borda{
	
	border-radius: 15px;
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
    	      action="php/login.php" 
    	      method="post"
			  id="borda">

    		<h4 class="display-4  fs-1">LOGIN</h4><br>
    		<?php if(isset($_GET['error'])){ ?>
    		<div class="alert alert-danger" role="alert">
			  <?php echo $_GET['error']; ?>
			</div>
		    <?php } ?>

		  
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
		  
		  <button type="submit" class="botao">Entrar</button>
		  
		  <br>
		  <a>Não tem conta?: <a href="index.php" class="link-secondary">Cadastre-se</a></a>
		</form>
		
    </div>
</body>
</html>