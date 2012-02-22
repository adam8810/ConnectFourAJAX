<html>
    <head>
        <title>New User</title>
        <link rel="stylesheet" type="text/css" href="_css/user.css" />

        <script src="../_js/jquery.js" type="text/javascript"></script>

        <script type="text/javascript">
            
            
            $(document).ready(function() {
                
                
                
            });
        </script>

    </head>
    <body>
        <div id="new-user-box">
            <h1>Connect Four</h1>
            <h2>User Registration</h2>
            <form action="../connection.php?task=createUser&redirectTo=created.php" method="POST">
                <table>
                    <tr>
                        <td>
                            <label>Username:</label>
                        </td>
                        <td>
                            <input name="u" type="text"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>First Name:</label>
                        </td>
                        <td>
                            <input name="fn" type="text"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Last Name:</label>
                        </td>
                        <td>
                            <input name="ln" type="text"/>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Password:</label></td>
                        <td>
                            <input type="password"/>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Confirm Password:</label></td>
                        <td>
                            <input name="p" type="password"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Email:</label>
                        </td>
                        <td>
                            <input name="e" type="text"/>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input name="task" value="createUser" type="hidden"/>
                            <input type="submit" value="Submit"/>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </body>
</html>