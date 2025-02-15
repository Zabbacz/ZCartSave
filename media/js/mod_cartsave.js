document.addEventListener('DOMContentLoaded', function() {
    // Získání odkazu podle ID
    let link = document.querySelector('.listtoggler');
    let linkLoad = document.querySelectorAll('.load_named_cart');
    let linkDrop = document.querySelectorAll('.drop_named_cart');
    let linkCartName = document.querySelector('.cart_name_input');
    let actionButtonSaveDefault = document.getElementById('save_default_button');
    let actionButtonSave = document.getElementById('save_button');
    let actionButtonLoadDefault = document.getElementById('load_default_button');
    let actionButtonLoad = document.getElementById('load_button');
    let actionMergeChange = document.querySelector('.mergecheckbox');

if (actionMergeChange) {
    if (actionMergeChange.checked){
        document.getElementById('label_merge').innerHTML = MOD_CARTSAVE_MERGE_TRUE;
    }
    else{
        document.getElementById('label_merge').innerHTML = MOD_CARTSAVE_MERGE_FALSE;
    }
    actionMergeChange.addEventListener('change', function(event) {
        if (actionMergeChange.checked){
            document.getElementById('label_merge').innerHTML = MOD_CARTSAVE_MERGE_TRUE;
        }
        else{
            document.getElementById('label_merge').innerHTML = MOD_CARTSAVE_MERGE_FALSE;
        }
            });
        }
    
    if (actionButtonSaveDefault) {
        actionButtonSaveDefault.addEventListener('click', function(event) {
            event.preventDefault(); // Zabránění výchozímu chování odkazu
                   let cartAction = this.getAttribute('cart-action');
        let cartId = this.getAttribute('cart-id');
        // Kontrola, zda jsou hodnoty platné
        if (cartAction && cartId) {
            actionCart(cartAction, cartId);
        } else {
            console.error('Atributy "cart-action" nebo "cart-id" nejsou správně nastaveny');
            return false; // Prevence dalšího vykonání
        }

        });
    }

if (actionButtonSave) {
    actionButtonSave.addEventListener('click', function(event) {
        event.preventDefault(); // Zabránění výchozímu chování odkazu

               let cartAction = this.getAttribute('cart-action');
        let cartId = this.getAttribute('cart-id');
        // Kontrola, zda jsou hodnoty platné
        if (cartAction && cartId) {
            actionCart(cartAction, cartId);
        } else {
            console.error('Atributy "cart-action" nebo "cart-id" nejsou správně nastaveny');
            return false; // Prevence dalšího vykonání
        }

    });
} 

if (actionButtonLoadDefault) {
    actionButtonLoadDefault.addEventListener('click', function(event) {
        event.preventDefault(); // Zabránění výchozímu chování odkazu

        let cartAction = this.getAttribute('cart-action');
        let cartId = this.getAttribute('cart-id');
        // Kontrola, zda jsou hodnoty platné
        if (cartAction && cartId) {
            actionCart(cartAction, cartId);
        } else {
            console.error('Atributy "cart-action" nebo "cart-id" nejsou správně nastaveny');
            return false; // Prevence dalšího vykonání
        }

    });
} 

if (actionButtonLoad) {
    actionButtonLoad.addEventListener('click', function(event) {
        event.preventDefault(); // Zabránění výchozímu chování odkazu

        let cartAction = this.getAttribute('cart-action');
        let cartId = this.getAttribute('cart-id');
        // Kontrola, zda jsou hodnoty platné
        if (cartAction && cartId) {
            actionCart(cartAction, cartId);
        } else {
            console.error('Atributy "cart-action" nebo "cart-id" nejsou správně nastaveny');
            return false; // Prevence dalšího vykonání
        }

    });
} 


    if (link) {
        // Připojení event listeneru pro kliknutí
        link.addEventListener('click', function(event) {
            event.preventDefault(); // Zabránění výchozímu chování odkazu
            let moduleId = this.getAttribute('data-id');
            toggleList(moduleId);  
        });
    }
    if (linkLoad) {
        for (let i = 0; i < linkDrop.length; i++) {
        // Připojení události click
        linkLoad[i].addEventListener('click', function(event) {
            event.preventDefault();  // Zabráníme výchozímu chování odkazu (navigace)
            
            let cartNameId = this.getAttribute('cartName-Id');
            let cartId = this.getAttribute('cart-id');
            
            loadCart(cartNameId, cartId);
        });
    }
    }
        if (linkDrop) {
        // Připojení události click
        for (let i = 0; i < linkDrop.length; i++) {
        linkDrop[i].addEventListener('click', function(event) {
            event.preventDefault();  // Zabráníme výchozímu chování odkazu (navigace)
            
            let cartNameId = this.getAttribute('cartName-Id');
            let cartId = this.getAttribute('cart-id');
            let cartName = this.getAttribute('cart-name');
            
            dropCart(cartNameId, cartId, cartName);
        });
    }
    }

    if (linkCartName) {
        // Připojení události click
        linkCartName.addEventListener('keyup', function(event) {
            event.preventDefault();  // Zabráníme výchozímu chování odkazu (navigace)
            let cartId = this.getAttribute('cart-id');
            let thisCart = this.getAttribute('thisCart');
            alterDisplayFields(thisCart,cartId);
        });
        linkCartName.addEventListener('blur', function(event) {
            event.preventDefault();  // Zabráníme výchozímu chování odkazu (navigace)
            
            let cartId = this.getAttribute('cart-id');
            let thisCart = this.getAttribute('this');
            alterDisplayFields(this,cartId);
        });
    }
});

function toggleList(module_id) {
        // Zkontrolujeme, zda existují elementy, na které chceme aplikovat toggle
    let listElement = document.querySelector('.cart_list_' + module_id);
    let togglerElement = document.querySelector('.listtoggler_');

    if (!listElement) return;

    if (listElement.style.display === "none" || element.style.display === "") {
        // Pokud je element skrytý, postupně jej zobrazíme
        listElement.style.display = "block";
        fadeIn(listElement);
    } else {
        // Pokud je element zobrazený, postupně jej skryjeme
        fadeOut(listElement);
    }
    if (togglerElement) {
        // Skrytí odkazu
        togglerElement.style.display = 'none';
    } else {
        console.warn('Element .listtoggler_' + module_id + ' nenalezen');
    }
    return false;
}    

function loadCart(cart_name_id, module_id) {
	  let d = document.getElementById('cart_name_id_'+module_id); 
	  if (d !== null) {
		  d.value = cart_name_id; 
		  
		  let d2 = document.getElementById('myaction_'+module_id); 
		  if (d2 !== null) {
			  d2.value = 'loadid'; 
			  
			  let f = getForm(module_id); 
			  
			  let callback = function() {
				f.submit();   
			  };
			  checkMergeCart(module_id, callback); 
			  
			  
		  }
	  }
	  return false; 
  }
  
   function dropCart(cart_name_id, module_id, name) {
	  let d = document.getElementById('cart_name_id_'+module_id); 
	  if (d !== null) {
		  d.value = cart_name_id; 
		  
		  let d2 = document.getElementById('myaction_'+module_id); 
                  
		  if (d2 != null) {
			  d2.value = 'dropid'; 
			  
			  let dtx = MOD_CARTSAVE_QUESTION; 
			  dtx = dtx.split('{cart_name}').join(name); 
			  if (confirm(dtx)) {
				let f = getForm(module_id); 
				f.submit(); 
				return false; 
			  }
		  }
	  }
	  return false; 
  }
  
  function actionCart(action, module_id) {
		  if (action === 'save') {
			   var d = document.getElementById('cart_name_'+module_id); 
			   if (d) {
				   if (d.value === '') {
					   alert(MOD_CARTSAVE_ERROR_NAME_MISSING_SAVE); 
					   return false; 
				   }
			   }
		  }

		  if (action === 'save_default') {
			   let d = document.getElementById('cart_name_default_'+module_id);
			   if (d) {
							let r = confirm(MOD_CARTSAVE_DELETEACRT);
							if (r == false) {
					   return false; 
				   }
			   }

		  }

		  if (action === 'load_default') {
			   var d = document.getElementById('cart_name_default_'+module_id); 
			   if (d) {
                               let MergeCheck = document.querySelector('.mergecheckbox');
                               if (MergeCheck){
                                    if (MergeCheck.checked){
                                        var r = confirm(MOD_CARTSAVE_MERGE_TRUE);
					if (r === false) {
					   return false; 
                                        }
                                    }
                                    else{
                                        var r = confirm(MOD_CARTSAVE_MERGE_FALSE);
					if (r === false) {
					   return false; 
                                        }

                                    }
                                }
				   }
			   }

                    if (action === 'load') {
			   var d = document.getElementById('cart_name_'+module_id); 
			   if (d) {
                               let MergeCheck = document.querySelector('.mergecheckbox');
                               if (MergeCheck){
                                    if (MergeCheck.checked){
                                        var r = confirm(MOD_CARTSAVE_MERGE_FALSE);
					if (r === false) {
					   return false; 
                                        }
                                    }
                                    else{
                                        var r = confirm(MOD_CARTSAVE_MERGE_FALSE);
					if (r === false) {
					   return false; 
                                        }

                                    }
                                }
				if (d.value === '') {
                                    alert(MOD_CARTSAVE_ERROR_NAME_MISSING_LOAD); 
                                    return false; 
				 }
			   }
		    }
		  
		  let d2 = document.getElementById('myaction_'+module_id); 
		  if (d2 !== null) {
			  d2.value = action; 
			  let f = getForm(module_id); 
			  f.submit(); 
		
			}
	  return false; 
  }
  
 function alterDisplayFields(el, module_id) {
    var fromwrap = document.getElementById('cartsaverform_' + module_id);
    // Zkontrolujeme, zda element 'el' má prázdnou hodnotu
    if (el.value === '') {
        fromwrap.classList.remove('is_not_empty');
        fromwrap.classList.add('is_empty');
    } else {
        fromwrap.classList.remove('is_empty');
        fromwrap.classList.add('is_not_empty');
    }
}
 

function fadeIn(element) {
    let opacity = 0; // Začínáme od 0 opacity
    let interval = setInterval(function() {
        if (opacity >= 1) {
            clearInterval(interval);
        }
        element.style.opacity = opacity;
        opacity += 0.05; // Zvyšujeme opacity o 5% za interval
    }, 16);  // Interval 16ms pro plynulý efekt (60fps)
}

function fadeOut(element) {
    let opacity = 1; // Začínáme od 100% opacity
    let interval = setInterval(function() {
        if (opacity <= 0) {
            clearInterval(interval);
            element.style.display = "none"; // Skryjeme element po dokončení fade-out
        }
        element.style.opacity = opacity;
        opacity -= 0.05; // Snižujeme opacity o 5% za interval
    }, 16);  // Interval 16ms pro plynulý efekt (60fps)
}

function checkMergeCart(module_id, callback) {
    let d = document.getElementById('merge_' + module_id); // Získání elementu podle ID
    if (d !== null) {
        // Získání datových atributů pomocí dataset
        let q = d.dataset.question;
        if (q) {
            let yes = d.dataset.questionyes;
            let no = d.dataset.questionno;
            let cancel = d.dataset.questioncancel;
            
            // Vytvoření dialogového okna
            let dialog = document.createElement('div');
            dialog.classList.add('dialog');
            
            let message = document.createElement('p');
            message.textContent = q;
            dialog.appendChild(message);
            
            // Vytvoření tlačítek
            let buttonYes = document.createElement('button');
            buttonYes.textContent = yes || 'Yes';
            buttonYes.onclick = function() {
                d.value = 1;
                callback();
                closeDialog();
                return false;
            };
            dialog.appendChild(buttonYes);

            let buttonNo = document.createElement('button');
            buttonNo.textContent = no || 'No';
            buttonNo.onclick = function() {
                d.value = 0;
                callback();
                closeDialog();
                return false;
            };
            dialog.appendChild(buttonNo);

            let buttonCancel = document.createElement('button');
            buttonCancel.textContent = cancel || 'Cancel';
            buttonCancel.onclick = function() {
                closeDialog();
                return false;
            };
            dialog.appendChild(buttonCancel);

            // Přidání dialogu do dokumentu
            document.body.appendChild(dialog);
            
            // Funkce pro zavření dialogu
            function closeDialog() {
                dialog.remove();
            }
            
            // Návrat false zamezí dalšímu vykonání (pokud je to potřeba)
            return false;
        }
    }
    callback(); // Pokud není potřeba dialog, zavoláme callback
}

function getForm(module_id) {
    // Získání elementu podle ID
    let el = document.getElementById('cartsaverform_' + module_id);
    if (el) {
        let tag = el.tagName.toUpperCase(); // Získání názvu tagu (např. 'FORM')
        if (tag !== 'FORM') {
            // Získání datového atributu 'data-ref' z elementu
            let ref = el.dataset.ref; // 'dataset.ref' odpovídá jQuery(el).data('ref')
            if (ref) {
                // Pokud je 'ref' přítomen, vrátíme element s tímto ID
                let dx = document.getElementById(ref);
                return dx;
            }
        }
        
        if (tag === 'FORM') {
            // Pokud je tag FORM, vrátíme přímo tento element
            return el;
            
            
        }
    }
}
