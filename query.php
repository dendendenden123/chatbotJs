<?php 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type");

//include("conn.php");






function query($keyword, $conn){
    $stmt = $conn->prepare("SELECT repond FROM chatbotrespond WHERE keyword = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s",  $keyword ); 
    

    $stmt->execute();

    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {  
            return $row["repond"];
        }
    } 
}


function dictionary($messageArray ){

    $jsonData = file_get_contents('dictionary.json');
    $dictionary = json_decode($jsonData, true);
    foreach($messageArray as $message){
    foreach ($dictionary as $word => $details) {
            if($message == $word){
                $definition = preg_split("/[;]/", $details);
                return $word . "\n" . "is ".$definition[0] .".";
            }
        }
      
        }
}




    function getResponse($input, $filename) {
        $jsonData = file_get_contents($filename);
        $data = json_decode($jsonData, true); 
        foreach ($data['intents'] as $intent) {
            if (in_array($input, $intent['patterns'])) {
                // Choose a random response
                return $intent['responses'][array_rand($intent['responses'])];
            }
        }
    }
    




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
    $json = file_get_contents('php://input');
    $data = json_decode($json, true); 


    $key1 = isset($data['message']) ? $data['message'] : null;

     $messageArray = preg_split("/[,.\s]+/", $key1 );
    $messageArray  = array_filter($messageArray);

    $wordCount = count($messageArray);


    $found = null;

   
    

    
    if ($found == null) {  
       // $found = query($key1, $conn);
        if ($found == null) {
            $found =  getResponse($key1, "intents.json");
            if($found == null){
                $found =  getResponse($key1, "mentalHealthConversation.json");
                if ($found == null) {
                    $i = 0;
                    // while ($i  < $wordCount) {
                    //     $found =  query($messageArray[$i], $conn);
                    //     $i++;
                    //  }
                     if ($found == null) {
                        $found = dictionary($messageArray );
                        if($found == null)  {
                            $apologyMesage = [
                                "I'm sorry, but I didn't quite understand that phrase. Could you please rephrase your question or ensure that the sentence is grammatically correct? Thank you!", 
                                "I apologize, but I'm having trouble understanding that phrase. Could you kindly rephrase your question or check the grammar? Thank you!",
                                "I’m sorry, but I’m not sure I understand that. Would you mind rephrasing your question or ensuring it’s grammatically correct? Thanks!",
                                "Unfortunately, I didn’t quite catch that. Could you please rephrase your question or make sure it's grammatically accurate? Thank you!",
                                "I’m sorry for the confusion, but I didn’t fully grasp that phrase. Could you reword your question or check for grammar? Thanks a lot!",
                                "My apologies, but I found that phrase unclear. Would you be able to rephrase your question or confirm its grammar? Thank you!",
                                "I’m sorry, but I didn’t understand what you meant. Could you please rephrase your question or ensure it's grammatically correct? Thanks!",
                                "I regret that I didn’t quite understand that statement. Could you rephrase your question or verify the grammar? Thank you very much!",
                                "I apologize for the misunderstanding. Could you please clarify your question or check its grammatical structure? Thanks for your patience!",
                                "I'm sorry, but that phrase isn't clear to me. Could you kindly rephrase your question or confirm its grammar? I appreciate it!",
                                "I didn’t fully understand that phrase, and I apologize. Could you please reword your question or check for grammatical accuracy? Thank you"
                                ];

                                $found  =  $apologyMesage[Array_rand($apologyMesage)];
                        }  
                     }
                }
            }
        }
    }

    echo $found;

    }  else {
    echo json_encode(['error' => 'Invalid request method.']);
   }
