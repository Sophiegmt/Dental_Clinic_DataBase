<html>
<body>
    <?php
        # FORM
        header("refresh: 60;");
        $date = strtotime($_REQUEST['date_timestamp']);
        $date_timestamp=date('Y-m-d H:i:s', $date);

        $VAT_doctor = (integer)$_REQUEST['VAT_doctor'];
        $isConsult =(string)$_REQUEST['isConsult'];
        $VAT_client = (integer)$_REQUEST['VAT_client'];

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

        if($isConsult=="consultation"){
            echo("<h1>Consultation Information:</h1>");

            $info_cons = $connection->prepare(" SELECT *
                                                FROM appointment
                                                INNER JOIN consultation as c
                                                USING (VAT_doctor,date_timestamp)
                                                INNER JOIN employee as e
                                                ON (e.VAT=c.VAT_doctor)
                                                WHERE (c.VAT_doctor = :VAT_doctor AND c.date_timestamp= :dates)");

            $result=$info_cons->execute([
                'VAT_doctor' => $VAT_doctor,
                'dates' => $date_timestamp
                ]);
        
            $n_data = $info_cons->rowCount();
            $data=$info_cons->fetchAll();

            
            if($n_data>0)
            { 

                echo("<table border=\"1\" style=\"margin-bottom: 20px;\"> ");
                echo("<tr>");
                echo("<td>VAT_client</td>");
                echo("<td>Consultation Date and Time</td>");
                echo("<td>Doctor's Name</td>");  
                echo("<td>Doctor's VAT</td>"); 
                echo("<td>Appointment Description</td>");
                echo("</tr>\n");
                {
                    foreach($data as $row){
                        echo("<tr>");
                        echo("<td>{$row['VAT_client']}</td>");
                        echo("<td>{$row['date_timestamp']}</td>");
                        echo("<td>{$row['name']}</td>");     
                        echo("<td>{$row['VAT_doctor']}</td>");              
                        echo("<td>{$row['description']}</td>");              
                        echo("</tr>\n");
                    }
                }echo("</table>");


                $info_ass = $connection->prepare(" SELECT *
                                                    FROM appointment
                                                    INNER JOIN consultation as c
                                                    USING (VAT_doctor,date_timestamp)
                                                    INNER JOIN (SELECT *
                                                                FROM  consultation_assistant as ass
                                                                INNER JOIN employee as e
                                                                ON (e.VAT=ass.VAT_nurse) 
                                                                ) AS nurse
                                                    USING (VAT_doctor,date_timestamp)
                                                    WHERE (c.VAT_doctor = :VAT_doctor AND c.date_timestamp= :dates)");

                $result=$info_ass->execute([
                'VAT_doctor' => $VAT_doctor,
                'dates' => $date_timestamp
                ]);
                $n_ass = $info_ass->rowCount();
                $ass=$info_ass->fetchAll();
                if($n_ass>0){
                    echo("</table>");
                    echo("<table border=\"1\" style=\"margin-bottom: 20px;\"> ");
                    echo("<tr>");
                    echo("<h2>Nurse</h2>");
                    echo("<td>Nurse name</td>");
                    echo("<td>Nurse VAT</td>");
                    {
                        foreach($ass as $row){
                            echo("<tr>");
                            echo("<td>{$row['VAT_nurse']}</td>");
                            echo("<td>{$row['name']}</td>");                
                            echo("</tr>\n");

                        }
                    } echo("</table>");
                }else{
                    echo("<td>No nurse present</td>");
                }

                echo("<table border=\"1\" style=\"margin-bottom: 20px;\"> ");
                echo("<tr>");
                echo("<h2>SOAP:</h2>");
                echo("<td>Subjective Observation</td>");
                echo("<td>Objective Observation</td>");
                echo("<td>Assessment</td>");
                echo("<td>Plan</td>");
                echo("</tr>\n");
                {
                    foreach($data as $row){
                        echo("<tr>");
                        echo("<td>{$row['s']}</td>");
                        echo("<td>{$row['o']}</td>");
                        echo("<td>{$row['a']}</td>");
                        echo("<td>{$row['p']}</td>");                    
                        echo("</tr>\n");
                    }
                }echo("</table>");
            
                $info_diag = $connection->prepare(" SELECT diag.ID, diag.description
                                                    FROM appointment as a
                                                    INNER JOIN consultation as c
                                                    USING (VAT_doctor ,date_timestamp) 
                                                    INNER JOIN (SELECT * FROM consultation_diagnostic
                                                                         INNER JOIN diagnostic_code
                                                                         USING (ID) 
                                                                ) as diag
                                                    USING(VAT_doctor, date_timestamp)
                                                    WHERE (c.VAT_doctor=:VAT_doctor AND c.date_timestamp= :dates)");
                $result=$info_diag->execute([
                    'VAT_doctor' => $VAT_doctor,
                    'dates' => $date_timestamp
                    ]);
            
                $n_diag = $info_diag->rowCount();
                $diag=$info_diag->fetchAll();
                if($n_diag>0)
                {    
                    echo("<h2>Diagnosis:</h2>");
                    echo("</table>");
                    echo("<table border=\"1\" style=\"margin-bottom: 20px;\"> ");
                    echo("<tr>");
                    echo("<td>Diagnostic</td>");
                    echo("<td>Description</td>");
                    echo("</tr>\n");
                    {
                        foreach($diag as $row){
                            echo("<tr>");
                            echo("<td>{$row['ID']}</td>");
                            echo("<td>{$row['description']}</td>");
                            echo("</tr>\n");
                        }
                    }echo("</table>");
                }
                $info_pres = $connection->prepare(" SELECT *
                                                    FROM appointment as a
                                                    INNER JOIN consultation as c
                                                    USING (VAT_doctor,date_timestamp) 
                                                    INNER JOIN prescription as pres
                                                    USING(VAT_doctor, date_timestamp)
                                                    WHERE (c.VAT_doctor=:VAT_doctor AND c.date_timestamp= :dates)");
                $result=$info_pres->execute([
                'VAT_doctor' => $VAT_doctor,
                'dates' => $date_timestamp
                ]);
                $n_pres = $info_pres->rowCount();
                $pres=$info_pres->fetchAll();
                if($n_pres>0)
                {   
                    echo("<h2>Prescriptions:</h2>");
                    echo("</table>");
                    echo("<table border=\"1\" style=\"margin-bottom: 20px;\"> ");
                    echo("<tr>");
                    echo("<td>Prescription</td>");
                    echo("<td>Lab</td>");
                    echo("<td>Dosage</td>");
                    echo("<td>Description</td>");
                    
                    echo("</tr>\n");
                    {
                        foreach($pres as $row){
                            echo("<tr>");
                            echo("<td>{$row['name']}</td>");
                            echo("<td>{$row['lab']}</td>");
                            echo("<td>{$row['dosage']}</td>");
                            echo("<td>{$row['description']}</td>");
                            echo("</tr>\n");
                        }
                    }echo("</table>");
                }
            }else{
                echo("No more information found");
            }  
        }else{
            $info_apps = $connection->prepare("SELECT *
                                                FROM appointment as a
                                                INNER JOIN employee as e
                                                ON (e.VAT=a.VAT_doctor)
                                                WHERE (a.VAT_doctor = :VAT_doctor AND a.date_timestamp= :dates)");
            $result=$info_apps->execute(['VAT_doctor' => $VAT_doctor,'dates' => $date_timestamp]);
            
            $n_info = $info_apps->rowCount();
            if($n_info>0)
            { 
                echo("<h1>Appointment Information:</h1>");

                echo("<table border=\"1\" style=\"margin-bottom: 20px;\"> ");
                echo("<tr>");
                echo("<td>Date and Time</td>");
                echo("<td>Doctor's name</td>");
                echo("<td>Doctor's VAT</td>");
                echo("<td>Description</td>");
                echo("</tr>\n");
               {
                    while($apps=$info_apps->fetch()){
                        echo("<tr>");
                        echo("<td>{$apps['date_timestamp']}</td>");
                        echo("<td>{$apps['name']}</td>");
                        echo("<td>{$apps['VAT_doctor']}</td>");
                        echo("<td>{$apps['description']}</td>");
                        echo("</tr>\n");
                    }
                }
                echo("</table>");
            }else{   #--------------------------------------------------------------------------------------------------------------
                echo("No more information found");
            }   
        }        



        $search_procedure = $connection->prepare("SELECT name, description
                                                    FROM procedure_in_consultation
                                                    WHERE (VAT_doctor=:VAT_doctor AND date_timestamp= :dates)");
                $result5=$search_procedure->execute([
                'VAT_doctor' => $VAT_doctor,
                'dates' => $date_timestamp
                ]);
                $n_procedures = $search_procedure->rowCount();
                $procedures=$search_procedure->fetchAll();
                if($n_procedures>0)
                {   
                    echo("<h2>Procedures:</h2>");
                    echo("</table>");
                    echo("<table border=\"1\" style=\"margin-bottom: 20px;\"> ");
                    echo("<tr>");
                    echo("<td>Name</td>");
                    echo("<td>Description</td>");
                    echo("</tr>\n");
                    {
                        foreach($procedures as $row){
                            echo("<tr>");
                            echo("<td>{$row['name']}</td>");
                            echo("<td>{$row['description']}</td>");
                            echo("</tr>\n");
                        }
                    }echo("</table>");
                }

        $search_charting = $connection->prepare("SELECT name, quadrant, nm, description, measure
                                                    FROM procedure_charting
                                                    WHERE (VAT=:VAT_doctor AND date_timestamp= :dates)");
                $result6=$search_charting->execute([
                'VAT_doctor' => $VAT_doctor,
                'dates' => $date_timestamp
                ]);
                $n_charting = $search_charting->rowCount();
                $charts=$search_charting->fetchAll();
                if($n_charting>0)
                {   
                    echo("<h2>Chartings:</h2>");
                    echo("</table>");
                    echo("<table border=\"1\" style=\"margin-bottom: 20px;\"> ");
                    echo("<tr>");
                    echo("<td>Name</td>");
                    echo("<td>Quadrant</td>");
                    echo("<td>Number</td>");
                    echo("<td>Description</td>");
                    echo("<td>Measure</td>");
                    echo("</tr>\n");
                    {
                        foreach($charts as $row){
                            echo("<tr>");
                            echo("<td>{$row['name']}</td>");
                            echo("<td>{$row['quadrant']}</td>");
                            echo("<td>{$row['nm']}</td>");
                            echo("<td>{$row['description']}</td>");
                            echo("<td>{$row['measure']}</td>");
                            echo("</tr>\n");
                        }
                    }echo("</table>");
                }
                else
                {
                    if($isConsult=="consultation"){
                        echo("<form action='add_dental_form.php' method='post'>
                        <input type='submit' value='Add Dental Charting Informations'/>
                        <input type=hidden name='VAT_doctor' value='{$VAT_doctor}'>
                        <input type=hidden name='date_timestamp' value='{$date_timestamp}'>
                        <input type=hidden name='isConsult' value='{$isConsult}'>
                        <input type=hidden name='VAT_client' value='{$VAT_client}'>
                        </form>"); 
                    }
                }

        #######
        if($isConsult=="missed"){
            echo("<form action='add_info_form.php' method='post'>
            <input type='submit' value='Add Information'/>
            <input type=hidden name='VAT_doctor' value='{$VAT_doctor}'>
            <input type=hidden name='VAT_client' value='{$VAT_client}'>
            <input type=hidden name='date_timestamp' value='{$date_timestamp}'>
            </form>");
         }   
        #######
        
        echo("<form action='apps_and_cons.php' method='post'>
        <input type=hidden name='VAT_client' value='{$VAT_client}'>
        <input type='submit' value='Go Back'/></a>
        </form>");
        # END CONNECTION
        $connection = null;
    ?>

</body>
</html>
