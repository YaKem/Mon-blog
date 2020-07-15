'use strict';

/***********************************************************************************/
/* ************************************ FONCTIONS **********************************/
/***********************************************************************************/

function slider() {
    
    let img = document.querySelector('#slide img');
    
    let index = 0;
    
    setInterval(function () {
    
        index++;
    
        if(index < 5) {
    
            img.src = 'ASSETS/images/slides/' + index + '.jpg';
    
        } else {
    
            index = 0;
    
        }
    
    }, 3000);
}



/***********************************************************************************/
/* ************************************** CODE *************************************/
/***********************************************************************************/

window.addEventListener('DOMContentLoaded', slider);


