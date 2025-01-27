$(document).ready(function() {

    $(".text_field").keydown(function(event){
        var inputValue = event.which;
        // allow letters and whitespaces only.
        if(!(inputValue >= 65 && inputValue <= 120) && (inputValue != 32 && inputValue != 0  && inputValue !=8)) { 
            event.preventDefault(); 
        }
  });
  $(".number_field").keydown(function(evt){
    evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
  });

  $(".mobile_number_field").keyup(function () {
    this.value = this.value.replace(/[^0-9\./.]/g,'');
});

});