<html>
<body>
    <?php 
        $VAT_client = (integer)$_REQUEST['VAT_client'];
        $VAT_doctor= (integer)$_REQUEST['VAT_doctor'];
        $date_timestamp= $_REQUEST['date_timestamp']; 
        $ID = (integer)$_REQUEST['ID'];


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

                        ####### FIND CONSULTATION ######
        $find_cons = $connection->prepare("SELECT *
                                            FROM consultation 
                                            WHERE VAT_doctor=:VAT_doctor AND date_timestamp=:date_timestamp");

        $result3=$find_cons ->execute([
            'VAT_doctor' => $VAT_doctor,
            'date_timestamp'=> $date_timestamp,
        ]);
        if ($result3==FALSE)
        {
            echo("Couldn't find consultation.");
            $info = $connection->errorInfo();
            echo("<p>Error: {$info[1]}</p>");
            exit();
        }
        $cons = $find_cons->rowCount();
        if ($cons == 0) 
        {
	    echo("<p>There isn't a consultation associated to this appointment.</p><p> Please create one first by adding SOAP information to the appointment.</p>");
            echo("<form action='add_info_form.php' method='post'>
            <input type=hidden name='VAT_client' value='{$VAT_client}'>
            <input type=hidden name='VAT_doctor' value='{$VAT_doctor}'>
            <input type=hidden name='date_timestamp' value='{$date_timestamp}'>
            <input type='submit' value='Go Back'/></a>
            </form>");        
        }
        else
        {
            $stmt=$connection->prepare("INSERT INTO consultation_diagnostic 
                                        VALUES ($VAT_doctor, '$date_timestamp', $ID)");
            $result=$stmt->execute();
            if ($result == FALSE)
            {
                echo("<p>Couldn't add Diagnostic in consultation</p>");
                $info = $connection->errorInfo();
                echo("<p>Error: {$info[1]}</p>");
                exit();
            }

            header('Location: http://web.tecnico.ulisboa.pt/ist425487/add_info_form.php?VAT_doctor='.$VAT_doctor.'&date_timestamp='.$date_timestamp.'&VAT_client='.$VAT_client);
            die(); 
        }
        $connection = null;
    ?>

</body>
</html>
