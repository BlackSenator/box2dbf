<?php

namespace blacksenator;

use blacksenator\fritzsoap\fritzsoap;
use blacksenator\FritzAdr\fritzadr;
use \SimpleXMLElement;
use \stdClass;

/**
 * set up a stable FTP connection to a designated destination
 *
 * @param string $url
 * @param string $user
 * @param string $password
 * @param string $directory
 * @param boolean $secure
 * @return mixed false or stream of ftp connection
 */
function getFtpConnection($url, $user, $password, $directory, $secure)
{
    $ftpserver = parse_url($url, PHP_URL_HOST) ? parse_url($url, PHP_URL_HOST) : $url;
    $connectFunc = $secure ? 'ftp_connect' : 'ftp_ssl_connect';

    if ($connectFunc == 'ftp_ssl_connect' && !function_exists('ftp_ssl_connect')) {
        throw new \Exception("PHP lacks support for 'ftp_ssl_connect', please use `plainFTP` to switch to unencrypted FTP");
    }
    if (false === ($ftp_conn = $connectFunc($ftpserver))) {
        $message = sprintf("Could not connect to ftp server %s for upload", $ftpserver);
        throw new \Exception($message);
    }
    if (!ftp_login($ftp_conn, $user, $password)) {
        $message = sprintf("Could not log in %s to ftp server %s for upload", $user, $ftpserver);
        throw new \Exception($message);
    }
    if (!ftp_pasv($ftp_conn, true)) {
        $message = sprintf("Could not switch to passive mode on ftp server %s for upload", $ftpserver);
        throw new \Exception($message);
    }
    if (!ftp_chdir($ftp_conn, $directory)) {
        $message = sprintf("Could not change to dir %s on ftp server %s for upload", $directory, $ftpserver);
        throw new \Exception($message);
    }
    return $ftp_conn;
}

/**
 * Downloads the phone book from Fritzbox via TR-064
 * Unfortunately, only this export will deliver the timestamp of the last change
 *
 * @param array $config
 * @return SimpleXMLElement|void phonebook
 */
function downloadPhonebookSOAP($config)
{
    $fritzbox = $config['fritzbox'];
    $phonebook = $config ['phonebook'];

    $client = new fritzsoap($fritzbox['url'], $fritzbox['user'], $fritzbox['password']);
    $client->getClient('x_contact', 'X_AVM-DE_OnTel:1');
    $result = $client->getPhonebook($phonebook['id']);
    return $result;
}

/**
 * if $config['fritzbox']['fritzadr'] is set, than all contact (names) with a fax number
 * are copied into a dBase III database fritzadr.dbf for FRITZ!fax purposes
 *
 * @param SimpleXMLElement $xmlPhonebook phonebook in FRITZ!Box format
 * @param array $config
 * @return int number of records written to fritzadr.dbf
 */
function uploadFritzAdr(SimpleXMLElement $xmlPhonebook, $config)
{
    // Prepare FTP connection
    $secure = @$config['plainFTP'] ? $config['plainFTP'] : false;
    $ftp_conn = getFtpConnection($config['url'], $config['user'], $config['password'], $config['fritzadr'], $secure);

    // open a fast in-memory file stream
    $memstream = fopen('php://memory', 'r+');
    $converter = new fritzadr;

    $faxContacts = $converter->getFAXcontacts($xmlPhonebook);                  // extracting
    if (count($faxContacts)) {
        fputs($memstream, $converter->getdBaseData($faxContacts));
        rewind($memstream);
        if (!ftp_fput($ftp_conn, 'fritzadr.dbf', $memstream, FTP_BINARY)) {
            error_log("Error uploading fritzadr.dbf!" . PHP_EOL);
        }
    }
    fclose($memstream);
    ftp_close($ftp_conn);

    return count($faxContacts);
}
