function currency_br(input){
	input = input.replace(/([^\d]*)/g,"");
	
    if(input.length == 3 ){
        return input.replace(/([0-9]{1})([0-9])/g,"$1,$2");
    }

    if(input.length == 4 ){
        return input.replace(/([0-9]{2})([0-9])/g,"$1,$2");
    }
    if(input.length == 5 ){
        return input.replace(/([0-9]{3})([0-9])/g,"$1,$2");
    }
    if(input.length == 6){
        return input.replace(/([0-9]{4})([0-9])/g,"$1,$2");
    }
    if(input.length == 7){
        return input.replace(/([0-9]{2})([0-9]{3})([0-9])/g,"$1.$2,$3");
    }
    if(input.length == 8){
        return input.replace(/([0-9]{3})([0-9]{3})([0-9])/g,"$1.$2,$3");
    }
    if(input.length == 9){
        return input.replace(/([0-9]{1})([0-9]{3})([0-9]{3})([0-9])/g,"$1.$2.$3,$4");
    }
    if(input.length > 9){
        return input.replace(/([0-9]{1})([0-9]{3})([0-9]{3})([0-9])/g,"$1.$2.$3,$4 ");            
    }
}