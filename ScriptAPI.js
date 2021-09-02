let request = new XMLHttpRequest();

window.setInterval(requestData, 3000);//Intervall zur Abfrage der UserDataAPI wird gesetzt (alle 3 Sekunden eine Abfrage)

function requestData() {
    "use strict";
    let username = document.getElementById("username_data").value;//Gewünschter Username wird abgefragt
    let url = "UserDataAPI.php?usr=" + username;//URL der API wird zusammengesetzt

    request.open("GET", url);//API wird abgefragt
    request.onreadystatechange = processData;//processData() wird gesetzt um die API Daten auszuwerten
    request.send(null);

}

function processData() {
    "use strict";
    if (request.readyState === 4) { // Uebertragung = DONE
        if (request.status === 200) { // HTTP-Status = OK
            if (request.responseText != null){//responseText darf nicht null sein
                pollData(request.responseText);//JSON-String kann ausgewertet werden
            }
            else console.error("Dokument ist leer");
        } else console.error("Uebertragung fehlgeschlagen");
    } else ;
}

function pollData(input){
    "use strict";
    let object = JSON.parse(input);//Formatierung des JSON-Strings zu einem Objekt
    if(object[0] == 1){//Wurden erfolgreich Nutzerdaten ausgelesen (1 = ja, 0 = Nutzer mit diesem Namen existiert nicht)
        if(object[1][2] != ""){//Wenn der Nutzer einen Namen eingetragen hat wird dieser ausgegeben
            document.getElementById("name-field").innerHTML = "Name: " + object[1][2] + " " + object[1][3];
        }
        document.getElementById("email-field").innerHTML = "Email: " + object[1][4];//E-Mail Adresse wird ausgegeben
        if(object[1][5] != "0000-00-00"){//Wenn Geburtstag eingetragen wurde, kann dieser ausgegeben werden
            document.getElementById("birthday-field").innerHTML = "Geburtstag: " + object[1][5];
        }
        document.getElementById("visits-field").innerHTML = "Reloads (live): " + object[2].length;
        if(object[2].length > 0){//Wenn der Nutzer mindestens einmal auf der MainPage war kann der Timestamp seines letzten Besuchs ausgegeben werden
            document.getElementById("last-visit-field").innerHTML = "Letzter Reload (live): " + object[2][object[2].length -1];
        }
    }
    else {//Falls kein Nutzer geladen werden konnte bleiben alle Felder leer
        document.getElementById("name-field").innerHTML = "";
        document.getElementById("email-field").innerHTML = "";
        document.getElementById("birthday-field").innerHTML = "";
        document.getElementById("visits-field").innerHTML = "";
    }
}