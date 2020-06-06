<html>
<body>
    <?php
        $VAT_client = (integer)$_REQUEST['VAT_client'];
        $name_nurse=(string)$_REQUEST['name_nurse'];
        $VAT_doctor= (integer)$_REQUEST['VAT_doctor'];
        $date_timestamp= $_REQUEST['date_timestamp'];

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
         ############################# NURSE ###############################

            $find_nurse_vat = $connection->prepare("SELECT VAT 
                                                    FROM employee 
                                                    WHERE name=:name_nurse");

            $result=$find_nurse_vat ->execute([
            'name_nurse' => $name_nurse,
            ]);
            if ($result== FALSE)
            {
                echo("Couldn't find nurse");
                $info = $connection->errorInfo();
                echo("<p>Error: {$info[1]}</p>");
                exit();
            }
            $data=$find_nurse_vat->fetchAll();
            foreach($data as $row){
                    $VAT_nurse=$row['VAT'];
            }
         
            $sql2 = "INSERT INTO consultation_assistant VALUES ($VAT_doctor, '$date_timestamp', $VAT_nurse)";
            $nrows = $connection->exec($sql2);
            if($nrows== FALSE)
            {
                echo("<p>Couldn't add nurse info</p>");
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

