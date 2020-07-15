'use strict';

/***********************************************************************************/
/* ************************************ FONCTIONS **********************************/
/***********************************************************************************/

// Quand l'ascenseur se trouve à une position supérieure à 50px alors la flèche devient visible
function scrollToUp() {
    let pos = document.documentElement.scrollTop;
    let div = document.getElementById('scrollUp');
    
    if(pos > 50) {
        div.setAttribute('style', 'right: 10px');
    } else {
        div.removeAttribute('style');
    }
}



/***********************************************************************************/
/* ************************************** CODE *************************************/
/***********************************************************************************/

// Mise en place d'un gestionnaire d'évènement qui se déclenchera au mouvement du scroll
document.addEventListener('scroll', scrollToUp);