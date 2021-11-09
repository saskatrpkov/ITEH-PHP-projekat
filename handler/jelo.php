<?php
include "../broker.php";
$broker=Broker::getBroker();
$broker=Broker::getBroker();

    if(isset($_GET["metoda"])){
        $metoda=$_GET["metoda"];
        if($metoda=='sve'){
            echo json_encode( $broker->vratiKolekciju("select j.*, COUNT(s.id) as 'broj_sastojaka' from jelo j left join sastojak s on(j.id=s.jelo_id) group by j.id"));
        }
        if($metoda=='sastojci'){
            $id=$_GET["id"];
            echo json_encode( $broker->vratiKolekciju("select * from sastojak where jelo_id=".$id));
        }
    }
    if(isset($_POST["metoda"])){
        $metoda=$_POST["metoda"];
        if($metoda=='obrisi'){
            $id=$_POST["id"];
            echo json_encode( $broker->izmeni("delete from jelo where id=".$id));
        }
        
    }

?>