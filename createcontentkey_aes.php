<?php 
//Copyright (c) 2016, Marcelo Leal
//Description: Simple Azure Media Services AES Protection key Creation
//Using and based on the Azure PHP SDK Examples: https://github.com/Azure/azure-sdk-for-php
//License: MIT (see LICENSE.txt file for details)

require_once __DIR__.'/vendor/autoload.php';

use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\Internal\MediaServicesSettings;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\MediaServices\Models\ContentKey;
use WindowsAzure\MediaServices\Models\ProtectionKeyTypes;
use WindowsAzure\MediaServices\Models\ContentKeyTypes;

// Our credentials...
include_once 'config.php';

echo "\r\n--------------------------------------------------------";
echo "\r\nAzure Media Services AES Protection Content Key Creation";
echo "\r\n--------------------------------------------------------\r\n";

// Let's Authenticate and get our token....
$access_token = ServicesBuilder::getInstance()->createMediaServicesService(new MediaServicesSettings($account, $secret));

// Create the Content Key... 
$content_key = createContentKey($access_token);

// Here is the result....
echo "\r\n>>> KEY\r\n";
echo "Content Key Id = {$content_key->getId()}\r\n";
echo "Content Key ProtectionId = {$content_key->getProtectionKeyId()}\r\n";
echo "Content Key Checksum = {$content_key->getChecksum()}\r\n";
echo "Content Key EncryptedContentKey = {$content_key->getEncryptedContentKey()}\r\n";

function createContentKey($access_token)
{
    // Generate a random new key (16-byte is for Common and envelope encryption)...
    $aes_key = Utilities::generateCryptoKey(16);
    //$aes_key = Utilities::generateCryptoKey(32);

    // Get the protection key id and etrieve the X.509 certificate using the ProtectionKeyId...
    $protection_key_id = $access_token->getProtectionKeyId(ContentKeyTypes::ENVELOPE_ENCRYPTION);
    //$protection_key_id = $access_token->getProtectionKeyId(ContentKeyTypes::STORAGE_ENCRYPTION);
    $protection_key = $access_token->getProtectionKey($protection_key_id);

    // Assemble the payload (encrypt the content key with the public key of the X.509 Cert, create checksum, etc)...
    // Reference: https://azure.microsoft.com/en-us/documentation/articles/media-services-rest-create-contentkey/
    $content_key = new ContentKey();
    $content_key->setContentKey($aes_key, $protection_key);
    $content_key->setProtectionKeyId($protection_key_id);
    $content_key->setProtectionKeyType(ProtectionKeyTypes::X509_CERTIFICATE_THUMBPRINT);
    $content_key->setContentKeyType(ContentKeyTypes::ENVELOPE_ENCRYPTION);
    //$content_key->setContentKeyType(ContentKeyTypes::STORAGE_ENCRYPTION);

    // Do our thing...
    $content_key = $access_token->createContentKey($content_key);

    return $content_key;
}

?>
