<?php
$boardID = htmlspecialchars(trim($_GET['boardID']));
$player = htmlspecialchars(trim($_GET['player']));
$singleplayer = htmlspecialchars(trim($_GET['sp']));
$debugURL = htmlspecialchars(trim($_GET['debug']));

if ($boardID == '') {
    $boardID = 1;
    pageError('board');
}

if ($player == '') {
    pageError('player');
}

if ($singleplayer == '') {
    $singleplayer = "false";
}

if ($debugURL == '') {
    $debug = "false";
} else {
    $debug = "true";
}

function pageError($error) {
    $errorLog = "An error occurred: ";

    if ($error == "board")
        $errorLog .= "No board selected.<br/>";
    if ($error == "player")
        $errorLog .= "No player selected.<br/>";

    echo $errorLog;
}
?>

<html>
    <head>
        <title id="pageTitle">Loading Board</title>

        <link id="favicon" rel="shortcut icon" type="image/gif" href="_img/black-favicon.gif" />

        <script src="_js/jquery.js" type="text/javascript"></script>

        <script type="text/javascript">
            $(document).ready(function() {
               
                                    
                //                $('#board-wrapper').css('width', $(window).width()- ($(window).width() *.05));
                //                $('.tile-wrapper').css('width', $(window).width() - ($(window).width() *.1));
                //                $('.tile').css('width', $(window).width() *.4); 
               
                // USER INFO
                $userNumber = <?php echo $player; ?>;
                $boardID = <?php echo $boardID; ?>;
                $singleplayer = <?php echo $singleplayer; ?>;
                $debug = <?php echo $debug; ?>;
                
                
                // Enable debugging/administrator functionality
                if($debug){
                    $('.clearDBbutton').css('display','block');
                }
                
                 
                // ==========
               
                // LOAD BOARD ON PAGE LOAD
                startUpdatePage();
                
                // ************************************
                //    TOGGLE TITLE EVER 2 SECONDS     *
                // ************************************
                setInterval(function(){
                    $turn = checkTurn();
                    if($turn == $userNumber){
                        if($('#pageTitle').text() == 'Your Turn ========' || $('#pageTitle').text() == 'Loading Board')
                        {
                            $('#pageTitle').text('======== Your Turn');
                        }
                        else{
                            
                            $('#pageTitle').text('Your Turn ========');
                        }
                    }
                },2000);
                
                
                // ************************************
                //   CHECK FOR CHANGES EVERY 3 SEC   *
                // ************************************
                
                setInterval(function(){
                    checkForChanges();
                    startUpdatePage();
                    
                    // Get height & width of browser
                    //                    console.log('width: ' + $(window).width());
                    //                    console.log('height: ' + $(window).height());
                    
                
                    $turn = checkTurn();
                    
                    if($turn == $userNumber)
                    {
                        // Update turn text on top
                        $('#color-turn').text('Your');
                        
                        
                                              
                    }
                    else{
                        // Update turn text on top
                        $('#color-turn').text('Their');
                        $('#pageTitle').text('Their Turn');
                    }
                         
                    if($turn == 1){
                        $('#player-turn .small').removeClass('red').addClass('black');
                        $('#favicon').attr("href","black-favicon.ico");
                    }
                    else{
                        $('#player-turn .small').removeClass('black').addClass('red');
                        $('#favicon').attr("href","red-favicon.ico");
                    }
                   
                   
                    
                
                },3000);
                
                $('.newGameButton').click(function(){
                    
                    
                    var c = confirm("Are you sure you want to start a new game? Other player will need to confirm");
                    console.log('c=' + c);
                    if(c){
                        
                        // TODO: Will need to confirm with other player
                        clearDB();
                        clearBoard();
                    }
                    else{
                        
                    }
                });
                
            
                // Check player turn
                checkTurn();
            
                $('.refresh').hover().css('cursor', 'hand');
                
                $('#refreshed').css('display','none');
                $('.refresh').click(function(){
                    
                    $connectionString = 'connection.php?task=checkUpdate&boardID=' + <?php echo $boardID; ?>;
                    
                    $.getJSON($connectionString, function(data){
                        
                        checkForChanges();
                    });
                })
                
                $('.clearDBbutton').click(function(){
                    console.log('clearing...');
                    $('#dropper').css('display','none');
                    clearDB();
                    clearBoard();
                });
                
                
                // ************************************
                //   CLEAR ALL PIECES OFF BOARD       *
                // ************************************
                function clearBoard(){
                        
                    for(var $i = 0; $i < 6; $i++)
                    {
                        for(var $j = 0; $j < 7; $j++)
                        {
                            $('.tile').children().addClass('empty').removeClass('piece').removeClass('red').removeClass('black').children().removeClass('indention');
                        }
                    }
                           
                }
                
                // ************************************
                //   Check to see whos turn it is     *
                // ************************************
                function checkTurn(){
                            
                    $connectionString = 'connection.php?task=getturn&boardID=' + <?php echo $boardID; ?>;
                            
                    var $turn = null;
                            
                    $.ajax({
                        url: $connectionString,
                        type: 'get',
                        dataType: 'html',
                        async: false,
                        success: function($data){
                            $turn = $data;
                        }
                    });
                   
                    return $turn;
                            
                }
                
                function clearDB(){
                    $connectionString = 'connection.php?task=clearboard&boardID=' + <?php echo $boardID; ?>;
                    $.get($connectionString);
                }
                
                // ************************************
                //   LOAD BOARD FROM DB ON PAGE LOAD  *
                // ************************************
                function startUpdatePage(){
                    $connectionString = 'connection.php?task=getJSON&boardID=' + <?php echo $boardID; ?>;
                            
                    $.get($connectionString , function($data) {
                                
                        $json = jQuery.parseJSON($data);
                                
                        for(var $i = 0; $i < 6; $i++)
                        {
                            for(var $j = 0; $j < 7; $j++)
                            {
                                var $jsonString = $i.toString() + $j.toString();
                                        
                                if($json[$jsonString] == 1)
                                {
                                    $('.tile.row-' + $i + '.col-' + $j).children().removeClass('empty').addClass('black').children().addClass('indention');
                                            
                                }
                                else if ($json[$jsonString] == 2)
                                {
                                    $('.tile.row-' + $i + '.col-' + $j).children().removeClass('empty').addClass('red').children().addClass('indention');
                                }
                            }      
                        }
                    });
                }
                
                
                // ************************************
                //   CHECK DATABASE FOR CURRENT TURN  *
                // ************************************
                function checkForChanges(){
                    
                    // Display AJAX loading img
                    $('#ajax-loading-img').css('display','block');
                    
                    
                    $connectionString = 'connection.php?task=checkUpdate&boardID=' + <?php echo $boardID; ?>;
                    
                    $.get($connectionString, function($turn) {
                        console.log('turn:' + $turn + ', u:' + $userNumber);
                        
                        if($turn != $userNumber)
                        {
                            //                            console.log('Not Your Turn');
                        }
                        else
                        {
                            //                            console.log('Your Turn');
                        }
                        
                        // Hide AJAX loading img
                        $('#ajax-loading-img').css('display','none');
                        
                    });
                    
                    
                }
                
                
                var row5 = new Array(0,0,0,0,0,0,0);
                var row4 = new Array(0,0,0,0,0,0,0);
                var row3 = new Array(0,0,0,0,0,0,0);
                var row2 = new Array(0,0,0,0,0,0,0);
                var row1 = new Array(0,0,0,0,0,0,0);
                var row0 = new Array(0,0,0,0,0,0,0);
                        
                
                
              
                for(var i = 0; i < 7; i++)
                {
                    if(row5[i] == 1)
                    {
                        $('.tile.row-' + 5 + '.col-' + i).children().removeClass('empty').addClass('black').children().addClass('indention');
                    }
                    else if (row5[i]== 2)
                    {
                        $('.tile.row-' + 5 + '.col-' + i).children().removeClass('empty').addClass('red').children().addClass('indention');
                    }
                }
                for(var i = 0; i < 7; i++)
                {
                    if(row4[i] == 1)
                    {
                        $('.tile.row-' + 4 + '.col-' + i).children().removeClass('empty').addClass('black').children().addClass('indention');
                    }
                    else if (row4[i]== 2)
                    {
                        $('.tile.row-' + 4 + '.col-' + i).children().removeClass('empty').addClass('red').children().addClass('indention');
                    }
                }
                for(var i = 0; i < 7; i++)
                {
                    if(row3[i] == 1)
                    {
                        $('.tile.row-' + 3 + '.col-' + i).children().removeClass('empty').addClass('black').children().addClass('indention');
                    }
                    else if (row3[i]== 2)
                    {
                        $('.tile.row-' + 3 + '.col-' + i).children().removeClass('empty').addClass('red').children().addClass('indention');
                    }
                }
                for(var i = 0; i < 7; i++)
                {
                    if(row2[i] == 1)
                    {
                        $('.tile.row-' + 2 + '.col-' + i).children().removeClass('empty').addClass('black').children().addClass('indention');
                    }
                    else if (row2[i]== 2)
                    {
                        $('.tile.row-' + 2 + '.col-' + i).children().removeClass('empty').addClass('red').children().addClass('indention');
                    }
                }
                for(var i = 0; i < 7; i++)
                {
                    if(row1[i] == 1)
                    {
                        $('.tile.row-' + 1 + '.col-' + i).children().removeClass('empty').addClass('black').children().addClass('indention');
                    }
                    else if (row1[i]== 2)
                    {
                        $('.tile.row-' + 1 + '.col-' + i).children().removeClass('empty').addClass('red').children().addClass('indention');
                    }
                }
                for(var i = 0; i < 7; i++)
                {
                    if(row0[i] == 1)
                    {
                        $('.tile.row-' + 0 + '.col-' + i).children().removeClass('empty').addClass('black').children().addClass('indention');
                    }
                    else if (row0[i]== 2)
                    {
                        $('.tile.row-' + 0 + '.col-' + i).children().removeClass('empty').addClass('red').children().addClass('indention');
                    }
                }
              
              
                
                var warningNum = 0;
                
                
                var drops = 0;
                
                
                
<?php
for ($i = 0; $i < 7; $i++) {
    echo '$(".col-' . $i . '").mouseenter(function(){
                        $(".col-' . $i . '").addClass("over");
                    }).mouseleave(function(){
                        $(".col-' . $i . '").removeClass("over");
                    });';
}
?>   
        var color = <?php echo $player; ?>;
        var drops = 0;
        var done = 1;
        $('.tile').click(function(){
            var classString = $(this).attr('class');
            var col = extractCol(classString);
            var row = extractRow(classString);
            var col_tile;
                
            if(checkTurn() == <?php echo $player ?> || $singleplayer){
                
                
                // Update current player
                $currentPlayerString = 'connection.php?task=changeplayer&player=' + <?php echo $player; ?> + '&boardID=' + <?php echo $boardID; ?>;
                            
                $.get($currentPlayerString);
                        
                // Column Animation coordinates (LEFT)
                if(col == 0)
                {
                    col_tile = 24;
                }
                else if(col == 1)
                {
                    col_tile = 128;
                }
                else if(col == 2)
                {
                    col_tile = 232;
                }
                else if(col == 3)
                {
                    col_tile = 336;
                }
                else if(col == 4)
                {
                    col_tile = 440;
                }
                else if(col == 5)
                {
                    col_tile = 544;
                }
                else if(col == 6)
                {
                    col_tile = 648;
                }
            
                // Reset the dropper to the top, set where to drop from
                $('#dropper').css('top',0);
                $('#dropper').css('left',col_tile);
            
           
                //Recursion!!! BOOM
                var count = 0;
            
                $newPosition = simDrop(col, row, count);
            
            
                // Call function to update the database and add the new position
                updateDB($newPosition);
            
            
                // TODO
                function updateDB($newPosition){
                    $connectionString = 'connection.php?task=sendJSON&color='+ <?php echo $player; ?> +'&updateString=' + $newPosition + '&boardID=' + <?php echo $boardID; ?>;
                
                    $.get($connectionString);
                }
                        
                function simDrop(col,row, count)
                {
                    var count_tile;
               
                    // Row Animation coordinates (TOP)
                    if(count == 0)
                    {
                        count_tile = 602;
                    }
                    else if(count == 1)
                    {
                        count_tile = 496;
                    }
                    else if(count == 2)
                    {
                        count_tile = 390;
                    }
                    else if(count == 3)
                    {
                        count_tile = 284;
                    }
                    else if(count == 4)
                    {
                        count_tile = 178;
                    }
                    else if(count == 5)
                    {
                        count_tile = 72;
                    }
                    
            
                    if(count <= 6)
                    {
                        if( $('.tile.row-'+ count +'.col-' + col).children().hasClass('empty') )
                        {
                    
                            if(color == 1)
                            {                    
                                $('#dropper').removeClass('red').addClass('black').children().addClass('indention');
                                $('#dropper').animate({"top": "+=" + count_tile +"px"}, "slow", function(){
                                    
                                    $('.tile.row-'+ count +'.col-'+col).children().removeClass('empty').addClass('black').children().addClass('indention');
                                    updateDB(count.toString() + col.toString());
                                    
                                });
                            }
                            else if(color == 2)
                            {
                                $('#dropper').removeClass('black').addClass('red').children().addClass('indention'); 
                                $('#dropper').animate({"top": "+=" + count_tile +"px"}, "slow",function(){
                                    
                                    $('.tile.row-'+ count +'.col-'+col).children().removeClass('empty').addClass('red').children().addClass('indention');
                                    updateDB(count.toString() + col.toString());
                                    
                                });
                        
                                $returnable = (count + col);
                            }
                        }
                        else
                        {
                            console.log('else: has class empty');
                            count++;
                        
                            simDrop(col,row,count);
                        
                            $returnable = (count + col);
                        }
                        $returnable = (count + col);
                    }
            
                    if(0)
                    {
            
                        checkWin(col, row)
                        {
                            // TODO: Probably should figure this part out someday...
                        }
                    }
            
                    return $returnable;
                }
                
                // If current turn is black
                if(checkTurn() == 1)
                {
                    console.log('hit-black');
                    
                    //TODO: remove link elements before adding more
                
                    $('drops-' + (drops - 1)).empty();
                
                    
                
                }
                else // If turn is 2: red's turn
                {
                    console.log('hit-red');
                    $('drops-' + (drops - 1)).empty();
                
                }
            
                drops++;
            
            }
            else
            {
                //                console.log('Still not your turn');
            }
        });
        
        
        // Prevent multiple animations from happening at once
        function animationQue(){
            
        }
        
        function extractCol(classString){
        
        
            if(classString[5] == 'r')
            {
                
                return classString[15];
            }
            else
            {
                return classString[9];
            }
    
        }
        
        function extractRow(classString){
             
            if(classString[11] == 'c')
            {
                
                return classString[9];
            }
            else
                return classString[15];
        } 
       
        
        
    });
        </script>

        <link rel="stylesheet" type="text/css" href="_css/board.css" />
    </head>
    <body>
        <div id="info_box">
            <h1>Connect Four - 0.4.7 (Multi-player)</h1>

        </div>


        <div id="player-turn"><div class="small black"></div><span id="color-turn">Getting</span> Turn</div>
        <div class="refresh button"><div >Refresh</div><div id="ajax-loading-img"></div></div>
        <div class="newGameButton button">New Game</div>
        <div class="clearDBbutton button">Clear</div>
        <div id="board-wrapper">

            <div id="board">
                <div id="dropper"><div></div></div>
                <?php
                $count = 1;
                $count_8 = 0;
                $counter = 5;

                for ($i = 5; $i > -1; $i--) {
                    for ($j = 0; $j < 7; $j++) {
                        echo '<div class="tile-wrapper"><div class="tile row-' . $i . ' col-' . $j . '"><div class="empty piece"><div>' . '</div><!--' . $i . $j . '--></div></div></div>';
                    }
                }
                ?>
                <div class="clearer"></div>
            </div>
        </div>

    </body>
</html>