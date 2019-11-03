
function dabDeleteDomElement(element, domIdToStop, stopRemoving){
    if(element && !stopRemoving) {
        // Remove the next element and stop at ID = domIdToStop
        dabDeleteDomElement(element.nextElementSibling, domIdToStop, element.id === domIdToStop);

        // Remove current element
        if(element.id !== domIdToStop){
            element.remove();
        }
    }
}


// Cookies: https://www.w3schools.com/js/js_cookies.asp

function dabSetCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function dabGetCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}