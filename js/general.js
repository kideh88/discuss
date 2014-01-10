var addEvent = (function () {

    if(window.addEventListener) {
        return function (el, ev, fn) {
            el.addEventListener(ev, fn, false);
        };
    }
    else if(window.attachEvent) {
        return function (el, ev, fn) {
            el.attachEvent('on' + ev, fn);
        };
    }
    else {
        return function (el, ev, fn) {
            el['on' + ev] = fn;
        };
    }

})();

var removeEvent = (function () {

    if(window.removeEventListener) {
        return function (el, ev, fn) {
            el.removeEventListener(ev, fn, false);
        };
    }
    else if(window.detachEvent) {
        return function (el, ev, fn) {
            el.detachEvent('on' + ev, fn);
        };
    }
    else {
        return function (el, ev, fn) {
            el['on' + ev] = '';
        };
    }

})();

var show = function(objElement, strValue) {
    if(!strValue) {
        strValue = 'block'
    }
    objElement.style.display = strValue;
};

var hide = function(objElement, strValue) {
    if(!strValue) {
        strValue = 'none'
    }
    objElement.style.display = strValue;
};

var showResponse = function(mixData) {
    console.log(mixData);
};