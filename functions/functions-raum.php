<?php
function raeume_auflisten($order, $extended) {
	global $t, $id, $timeout, $raumstatus1, $raumstatus2, $admin;
	
	if (!isset($order)) {
		$order = "r_name";
	}
	
	$text = "";
	
	// Liste der Räume mit der Anzahl der Benutzer aufstellen
	$query = "SELECT r_id,count(o_id) as anzahl FROM raum LEFT JOIN online ON r_id=o_raum WHERE ((UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout OR o_id IS NULL) GROUP BY r_id";
	$result = sqlQuery($query);
	
	while ($row = mysqli_fetch_object($result)) {
		$anzahl_user[$row->r_id] = $row->anzahl;
	}
	mysqli_free_result($result);
	
	// Liste der Räume und der Raumbesitzer lesen
	$query = "SELECT raum.*,u_id,u_nick,u_level,u_punkte_gesamt,u_punkte_gruppe FROM raum left join user on r_besitzer=u_id GROUP BY r_name ORDER BY $order";
	$result = sqlQuery($query);
	if ($result && mysqli_num_rows($result) > 0) {
		// extended==Ansicht mit Details im extra beiten Fenster
		if (true) {
			if (isset($extended) && ($extended == 1)) {
				$rlink = "<center><span class=\"smaller\">" . "<b><a href=\"inhalt.php?bereich=raum&id=$id&order=$order\">" . $t['raum_raumliste_einfach'] . "</a></b>" . "</span></center>\n";
				$text .= "<script language=\"javascript\">\n"
					. "window.resizeTo(800,600); window.focus();"
					. "</script>\n";
			} else {
				$rlink = "<center><span class=\"smaller\">" . "<b><a href=\"inhalt.php?bereich=raum&id=$id&order=$order&extended=1\">" . $t['raum_raumliste_ausuehrlich'] . "</a></b>" . "</span></center>\n";
			}
			$text .= "$rlink<br>";
		}
		$text .= "<table style=\"width:100%\">\n";
		$text .= "<tr><td class=\"tabelle_kopfzeile\">$t[raeume_raum] <a href=\"inhalt.php?bereich=raum&id=$id&order=r_name\" class=\"button\"><span class=\"fa fa-arrow-down icon16\"></span></a></td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[raeume_benutzer_online]</td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[raeume_status] <a href=\"inhalt.php?bereich=raum&id=$id&order=r_status1,r_name\" class=\"button\"><span class=\"fa fa-arrow-down icon16\"></span></a></td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[raeume_art] <a href=\"inhalt.php?bereich=raum&id=$id&order=r_status2,r_name\" class=\"button\"><span class=\"fa fa-arrow-down icon16\"></span></a></td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[raeume_raumbesitzer] <a href=\"inhalt.php?bereich=raum6id=$id&order=u_nick\" class=\"button\"><span class=\"fa fa-arrow-down icon16\"></span></a></td>";
		if (isset($extended) && $extended) {
			$text .= "<td class=\"tabelle_kopfzeile\">$t[raeume_smilies]</td>";
			$text .= "<td class=\"tabelle_kopfzeile\">$t[raeume_mindestpunkte]</td>";
			$text .= "<td class=\"tabelle_kopfzeile\">$t[raeume_topic]</td>";
			$text .= "<td class=\"tabelle_kopfzeile\">$t[raeume_eintritt]</td>";
			$text .= "<td class=\"tabelle_kopfzeile\">$t[raeume_austritt]</td>";
		}
		
		$text .= "</tr>\n";
		$i = 1;
		$bgcolor = 'class="tabelle_zeile1"';
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$uu_id = $row['u_id'];
			if ($row['r_status2'] == "P") {
				$b1 = "<small><b>";
				$b2 = "</b></small>";
			} else {
				$b1 = "<small>";
				$b2 = "</small>";
			}
			// raum nur anzeigen falls offen, moderiert, besitzer oder admin...
			// "m" -> moderiert, "M" -> moderiert+geschlossen.
			if ($row['r_status1'] == "O" || $row['r_status1'] == "m" || $uu_id == $u_id || $admin) {
				$rlink = "<a href=\"inhalt.php?bereich=raum&id=$id&aktion=edit&raum=" . $row['r_id'] . "\">" . $row['r_name'] . "</a>";
				
				// Anzahl der Benutzer online ermitteln
				if ((isset($anzahl_user[$row['r_id']])) && ($anzahl_user[$row['r_id']] > 0)) {
					$anzahl = $anzahl_user[$row['r_id']];
				} else {
					$anzahl = 0;
				}
				// Bei 0 Benutzern keinen Link anzeigen
				if( $anzahl == 0) {
					$ulink = "$anzahl $t[raeume_benutzer]";
				} else {
					$ulink = "<a href=\"inhalt.php?bereich=benutzer&id=$id&schau_raum=" . $row['r_id'] . "\">$anzahl $t[raeume_benutzer]</a>";
				}
				
				$text .= "<tr><td $bgcolor>$b1" . $rlink . "$b2</td>";
				$text .= "<td $bgcolor>$b1" . $ulink . "$b2&nbsp;</td>";
				
				$text .= "<td $bgcolor>$b1". $raumstatus1[$row['r_status1']] . "$b2&nbsp;</td>";
				$text .= "<td $bgcolor>$b1" . $raumstatus2[$row['r_status2']] . "$b2&nbsp;</td>";
				$text .= "<td $bgcolor>$b1" . zeige_userdetails($row['u_id'], $row, false) . $b2 . "</td>";
				if ((isset($extended)) && ($extended)) {
					if ($row['r_smilie'] == 1) {
						$r_smilie = $t['raum_erlaubt'];
					} else {
						$r_smilie = $t['raum_verboten'];
					}
					$text .= "<td $bgcolor>$b1" . $r_smilie . "&nbsp;$b2</td>";
					
					if( $row['r_min_punkte'] == "1") {
						$punkte_name = $t['raum_punkt'];
					} else {
						$punkte_name = $t['raum_punkte'];
					}
					$text .= "<td $bgcolor>$b1" . $row['r_min_punkte'] . " $punkte_name$b2</td>";
					
					$temp = htmlspecialchars($row['r_topic']);
					$temp = str_replace('&amp;lt;', '<', $temp);
					$temp = str_replace('&amp;gt;', '>', $temp);
					$temp = str_replace('&amp;quot;', '"', $temp);
					$text .= "<td $bgcolor>$b1" . $temp . "&nbsp;$b2</td>";
					
					$temp = htmlspecialchars($row['r_eintritt']);
					$temp = str_replace('&amp;lt;', '<', $temp);
					$temp = str_replace('&amp;gt;', '>', $temp);
					$temp = str_replace('&amp;quot;', '"', $temp);
					$text .= "<td $bgcolor>$b1" . $temp . "&nbsp;$b2</td>";
					
					$temp = htmlspecialchars($row['r_austritt']);
					$temp = str_replace('&amp;lt;', '<', $temp);
					$temp = str_replace('&amp;gt;', '>', $temp);
					$temp = str_replace('&amp;quot;', '"', $temp);
					$text .= "<td $bgcolor>$b1" . $temp . "&nbsp;$b2</td>";
				}
				
				$text .= "</tr>\n";
				$i++;
				if (($i % 2) > 0) {
					$bgcolor = 'class="tabelle_zeile1"';
				} else {
					$bgcolor = 'class="tabelle_zeile2"';
				}
			}
		}
		$text .= "</table>";
		
		mysqli_free_result($result);
		
		return $text;
	}
}

function raum_editieren($raum_id, $raumname, $status1, $status2, $smilies, $min_punkte, $topic, $eintrittsnachricht, $austrittsnachricht, $raumbesitzer) {
	global $lobby, $t, $raumstatus1, $raumstatus2, $id, $u_id;
	
	$text = "<form action=\"inhalt.php?bereich=raum\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	$text .= "<table style=\"width:100%;\">\n";
	
	if ($raumname == $lobby) {
		// Raum
		$text .= zeige_formularfelder("text", $zaehler, $t['raeume_raum'], "", $raumname . "<input type=\"hidden\" name=\"r_name\" value=\"$raumname\">");
		$zaehler++;
	} else {
		// Raum
		$text .= zeige_formularfelder("input", $zaehler, $t['raeume_raum'], "r_name", $raumname);
		$zaehler++;
		
		
		// Status
		$selectbox = "<select name=\"r_status1\">\n";
		
		$a = 0;
		reset($raumstatus1);
		while ($a < count($raumstatus1)) {
			$keynum = key($raumstatus1);
			$status1Select = $raumstatus1[$keynum];
			if ($keynum == $status1) {
				$selectbox .= "<option selected value=\"$keynum\">$status1Select\n";
			} else {
				$selectbox .= "<option value=\"$keynum\">$status1Select\n";
			}
			next($raumstatus1);
			$a++;
		}
		
		$selectbox .= "</select>\n";
		
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		$text .= "<tr>";
		$text .= "<td $bgcolor style=\"text-align:right\">$t[raeume_status]</td>\n";
		$text .= "<td $bgcolor>$selectbox</td>\n";
		$text .= "</tr>\n";
		$zaehler++;
		
		
		// Art
		$selectbox = "<select name=\"r_status2\">\n";
		$a = 0;
		reset($raumstatus2);
		while ($a < count($raumstatus2)) {
			$keynum = key($raumstatus2);
			$status2Select = $raumstatus2[$keynum];
			if ($keynum == $status2) {
				$selectbox .= "<option selected value=\"$keynum\">$status2Select\n";
			} else {
				$selectbox .= "<option value=\"$keynum\">$status2Select\n";
			}
			next($raumstatus2);
			$a++;
		}
		
		$selectbox .= "</select>\n";
		
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		$text .= "<tr>";
		$text .= "<td $bgcolor style=\"text-align:right\">$t[raeume_art]</td>\n";
		$text .= "<td $bgcolor>$selectbox</td>\n";
		$text .= "</tr>\n";
		$zaehler++;
	}
	
	
	// Smilies
	$value = array($t['raum_verboten'], $t['raum_erlaubt']);
	$text .= zeige_formularfelder("selectbox", $zaehler, $t['raeume_smilies'], "r_smilie", $value, $smilies);
	$zaehler++;
	
	
	// Raumbesitzer
	if($raum_id != 0) {
		// Den Raumbesitzer nur beim Editieren anzeigen
		$query = "SELECT u_nick FROM user WHERE u_id= '" . escape_string($raumbesitzer) . "' AND `u_level` != 'Z' AND `u_level` != 'C';";
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_object($result);
			$raumbesitzerName = $row->u_nick;
		} else {
			$raumbesitzerName = "";
		}
		$text .= zeige_formularfelder("input", $zaehler, $t['raeume_raumbesitzer'], "r_besitzer_name", $raumbesitzerName, 0, "7");
		$zaehler++;
	}
	
	
	// Mindestpunkte
	if ($raumname != $lobby) {
		$text .= zeige_formularfelder("input", $zaehler, $t['raeume_mindestpunkte'], "r_min_punkte", $min_punkte, 0, "7");
		$zaehler++;
	}
	
	
	// Topic
	$text .= zeige_formularfelder("textarea", $zaehler, $t['raeume_topic'], "r_topic", $topic);
	$zaehler++;
	
	
	// Eintrittsnachricht
	$text .= zeige_formularfelder("textarea", $zaehler, $t['raeume_eintritt'], "r_eintritt", $eintrittsnachricht);
	$zaehler++;
	
	
	// Austrittsnachricht
	$text .= zeige_formularfelder("textarea", $zaehler, $t['raeume_austritt'], "r_austritt", $austrittsnachricht);
	$zaehler++;
	
	
	// Eintragen
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>";
	if($raum_id == 0 || $raumname == $lobby) {
		$text .= "<td $bgcolor style=\"text-align:right\">&nbsp;</td>\n";
	} else {
		$text .= "<td $bgcolor style=\"text-align:right\"><input type=\"submit\" name=\"loesch\" value=\"$t[raum_loeschen]\"></td>\n";
	}
	$text .= "<td $bgcolor><input type=\"submit\" value=\"$t[raum_speichern]\"></td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	
	$text .= "</table>\n";
	
	$text .= "<input type=\"hidden\" name=\"r_id\" value=\"$raum_id\">\n";
	if($raum_id == 0) {
		// Bei neuen Räumen wird automatisch der Ersteller als Besitzer gesezt
		$text .= "<input type=\"hidden\" name=\"r_besitzer\" value=\"$u_id\">\n";
	}
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"edit\">\n";
	$text .= "<input type=\"hidden\" name=\"formular\" value=\"1\">\n";
	$text .= "</form>\n";
	
	return $text;
}
?>