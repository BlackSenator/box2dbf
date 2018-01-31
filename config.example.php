<?php

$config = [
    'phonebooks' => [
	    [
            'id'   => 0,
            'name' => 'Telefonbuch'
		],                                 /* add as many as you need
		[
            'id'   => 1,
            'name' => '?'
		],                                 */

    ],

    'fritzbox' => [
        'url'      => 'fritz.box',
        'user'     => 'dslf-config',       // default User 
        'password' => '',
    ],

    'fritzadrpath' => [                
        '/media/fritzbox/FRITZ/mediabox/FritzAdr.dbf'
    ],   
];
