document.addEventListener('DOMContentLoaded', function() {
  const phoneInputs = document.querySelectorAll('input[name="phone"]');

  phoneInputs.forEach(input => {
    input.addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      if(value.length > 11) value = value.slice(0, 11);

      let formatted = '';
      if(value.length > 0) {
        formatted = '+7';
        if(value.length > 1) formatted += '(' + value.slice(1, 4);
        if(value.length > 4) formatted += ')' + value.slice(4, 7);
        if(value.length > 7) formatted += '-' + value.slice(7, 9);
        if(value.length > 9) formatted += '-' + value.slice(9, 11);
      }
      e.target.value = formatted;
    });
    input.removeAttribute('pattern');
  });
});
