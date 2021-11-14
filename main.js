

function ucitaj(putanja) {
    return $.getJSON(putanja).then(val => {
        if (!val.status) {
            return Promise.reject(val.error);
        }
        return Promise.resolve(val.podaci);
    });
}

function upisi(putanja, telo) {
    return $.post(putanja, telo).then(val => {
        val = JSON.parse(val);
        if (!val.status) {
            return Promise.reject(val.error);
        }
        return Promise.resolve();
    })
}