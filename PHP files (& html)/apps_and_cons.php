<html>
<body>
    <?php
        #FORM
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
        $find_name = $connection->prepare("SELECT name
                                           FROM client
                                           WHERE VAT_client = :VAT_client");
        $result=$find_name->execute(['VAT_client' => $VAT_client]);
        $n_client = $find_name->rowCount();
        if ($result== FALSE)
        {
            echo("Couldn't find client");
            $info = $connection->errorInfo();
            echo("<p>Error: {$info[1]}</p>");
            exit();
        }
        $name=$find_name->fetchAll();
        foreach($name as $row){
                $client_name=$row['name'];
        }
        echo("<h2>Client: VAT= {$VAT_client} and Name= {$client_name}</h2>");

        $find_apps = $connection->prepare("SELECT ap.date_timestamp as dates, ap.VAT_doctor as VAT_doc, c.s as s, c.o as o, c.a as a, c.p as p
                                           FROM appointment as ap
                                           LEFT OUTER JOIN consultation as c
                                           ON ap.VAT_doctor=c.VAT_doctor and  ap.date_timestamp= c.date_timestamp
                                           WHERE ap.VAT_client = :VAT_client
                                           ORDER BY ap.date_timestamp ASC");

        $result=$find_apps->execute(['VAT_client' => $VAT_client]);
        $n_apps = $find_apps->rowCount();
        if($n_apps>0)
        { 
            echo("<h3>Appointments and Consultations:</h3>");
            echo("<p> Number of Appointments/Consultations: {$n_apps}</p>" );
            echo("<table border=\"1\" style=\"margin-bottom: 20px;\"> ");
            echo("<tr>");
            echo("<td>Appointment Date</td>");
            echo("<td>Status</td>");
            echo("<td>Link</td>");
            echo("</tr>\n");
           {
                while($app=$find_apps->fetch()){
                    echo("<tr>");
                    echo("<td>{$app['dates']}</td>");
                    if($app['dates']>date("Y-m-d")){ 
                        echo("<td>Appointment booked</td>");
                        $isConsult="booked";
                    }elseif($app['s']==NULL){
                        echo("<td>Passed Appointment</td>");
                        $isConsult="missed";
                    }else{
                        echo("<td>Consultation</td>");
                        $isConsult="consultation";
                    }
                    echo("<td><a href=\"list_cons_info.php?date_timestamp=");
                    echo($app['dates']);
                    echo("&VAT_doctor=");
                    echo($app['VAT_doc']);
                    echo("&isConsult=");
                    echo($isConsult);
                    echo("&VAT_client=");
                    echo($VAT_client);
                    echo("\">More Information</a></td>\n");
                    echo("</tr>\n");
                }
           }
           echo("</table>");
        }else{
                echo("<h4>No Appointments nor Consultations found</h4>");
        } 

        #######################################################################
        ###           PROCURAR CONSULTAS/APPS DO CLIENTE                    ###
        ###            QUANDO SE CARREGAR NA CONSULTA                       ###
        ###       action='list_cons_info.php' (ainda nao esta feita)        ###
        #######################################################################


        echo("<form action='add_app_form.php' method='post'>
        <input type='submit' value='Add New Appointment'/>
        <input type=hidden name='VAT_client' value='{$VAT_client}'>
        </form>");
        echo("<form action='search_client_form.php' method='post'>
        <input type='submit' value='Go Back to Search'/>
        </form>");
  

        $connection = null;
    ?>

</body>
</html>
