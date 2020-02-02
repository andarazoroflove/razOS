function myswitch(obj) {
	var div = null;

	if (document.getElementById){
		div = document.getElementById(obj);
	}else if (document.all){
		div = document.all[obj];
	}else if (document.layers){
		div = document.layers[obj];
	}

	if (!div) {	}
	else if (div.style){
		if (div.style.display != 'none') {
			div.style.display = 'none';
		}else{
			div.style.display = 'block';
		}
	}else{
		if (div.visibility == 'hidden'){
			div.visibility = 'show';
		}else{
			div.visibility = 'hidden';
		}
	}
} 
