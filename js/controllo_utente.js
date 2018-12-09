/**
 * Created by Simone on 06/12/2018.
 */

// Pattern validazione
var espressnome = /^([a-zA-Z\xE0\xE8\xE9\xF9\xF2\xEC\x27]\s?)+$/;
var espressemail = /^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
var espressdata = /^[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])$/;
var espresstelefono = /^\+?([0-9\-]\s?)+\*?$/;
var meno = /\B\-/;
var espresscap = /^\d{5}$/;

function ControlloImmediato(object){
    /*
     * Vari stili d'errore
     *
     *   Box Shadow
     * 		object.style.boxShadow = "0px 0px 3em red";
     * 	 border-background
     * 		object.style.border = "2px solid red";
     * 		object.style.background = "#ffcccc";
     */
    switch (object.name){
        case "nome": case "cognome":
        if(object.value.length < 1 || !espressnome.test(object.value)){
            object.style.borderColor = "red";
        }else {
            object.style.borderColor = "green";
        }
        break;
        case "data_di_nascita":
            if(object.value.length < 1 || !espressdata.test(object.value)){
                object.style.borderColor = "red";
            }else {
                object.style.borderColor = "green";
            }
            break;
        case "cap":
            if(object.value.length < 1 || !espresscap.test(object.value)){
                object.style.borderColor = "red";
            }else {
                object.style.borderColor = "green";
            }
            break;
        case "email":
            if(object.value.length < 1 || !espressemail.test(object.value)){
                object.style.borderColor = "red";
            }else {
                object.style.borderColor = "green";
            }
            break
        case "tipo1": case "tipo2": case "tipo3":
        if(object.value.length < 1){
            object.style.borderColor = "#696969";
        }else {
            object.style.borderColor = "green";
        }
        break;
        case "telefono1": case "telefono2": case "telefono3":
        if(object.value.length < 1){
            object.style.borderColor = "#696969";
        }else if(object.value.length < 8 || !espresstelefono.test(object.value) || meno.test(object.value)){
            object.style.borderColor = "red";
        }else {
            object.style.borderColor = "green";
        }
        break;
        default:
            object.style.borderColor = "#696969";
    }

}

function ControllaForm(object){

    //Normalizzazione Nome e Cognome
    object.nome.value = object.nome.value.substring(0,1).toUpperCase() + object.nome.value.substring(1);
    object.cognome.value = object.cognome.value.substring(0,1).toUpperCase() + object.cognome.value.substring(1);

    //Nome
    if(object.nome.value.length < 1 || !espressnome.test(object.nome.value)){
        object.nome.focus();
        object.nome.select();
        return false;
    }
    //Cognome
    if(object.cognome.value.length < 1 || !espressnome.test(object.cognome.value)){
        object.cognome.focus();
        object.cognome.select();
        return false;
    }
    //Data di nascita
    if(object.data_di_nascita.value.length < 1 || !espressdata.test(object.data_di_nascita.value)){
        object.data_di_nascita.focus();
        object.data_di_nascita.select();
        return false;
    }
    //Email
    if(object.email.value.length < 1 || !espressemail.test(object.email.value)){
        object.email.focus();
        object.email.select();
        return false;
    }
    //Codice di Avviamento Postale
    if(object.cap.value.length < 1 || !espresscap.test(object.cap.value)){
        object.cap.focus();
        object.cap.select();
        return false;
    }
    //Telefoni
    for (var i=1; i<=3; i++){
        var itemName = object["telefono"+i];
        var itemTipoName = object["tipo"+i];
        if (itemName.value != "" || itemTipoName.value != ""){
            if (itemName.value.length < 8 || !espresstelefono.test(itemName.value) || meno.test(itemName.value)){
                itemName.focus();
                itemName.select();
                return false;
            }else if(itemTipoName.value.length < 1){
                itemTipoName.focus();
                itemTipoName.select();
                return false;
            }
        }
    }
    return true;
}
