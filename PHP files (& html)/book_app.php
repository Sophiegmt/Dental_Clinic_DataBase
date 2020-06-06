<html>
<body>
    <?php
        # FORM
        $date = $_REQUEST['date'];
        $time= $_REQUEST['time'];
        $datetime_new = date('Y-m-d H:i:s', strtotime("$date $time"));
    
        $host = "db.tecnico.ulisboa.pt";
        $description= (string)$_REQUEST['description'];
        $VAT_client= $_REQUEST['VAT_client'];
        $VAT_doctor= $_REQUEST['VAT_doctor'];
        # TRY TO CONNECT WITH DB

        $host = "db.tecnico.ulisboa.pt";
        $user = "ist425487";
        $pass = "vief8835";
        $dsn = "mysql:host=$host;dbname=$user";
        $connection = null;
        try
        {
            $connection = new PDO($dsn, $user, $pass);
        }
        catch(PDOException $exception)
        {
            echo("<p>Error: ");
            echo($exception->getMessage());
            echo("</p>");
            exit();
        }

        $stmt=$connection->prepare("INSERT INTO appointment (VAT_doctor, date_timestamp, description, VAT_client)
                                    VALUES (:VAT_doctor, :datetime_new, :description, :VAT_client)");
        $stmt->bindParam(':VAT_doctor', $VAT_doctor);
        $stmt->bindParam(':datetime_new', $datetime_new);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':VAT_client', $VAT_client);
        $result=$stmt->execute();
        if ($result == FALSE)
        {
            echo("Couldn't book appointment");
        }else{
            echo("Appointment booked");
        }
        echo ("<p></p>");

        echo("<form action='apps_and_cons.php' method='post'>
        <input type='hidden' value='{$VAT_client}' name='VAT_client'>
        <input type='submit' value='Go Back'/>
        </form>");

        # END CONNECTION
        $connection = null;
    ?>
</body>
</html>