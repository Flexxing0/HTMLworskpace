const turnos = ["x", "o"];
var turnoNum;
var botones = document.getElementsByClassName("btn");
var termina = false;
const filas = 3;
const columnas = 3;
var matriz = [];

function inicializaMatriz(){ //inicializa la matriz en 0
for (i = 0; i < filas; i++) {
    matriz[i] = [];
    for (j = 0; j < columnas; j++) {
        matriz[i][j] = 0; // valor inicial
    }
}
}

function cargaMatriz(indice) { //carga la matriz a medida que se aprietan botones, el indice se obtiene del value del boton para asignar en la misma posicion que el boton
    indice++
    cuenta = 0;
    for (i = 0; i < filas; i++) {
        for (j = 0; j < columnas; j++) {
            cuenta++
            if(cuenta===indice){
            matriz[i][j] = turnos[turnoNum];
            console.log(matriz[i][j])
            console.log(matriz) // valor inicial
            }
        }
    }
}

function recorreColumnas() {
  for (let i = 0; i < columnas; i++) {
    let contador = 0;
    for (let j = 0; j < filas; j++) {
      if (matriz[j][i] === turnos[turnoNum]) {
        contador++;
      } else {
        contador = 0; // reinicia si se rompe la secuencia
      }
      if (contador === 3) {
        return true;
      }
    }
  }

  return false; // si no se encontraron 3 seguidos en ninguna columna
}

function recorreFila() {
  for (let i = 0; i < filas; i++) {
    let contador = 0;

    for (let j = 0; j < columnas; j++) {
      if (matriz[i][j] === turnos[turnoNum]) {
        contador++;
      } else {
        contador = 0; // reinicia si se rompe la secuencia
      }

      if (contador === 3) {
        return true;
      }
    }
  }
  return false; // no encontró ninguna fila con 3 iguales seguidos
}

function recorreDiagonal() {
  const jugador = turnos[turnoNum];

  // Diagonal principal ↘
  const diagonalPrincipal =
    matriz[0][0] === jugador &&
    matriz[1][1] === jugador &&
    matriz[2][2] === jugador;

  // Diagonal secundaria ↙
  const diagonalSecundaria =
    matriz[0][2] === jugador &&
    matriz[1][1] === jugador &&
    matriz[2][0] === jugador;

    console.log(diagonalPrincipal);
    console.log(diagonalSecundaria);
  return diagonalPrincipal || diagonalSecundaria;
}

function verificaGanador() {//
    if(recorreColumnas() || recorreDiagonal() || recorreFila()){
        generaVentanaGanador();
    }
}

function cargaIDs() {//se utiliza al apretar iniciarjuego, vacia los cuadros y restaura los id de los botones para identificarlos
    for (i = 0; i < botones.length; i++) {
        botones[i].setAttribute("id", "boton" + i);
        botones[i].innerHTML = "";
    }
}

function creaTextoGdor(){//crea texto del jugador que gana
    var parrafoAnt = document.getElementById("ganador");
    if(parrafoAnt){
        padre = parrafoAnt.parentNode;
        padre.removeChild(parrafoAnt);
    }
    var parrafo = document.createElement("p");
    parrafo.setAttribute("id","ganador");
    parrafo.innerHTML = "Felicidades al jugador " + turnos[turnoNum] ;
    return parrafo;
}

function generaVentanaGanador(){//agrega la ventana al contenedor
    var contenedor = document.getElementById("seccion-ganador");
    contenedor.appendChild(creaTextoGdor());
}

function iniciarJuego() {//este es el boton iniciar juego
    inicializaMatriz();//inicializa la matriz en 0
    cargaIDs();//carga los id para identificar los botones
    turnoAleatorio();//permite que comienze cualquier jugador
    for (i = botones.length - 1; i >= 0; i--) { //agrega el onClick a cada boton
        botones[i].setAttribute("onClick", "comienzaTurno(this.id);")
    }
}

function comienzaTurno(valor) {//al apretar el boton, se ejecuta esta funcion, "valor" es el "id" del boton
    var botonApretado = document.getElementById(valor);
    var posicion;//posicion guardara el value del boton, si es 0, en la matriz se ve que se apreto y guardo en la posicion 0,0
    if (botonApretado) {
        botonApretado.innerHTML = turnos[turnoNum];
        posicion = botonApretado.getAttribute("value");
        botonApretado.removeAttribute("id");//elimino el id para que no se vuelva a apretar
    }
    cargaMatriz(parseInt(posicion));//carga la matriz con el jugador que apreto el boton, se guarda "X" o "O"
    verificaGanador();//verifica si el ganador que es su turno gana
    siguienteTurno();//pasa al siguiente turno
}

function siguienteTurno() {//simple, pasa al siguiente turno, 1 o 0, ya que los jugadores estan en un array
    switch (turnoNum) {
        case 0: turnoNum = 1;
            break;
        case 1: turnoNum = 0;
            break;
        default:
            break;
    }
}

function generaParrafo(turnoJugador) {//genera el parrafo que muestra el turno del jugador actual
    var parrafo = document.getElementById("turno-jugador");
    parrafo.innerHTML = "Turno del Jugador " + turnos[turnoJugador].toUpperCase();
}

function turnoAleatorio() {//permite que comienze cualquier jugador
    turnoNum = Math.floor(Math.random() * turnos.length);
    generaParrafo(turnoNum);
}