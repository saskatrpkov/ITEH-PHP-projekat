<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css" rel=" stylesheet">

    <title>Jela</title>
</head>

<body>
    <?php
        include "header.php";
    ?>

    <div class="container mt-2">
        <h1 class="text-center">
            Jela u restoranu
        </h1>
        <div class=" mt-2">
            <input placeholder="Pretrazi..." type="text" id="pretraga" class="form-control">
        </div>
        <div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Naziv</th>
                        <th>Posno</th>
                        <th>Vreme pripreme</th>
                        <th>Broj sastojaka</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody id="tabela">

                </tbody>
            </table>
        </div>
        <h2 class="text-center" id='naslovForme'>
            Kreiraj jelo
        </h2>
        <div class="row">
            <div class="col-4">
                <form class="mt-2" id='forma'>

                    <div class="form-group">
                        <label for="naziv">Naziv</label>
                        <input required type="text" class="form-control" id="naziv" placeholder="Naziv">
                    </div>
                    <div class="form-group">
                        <label for="posno">Posno</label>
                        <select required class="form-control" id="posno">
                            <option value="1">Da</option>
                            <option value="0">Ne</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="vremePripreme">Vreme pripreme</label>
                        <input required type="text" class="form-control" id="vremePripreme"
                            placeholder="Vreme pripreme">
                    </div>
                    <div class="form-group">
                        <label for="opis">Opis</label>
                        <textarea required class="form-control" id="opis" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary form-control">Sacuvaj</button>
                </form>
                <button id='vratiSe' hidden class=" mt-2 btn btn-secondary form-control">Vrati se</button>
            </div>

            <div class="col-4">
                <h4>Sastojci</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Naziv</th>
                            <th>Kolicina</th>
                            <th>Obrisi</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaSastojaka">

                    </tbody>
                </table>
            </div>
            <div class="col-4">
                <h3>Dodaj sastojak</h3>
                <form id='formaSastojak'>
                    <div class="form-group">
                        <label for="kolicina">Kolicina</label>
                        <input required type="number" class="form-control" id="kolicina" placeholder="Kolicina">
                    </div>
                    <div class="form-group">
                        <label for="namirnica">Namirnica</label>
                        <select class="form-control" id="namirnica"></select>
                    </div>
                    <button type='submit' class=" mt-2 btn btn-secondary form-control">Dodaj</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="./main.js"></script>
    <script>
        let jela = [];
        let selektovaniId = undefined;
        let pretraga = '';
        let namirnice = [];
        let sastojci = [];

        $(function () {
            ucitajJela();
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
                const posno = $('#posno').val();
                const vremePripreme = $('#vremePripreme').val();
                const opis = $('#opis').val();

                upisi("./handler/jelo.php", {
                    metoda: selektovaniId ? 'izmeni' : 'kreiraj',
                    jelo: JSON.stringify({
                        id: selektovaniId,
                        naziv,
                        posno,
                        vreme_pripreme: vremePripreme,
                        opis,
                        sastojci
                    })
                }).then(() => {
                    ucitajJela();
                    setSastojci([]);
                    setSelektovaniId(undefined);

                });

            })
            $('#formaSastojak').submit((e) => {
                e.preventDefault();
                const kolicina = $('#kolicina').val();
                const namirnicaId = $('#namirnica').val();
                if (!kolicina || !namirnicaId) {
                    return;
                }
                if (!selektovaniId) {
                    setSastojci([...sastojci, {
                        namirnica_id: namirnicaId,
                        kolicina
                    }])
                    return;
                }
                const postojeci = sastojci.find(element => element.jelo_id == selektovaniId && element.namirnica_id == namirnicaId);
                if (!postojeci) {
                    setSastojci([...sastojci, {
                        jelo_id: selektovaniId,
                        namirnica_id: namirnicaId,
                        kolicina
                    }])
                    return;
                } else {
                    postojeci.obrisan = false;
                    postojeci.kolicina = kolicina;
                    setSastojci(sastojci)
                }
            })
        })
        function setJela(val) {
            jela = val;
            popuniTabelu(jela);
        }
        function setPretraga(val) {
            pretraga = val;

            popuniTabelu(jela.filter(element => {
                return element.naziv.toLowerCase().includes(pretraga.toLowerCase());
            }))
        }
        function popuniTabelu(j) {
            $('#tabela').html('');
            for (let jelo of j) {
                $('#tabela').append(`
                    <tr>
                        <td>${jelo.id}</td>
                        <td>${jelo.naziv}</td>
                        <td>${jelo.posno ? 'Da' : 'Ne'}</td>
                        <td>${jelo.vreme_pripreme}</td>
                        <td>${jelo.broj_sastojaka}</td>
                        <td>
                            <button onClick="setSelektovaniId(${jelo.id})" class='btn btn-secondary'>Vidi</button>    
                            <button class='btn btn-danger' onClick="obrisi(${jelo.id})">Obrisi</button>    
                        </td>
                    </tr>
                `)
            }
        }
        function setSelektovaniId(val) {
            selektovaniId = val;
            if (!selektovaniId) {
                $('#naslovForme').html('Kreiraj jelo')
                $('#vratiSe').attr('hidden', true);
                $('#naziv').val('')
                $('#opis').val('')
                $('#posno').val('1')
                $('#vremePripreme').val('')
                setSastojci([]);
            }
            else {
                ucitaj('./handler/jelo.php?metoda=sastojci&id=' + selektovaniId).then(setSastojci);
                const sel = jela.find(e => e.id == selektovaniId);
                $('#naslovForme').html('Izmeni jelo');
                $('#vratiSe').attr('hidden', false);
                $('#naziv').val(sel.naziv)
                $('#opis').val(sel.opis)
                $('#posno').val(sel.posno)
                $('#vremePripreme').val(sel.vreme_pripreme)
            }
        }
        function ucitajJela() {
            ucitaj('./handler/jelo.php?metoda=sve').then(setJela);
        }
        function ucitajNamirnice() {
            ucitaj('./handler/namirnica.php?metoda=sve').then(setNamirnice);
        }
        function setNamirnice(val) {
            namirnice = val;
            popuniMeni(namirnice);
        }
        function setSastojci(val) {
            sastojci = val || [];
            popuniMeni(namirnice);
            popuniTabeluSastojaka(sastojci);
        }

        function popuniTabeluSastojaka(val) {
            $('#tabelaSastojaka').html('');
            let index = 0;
            for (let sastojak of val) {
                if (sastojak.obrisan) {
                    continue;
                }
                $('#tabelaSastojaka').append(`
                    <tr>
                        <td>${sastojak.id || '/'}</td>
                        <td>${namirnice.find(e => e.id == sastojak.namirnica_id).naziv}</td>
                        <td>${sastojak.kolicina}</td>
                        <td>
                              <button class='btn btn-danger' onClick="izbaciSastojak(${sastojak.namirnica_id})">Izbaci</button>    
                        </td>
                    </tr>
                `)
                index++;
            }
        }

        function popuniMeni(val) {

            $('#namirnica').html('');
            const filtrirani = val.filter(element => {
                return sastojci.find(s => !s.obrisan && Number(s.namirnica_id) === Number(element.id)) === undefined;
            });
            for (let namirnica of filtrirani) {
                $('#namirnica').append(`
                    <option value='${namirnica.id}'>${namirnica.naziv}</option>
                `)
            }
        }
        function izbaciSastojak(namirnicaId) {
            const sastojak = sastojci.find(element => element.namirnica_id == namirnicaId);
            if (!sastojak.id) {
                setSastojci(sastojci.filter((element) => element.namirnica_id != namirnicaId));
            } else {
                sastojak.obrisan = true;
                setSastojci(sastojci);
            }
        }

        function obrisi(id) {
            upisi("./handler/jelo.php", {
                metoda: 'obrisi',
                id
            }).then(ucitajJela);
        }
    </script>
</body>

</html>