       
<html>
<body>
    <?php      
        $VAT_client = (integer)$_REQUEST['VAT_client'];
        $VAT_doctor= (integer)$_REQUEST['VAT_doctor'];
        $date_timestamp= $_REQUEST['date_timestamp']; 
        $S = (string)$_REQUEST['sub'];
        $O = (string)$_REQUEST['obj'];
        $A = (string)$_REQUEST['assess'];
        $P = (string)$_REQUEST['plan'];

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

        #######################################################################
        ###           ADICIONAR NOTAS À CONSULTA                            ###
        #######################################################################
        
        $sql1 = "INSERT INTO consultation VALUES ($VAT_doctor, '$date_timestamp', '$S','$O','$A','$P')";

        $nrows = $connection->exec($sql1); 
        if($nrows>0)
        {
            echo("<p>SOAP notes included!</p>");
        }
        elseif ($nrows== FALSE)
        {
            echo("<p>Couldn't include SOAP notes</p>");
            $info = $connection->errorInfo();
            echo("<p>Error : {$info[1]}</p>");
            exit();
        }
        header('Location: http://web.tecnico.ulisboa.pt/ist425487/add_info_form.php?VAT_doctor='.$VAT_doctor.'&date_timestamp='.$date_timestamp.'&VAT_client='.$VAT_client);

        die();
        $connection = null;
    ?>

</body>
</html>
