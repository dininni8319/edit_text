<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\WriteAndDeleteXML;
use Illuminate\Support\Facades\Response;

class ImportController extends Controller
{
   
   public function index(Request $req, WriteAndDeleteXML $action)
   {
       try {
           //code...
           $file1 = $req->file_import1;
           $files = $req->file_import2;
   
           // we get the original name of the file
           $fileName1 = $file1->getClientOriginalName();

           // store the files in a tempo folder 
           $file1->move("temp/"."fatture/", $fileName1);
          
           $action->handle($files, $fileName1);
       
           return Response::download(getcwd().'/temp/fatture/'.$fileName1, $fileName1, ['Content-Type: text/xml'])
             ->deleteFileAfterSend(true);
        } catch (\Throwable $th) {
           return redirect("/")->with("error", 'Il file non è stato elaborato');
       }
    }
}
