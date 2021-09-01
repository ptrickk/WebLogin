class RegistrationFunctions
{

    static check()
    {
        "use strict";

        let username = document.getElementById("username").value;
        let email = document.getElementById("email").value;
        let password = document.getElementById("password1").value;
        let password_copy = document.getElementById("password2").value;
        //alert(username);

        if(!(username === "" || username.length == 0))
        {
            if(!(email === "" || email.length == 0))
            {
                if(!(password === "" || password.length == 0))
                {
                    if(password.localeCompare(password_copy) == 0)
                    {
                        if(email.includes('@')){
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

    static Alert(text){
        alert(text);
    }
}

