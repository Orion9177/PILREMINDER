function createCountDown(elementId, temps, url){
    var x = setInterval(function () {

        temps-- ;
        j = parseInt(temps) ;
        h = parseInt(temps/3600) ;
        m = parseInt((temps%3600)/60) ;
        s = parseInt((temps%3600)%60) ;
        var h_neg = -h;
        var m_neg = -m;
        var s_neg = -s;

        if(h < 10 && h >= 0){ 
            h = '0' + h; 
        }
        if(m < 10 && m >= 0){ 
            m = '0' + m; 
        }
        if(s < 10 && s >= 0){ 
            s = '0' + s;
        }
        if(h <= 0 && h > -10){
            h_neg = '0' + h_neg;
        }
        if(m <= 0 && m > -10){
            m_neg = '0' + m_neg;
        }
        if(s <= 0 && s > -10){
            s_neg = '0' + s_neg;
        }

        if(temps < 0){
            var t = '-' + h_neg + ':' + m_neg + ':' + s_neg;
            document.getElementById(elementId).innerHTML= t;
        }
        if(temps >= 0){
            var t = h + ':' + m + ':' + s;
            document.getElementById(elementId).innerHTML= t;
        }

		if ((s == 0 && m ==0 && h ==0)) {
			clearInterval(x); 
			window.location.reload();
		}
    }, 1000);
}
