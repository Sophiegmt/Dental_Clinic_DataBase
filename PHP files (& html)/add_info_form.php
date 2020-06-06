<html>
<body>
    <h1>Information:</h1>
    <h3>Please make sure to fill the SOAP criteria first:</h3>
<!-- ##################################### SOAP ######################################### -->

<h2>SOAP:</h2>
        <?php

            $VAT_client = (integer)$_REQUEST['VAT_client'];
            $VAT_doctor=(integer)$_REQUEST['VAT_doctor'];
            $date_timestamp=(string)$_REQUEST['date_timestamp'];
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
            
            // VER SE JA EXISTEM SOAP NOTES. SE SIM, PUBLICAR TABELA
            $sql1= "SELECT  s, o, a, p FROM consultation INNER JOIN appointment USING (VAT_doctor, date_timestamp) WHERE (VAT_doctor=:VAT_doctor AND date_timestamp=:date_timestamp)";
            $result = $connection->prepare($sql1);
            $result1=$result->execute([
                'VAT_doctor' => $VAT_doctor,
                'date_timestamp' => $date_timestamp
                ]);

            if ($result1 == FALSE)
            {
                $info = $connection->errorInfo();
                echo("<p>Error: {$info[2]}</p>");
            }
            $n_data = $result->rowCount(); // number of nurses
            $data=$result->fetchAll();
            if($n_data>0){

                $isConsult="consultation";
                echo("<table border=\"1\" style=\"margin-bottom: 20px;\"> ");
                echo("<tr>");
                echo("<td>Subjective Observation:</td>");
                echo("<td>Objective Observation:</td>");
                echo("<td>Assessment:</td>");
                echo("<td>Plan:</td>");
                echo("</tr>\n");
               {
                    foreach($data as $row)
                    {
                        echo("<tr>");
                        echo("<td>{$row['s']}</td>");
                        echo("<td>{$row['o']}</td>");
                        echo("<td>{$row['a']}</td>");
                        echo("<td>{$row['p']}</td>");
                        echo("</tr>\n");
                    }
                }
                echo("</table>");
            }else{
                $today = date("Y-m-d H:i:s");
                if ($date_timestamp < $today) {$isConsult="missed";}
                else{$isConsult="booked";}
                
                echo("<form action='add_soap.php' method='post'>");
                echo("<p>Subjective Observation:");
                echo("<input type='text' maxlength='255' name='sub' required/>");
                echo("</p>");
                echo("<p>Objective Observation:");
                echo("<input type='text' name='obj' maxlength='255' required/>");
                echo("</p>");
                echo("<p>Assessment:");
                echo("<input type='text' name='assess' maxlength='255' required/>");
                echo("</p>");
                echo("<p>Plan:");
                echo("<input type='text' name='plan' maxlength='255' required/>");
                echo("</p>");
                echo("<input type=hidden name='VAT_client' value='{$VAT_client}'>");
                echo("<input type='hidden' value='{$VAT_doctor}' name='VAT_doctor' />");
                echo("<input type='hidden' value='{$date_timestamp}' name='date_timestamp'/>");
                echo("<input type='submit' value='Add SOAP'>");
                echo("</form>");
            }
            $connection=null;
        ?>

 <!-- ##################################### NURSE ######################################### -->
        <h2>Assistant Nurse(s) (Optional):</h2>
        <?php
            $VAT_client = (integer)$_REQUEST['VAT_client'];
            $VAT_doctor=(integer)$_REQUEST['VAT_doctor'];
            $date_timestamp=(string)$_REQUEST['date_timestamp'];
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

            // VER SE JA EXISTEM NURSES. SE SIM, PUBLICAR TABELA
            $sql1= "SELECT DISTINCT name,VAT FROM employee INNER JOIN nurse USING (VAT) WHERE VAT in (SELECT VAT_nurse FROM consultation_assistant WHERE (VAT_doctor=:VAT_doctor AND date_timestamp=:date_timestamp)) ORDER BY name ASC";
            $result = $connection->prepare($sql1);
            $result1=$result->execute([
                'VAT_doctor' => $VAT_doctor,
                'date_timestamp' => $date_timestamp
                ]);

            if ($result1 == FALSE)
            {
                $info = $connection->errorInfo();
                echo("<p>Error: {$info[2]}</p>");

            }

            $n_data = $result->rowCount(); // number of nurses
            $data=$result->fetchAll();
            if($n_data>0){
                echo("<table border=\"1\" style=\"margin-bottom: 20px;\"> ");
                echo("<tr>");
                echo("<td>Nurse's Name</td>");
                echo("<td>Nurse's VAT</td>");
                echo("</tr>\n");
               {
                    foreach($data as $row)
                    {
                        echo("<tr>");
                        echo("<td>{$row['name']}</td>");
                        echo("<td>{$row['VAT']}</td>");
                        echo("</tr>\n");
                    }
                }
                echo("</table>");
            }
            // SELECIONAR MAIS NURSES


            echo("<form action='add_nurse.php' method='post'>");
            echo("<p>Nurse Name:&nbsp&nbsp&nbsp&nbsp&nbsp");
            echo("<select name='name_nurse'>");
                $sql2= "SELECT DISTINCT name FROM employee INNER JOIN nurse USING (VAT) WHERE VAT not in (SELECT VAT_nurse FROM consultation_assistant WHERE (VAT_doctor=:VAT_doctor AND date_timestamp=:date_timestamp)) ORDER BY name ASC";
                $result = $connection->prepare($sql2);
                $result1=$result->execute([
                    'VAT_doctor' => $VAT_doctor,
                    'date_timestamp' => $date_timestamp
                    ]);
                if ($result1 == FALSE)
                {
                    $info = $connection->errorInfo();
                    echo("<p>Error: {$info[2]}</p>");
                    exit();
                }
                $data=$result->fetchAll();
                foreach($data as $row)
                {
                    $name = $row['name'];
                    echo("<option value='$name'>$name</option>");
                }
            echo("</select>&nbsp&nbsp&nbsp");
            echo("<input type=hidden name='VAT_client' value='{$VAT_client}'>");
            echo("<input type='hidden' value='{$VAT_doctor}' name='VAT_doctor' />");
            echo("<input type='hidden' value='{$date_timestamp}' name='date_timestamp'/>");
            $n_data = $result->rowCount(); // number of nurses
            if($n_data>0){
                echo("<input type='submit' value='Add nurse'>");
            }
            echo("</form>");
            echo("</p>");
            $connection=null;
        ?>


 <!-- ##################################### DIAGNOSIS ######################################### -->


        <h2>Diagnosis (Optional):</h2>
        <?php
                $VAT_client = (integer)$_REQUEST['VAT_client'];
                $VAT_doctor=(integer)$_REQUEST['VAT_doctor'];
                $date_timestamp=(string)$_REQUEST['date_timestamp'];
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
                $sql1="SELECT DISTINCT ID FROM consultation_diagnostic WHERE (VAT_doctor=:VAT_doctor AND date_timestamp=:date_timestamp) ORDER BY ID ASC";
                $result = $connection->prepare($sql1);
                $result1=$result->execute([
                    'VAT_doctor' => $VAT_doctor,
                    'date_timestamp' => $date_timestamp
                ]);
                if ($result1 == FALSE)
                {
                    $info = $connection->errorInfo();
                    echo("<p>Error: {$info[2]}</p>");
                }
                $n_data = $result->rowCount(); // number of nurses
                $data=$result->fetchAll();
                if($n_data>0){
                    echo("<table border=\"1\" style=\"margin-bottom: 20px;\"> ");
                    echo("<tr>");
                    echo("<td>Diagnostic Code</td>");
                    echo("</tr>\n");
                {
                        foreach($data as $row)
                        {
                            echo("<tr>");
                            echo("<td>{$row['ID']}</td>");
                            echo("</tr>\n");
                        }
                    }
                    echo("</table>");
                }
                // SELECIONAR MAIS DIAGNOSTICOS
                echo("<form action='add_diagnostic.php' method='post'>");
                echo("<p>Diagnostic Code:&nbsp&nbsp&nbsp&nbsp&nbsp");
                echo("<select name='ID'>");
                    $sql2= "SELECT DISTINCT ID FROM diagnostic_code WHERE (ID) not in (SELECT DISTINCT ID FROM consultation_diagnostic WHERE (VAT_doctor=:VAT_doctor AND date_timestamp=:date_timestamp)) ORDER BY ID ASC";
                    $result = $connection->prepare($sql2);
                    $result1=$result->execute([
                        'VAT_doctor' => $VAT_doctor,
                        'date_timestamp' => $date_timestamp
                        ]);
                    if ($result1 == FALSE)
                    {
                        $info = $connection->errorInfo();
                        echo("<p>Error: {$info[2]}</p>");
                        exit();
                    }
                    $data=$result->fetchAll();
                    foreach($data as $row)
                    {
                        $id = $row['ID'];
                        echo("<option value=\"$id\">$id</option>");
                    }
                echo("</select>&nbsp&nbsp&nbsp");
                $n_data = $result->rowCount(); // number of nurses
                if($n_data>0){
                    echo("<input type='submit' value='Add Diagnostic Code'>");
                }
                echo("<input type=hidden name='VAT_client' value='{$VAT_client}'>");
                echo("<input type='hidden' value='{$VAT_doctor}' name='VAT_doctor' />");
                echo("<input type='hidden' value='{$date_timestamp}' name='date_timestamp'/>");
                echo("</form>");
                echo("</p>");
                $connection=null;
            ?>

 <!-- ##################################### PRESCRIPTIONS ######################################### -->

 <h2>Prescription(s) (Optional):</h2>
        <?php

            $VAT_client = (integer)$_REQUEST['VAT_client'];
            $VAT_doctor=(integer)$_REQUEST['VAT_doctor'];
            $date_timestamp=(string)$_REQUEST['date_timestamp'];

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


            $sql1="SELECT DISTINCT ID, name, lab ,dosage, description FROM prescription 
                   WHERE (VAT_doctor=:VAT_doctor AND date_timestamp=:date_timestamp) ORDER BY ID ASC ";
            $result = $connection->prepare($sql1);
            $result1=$result->execute([
                'VAT_doctor' => $VAT_doctor,
                'date_timestamp' => $date_timestamp
            ]);
            if ($result1 == FALSE)
            {
                $info = $connection->errorInfo();
                echo("<p>Error: {$info[2]}</p>");
            }
            $n_data = $result->rowCount(); // number of prescriptions
            $data=$result->fetchAll();
            if($n_data>0){
                echo("<table border=\"1\" style=\"margin-bottom: 20px;\">");
                echo("<tr>");
                echo("<td>Prescription ID</td>");
                echo("<td>Prescription names</td>");
                echo("<td>Prescription lab</td>");
                echo("<td>Prescription dosage</td>");
                echo("<td>Prescription description</td>");
                echo("</tr>\n");
                {
                    foreach($data as $row)
                    {
                        echo("<tr>");
                        echo("<td>{$row['ID']}</td>");
                        echo("<td>{$row['name']}</td>");
                        echo("<td>{$row['lab']}</td>");
                        echo("<td>{$row['dosage']}</td>");
                        echo("<td>{$row['description']}</td>");
                        echo("</tr>\n");
                    }
                }
                echo("</table>");
            }

            $sqltr ="SELECT DISTINCT (ID) FROM consultation_diagnostic WHERE (VAT_doctor=:VAT_doctor AND date_timestamp=:date_timestamp)";
            $resulttr = $connection->prepare($sqltr);
            $result1tr=$resulttr->execute([
                'VAT_doctor' => $VAT_doctor,
                'date_timestamp' => $date_timestamp
            ]);
            if ($result1tr == FALSE)
            {
                $info = $connection->errorInfo();
                echo("<p>Error: {$info[2]}</p>");
                exit();
            }
            $n_datatr = $resulttr->rowCount(); // number of medication
            $data_trt = $resulttr->fetchAll();
            if($n_datatr>0)
            {
            foreach($data_trt as $roww)
            {
              echo("<form action='add_prescription.php' method='post'>");

                $sql2= "SELECT DISTINCT  medication.name as name, medication.lab as lab FROM medication ORDER BY name ASC";
                $result = $connection->prepare($sql2);
                $result1=$result->execute([
                    'VAT_doctor' => $VAT_doctor,
                    'date_timestamp' => $date_timestamp
                ]);
                if ($result1 == FALSE)
                {
                    $info = $connection->errorInfo();
                    echo("<p>Error: {$info[2]}</p>");
                    exit();
                }
                $n_data = $result->rowCount(); // number of medication
                $data=$result->fetchAll();
                if($n_data>0){
                    echo("<p>Prescription for ID $roww[0]:&nbsp&nbsp&nbsp&nbsp&nbsp");
                    echo("<p>Name of medication:&nbsp&nbsp&nbsp&nbsp");
                    $ID=$roww[0];
                    echo("<select name='name'>");
                    foreach($data as $row)
                    {
                        $name = $row['name'];
                        $lab = $row['lab'];
                        echo("<option value=\"$name\">$name</option>");
                    }
                    echo("</select>&nbsp&nbsp&nbsp");
                    echo("Dosage:");
                    echo("<input type='text' name='dosage' maxlength='255'/>");
                    echo("&nbsp&nbsp&nbsp&nbsp&nbsp"); 
                    echo("Description:");
                    echo("<input type='text' name='description' maxlength='255'/>");
                    echo("&nbsp&nbsp&nbsp&nbsp&nbsp");
                    if($n_data>0){
                        echo("<input type='submit' value='Add Prescription'>");
                    }
                }
                echo("<input type='hidden' value='{$ID}' name='ID' />");
                echo("<input type=hidden name='VAT_client' value='{$VAT_client}'>");
                echo("<input type='hidden' value='{$VAT_doctor}' name='VAT_doctor' />");
                echo("<input type='hidden' value='{$date_timestamp}' name='date_timestamp'/>");
                
                echo("</form>");
                echo("</p>");
              }
              
            }
            else
            {
                echo("Add a Diagnosis First");
            }
            // SELECIONAR MAIS PRESCRIPTIONS

            $connection=null;
        echo("<p></p>");
        echo("<form action='list_cons_info.php' method='post'>
            <input type='hidden' value='{$VAT_client}' name='VAT_client'>
            <input type='hidden' value='{$isConsult}' name='isConsult'>
            <input type='hidden' value='{$VAT_doctor}' name='VAT_doctor' />
            <input type='hidden' value='{$date_timestamp}' name='date_timestamp'/>
            <input type='submit' value='Done'/>
            </form>");
          ?>
        
        <p></p>
        <form action="initial_page.php" method="post">
        <input type="submit" value="Go to Homepage"/>
        </form>
</body>
</html>
