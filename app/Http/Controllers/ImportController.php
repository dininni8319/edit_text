<?php

namespace App\Http\Controllers;

use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ImportController extends Controller
{
   public function index(Request $req)
   {
        $file1 = $req->file_import1;
        $file2 = $req->file_import2;

        // we get the original name of the file
        $fileName1 = $file1->getClientOriginalName();
        $fileName2 = $file2->getClientOriginalName();

        // store the files in a tempo folder 
        $file1->move("temp/"."fatture/", $fileName1);
        $file2->move("temp/"."fatture/", $fileName2);

        // we are getting the original name and the extension of the attachment
        $delimiter = '.';

        $nameAndExtension = explode($delimiter ,$fileName2);
        $fileNameOriginal = explode($delimiter ,$fileName2)[0];
        $fileExtension = explode($delimiter ,$fileName2)[1];
        
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

        $childElement = $fileXML->createElement('Allegati');
        $childElement->appendChild($fileXML->createElement('NomeAttachment', $fileNameOriginal));
        $childElement->appendChild($fileXML->createElement('FormatoAttachment', ucfirst($fileExtension)));
        $childElement->appendChild($fileXML->createElement('Attachment', $attachmentEncoded));
        $root->appendChild($childElement);

        $fileXML->save(getcwd().'/temp/fatture/'.$fileName1);
        
        unlink("temp/"."fatture/".$fileName2);

        return Response::download(getcwd().'/temp/fatture/'.$fileName1, 'nuovo_fattura_'.$fileNameOriginal.".xml", ['Content-Type: text/xml']);
    }
}
