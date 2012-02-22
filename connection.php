<?php

include ('global.php');

// user info
$db_host = 'localhost';
$db_name = 'pma_c4';
$db_user = 'pma_c4';
$db_pass = 'connect4';

$redirect = htmlspecialchars(trim($_GET['redirectTo']));
$task = htmlspecialchars(trim($_GET['task']));
$boardID = htmlspecialchars(trim($_GET['boardID']));
$updateString = htmlspecialchars(trim($_GET['updateString']));
$color = htmlspecialchars(trim($_GET['color']));
$player = htmlspecialchars(trim($_GET['player']));

// Tasks for chat system
$chatString = htmlspecialchars(trim($_GET['chatString']));
$chatInstance = htmlspecialchars(trim($_GET['chatInstance']));
$userID = htmlspecialchars(trim($_GET['userID']));

// New user information

$new_username = $_POST['u'];
$new_firstName = $_POST['fn'];
$new_lastName = $_POST['ln'];
$new_password = sha1($_POST['p']);
$new_email = $_POST['e'];
//mySQL connection

$sql = mysql_connect($db_host, $db_user, $db_pass) or die(mysql_error());

switch ($task) {
    case "checkUpdate":
        mysql_select_db($db_name) or die(mysql_error());

        $json = mysql_query("SELECT turn FROM ajaxBoard where boardID = " . $boardID);

        $returnJson = mysql_fetch_array($json);

        echo $returnJson[0];
        break;

    case "getturn":
        mysql_select_db($db_name) or die(mysql_error());

        $json = mysql_query("SELECT turn FROM ajaxBoard where boardID = " . $boardID);

        $turn = mysql_fetch_array($json);

        echo $turn[0];
        break;

    case "getJSON":
        mysql_select_db($db_name) or die(mysql_error());

        $json = mysql_query("SELECT jsonFile FROM ajaxBoard where boardID = " . $boardID);

        $returnJson = mysql_fetch_array($json);

        echo $returnJson['jsonFile'];
        break;
    case "sendJSON":
        mysql_select_db($db_name) or die(mysql_error());
        echo 'updating...' . '<br/>';
        echo 'boardID = ' . $boardID . '<br/>';
        echo 'task = ' . $task . '<br/>';
        echo 'string = ' . $updateString . '<br/>';
        $updatedJSON = parseJSON($updateString, $color);
        echo $updatedJSON . '<';

        mysql_query("UPDATE ajaxBoard SET jsonFile='" . $updatedJSON . "' WHERE boardID = " . $boardID);
        break;

    case "createUser":
        mysql_select_db($db_name) or die(mysql_error());
        echo 'creating';
        mysql_query("INSERT INTO `" . $db_name . "`.`user` (`userid`, `username`, `firstName`, `lastName`, `cookie`, `email`, `password`) VALUES (NULL, '" . $new_username . "', '" . $new_firstName . "', '" . $new_lastName . "', '', '" . $new_email . "', '" . $new_password . "');") or die(mysql_error());

        break;

    case "clearboard":
        if ($boardID != "") {
            echo 'clearing board ' . $boardID;
            $clearedBoard = '{"00":0,"01":0,"02":0,"03":0,"04":0,"05":0,"06":0,"10":0,"11":0,"12":0,"13":0,"14":0,"15":0,"16":0,"20":0,"21":0,"22":0,"23":0,"24":0,"25":0,"26":0,"30":0,"31":0,"32":0,"33":0,"34":0,"35":0,"36":0,"40":0,"41":0,"42":0,"43":0,"44":0,"45":0,"46":0,"50":0,"51":0,"52":0,"53":0,"54":0,"55":0,"56":0}';
            mysql_select_db($db_name) or die(mysql_error());
            mysql_query("UPDATE ajaxBoard SET jsonFile='" . $clearedBoard . "' WHERE boardID = " . $boardID);
        } else {
            echo 'Error: No board set';
        }
        break;

    case "changeplayer":

        // First see which turn is set in database
        mysql_select_db($db_name) or die(mysql_error());
        $currentTurn = mysql_query("SELECT turn FROM ajaxBoard where boardID = " . $boardID);

        $dbCurrentTurn = mysql_fetch_array($currentTurn);

        $dbCurrentTurn = $dbCurrentTurn['turn'];

        if ($dbCurrentTurn == 1)
            $newPlayer = 2;
        else
            $newPlayer = 1;

        echo "Changing turn. Was " . $dbCurrentTurn . ', now ' . $newPlayer;
        mysql_select_db($db_name) or die(mysql_error());
        mysql_query("UPDATE ajaxBoard SET turn='" . $newPlayer . "' WHERE boardID = " . $boardID);

        break;

    case "getboardlist":
        mysql_select_db($db_name) or die(mysql_error());
        $list = mysql_query("SELECT boardID FROM ajaxBoard");

        $returnable = '{"boardList":';


        while ($row = mysql_fetch_array($list)) {

            $returnable .= '"' . $row[0] . '"';

            $returnable .= ',';

            $count++;
        }
        $returnable .= '}';

        echo $returnable;

        break;
    case "sendChat":
        echo 'sending chat string: ' . $chatString;
        echo '<br/>userID: ' . $userID;
        echo '<br/>chatInstance: ' . $chatInstance;
        mysql_select_db($db_name) or die(mysql_error());
        $temp = mysql_query("INSERT into chat (userID,chatString,chatInstance,timestamp) VALUES (" . $userID . ",'" . $chatString . "', " . $chatInstance . ", NOW())") or die(mysql_error());
        break;

    case "getChat":

        mysql_select_db($db_name) or die(mysql_error());

        $qString = mysql_query("SELECT user.firstName, chat.chatString, chat.timestamp FROM user,chat WHERE user.`userid` = chat.`userID` AND chat.chatInstance = " . $chatInstance);

        $returnable = null;

        $returnable = '{"chatLog":[{';

        $whileCount = 0;
        $rowCount = mysql_num_rows($qString);
        $first = true;

        while ($row = mysql_fetch_array($qString)) {

            if (!$first)
                $returnable .= ',';

            $returnable .= '"line":{';
            $returnable .= '"First Name":"' . $row['firstName'];
            $returnable .= '","chatString": "' . $row['chatString'] . '"';
            $returnable .= ',"timestamp": "' . $row['timestamp'] . '"}';


            if ($whileCount == $rowCount) {
                $returnable .= ', ';
            }

            $first = false;


            $whileCount++;
        }

        $returnable .= '}]}';

        echo $returnable;

        break;
}

function parseJSON($position, $color) {
    $JSONbefore = getJSON();

    // Update db to reflect last move
    updateLastMove($position);


    $start = strpos($JSONbefore, $position);

    // Get beginning JSON string segment
    for ($i = 0; $i < $start + 4; $i++)
        $stringP1 .= substr($JSONbefore, $i, 1);

    // Get ending JSON string segment
    for ($i = $start + 5; $i < strlen($JSONbefore); $i++)
        $stringP2 .= substr($JSONbefore, $i, 1);

    // Put stringP1 & stringP2 together along with the color
    $returnable = $stringP1 . $color . $stringP2;


    return $returnable;
}

function getJSON() {

    $db_host = 'localhost';
    $db_name = 'pma_c4';
    $db_user = 'pma_c4';
    $db_pass = 'connect4';
    $boardID = htmlspecialchars(trim($_GET['boardID']));
    $sql = mysql_connect($db_host, $db_user, $db_pass) or die(mysql_error());

    mysql_select_db($db_name) or die(mysql_error());

    $json = mysql_query("SELECT jsonFile FROM ajaxBoard where boardID = " . $boardID);

    $returnJson = mysql_fetch_array($json);

    return $returnJson[0];
}

function updateLastMove($lastMove) {

    $db_host = 'localhost';
    $db_name = 'pma_c4';
    $db_user = 'pma_c4';
    $db_pass = 'connect4';
    $boardID = htmlspecialchars(trim($_GET['boardID']));

    $sql = mysql_connect($db_host, $db_user, $db_pass) or die(mysql_error());

    mysql_select_db($db_name) or die(mysql_error());

    mysql_query("UPDATE ajaxBoard SET lastMove='" . $lastMove . "' WHERE boardID = " . $boardID);
}

mysql_close($sql);
?>
