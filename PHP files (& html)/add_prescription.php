<html>
    <body>
        <?php
            $VAT_client = (integer)$_REQUEST['VAT_client'];
            $name=(string)$_REQUEST['name'];
            $ID=(integer)$_REQUEST['ID'];
            $dosage=(string)$_REQUEST['dosage'];
            $description=(string)$_REQUEST['description'];
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

 ##################################### PRESCRIPTIONS ######################################### 


            $sql_aux="SELECT lab FROM medication WHERE (name=:name)";
            $result = $connection->prepare($sql_aux);
            $result1=$result->execute([
                'name' => $name       
            ]);
            if ($result1 == FALSE)
            {
                $info = $connection->errorInfo();
                echo("<p>Error1: {$info[2]}</p>");
                exit();
            }
            $n_data = $result->rowCount(); // number of medication
            $data=$result->fetchAll();
            if($n_data>0){
                foreach($data as $row)
                {               
                     $lab = $row['lab']; 
                }    
            }


            $sql_test="SELECT * FROM prescription WHERE (name=:name and lab=:lab and VAT_doctor=:VAT_doctor and date_timestamp=:date_timestamp and ID=:ID)";
            $result = $connection->prepare($sql_test);
            $result1=$result->execute([
                'name' => $name, 
                'lab' => $lab,
                'VAT_doctor' => $VAT_doctor,
                'date_timestamp' => $date_timestamp,
                'ID' => $ID
                ]);
            if ($result1 == FALSE)
            {
                $info = $connection->errorInfo();
                echo("<p>Error2: {$info[1]}</p>");
                exit();
            }
            $n_data = $result->rowCount();
            echo("<p>n_data={$n_data}</p>");
            if($n_data==0){
                $sql="INSERT INTO prescription 
                      VALUES  ('$name', '$lab', $VAT_doctor, '$date_timestamp', $ID, '$dosage', '$description')";
                $result = $connection->exec($sql);
                if($result>0)
                {
                    echo("<p>Prescription included!</p>");
                }
                elseif ($result== FALSE)
                {
                    echo("<p>Couldn't include prescriptons</p>");
                    $info = $connection->errorInfo();
                    echo("<p>Error3 : {$info[1]}</p>");
                    exit();
                }
              }
              header('Location: http://web.tecnico.ulisboa.pt/ist425487/add_info_form.php?VAT_doctor='.$VAT_doctor.'&date_timestamp='.$date_timestamp.'&VAT_client='.$VAT_client);
              die(); 

            $connection = null;
        ?>

    </body>
</html>