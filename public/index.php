<?php

require_once(__DIR__ . '/../bootstrap/bootstrap.php');

if (isset($_POST['text']) && $_POST['text'] != '') {
    $usernameAndPasswordArray = explode(" ", $_POST['text']);
    $username = $usernameAndPasswordArray[0];
    $password = $usernameAndPasswordArray[1];

    \Database\Repository::checkIfTokenGenerated($db);

    $client = new \GuzzleHttp\Client([]);
    $response = $client->request('POST', 'https://accounts.zoho.com/apiauthtoken/nb/create', [
        'form_params' => [
            'SCOPE' => 'zohopeople/peopleapi',
            'EMAIL_ID' => $username,
            'PASSWORD' => $password,
        ]
    ]);

    $respToken = explode("\n", $response->getBody()->getContents())[2];
    $authToken = substr($respToken, strpos($respToken, "=") + 1);

	try {
		if($authToken === "INVALID_PASSWORD"){
			throw new Exception("Invalid password - please try again");
		} elseif($authToken === "NO_SUCH_USER"){
			throw new Exception("Invalid username - please try again");
		}

		\Database\Repository::insertToken($db, $_POST['user_id'], $authToken);

		echo "*Your token has been successfully generated! Thanks for setting up the ZohoApp*";
	} catch(PDOException $e){
		echo $e->getMessage();
	} catch(Exception $e){
		echo $e->getMessage();
	}


} else {
    $ch = curl_init();

    if (FALSE === $ch)
        throw new Exception('failed to initialize');


    $payload = '{
    "text": "Would you like to play a game?",
    "response_type": "in_channel",
    "attachments": [
        {
            "text": "Choose a game to play",
            "fallback": "If you could read this message, you\'d be choosing something fun to do right now.",
            "color": "#3AA3E3",
            "attachment_type": "default",
            "callback_id": "game_selection",
            "actions": [
                {
                    "name": "games_list",
                    "text": "Pick a game...",
                    "type": "select",
                    "options": [
                        {
                            "text": "Hearts",
                            "value": "hearts"
                        },
                        {
                            "text": "Bridge",
                            "value": "bridge"
                        },
                        {
                            "text": "Checkers",
                            "value": "checkers"
                        },
                        {
                            "text": "Chess",
                            "value": "chess"
                        },
                        {
                            "text": "Poker",
                            "value": "poker"
                        },
                        {
                            "text": "Falken\'s Maze",
                            "value": "maze"
                        },
                        {
                            "text": "Global Thermonuclear War",
                            "value": "war"
                        }
                    ]
                }
            ]
        }
    ]
}';//'{"text": "Hi there, looks like this is your first time running the Zoho APP. Please run this command \"/zoho username password\" so I can generate a token for you."}';

    curl_setopt($ch, CURLOPT_URL, "https://hooks.slack.com/services/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");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);

    if (FALSE === $result)
        throw new Exception(curl_error($ch), curl_errno($ch));

    curl_close($ch);
}


//    $ch = curl_init();
//
//    if (FALSE === $ch)
//        throw new Exception('failed to initialize');
//
//
//    $payload = '{
//    "text": "Would you like to play a game?",
//    "response_type": "in_channel",
//    "attachments": [
//        {
//            "text": "Choose a game to play",
//            "fallback": "If you could read this message, you\'d be choosing something fun to do right now.",
//            "color": "#3AA3E3",
//            "attachment_type": "default",
//            "callback_id": "game_selection",
//            "actions": [
//                {
//                    "name": "games_list",
//                    "text": "Pick a game...",
//                    "type": "select",
//                    "options": [
//                        {
//                            "text": "Hearts",
//                            "value": "hearts"
//                        },
//                        {
//                            "text": "Bridge",
//                            "value": "bridge"
//                        },
//                        {
//                            "text": "Checkers",
//                            "value": "checkers"
//                        },
//                        {
//                            "text": "Chess",
//                            "value": "chess"
//                        },
//                        {
//                            "text": "Poker",
//                            "value": "poker"
//                        },
//                        {
//                            "text": "Falken\'s Maze",
//                            "value": "maze"
//                        },
//                        {
//                            "text": "Global Thermonuclear War",
//                            "value": "war"
//                        }
//                    ]
//                }
//            ]
//        }
//    ]
//}';//'{"text": "Hi there, looks like this is your first time running the Zoho APP. Please run this command \"/zoho username password\" so I can generate a token for you."}';
//
//    curl_setopt($ch, CURLOPT_URL, "https://hooks.slack.com/services/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");
//    curl_setopt($ch, CURLOPT_POST, 1);
//    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
//    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//    $result = curl_exec($ch);
//
//    echo $_POST['text'];
//
//    if (FALSE === $result)
//        throw new Exception(curl_error($ch), curl_errno($ch));
//
//    curl_close($ch);
//} catch (Exception $e) {
//
//    trigger_error(sprintf(
//        'Curl failed with error #%d: %s',
//        $e->getCode(), $e->getMessage()),
//        E_USER_ERROR);
//
//}