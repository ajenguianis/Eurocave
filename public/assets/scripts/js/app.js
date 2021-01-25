$(function() {
    $(document).on('change', 'input[name=serial]', function(e){

        $('#inputEmail').val('code-'+sha1($(this).val().toUpperCase())+'@eurocave.fr');
        $('#password').val($(this).val().toUpperCase());
        console.log($('#inputEmail').val());
        console.log($('#password').val().toUpperCase());
    });
   var serialNumber=$('#User_serialNumber').val();
   if(serialNumber){
       $('#User_serialNumber').closest('.content-panel').find('.email-user').hide();
   }else{
       $('#User_serialNumber').closest('.serial-user').hide();
   }
});



function sha1 (str) {
    //  discuss at: https://locutus.io/php/sha1/
    // original by: Webtoolkit.info (https://www.webtoolkit.info/)
    // improved by: Michael White (https://getsprink.com)
    // improved by: Kevin van Zonneveld (https://kvz.io)
    //    input by: Brett Zamir (https://brett-zamir.me)
    //      note 1: Keep in mind that in accordance with PHP, the whole string is buffered and then
    //      note 1: hashed. If available, we'd recommend using Node's native crypto modules directly
    //      note 1: in a steaming fashion for faster and more efficient hashing
    //   example 1: sha1('Kevin van Zonneveld')
    //   returns 1: '54916d2e62f65b3afa6e192e6a601cdbe5cb5897'

    var hash
    try {
        var crypto = require('crypto')
        var sha1sum = crypto.createHash('sha1')
        sha1sum.update(str)
        hash = sha1sum.digest('hex')
    } catch (e) {
        hash = undefined
    }

    if (hash !== undefined) {
        return hash
    }

    var _rotLeft = function (n, s) {
        var t4 = (n << s) | (n >>> (32 - s))
        return t4
    }

    var _cvtHex = function (val) {
        var str = ''
        var i
        var v

        for (i = 7; i >= 0; i--) {
            v = (val >>> (i * 4)) & 0x0f
            str += v.toString(16)
        }
        return str
    }

    var blockstart
    var i, j
    var W = new Array(80)
    var H0 = 0x67452301
    var H1 = 0xEFCDAB89
    var H2 = 0x98BADCFE
    var H3 = 0x10325476
    var H4 = 0xC3D2E1F0
    var A, B, C, D, E
    var temp

    // utf8_encode
    str = unescape(encodeURIComponent(str))
    var strLen = str.length

    var wordArray = []
    for (i = 0; i < strLen - 3; i += 4) {
        j = str.charCodeAt(i) << 24 |
            str.charCodeAt(i + 1) << 16 |
            str.charCodeAt(i + 2) << 8 |
            str.charCodeAt(i + 3)
        wordArray.push(j)
    }

    switch (strLen % 4) {
        case 0:
            i = 0x080000000
            break
        case 1:
            i = str.charCodeAt(strLen - 1) << 24 | 0x0800000
            break
        case 2:
            i = str.charCodeAt(strLen - 2) << 24 | str.charCodeAt(strLen - 1) << 16 | 0x08000
            break
        case 3:
            i = str.charCodeAt(strLen - 3) << 24 |
                str.charCodeAt(strLen - 2) << 16 |
                str.charCodeAt(strLen - 1) <<
                8 | 0x80
            break
    }

    wordArray.push(i)

    while ((wordArray.length % 16) !== 14) {
        wordArray.push(0)
    }

    wordArray.push(strLen >>> 29)
    wordArray.push((strLen << 3) & 0x0ffffffff)

    for (blockstart = 0; blockstart < wordArray.length; blockstart += 16) {
        for (i = 0; i < 16; i++) {
            W[i] = wordArray[blockstart + i]
        }
        for (i = 16; i <= 79; i++) {
            W[i] = _rotLeft(W[i - 3] ^ W[i - 8] ^ W[i - 14] ^ W[i - 16], 1)
        }

        A = H0
        B = H1
        C = H2
        D = H3
        E = H4

        for (i = 0; i <= 19; i++) {
            temp = (_rotLeft(A, 5) + ((B & C) | (~B & D)) + E + W[i] + 0x5A827999) & 0x0ffffffff
            E = D
            D = C
            C = _rotLeft(B, 30)
            B = A
            A = temp
        }

        for (i = 20; i <= 39; i++) {
            temp = (_rotLeft(A, 5) + (B ^ C ^ D) + E + W[i] + 0x6ED9EBA1) & 0x0ffffffff
            E = D
            D = C
            C = _rotLeft(B, 30)
            B = A
            A = temp
        }

        for (i = 40; i <= 59; i++) {
            temp = (_rotLeft(A, 5) + ((B & C) | (B & D) | (C & D)) + E + W[i] + 0x8F1BBCDC) & 0x0ffffffff
            E = D
            D = C
            C = _rotLeft(B, 30)
            B = A
            A = temp
        }

        for (i = 60; i <= 79; i++) {
            temp = (_rotLeft(A, 5) + (B ^ C ^ D) + E + W[i] + 0xCA62C1D6) & 0x0ffffffff
            E = D
            D = C
            C = _rotLeft(B, 30)
            B = A
            A = temp
        }

        H0 = (H0 + A) & 0x0ffffffff
        H1 = (H1 + B) & 0x0ffffffff
        H2 = (H2 + C) & 0x0ffffffff
        H3 = (H3 + D) & 0x0ffffffff
        H4 = (H4 + E) & 0x0ffffffff
    }

    temp = _cvtHex(H0) + _cvtHex(H1) + _cvtHex(H2) + _cvtHex(H3) + _cvtHex(H4)
    return temp.toLowerCase()
}
/*
 * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
 * Digest Algorithm, as defined in RFC 1321.
 * Copyright (C) Paul Johnston 1999 - 2000.
 * Updated by Greg Holt 2000 - 2001.
 * See http://pajhome.org.uk/site/legal.html for details.
 */

/*
 * Convert a 32-bit number to a hex string with ls-byte first
 */
var hex_chr = "0123456789abcdef";
function rhex(num)
{
    str = "";
    for(j = 0; j <= 3; j++)
        str += hex_chr.charAt((num >> (j * 8 + 4)) & 0x0F) +
            hex_chr.charAt((num >> (j * 8)) & 0x0F);
    return str;
}

/*
 * Convert a string to a sequence of 16-word blocks, stored as an array.
 * Append padding bits and the length, as described in the MD5 standard.
 */
function str2blks_MD5(str)
{
    nblk = ((str.length + 8) >> 6) + 1;
    blks = new Array(nblk * 16);
    for(i = 0; i < nblk * 16; i++) blks[i] = 0;
    for(i = 0; i < str.length; i++)
        blks[i >> 2] |= str.charCodeAt(i) << ((i % 4) * 8);
    blks[i >> 2] |= 0x80 << ((i % 4) * 8);
    blks[nblk * 16 - 2] = str.length * 8;
    return blks;
}

/*
 * Add integers, wrapping at 2^32. This uses 16-bit operations internally
 * to work around bugs in some JS interpreters.
 */
function add(x, y)
{
    var lsw = (x & 0xFFFF) + (y & 0xFFFF);
    var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
    return (msw << 16) | (lsw & 0xFFFF);
}

/*
 * Bitwise rotate a 32-bit number to the left
 */
function rol(num, cnt)
{
    return (num << cnt) | (num >>> (32 - cnt));
}

/*
 * These functions implement the basic operation for each round of the
 * algorithm.
 */
function cmn(q, a, b, x, s, t)
{
    return add(rol(add(add(a, q), add(x, t)), s), b);
}
function ff(a, b, c, d, x, s, t)
{
    return cmn((b & c) | ((~b) & d), a, b, x, s, t);
}
function gg(a, b, c, d, x, s, t)
{
    return cmn((b & d) | (c & (~d)), a, b, x, s, t);
}
function hh(a, b, c, d, x, s, t)
{
    return cmn(b ^ c ^ d, a, b, x, s, t);
}
function ii(a, b, c, d, x, s, t)
{
    return cmn(c ^ (b | (~d)), a, b, x, s, t);
}

/*
 * Take a string and return the hex representation of its MD5.
 */
function calcMD5(str)
{
    x = str2blks_MD5(str);
    a =  1732584193;
    b = -271733879;
    c = -1732584194;
    d =  271733878;

    for(i = 0; i < x.length; i += 16)
    {
        olda = a;
        oldb = b;
        oldc = c;
        oldd = d;

        a = ff(a, b, c, d, x[i+ 0], 7 , -680876936);
        d = ff(d, a, b, c, x[i+ 1], 12, -389564586);
        c = ff(c, d, a, b, x[i+ 2], 17,  606105819);
        b = ff(b, c, d, a, x[i+ 3], 22, -1044525330);
        a = ff(a, b, c, d, x[i+ 4], 7 , -176418897);
        d = ff(d, a, b, c, x[i+ 5], 12,  1200080426);
        c = ff(c, d, a, b, x[i+ 6], 17, -1473231341);
        b = ff(b, c, d, a, x[i+ 7], 22, -45705983);
        a = ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
        d = ff(d, a, b, c, x[i+ 9], 12, -1958414417);
        c = ff(c, d, a, b, x[i+10], 17, -42063);
        b = ff(b, c, d, a, x[i+11], 22, -1990404162);
        a = ff(a, b, c, d, x[i+12], 7 ,  1804603682);
        d = ff(d, a, b, c, x[i+13], 12, -40341101);
        c = ff(c, d, a, b, x[i+14], 17, -1502002290);
        b = ff(b, c, d, a, x[i+15], 22,  1236535329);

        a = gg(a, b, c, d, x[i+ 1], 5 , -165796510);
        d = gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
        c = gg(c, d, a, b, x[i+11], 14,  643717713);
        b = gg(b, c, d, a, x[i+ 0], 20, -373897302);
        a = gg(a, b, c, d, x[i+ 5], 5 , -701558691);
        d = gg(d, a, b, c, x[i+10], 9 ,  38016083);
        c = gg(c, d, a, b, x[i+15], 14, -660478335);
        b = gg(b, c, d, a, x[i+ 4], 20, -405537848);
        a = gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
        d = gg(d, a, b, c, x[i+14], 9 , -1019803690);
        c = gg(c, d, a, b, x[i+ 3], 14, -187363961);
        b = gg(b, c, d, a, x[i+ 8], 20,  1163531501);
        a = gg(a, b, c, d, x[i+13], 5 , -1444681467);
        d = gg(d, a, b, c, x[i+ 2], 9 , -51403784);
        c = gg(c, d, a, b, x[i+ 7], 14,  1735328473);
        b = gg(b, c, d, a, x[i+12], 20, -1926607734);

        a = hh(a, b, c, d, x[i+ 5], 4 , -378558);
        d = hh(d, a, b, c, x[i+ 8], 11, -2022574463);
        c = hh(c, d, a, b, x[i+11], 16,  1839030562);
        b = hh(b, c, d, a, x[i+14], 23, -35309556);
        a = hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
        d = hh(d, a, b, c, x[i+ 4], 11,  1272893353);
        c = hh(c, d, a, b, x[i+ 7], 16, -155497632);
        b = hh(b, c, d, a, x[i+10], 23, -1094730640);
        a = hh(a, b, c, d, x[i+13], 4 ,  681279174);
        d = hh(d, a, b, c, x[i+ 0], 11, -358537222);
        c = hh(c, d, a, b, x[i+ 3], 16, -722521979);
        b = hh(b, c, d, a, x[i+ 6], 23,  76029189);
        a = hh(a, b, c, d, x[i+ 9], 4 , -640364487);
        d = hh(d, a, b, c, x[i+12], 11, -421815835);
        c = hh(c, d, a, b, x[i+15], 16,  530742520);
        b = hh(b, c, d, a, x[i+ 2], 23, -995338651);

        a = ii(a, b, c, d, x[i+ 0], 6 , -198630844);
        d = ii(d, a, b, c, x[i+ 7], 10,  1126891415);
        c = ii(c, d, a, b, x[i+14], 15, -1416354905);
        b = ii(b, c, d, a, x[i+ 5], 21, -57434055);
        a = ii(a, b, c, d, x[i+12], 6 ,  1700485571);
        d = ii(d, a, b, c, x[i+ 3], 10, -1894986606);
        c = ii(c, d, a, b, x[i+10], 15, -1051523);
        b = ii(b, c, d, a, x[i+ 1], 21, -2054922799);
        a = ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
        d = ii(d, a, b, c, x[i+15], 10, -30611744);
        c = ii(c, d, a, b, x[i+ 6], 15, -1560198380);
        b = ii(b, c, d, a, x[i+13], 21,  1309151649);
        a = ii(a, b, c, d, x[i+ 4], 6 , -145523070);
        d = ii(d, a, b, c, x[i+11], 10, -1120210379);
        c = ii(c, d, a, b, x[i+ 2], 15,  718787259);
        b = ii(b, c, d, a, x[i+ 9], 21, -343485551);

        a = add(a, olda);
        b = add(b, oldb);
        c = add(c, oldc);
        d = add(d, oldd);
    }
    return rhex(a) + rhex(b) + rhex(c) + rhex(d);
}
function checkLogin(){
    $.ajax({
        url:        '/check/login',
        type:       'POST',
        dataType:   'json',
        async:      true,

        success: function(data) {
            console.log(data);
            // $('#firster').show();
            // $("#frame1").attr("src", decodeURIComponent($.urlParam('url', $(this).attr("href")))); // no prepend of current URL, just use the href directly
            // return false;
        },
        error : function(xhr, textStatus, errorThrown) {
            alert('Ajax request failed.');
        }
    });
}
$(document).ready(function () {


    $('#firster').hide();
    $('<div />', {
        id: 'firster',
        style: 'position: fixed; top: 0; bottom: 0; right: 0; max-width: 500px; max-height: 100%; z-index: 120;'
    }).appendTo('body');
    $('#firster').hide();
    $('<iframe />', {
        name: 'frame1',
        id: 'frame1',
        src: ''
    })
        .css({
            'height': '100%',
            'width': '450px'
        }).appendTo('#firster');

    function loadIframe(iframeName, url) {
        if ( window.frames[iframeName] ) {
            window.frames[iframeName].location = url;
            return false;
        }
        return true;
    }
    $(document).on("click","#user-iframe",function(e) {
        e.preventDefault();

        $.ajax({
            url:        '/check/login',
            type:       'POST',
            dataType:   'json',
            async:      true,

            success: function(data) {
                if(data.status==='success'){
                    window.location.href=$("#user-iframe").attr('data-href');
                }else{
                    window.location.href=data.redirectUrl;
                }
            },
            error : function(xhr, textStatus, errorThrown) {
                alert('Ajax request failed.');
            }
        });

    })

    $.urlParam = function (name, urlItself) {
        var results = new RegExp('[\?&]' + name + '=([^&#]*)')
            .exec(urlItself);

        return results[1] || 0;
    }

    // console.log($.urlParam('url', urlItself)); //registration

    /*reset password*/
    var firstpass=$('#change_password_form_plainPassword_first');
    firstpass.addClass('form-control');

    firstpass.closest('div').addClass('form-group');
    firstpass.closest('div').find('label').addClass('text-info');
    var secondpass=$('#change_password_form_plainPassword_second');
    secondpass.addClass('form-control');

    secondpass.closest('div').addClass('form-group');
    secondpass.closest('div').find('label').addClass('text-info');
});
