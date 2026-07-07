<script>

// FORMAT RUPIAH OTOMATIS
document.addEventListener("DOMContentLoaded", function(){

  // pilih semua input uang
  let uang = document.querySelectorAll('input[name="harga"], input[name="total"], input[name="bayar"]');

  uang.forEach(function(input){

    input.addEventListener('keyup', function(e){

      let angka = this.value.replace(/[^,\d]/g, '').toString();

      let split = angka.split(',');

      let sisa = split[0].length % 3;

      let rupiah = split[0].substr(0, sisa);

      let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

      if(ribuan){
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
      }

      rupiah = split[1] != undefined
        ? rupiah + ',' + split[1]
        : rupiah;

      this.value = rupiah;

    });

  });

});

</script>
<?php $base = "http://localhost/LayananWeb/"; ?>

</div>

<script src="<?= $base ?>adminlte/plugins/jquery/jquery.min.js"></script>
<script src="<?= $base ?>adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= $base ?>adminlte/dist/js/adminlte.min.js"></script>

</body>
</html>