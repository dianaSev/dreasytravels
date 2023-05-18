<?php include("../template/cabecera.php "); ?>

<?php
$txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";
$txtNombre=(isset($_POST['txtNombre']))?$_POST['txtNombre']:"";
$txtImagen=(isset($_FILES['txtImagen']['name']))?$_FILES['txtImagen']['name']:"";
$accion=(isset($_POST['accion']))?$_POST['accion']:"";

include("../configuracion/bd.php ");

switch($accion){
    case "Agregar":
        //INSERT INTO `lugares` (`id`, `nombre`, `imagen`) VALUES (NULL, 'Cuetzalan del Progreso, Puebla', 'imagen.jpg');
        $sentenciasSQL= $conexion->prepare("INSERT INTO lugares (nombre, imagen) VALUES (:nombre, :imagen);");
        $sentenciasSQL ->bindParam(':nombre',$txtNombre);
        
        $fecha=new DateTime();
        $nombreArchivo=($txtImagen!="")?$fecha->getTimestamp()."_".$_FILES["txtImagen"]["name"]:"imagen.jpg";
        
        $tmpImagen=$_FILES["txtImagen"]["tmp_name"];

        if($tmpImagen!=""){
                move_uploaded_file($tmpImagen,"../../img/".$nombreArchivo);
        }


        $sentenciasSQL ->bindParam(':imagen',$nombreArchivo);
        $sentenciasSQL->execute();
        header("Location:lugares.php");
        break;
    case "Modificar":
        $sentenciasSQL= $conexion->prepare("UPDATE lugares SET nombre=:nombre WHERE id=:id"); 
        $sentenciasSQL->bindParam(':nombre',$txtNombre);
        $sentenciasSQL->bindParam(':id',$txtID);
        $sentenciasSQL->execute();

        if($txtImagen!=""){

            $fecha=new DateTime();
            $nombreArchivo=($txtImagen!="")?$fecha->getTimestamp()."_".$_FILES["txtImagen"]["name"]:"imagen.jpg";
            $tmpImagen=$_FILES["txtImagen"]["tmp_name"];

            move_uploaded_file($tmpImagen,"../../img/".$nombreArchivo);

            $sentenciasSQL= $conexion->prepare("SELECT imagen FROM lugares WHERE id=:id"); 
            $sentenciasSQL->bindParam(':id',$txtID);
            $sentenciasSQL->execute();
            $lugar=$sentenciasSQL->fetch(PDO::FETCH_LAZY);

        if(isset($lugar["imagen"]) &&($lugar["imagen"]!="imagen.jpg")){
            if(file_exists("../../img/".$lugar["imagen"])){
                unlink("../../img/".$lugar["imagen"]);
            }

        }
            

            $sentenciasSQL=$conexion->prepare("UPDATE lugares SET imagen=:imagen WHERE id=:id"); 
            $sentenciasSQL->bindParam(':imagen',$nombreArchivo);
            $sentenciasSQL->bindParam(':id',$txtID);
            $sentenciasSQL->execute();
        }
        header("Location:lugares.php");
        break;

    case "Cancelar":
        header("Location:lugares.php");
    
        break;
        
    case "Seleccionar":
        $sentenciasSQL= $conexion->prepare("SELECT * FROM lugares WHERE id=:id"); 
        $sentenciasSQL->bindParam(':id',$txtID);
        $sentenciasSQL->execute();
        $lugar=$sentenciasSQL->fetch(PDO::FETCH_LAZY);

        $txtNombre=$lugar['nombre'];
        $txtImagen=$lugar['imagen'];
        break;

    case "Borrar":
        $sentenciasSQL= $conexion->prepare("SELECT imagen FROM lugares WHERE id=:id"); 
        $sentenciasSQL->bindParam(':id',$txtID);
        $sentenciasSQL->execute();
        $lugar=$sentenciasSQL->fetch(PDO::FETCH_LAZY);

        if(isset($lugar["imagen"]) &&($lugar["imagen"]!="imagen.jpg")){
            if(file_exists("../../img/".$lugar["imagen"])){
                unlink("../../img/".$lugar["imagen"]);
            }

        }

            $sentenciasSQL= $conexion->prepare("DELETE FROM lugares WHERE id=:id");
            $sentenciasSQL->bindParam(':id',$txtID);
            $sentenciasSQL->execute();
            header("Location:lugares.php");
            
        break;
}

    $sentenciasSQL= $conexion->prepare("SELECT * FROM lugares"); 
    $sentenciasSQL->execute();
    $listaLugares=$sentenciasSQL->fetchAll(PDO::FETCH_ASSOC);



?>

<div class="col-md-5">
    <div class="card">
        <div class="card-header">
            Datos del lugar
        </div>

        <div class="card-body">
        <form method="POST" enctype="multipart/form-data">

            <div class = "form-group">
            <label for="txtID">ID:</label>
            <input type="txt" required readonly class="form-control" value="<?php echo $txtID; ?>" name="txtID" id="txtID" placeholder="ID">
            </div>

            <div class="form-group">
            <label for="txtNombre">Nombre del lugar: </label>
            <input type="text" required class="form-control" value="<?php echo $txtNombre; ?>"  name="txtNombre" id="txtNombre" placeholder="Nombre del lugar">
            </div>

            <div class="form-group">
            <label for="txtNombre">Imagen:</label>

            <br/>
            <?php if($txtImagen!=""){ ?>

                <img class="img-thumbnail rounded"  src="../../img/<?php echo $txtImagen;?>" width="100" alt="" srcset="">
           
           <?php } ?>



            <input type="file" class="form-control" name="txtImagen" id="txtImagen" placeholder="Nombre del lugar">
            </div>

                <div class="btn-group" role="group" aria-label="">
                    <button type="submit" name="accion" <?php echo($accion=="Seleccionar")?"disabled":""; ?> value="Agregar" class="btn btn-success">Agregar</button>
                    <button type="submit" name="accion" <?php echo($accion!="Seleccionar")?"disabled":""; ?> value="Modificar" class="btn btn-warning">Modificar</button>
                    <button type="submit" name="accion" <?php echo($accion!="Seleccionar")?"disabled":""; ?> value="Cancelar" class="btn btn-info">Cancelar</button>
                </div>
        </form>
        
        </div>

       
    </div>
 
        
</div>
<div class="col-md-7">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID </th>
                <th>Nombre del lugar</th>
                <th>Imagenes</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($listaLugares as $lugar) {?>
                <tr>
                    <td><?php echo $lugar ['id']; ?></td>
                    <td><?php echo $lugar ['nombre']; ?></td>
                    <td>
                        <img class="img-thumbnail rounded" src="../../img/<?php echo $lugar ['imagen']; ?>" width="100" alt="" srcset="">
                        
                    
                    
                    </td>

                    <td>
                        
                    <form  method="post">
                        <input type="hidden" name="txtID" id="txtID" value="<?php echo $lugar['id']; ?>"/>
                        <input type="submit" name="accion" value="Seleccionar" class="btn btn-primary"/>
                        <input type="submit" name="accion" value="Borrar" class="btn btn-danger"/>
                    </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
</div>


<?php include("../template/pie.php "); ?>