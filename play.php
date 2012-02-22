<html>
    <head>
        <title>PHP Site</title>
    </head>

    <?php
    include('connection.php');

    $qUser = mysql_query("select * from user");
    $qInstance = mysql_query("select * from instance");
    
    
    ?>


    <body>

    <center>
        <h1>Connect Four</h1>

        <form action="<?php $PHP_SELF; ?>" method="post">
            <input type="submit" value="Drop" name="btnSubmit">
            <input type="radio" name="column" value="Reset">Restart?
            <br/>
            <br/>
            <br/>
            &nbsp&nbsp
            <input type="radio" name="column" value="One">&nbsp&nbsp&nbsp
            <input type="radio" name="column" value="Two">&nbsp&nbsp&nbsp
            <input type="radio" name="column" value="Three">&nbsp&nbsp&nbsp
            <input type="radio" name="column" value="Four">&nbsp&nbsp&nbsp
            <input type="radio" name="column" value="Five">&nbsp&nbsp&nbsp
            <input type="radio" name="column" value="Six">&nbsp&nbsp
            <input type="radio" name="column" value="Seven">&nbsp&nbsp&nbsp
            <!--<input type="button" value="Reload Page" onClick="window.location.reload()">-->

        </form>

    </center>


    <?php
    // Global Variables
    $gCurrentPlayer = "R";
    $gDisplayMsg;
    $gGameState;
    $gMoveCounter;
    $gWin;

    $gMemFile;

    $gGameBoard = array(array(0, 1, 2, 3, 4, 5),
        array(0, 1, 2, 3, 4, 5, 6));

    // Gets data from file, puts in $gMemFile
    $gMemFile = loadDataFromMemory();

    //echo "memFile = " . $gMemFile . "<br/>";
    clear();

    //echo "<br/> FS= " . strlen($gMemFile) . "<br/>";

    for ($i = 0; $i < strlen($gMemFile); $i++) {
        move($gMemFile{$i});
    }

    unset($formInputCol);

    // Get form input, store in local variable
    $formInputCol = $_POST["column"];


    if ($formInputCol == "One") {
        $formInputCol = 1;
    } else if ($formInputCol == "Two") {
        $formInputCol = 2;
    } else if ($formInputCol == "Three") {
        $formInputCol = 3;
    } else if ($formInputCol == "Four") {
        $formInputCol = 4;
    } else if ($formInputCol == "Five") {
        $formInputCol = 5;
    } else if ($formInputCol == "Six") {
        $formInputCol = 6;
    } else if ($formInputCol == "Seven") {
        $formInputCol = 7;
    } else if ($formInputCol == "Reset") {
        eraseMemory();
        unset($formInputCol);
    }

    if (isset($formInputCol)) {
        //echo "isset - " . $formInputCol . "<br/>";
        writeDataToMemory($formInputCol);
    } else {
        //echo "not set<br/>";
    }

    display();

    //simHorRightWin();
    //simHorLeftWin();
    //simVertWin();
    //simHor2Win();

    function eraseMemory() {
        //echo "mem erased";
        $memFile = "memory.txt";

        $memFile = fopen($memFile, 'w') or die("can't open file");


        fwrite($memFile, "");

        fclose($memFile);

        //unlink($memFile);
    }

    function loadDataFromMemory() {
        
        //TODO: gameboardID is fixed atm
    $gameboardString = "select * from gameboard where gameboardID = " . $_GET['id'];
    
    $qGameboard = mysql_query($gameboardString) or die(mysql_error());
        // Using mySQL populate the data from database
    
       while ($rows = mysql_fetch_array($qGameboard))
       {
           $val = 0;
           for($i = 0; $i < 42; $i++)
           {
               $val++;
               echo $rows[$val] . '<br/>';
           }
       }
        
        
        
        // text file crap
        $memFile = "memory.txt";
        $memFile = fopen($memFile, 'r');

        $storedVal = fread($memFile, strlen($memFile));

        fclose($memFile);
        return $storedVal;
    }

    function writeDataToMemory($inputToMemory) {
        
        // mySQL
        $qGameboardUpdate = mysql_query("UPDATE gameboard SET");
        
        
        // Text file crap
        $memFile = "memory.txt";

        $memFile = fopen($memFile, 'a+') or die("can't open file");


        fwrite($memFile, $inputToMemory);

        fclose($memFile);
    }

    function simHorRightWin() {
        move(5);
        move(5);
        move(4);
        move(4);
        move(3);
        move(3);
        move(2);
        move(1);
    }

    function simHorLeftWin() {
        move(1);
        move(1);
        move(2);
        move(2);
        move(3);
        move(3);
        move(4);
        move(4);
    }

    function simVertWin() {
        move(5);
        move(3);
        move(5);
        move(3);
        move(5);
        move(3);
        move(5);
        move(3);
    }

    function simHor2Win() {
        move(2);
        move(2);
        move(1);
        move(1);
        move(4);
        move(4);
        move(3);
        move(3);
    }

// ====================================
// Reset all spaces back to default "O"
// ====================================
    function clear() {
        global $gGameBoard;
        global $gCurrentPlayer;
        global $gGameState;
        global $gMoveCounter;
        global $gDisplayMsg; // not needed? should be done through setDisplayMsg()?
        global $gWin;

        // Reset game state back to none
        $gGameState = "NONE";

        // Reset Current player back to red
        $gCurrentPlayer = "R";

        // Reset Move Counter back to zero
        $gMoveCounter = 0;

        // Reset Display Msg back to default
        setDisplayMsg(" Game On!");

        // Reset $gWin to false
        $gWin = false;

        // Reset row to "O"
        for ($row = 0; $row < 6; $row++) {
            for ($col = 0; $col < 7; $col++) {
                $gGameBoard[$row][$col] = "O";
            }
        }

        // Call display()
        //display();	
    }

// =============================================================
// ChangePlayerState
// =============================================================
    function changeGameState() {
        global $gCurrentPlayer;
        global $gGameState;

        if ($gCurrentPlayer == "B")
            $gGameState = "BLACK";
        else if ($gCurrentPlayer == "R")
            $gGameState = "RED";
    }

// ====================================
// Display
// ====================================
    function display() {
        global $gGameBoard;
        global $gGameState;
        global $gCurrentPlayer;

        print "<center>";
        showDisplayMsg();
        //print "<pre>1   2   3   4   5   6   7</pre>";
        print "<pre>";
        for ($row = 0; $row < 6; $row++) {

            for ($col = 0; $col < 7; $col++) {
                print " | " . $gGameBoard[$row][$col];
            }
            print " | ";

            print $gRow[$row];
            print "<br/>";
        }
        print "</pre>";
        if ($gGameState != "NONE") {
            printf("%s WINS!!!<br/>", $gGameState);

            if ($gGameState == "RED") {
                print "<body style='background-color:red'>";
            } else {
                print "<body style='background-color:grey'>";
            }
        } else {
            if ($gCurrentPlayer == "R")
                print "Red's Turn<br/>";
            else
                print "Black's Turn<br/>";
        }
    }

// ====================================
// Display MSG Logic Function
// ====================================
    function showDisplayMsg() {
        $length = strlen(getDisplayMsg()) - 1;


        //print "========================<br/>";
        //printf("%s<br/>", getDisplayMsg() );
        //print "========================<br/>";
    }

// ====================================
// Display MSG GET
// ====================================
    function getDisplayMsg() {
        global $gDisplayMsg;

        return $gDisplayMsg;
    }

// ====================================
// Display MSG SET
// ====================================
    function setDisplayMsg($inMsg) {
        global $gDisplayMsg;

        $gDisplayMsg = $inMsg;
    }

// ##
// ====================================
// Switch Current Player
// ====================================
    function switchCurrentPlayer() {
        global $gCurrentPlayer;

        if ($gCurrentPlayer == "B") {
            $gCurrentPlayer = "R";
        } else {
            $gCurrentPlayer = "B";
        }
    }

// ====================================
// Move
// ====================================

    function move($column) {
        global $gGameState;
        global $gGameBoard;
        global $gMoveCounter;
        global $gCurrentPlayer;


        if ($gGameState == "NONE") {
            //Only accept 1-7 as inputs
            if (($column >= 0) && ($column <= 7)) {
                $row = 5;
                while ($row >= 0) {
                    //Check to see if the column has any empty spots
                    if ($gGameBoard[$row][$column - 1] == "O") {
                        $gMoveCounter++;

                        //If all spaces are taken, set state to DRAW
                        if ($gMoveCounter >= 42)
                            $gGameState = "DRAW";

                        //Reset game message to Game On
                        setDisplayMsg(" Game On");

                        //If it has an empty spot, put the
                        //  currentPlayer's piece in that spot
                        $gGameBoard[$row][$column - 1] = $gCurrentPlayer;

                        //Check if the currentPlayer won
                        CheckForWin($row, $column - 1);

                        //Switch currentPlayer
                        switchCurrentPlayer();

                        // display();
                        //display();

                        return true;
                    }

                    $row--;
                }

                //No room was left in the column specified
                return false;
            }
            else {
                //Column number was out of range
                return false;
            }
        }
        else
            return false;
    }

// =============================================================
// CheckForWin
// =============================================================
    function CheckForWin($inRow, $inColumn) {
        global $gWin;
        global $gCurrentPlayer;

        $gWin = false;

        //Check for different $gWinning combinations
        if (CheckForWinRightHorizontal($inRow, $inColumn, $gWin) + CheckForWinLeftHorizontal($inRow, $inColumn, $gWin) + 1 >= 4) {
            //printf("%d + %d",CheckForWinRightHorizontal($inRow, $inColumn, $gWin),CheckForWinLeftHorizontal($inRow, $inColumn, $gWin));
            setDisplayMsg(" Horizontal Win!");

            $gWin = true;

            //change the last entered piece to lower
            $gGameBoard[$inRow][$inColumn] = $gCurrentPlayer;

            //call the checks again, this time setting the $gWinning combination to lower
            CheckForWinRightHorizontal($inRow, $inColumn, $gWin);
            CheckForWinLeftHorizontal($inRow, $inColumn, $gWin);

            changeGameState();
            return true;
        } else if (CheckForWinVertical($inRow, $inColumn, $gWin)) {
            setDisplayMsg(" Vertical Win!");

            $gWin = true;

            //change the last entered piece to lower
            $gGameBoard[$inRow][$inColumn] = $gCurrentPlayer;

            //call the check again, this time setting the $gWinning combination to lower
            CheckForWinVertical($inRow, $inColumn, $gWin);
            display();

            changeGameState();
            return true;
        } else if (CheckForWinRightDiagonalUp(inRow, inColumn, $gWin) + CheckForWinRightDiagonalDown(inRow, inColumn, $gWin) + 1 >= 4) {
            setDisplayMsg("Diagonal $gWin!");

            $gWin = true;

            //change the last entered piece to lower
            $gGameBoard[$inRow][$inColumn] = $gCurrentPlayer;

            //call the checks again, this time setting the $gWinning combination to lower
            CheckForWinRightDiagonalUp($inRow, $inColumn, $gWin);
            CheckForWinRightDiagonalDown($inRow, $inColumn, $gWin);

            changeGameState();
            return true;
        } else if (CheckForWinLeftDiagonalUp($inRow, $inColumn, $gWin) + CheckForWinLeftDiagonalDown($inRow, $inColumn, $gWin) + 1 >= 4) {
            setDisplayMsg("Diagonal $gWin!");

            $gWin = true;

            //change the last entered piece to lower
            $gGameBoard[$inRow][$inColumn] = $gCurrentPlayer;

            //call the checks again, this time setting the $gWinning combination to lower
            CheckForWinLeftDiagonalUp($inRow, $inColumn, $gWin);
            CheckForWinLeftDiagonalDown($inRow, $inColumn, $gWin);
            display();

            changeGameState();
            return true;
        }
        else
            return false;
    }

// =============================================================
// CheckForWinVertical
// =============================================================
    function CheckForWinVertical($inRow, $inColumn, $gWin) {
        global $gWin;
        global $gGameBoard;
        global $gCurrentPlayer;

        $pieceCounter = 0;

        //Check down
        $i = 0;

        while ($i < 4 && $inRow < 3) {
            //Make sure the next position down matches currentPlayer
            if ($gGameBoard[$inRow + $i][$inColumn] == $gCurrentPlayer) {
                if ($gWin) {
                    //set current offset position to lower
                    $gGameBoard[$inRow + $i][$inColumn] = $gCurrentPlayer;
                }

                $pieceCounter++;

                if ($pieceCounter >= 4) {
                    return true;
                }
            }
            $i++;
        }

        return false;
    }

// =============================================================
// CheckForWinRightHorizontal
// =============================================================
    function CheckForWinRightHorizontal($inRow, $inColumn, $gWin) {
        global $gWin;
        global $gGameBoard;
        global $gCurrentPlayer;

        $pieceCounter = 0;

        //Check to the right
        $i = 1;

        while ($i < 5) {
            //Make sure not to check out of bounds
            if (($inColumn + ($i + 1)) >= 0 && ($inColumn + ($i + 1) <= 7)) {
                //Check to the right of the current piece to see if
                //  it is from the same player
                if ($gGameBoard[$inRow][$inColumn + $i] == $gCurrentPlayer) {
                    if ($gWin) {
                        $gGameBoard[$inRow][$inColumn + $i] = $gCurrentPlayer;
                    }

                    $pieceCounter++;
                }
                else
                    return $pieceCounter;
            }
            $i++;
        }

        return $pieceCounter;
    }

// =============================================================
// CheckForWinRightDiagonalUp
// =============================================================
    function CheckForWinRightDiagonalUp($inRow, $inColumn, $gWin) {
        global $gWin;
        global $gGameBoard;
        global $gCurrentPlayer;

        $pieceCounter = 0;


        //  Check down and left
        $i = 1;

        while ($i < 5) {
            //  Make sure not to check out of bounds
            if ((($inRow + $i) <= 5) && (($inColumn - $i) >= 0)) {
                //  Check down and left of the current piece to see if
                //  it is from the same player
                if ($gGameBoard[$inRow + $i][$inColumn - $i] == $gCurrentPlayer) {
                    if ($gWin) {
                        $gGameBoard[$inRow + $i][$inColumn - $i] = $gCurrentPlayer;
                    }

                    $pieceCounter++;
                }
                else
                    return $pieceCounter;
            }
            $i++;
        }

        return $pieceCounter;
    }

// =============================================================
// CheckForWinRightDiagonalDown
// =============================================================
    function CheckForWinRightDiagonalDown($inRow, $inColumn, $gWin) {
        global $gWin;
        global $gGameBoard;
        global $gCurrentPlayer;

        $pieceCounter = 0;


        //  Check up and right
        $i = 1;

        while ($i < 5) {
            //  Make sure not to check out of bounds
            if ((($inRow - $i) >= 0) && (($inColumn + $i) <= 6)) {
                //  Check up and right of the current piece to see if
                //  it is from the same player
                if ($gGameBoard[$inRow - $i][$inColumn + $i] == $gCurrentPlayer) {
                    if ($gWin) {
                        $gGameBoard[$inRow - $i][$inColumn + $i] = $gCurrentPlayer;
                    }

                    $pieceCounter++;
                }
                else
                    return $pieceCounter;
            }

            $i++;
        }

        return $pieceCounter;
    }

// =============================================================
// CheckForWinLeftHorizontal
// =============================================================
    function CheckForWinLeftHorizontal($inRow, $inColumn, $gWin) {
        global $gWin;
        global $gGameBoard;
        global $gCurrentPlayer;

        $pieceCounter = 0;


        //Check to the left
        $i = 1;

        while ($i < 5) {
            //Make sure not to check out of bounds
            if (($inColumn - $i >= 0) && ($inColumn - $i <= 6)) {
                //Check to the left of the current piece to see if
                //  it is from the same player
                if ($gGameBoard[$inRow][$inColumn - $i] == $gCurrentPlayer) {
                    if ($gWin) {
                        $gGameBoard[$inRow][$inColumn - $i] = $gCurrentPlayer;
                    }

                    $pieceCounter++;
                }
                else
                    return $pieceCounter;
            }
            $i++;
        }

        return $pieceCounter;
    }

// =============================================================
// CheckForWinLeftDiagonalDown
// =============================================================
    function CheckForWinLeftDiagonalDown($inRow, $inColumn, $gWin) {
        global $gWin;
        global $gGameBoard;
        global $gCurrentPlayer;

        $pieceCounter = 0;

        //  Check right and down
        $i = 1;

        while ($i < 5) {
            //  Make sure not to check out of bounds
            if ((($inRow + $i) <= 5) && (($inColumn + $i) <= 6)) {
                //  Check down and right of the current piece to see if
                //  it is from the same player
                if ($gGameBoard[$inRow + $i][$inColumn + $i] == $gCurrentPlayer) {
                    if ($gWin) {
                        $gGameBoard[$inRow + $i][$inColumn + $i] = $gCurrentPlayer;
                    }

                    $pieceCounter++;
                }
                else
                    return $pieceCounter;
            }
            $i++;
        }

        return $pieceCounter;
    }

// =============================================================
// CheckForWinLeftDiagonalUp
// =============================================================
    function CheckForWinLeftDiagonalUp($inRow, $inColumn, $gWin) {
        global $gWin;
        global $gGameBoard;
        global $gCurrentPlayer;

        $pieceCounter = 0;

        //  Check up and left
        $i = 1;

        while ($i < 5) {
            //  Make sure not to check out of bounds
            if ((($inRow - $i) >= 0) && (($inColumn - $i) >= 0)) {
                //  Check up and left of the current piece to see if
                //  it is from the same player
                if ($gGameBoard[$inRow - $i][$inColumn - $i] == $gCurrentPlayer) {
                    if ($gWin) {
                        $gGameBoard[$inRow - $i][$inColumn - $i] = $gCurrentPlayer;
                    }

                    $pieceCounter++;
                }
                else
                    return $pieceCounter;
            }
            $i++;
        }

        return $pieceCounter;
    }
    ?>
</body>
</html>