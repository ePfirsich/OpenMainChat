<?php

require("functions.php");
require("functions.php-func-chat_lese.php");

// Userdaten setzen
id_lese($id);

if ($u_farbe_bg != "" && $u_farbe_bg != "-")
	$farbe_chat_background1 = $u_farbe_bg;

?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $body_titel; ?></title>
<meta charset="utf-8">
<?php

// Userdaten gesetzt?
if (strlen($u_id) > 0) {
    
    // Fenstername
    $fenster = str_replace("+", "", $u_nick);
    $fenster = str_replace("-", "", $fenster);
    $fenster = str_replace("ä", "", $fenster);
    $fenster = str_replace("ö", "", $fenster);
    $fenster = str_replace("ü", "", $fenster);
    $fenster = str_replace("Ä", "", $fenster);
    $fenster = str_replace("Ö", "", $fenster);
    $fenster = str_replace("Ü", "", $fenster);
    $fenster = str_replace("ß", "", $fenster);
    
    // Ohne die Stringersetzung, würde das Fenster bei Umlauten 
    // auf die Startseite springen, da id_lese ein Problem damit hat
    $userfuerrefresh = urlencode($user_nick);
	?>
	<meta http-equiv="REFRESH" CONTENT="<?php echo intval(15)
	        . "; URL=messages-popup.php?http_host=$http_host&id=$id&user=$user&user_nick=$userfuerrefresh"; ?>">
	<style type="text/css">
	<?php echo $stylesheet; ?>
	body {
		background-color:<?php echo $farbe_chat_background1; ?>;
	<?php if(strlen($grafik_background1) > 0) { ?>
		background-image:<?php echo $grafik_background1; ?>;
	<?php } ?>
	}
	a, a:link {
		color:<?php echo $farbe_chat_link1; ?>;
	}
	a:visited, a:active {
		color:<?php echo $farbe_chat_vlink1; ?>;
	}
	</style>
	<?php echo "<SCRIPT>\n"
        . "function neuesFenster(url,name) {\n"
        . "hWnd=window.open(url,name,\"resizable=yes,scrollbars=yes,width=300,height=580\");\n"
        . "}\n" . "function neuesFenster2(url) {\n"
        . "hWnd=window.open(url,\"640_$fenster\",\"resizable=yes,scrollbars=yes,width=780,height=580\");\n"
        . "}\n" . "</script>\n";
	?>
	</head>
	<body onLoad="window.scrollTo(1,300000)">
	<?php
    // Timestamp im Datensatz aktualisieren
    aktualisiere_online($u_id, $o_raum, 2);
    
    // eigene Farbe für BG gesetzt? dann die nehmen.
    if ($u_farbe_bg != "" && $u_farbe_bg != "-")
        $farbe_chat_background1 = $u_farbe_bg;
    
    if (strlen($grafik_background1) > 0) {
        $table_option = "BACKGROUND=\"$grafik_background1\"";
    } else {
        $table_option = "BGCOLOR=\"$farbe_chat_background1\"";
    }
    echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\">\n";
    echo "<tr><td><table $table_option width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\"><tr><td>\n";
    
    // Aktuelle Privat- und Systemnachrichten oder Statusmeldung ausgeben
    if (!chat_lese($o_id, $o_raum, $u_id, TRUE, $ignore, 10, TRUE, $user)) {
        echo $t['chat_msg106'];
    }
    ?>
    </td>
    </tr>
    </table>
    </td>
    </tr>
    </table>
    <?php
} else {
	// User wird nicht gefunden. Login ausgeben
	?>
	</head>
	<body onLoad='javascript:parent.location.href="index.php?http_host=<?php echo $http_host; ?>'>
	<?php
}
?>
</body>
</html>