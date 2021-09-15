// var temps = "<?php echo $secondes;?>";
// var timer = setInterval('createCountDown()',1000);
// var url = "<?php echo $redirection;?>";

function Redirection() {
    setTimeout("window.location=url", 500);
}
            
// function createCountDown(elementId){
//     var x = setInterval(function () {
//         temps--;

//         j = parseInt(temps);
//         h = parseInt(temps/3600);
//         m = parseInt((temps%3600)/60);
//         s = parseInt((temps%3600)%60);
//         var h_neg = -h;
//         var m_neg = -m;
//         var s_neg = -s;

//         if(h < 10 && h >= 0){ 
//             h = '0' + h; 
//         }
//         if(m < 10 && m >= 0){ 
//             m = '0' + m; 
//         }
//         if(s < 10 && s >= 0){ 
//             s = '0' + s;
//         }
//         if(h <= 0 && h > -10){
//             h_neg = '0' + h_neg;
//         }
//         if(m <= 0 && m > -10){
//             m_neg = '0' + m_neg;
//         }
//         if(s <= 0 && s > -10){
//             s_neg = '0' + s_neg;
//         }

//         if(temps < 0){
//             var t = '-' + h_neg + ':' + m_neg + ':' + s_neg;
//             document.getElementById(elementId).innerHTML= t;
//         }
//         if(temps > 0){
//             var t = h + ':' + m + ':' + s;
//             document.getElementsById(elementId).innerHTML= t;
//         }

//         if ((s == 0 && m ==0 && h ==0)) {
//             clearInterval(timer);
//             Redirection(url)
//         }
//         }, 1000);
// }


function createCountDown(elementId, date) {
// Set the date we're counting down to
    var countDownDate = new Date(date).getTime();

// Update the count down every 1 second
    var x = setInterval(function () {

// Get todays date and time
        var now = new Date().getTime();

// Find the distance between now an the count down date
        var distance = countDownDate - now;

// Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        var days_neg = -days;
        var hours_neg = -hours;
        var minutes_neg = -minutes;
        var seconds_neg = -seconds;

// Rajout d'un "0" lorsque le chiffres sont < 10
        if(days < 10 && days >= 0){ 
            days = '0' + days; 
        }
        if(hours < 10 && hours >= 0){ 
            hours = '0' + hours; 
        }
        if(minutes < 10 && minutes >= 0){ 
            minutes = '0' + minutes; 
        }
        if(seconds < 10 && seconds >= 0){ 
            seconds = '0' + seconds;
        }

// Lorsque le timer est négatif
        if(days <= 0 && days > -10){ 
            days_neg = '0' + days_neg;
        }
        if(hours <= 0 && hours > -10){
            hours_neg = '0' + hours_neg;
        }
        if(minutes <= 0 && minutes > -10){
            minutes_neg = '0' + minutes_neg;
        }
        if(seconds <= 0 && seconds > -10){
            seconds_neg = '0' + seconds_neg;
        }

// Display the result in the element with id="timer"
        if(distance > 0){
            var t = days + ':' + hours + ':' + minutes + ':' + seconds;
            document.getElementById(elementId).innerHTML = t;
        }

// If the count down is finished, write some text
        if (distance < 0) {
            var t = '-' + days_neg + ':' + hours_neg + ':' + minutes_neg + ':' + seconds_neg;
            document.getElementById(elementId).innerHTML = t;
        }
                
        if ((seconds == 0 && minutes ==0 && hours ==0)) {
            clearInterval(timer);
            url = "<?php echo $redirection;?>"
            Redirection(url)
        }

    }, 1000);
}

