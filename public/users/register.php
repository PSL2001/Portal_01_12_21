<?php
session_start();
require dirname(__DIR__, 2) . "/vendor/autoload.php";

use Portal\Users;

$perfil = -1;
if (isset($_SESSION['user'])) {
    $perfil = (new Users)->setUsername($_SESSION['user'])->getPerfil();
}
if ($perfil == 0) {
    header("Location: index.php");
    die();
}
$error = false;

function estaVacio($c, $v, $l) {
    global $error;
    if (strlen($v) < $l) {
        $error = true;
        $_SESSION['err_'.$c] = "El campo $c no puede tener menos de $l caracter/es";
    }
}

function esImagen($tipo) {
    $tiposBuenos=[
        'image/jpeg',
        'image/bmp',
        'image/png',
        'image/webp',
        'image/gif',
        'image/svg-xml',
        'image/x-icon'
    ];
    return in_array($tipo, $tiposBuenos);
}

if (isset($_POST['btnCrear'])) {
    # Procesamos el form
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (estaVacio("username", $username, 7)) {
        $error = true;
    } else {
        # Sabemos que el campo username no esta vacio, comprobamos si el usuario ya existe
        if ((new Users)->existeCampo("username", $username)) {
            # Si esto es verdadero, entonces el campo existe y por lo tanto no lo podemos añadir
            $_SESSION['err_username'] = "Este usuario ya existe en la base de datos";
            $error = true;
        }
    }

    if (estaVacio("email", $email, 0)) {
        $error = true;
    } else {
        # Sabemos que el campo email no esta vacio, comprobamos si el email ya existe
        if ((new Users)->existeCampo("email", $email)) {
            # Si es verdadero, el email existe y no lo podemos añadir
            $_SESSION['err_email'] = "Este correo ya existe en la base de datos";
        }
    }

    if (estaVacio("password", $password, 1)) {
        $error = true;
    }

    $usuario = new Users;

    //Comprobamos la imagen
    //1. Comprobamos que se ha subido un fichero
    if (is_uploaded_file($_FILES['img']['tmp_name'])) {
        # He subido un archivo, ahora hay que comprobar que dicho archivo es una imagen
        if (esImagen($_FILES['img']['type'])) {
            # He subido una imagen
        } else {
            # No he subido una imagen
            $error = true;
            $_SESSION['err_img'] = "El fichero debe de ser una imagen";
        }
    } else {
        # No he subido una imagen
    }

} else {
    # Mostramos el form
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- BootStrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- FONTAWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>Registrar Usuario</title>





</head>

<body style="background-color:silver">
    <h5 class="text-center mt-4">Registrar Usuario</h5>
    <div class="container mt-2">
        <div class="bg-success p-4 text-white rounded shadow-lg m-auto" style="width:35rem">
            <form name="cautor" action="<?php echo $_SERVER['PHP_SELF'] ?>" method='POST' enctype="multipart/form-data">

                <div class="mb-3">
                    <label for="n" class="form-label">Nombre Usuario</label>
                    <input type="text" class="form-control" id="n" placeholder="Username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="a" class="form-label">Email</label>
                    <input type="email" class="form-control" id="a" placeholder="Correo" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="p" class="form-label">Password</label>
                    <input type="password" class="form-control" id="p" placeholder="Contraseña" name="password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Imagen de Perfil</label>
                    <input class="form-control" type="file" name="img">
                </div>
                <?php
                if ($perfil == 1) {
                    echo <<< TXT
                    <div class="mb-3">
                    <label for="p" class="form-label">Perfil</label>
                    <select class="form-control" name="perfil">
                        <option value='1'>Admistrador</option>
                        <option value='0' selected>Usuario</option>
                    </select>
                    </div>
                    TXT;
                }
                ?>
                <div>
                    <button type='submit' name="btnCrear" class="btn btn-info"><i class="fas fa-save"></i> Registrar</button>
                    <button type="reset" class="btn btn-warning"><i class="fas fa-broom"></i> Limpiar</button>
                </div>

            </form>
        </div>

    </div>
</body>

</html>
<?php
}
?>