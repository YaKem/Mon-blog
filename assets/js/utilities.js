'use strict';

/***********************************************************************************/
/* **************************** FONCTIONS DU LOCALSTORAGE **************************/
/***********************************************************************************/

// Récupère les données de clef Name
function getStorage(name) {
    let jsonData;
    jsonData = window.localStorage.getItem(name);
    return JSON.parse(jsonData);
}

// Stocke dans le localstorage les données data à la clef name
function setStorage(name, data) {
    let jsonData;
    jsonData = JSON.stringify(data);
    window.localStorage.setItem(name, jsonData);
}

// Supprime les données à la clef name
function removeStorage(name) {
    window.localStorage.removeItem(name);
}

// Vérifie si données existe à la clef name, si oui retourne true sinon false
function isKeyStorage(name) {
    return window.localStorage.getItem(name) != null ? true : false;
}

// Vide entièrement le localstorage
function clearStorage() {
    window.localStorage.clear();
}