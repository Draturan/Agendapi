/**
 * Created by Simone on 07/12/2018.
 */


// Pattern validazione
var espressnome = /^([a-zA-Z\xE0\xE8\xE9\xF9\xF2\xEC\x27]\s?)+$/;
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
        case "titolo":
        if(object.value.length < 1){
            object.style.borderColor = "red";
        }else {
            object.style.borderColor = "green";
        }
        break;
        case "autore":
            if(object.value.length < 1 || !espressnome.test(object.value)){
                object.style.borderColor = "red";
            }else {
                object.style.borderColor = "green";
            }
            break;
        case "data":
            if(object.value.length < 1 || !espressanno.test(object.value)){
                object.style.borderColor = "red";
            }else {
                object.style.borderColor = "green";
            }
            break;
        case "genere":
            if(object.value.length < 1 || !espressnome.test(object.value)){
                object.style.borderColor = "red";
            }else {
                object.style.borderColor = "green";
            }
            break
        default:
            object.style.borderColor = "#696969";
    }

}

function ControllaForm(object){

    //Normalizzazione Titolo e Autore
    object.titolo.value = object.titolo.value.substring(0,1).toUpperCase() + object.titolo.value.substring(1);
    object.autore.value = object.autore.value.substring(0,1).toUpperCase() + object.autore.value.substring(1);

    //Titolo
    if(object.titolo.value.length < 1){
        object.titolo.focus();
        object.titolo.select();
        return false;
    }
    //Autore
    if(object.autore.value.length < 1 || !espressnome.test(object.autore.value)){
        object.autore.focus();
        object.autore.select();
        return false;
    }
    //Data
    if(object.data.value.length < 1 || !espressanno.test(object.data.value)){
        object.data.focus();
        object.data.select();
        return false;
    }
    //Genere
    if(object.genere.value.length < 1 || !espressnome.test(object.genere.value)){
        object.genere.focus();
        object.genere.select();
        return false;
    }
    return true;
}
