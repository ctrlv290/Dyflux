
var RegexpPattern = {
    name: /^[0-9a-zA-Z\s]+$/,
    adminName: /^[0-9a-zA-Zㄱ-힣\s]+$/,
    password: /^[a-zA-Z\d]+$/,
    number: /^[0-9]+$/,
    english: /^[a-zA-Z]+$/,
    userID: /^[0-9a-z\-_]+$/,
    email: /^[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*@[0-9a-zA-Z-]+(\.[a-zA-Z-]+){1,2}$/,
    emailID: /^[0-9a-zA-Z.\-_]+$/,
    emailAddr: /^[0-9a-zA-Z-]+(\.[a-zA-Z-]+){1,2}$/,
    spChar: /^[^\~\`\!\@\#\$\%\^\&\*\(\)\_\+\|\-\=\\]$/,
    tag: /^[0-9a-zA-Zㄱ-힣]+$/
};



/*******************************************************************************
* string prototype
* trim, toCurrency
*******************************************************************************/
String.prototype.trim = function () {
    return this.replace(/(^[\s　]+)|([\s　]+$)/g, "");
};


String.prototype.isRegexp = function (_pattern) {
    return (_pattern && !_pattern.test(this)) ? false : true;
};


String.prototype.removeTag = function () {

    var str = this.replace(new RegExp("<(/)?([a-zA-Z]*)(\\s[a-zA-Z]*=[^>]*)?(\\s)*(/)?>", "gim"), "");
    return str.replace(new RegExp("&nbsp;", "gim"), " ");
};


String.prototype.toCurrency = function () {
    var str = new Array();
    number = String(this);

    for (var i = 1; i <= number.length; i++) {
        if (i % 3)
            str[number.length - i] = number.charAt(number.length - i);
        else
            str[number.length - i] = ',' + number.charAt(number.length - i);
    }
    return str.join('').replace(/^,/, '');
};

String.prototype.toBytes = function () {

    var bytesAll = 0;

    for (var i = 0; i < this.length; i++) {
        var _char = this.charAt(i);
        var encodedChar = encodeURI(_char);
        var bytesCount = 0;
        if (encodedChar.indexOf("%") != -1) {
            bytesCount = encodedChar.split("%").length - 1;
            if (bytesCount == 0) bytesCount++;
            var tmp = encodedChar.length - (bytesCount * 3);
            bytesCount = bytesCount + tmp;
        } else {
            bytesCount = encodedChar.length;
        }
        bytesAll += bytesCount;
    }

    return bytesAll;
};

String.prototype.toLength = function () {
    return this.length;
};
