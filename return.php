<?php
        include ("phpagi.php");
        date_default_timezone_set("Asia/Bangkok");

        $agi = new AGI();
        $Ref_ID = $argv[1];
        $DialStatus = $argv[2];
        $callStartDtm = $argv[3];
        $callEndDtm = $argv[4];
        $connt = "1";

        $data = array("callID" => $Ref_ID,
                            "callStatusID" => $DialStatus,
                            "callStartDtm" => $callStartDtm,
                            "callEndDtm" => $callEndDtm); // data u want to post

        $data_string = json_encode($data);
        $api_key = "xxxx";
        $password = "xxxx";
        if($connt == "1"){
                sleep(15);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://apitest/result");
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_USERPWD, $api_key.':'.$password);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Accept: application/json',
                    'Content-Type: application/json')
                );

                if(curl_exec($ch) === false)
        {
                    echo 'Curl error: ' . curl_error($ch);
        }
                $errors = curl_error($ch);
                $result = curl_exec($ch);
                $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                echo $returnCode;
                var_dump($errors);
                print_r(json_decode($result, true));

        } else {
                echo "SendBack";
        }
?>

