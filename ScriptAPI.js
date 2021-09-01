let request = new XMLHttpRequest();

window.setInterval(requestData, 3000);

function requestData() {
    "use strict";
    let username = document.getElementById("username_data").value;
    let url = "UserDataAPI.php?usr=" + username;

    //document.getElementById("ausgabe").innerHTML = "HALLO";
    request.open("GET", url);
    request.onreadystatechange = processData;
    request.send(null);

}

function processData() {
    "use strict";
    if (request.readyState === 4) { // Uebertragung = DONE
        if (request.status === 200) { // HTTP-Status = OK
            if (request.responseText != null){
                pollData(request.responseText);
            }
            else console.error("Dokument ist leer");
        } else console.error("Uebertragung fehlgeschlagen");
    } else ;
}

function pollData(input){
    "use strict";
    let object = JSON.parse(input);
    if(object[0] == 1){
        if(object[1][2] != ""){
            document.getElementById("name-field").innerHTML = "Name: " + object[1][2] + " " + object[1][3];
        }
        document.getElementById("email-field").innerHTML = "Email: " + object[1][4];
        if(object[1][5] != "0000-00-00"){
            document.getElementById("birthday-field").innerHTML = "Geburtstag: " + object[1][5];
        }
        document.getElementById("visits-field").innerHTML = "Reloads (live): " + object[2].length;
        if(object[2].length > 0){
            document.getElementById("last-visit-field").innerHTML = "Letzter Reload (live): " + object[2][object[2].length -1];
        }
    }
    else {
        document.getElementById("name-field").innerHTML = "";
        document.getElementById("email-field").innerHTML = "";
        document.getElementById("birthday-field").innerHTML = "";
        document.getElementById("visits-field").innerHTML = "";
    }
}