<?php
$chatInstance = htmlspecialchars(trim($_GET['chatInstance']));
$userID = htmlspecialchars(trim($_GET['userID']));

if ($chatInstance == "") {
    echo "ERROR: Chat Instance not set";
}

if ($userID == "") {
    echo "ERROR: Chat Instance not set";
}
?>
<html>
    <head>
        <title>Chat System</title>
        <script src="../_js/jquery.js" type="text/javascript"></script>

        <script type="text/javascript">
            $(document).ready(function() {
                $chatInstance = <?= $chatInstance; ?>;
                $userID = <?= $userID; ?>;
                
                // Start by updating the textarea
                updateChatList();
                
                
                // Check for changes every 3 seconds
                setInterval(function(){
                    checkNew();
                },300);
                
                $('textarea#chatTextArea').focus();
                
                $('#chatSendBtn').click(function(){
                    sendText($('textarea#chatTextArea').val());                
                });
                
                // Handle if user presses enter
                $('textarea#chatTextArea').keydown(function(e) {
                    if(e.keyCode == 13) {
                        e.preventDefault(); // Makes no difference
                        
                        // Call function to send string
                        sendText($('textarea#chatTextArea').val());
                    }
                });
                
                
                function sendText($text){
                    $connectionString = 'http://dev.ambooth.com/p/connect/connection.php?task=sendChat&chatString=' + encodeURIComponent($text) + '&chatInstance=' + $chatInstance + '&userID=' + $userID;
                   
                    console.log($connectionString);
                    $.get($connectionString);
                   
                    // clear textarea
                    $('textarea#chatTextArea').val('');
                    
                    // Retain focus on textarea
                    $('textarea#chatTextArea').focus();
                }
                
                function checkNew(){
                    
                    updateChatList();
                }
                
                
                function updateChatList(){
                    
                    // Get the contents of current chat
                    $json = null;
                    $.ajax({
                        url: 'http://dev.ambooth.com/p/connect/connection.php?task=getChat&chatInstance=' + $chatInstance + '&userID=' + $userID,
                        type: 'get',
                        dataType: 'json',
                        async: false,
                        success: function($data){
                            $json = eval($data);
                        },
                        error: function(){
                            console.log('Cannot connect to database');
                        }
                    });
                    
                    $.each($json.chatLog.line, function(){
                       console.log(this.timestamp);
                    })
                    
                    
                    $('#chatTextList').val($json.chatLog.line.chatString);
                }
            });
           
        </script>

        <link rel="stylesheet" type="text/css" href="../_css/chat.css" />

    </head>

    <body>
        <textarea disabled="disabled" id="chatTextList">

        </textarea>
        <form>
            <textarea id="chatTextArea"> </textarea>
            <div id="chatSendBtn" class="button">Send</div>
        </form>
    </body>
</html>