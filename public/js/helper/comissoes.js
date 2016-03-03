var oldVar,
	oldRestante;

$('.currency').on('focus', function(){
	oldVar = $(this).val(),
	id 	= $(this).data('id'),
	oldRestante = $('#restante-'+ id +'').val();
});

$('.currency').keyup(function(event) {
	
	var valor = parseInt($(this).val()),
		self = $(this);
	 	valorFormatado = currency_br($(this).val()),
		id 	= $(this).data('id'),
		elements = $('.currency[data-id='+ id +']'),
		restante = $('#restante-'+ id +''),
		restante_hide = $('#restante_hide-'+ id +''),
		valorparcial = 0,
		valortotal = $('#valor-'+ id +'').val().replace(/[^\d]/g,'');

	if(valorFormatado != undefined){
		$(this).val(valorFormatado);
	}

	for (var i = 0; i < elements.length; i++) {				
		valorparcial += parseInt(elements.eq(i).val().replace(/[^\d]/g,'') || '0');				
	};

	if(valorparcial > valortotal){
		$(this).blur();				
		bootbox.alert('Valor informado superior ao valor da parcela ou restante!');
		$(this).val(oldVar);
		restante_hide.val(oldRestante);
		restante.val(oldRestante);
		return;
	}

	if(parseInt(valortotal - valorparcial)){
		var valor_formatado = currency_br(String(parseInt(valortotal - valorparcial)));
		restante.val(valor_formatado);
		restante_hide.val(valor_formatado);
	}else{
		restante.val('00,00');
		restante_hide.val('00,00');	
	}	
});