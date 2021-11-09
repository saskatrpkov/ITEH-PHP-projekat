<?php
include "../broker.php";
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
            exit;
        }
        $jelo=json_decode($_POST['jelo'],true);
        if($metoda=='kreiraj'){
            $broker->izmeni("insert into jelo(naziv,opis,posno,vreme_pripreme) values('".$jelo['naziv']."','".$jelo['opis']."',".$jelo['posno'].",".$jelo['vreme_pripreme'].")");
            $id= $broker->getLastId();
            foreach($jelo['sastojci'] as $sastojak){
                kreirajSastojak($id,$sastojak,$broker);
            }
            echo json_encode([
                "status"=>true
            ]);
        }
        if($metoda=='izmeni'){
            $id=$jelo["id"];
            $broker->izmeni("update jelo set naziv='".$jelo['naziv']."', opis='".$jelo['opis']."', posno=".$jelo['posno'].", vreme_pripreme=".$jelo['vreme_pripreme']." where id=".$id);
            foreach($jelo['sastojci'] as $sastojak){
                if(isset($sastojak['obrisan']) && $sastojak['obrisan']){
                    obrisiSastojak($sastojak,$broker);
                }else{
                    if(!isset($sastojak['id']))
                        kreirajSastojak($id,$sastojak,$broker);
                }
            
            }
         
            echo json_encode([
                "status"=>true
            ]);
        }
    }
    
    function kreirajSastojak($jeloId,$sastojak,$broker){
        $broker->izmeni("insert into sastojak(jelo_id,namirnica_id,kolicina) values(".$jeloId.",".$sastojak['namirnica_id'].",".$sastojak['kolicina'].")" );
    }
    function obrisiSastojak($sastojak,$broker)
    {
        $broker->izmeni("delete from sastojak where id=".$sastojak['id']." and jelo_id=".$sastojak['jelo_id']);
    }
?>