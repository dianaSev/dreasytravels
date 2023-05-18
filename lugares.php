<?php include("template/cabecera.php"); ?>

<center><h1 class="display-4">Dreams&Travel</h1></center>
<p class="lead">Selecciona el destino de tu interes</p>
<?php 

include ("administrador/configuracion/bd.php");
$sentenciasSQL= $conexion->prepare("SELECT * FROM lugares"); 
$sentenciasSQL->execute();
$listaLugares=$sentenciasSQL->fetchAll(PDO::FETCH_ASSOC);

?>

<?php foreach($listaLugares as $lugar){ ?>
<div class="col-md-3">
    <div class="card">
    <img class="card-img-top" src="./img/<?php echo $lugar['imagen'];?>" alt="">
    <div class="card-body">
        <h4 class="card-title"><?php echo $lugar['nombre']; ?></h4>

        <a name="" id="" class="btn btn-primary" href="https://www.gob.mx/sectur/articulos/pueblos-magicos-206528" role="button"> ver mas </a>
    </div>
</div>
</div>
<?php } ?>




<?php include("template/pie.php"); ?>