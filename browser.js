function reverseString(instr) {
    var newstr = "";
    for (var i=instr.length;i>0;i--)
    {
        newstr = newstr + instr.substring(i-1,i);
    }
    return newstr;
}
function fetch(pageurl) {
    //This piece of code is adapted from http://www.w3school.com.cn/ajax/
    var xmlhttp;
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
       xmlhttp=new XMLHttpRequest();
    } else {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function(){
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            var ts = xmlhttp.responseText;
            var prefixcode = '<script src="base64.js"></script><script src="browser.js"></script>' + "\n";
            /*var insertcode = '<script>changeSrc("'+window.fetcherUrl+'","'+window.hostUrl+'");';
            var insertcode = insertcode + "\n" + "</script>";*/
            var ind = ts.indexOf("[") + 1;
            var indd = ts.indexOf("]");
            var text = ts.slice(ind,indd);
            var content = Base64.decode(reverseString(text));
            var content = changeScripts(window.fetcherUrl,window.hostUrl,content,parseTag);
            var content = prefixcode + content;
            //alert(content);
            document.write(content);
        }
    }
    xmlhttp.open("GET","fetch.php?url="+reverseString(Base64.encodeURI(pageurl))+"&mode=enc&meth=get",true);
    xmlhttp.send();
}
function changeTagsAttr(tagName,attrName,flag,fetchername,host)
{
    //It can change the tags!
    var faddarr = fetchername.split("/");
    var faddrarray = faddarr.slice(0,3);
    var faddre = faddrarray.join("/");
    var faddr = faddre + "/";
    var taglist = document.getElementsByTagName(tagName);
    for (var i=0;i<taglist.length;i++) {
        var x = taglist[i];
        eval("var temp = new String(x." + attrName +");");
        if (temp.match(faddr)) {
            var tgarray = temp.split("/").slice(3);
            var hostArray = host.split("/").slice(0,3);
            var totalArray = hostArray.concat(tgarray);
            var totalStr = totalArray.join("/");
            var result = fetchername + flag + reverseString(Base64.encodeURI(totalStr));
        } else {
            var result = fetchername + flag + reverseString(Base64.encodeURI(temp));
        }
        eval("x." + attrName + " = result;");
    }
}
function encodeURLStr(fetchername,urlin,current,add)
{
    var temp = new String(urlin);
    if (temp.slice(0,1) == "/") {
        var tgarray = temp.split("/");
        var hostArray = current.split("/").slice(0,3);
        var totalArray = hostArray.concat(tgarray);
        var totalStr = totalArray.join("/");
        var result = fetchername + "?" + add +"url=" + reverseString(Base64.encodeURI(totalStr));
    } else if((temp.match(":"))) {
        var result = fetchername + "?" + add + "url=" + reverseString(Base64.encodeURI(temp));
    } else {
        if (current.slice(current.length - 1) == "/")
        {
            var r = current + urlin;
        } else {
            var r = current + "/" + urlin;
        }
        var result = fetchername + "?" + add + "url=" + reverseString(Base64.encodeURI(r));
    }
    return result;
}
function modifyTag(Tag,attrName,fetcher,currUrl,add)
{
    if (Tag.match('"')) { var Quote = '"'; }
    else { var Quote = "'"; }
    var attrIndex = Tag.indexOf(attrName);
    var afterSrc = Tag.substring(attrIndex);
    var firstQuote = afterSrc.indexOf(Quote)+1;
    var invaildAddr = afterSrc.substring(firstQuote);
    var nextQuote = invaildAddr.indexOf(Quote);
    var realAddr = invaildAddr.substring(0,nextQuote);
    var encodedURL = encodeURLStr(fetcher,realAddr,currUrl,add);
    var quoteIndex = attrIndex + firstQuote;
    var nextQuoteIndex = quoteIndex + nextQuote;
    var parsedTag = Tag.substring(0,quoteIndex) + encodedURL + Tag.substring(nextQuoteIndex);
    return parsedTag;
}
function parseTag(Tag,fetcher,currUrl)
{
    var scriptSrcPrefix = "mode=script&meth=get&";
    var imglinkSrcPrefix = "mode=raw&meth=get&";
    var aHrefPrefix = "mode=loader&meth=get&";
    if (Tag.match("<script") && Tag.match("src"))
    {
        var parsedTag = modifyTag(Tag,"src",fetcher,currUrl,scriptSrcPrefix);
    } else if (Tag.match("<img") && Tag.match("src")) {
        var parsedTag = modifyTag(Tag,"src",fetcher,currUrl,imglinkSrcPrefix);
    } else if (Tag.match("<a") && Tag.match("href")) {
        var parsedTag = modifyTag(Tag,"href",fetcher,currUrl,aHrefPrefix);
    } else if (Tag.match("<link") && Tag.match("href")) {
        var parsedTag = modifyTag(Tag,"href",fetcher,currUrl,imglinkSrcPrefix);
    } else {
        var parsedTag = Tag;
    }
    return parsedTag;
}
function changeScripts(fetchername,currentURL,hc,parseFunc)
{
    //The same as changeSrc.But it is String Oriented.
    var parsedCode = new String("");
    //var hc = new String(htmlcode);
    var EnableScripts = true;
    do
    {
        do{
            var index1 = hc.indexOf("<");
            var index2 = hc.indexOf(">");
        } while(!index2 > index1);
        var Tag = hc.slice(index1,index2 + 1);
        var beforeTag = hc.slice(0,index1);
        if (EnableScripts) {
            var parsedTag = parseFunc(Tag,fetchername,currentURL);
        } else {
            var newTag = parseFunc(Tag,fetchername,currentURL);
            if (newTag == Tag) { var parsedTag = Tag;}
            else { var parsedTag = '<script>'; }
        }
        var hc = hc.substring(index2+1);
        parsedCode = parsedCode + beforeTag + parsedTag;
    }while (hc.length > 4);
    return parsedCode;
}
function changeSrc(fetchername,host) {
    //This function is used to change the target of a,link and img.
    /*
    changeTagsAttr("a","href","?mode=loader&url=",fetchername,host);
    changeTagsAttr("img","src","?mode=raw&meth=get&url=",fetchername,host);
    changeTagsAttr("link","href","?mode=script&meth=get&url=",fetchername,host);
    var formlist = document.getElementsByTagName("form");
    for (var i=0;i<formlist.length;i++) {
        formlist[i].method="post";
    }
    changeTagsAttr("form","action","?mode=p2get_p&url=",fetchername,host);*/
}

