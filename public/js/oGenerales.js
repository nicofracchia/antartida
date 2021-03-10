var oGen = oGen || {};

// FUNCIONES ELIMINAR
oGen.fnCargaClickEliminar = function(){
    Array.from(document.getElementsByClassName('iconoBorrarListados')).forEach((el) => {
        el.addEventListener('click', () => {oGen.fnEliminar(el)}, false);
    });
}

oGen.fnEliminar = (el) => {
    if(confirm(el.getAttribute("data-mensaje"))){
        let formData = new FormData();
            formData.append("_token", el.getAttribute("data-token"));

        fetch(el.getAttribute("data-ruta"), {
            method: 'post',
            mode: 'cors',
            credentials: 'same-origin',
            body: formData
        }).then(function(response){
            return response.json();
        }).then(function(json){
            if(json.estado == 'OK')
                el.parentElement.parentElement.remove();
            alert(json.mensaje);
        }).catch(function(error){
            console.log('ERROR:', error);
        })
    }
}

// LOAD
oGen.load = () => {
    oGen.fnCargaClickEliminar();
}