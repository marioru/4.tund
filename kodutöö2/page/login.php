<?php
	//LOGIN.PHP
	
	require_once("../../../config.php");
	$database = "if15_mkoinc_3";
	$mysqli = new mysqli ($servername, $username,
	$password, $database);
	
	$email_error = "";
	$password_error = "";
	$name_error = "";
	$surename_error = "";
	$username_error = "";
	
	$mail_error = "";
	$passwordtwo_error = "";

	
	//muutjuad ab väärtuste jaoks
	$name = "";
	$surename = "";
	$email = "";
	$password = "";
	$mail = "";
	$passwordtwo = "";
	//echo $_POST€["email"];
	
	//kontrollime et keegi vajutas nuppu
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		
		
		//vajutas login nuppu
		if(isset($_POST["login"])){
			
			echo "vajutas login nuppu!";
			
			//kontrollin et e-post ei ole tühi
			
			if ( empty($_POST["email"]) ) {
				$email_error = "See väli on kohustuslik";
				
			}else{
        // puhastame muutuja võimalikest üleliigsetest sümbolitest
				$email = test_input($_POST["email"]);
			}
			
			//kontrollin et parool ei ole tühi
			 if ( empty($_POST["password"]) ) {
				 $password_error = "See väli on kohustuslik";
			} else{
				$password = test_input($_POST["password"]);
			}
			// Kui oleme siia jõudnud, võime kasutaja sisse logida
			if($password_error == "" && $email_error == "")
			{
				echo "Võib sisse logida! Kasutajanimi on ".$email." ja parool on ".$password;
				
				$hash = hash("sha512", $password);
				
				$stmt = $mysqli->prepare("SELECT id, email FROM user WHERE email=? AND password=?");
				$stmt->bind_param("ss", $email, $hash);
				
				//muutjuad tulemustele
				$stmt->bind_result($id_from_db, $email_from_db);
				$stmt->execute();
				
				//kontroolin kas tulemusi leiti 
				if($stmt->fetch()){
					//ab'i oli midagi
					echo "Email ja parool õiged, kasutaja id=".$id_from_db;
					
				}else{
					//ei leitud
					echo "Wrong credentials!";
				}
				
				$stmt->close();
			}
			
			
		// *********************
		// ** LOO KASUTAJA *****
		// *********************
		
		
		//kontrollime et keegi vajutas nuppu
		}elseif(isset($_POST["create"])){
			
			
			//kontrollin et nimi ja perekonnanime väljad ei oleks tühjad
			if ( empty($_POST["name"]) ) {
				$name_error = "See väli on kohustuslik";
			}else{
				//kõik korras
				//test_input eemaldab pahatahtlikud osad
				$name = test_input($_POST["name"]);
				
			}
			if($name_error == ""){
				echo "salvestan ab'i ".$name;
			}
			//kontrollin et perekonnanimi ei oleks tühi	
			if ( empty($_POST["surename"]) ) {
				$surename_error = "See väli on kohustuslik";
				
			}else{
 				
 				$surename = test_input($_POST[“surename”]);
 				}



			//kontrooli et kasutajanimi ei oleks tühi ja et see oleks vähemalt 3 tähemärki pikk
			if ( empty($_POST["username"]) ) {
				$username_error = "See väli on kohustuslik";
				
			} else {
				
				if(strlen($_POST["username"]) < 3) {
					
					$username_error = "Peab olema vähemalt 3 tähemärki pikk!";
					}
			}
			if (empty($_POST["email"])) {
				$mail_error = "See väli on kohustuslik";
			}
			if (empty($_POST["password"])) {
				$passwordtwo_error = "See väli on kohustuslik";
				} else {
				
				//kui oleme siia jõudnud, siis parool ei ole tühi
				//konrollin et oleks vähemalt 8 üsmbolit pikk
				if(strlen($_POST["password"]) < 8) {
					
					$passwordtwo_error = "Peab olema vähemalt 8 tähemärki pikk!";
					}
					
			if($mail_error == "" && $passwordtwo_error == ""){
				
				//räsi paroolist
				$hash = hash("sha512", $passwordtwo);
				
				echo "Võib kasutajat luua! Kasutajanimi on ".$mail." ja parool on ".$passwordtwo." ja räsi on ".$hash;
				
				$stmt = $mysqli -> prepare("INSERT INTO users (name, surename, username, email, password) VALUES(?, ?, ?, ?, ?)");
				
          //ss-s on string email, s on string password		
	  
				$stmt ->bind_param("ss", $name, $surename, $mail, $hash);
				$stmt ->execute();
				$stmt ->close();
      }
			}
				
			}
		
		}
	
			
function test_input($data) {
	//võtab ära tühikud,enterid,tabid
  $data = trim($data);
  //tagurpidi kaltkriipsud
  $data = stripslashes($data);
  //teeb html'i tekstiks <lährb &lt;
  $data = htmlspecialchars($data);
  return $data;
}

$mysqli->close();	
	
?>

<?php
	$page_title = "Sisselgimis leht";
	$page_file_name = "login.php";
?>

<?php require_once("../header.php"); ?>



<body bgcolor="E0FFF0">

	<h2>Log in</h2>

		
	  <form action="login.php" method="post">	
		<input name="email" type="email" placeholder="E-post"> <?php echo $email_error;?><br><br>
		<input name="password" type="password" placeholder="Parool"> <?php echo $password_error;?><br><br>
		<input name="login" type="submit" value="Log in">
	  </form>	
	<h2>Create user</h2>
	
		<form action="login.php" method="post">	
		<input name="name" type="name" value="<?php echo $name; ?>" placeholder="Eesnimi"><?php echo $name_error;?>&nbsp;&nbsp;&nbsp;&nbsp;<input name="username" type="username" placeholder="Kasutajanimi"><?php echo $username_error;?> <br><br>
		<input name="surename" type="surename" placeholder="Perekonnanimi"><?php echo $surename_error;?>&nbsp;&nbsp;&nbsp;&nbsp;<input name="password" type="password" placeholder="Parool"> <?php echo $passwordtwo_error;?> <br><br>
		<input name="email" type="email" placeholder="E-post"> <?php echo $mail_error;?><br><br>
		<input name="create" type="submit" value="Create user">
	  </form>	
</body>


<?php require_once("../footer.php"); ?>
