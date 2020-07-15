'use strict';

/***********************************************************************************/
/* ************************************* VARIABLE **********************************/
/***********************************************************************************/

let flash = document.getElementById('flash');



/***********************************************************************************/
/* ************************************ FONCTIONS **********************************/
/***********************************************************************************/

// Cache les messages flash après un délai
function hideFlash() {
   
    // console.log(flash);
    
    if(flash != null) {
            window.setTimeout(function () {
                flash.style.display = 'none';
                // console.log('ok');
            }, 4000);
    }
}



/***********************************************************************************/
/* ************************************** CODE *************************************/
/***********************************************************************************/

// Installe un gestionnaire d'évènement qui se déclenchera lorsque le DOM est totalement construit
window.addEventListener('DOMContentLoaded', function () {      
    hideFlash();
});