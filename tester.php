

<html>
    <head>
        <title id="pageTitle">Board</title>

        <link id="favicon" rel="shortcut icon" type="image/gif" href="_img/black-favicon.gif" />

        <script src="_js/jquery.js" type="text/javascript"></script>

        <script type="text/javascript">
            $(document).ready(function() {
               
               
               
               
                // USER INFO
                $userNumber = 1;
                $boardID = 1;
                $singleplayer = true;
                $debug = true;
                
                
                // Enable debugging/administrator functionality
                if($debug){
                    $('.clearDBbutton').css('display','block');
                }
                 
                // ==========
               
                // LOAD BOARD ON PAGE LOAD
                startUpdatePage();
                
                // ************************************
                //   CHECK FOR CHANGES EVERY 3 SEC   *
                // ************************************
                
                setInterval(function(){
                    checkForChanges();
                    startUpdatePage();
                    
                    // Get height & width of browser
                    console.log('width: ' + $(window).width());
                    console.log('height: ' + $(window).height());
                    //                    
                    //                    $('#board-wrapper').css('width', $(window).width()- 30);
                    //                    $('.tile-wrapper').css('width', $(window).width() - 1300);
                    //                    $('.tile').css('width', $(window).width() *.4); 
                
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
                
//                $('.newGameButton').click(function(){
//                    var c = confirm("Would you like to start a new game? Other player will need to confirm");
//                   
//                    if(c){
//                        clearDB();
//                        clearBoard();
//                    }
//                    else{
//                       
//                    }
//                });
                
            
                // Check player turn
                checkTurn();
            
                
                
                $('.refresh').hover().css('cursor', 'hand');
                
                $('#refreshed').css('display','none');
                $('.refresh').click(function(){
                    
                    $connectionString = 'connection.php?task=checkUpdate&boardID=' + 1;
                    
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
                            
                    $connectionString = 'connection.php?task=getturn&boardID=' + 1;
                            
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
                    $connectionString = 'connection.php?task=clearboard&boardID=' + 1;
                    $.get($connectionString);
                }
                
                // ************************************
                //   LOAD BOARD FROM DB ON PAGE LOAD  *
                // ************************************
                function startUpdatePage(){
                    $connectionString = 'connection.php?task=getJSON&boardID=' + 1;
                            
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
                    
                    
                    $connectionString = 'connection.php?task=checkUpdate&boardID=' + 1;
                    
                    $.get($connectionString, function($turn) {
                        console.log('turn:' + $turn + ', u:' + $userNumber);
                        
                        if($turn != $userNumber)
                        {
                            console.log('Not Your Turn');
                        }
                        else
                        {
                            console.log('Your Turn');
                        }
                        
                        // ************************************
                        //   UPDATE BOARD FROM DATABASE       *
                        // ************************************
                        function updateBoard(){
                            clearBoard();
                            
                            $connectionString = 'connection.php?task=getJSON&boardID=' + 1;
                            
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
                
                
                
                $(".col-0").mouseenter(function(){
                    $(".col-0").addClass("over");
                }).mouseleave(function(){
                    $(".col-0").removeClass("over");
                });$(".col-1").mouseenter(function(){
                    $(".col-1").addClass("over");
                }).mouseleave(function(){
                    $(".col-1").removeClass("over");
                });$(".col-2").mouseenter(function(){
                    $(".col-2").addClass("over");
                }).mouseleave(function(){
                    $(".col-2").removeClass("over");
                });$(".col-3").mouseenter(function(){
                    $(".col-3").addClass("over");
                }).mouseleave(function(){
                    $(".col-3").removeClass("over");
                });$(".col-4").mouseenter(function(){
                    $(".col-4").addClass("over");
                }).mouseleave(function(){
                    $(".col-4").removeClass("over");
                });$(".col-5").mouseenter(function(){
                    $(".col-5").addClass("over");
                }).mouseleave(function(){
                    $(".col-5").removeClass("over");
                });$(".col-6").mouseenter(function(){
                    $(".col-6").addClass("over");
                }).mouseleave(function(){
                    $(".col-6").removeClass("over");
                });   
                var color = 1;
                var drops = 0;
                var done = 1;
                $('.tile').click(function(){
                    var classString = $(this).attr('class');
                    var col = extractCol(classString);
                    var row = extractRow(classString);
                    var col_tile;
                
                    if(checkTurn() == 1 || $singleplayer){
                
                
                        // Update current player
                        $currentPlayerString = 'connection.php?task=changeplayer&player=' + 1 + '&boardID=' + 1;
                            
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
                            $connectionString = 'connection.php?task=sendJSON&color='+ 1 +'&updateString=' + $newPosition + '&boardID=' + 1;
                
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
                                if($('.tile.row-'+ count +'.col-'+col).children().hasClass('empty'))
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
                        
                        
                                        //                                color = 1;
                                        $returnable = (count + col);
                                    }
                                }
                                else
                                {
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
                            $('drops-' + (drops - 1)).remove();
                
                        }
            
                        drops++;
            
                    }
                    else
                    {
                        console.log('Still not your turn');
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
            <h1>Connect Four - 0.4.7 (Multiplayer)</h1>

        </div>


        <div id="player-turn"><div class="small black"></div><span id="color-turn">Getting</span> Turn</div>
        <div class="refresh button"><div class="float-left">Refresh</div><div id="ajax-loading-img" class="float-left"></div></div>
        <!--        <div class="newGameButton button">New Game</div>--> 
        <div class="clearDBbutton button">Clear</div>
        <div id="board-wrapper">

            <div id="board">
                <div id="dropper"><div></div></div>
                <div class="tile-wrapper"><div class="tile row-5 col-0"><div class="empty piece"><div></div><!--50--></div></div></div><div class="tile-wrapper"><div class="tile row-5 col-1"><div class="empty piece"><div></div><!--51--></div></div></div><div class="tile-wrapper"><div class="tile row-5 col-2"><div class="empty piece"><div></div><!--52--></div></div></div><div class="tile-wrapper"><div class="tile row-5 col-3"><div class="empty piece"><div></div><!--53--></div></div></div><div class="tile-wrapper"><div class="tile row-5 col-4"><div class="empty piece"><div></div><!--54--></div></div></div><div class="tile-wrapper"><div class="tile row-5 col-5"><div class="empty piece"><div></div><!--55--></div></div></div><div class="tile-wrapper"><div class="tile row-5 col-6"><div class="empty piece"><div></div><!--56--></div></div></div><div class="tile-wrapper"><div class="tile row-4 col-0"><div class="empty piece"><div></div><!--40--></div></div></div><div class="tile-wrapper"><div class="tile row-4 col-1"><div class="empty piece"><div></div><!--41--></div></div></div><div class="tile-wrapper"><div class="tile row-4 col-2"><div class="empty piece"><div></div><!--42--></div></div></div><div class="tile-wrapper"><div class="tile row-4 col-3"><div class="empty piece"><div></div><!--43--></div></div></div><div class="tile-wrapper"><div class="tile row-4 col-4"><div class="empty piece"><div></div><!--44--></div></div></div><div class="tile-wrapper"><div class="tile row-4 col-5"><div class="empty piece"><div></div><!--45--></div></div></div><div class="tile-wrapper"><div class="tile row-4 col-6"><div class="empty piece"><div></div><!--46--></div></div></div><div class="tile-wrapper"><div class="tile row-3 col-0"><div class="empty piece"><div></div><!--30--></div></div></div><div class="tile-wrapper"><div class="tile row-3 col-1"><div class="empty piece"><div></div><!--31--></div></div></div><div class="tile-wrapper"><div class="tile row-3 col-2"><div class="empty piece"><div></div><!--32--></div></div></div><div class="tile-wrapper"><div class="tile row-3 col-3"><div class="empty piece"><div></div><!--33--></div></div></div><div class="tile-wrapper"><div class="tile row-3 col-4"><div class="empty piece"><div></div><!--34--></div></div></div><div class="tile-wrapper"><div class="tile row-3 col-5"><div class="empty piece"><div></div><!--35--></div></div></div><div class="tile-wrapper"><div class="tile row-3 col-6"><div class="empty piece"><div></div><!--36--></div></div></div><div class="tile-wrapper"><div class="tile row-2 col-0"><div class="empty piece"><div></div><!--20--></div></div></div><div class="tile-wrapper"><div class="tile row-2 col-1"><div class="empty piece"><div></div><!--21--></div></div></div><div class="tile-wrapper"><div class="tile row-2 col-2"><div class="empty piece"><div></div><!--22--></div></div></div><div class="tile-wrapper"><div class="tile row-2 col-3"><div class="empty piece"><div></div><!--23--></div></div></div><div class="tile-wrapper"><div class="tile row-2 col-4"><div class="empty piece"><div></div><!--24--></div></div></div><div class="tile-wrapper"><div class="tile row-2 col-5"><div class="empty piece"><div></div><!--25--></div></div></div><div class="tile-wrapper"><div class="tile row-2 col-6"><div class="empty piece"><div></div><!--26--></div></div></div><div class="tile-wrapper"><div class="tile row-1 col-0"><div class="empty piece"><div></div><!--10--></div></div></div><div class="tile-wrapper"><div class="tile row-1 col-1"><div class="empty piece"><div></div><!--11--></div></div></div><div class="tile-wrapper"><div class="tile row-1 col-2"><div class="empty piece"><div></div><!--12--></div></div></div><div class="tile-wrapper"><div class="tile row-1 col-3"><div class="empty piece"><div></div><!--13--></div></div></div><div class="tile-wrapper"><div class="tile row-1 col-4"><div class="empty piece"><div></div><!--14--></div></div></div><div class="tile-wrapper"><div class="tile row-1 col-5"><div class="empty piece"><div></div><!--15--></div></div></div><div class="tile-wrapper"><div class="tile row-1 col-6"><div class="empty piece"><div></div><!--16--></div></div></div><div class="tile-wrapper"><div class="tile row-0 col-0"><div class="empty piece"><div></div><!--00--></div></div></div><div class="tile-wrapper"><div class="tile row-0 col-1"><div class="empty piece"><div></div><!--01--></div></div></div><div class="tile-wrapper"><div class="tile row-0 col-2"><div class="empty piece"><div></div><!--02--></div></div></div><div class="tile-wrapper"><div class="tile row-0 col-3"><div class="empty piece"><div></div><!--03--></div></div></div><div class="tile-wrapper"><div class="tile row-0 col-4"><div class="empty piece"><div></div><!--04--></div></div></div><div class="tile-wrapper"><div class="tile row-0 col-5"><div class="empty piece"><div></div><!--05--></div></div></div><div class="tile-wrapper"><div class="tile row-0 col-6"><div class="empty piece"><div></div><!--06--></div></div></div>                <div class="clearer"></div>
            </div>
        </div>

    </body>
</html>