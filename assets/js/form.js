'use strict';

/***********************************************************************************/
/* ******************************** DONNEES FORMULAIRE *****************************/
/***********************************************************************************/

let formComment = document.getElementById('form-comment');
let parent = document.getElementById('id-parent');
let input = document.getElementById('name');
let btnForm = document.querySelector('input[type="button"]');
let msgError = document.querySelectorAll('#form-comment p');



/***********************************************************************************/
/* ******************************* FONCTIONS FORMULAIRE ****************************/
/***********************************************************************************/

// Raffraichit l'affichage des commentaires en AJAX
function refreshComment() {
    let target = document.getElementById('comment-list');
    let articleId = target.getAttribute('data-id');
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if(this.readyState == 4 && this.status == 200) {
            target.innerHTML = this.responseText;
            // Gère les évènements liés au bouton du commentaire auquel on répond
            let btns = document.getElementsByClassName('btn-reply');
            for(let i = 0; i < btns.length; i++) {
                btns[i].addEventListener('click', function() {
                    
                    input.focus();
                    input.style.border = "1px solid red";
                    
                    // Ajouter index du commentaire parent au formulaire
                    let button = event.target;
                    let idParent = button.getAttribute('data-idcomment');
                    
                    parent.value = idParent;            
                });
            }
        }
    }
    xhr.open('POST', 'index.php?r=comment', true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
    xhr.send("id_article=" + articleId);
}

// Créé un commentaire (récupère les données du formulaire à l'aide de l'objet FormData puis les envoie en post, cela en AJAX)
function addComment() {

    if(formComment.name.value != "" && formComment.mail.value != "" && formComment.content.value != "") {

        // Si tous les champs sons remplis alors on ajoute le commentaire
        // On récupère les données du formulaire avec l'objet FormData
        let data = new FormData(formComment);    
        
        // On envoie en AJAX avec la méthode POST les données du formulaire
        let xhr = new XMLHttpRequest();
        
        xhr.onreadystatechange = function() {
            if(this.readyState == 4 && this.status == 200) {
                parent.value = 0;
                formComment.reset();
                input.blur();
                input.style.border = "1px solid grey";
            }
        }   
        xhr.open("POST", "index.php?r=create_comment", true);
        xhr.send(data);
    } else {
        // Si au moins un des champs est vide alors on stocke les valeurs dans le localstorage
        let inputValues = [formComment.name.value, formComment.mail.value, formComment.content.value];
        setStorage('inputs', inputValues);
    }
}

// Réinitialise le formulaire
function resetForm() {
    // On vide les inputs du formulaire
    formComment.reset();
    // On cache les messages d'erreurs
    hideMsgErrors();
    // On met à 0 l'identifiant, déclare le commentaire comme n'étant pas en réponse à un autre
    parent.value = 0;
    // Enlève le focus sur l'élément courant
    input.blur();
    // On enlève la bordure rouge     
    input.style.border = "1px solid grey";
    // Après un petit délai, on centre la page sur le formulaire
    window.setTimeout(function () {
        window.scrollTo(0, formComment.offsetTop - 50);
    }, 2);
}

// Cache les messages d'erreurs
function hideMsgErrors() {
    for(let i = 0; i < msgError.length; i++) {
        msgError[i].style.display = 'none';
        formComment.elements[i + 3].value = '';
    }
}



/***********************************************************************************/
/* ******************************** CODE PRINCIPAL *********************************/
/***********************************************************************************/

// On met en place un événement qui se déclenche lorsque tout le DOM est contruit
window.addEventListener('DOMContentLoaded', function () {
    
    // Cache tous les messages d'erreurs
    hideMsgErrors();
    
    // Si données dans le local storage à la clef inputs alors on affiche les messages d'erreurs puis on les supprime
    if(isKeyStorage('inputs')) {
        let inputValues = getStorage('inputs');
        for(let i = 3; i < 6; i++) {           
            if(inputValues[i - 3] != '') {
                formComment.elements[i].value = inputValues[i - 3];
            } else {
                msgError[i - 3].style.display = 'block';
            }
        }
        removeStorage('inputs');
    }

    // Raffraichit l'affichage des commentaires        
    refreshComment();

    // Installation d'un gestionnaire d'évènement sur le formulaire qui se déclenchera lors de sa soumission    
    
    formComment.addEventListener('submit', addComment);
    
    // Gestionnaire d'évènement qui au click enclenche la réinitialisation du formulaire
    let btnReset = document.getElementById('btn-reset');
    btnReset.addEventListener('click', resetForm);

});