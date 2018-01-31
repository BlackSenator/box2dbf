<?php

namespace BlackSenator;

use BlackSenator\FritzAdr\converter2fa;
use BlackSenator\FritzAdr\fritzadr;
use \SimpleXMLElement;


function getSOAPclient($fb_ip = 'fritz.box', $user = 'dslf_config', $password = false) {
	
	    $client = new \SoapClient(
            null,
            array(
                'location'   => "http://".$fb_ip.":49000/upnp/control/x_contact",
                'uri'        => "urn:dslforum-org:service:X_AVM-DE_OnTel:1",
                'noroot'     => true,
                'login'      => $user,
                'password'   => $password,  /*
				'trace'      => true,
                'exceptions' => true  */
            )
        );
	return $client;
}


function exportFA($xml, string $dblocation) { 
    
	$convert2fa = new converter2fa();
    $DB3 = new fritzadr;                                            // Instanz von fritzadr erzeugen                                                // Achtung -> in config mit aufnehmen!
    
    IF ($DB3->CreateFritzAdr($dblocation)) {                        // Versuche die dBase-Datei zu erzeugen
        $DB3->OpenFritzAdr();                                       // wenn erfolgreich dann öffne die dBase-Datei
        $FritzAdrRecords = $convert2fa->convert($xml, $DB3->NumAttributes);
		$numconv = count($FritzAdrRecords);
		IF ($numconv > 0) {
		    foreach ($FritzAdrRecords as $FritzAdrRecord) {         // zerlege  FritzAdr array
                $DB3->AddRecordFritzAdr($FritzAdrRecord);           // und schreibe ihn als Datensatz in die dBase-Datei
            }
        }
		$numupload = $DB3->CountRecordFritzAdr();
		IF ($numupload <> $numconv) {
		    throw new \Exception('Upload to dBase File failed!');
		}
        $DB3->CloseFritzAdr();                                      // schließe die dBase-Datei
    return $numupload;
	}
}