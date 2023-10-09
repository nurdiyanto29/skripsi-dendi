$(document).on('keyup', '.integer', function(event) {
    event.preventDefault();
    var rp = cekInt($(this).val())
    $(this).val(rp < 1 ? '' : rp);
});

// PENGRUPIAHAN
$(document).on('keyup', '.rupiah', function(event) {
    event.preventDefault();
    var rp = rupiah($(this).val())
    $(this).val(rp);
});

// PENGRUPIAHAN
$(document).on('keyup', '.nominal', function(event) {
    event.preventDefault();
    var rp = rupiah($(this).val())
    $(this).val(rp);
});


function rupiah(angka, prefix) {
    angka = angka || 0;
    var number_string = angka.toString().replace(/[^,\d]/g, '').replace(/^0+/, '');

    var split = number_string.split(',');
    var sisa = split[0].length % 3;
    var rupiah = split[0].substr(0, sisa);
    var ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    // console.log(rupiah);
    return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
}

function rup2int(a) {
    if (typeof a == 'number') return a;
    a = a ? parseInt(a.replace('Rp ', '').replace(/\./g, '')) : 0;
    return isNaN(a) ? 0 : a;
}