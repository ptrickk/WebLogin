class RegistrationFunctions
{
    //Überprüft ob alle Daten im Registrierungs-Formular korrekt eingetragen wurden
    static check()
    {
        "use strict";

        let username = document.getElementById("username").value.trim();
        let email = document.getElementById("email").value;
        let password = document.getElementById("password1").value.trim();
        let password_copy = document.getElementById("password2").value.trim();
        //alert(username);

        if(!(username === "" || username.length == 0))//Username muss gesetzt sein
        {
            if(!(email === "" || email.length == 0))//E-Mail muss gesetzt sein
            {
                if(!(password === "" || password.length == 0))//Passwort muss gesetzt sein
                {
                    if(password.localeCompare(password_copy) == 0)//Beide Passwörter müssen übereinstimmen
                    {
                        if(email.includes('@')){//Die E-Mail-Adresse muss ein '@' enthalten
                            return true;
                        }
                        else {
                            document.getElementById("email_text").hidden = false;
                        }
                    }
                    else {
                        document.getElementById("pwd_text").innerHTML = "*Die beiden Passwörter stimmen nicht überein";
                        document.getElementById("pwd_text").hidden = false;
                    }
                }
                else {
                    document.getElementById("pwd_text").hidden = false;
                }
            }
            else {
                document.getElementById("email_text").hidden = false;
            }
        }
        else {
            document.getElementById("username_text").hidden = false;
        }

        return false;
    }
}

