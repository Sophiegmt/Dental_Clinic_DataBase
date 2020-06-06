<html>
<body>
    <?php
        # FORM
        $VAT_client = (integer)$_REQUEST['VAT_client'];
        $client_name = (string)$_REQUEST['client_name'];
        $client_addr = (string)$_REQUEST['client_addr'];

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
        #######################################################################################################################
        if(!empty($VAT_client))
        {
            ###################################################
            if(empty($client_name) && empty($client_addr))       // quando existe um num VAT no form
            {
                $find_clients = $connection->prepare("SELECT *
                                            FROM client
                                            WHERE VAT_client =:VAT_client");

                $result=$find_clients ->execute([
                    'VAT_client' => $VAT_client,
                ]);
                if ($result== FALSE)
                {
                    echo("Client search query(1)");
                    $info = $connection->errorInfo();
                    echo("<p>Error: {$info[1]}</p>");
                    exit();
                }
                ##################################################
            }elseif(!empty($client_name) && empty($client_addr)) // quando existe um num VAT e um nome no form
            {
                $find_clients = $connection->prepare("SELECT *
                                            FROM client
                                            WHERE VAT_client =:VAT_client
                                            AND name LIKE CONCAT('%',:client_name,'%')");

                $result=$find_clients ->execute([
                    'VAT_client' => $VAT_client,
                    'client_name' => $client_name,
                ]);
                if ($result== FALSE)
                {
                    echo("Client search query(2)");
                    $info = $connection->errorInfo();
                    echo("<p>Error: {$info[1]}</p>");
                    exit();
                }
                ###################################################
            }elseif(empty($client_name) && !empty($client_addr)){ // quando existe um num VAT e uma morada no form
                $find_clients = $connection->prepare("SELECT *
                                                    FROM client
                                                    WHERE VAT_client =:VAT_client
                                                    AND street LIKE CONCAT('%',:client_addr,'%') OR zip LIKE CONCAT('%',:client_addr,'%') OR city LIKE CONCAT('%',:client_addr,'%')");

                $result=$find_clients ->execute([
                    'VAT_client' => $VAT_client,
                    'client_addr' => $client_addr,
                ]);
                if ($result== FALSE)
                {
                    echo("Client search query(3)");
                    $info = $connection->errorInfo();
                    echo("<p>Error: {$info[1]}</p>");
                    exit();
                }
                 ###################################################
            }elseif(!empty($client_name) && !empty($client_addr)){ // quando existe um num VAT, um nome e uma morada no form
                $find_clients = $connection->prepare("SELECT *
                                                    FROM client
                                                    WHERE VAT_client =:VAT_client
                                                    AND name LIKE CONCAT('%',:client_name,'%')
                                                    AND street LIKE CONCAT('%',:client_addr,'%') OR zip LIKE CONCAT('%',:client_addr,'%') OR city LIKE CONCAT('%',:client_addr,'%')");

                $result=$find_clients ->execute([
                    'VAT_client' => $VAT_client,
                    'client_name' => $client_name,
                    'client_addr' => $client_addr,
                ]);
                if ($result== FALSE)
                {
                    echo("Client search query(4)");
                    $info = $connection->errorInfo();
                    echo("<p>Error: {$info[1]}</p>");
                    exit();
                }
            }
        }
        #####################################################################################################################
        elseif(empty($VAT_client) && !empty($client_name))
        {
            if(!empty($client_addr)){

                $find_clients = $connection->prepare("SELECT *
                                            FROM client
                                            WHERE client.name LIKE CONCAT('%',:client_name,'%')
                                            AND street LIKE CONCAT('%',:client_addr,'%') OR zip LIKE CONCAT('%',:client_addr,'%') OR city LIKE CONCAT('%',:client_addr,'%')");

                $result=$find_clients ->execute([
                'client_name' => $client_name,
                'client_addr' => $client_addr,
                ]);
                if ($result== FALSE)
                {
                    echo("Client search query(5)");
                    $info = $connection->errorInfo();
                    echo("<p>Error: {$info[1]}</p>");
                    exit();
                }
            }else{
                $find_clients = $connection->prepare("SELECT *
                                                      FROM client
                                                      WHERE client.name LIKE CONCAT('%',:client_name,'%')");

                $result=$find_clients ->execute([
                'client_name' => $client_name,
                ]);
                if ($result== FALSE)
                {
                    echo("Client search query(6)");
                    $info = $connection->errorInfo();
                    echo("<p>Error: {$info[1]}</p>");
                    exit();
                }
            }
        }
        ########################################################################################################################
        elseif(empty($VAT_client) && empty($client_name) && !empty($client_addr))
        {
            $find_clients = $connection->prepare("SELECT *
                                          FROM client
                                          WHERE client.street LIKE CONCAT('%',:client_addr,'%') OR client.zip LIKE CONCAT('%',:client_addr,'%') OR client.city LIKE CONCAT('%',:client_addr,'%')");

            $result=$find_clients ->execute([
            'client_addr' => $client_addr,
            ]);
            if ($result== FALSE)
            {
            echo("Client search query(7)");
            $info = $connection->errorInfo();
            echo("<p>Error: {$info[1]}</p>");
            exit();
            }
        }
        ########################################################################################################################
        elseif(empty($VAT_client) && empty($client_name) && empty($client_addr))
        {
            $find_clients = $connection->prepare("SELECT *
                                          FROM client");

            $result=$find_clients ->execute();
            if ($result== FALSE)
            {
            echo("Client search query(8)");
            $info = $connection->errorInfo();
            echo("<p>Error: {$info[1]}</p>");
            exit();
            }
        }


        $n_clients=$find_clients->rowCount();
        if($n_clients>0)
        {
            echo("<h4>Client:</h4>");

            echo("<p> Number of Clients: {$n_clients}</p>" );
            echo("<table border=\"1\" style=\"margin-bottom: 20px;\"> ");
            echo("<tr>");
            echo("<td>VAT_client</td>");
            echo("<td>Name</td>");
            echo("<td>Address</td>");
            echo("<td>City</td>");
            echo("<td>Zip</td>");
            echo("<td>Appointment Records</td>");
            echo("<td>Add Appointment</td>");
            echo("</tr>\n");
           {
                while($client=$find_clients->fetch()){
                    echo("<tr>");

                    echo("<td>{$client['VAT_client']}</td>");
                    echo("<td>{$client['name']}</td>");
                    echo("<td>{$client['street']}</td>");
                    echo("<td>{$client['city']}</td>");
                    echo("<td>{$client['zip']}</td>");
                    echo("<td><a href=\"apps_and_cons.php?VAT_client=");
                    echo($client['VAT_client']);
                    echo("\">here</a></td>\n");
                    echo("<td><a href=\"add_app_form.php?VAT_client=");
                    echo($client['VAT_client']);
                    echo("\">here</a></td>\n");
                    echo("</tr>\n");
                }
            }
            #######################################################################
            ###            QUANDO SE CARREGAR NO CLIENTE                        ###
            ###       action='apps_and_cons.php' (ainda nao esta feita)           ###
            #######################################################################




            echo("</table>");
            echo ("<a href=\"javascript:history.go(-1)\">
            <input type='submit' value='Go Back'/></a>
            </form>");
            echo("<form action='new_client_form.php' method='post'>
            <input type='submit' value='Add New Client'/>
            </form>");
        }else
        {
            echo("<p>Client not found</p>\n");
            echo ("<a href=\"javascript:history.go(-1)\">
            <input type='submit' value='Go Back'/></a>
            </form>");

        }
            # END CONNECTION
            $connection = null;
        ?>

</body>
</html>
