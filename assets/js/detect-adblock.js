function dabDeleteDomElement(element, domIdToStop, stopRemoving) {
    if (element && !stopRemoving) {
        // Remove the next element and stop at ID = domIdToStop
        dabDeleteDomElement(element.nextElementSibling, domIdToStop, element.id === domIdToStop);

        // Remove current element
        if (element.id !== domIdToStop) {
            element.remove();
        }
    }
}

function dabGetPluginBaseUrl() {
    var scripts = document.getElementsByTagName('script');
    var marker = 'detect-adblock/assets/js/detect-adblock.js';
    for (var i = 0; i < scripts.length; i++) {
        var src = scripts[i].getAttribute('src') || '';
        var index = src.indexOf(marker);
        if (index !== -1) {
            // src looks like: [base]/user/plugins/detect-adblock/assets/js/detect-adblock.js
            // We strip "detect-adblock/assets/js/detect-adblock.js" and re-append "detect-adblock"
            // to get: [base]/user/plugins/detect-adblock
            return src.substring(0, index) + 'detect-adblock';
        }
    }
    return '';
}

// language: javascript
function dabDetectAdBlock(timeout = 150) {
    return new Promise(resolve => {
        // Bait creation
        const bait = document.createElement('div');
        bait.id = 'DaEdTbEloCcTk';
        bait.className = 'adsbox ad banner ad-placement google-ads';
        bait.style.width = '1px';
        bait.style.height = '1px';
        bait.style.position = 'absolute';
        bait.style.left = '-9999px';
        document.body.appendChild(bait);

        const cleanAndResolve = (result) => {
            if (document.body.contains(bait)) document.body.removeChild(bait);
            resolve(result);
        };

        const checkDom = () => {
            try {
                const computed = window.getComputedStyle(bait);
                const removed = !document.body.contains(bait);
                const hiddenByCss = computed.display === 'none' || computed.visibility === 'hidden';
                const noSize = bait.offsetParent === null || bait.offsetHeight === 0 || bait.getClientRects().length === 0;
                if (removed || hiddenByCss || noSize) {
                    // If DOM indicates a blockage, confirmation is made via network (optional).
                    doNetworkCheck(result => cleanAndResolve(result));
                    return;
                }
            } catch (e) {
                // error => consider this a possible blockage and test the network
                doNetworkCheck(result => cleanAndResolve(result));
                return;
            }
            // No indication from the DOM side, but run a network test anyway.
            doNetworkCheck(result => cleanAndResolve(result));
        };

        const doNetworkCheck = (cb) => {
            const img = new Image();
            let finished = false;
            const timer = setTimeout(() => {
                if (finished) return;
                finished = true;
                cb(true); // timeout ≈ blocked
            }, 1200);

            img.onload = () => {
                if (finished) return;
                finished = true;
                clearTimeout(timer);
                cb(false); // loaded -> no network blocking
            };
            img.onerror = () => {
                if (finished) return;
                finished = true;
                clearTimeout(timer);
                cb(true); // error -> probably blocked
            };

            // Fake ads image URL (must be blocked by ad blockers)
            const pluginBaseUrl = dabGetPluginBaseUrl();
            const cacheBuster = Date.now();
            if (pluginBaseUrl) {
                img.src = pluginBaseUrl + '/assets/img/ads.png?cb=' + cacheBuster;
            } else {
                // Fallback to a relative path if the base URL could not be detected.
                img.src = '/user/plugins/detect-adblock/assets/img/ads.png?cb=' + Date.now();
            }
        };

        setTimeout(checkDom, timeout);
    });
}


// Cookies: https://www.w3schools.com/js/js_cookies.asp

function dabSetCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function dabGetCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
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