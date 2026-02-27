<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Str;
use Crypt;

class CKEditorController extends Controller
{
    public function upload(Request $request)
    {
        if($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName.'_'.time().'.'.$extension;
            $request->file('upload')->move(public_path('images'), $fileName);
            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = asset('images/'.$fileName);
            $msg = 'Image successfully uploaded';
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

            @header('Content-type: text/html; charset=utf-8');
            echo $response;
        }
    }
    public function summer_upload(Request $request)
    {
		$file = $request->file('file');
		if($request->hasFile('file'))
		{
			$filename = izrand(5).'-'.$file->getClientOriginalName();
			handleUpload($file,$filename,'/upload');
			//$input[$key] = $filename;
			//echo "masuk ";die;
			echo URL('/').'/uploads/'.$filename;
			die;
		}
        // $file = $request->file('file');
        // $file = $request->file('file');

        // if ($request->hasFile('file')) {
        //     // Generate a random filename
        //     $originalFilename = $file->getClientOriginalName();
        //     $extension = $file->getClientOriginalExtension(); // Get the file extension
        //     $randomString = Str::random(5);
        //     $encodedFilename = base64_encode($randomString . '-' . $originalFilename).'.' . $extension;

        //     // Save the file with the encoded filename
        //     handleUpload($file, $encodedFilename, '/upload');

        //     // Return the URL to the encoded filename
        //     echo URL('/') . '/uploads/' . $encodedFilename;
        //     die;
        // }

		echo $message = 'Ooops!  Your upload triggered the error:  ';

//if ($_FILES['file']['name']) {
  //if (!$_FILES['file']['error']) {
    //$name = md5(rand(100, 200));
    //$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    //$filename = $name.
    //'.'.$ext;
    //$destination = '/uploads/'.$filename; //change this directory
    //$location = $_FILES["file"]["tmp_name"];
    //move_uploaded_file($location, $destination);
    //echo 'http://test.yourdomain.al/images/'.$filename; //change this URL
  //} else {
    //echo $message = 'Ooops!  Your upload triggered the following error:  '.$_FILES['file']['error'];
  //}
//}
//die;
        //if($request->hasFile('file')) {
            //$originName = $request->file('file')->getClientOriginalName();
            //$fileName = pathinfo($originName, PATHINFO_FILENAME);
            //$extension = $request->file('file')->getClientOriginalExtension();
            //$fileName = $fileName.'_'.time().'.'.$extension;
            //$request->file('file')->move(public_path('images'), $fileName);
            //$CKEditorFuncNum = $request->input('CKEditorFuncNum');
            //$url = asset('images/'.$fileName);
            //$msg = 'Image successfully uploaded';
            //$response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

            //@header('Content-type: text/html; charset=utf-8');
            //echo $response;
        //}
    }
}
