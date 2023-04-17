
<?php
// IS RECEIVED SHORTCUT
if(isset($_GET['q'])){

	// VARIABLE
	$shortcut = htmlspecialchars($_GET['q']);

	// IS A SHORTCUT ?
	$bdd = new PDO('mysql:host=localhost;dbname=bitly;charset=utf8', 'root', '');
	$req =$bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE shortcut = ?');
	$req->execute(array($shortcut));

	while($result = $req->fetch()){

		if($result['x'] != 1){
			header('location: ../?error=true&message=Adresse url non connue');
			exit();
		}

	}

	// REDIRECTION
	$req = $bdd->prepare('SELECT * FROM links WHERE shortcut = ?');
	$req->execute(array($shortcut));

	while($result = $req->fetch()){

		header('location: '.$result['url']);
		exit();

	}

}

if(isset($_POST['url'])) {

	$url = $_POST['url'];

 // vérifier si $url est réellement une URL
  //si ce n'est pas le cas, rediriger vers index.php avec le message d'erreur

	if(!filter_var($url, FILTER_VALIDATE_URL)) {
		header('location: ../?error=true&message=Adresse url non valide');
		exit();
	}

	// SHORTCUT
	$shortcut = crypt($url, rand());

	// si url a déjà été utilisée
	$bdd = new PDO('mysql:host=localhost;dbname=bitly;charset=utf8', 'root', '');
	$req = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE url = ?');
	$req->execute(array($url));

	while($result = $req->fetch()){

		if($result['x'] != 0){
			header('location: ../?error=true&message=Adresse déjà raccourcie');
			exit();
		}

	}

	//insérer url et sa valeur raccourcie dans la bdd
	$req = $bdd->prepare('INSERT INTO links(url, shortcut) VALUES(?, ?)');
	$req->execute(array($url, $shortcut));

 //rediriger vers index.php avec le shortcut
	header('location: ../?short='.$shortcut);
	exit();

}
?>

<!DOCTYPE html>
<html lang="fr">
 <head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>URL SHORTENER</title>
  <link rel="stylesheet" type="text/css" href="design/default.css">
  <link rel="icon" type="image/png" href="assets/favico.png">
 </head>

 <body>
  <div id="hello">
   <div class="container">
    
    <header>
     <img id="logo" src="assets/logo.png">
    </header>

    <h1>Url Shortener</h1>
    <h2>Shorten your URL and share it with your friends !</h2>
   
    <form action="index.php" method="POST">
     <input type="url" name="url" placeholder="Enter your URL here">
     <input type="submit" value="SHORTEN">
    </form>

				<?php if(isset($_GET['error']) && isset($_GET['message'])) { ?>

					<div class="center">
						<div id="result">
							<b><?php echo htmlspecialchars($_GET['message']); ?></b>
						</div>
					</div>

				<?php } else if(isset($_GET['short'])) { ?>

					<div class="center">
						<div id="result">
							<b>Here is your shortened URL : </b>
							http://localhost/?q=<?php echo htmlspecialchars($_GET['short']); ?>
						</div>
					</div>

				<?php } ?>

			</div>
		</div>

   <section id="brands">
   <h3>WE ARE TRUSTED BY</h3>

   <div class="pictures">
    <img src="assets/1.jpg">
    <img src="assets/2.jpg">
    <img src="assets/3.jpg">
    <img src="assets/4.jpg">
   </div>

  </section>

  <footer>
   <img src="assets/logo2.jpg">

   <div class="container">
    <p>2023 ©Bitly</p>
    <a href="#">Contact</a> - 
    <a href="#">A propos</a>
   </div>

  </footer> 
  <br>

 </body>
</html>