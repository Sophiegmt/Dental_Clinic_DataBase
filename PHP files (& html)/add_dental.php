<html>
<body>
    <?php
        # FORM

        $TR = array($_REQUEST['TR1'], $_REQUEST['TR2'], $_REQUEST['TR3'], $_REQUEST['TR4'],$_REQUEST['TR5'], $_REQUEST['TR6'], $_REQUEST['TR7'], $_REQUEST['TR8']);

        $TL = array($_REQUEST['TL1'], $_REQUEST['TL2'], $_REQUEST['TL3'], $_REQUEST['TL4'],$_REQUEST['TL5'], $_REQUEST['TL6'], $_REQUEST['TL7'], $_REQUEST['TL8']);

        $BR = array($_REQUEST['BR1'], $_REQUEST['BR2'], $_REQUEST['BR3'], $_REQUEST['BR4'],$_REQUEST['BR5'], $_REQUEST['BR6'], $_REQUEST['BR7'], $_REQUEST['BR8']);

        $BL = array($_REQUEST['BL1'], $_REQUEST['BL2'], $_REQUEST['BL3'], $_REQUEST['BL4'],$_REQUEST['BL5'], $_REQUEST['BL6'], $_REQUEST['BL7'], $_REQUEST['BL8']);



        $TRD = array($_REQUEST['TR1D'], $_REQUEST['TR2D'], $_REQUEST['TR3D'], $_REQUEST['TR4D'],$_REQUEST['TR5D'], $_REQUEST['TR6D'], $_REQUEST['TR7D'], $_REQUEST['TR8D']);

        $TLD = array($_REQUEST['TL1D'], $_REQUEST['TL2D'], $_REQUEST['TL3D'], $_REQUEST['TL4D'],$_REQUEST['TL5D'], $_REQUEST['TL6D'], $_REQUEST['TL7D'], $_REQUEST['TL8D']);

        $BRD = array($_REQUEST['BR1D'], $_REQUEST['BR2D'], $_REQUEST['BR3D'], $_REQUEST['BR4D'],$_REQUEST['BR5D'], $_REQUEST['BR6D'], $_REQUEST['BR7D'], $_REQUEST['BR8D']);

        $BLD = array($_REQUEST['BL1D'], $_REQUEST['BL2D'], $_REQUEST['BL3D'], $_REQUEST['BL4D'],$_REQUEST['BL5D'], $_REQUEST['BL6D'], $_REQUEST['BL7D'], $_REQUEST['BL8D']);

        $description=$_REQUEST['desc'];

        $VAT_doctor=(integer)$_REQUEST['VAT_doctor'];
        $date_timestamp=(string)$_REQUEST['date_timestamp'];
        $isConsult=$_REQUEST['isConsult'];


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

        ############## FIND CLIENT VAT ###############################

        $find_client = $connection->prepare("SELECT VAT_client
                                            FROM appointment 
                                            WHERE VAT_doctor=:VAT_doctor AND date_timestamp=:date_timestamp");

        $result=$find_client ->execute([
       'VAT_doctor' => $VAT_doctor,
       'date_timestamp'=> $date_timestamp,
        ]);
        if ($result== FALSE)
        {
            echo("Couldn't find client.");
            $info = $connection->errorInfo();
            echo("<p>Error: {$info[1]}</p>");
            exit();
        }

        $data=$find_client->fetchAll();
        foreach($data as $row)
        {
            $VAT_client=$row['VAT_client'];
        }


        ################### FIND CLIENT AGE ##################################
        ### (to know which type of procedure to insert (teenager or adult)) ##

        $find_client_age = $connection->prepare("SELECT age 
                                                FROM client
                                                WHERE VAT_client=:VAT_client");

        $result2=$find_client_age->execute([
        'VAT_client' => $VAT_client,
        ]);
        if ($result2== FALSE)
        {
            echo("<p>Couldn't find client age.");
            $info = $connection->errorInfo();
            echo("<p>Error: {$info[1]}</p>");
            exit();
        }
        $data=$find_client_age->fetchAll();
        foreach($data as $row)
        {
            $client_age=$row['age'];
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
			echo("<form action='list_cons_info.php' method='post'>
        <input type=hidden name='VAT_client' value='{$VAT_client}'>
        <input type=hidden name='VAT_doctor' value='{$VAT_doctor}'>
        <input type=hidden name='date_timestamp' value='{$date_timestamp}'>
        <input type=hidden name='isConsult' value='{$isConsult}'>
        <input type='submit' value='Go Back'/></a>
        </form>");        
		}
		else
		{
	        ########## SET UP STRINGS TO USE IN INSERTION #######################
	        $teenager = "teenager dental charting";
	        $adult = "adult dental charting";
	        $TopR = "top right";
	        $TopL = "top left";
	        $BottomL = "bottom left";
	        $BottomR = "bottom right";

	        ###########           FOR ADULT CLIENT         ######################

	        if ($client_age>19) 
	        {
	            echo("<p>Searching for procedure.</p>");
	            ## CHECK IF THERE IS A PROCEDURE ALREADY
	            $find_procedure = $connection->prepare("SELECT * 
	                                                    FROM procedure_in_consultation
	                                                    WHERE VAT_doctor=:VAT_doctor 
	                                                    AND date_timestamp=:date_timestamp
	                                                    AND name =:proc");
	            $result3=$find_procedure ->execute([
	           'VAT_doctor' => $VAT_doctor,
	           'date_timestamp'=> $date_timestamp,
	           'proc' => $adult,
	            ]);
	            $n_proc = $find_procedure->rowCount();
	            if ($result3== FALSE)
	            {
	                echo("Couldn't find procedure.");
	                $info = $connection->errorInfo();
	                echo("<p>Error: {$info[1]}</p>");
	                exit();
	            }
	            elseif($n_proc == 0)
	            {
	                echo("<p>Didnt find procedure, so adding it.</p>");
	                ## IF THERE ISNT PROCEDURE, ADD IT
	                $sql = "INSERT INTO procedure_in_consultation VALUES ('$adult', $VAT_doctor, '$date_timestamp', '$description')"; 
	                $nrows = $connection->exec($sql);
	                if($nrows== FALSE)
	                {
	                    echo("<p>Couldn't add procedure_in_consultation.</p>");
	                    $info = $connection->errorInfo();
	                    echo("<p>Error: {$info[1]}</p>");
	                    exit();
	                }
	                else
	                {
	                    echo("<p> procedure added!!</p>");
	                    #### ADD INFO FOR EACH TEETH #####
	                    $i = 1;
	                    foreach ($TR as $key => $value) 
	                    {
	                        if (!empty($value) && !empty($TRD[$key])) 
	                        {
	                            $sql1 = "INSERT INTO procedure_charting VALUES ('$adult', $VAT_doctor, '$date_timestamp', '$TopR', $i, '$TRD[$key]', $value)";

	                            $nrows = $connection->exec($sql1);
	                            if($nrows== FALSE)
	                            {
	                                echo("<p>Couldn't add $TopR $key teeth information.</p>");
	                                $info = $connection->errorInfo();
	                                echo("<p>Error: {$info[1]}</p>");
	                                exit();
	                            }           
	                        }
	                        $i++;
	                    }   
	                    $i = 1;
	                    foreach ($TL as $key => $value) 
	                    {
	                        if (!empty($value) && !empty($TLD[$key])) 
	                        {
	                            $sql2 = "INSERT INTO procedure_charting VALUES ('$adult', $VAT_doctor, '$date_timestamp', '$TopL', $i, '$TLD[$key]', $value)";

	                            $nrows = $connection->exec($sql2);
	                            if($nrows== FALSE)
	                            {
	                                echo("<p>Couldn't add $TopL $key teeth information.</p>");
	                                $info = $connection->errorInfo();
	                                echo("<p>Error: {$info[1]}</p>");
	                                exit();
	                            }           
	                        }
	                        $i++;
	                    }  
	                    $i = 1;
	                    foreach ($BR as $key => $value) 
	                    {
	                        if (!empty($value) && !empty($BRD[$key])) 
	                        {
	                            $sql3 = "INSERT INTO procedure_charting VALUES ('$adult', $VAT_doctor, '$date_timestamp', '$BottomR', $i, '$BRD[$key]', $value)";

	                            $nrows = $connection->exec($sql3);
	                            if($nrows== FALSE)
	                            {
	                                echo("<p>Couldn't add $BottomR $key teeth information.</p>");
	                                $info = $connection->errorInfo();
	                                echo("<p>Error: {$info[1]}</p>");
	                                exit();
	                            }           
	                        }
	                        $i++;
	                    }
	                    $i = 1;
	                    foreach ($BL as $key => $value) 
	                    {
	                        if (!empty($value) && !empty($BLD[$key])) 
	                        {
	                            $sql4 = "INSERT INTO procedure_charting VALUES ('$adult', $VAT_doctor, '$date_timestamp', '$BottomL', $i, '$BLD[$key]', $value)";

	                            $nrows = $connection->exec($sql4);
	                            if($nrows== FALSE)
	                            {
	                                echo("<p>Couldn't add $BottomL $key teeth information.</p>");
	                                $info = $connection->errorInfo();
	                                echo("<p>Error: {$info[1]}</p>");
	                                exit();
	                            }           
	                        }
	                        $i++;
	                    }    
	                }    
	            }
	        }
	        else
	        {
	            echo("<p>Searching for procedure.</p>");
	            ## CHECK IF THERE IS A PROCEDURE ALREADY
	            $find_procedure = $connection->prepare("SELECT * 
	                                                    FROM procedure_in_consultation
	                                                    WHERE VAT_doctor=:VAT_doctor 
	                                                    AND date_timestamp=:date_timestamp
	                                                    AND name =:proc");
	            $result3=$find_procedure ->execute([
	           'VAT_doctor' => $VAT_doctor,
	           'date_timestamp'=> $date_timestamp,
	           'proc' => $teenager,
	            ]);
	            $n_proc = $find_procedure->rowCount();
	            if ($result3== FALSE)
	            {
	                echo("Couldn't find procedure.");
	                $info = $connection->errorInfo();
	                echo("<p>Error: {$info[1]}</p>");
	                exit();
	            }
	            elseif($n_proc == 0)
	            {
	                echo("<p>Didnt find procedure, so adding it.</p>");
	                ## IF THERE ISNT PROCEDURE, ADD IT
	                $sql = "INSERT INTO procedure_in_consultation VALUES ('$teenager', $VAT_doctor, '$date_timestamp', '$description')"; 
	                $nrows = $connection->exec($sql);
	                if($nrows== FALSE)
	                {
	                    echo("<p>Couldn't add procedure_in_consultation.</p>");
	                    $info = $connection->errorInfo();
	                    echo("<p>Error: {$info[1]}</p>");
	                    exit();
	                }
	                else
	                {
	                    echo("<p> procedure added!!</p>");
	                    #### ADD INFO FOR EACH TEETH #####
	                    $i = 1;
	                    foreach ($TR as $key => $value) 
	                    {
	                        if (!empty($value) && !empty($TRD[$key])) 
	                        {
	                            $sql1 = "INSERT INTO procedure_charting VALUES ('$teenager', $VAT_doctor, '$date_timestamp', '$TopR', $i, '$TRD[$key]', $value)";

	                            $nrows = $connection->exec($sql1);
	                            if($nrows== FALSE)
	                            {
	                                echo("<p>Couldn't add $TopR $key teeth information.</p>");
	                                $info = $connection->errorInfo();
	                                echo("<p>Error: {$info[1]}</p>");
	                                exit();
	                            }           
	                        }
	                        $i++;
	                    }   
	                    $i = 1;
	                    foreach ($TL as $key => $value) 
	                    {
	                        if (!empty($value) && !empty($TLD[$key])) 
	                        {
	                            $sql2 = "INSERT INTO procedure_charting VALUES ('$teenager', $VAT_doctor, '$date_timestamp', '$TopL', $i, '$TLD[$key]', $value)";

	                            $nrows = $connection->exec($sql2);
	                            if($nrows== FALSE)
	                            {
	                                echo("<p>Couldn't add $TopL $key teeth information.</p>");
	                                $info = $connection->errorInfo();
	                                echo("<p>Error: {$info[1]}</p>");
	                                exit();
	                            }           
	                        }
	                        $i++;
	                    }  
	                    $i = 1;
	                    foreach ($BR as $key => $value) 
	                    {
	                        if (!empty($value) && !empty($BRD[$key])) 
	                        {
	                            $sql3 = "INSERT INTO procedure_charting VALUES ('$teenager', $VAT_doctor, '$date_timestamp', '$BottomR', $i, '$BRD[$key]', $value)";

	                            $nrows = $connection->exec($sql3);
	                            if($nrows== FALSE)
	                            {
	                                echo("<p>Couldn't add $BottomR $key teeth information.</p>");
	                                $info = $connection->errorInfo();
	                                echo("<p>Error: {$info[1]}</p>");
	                                exit();
	                            }           
	                        }
	                        $i++;
	                    }
	                    $i = 1;
	                    foreach ($BL as $key => $value) 
	                    {
	                        if (!empty($value) && !empty($BLD[$key])) 
	                        {
	                            $sql4 = "INSERT INTO procedure_charting VALUES ('$teenager', $VAT_doctor, '$date_timestamp', '$BottomL', $i, '$BLD[$key]', $value)";

	                            $nrows = $connection->exec($sql4);
	                            if($nrows== FALSE)
	                            {
	                                echo("<p>Couldn't add $BottomL $key teeth information.</p>");
	                                $info = $connection->errorInfo();
	                                echo("<p>Error: {$info[1]}</p>");
	                                exit();
	                            }           
	                        }
	                        $i++;
	                    }    
	                }    
	            }
	        }
	        header('Location: http://web.tecnico.ulisboa.pt/ist425487/list_cons_info.php?VAT_doctor='.$VAT_doctor.'&date_timestamp='.$date_timestamp.'&isConsult='.$isConsult.'&VAT_client='.$VAT_client);
       		die(); 
    	}
        $connection = null;
    ?>

</body>
</html>
