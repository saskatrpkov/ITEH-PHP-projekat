<?php
include "../broker.php";
$broker=Broker::getBroker();
$broker=Broker::getBroker();

    if(isset($_GET["metoda"])){
        $metoda=$_GET["metoda"];
        if($metoda=='sve'){
            echo json_encode( $broker->vratiKolekciju("select * from namirnica"));
        }
    }
    if(isset($_POST["metoda"])){
        $metoda=$_POST["metoda"];
        if($metoda=='obrisi'){
            $id=$_POST["id"];
            echo json_encode( $broker->izmeni("delete from namirnica where id=".$id));
        }
        if($metoda==='kreiraj'){
           
        }
        if($metoda=='izmeni'){
           
        }
    }

?>