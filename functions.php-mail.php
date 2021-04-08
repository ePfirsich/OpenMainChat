<?php

function formular_neue_email($neue_email, $m_id = "") {
	// Gibt Formular für Nicknamen zum Versand einer Mail aus
	
	global $id, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $mysqli_link, $dbase;
	global $PHP_SELF;
	
	if (!$eingabe_breite) {
		$eingabe_breite = 30;
	}
	
	// Nickname aus u_nick lesen und setzen
	if (isset($neue_email['m_an_uid']) && $neue_email['m_an_uid']) {
		$query = "select `u_nick` FROM `user` WHERE `u_id` = " . intval($neue_email[m_an_uid]);
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$an_nick = mysqli_result($result, 0, 0);
			if (strlen($an_nick) > 0) {
				$neue_email['an_nick'] = $an_nick;
			}
		}
	}
	
	if ($m_id) {
		$box = "Mail weiterleiten an:";
	} else {
		$box = "Neue Mail senden:";
	}
	$text = '';
	
	$text .= "<form name=\"mail_neu\" action=\"$PHP_SELF\" METHOD=POST>\n"
		. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
		. "<input type=\"hidden\" name=\"aktion\" value=\"neu2\">\n"
		. "<input type=\"hidden\" name=\"m_id\" value=\"$m_id\">\n"
		. "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">\n";
	
	if (!isset($neue_email['an_nick'])) {
	$neue_email['an_nick'] = "";
	}
	$text .= "<b>Nickname:</b> "
		. $f1 . "<input type=\"text\" name=\"neue_email[an_nick]\" value=\"" . $neue_email['an_nick'] . "\" SIZE=20>" . "&nbsp;"
		. "<input type=\"submit\" name=\"los\" value=\"WEITER\">" . $f2;
	
	// Box anzeigen
	show_box_title_content($box, $text);
}

function formular_neue_email2($neue_email, $m_id = "") {
	// Gibt Formular zum Versand einer neuen Mail aus
	
	global $id, $http_host, $eingabe_breite1, $eingabe_breite2, $PHP_SELF, $f1, $f2, $f3, $f4, $mysqli_link, $dbase, $u_id;
	global $PHP_SELF;
	global $u_punkte_gesamt;
	
	if (!$eingabe_breite1)
		$eingabe_breite1 = 30;
	if (!$eingabe_breite2)
		$eingabe_breite2 = 40;
	echo '<script language="JavaScript">
	function zaehle()
	{
	betr=document.mail_neu.elements["neue_email[m_betreff]"].value.length;
	text=document.mail_neu.elements["neue_email[m_text]"].value.length;
	document.mail_neu.counter.value=betr+text;
	}
	</script>
	';
	
	$text = '';
	if ($m_id) {
		// Alte Mail lesen und als Kopie in Formular schreiben
		$box = "Mail weiterleiten an";
		$query = "select m_betreff,m_text from mail "
			. "where m_id=" . intval($m_id) . " AND m_an_uid=$u_id";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$row = mysqli_fetch_object($result);
			
			$neue_email['m_betreff'] = $row->m_betreff . " [Weiterleitung]";
			$neue_email['m_text'] = $row->m_text;
			
			$neue_email['m_text'] = str_replace("<b>", "_",
				$neue_email['m_text']);
			$neue_email['m_text'] = str_replace("</b>", "_",
				$neue_email['m_text']);
			
			$neue_email['m_text'] = str_replace("<i>", "*",
				$neue_email['m_text']);
			$neue_email['m_text'] = str_replace("</i>", "*",
				$neue_email['m_text']);
			
		}
		@mysqli_free_result($result);
		
	} else {
		// Neue Mail versenden
		$box = "Neue Mail an";
	}
	
	// Signatur anfügen
	if (!isset($neue_email['m_text']))
		$neue_email['m_text'] = htmlspecialchars(erzeuge_fuss(""));
	
	// Userdaten aus u_id lesen und setzen
	if ($neue_email['m_an_uid']) {
		$query = "select u_nick,u_email,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,o_id, "
			. "date_format(u_login,'%d.%m.%y %H:%i') as login, "
			. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online "
			. "from user left join online on o_user=u_id "
			. "WHERE u_id = ".$neue_email['m_an_uid']." ";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$row = mysqli_fetch_object($result);
			
			$email = "";
			$email_select = "&nbsp;";
			// E-Mail Adresse vorhanden?
			$email_bekannt = false;
			if (strlen($row->u_email) > 0) {
				$email = $f1 . " (E-Mail: <a href=\"mailto:$row->u_email\">"
					. htmlspecialchars($row->u_email) . "</A>)" . $f2;
				$email_bekannt = true;
			}
			
			$email_select = "<b>Art des Mailversands:</b>&nbsp;<select name=\"neue_email[typ]\">";
			if (isset($neue_email['typ']) && $neue_email['typ'] == 1) {
				$email_select .= "<option value=\"0\">Mail in Chat-Mailbox\n";
				if ($email_bekannt)
					$email_select .= "<option selected value=\"1\">E-Mail an "
						. $row->u_email . "\n";
			} else {
				$email_select .= "<option selected value=\"0\">Mail in Chat-Mailbox\n";
				if ($email_bekannt)
					$email_select .= "<option value=\"1\">E-Mail an "
						. $row->u_email . "\n";
			}
			$email_select .= "</select>\n";
			
			$text .= "<form name=\"mail_neu\" action=\"$PHP_SELF\" METHOD=POST>\n"
				. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
				. "<input type=\"hidden\" name=\"aktion\" value=\"neu3\">\n"
				. "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">\n"
				. "<input type=\"hidden\" name=\"neue_email[m_an_uid]\" value=\"$neue_email[m_an_uid]\">\n"
				. "<table style=\"width:100%;\">";
			
			if ($row->o_id == "" || $row->o_id == "NULL") {
				$row->online = "";
				}
			
			if (!isset($neue_email['m_betreff'])) {
				$neue_email['m_betreff'] = "";
				}
			$text .= "<tr><td style=\"vertical-align:top;\" align=\"right\" class =\"tabelle_zeile2\"><b>Betreff:</b></td><td colspan=2 class =\"tabelle_zeile2\">"
				. $f1
				. "<input type=\"TEXT\" name=\"neue_email[m_betreff]\" value=\""
				. $neue_email['m_betreff'] . "\" SIZE="
				. ($eingabe_breite1)
				. "
				 ONCHANGE=zaehle() ONFOCUS=zaehle() ONKEYDOWN=zaehle() ONKEYUP=zaehle()>"
				. $f2 . "<input name=\"counter\" size=3></td></tr>"
				. "<tr><td style=\"vertical-align:top;\" align=\"right\" class=\"tabelle_zeile1\"><b>Ihr Text:</b></td><td colspan=2 class=\"tabelle_zeile1\">"
				. $f1 . "<TEXTAREA COLS=" . ($eingabe_breite2)
				. " ROWS=20 name=\"neue_email[m_text]\" ONCHANGE=zaehle() ONFOCUS=zaehle() ONKEYDOWN=zaehle() ONKEYUP=zaehle()>"
				. $neue_email['m_text'] . "</TEXTAREA>\n" . $f2
				. "</td></tr>"
				. "<tr><td class=\"tabelle_zeile2\">&nbsp;</td><td class=\"tabelle_zeile2\">$email_select</td>"
				. "<td ALIGN=\"right\" class=\"tabelle_zeile2\">" . $f1
				. "<input type=\"SUBMIT\" name=\"los\" value=\"VERSENDEN\">"
				. $f2 . "</td></tr>\n" . "</table></form>\n";
			
			// Box anzeigen
			show_box_title_content($box, $text);
			
		} else {
			echo "<P><b>Fehler:</b> Der User mit ID '$neue_email[m_an_uid]' existiert nicht!</P>\n";
		}
	}
}

function zeige_mailbox($aktion, $zeilen) {
	// Zeigt die Mails in der Übersicht an
	
	global $id, $mysqli_link, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $dbase, $mysqli_link, $u_nick, $u_id;
	global $PHP_SELF, $chat;
	
	if (!$eingabe_breite) {
		$eingabe_breite = 30;
	}
	
	switch ($aktion) {
		case "alle";
			$query = "select m_id,m_status,m_von_uid,date_format(m_zeit,'%d.%m.%y um %H:%i') as zeit,m_betreff,u_nick "
				. "FROM mail LEFT JOIN user on m_von_uid=u_id "
				. "WHERE m_an_uid=$u_id order by m_zeit desc";
			$button = "LÖSCHEN";
			$titel = "Mails in der Mailbox für";
			break;
		
		case "geloescht":
			$query = "select m_id,m_status,m_von_uid,date_format(m_zeit,'%d.%m.%y um %H:%i') as zeit,m_betreff,u_nick "
				. "FROM mail LEFT JOIN user on m_von_uid=u_id "
				. "WHERE m_an_uid=$u_id AND m_status='geloescht' "
				. "order by m_zeit desc";
			$button = "WIEDERHERSTELLEN";
			$titel = "Mails im Papierkorb von";
			break;
		
		case "normal":
		default;
			$query = "select m_id,m_status,m_von_uid,date_format(m_zeit,'%d.%m.%y um %H:%i') as zeit,m_betreff,u_nick "
				. "FROM mail LEFT JOIN user on m_von_uid=u_id "
				. "WHERE m_an_uid=$u_id AND m_status!='geloescht' "
				. "order by m_zeit desc";
			$button = "LÖSCHEN";
			$titel = "Mails in der Mailbox für";
	}
	
	$result = mysqli_query($mysqli_link, $query);
	if ($result) {
		$anzahl = mysqli_num_rows($result);
		$text = '';
		$box = "$anzahl $titel $u_nick:";
		
		$text .= "<form name=\"mailbox\" action=\"$PHP_SELF\" method=\"POST\">\n"
				. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
				. "<input type=\"hidden\" name=\"aktion\" value=\"loesche\">\n"
				. "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">";
		
		if ($anzahl == 0) {
			// Leere Mailbox
			$text .=  "Ihre Mailbox ist leer.";
			
		} else {
			// Mails anzeigen
			$text .= "<table style=\"width:100%;\">\n"
				. "<tr><td style=\"font-weight:bold;\">" . $f1 . "Löschen" . $f2 . "</td><td style=\"font-weight:bold;\">" . $f1
				. "von" . $f2 . "</td><td style=\"font-weight:bold;\">" . $f1 . "Betreff" . $f2
				. "</td><td style=\"font-weight:bold;\">" . $f1 . "Datum" . $f2 . "</td><td style=\"font-weight:bold;\">" . $f1
				. "Status" . $f2 . "</td></tr>\n";
			
			$i = 0;
			$bgcolor = 'class="tabelle_zeile1"';
			while ($row = mysqli_fetch_object($result)) {
				
				$url = "<a href=\"" . $PHP_SELF
					. "?id=$id&http_host=$http_host&aktion=zeige&m_id="
					. $row->m_id . "\">";
				if ($row->m_status == "neu"
					|| $row->m_status == "neu/verschickt") {
					$auf = "<b>" . $f1;
					$zu = $f2 . "</b>";
				} else {
					$auf = $f1;
					$zu = $f2;
				}
				
				if ($row->u_nick == "NULL" || $row->u_nick == "") {
					$von_nick = "$chat";
				} else {
					$von_nick = $row->u_nick;
				}
				
				$text .= "<tr><td style=\"text-align:center;\" $bgcolor>" . $auf
					. "<input type=\"checkbox\" name=\"loesche_email[]\" value=\""
					. $row->m_id . "\">" . $zu . "</td>" . "<td $bgcolor>" . $auf . $url
					. $von_nick . "</a>" . $zu . "</td>" . "<td style=\"width:50%;\" $bgcolor>"
					. $auf . $url
					. htmlspecialchars($row->m_betreff) . "</a>"
					. $zu . "</td>" . "<td $bgcolor>" . $auf . $row->zeit . $zu
					. "</td>" . "<td $bgcolor>" . $auf . $row->m_status . $zu . "</td>"
					. "</tr>\n";
				
				if (($i % 2) > 0) {
					$bgcolor = 'class="tabelle_zeile1"';
				} else {
					$bgcolor = 'class="tabelle_zeile2"';
				}
				$i++;
			}
			
			$text .= "<tr><td $bgcolor><input type=\"checkbox\" onClick=\"toggle(this.checked)\">"
				. $f1 . " Alle Auswählen" . $f2 . "</td>\n"
				. "<td ALIGN=\"right\" $bgcolor colspan=\"4\">" . $f1
				. "<input type=\"SUBMIT\" name=\"los\" value=\"$button\">"
				. $f2 . "</td></tr></table>\n";
		}
		
		$text .= "</form>\n";
		
		// Box anzeigen
		show_box_title_content($box, $text);
	}
}

function zeige_email($m_id) {
	// Zeigt die Mail im Detail an
	
	global $id, $mysqli_link, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $dbase, $mysqli_link, $u_nick, $u_id;
	global $PHP_SELF, $chat, $t;
	
	if (!$eingabe_breite) {
		$eingabe_breite = 30;
	}
	
	$query = "select mail.*,date_format(m_zeit,'%d.%m.%y um %H:%i') as zeit,u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe "
		. "FROM mail LEFT JOIN user on m_von_uid=u_id "
		. "WHERE m_an_uid=$u_id AND m_id=" . intval($m_id) . " order by m_zeit desc";
	$result = mysqli_query($mysqli_link, $query);
	
	if ($result && mysqli_num_rows($result) == 1) {
		
		// Mail anzeigen
		$row = mysqli_fetch_object($result);
		
		if ($row->u_nick == "NULL" || $row->u_nick == "") {
			$von_nick = "$chat";
		} else {
			$von_nick = "<b>" . user($row->u_id, $row, TRUE, FALSE) . "</b>";
		}
		$row->m_text = str_replace("<ID>", $id, $row->m_text);
		$row->m_text = str_replace("<HTTP_HOST>", $http_host, $row->m_text);
		
		$text = '';
		$box = $t['sonst2'];
		
		// Mail ausgeben
		$text .= "<table style=\"width:100%;\">";
		$text .= "<tr><td style=\"text-align:right;\" class=\"tabelle_zeile1\"><b>"
			. $f1 . "Von:" . $f2 . "</b></td><td colspan=3 class=\"tabelle_zeile1\">"
			. $von_nick . "</td></tr>\n"
			. "<tr><td style=\"text-align:right;\" class=\"tabelle_zeile2\"><b>"
			. $f1 . "Am:" . $f2 . "</b></td><td colspan=3 class=\"tabelle_zeile2\">" . $f1 . $row->zeit
			. $f2 . "</td></tr>\n"
			. "<tr><td style=\"text-align:right;\" class=\"tabelle_zeile1\"><b>"
			. $f1 . "Betreff:" . $f2 . "</b></td><td colspan=3 class=\"tabelle_zeile1\">" . $f1
			. htmlspecialchars($row->m_betreff) . $f2
			. "</td></tr>\n"
			. "<tr><td style=\"text-align:right;\" class=\"tabelle_zeile2\"><b>"
			. $f1 . "Status:" . $f2 . "</b></td><td colspan=3 class=\"tabelle_zeile2\">" . $f1
			. $row->m_status . $f2 . "</td></tr>\n"
			. "<tr><td class=\"tabelle_zeile1\">&nbsp;</td><td colspan=3 class=\"tabelle_zeile1\">"
			. $f1 . str_replace("\n", "<br>\n", $row->m_text)
			. $f2 . "</td></tr>\n";
		
		// Formular zur Löschen, Beantworten
		$text .= "<tr><td class=\"tabelle_zeile1\">&nbsp;</td>"
			. "<td style=\"text-align:left;\" class=\"tabelle_zeile1\"><form name=\"mail_antworten\" action=\"$PHP_SELF\" method=\"POST\">" . $f1
			. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
			. "<input type=\"hidden\" name=\"m_id\" value=\"" . $row->m_id . "\">\n"
			. "<input type=\"hidden\" name=\"aktion\" value=\"antworten\">\n"
			. "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">\n"
			. "<input type=\"SUBMIT\" name=\"los\" value=\"ANTWORTEN\">" . $f2 . "</form></td>\n"
			. "<td style=\"text-align:center;\" class=\"tabelle_zeile1\"><form name=\"mail_antworten\" action=\"$PHP_SELF\" method=\"POST\">" . $f1
			. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
			. "<input type=\"hidden\" name=\"m_id\" value=\"" . $row->m_id . "\">\n"
			. "<input type=\"hidden\" name=\"aktion\" value=\"weiterleiten\">\n"
			. "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">\n"
			. "<input type=\"SUBMIT\" name=\"los\" value=\"WEITERLEITEN\">" . $f2 . "</form></td>\n"
			. "<td style=\"text-align:right;\" class=\"tabelle_zeile1\"><form name=\"mail_loeschen\" action=\"$PHP_SELF\" method=\"POST\">" . $f1
			. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
			. "<input type=\"hidden\" name=\"loesche_email\" value=\"" . $row->m_id . "\">\n"
			. "<input type=\"hidden\" name=\"aktion\" value=\"loesche\">\n"
			. "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">\n"
			. "<input type=\"SUBMIT\" name=\"los\" value=\"LÖSCHEN\">" . $f2
			. "</form></td></tr>\n";
		
		$text .= "</table>\n";
		
		// Box anzeigen
		show_box_title_content($box, $text);
		
		if ($row->m_status != "geloescht") {
			// E-Mail auf gelesen setzen
			unset($f);
			$f['m_status'] = "gelesen";
			$f['m_zeit'] = $row->m_zeit;
			schreibe_db("mail", $f, $row->m_id, "m_id");
		}
	}
}

function loesche_mail($m_id, $u_id) {
	
	// Löscht eine Mail der ID m_id
	
	global $id, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $dbase, $mysqli_link, $u_nick, $u_id;
	
	$query = "select m_zeit,m_id,m_status FROM mail "
		. "WHERE m_id=" . intval($m_id) . " AND m_an_uid=$u_id";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) == 1) {
		
		$row = mysqli_fetch_object($result);
		$f['m_zeit'] = $row->m_zeit;
		
		if ($row->m_status != "geloescht") {
			$f['m_status'] = "geloescht";
			$f['m_geloescht_ts'] = date("YmdHis");
		} else {
			$f['m_status'] = "gelesen";
			$f['m_geloescht_ts'] = '00000000000000';
		}
		;
		schreibe_db("mail", $f, $row->m_id, "m_id");
	} else {
		echo "<P><b>Fehler:</b> Diese Mail nicht kann nicht gelöscht werden!</P>";
	}
	@mysqli_free_result($result);
	
}

?>