/**
 * Created by Simone on 07/12/2018.
 */


// Pattern validazione
var espressnome = /^([a-zA-Z\xE0\xE8\xE9\xF9\xF2\xEC\x27]\s?)+$/;
var espressdata = /^[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])$/;
var espressanno = /^[0-9]{4}$/;

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
        case "libro":
            if(object.value.length < 1){
                object.style.borderColor = "red";
            }else {
                object.style.borderColor = "green";
            }
            break;
        case "utente":
            if(object.value.length < 1){
                object.style.borderColor = "red";
            }else {
                object.style.borderColor = "green";
            }
            break;
        case "data_inizio":
            if(object.value.length < 1 || !espressdata.test(object.value)){
                object.style.borderColor = "red";
            }else {
                object.style.borderColor = "green";
            }
            break;
        case "data_fine":
            if(object.value.length < 1 || !espressdata.test(object.value)){
                object.style.borderColor = "red";
            }else {
                object.style.borderColor = "green";
            }
            break;
        case "data_riconsegna":
            if(object.value.length < 1 || !espressdata.test(object.value)){
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

    //Normalizzazione Libro e Utente
    object.libro.value = object.libro.value.substring(0,1).toUpperCase() + object.libro.value.substring(1);
    object.utente.value = object.utente.value.substring(0,1).toUpperCase() + object.utente.value.substring(1);

    //Libro
    if(object.libro.value.length < 1){
        object.libro.focus();
        object.libro.select();
        return false;
    }
    //Utente
    if(object.utente.value.length < 1 || !espressnome.test(object.utente.value)){
        object.utente.focus();
        object.utente.select();
        return false;
    }
    //Data inizio prestito
    if(object.data_inizio.value.length < 1 || !espressdata.test(object.data_inizio.value)){
        object.data_inizio.focus();
        object.data_inizio.select();
        return false;
    }
    //Data fine prestito
    if(object.data_fine.value.length < 1 || !espressdata.test(object.data_fine.value)){
        object.data_fine.focus();
        object.data_fine.select();
        return false;
    }
    return true;
}
