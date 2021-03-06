<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" href="estilos.css">
  </head>
  <body>

    <?php

        session_start();

        $db = new mysqli("localhost","root","","biblioteca");

        if (!isset($_REQUEST["action"])) {

            if (isset($_SESSION["usuario"])) {

                echo "<button class='botonSesion cerrarSesion' onclick='location.href=\"index.php?action=cerrarSesion\"'>Cerrar sesión</button>";

            }

            else {

                echo "<button class='botonSesion iniciarSesion' onclick='location.href=\"index.php?action=formularioIniciarSesion\"'>Iniciar sesión</button>";

            }

            $consulta = $db->query("SELECT libros.idLibro as idLibro,titulo,genero,pais,anyo,numPaginas
                                    FROM libros
                                    ORDER BY libros.idLibro ASC");

            if ($consulta->num_rows > 0) {

                echo "<table class='tablaResultados' id='tablaResultados'>
                        <tr>
                            <th colspan='9'>
                                LIBROS GUARDADOS
                            </th>
                        </tr>
                        <tr style='height: 10px'></tr>
                        <tr class='filaArriba'>
                            <td>ID</td>
                            <td style='width: 30%'>Título</td>
                            <td>Género</td>
                            <td>País</td>
                            <td>Año</td>
                            <td>Páginas</td>
                            <td style='width: 30%'>Escritor/a (s)</td>
                            <td colspan='2'>Acciones</td>
                        </tr>
                        <tr style='height: 5px'></tr>";

                while ($fila = $consulta->fetch_object()) {

                    echo "<tr><td>" . $fila->idLibro . "</td><td>" . $fila->titulo . "</td><td>" . $fila->genero . 
                        "</td><td>" . $fila->pais . "</td><td>" . $fila->anyo . "</td><td>" . $fila->numPaginas . 
                        "</td><td>";

                    $consulta2 = $db->query("SELECT nombre,apellidos
                                            FROM personas p 
                                            INNER JOIN escriben e
                                                ON p.idPersona = e.idPersona
                                            WHERE e.idLibro = '$fila->idLibro'");

                    while ($fila2 = $consulta2->fetch_object()) {

                        echo $fila2->nombre . " " . $fila2->apellidos . "<br>";

                    }

                    echo    "</td>";

                    if (isset($_SESSION["usuario"])) {

                        echo "<td><a class='eliminar' onclick='borrar($fila->idLibro, \"$fila->titulo\")'>Eliminar</a></td>\n
                        <td><a class='modificar' onclick='modificar($fila->idLibro)'>Modificar</a></td>";

                    }

                    else {

                        echo "<td><a class='eliminar disabled'>Eliminar</a></td>\n
                        <td><a class='modificar disabled'>Modificar</a></td>";

                    }
                            
                    echo "</tr>";

                }

                if (isset($_SESSION["usuario"])) {

                    echo "<tr><td colspan='9'><button class='nuevoLibro' onclick='location.href=\"index.php?action=formularioAltaLibros\"'>+ Añadir libro</button></td></tr>";

                }

                else {

                    echo "<tr><td colspan='9'><button class='nuevoLibro disabled'>+ Añadir libro</button></td></tr>";

                }
                
                echo "<tr><td colspan='9'>
                        <form action = 'index.php' method = 'get'>
                            <input class='inputBuscar' type='text' name='titulo' placeholder='Título...'>
                            <input type='hidden' name='action' value='buscarLibros'>
                            <input class='submitBuscar'type='submit' value='Buscar libro(s)'>  
                        </form>
                    </td></tr>";
                echo "</table>";

            }

            else {

                echo "<table class='tablaResultados' id='tablaResultados'>
                        <tr>
                            <th colspan='9'>
                                LIBROS GUARDADOS
                            </th>
                        </tr>
                        <tr style='height: 10px'></tr>
                        <tr>
                            <td colspan='9'>
                                No existe ningún libro en la base de datos.
                            </td>
                        </tr>";

                if (isset($_SESSION["usuario"])) {

                    echo "<tr><td colspan='9'><button class='nuevoLibro' onclick='location.href=\"index.php?action=formularioAltaLibros\"'>+ Añadir libro</button></td></tr>";

                }

                else {

                    echo "<tr><td colspan='9'><button class='nuevoLibro disabled'>+ Añadir libro</button></td></tr>";

                }

                echo "</table>";

            }

        }

        else {

            switch($_REQUEST["action"]) {


                // ========================================================== INICIAR SESIÓN (USUARIO) ==========================================================


                case "formularioIniciarSesion":

                    if (isset($_SESSION["usuario"])) {

                        header("Location: index.php");

                    }

                    else {

                        echo "<div class='divFondoBorrar'></div>";

                        echo "<div class='formularios'>
                            <h1>Formulario de inicio de sesión</h1>
                            <table>
                                <form action = 'index.php' method = 'post'>
                                    <tr>
                                        <td class='tdTexto'>Usuario:</td>
                                        <td width='60%'><input type='text' name='usuario' required></td>
                                    </tr>
                                    <tr>
                                        <td class='tdTexto'>Contraseña:</td>
                                        <td width='60%'><input type='password' name='contrasenya' required></td>
                                    </tr>
                                    <tr>
                                        <td colspan='2'><input type='submit' value='Iniciar sesión' class='botonSesion iniciarSesion botonFormulario'></td>
                                    </tr>
                                    <tr style='height: 20px'></tr>";

                        if (isset($_REQUEST["error"])) {

                            echo "<tr>
                                <td colspan='2'><span style='color: red'>Los datos introducidos son incorrectos.</span></td>
                            </tr>";

                        }
                    
                        echo "<tr style='height: 10px'></tr>
                                <tr>
                                    <td colspan='2'>¿No tienes una cuenta? <a href='index.php?action=formularioRegistrarse'>¡Regístrate!</a> </td>
                                </tr>
                                <input type='hidden' name='action' value='iniciarSesion'>
                            </form>
                        </table>
                    </div>";

                    }


                break;


                // ========================================================== INICIAR SESIÓN (MÁQUINA) ==========================================================


                case "iniciarSesion":

                    if (isset($_SESSION["usuario"])) {

                        header("Location: index.php");

                    }

                    else {

                        $usuario = $_REQUEST["usuario"];
                        $contrasenya = $_REQUEST["contrasenya"];
    
                        $consulta = $db->query("SELECT id
                                                FROM usuarios
                                                WHERE usuario = '$usuario'
                                                AND BINARY contrasenya = '$contrasenya'");
                        
                        if ($consulta->num_rows > 0) {
    
                            session_start();
                            $_SESSION["usuario"] = $usuario;
                            
                            header('Location: index.php');
    
                        }
    
                        else {
    
                            header('Location: index.php?action=formularioIniciarSesion&error');
    
                        }

                    }

                break;


                // ========================================================== INICIAR SESIÓN (USUARIO) ==========================================================


                case "formularioRegistrarse":

                    if (isset($_SESSION["usuario"])) {

                        header("Location: index.php");

                    }

                    else {

                        echo "<div class='divFondoBorrar'></div>";

                        echo "<div class='formularios' style='width: 400px'>
                            <h1>Formulario de registro</h1>
                            <table>
                                <form action = 'index.php' method = 'post'>
                                    <tr>
                                        <td class='tdTexto'>Usuario:</td>
                                        <td width='60%'><input type='text' name='usuario' required></td>
                                    </tr>
                                    <tr>
                                        <td class='tdTexto'>Contraseña:</td>
                                        <td width='60%'><input type='password' name='contrasenya' required></td>
                                    </tr>
                                    <tr>
                                        <td class='tdTexto'>Repita contraseña:</td>
                                        <td width='60%'><input type='password' name='contrasenya2' required></td>
                                    </tr>
                                    <tr>
                                        <td colspan='2'><input type='submit' value='Registrarse' class='botonSesion iniciarSesion botonFormulario'></td>
                                    </tr>
                                    <tr style='height: 20px'></tr>";

                        if (isset($_REQUEST["error"])) {

                            echo "<tr>
                                <td colspan='2'><span style='color: red'>Los datos introducidos son incorrectos.</span></td>
                            </tr>";

                        }
                    
                        echo "<tr style='height: 10px'></tr>
                                <tr>
                                    <td colspan='2'>¿Ya tienes una cuenta? <a href='index.php?action=formularioIniciarSesion'>¡Inicia sesión!</a> </td>
                                </tr>
                                <input type='hidden' name='action' value='registrarse'>
                            </form>
                        </table>
                    </div>";

                    }


                break;


                // ========================================================== INICIAR SESIÓN (MÁQUINA) ==========================================================


                case "registrarse":

                    if (isset($_SESSION["usuario"])) {

                        header("Location: index.php");

                    }

                    else {

                        $usuario = $_REQUEST["usuario"];
                        $contrasenya = $_REQUEST["contrasenya"];
                        $contrasenya2 = $_REQUEST["contrasenya2"];

                        if ($contrasenya == $contrasenya2) {

                            $consulta = $db->query("INSERT INTO usuarios
                                                    VALUES (DEFAULT, '$usuario', '$contrasenya')");
    
                            if ($db->affected_rows > 0) {

                                session_start();
                                $_SESSION["usuario"] = $usuario;
                                
                                header('Location: index.php');

                            }

                            else {

                                //header('Location: index.php?action=formularioRegistrarse&error');

                            }

                        }
    
                        else {

                            header('Location: index.php?action=formularioRegistrarse&error');

                        }
                        

                    }

                break;


                // ========================================================== CERRAR SESIÓN (MÁQUINA) ==========================================================


                case "cerrarSesion":

                    if (isset($_SESSION["usuario"])) {

                        session_destroy();

                    }

                    header('Location: index.php');

                break;


                // ========================================================== DAR DE ALTA LIBROS (USUARIO) ==========================================================


                case "formularioAltaLibros":

                    if (!isset($_SESSION["usuario"])) {

                        header('Location: index.php');

                    }

                    else {

                        if (isset($_REQUEST["titulo"])) {
                            $titulo = $_REQUEST["titulo"];
                            $genero = $_REQUEST["genero"];
                            $pais = $_REQUEST["pais"];
                            $anyo = $_REQUEST["anyo"];
                            $numPaginas = $_REQUEST["numPaginas"];
                        }
                        else {
                            $titulo = "";
                            $genero = "";
                            $pais = "";
                            $anyo = "";
                            $numPaginas = "";
                        }
                        
                        echo "<div class='menu'>
                                <h1>Formulario de alta de libros</h1>
                                    <table>
                                        <form action = 'index.php' method = 'get' id='form1'>
                                            <tr>
                                                <td class='tdTexto'>Título</td>
                                                <td width='60%'><input type='text' name='titulo' value='$titulo' required></td>
                                            </tr>
                                            <tr>
                                                <td class='tdTexto'>Género</td>
                                                <td><input type='text' name='genero' value='$genero' required></td>
                                            </tr>
                                            <tr>
                                                <td class='tdTexto'>País</td>
                                                <td><input type='text' name='pais' value='$pais' required></td>
                                            </tr>
                                            <tr>
                                                <td class='tdTexto'>Año</td>
                                                <td><input type='text' name='anyo' value='$anyo' required></td>
                                            </tr>
                                            <tr>
                                                <td class='tdTexto'>Número de páginas</td>
                                                <td><input type='text' name='numPaginas' value='$numPaginas' required></td>
                                            </tr>
                                            <tr>
                                                <td class='tdTexto' id='tdEscritor'>Escritor/a</td>
    
    
                                                <td>
                                                    <select name='escritor[]' class='selectAutor' multiple size='4'>";
                                    
                                                        $consulta = $db->query("SELECT * FROM personas");
    
                                                        while ($fila = $consulta->fetch_object()) {
    
                                                            echo "<option value='$fila->idPersona'>$fila->nombre $fila->apellidos</option>";
    
                                                        }
                                                        
    
                        echo "                      </select>
                                                    <button type='button' id='botonNuevoEscritor' onclick='nuevoEscritor()' class='nuevoEscritor'>Nuevo escritor</button>
                                                </td>
                                            </tr>
                                            <input type='hidden' name='action' value='insertarLibro'>
                                        </form>
                                        <tr id='trNuevoEscritor' style='visibility: hidden'>
                                            <td></td>
                                            <td>
                                                <form id='form2' method='get' action='index.php'>
                                                    <input class='inputAutor' name='nombre' required placeholder='Nombre'>
                                                    <input class='inputAutor' name='apellidos' required placeholder='Apellidos'>
                                                    <input type='hidden' name='action' value='insertarAutor'>
                                                    <button type='button' class='nuevoAutor' onclick='document.getElementById(\"form2\").submit()'>+</button>
                                                </form>
                                            </td>
                                        </tr>
                                        <tr style='height: 20px'></tr>
                                        <tr>
                                            <td colspan='2'>
                                                <button class='guardar' onclick='document.getElementById(\"form1\").submit()'>Guardar libro</button>
                                                <button type='button' onclick='volver()' class='cancelar'>Cancelar</button>    
                                            </td>
                                        </tr>
                                    </table>
                            </div>";

                    }

                break;


                // ========================================================== DAR DE ALTA LIBROS (MAQUINA) ==========================================================


                case "insertarLibro":

                    if (!isset($_SESSION["usuario"])) {

                        header('Location: index.php');

                    }

                    else {

                        $titulo = $_REQUEST["titulo"];
                        $genero = $_REQUEST["genero"];
                        $pais = $_REQUEST["pais"];
                        $anyo = $_REQUEST["anyo"];
                        $numPaginas = $_REQUEST["numPaginas"];
                        $idEscritor = $_REQUEST["escritor"];
                        $idLibro;
    
                        $consulta = $db->query("SELECT max(idLibro) + 1 as 'id'
                                                FROM libros");
                        while ($fila = $consulta->fetch_object()) {
                            $idLibro = $fila->id;
                        }
    
                        $consulta1 = $db->query("INSERT INTO libros
                                                VALUES ('$idLibro','$titulo','$genero','$pais','$anyo','$numPaginas')");
                        $filasAfectadasConsulta1 = $db->affected_rows;
    
                        foreach ($idEscritor as $escritor) {
                            $consulta2 = $db->query("INSERT INTO escriben
                                                VALUES ('$idLibro', '$escritor')");
                        }
    
                        
                        $filasAfectadasConsulta2 = $db->affected_rows;
    
                        if ($filasAfectadasConsulta1 > 0 && $filasAfectadasConsulta2) {
    
                            header('Location: index.php');
    
                        }
                        
                        else {
    
                            header('Location: index.php?action=error');
    
                        }

                    }


                break;


                // ========================================================== DAR DE ALTA AUTORES (MAQUINA) ==========================================================

                
                case "insertarAutor":

                    if (!isset($_SESSION["usuario"])) {

                        header('Location: index.php');

                    }

                    else {

                        $nombre = $_REQUEST["nombre"];
                        $apellidos = $_REQUEST["apellidos"];
                        $idLibro = $_REQUEST["idLibro"];
                        $idPersona;
    
                        $consulta = $db->query("SELECT max(idPersona) + 1 as 'id'
                                                FROM personas");
                        while ($fila = $consulta->fetch_object()) {
                            $idPersona = $fila->id;
                        }
    
                        $consulta = $db->query("INSERT INTO personas
                                                VALUES ('$idPersona','$nombre','$apellidos')");
    
                        if ($db->affected_rows > 0) {
    
                            if (isset($_REQUEST["modificando"])) {
    
                                header('Location: index.php?action=formularioModificar&idLibro=' . $idLibro);
    
                            }
                            
                            else {
    
                                header('Location: index.php?action=formularioAltaLibros');
    
                            }
    
                        }

                        else {

                            header('Location: index.php?action=error');
    
                        }

                    }


                break;


                // ========================================================== ELIMINAR LIBROS (MAQUINA) ==========================================================
                    

                case "eliminarLibro":

                    if (!isset($_SESSION["usuario"])) {

                        header('Location: index.php');

                    }

                    else {

                        $idLibro = $_REQUEST["idLibro"];

                        $consulta0 = $db->query("DELETE FROM libros
                                                WHERE idLibro = $idLibro");
                        
                        $consulta1 = $db->query("DELETE FROM escriben
                                                WHERE idLibro = $idLibro");
    
                        header('Location: index.php');

                    }

                break;


                // ========================================================== MODIFICAR LIBROS (USUARIO) ==========================================================


                case "formularioModificar":

                    if (!isset($_SESSION["usuario"])) {

                        header('Location: index.php');

                    }

                    else {

                        $idLibro = $_REQUEST["idLibro"];

                        $consulta0 = $db->query("SELECT titulo, genero, pais, anyo, numPaginas
                                                FROM libros
                                                INNER JOIN escriben
                                                    ON libros.idLibro = escriben.idLibro
                                                WHERE libros.idLibro = $idLibro");
                        $fila = $consulta0->fetch_object();
                            echo "<div class='menu'>
                                <h1>Formulario de modificación</h1>
                                    <table>
                                        <form id='form1' action = 'index.php' method = 'get'>
                                        <tr>
                                            <td class='tdTexto'>Título</td>
                                            <td width='60%'><input type='text' name='titulo' value='$fila->titulo'></td>
                                        </tr>
                                        <tr>
                                            <td class='tdTexto'>Género</td>
                                            <td><input type='text' name='genero' value='$fila->genero'></td>
                                        </tr>
                                        <tr>
                                            <td class='tdTexto'>País</td>
                                            <td><input type='text' name='pais' value='$fila->pais'></td>
                                        </tr>
                                        <tr>
                                            <td class='tdTexto'>Año</td>
                                            <td><input type='text' name='anyo' value='$fila->anyo'></td>
                                        </tr>
                                        <tr>
                                            <td class='tdTexto'>Número de páginas</td>
                                            <td><input type='text' name='numPaginas' value='$fila->numPaginas'></td>
                                        </tr>
                                        <tr>
                                            <td class='tdTexto' id='tdEscritor'>Escritor/a</td>
                                            <td>
                                                    <select name='escritor[]' class='selectAutor' multiple size='4'>";
                                    
                                                        $consulta = $db->query("SELECT * FROM personas");
    
                                                        $consulta2 = $db->query("SELECT idPersona
                                                                                FROM escriben
                                                                                WHERE idLibro = '$idLibro'");
    
                                                        $autoresLibro = array();
    
                                                        while ($fila = $consulta2->fetch_object()) {
    
                                                            $autoresLibro[] = $fila->idPersona;
    
                                                        }
    
                                                        while ($fila = $consulta->fetch_object()) {
    
                                                            if (in_array($fila->idPersona, $autoresLibro)) {
    
                                                                echo "<option selected value='$fila->idPersona'>$fila->nombre $fila->apellidos";
    
                                                            }
    
                                                            else {
    
                                                                echo "<option value='$fila->idPersona'>$fila->nombre $fila->apellidos";
    
                                                            }
    
                                                        }
                                                        
    
                        echo "                      </select>
                                                    <button type='button' id='botonNuevoEscritor' onclick='nuevoEscritor()' class='nuevoEscritor'>Nuevo escritor</button>
                                                </td>
                                            </tr>
                                            <input type='hidden' name='action' value='modificarLibro'>
                                            <input type='hidden' name='idLibro' value='$idLibro'>
                                        </form>
                                        <tr id='trNuevoEscritor' style='visibility: hidden'>
                                            <td></td>
                                            <td>
                                                <form id='form2' method='get' action='index.php'>
                                                    <input class='inputAutor' name='nombre' required placeholder='Nombre'>
                                                    <input class='inputAutor' name='apellidos' required placeholder='Apellidos'>
                                                    <input type='hidden' name='action' value='insertarAutor'>
                                                    <input type='hidden' name='idLibro' value='$idLibro'>
                                                    <input type='hidden' name='modificando' value='si'>
                                                    <button type='button' class='nuevoAutor' onclick='document.getElementById(\"form2\").submit()'>+</button>
                                                </form>
                                            </td>
                                        </tr>
                                        <tr style='height: 20px'></tr>
                                        <tr>
                                            <td colspan='2'>
                                                <button class='guardar' onclick='document.getElementById(\"form1\").submit()'>Guardar libro</button>
                                                <button type='button' onclick='volver()' class='cancelar'>Cancelar</button>    
                                            </td>
                                        </tr>
                                    </table>
                            </div>";

                    }

                    break;


                    // ========================================================== MODIFICAR LIBROS (MÁQUINA) ==========================================================


                    case "modificarLibro":

                        if (!isset($_SESSION["usuario"])) {

                            header('Location: index.php');
    
                        }

                        else {

                            $titulo = $_REQUEST["titulo"];
                            $genero = $_REQUEST["genero"];
                            $pais = $_REQUEST["pais"];
                            $anyo = $_REQUEST["anyo"];
                            $numPaginas = $_REQUEST["numPaginas"];
                            $idEscritor = $_REQUEST["escritor"];
                            $idLibro = $_REQUEST["idLibro"];
                
                            $consulta1 = $db->query("UPDATE libros
                                                    SET 
                                                        titulo = '$titulo',
                                                        genero = '$genero',
                                                        pais = '$pais',
                                                        anyo = '$anyo',
                                                        numPaginas = '$numPaginas'
                                                    WHERE idLibro = $idLibro");
                                                    
                            $consulta2 = $db->query("DELETE FROM escriben
                                                    WHERE idLibro = '$idLibro'");
    
                            foreach ($idEscritor as $escritor) {
                                $consulta3 = $db->query("INSERT INTO escriben
                                                        VALUES ('$idLibro', '$escritor')");
                            }
                
                            header('Location: index.php');

                        }
                        
                    break;


                    // ========================================================== BUSCAR LIBROS (MÁQUINA) ==========================================================


                    case "buscarLibros":

                        if (isset($_SESSION["usuario"])) {

                            echo "<button class='botonSesion cerrarSesion' onclick='location.href=\"index.php?action=cerrarSesion\"'>Cerrar sesión</button>";
            
                        }
            
                        else {
            
                            echo "<button class='botonSesion iniciarSesion' onclick='location.href=\"index.php?action=formularioIniciarSesion\"'>Iniciar sesión</button>";
            
                        }

                        $titulo = $_REQUEST["titulo"];

                        $consulta = $db->query("SELECT libros.idLibro as idLibro,titulo,genero,pais,anyo,numPaginas
                                                FROM libros
                                                WHERE titulo LIKE '%$titulo%'
                                                OR genero LIKE '%$titulo%'
                                                ORDER BY libros.idLibro ASC");
            
                        if ($consulta->num_rows > 0) {

                            echo "<table class='tablaResultados' id='tablaResultados'>
                                    <tr>
                                        <th colspan='9'>
                                            LIBROS GUARDADOS
                                        </th>
                                    </tr>
                                    <tr style='height: 10px'></tr>
                                    <tr class='filaArriba'>
                                        <td>ID</td>
                                        <td style='width: 30%'>Título</td>
                                        <td>Género</td>
                                        <td>País</td>
                                        <td>Año</td>
                                        <td>Páginas</td>
                                        <td style='width: 30%'>Escritor/a (s)</td>
                                        <td colspan='2'>Acciones</td>
                                    </tr>
                                    <tr style='height: 5px'></tr>";

                                    while ($fila = $consulta->fetch_object()) {

                                        echo "<tr><td>" . $fila->idLibro . "</td><td>" . $fila->titulo . "</td><td>" . $fila->genero . 
                                            "</td><td>" . $fila->pais . "</td><td>" . $fila->anyo . "</td><td>" . $fila->numPaginas . 
                                            "</td><td>";
                    
                                        $consulta2 = $db->query("SELECT nombre,apellidos
                                                                FROM personas p 
                                                                INNER JOIN escriben e
                                                                    ON p.idPersona = e.idPersona
                                                                WHERE e.idLibro = '$fila->idLibro'");
                    
                                        while ($fila2 = $consulta2->fetch_object()) {
                    
                                            echo $fila2->nombre . " " . $fila2->apellidos . "<br>";
                    
                                        }
                    
                                        echo    "</td>";
                    
                                        if (isset($_SESSION["usuario"])) {
                    
                                            echo "<td><a class='eliminar' onclick='borrar($fila->idLibro, \"$fila->titulo\")'>Eliminar</a></td>\n
                                            <td><a class='modificar' onclick='modificar($fila->idLibro)'>Modificar</a></td>";
                    
                                        }
                    
                                        else {
                    
                                            echo "<td><a class='eliminar disabled'>Eliminar</a></td>\n
                                            <td><a class='modificar disabled'>Modificar</a></td>";
                    
                                        }
                                                
                                        echo "</tr>";
                    
                                    }

                            if (isset($_SESSION["usuario"])) {

                                echo "<tr><td colspan='9'><button class='nuevoLibro' onclick='location.href=\"index.php?action=formularioAltaLibros\"'>+ Añadir libro</button></td></tr>";
            
                            }
            
                            else {
            
                                echo "<tr><td colspan='9'><button class='nuevoLibro disabled'>+ Añadir libro</button></td></tr>";
            
                            }

                            echo "<tr><td colspan='9'>
                                    <form action = 'index.php' method = 'get'>
                                        <input class='inputBuscar' type='text' name='titulo' placeholder='Título...'>
                                        <input type='hidden' name='action' value='buscarLibros'>
                                        <input class='submitBuscar'type='submit' value='Buscar libro(s)'>  
                                    </form>
                                </td></tr>";
                            echo "</table>";

                        }

                        else {

                            echo "<table class='tablaResultados' id='tablaResultados'>
                                    <tr>
                                        <th colspan='9'>
                                            LIBROS GUARDADOS
                                        </th>
                                    </tr>
                                    <tr style='height: 10px'></tr>
                                    <tr>
                                        <td colspan='9'>
                                            No existen coincidencias en la base de datos.
                                        </td>
                                    </tr>";

                            echo "<tr><td colspan='9'><button class='nuevoLibro' onclick='location.href=\"index.php?action=formularioAltaLibros\"'>+ Añadir libro</button></td></tr>";
                            echo "</table>";

                        }

                break;


                // ========================================================== ERRORES ==========================================================

                
                case "error":
                    echo "<div class='exito'>
                            Ha ocurrido un error. Inténtalo de nuevo más tarde.   
                        </div>";
                break;


                default: echo "Error 404: página no encontrada";
                break;

            } // switch

            
        } // else

    ?>

  </body>
  <script>

      function borrar(idLibro, tituloLibro) {

            var divFondo = document.createElement("div");
            divFondo.setAttribute("class","divFondoBorrar");
            divFondo.setAttribute("id","divFondo")

            var div = document.createElement("div");
            div.setAttribute("class","divBorrar");
            div.setAttribute("id","div");

            var tabla = document.createElement("table");
            tabla.setAttribute("class","tablaBorrar");

            var tr1 = document.createElement("tr");

            var td1 = document.createElement("td");
            td1.setAttribute("colspan","2");

            var span1 = document.createElement("span");
            span1.setAttribute("class","spanBorrarAlerta");
            span1.innerHTML = '¡ATENCIÓN!';
        
            var tr2 = document.createElement("tr");

            var td2 = document.createElement("td");
            td2.setAttribute("colspan","2");

            var span2 = document.createElement("span");
            span2.setAttribute("class","spanBorrarMensaje");
            span2.innerHTML = "¿Estás seguro de que deseas borrar el libro '<strong>" + tituloLibro + "</strong>'?";

            var tr3 = document.createElement("tr");

            var td3 = document.createElement("td");
            
            var button1 = document.createElement("button");
            button1.setAttribute("class","botonBorrar");
            button1.setAttribute("onclick","location.href = 'index.php?action=eliminarLibro&idLibro=" + idLibro + "'");
            button1.innerHTML = "Confirmar";

            var td4 = document.createElement("td");

            var button2 = document.createElement("button");
            button2.setAttribute("class","botonCancelar");
            button2.setAttribute("onclick","cancelar()")
            button2.innerHTML = "Cancelar";

            document.body.appendChild(divFondo);
            document.body.appendChild(div);
            div.appendChild(tabla);
            tabla.appendChild(tr1);
            tr1.appendChild(td1);
            td1.appendChild(span1);
            tabla.appendChild(tr2);
            tr2.appendChild(td2);
            td2.appendChild(span2);
            tabla.appendChild(tr3);
            tr3.appendChild(td3);
            td3.appendChild(button1);
            tr3.appendChild(td4);
            td4.appendChild(button2);
            
        }



        function cancelar() {

            document.getElementById("divFondo").remove();
            document.getElementById("div").remove();

        }



        function modificar(idLibro) {

            location.href = "index.php?action=formularioModificar&idLibro=" + idLibro;

        }



        function volver() {

            location.href = "index.php";

        }


        
        function nuevoEscritor() {

            var tr = document.getElementById("trNuevoEscritor");
            var boton = document.getElementById("botonNuevoEscritor");

            tr.style.visibility = "visible";
            boton.removeAttribute("onclick");
            boton.style.cursor = "not-allowed";
            boton.style.backgroundColor = "grey";

        }

</script>   
</html>