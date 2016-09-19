<?php

class GCM {
 
    //put your code here
    // constructor
    function __construct() {
         
    }
 
    /**
     * Sending Push Notification
     */
	public function storeUser($name, $email, $gcm_regid) {
        global $db;
		// insert user into database
		$data=array("regId"=>$gcm_regid,
					"mobileid"=>$email,
					"mobilename"=>$name);
		$id=$db->getVal("select id from ".PUSH_NOTIFICATION." where regId='".$gcm_regid."'");
        if($id==""){
			$id = $db->insertAry(PUSH_NOTIFICATION,$data);
		}else{ 
			return ($id);
        } 
    }
    public function send_notification($registatoin_ids, $message, $Image) {
		$registrationIds = ($registatoin_ids);
		// prep the bundle
		$msg = array
		(
			'message'       => $message,
			'image'         => $Image,
			'url'         => "http://m.fixndeal.com/test.html",
		);
		
		$fields = array
		(
			'registration_ids'  => $registrationIds,
			'data'              => $msg
		);
		
		$headers = array
		(
			'Authorization: key=' . GOOGLE_API_KEY,
			'Content-Type: application/json'
		);
		
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		curl_close( $ch );
		
		echo $result;
    }
}
?>