<?php

namespace App\Actions;
use DOMDocument;

class WriteAndDeleteXML 
{
  public function handle($files, $fileName1)
  {
    foreach ($files as $key => $file) {
      $fileName2 = $file->getClientOriginalName();
      $file->move("temp/"."fatture/", $fileName2);
      // we are getting the original name and the extension of the attachment
      $fileExtension = substr($fileName2, strrpos($fileName2, '.') + 1);;
      $fileNameOriginal = substr($fileName2, 0, strrpos( $fileName2, '.'));
      
      // we are going to encode the content of the attachment
      $attachmentEncoded = base64_encode(file_get_contents(getcwd().'/temp/fatture/'.$fileName2));

      //loading xml filecontent
      $fileXMLLocation = file_get_contents(getcwd().'/temp/fatture/'.$fileName1);
      
      $fileXML = new DOMDocument;
      $fileXML->preserveWhiteSpace = false; //formating purpuse
      $fileXML->formatOutput = true;
      
      //load the file and append Allegati, with children, to the parent node
      $fileXML->loadXML($fileXMLLocation);
      $root = $fileXML->getElementsByTagName('FatturaElettronicaBody')->item(0);

      // here we are going to create the children elements where we will append the data from the allegati
      $childElement = $fileXML->createElement('Allegati');
      $childElement->appendChild($fileXML->createElement('NomeAttachment', $fileNameOriginal));
      $childElement->appendChild($fileXML->createElement('FormatoAttachment', strtoupper($fileExtension)));
      $childElement->appendChild($fileXML->createElement('Attachment', $attachmentEncoded));
      $root->appendChild($childElement);

      $fileXML->save(getcwd().'/temp/fatture/'.$fileName1);
      unlink("temp/"."fatture/".$fileName2);
    }
  }
}