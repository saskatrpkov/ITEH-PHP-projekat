

function ucitaj(putanja) {
    console.log(putanja);
    return $.getJSON(putanja).then(val => {
        if (!val.status) {
            return Promise.reject(val.greska);
        }
        return Promise.resolve(val.podaci);
    });
}

function upisi(putanja, telo) {
    return $.post(putanja, telo).then(val => {
        val = JSON.parse(val);
        console.log(val);
        if (!val.status) {
            return Promise.reject(val.greska);
        }
        return Promise.resolve();
    })
}