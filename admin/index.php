<?php
include ('../global.php');
?>
<html>
    <head>
        <title>Admin Page</title>

        <script src="../_js/jquery.js" type="text/javascript"></script>

        <script type="text/javascript">
            
            
            $(document).ready(function() {
                
                $connectionString = '../connection.php?task=getboardlist';
                
                $result = null;
                
                $.ajax({
                    url: $connectionString,
                    type: 'get',
                    dataType: 'html',
                    async: false,
                    success: function($data){
                        $result = $data;
                    }
                });
                
                
                
                
            });
        </script>

        <style>
            .clearBox{
                float: left;
                margin-right: 20px;

            }

            .clearBox a{
                display: block;
                padding: 20px;
                background: #999;
                text-decoration: none;
                color: #FFF;
            }
        </style>
    </head>
    <body>
        <h1>Admin Page</h1>

        <h2>Clear Board</h2>
        
        <ul>
            <li class="clearBox"><a href="http://dev.ambooth.com/p/connect/connection.php?task=clearboard&boardID=1">1</a></li>
        </ul>

    </body>
</html>