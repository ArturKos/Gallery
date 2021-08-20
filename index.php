
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3c.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<meta http-equiv=”Content-Type” content=”text/html; charset=UTF-8” />
<meta name="description" content="Galeria zdjęć" />
<meta name="keywords" content="" />
<meta name="author" content="artur" >
<meta name="generator" content="Bluefish 2.2.3" >
<META NAME="Language" CONTENT="pl">
<link rel="Stylesheet" type="text/css" href="./css/style.css" />
<link rel="Stylesheet" type="text/css" href="./css/strona.css" />
<?php
// Rozpoczynamy sesję
session_start();
include 'func.php';
//echo password_hash('artuR', PASSWORD_BCRYPT);
// tutaj umieścimy kod sprawdzający
// sprawdzamy czy użytkownik jest zalogowany (true), lub zwracamy false
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['logowanie'])) {
        // można dodatkowo wyświetlić odpowiedni komunikat jeżeli hasło jest puste
        $pass = $_POST['pass'] ?? null;
        $_SESSION['login'] = $_POST['login'] ?? null;
        $admin_hash = require_once 'haslo.php';
        // możemy nadpisać tablicę samym hasłem
        $admin_hash = array_key_exists($_SESSION['login'], $admin_hash) ? $admin_hash[$_SESSION['login']] : null;
        if (!empty($pass) && password_verify($pass, $admin_hash)) {
            $_SESSION['admin'] = true;
            log_user($_SESSION['login']);
        } else {
            echo "<center><p style='"."color:red;'".">Login lub hasło nie pasuje!</p></center>";
        }
    } elseif (isset($_POST['logout'])) {
        $_SESSION['admin'] = false;
        session_regenerate_id();
        header('Location: http://' . $_SERVER['HTTP_HOST']);
    }
}

$is_admin = $_SESSION['admin'] ?? false;

if ($is_admin):
global $aktualny;

if(isset($_GET['directory']))
  {
   $aktualny =urldecode($_GET['directory']);
   if(!isset($_SESSION['login']) || (strstr($aktualny, "./konta/".$_SESSION['login']."/data")==FALSE))//sprawdzanie czy ktoś kombinuje z url
   {
     $_SESSION['admin'] = false;
     $_SESSION['login'] = "";
     session_regenerate_id();
     header('Location: http://' . $_SERVER['HTTP_HOST']);
     goto logowanie;
   }
	}else
	{
	 $aktualny = "./konta/".$_SESSION['login']."/data";
	}
global $mini;
global $file;

?>
<HTML>
	<HEAD>
		<TITLE>
		   <?php
       $replace = "./konta/".$_SESSION['login']."/data";
       if($replace == $aktualny)
        echo "Jesteś w katalogu głównym strony"; else
       {
		      $w = str_replace($replace."/","",$aktualny);
			     echo "Jesteś w $w";
       }
			 ?>
		</TITLE>
	</HEAD>
		<BODY>
			<div id="NAGLOWEK">
					<center><img src="./obrazy/strona_domowa.gif" border="0" class="banner" align="middle" alt="[Rozmiar: 9952 bajtów]">
					</center>
			</div>
		       <div id="MENU" style="position:relative;">
		           					<dl>
								<dt>Wybierz kategorię:</dt>
									<?php
									 Szukaj_katalogow($aktualny);
          global $aktualny;
          $t = urldecode($aktualny);
          while($t[-1]!='/') $t = substr($t, 0, -1);
          $t = substr($t, 0, -1);
          if(strstr($t, "./konta/".$_SESSION['login']."/data")!=FALSE)
            echo "<center><a href=index.php?directory=".urlencode($t)."\target=\_blank\>Wstecz</a></center>";
									?>
								</dl>
        <script type = "text/javascript" src = "//www.deszczowce.pl/skrypty/rich_trivia.php?template=zielony"></script>
						</div>
		       	<div id="INFORMACJE">
          <center>
            <form action="" method="POST">
             <input name="logout" type="hidden"></input>
             <input type="image" src="./obrazy/wyloguj2.gif" width="150px" alt="Wyloguj się" />
            </form>
            <a href="./ssl/artur-kos.asuscomm.com.crt"\target=\_blank\><img width="150px" src='./obrazy/certyfikat.gif'></a>
          </center>
						</div>
			<div id="TRESC">
				<?php
				 Szukaj_plikow($aktualny);
				?>
			</div>
			<div id="STOPKA">
			<?php
			echo "<center>";
			echo "<a href='index.php'\target=\_blank\><img class='banner' src='./obrazy/cofnij.gif'></a>";
			?>
			</center>
			</div>
		</BODY>
</HTML>
<?php else: logowanie: ?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8"/>
    <title>Podaj dane logowania</title>
</head>
<body>
    <main>
				<center>
        <p>Aby kontynuować musisz podać login oraz hasło:</p>
        <form action="" method="POST">
            <input name="login" type="login" placeholder="Podaj swój login"/>
						<input name="pass" type="password" placeholder="Podaj hasło"/>
            <button name="logowanie" type="submit">Zaloguj się</button>
        </form>
				</center>
    	<center><img src="./obrazy/brein_animated.gif" border="0" align="middle" width="200px" alt="[Rozmiar: 9952 bajtów]">
					</center>
    </main>
</body>
</html>
 <?php endif; ?>
