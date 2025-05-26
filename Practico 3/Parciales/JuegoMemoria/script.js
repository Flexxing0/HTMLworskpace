document.addEventListener('DOMContentLoaded', () => {
    const btnMensaje = document.querySelector("#btnCtnMensaje");
    const botonInicia = getId("btnIniciarJ");
    const botonSesion = getId("id-enviar-ingreso");
    const idUsuario = getId("idusuario");
    const tablero = getId("tablero");

    const usuarioLog = 'logged_user';
    let usuarioActual = null;
    let numerosJuego = [];

    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/; SameSite=Lax";
    }

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    function eraseCookie(name) {
        document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT; SameSite=Lax;';
    }

    function getUserData(username) {
        const cookieValue = getCookie(username);
        return cookieValue ? JSON.parse(cookieValue) : null;
    }

    function saveUserData(username, data) {
        setCookie(username, JSON.stringify(data), 30); // Guardar por 30 días
    }

    function getId(id){
        return document.getElementById(id);
    }

    function ocultaElemento(elemento){
        var e = getId(elemento);
        if(e.classList.contains("oculto")){
            return;
        }else{
        e.classList.add("oculto");
        }
    }

    function muestraElemento(elemento){
        var e = getId(elemento);
        if(e.classList.contains("oculto")){
            e.classList.remove("oculto");
        }else{return;}
    }

    function muestraMensajeBienvenida(){
        ocultaElemento("inicio-sesion");
        ocultaElemento("tablero");
        muestraElemento("mensaje-bienvenida");
    }

    function muestraTablero(){
        ocultaElemento("inicio-sesion");
        ocultaElemento("mensaje-bienvenida");
        muestraElemento("tablero");
    }

    function muestraLogin(){
        ocultaElemento("mensaje-bienvenida");
        ocultaElemento("tablero");
        muestraElemento("inicio-sesion");
    }

    function iniciaSesion(){
        const datos = idUsuario.value.trim();
        datosUsuario = getUserData(datos);
        if(datosUsuario){
            usuarioActual = {...datosUsuario};
            usuarioActual.visitas++;
            saveUserData(usuarioActual.usuario,{
                usuario: usuarioActual.usuario,
                visitas: usuarioActual.visitas,
                partidas: usuarioActual.partidas,
                partidaNum: partidasNum,
                jugadaActual: usuarioActual.jugadaActual,
                intentos: usuarioActual.intentos,
                aciertos: usuarioActual.aciertos,
                cartasSeleccionadas: usuarioActual.cartasSeleccionadas
            });
        }else{
            usuarioActual={
                usuario: datos,
                visitas: 0,
                partidas: 0,
                partidaNum:0,
                jugadaActual: [],
                intentos: 0,
                aciertos: 0,
                cartasSeleccionadas: []
            };
            saveUserData(usuarioActual.usuario,usuarioActual);
        }
        setCookie(usuarioLog,datos,1);
        actualizaSitio();

    }   

    function actualizaSitio(){
        if (!usuarioActual) return;
        mensajeBienvenida();
        muestraMensajeBienvenida();
        
    }

    function mensajeBienvenida(){
        const cont = getId("mensaje");
        let texto='<p>';
        if(usuarioActual){
            texto+="Bienvenido " +usuarioActual.usuario+" es tu visita: "+usuarioActual.visitas;
        }else{
            texto+="Bienvenido "+usuarioActual.usuario+" es tu primera visita!";
        }
        texto+="</p><br>";
        cont.innerHTML+=texto;
    }

    function inicializaTablero(){
        if(usuarioActual.jugadaActual && usuarioActual.jugadaActual.length > 0){
            actualizaTablero();
            
        }else{
            muestraTablero();
        }
        muestraTablero();
    }

    function actualizaTablero(){//utilizar solo en iniciaJuego
    const botones = document.querySelectorAll(".boton"); // Usa querySelectorAll para obtener un NodeList
    if(usuarioActual && usuarioActual.jugadaActual && usuarioActual.jugadaActual.length > 0){
        for(let i=0; i<botones.length; i++){ // Declara i con 'let' y usa botones.length
            const boton = botones[i];
            boton.innerHTML = usuarioActual.jugadaActual[i];
             // Asegúrate de que los IDs sean únicos y consistentes

            // Resetear estado visual de las cartas al actualizar el tablero
            //boton.classList.remove("revelada", "emparejada");
            //boton.disabled = false;

            // Si la carta está entre las seleccionadas actualmente, revelarla
            if(usuarioActual.cartasSeleccionadas.includes(boton.id)){
                revelaCarta(boton.id);
            }
            // Si la carta ya fue emparejada (necesitas una lista de cartas emparejadas)
            // Esto requiere una nueva propiedad en usuarioActual, por ejemplo:
            // usuarioActual.cartasEmparejadas = ['card-x', 'card-y'];
            // if (usuarioActual.cartasEmparejadas.includes(boton.id)) {
            //     revelaCarta(boton.id); // Permanecen reveladas
            //     boton.classList.add('emparejada');
            //     boton.disabled = true; // Deshabilitar clic
            // }
        }
    } else {
        // Si no hay jugada actual, ocultar todos los botones o vaciar su contenido
        botones.forEach(boton => {
            boton.innerHTML = '';
            boton.classList.remove("revelada", "emparejada");
            boton.disabled = false;
        });
    }

    const datos = getId("tablero-datos"); // Asegúrate de seleccionar el elemento correcto
    let texto = '';
    if (usuarioActual) {
        texto += '<p>Partida: ' + usuarioActual.partidas + '</p> <br>';
        texto += '<p>Aciertos: ' + usuarioActual.aciertos + '</p><br>';
        texto += '<p>Intentos: ' + usuarioActual.intentos + '</p><br>';
    }
    datos.innerHTML = texto;
    }

    function desordenarArray(arr) {
        for (let i = arr.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1)); // índice aleatorio entre 0 e i
            [arr[i], arr[j]] = [arr[j], arr[i]]; // intercambia elementos
        }
        return arr;
    }

    function ocultaCarta(elemento){
        var e = getId(elemento);
        if(e.classList.contains("revelada")){
            e.classList.remove("revelada");
        }else{
            return;
        }
    }

    function revelaCarta(elemento){
        var e = getId(elemento);
        if(e.classList.contains("revelada")){
            return;
        }else{
            e.classList.add("revelada");
        }
    }

function iniciaJuego() {
    let numeros = [1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 8, 9, 9, 10, 10];
    numeros = desordenarArray([...numeros]);

    // Reiniciar el estado del juego para un nuevo inicio
    usuarioActual.cartasSeleccionadas = [];
    usuarioActual.jugadaActual = [...numeros];
    usuarioActual.partidas++;
    usuarioActual.aciertos = 0; // Reiniciar aciertos para la nueva partida
    usuarioActual.intentos = 0; // Reiniciar intentos para la nueva partida

    // Limpiar el tablero visualmente antes de actualizarlo con los nuevos números
    // Esto asume que actualizaTablero renderiza los botones si es necesario
    // o al menos limpia su contenido/estado visual.
    actualizaTablero(); 

    // Guardar el estado del usuario
    saveUserData(usuarioActual.usuario, {
        usuario: usuarioActual.usuario,
        visitas: usuarioActual.visitas,
        partidas: usuarioActual.partidas,
        partidaNum: usuarioActual.partidaNum, // Asegúrate de que esto sea consistente
        jugadaActual: usuarioActual.jugadaActual,
        intentos: usuarioActual.intentos,
        aciertos: usuarioActual.aciertos,
        cartasSeleccionadas: usuarioActual.cartasSeleccionadas
    });
    muestraTablero(); // Mostrar el tablero después de inicializarlo
}

function analizaNumero() { // Ahora solo recibe el valor del último clic
    // Esperamos un momento para que el usuario vea la segunda carta
    setTimeout(() => {
        const idCarta1 = usuarioActual.cartasSeleccionadas[0];
        const idCarta2 = usuarioActual.cartasSeleccionadas[1];

        const valorCarta1 = getId(idCarta1).textContent;
        const valorCarta2 = getId(idCarta2).textContent;

        if (valorCarta1 === valorCarta2) {
            usuarioActual.aciertos++;
            // Marcar cartas como emparejadas (e.g., añadir una clase 'emparejada')
            getId(idCarta1).classList.add('emparejada');
            getId(idCarta2).classList.add('emparejada');
            // Deshabilitar clics en estas cartas
            getId(idCarta1).disabled = true;
            getId(idCarta2).disabled = true;

        } else {
            // Si no coinciden, ocultarlas de nuevo
            ocultaCarta(idCarta1);
            ocultaCarta(idCarta2);
        }

        usuarioActual.cartasSeleccionadas = []; // Limpiar selecciones para el siguiente turno
        tablero.style.pointerEvents = 'auto'; // Reactivar clics en el tablero
        actualizaTablero(); // Actualizar el panel de datos
        saveUserData(usuarioActual.usuario, usuarioActual); // Guardar el estado actualizado
        // Verificar si el juego ha terminado (todos los aciertos)
        if (usuarioActual.aciertos * 2 === usuarioActual.jugadaActual.length) { // Por ejemplo, 10 aciertos * 2 = 20 cartas
            finalizaJuego();
        }
    }, 1000); // Esperar 1 segundo para la visualización
}

    function finalizaJuego(){

    }

    function configurarTableroInicialmente() {
    // Solo adjuntar el listener una vez al contenedor del tablero
    if (!tablero.dataset.listenerAttached) {
        tablero.addEventListener('click', (event) => {
            const clickedButton = event.target.closest('.boton');
            // Asegura que el clic sea en un botón con la clase "boton"
            if (clickedButton && !clickedButton.classList.contains('emparejada')) { // Evita clics en cartas ya emparejadas
                console.log("llego");
                console.log(clickedButton);

                // Aquí es crucial la lógica para evitar clics duplicados en la misma carta
                // o clics cuando ya hay dos cartas volteadas esperando la comparación
                if (usuarioActual.cartasSeleccionadas.length < 2 && !usuarioActual.cartasSeleccionadas.includes(clickedButton.id)) {
                    usuarioActual.cartasSeleccionadas.push(clickedButton.id);
                    revelaCarta(clickedButton.id); // Mostrar la carta inmediatamente

                    // Si ya hay dos cartas seleccionadas, procede a analizar
                    if (usuarioActual.cartasSeleccionadas.length === 2) {
                        // Desactivar temporalmente los clics para evitar más selecciones mientras se comparan
                        tablero.style.pointerEvents = 'none'; 
                        usuarioActual.intentos++; // Sumar intento solo cuando se comparan 2 cartas
                        analizaNumero(); // La lógica de comparación va aquí
                        // analizarNumero se encargará de resetear cartasSeleccionadas y reactivar clics
                    }
                }
            }
        });
        tablero.dataset.listenerAttached = true;
    }
}

    function pagina(){
    botonInicia.addEventListener('click', iniciaJuego);
    botonSesion.addEventListener('click', iniciaSesion);
    btnMensaje.addEventListener('click', inicializaTablero); // Este es el listener del botón "Continuar"

    // Asegúrate de que los IDs de los botones sean consistentes (ej: "card-0", "card-1"...)
    // y que tu HTML contenga los 20 botones con la clase "boton".
    configurarTableroInicialmente(); // Llamar una vez para adjuntar el listener de delegación

    const logeado = getCookie(usuarioLog);
    if(logeado){
        const datosUsuario = getUserData(logeado);
        if(datosUsuario){
            usuarioActual = {...datosUsuario};
            // Asegúrate de que jugadaActual no esté vacía si vienes de un usuario logeado
            if (usuarioActual.jugadaActual && usuarioActual.jugadaActual.length === 0) {
                 // Si el usuario vuelve y no tiene una partida activa, puedes iniciar una nueva
                 // o dejar que el botón "Iniciar Juego" lo haga.
                 // Por ahora, solo muestra el mensaje de bienvenida y que el usuario decida iniciar juego.
                 actualizaSitio(); // Muestra el mensaje de bienvenida
            } else {
                actualizaSitio();
                inicializaTablero(); // Muestra el tablero si hay una partida cargada
            }
        }else{
            eraseCookie(logeado);
            muestraLogin();
        }
    }else{
        muestraLogin();
    }
}
pagina();
    
    //eraseCookie(usuarioLog);

});