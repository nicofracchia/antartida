var oGen = oGen || {};

// MODAL GENERAL
oGen.fnCerrarModal = function(self){
    if(document.querySelector('.baseModal') !== null)
        document.querySelector('.baseModal').remove();
}

oGen.fnCargaModal = function(ruta){

    fetch(ruta)
    .then(function(response){
        return response.text();
    }).then(function(html){
        
        oGen.fnCerrarModal();

        var parser = new DOMParser();
        var doc = parser.parseFromString(html, 'text/html');
        var modal = doc.querySelector('.baseModal');
        
        document.querySelector('section').append(modal);

    }).catch(function(error){
        console.log('ERROR:', error);
    })

}

oGen.agregarOpcionCombo = function(combo, valor, texto){
    let option = document.createElement('option');
        option.value = valor;
        option.text = texto;

    combo.add(option);
    combo.value = valor;
}

// FUNCIONES ELIMINAR PARA LISTADOS
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

// MARCAS
oGen.fnGuardaMarcaModal = function(){
    let combo = document.getElementById('producto_marca');
    let ruta = document.getElementById('ruta_marcas_guardar').value;
    let formData = new FormData();
        formData.append("marca", document.getElementById('modalMarca_marca').value);
        formData.append("token", document.getElementById('modalMarca_token').value);

    fetch(ruta, {
        method: 'post',
        mode: 'cors',
        credentials: 'same-origin',
        body: formData
    }).then(function(response){
        return response.json();
    }).then(function(json){
        oGen.agregarOpcionCombo(combo, json.id, json.marca);
        oGen.fnCerrarModal();
    }).catch(function(error){
        console.log('ERROR:', error);
    })
}

// LOAD
oGen.load = () => {
    oGen.fnCargaClickEliminar();
}