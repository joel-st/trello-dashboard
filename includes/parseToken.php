<script>

function getHashParams() {

    var hashParams = {};
    var e,
        a = /\+/g,  
        r = /([^&;=]+)=?([^&;]*)/g,
        d = function (s) { return decodeURIComponent(s.replace(a, " ")); },
        q = window.location.hash.substring(1);

    while (e = r.exec(q))
       hashParams[d(e[1])] = d(e[2]);

    return hashParams;
}

var token = getHashParams();
 
window.location.replace("/?token=" + token['token']);


</script>