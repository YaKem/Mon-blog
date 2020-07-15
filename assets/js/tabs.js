'use strict';

/***********************************************************************************/
/* ************************************ VARIABLES **********************************/
/***********************************************************************************/

let btns = document.querySelectorAll('button');
let tabId = 0;
let target = document.getElementById('content-admin');



/***********************************************************************************/
/* ************************************ FONCTIONS **********************************/
/***********************************************************************************/

// Récupère l'id de la table stocké dans le localstorage
function getTabId() {
    if(isKeyStorage('tabId')) {  
        tabId = getStorage('tabId');
        tabId = parseInt(tabId);
        removeStorage('tabId');
        tabsManager(tabId);
    } else {
        tabsManager(0);  
    }
}

// Récupère en AJAX la table de l'url
function getTable(url) {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if(this.readyState == 4 && this.status == 200) {
            target.innerHTML = this.response;
            btnRedirectionHandler();
        }
    }
    xhr.open('GET', url, true);
    xhr.setRequestHeader("Content-Type", "text/html");
    xhr.send();
}

// Récupère la valeur id de l'attribut data de l'onglet cliqué
function showText(e) {    
    let tg = e.target.getAttribute('data-id');
    tg = parseInt(tg);                      
    tabsManager(tg);
}

// Affiche le contenu de l'onglet d'indice id
function tabsManager(id) {
    // Met en l'onglet d'id actif (orange)
    if(btns[id].classList.contains('btn-blue') == true) {
        btns[id].classList.remove('btn-blue');
    }
    btns[id].classList.add('btn-orange');
   
    // Met les autres onglets inactifs (bleu)
    for(let j = 0; j < btns.length; j++) {        
        if(j != id) {            
            // réinitialise l'état des différents blocs de contenus en hide et la couleur de tous les onglets
            if(btns[j].classList.contains('btn-orange') == true) {
                btns[j].classList.remove('btn-orange');
            }
            btns[j].classList.add('btn-blue');            
        }       
    }
    // Récupère en AJAX la table correspondant à l'id
    switch(id) {
        case 0:
            getTable('index.php?r=article_ajax');
            break;
        case 1:
            getTable('index.php?r=comment_ajax');
            break;
        case 2:
            getTable('index.php?r=category_ajax');
            break;
        case 3:
            getTable('index.php?r=message_ajax');
            break;
    }
}

// Affiche la table qui était affichée avant l'action sur la table-même en stockant dans le localstorage l'id de l'attribut data des liens
function btnRedirectionHandler() {  
    let allBtn = document.querySelectorAll('#admin a');

    for(let i = 0; i < allBtn.length; i++) {
        allBtn[i].addEventListener('click', function() {
            tabId = this.getAttribute('data-tab');
            setStorage('tabId', tabId);
        });
    }
}



/***********************************************************************************/
/* ************************************** CODE *************************************/
/***********************************************************************************/


window.addEventListener('DOMContentLoaded', function() { 
    // On affiche la table de id s'il existe, sinon on affiche celle de la table 0 (Article)
    getTabId();
    
    // On installe un écouteur d'évènement sur chaque onglet, enclanché au click
    for(let i = 0; i < btns.length; i++) {
        btns[i].addEventListener('click', showText);
    }
});