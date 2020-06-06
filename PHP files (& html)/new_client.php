<html>
<body>
    <?php
        # FORM
        $VAT_client = $_REQUEST['VAT_client'];
        $client_name = $_REQUEST['client_name'];
        $client_birthdate = $_REQUEST['client_birthdate'];
        $client_street = $_REQUEST['client_street'];
        $client_city = $_REQUEST['client_city'];
        $client_zip = $_REQUEST['client_zip'];
        $client_gender = $_REQUEST['client_gender'];
        $client_phone = $_REQUEST['client_phone'];

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

        $today = date("Y-m-d");//end time
        $client_birthdate=date('Y-m-d', strtotime("$client_birthdate"));
        $diff = abs(strtotime($today) - strtotime($client_birthdate));
        $age = floor($diff / (365*60*60*24));

        $sql_VAT_check= "SELECT * FROM client WHERE VAT_client=:VAT_client";
        $check_vat = $connection->prepare($sql_VAT_check);
        $result=$check_vat ->execute([
            'VAT_client' => $VAT_client,
        ]);
        if ($result== FALSE)
        {
            echo("Add client query.");
            $info = $connection->errorInfo();
            echo("<p>Error: {$info[1]}</p>");
            exit();
        }
        $n_vat=$check_vat->rowCount();
        if($n_vat==0)
        {
            $sql1 = "INSERT INTO client VALUES 
            ($VAT_client, '$client_name', '$client_birthdate', '$client_street', '$client_city', '$client_zip', '$client_gender', $age)";

            $sql2 = "INSERT INTO phone_number_client VALUES ($VAT_client, $client_phone)";

            $nrows = $connection->exec($sql1);
            if($nrows>0)
            {
                echo("<p>$nrows Client Added</p>");
            }
            elseif ($nrows== FALSE)
            {
                echo("<p>Unable to add client.</p>");
                $info = $connection->errorInfo();
                echo("<p>Error: {$info[1]}</p>");
                exit();
            }

            $nrows = $connection->exec($sql2);
            if($nrows>0)
            {
                echo("<p>$nrows Phone Number Added</p>");
            }
            elseif ($nrows== FALSE)
            {
                echo("<p>Unable to add client phone number.</p>");
                $info = $connection->errorInfo();
                echo("<p>Error: {$info[1]}</p>");
                exit();
            }
        }else{
            echo("<p>VAT for client already used</p>");
            $sql2 = "INSERT INTO phone_number_client VALUES ($VAT_client, $client_phone)";
            $nrows = $connection->exec($sql2);
            if($nrows>0)
            {
                echo("<p>$nrows Phone Number Added</p>");
            }
            elseif ($nrows== FALSE)
            {
                echo("<p>Unable to add client phone number.</p>");
                $info = $connection->errorInfo();
                echo("<p>Error: {$info[1]}</p>");
                exit();
            }
        }
        echo("<form action='initial_page.php' method='post'>
        <input type='submit' value='Go To Homepage'/>
        </form>");

          $connection = null;
        ?>

</body>
</html>
