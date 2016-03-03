$("input[id^=date").datetimepicker({
		format: 'DD/MM/YYYY'
	});   	
	
	var totalParcelas = $("#lista-parcelas tr").length - 1;
	var totalContrato = $('input[name=totalcontrato').val(),
		options_parcelas_pagas;
	$.each(selectOptions, function(index, element) {
        	options_parcelas_pagas +='<option value="'+ index +'" >'+ element +'</option>\n';
    });
		
	$("#add-parcelas").click(function(e){
		e.preventDefault();			
		totalParcelas++;

        var tr = '<tr>';
        tr +='<td>';
        tr += totalParcelas + 1;
        tr +='</td>';
        tr +='<td>';
        tr +='<div class="form-group" >';
        tr +='<div class="input-group">';
        tr +='<span class="input-group-addon" >R$</span>';
        tr +='<input class="form-control valor" type="text" name="parcelas_pagas['+ totalParcelas +'][valor]" value="" />';
        tr +='</div>';
        tr +='</div>';
        tr +='</td>';
        tr +='<td>';
        tr +='<div class="form-group relative" >';            
        tr +='<input id="date-'+ totalParcelas +'" class="form-control" type="text" name="parcelas_pagas['+ totalParcelas +'][vencimento]" value="" />';
        tr +='<div class="input-group-addon" >';
        tr +='<span class="glyphicon glyphicon-calendar"></span>';
        tr +='</div>';            
        tr +='</div>';
        tr +='</td>';
        tr +='<td>';
        tr +='<select class="form-control" name="parcelas_pagas['+ totalParcelas +'][tipo]" >';
        tr += options_parcelas_pagas;
        tr +='</select>';
        tr +='</td>';    
        tr +='<td class="text-center" >';
        tr +='<input type="hidden" name="parcelas_pagas['+ totalParcelas +'][liberado]" value="false" />';
        tr +='Não';            
        tr +='</td>';
		tr +='<td>';  
        tr +='<a data-key="'+ totalParcelas +'" class="remove-parcela btn btn-danger" >';
        tr +='<i class="glyphicon glyphicon-minus" ></i>';
        tr +='</a>';
        tr +='</td>';
        tr +='</tr>';			
        $('#lista-parcelas').append(tr);

        // Adicionando o date
        $("input[id^=date").datetimepicker({
    		format: 'DD/MM/YYYY'
    	});  
        
	});

	$('body').on('click','.remove-parcela', function(e){
		e.preventDefault();
		var self = $(this);
		bootbox.confirm("Remover parcela?", function(result){
			if(result){
				self.parent().parent().remove();
				$('li#comissao-' + $(self).data('key')).remove();
				totalParcelas--;
				calcRestante();
			}
		});	
	});

	$('body').on('keyup, change, blur','.valor', function(e){
		
		var valor = currency_br($(this).val());

		if(valor != undefined){
			$(this).val(valor);
		}

		calcRestante(e);
	});

	function calcRestante(e){
		var total = $('.valor').length,
			subvalor = 0;				

		for (var i = 0; i < total; i++) {
			subvalor = parseInt(subvalor) + parseInt($('.valor').eq(i).val().replace(/([^\d]*)/g, '') || 0);
		};

		if(subvalor > totalContrato){
			bootbox.alert('Valor recebido é superior ao valor negociado');				
			if(e){					
				var val = parseInt($(e.target).val().replace(/([^\d]*)/g, ''));
				$(e.target).val('');
				$('#restante, input[name=restante]').val(currency_br(String(totalContrato - (subvalor - val))));	
				$('#recebido, input[name=recebido]').val(currency_br(String(subvalor - val)));
			}
		}else{
			$('#restante, input[name=restante]').val(currency_br(String(totalContrato - subvalor)));	
			$('#recebido, input[name=recebido]').val(currency_br(String(subvalor)));				
		}			
	};