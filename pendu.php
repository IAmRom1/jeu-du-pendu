<?php
session_start();
// session_destroy();

function remplace($mot_a_trouver, $mot_affiche, $c)
{
    $c = strtoupper($c);  

    for ($i = 0; $i < strlen($mot_a_trouver); $i++) {
        if ($c == strtoupper($mot_a_trouver[$i])) {
            $mot_affiche[$i] = $c;
        }
    }

    return $mot_affiche;
}

if (!isset($_SESSION['mot_a_trouver'])) {
    $mots = file('./liste_mots.txt', FILE_IGNORE_NEW_LINES);
    if ($mots !== false && count($mots) > 0) {
        $_SESSION['mot_a_trouver'] = $mots[array_rand($mots)];
        $_SESSION['mot_affiche'] = str_repeat('-', strlen($_SESSION['mot_a_trouver']));
        $_SESSION['lettres_mauvaises'] = '';
        $_SESSION['essais'] = 6;
    } else {
       
    }
}

extract($_SESSION);
$gagne = $perdu = false;
$c = filter_input(INPUT_GET, 'c', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z]$/")));
$c = strtoupper($c);  
$mot_affiche_recu = remplace($mot_a_trouver, $mot_affiche, $c);

if ($mot_affiche == $mot_affiche_recu) {
    $lettres_mauvaises .= " $c";
    if ($c) $essais--;
} else {
    $mot_affiche = $mot_affiche_recu;
}

if ($mot_a_trouver == $mot_affiche) {
    $gagne = true;
}
if ($essais == 0) {
    $perdu = true;
}

if ($gagne) {
    echo "<h2>BRAVO VOUS AVEZ GAGNÉ !</h2>";
    echo "<p>Le mot était : $mot_a_trouver</p>";
    session_unset();
    echo '<p><a href="pendu.php">Recommencer</a></p>';
} elseif ($perdu) {
    echo "<h2>VOUS AVEZ PERDU !</h2>";
    echo "<p>Le mot était : $mot_a_trouver</p>";
    session_unset();
    echo '<p><a href="pendu.php">Recommencer</a></p>';
} else {
    echo <<<_END
    <p style="font-family:'Courier New'"><b>$mot_affiche</b></p>
    <p>Mauvaises lettres utilisées: $lettres_mauvaises</p>
    <p>Essais restants: $essais</p>
    <p></p>
    <form action="pendu.php" method="get">
        <p>Lettre: <input type="text" required autofocus minlength="1" maxlength="1" size="1" name="c"></p>
        <p><input type="submit" value="valider"></p>
    </form>
_END;
    $_SESSION = compact('mot_a_trouver', 'lettres_mauvaises', 'mot_affiche', 'essais');
}
?>
