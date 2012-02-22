<html>
    <head>
        <title>Lounge</title>
        <LINK href="style.css" rel="stylesheet" type="text/css"/>
        <?php
        include('connection.php');


        $qUser = mysql_query("select * from user");
        $qInstance = mysql_query("select * from instance");
        ?>
    </head>
    <body>
        <h1 id="heading">Connect Four Online</h1>
        <h2>Create a new game or join an active game on the right</h2>
        
        <a href="#newgame">Start New Game</a>
        <div id="session_list">
            <h4>Sessions</h4>
            
        </div> <!-- #session_list -->
    </body>
</html>
