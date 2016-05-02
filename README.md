Encode a page in Base64,then push it to browser and decode it!
B64trans means Base64 Transporter.It helps you cross the Great Firewall.
File "cdtldr.php" load China Digital Times
So far,you can modify your own cache dictionary.
If the dictionary does not exist,it will create one.


Introduction for fetch.php :
Generally,the URL looks like "http://example.org/fetch.php?mode=loader&meth=get&url=t92YuIWdoRXan9yL6MHc0RHa"
For PHP it encodes like this (Called SPECIAL Base64):
$encoded_string = strrev(base64_encode($string));

And decodes like this:
$decoded = base64_decode(strrev($encoded_string));

There are 4 modes called "loader","raw","script" and "enc".
The first mode,"loader",is for browsers.So far it can ONLY load text contents including HTML pages.
The second mode,"raw",is used to load images,css files and even binary files!
The third mode,"script",can sometimes be taken place of by "raw".However it returns encoded Javascript.
The fourth mode,"enc",satisfies all above.Usually its format is:
(COMMON Base64 encoded JSON Data)[SPECIAL Base64 encoded BODY]

There is a "meth=get",it tells the program to read from cache.

Also,the "url=t92YuIWdoRXan9yL6MHc0RHa" is encoded by SPECIAL Base64.


Introduction for func.php:
It provides some functions for fetch.php .

