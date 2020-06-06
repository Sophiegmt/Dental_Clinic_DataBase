<html>
<body>
    <?php
        # FORM
        $date = $_REQUEST['app_date'];
        $time = $_REQUEST['app_time'];
        $VAT_client= $_REQUEST['VAT_client'];
        $description= $_REQUEST['description'];

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

        if(empty($date) || empty($time))
        {
            echo("Appointment date or time not complete.");
            echo ("<a href=\"javascript:history.go(-1)\">
        <input type='submit' value='Go Back'/></a>
        </form>");
        }
        else
        {
            echo("<p>date {$date}</p>");
            $time= date('H:i:s', strtotime("$time"));
            echo("<p>time {$time}</p>");
            $find_doctors = $connection->prepare("SELECT *
                                                FROM employee as e
                                                WHERE e.VAT NOT IN (SELECT VAT_doctor
                                                                    FROM appointment
                                                                    WHERE DATE(date_timestamp) =:appointment_date AND TIME(date_timestamp) =:appointment_time)
                                                AND e.VAT IN(SELECT VAT FROM doctor)");

            $result=$find_doctors ->execute([
            'appointment_date' => $date,
            'appointment_time' => $time,
            ]);
            if ($result== FALSE)
            {
                echo("Doctor search query.");
                $info = $connection->errorInfo();
                echo("<p>Error: {$info[1]}</p>");
                exit();
            }

            $n_doctors=$find_doctors->rowCount();
            if($n_doctors>0)
            {
                echo("<p> Number of Doctors available: {$n_doctors}</p>" );
                echo("<table border=\"1\" style=\"margin-bottom: 20px;\"> ");
                echo("<tr>");
                echo("<td>VAT_doctor</td>");
                echo("<td>Name</td>");
                echo("<td></td>");
                echo("</tr>\n");
            {
                    while($doctors=$find_doctors->fetch()){
                        echo("<tr>");
                        echo("<td>{$doctors['VAT']}</td>");
                        echo("<td>{$doctors['name']}</td>");

                        echo("<td><a href=\"book_app.php?VAT_client=");
                        echo("{$VAT_client}");
                        echo("&VAT_doctor=");
                        echo($doctors['VAT']);
                        echo("&date=");
                        echo($date);
                        echo("&time=");
                        echo($time);
                        echo("&description=");
                        echo($description);
                        echo("\">Book Appointment</a></td>\n");
                        echo("</tr>\n");
                    }
                }
            }echo("</table>");
        }
        #######################################################################
        ###            PROCURAR MEDICOS PARA A DATA                         ###
        ###       SELECIONAR UM MEDICO PARA MARCAR COM ESSE                 ###
        #######################################################################

        echo ("<a href=\"javascript:history.go(-1)\">
        <input type='submit' value='Go Back'/></a>
        </form>");
        # END CONNECTION
        $connection = null;
    ?>

</body>
</html>
