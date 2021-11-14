<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css" rel=" stylesheet">

    <title>Namirnice</title>
</head>

<body>
    <?php
        include "header.php";
    ?>

    <div class="container mt-2">
        <h1 class="text-center">
            Namirnice
        </h1>
        <div class=" mt-2">
            <input placeholder="Pretrazi..." type="text" id="pretraga" class="form-control">
        </div>
        <div class="row">
            <div class="col-5">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Naziv</th>
                            <th>Vidi</th>
                        </tr>
                    </thead>
                    <tbody id="tabela">

                    </tbody>
                </table>
            </div>
            <div class="col-1">

            </div>
            <div class="col-6">
                <form class="mt-2" id='forma'>
                    <h3 id='naslovForme'>
                        Kreiraj namirnicu
                    </h3>
                    <div class="form-group">
                        <label for="naziv">Naziv</label>
                        <input required type="text" class="form-control" id="naziv" placeholder="Naziv">
                    </div>
                    <div class="form-group">
                        <label for="opis">Opis</label>
                        <textarea required class="form-control" id="opis" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary form-control">Sacuvaj</button>
                </form>
                <button id='vratiSe' hidden class=" mt-2 btn btn-secondary form-control">Vrati se</button>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="main.js"></script>
    <script>
        let namirnice = [];
        let pretraga = '';
        let selektovaniId = undefined;
        $(function () {
            ucitajNamirnice();
            $('#pretraga').change(() => {
                setPretraga($('#pretraga').val());
            });
            $('#vratiSe').click(() => {
                setSelektovaniId(undefined);
            })
            $('#forma').submit(e => {
                e.preventDefault();
                const naziv = $('#naziv').val();
                const opis = $('#opis').val();
                upisi('./handler/namirnica.php', {
                    id: selektovaniId,
                    naziv,
                    opis,
                    metoda: selektovaniId ? 'izmeni' : 'kreiraj'
                }).then(() => {
                    setSelektovaniId(undefined);
                    ucitajNamirnice();
                }).catch(err => {
                    alert("desila se greska");
                })
            })
        })
        function setSelektovaniId(val) {
            selektovaniId = val;
            if (!selektovaniId) {
                $('#naslovForme').html('Kreiraj namirnicu')
                $('#vratiSe').attr('hidden', true);
                $('#naziv').val('')
                $('#opis').val('')

            }
            else {
                const sel = namirnice.find(e => e.id == selektovaniId);
                $('#naslovForme').html('Izmeni namirnicu');
                $('#vratiSe').attr('hidden', false);
                $('#naziv').val(sel.naziv)
                $('#opis').val(sel.opis)
            }
        }
        function setPretraga(val) {
            pretraga = val;

            popuniTabelu(namirnice.filter(element => {
                return element.naziv.toLowerCase().includes(pretraga.toLowerCase());
            }))
        }
        function ucitajNamirnice() {
            ucitaj('./handler/namirnica.php?metoda=sve').then(setNamirnice);
        }
        function setNamirnice(val) {
            namirnice = val;
            popuniTabelu(namirnice);
        }
        function popuniTabelu(nam) {
            $('#tabela').html('');
            for (let namirnica of nam) {
                $('#tabela').append(`
                    <tr>
                        <td>${namirnica.id}</td>
                        <td>${namirnica.naziv}</td>
                        <td>
                            <button onClick="setSelektovaniId(${namirnica.id})" class='btn btn-secondary'>Vidi</button>    
                            <button class='btn btn-danger' onClick="obrisi(${namirnica.id})">Obrisi</button>    
                        </td>
                    </tr>
                `)
            }
        }
        function obrisi(id) {
            upisi("./handler/namirnica.php", {
                metoda: 'obrisi',
                id
            }).then(ucitajNamirnice).catch(err => {
                alert('nije moguce brisanje namirnice');
            })
        }
    </script>
</body>

</html>