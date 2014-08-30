<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Error</title>
 
    <style type="text/css">
        body {
            background: #f7fbe9;
            font-family: "Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana;
        }
 
        #error {
            background: #333;
            width: 360px;
            margin: 0 auto;
            margin-top: 100px;
            color: #fff;
            padding: 10px;
 
            -moz-border-radius-topleft: 4px;
            -moz-border-radius-topright: 4px;
            -moz-border-radius-bottomleft: 4px;
            -moz-border-radius-bottomright: 4px;
            -webkit-border-top-left-radius: 4px;
            -webkit-border-top-right-radius: 4px;
            -webkit-border-bottom-left-radius: 4px;
            -webkit-border-bottom-right-radius: 4px;
 
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
        }
 
        h1 {
            padding: 10px;
            margin: 0;
            font-size: 36px;
        }
 
        p {
            padding: 0 20px 20px 20px;
            margin: 0;
            font-size: 12px;
        }
 
        img {
            padding: 0 0 5px 260px;
        }
    </style>
</head>
<body>
    <div id="error">
        <h1>Error</h1>
        <p>主人，出错了%>_<% <br>万万没想到，<?php echo $errorMsg?>啊！！</p>
    </div>
</body>
</html>
