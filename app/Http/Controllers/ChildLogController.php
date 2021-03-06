<?php

namespace App\Http\Controllers;

use App\ChildLog;
use Illuminate\Http\Request;
use Cloudder;

class ChildLogController extends Controller
{



  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index()
  {
    return ChildLog::all();
  }

  /**
  * Function to show blocked resources
  *
  * @return \Illuminate\Http\Response
  */
  public function blocked()
  {
    return ChildLog::where('moderation_status','rejected')->orderBy('id','desc')->get();
  }

  /**
  * Store logs into the database
  *
  * @param  \Illuminate\Http\Request $logArray
  * @return \Illuminate\Http\Response
  */
  public function store(Request $logArray)
  {
    $logArray = json_decode(request()->getContent(), true);

    foreach ($logArray as $key => $log) {
      if(is_null($log)){ continue; }
      $insLog = new ChildLog();
      $insLog->url = $log['url'];
      $insLog->initiator = $log['initiator'];
      $insLog->timestamp = $log['timeStamp'];
      if($insLog->save()){
        //Upload image
        $string = "ngunyimacharia.github.io";
        if (strpos( $log['url'], $string) !== false) {
          $res = Cloudder::upload($log['url'], null, array("moderation" => "aws_rek"), []);
        }else{
          $res = Cloudder::upload($log['url'], null, array(), []);
        }
        $res = $res->getResult();
        $update = [
          'cloudinary_public_id'=>$res['public_id'],
          'cloudinary_version'=>$res['version'],
          'cloudinary_url'=>$res['url'],
          'cloudinary_secure_url'=>$res['secure_url'],
          'moderation_status'=>"",
          'moderation_reasons'=>"",
        ];
        // dd($res["moderation"][0]["response"]["moderation_labels"]);
        //Check moderation
        $update['moderation_status'] = "approved";
        if(isset($res["moderation"])){
          if($res["moderation"][0]['status'] == "rejected"){
            $update['moderation_status'] = $res["moderation"][0]['status'];
            $moderation_reasons = "";
            foreach ($res["moderation"][0]["response"]["moderation_labels"] as $key => $value) {
              $moderation_reasons .= $value["name"]." ; ";
            }

            $update['moderation_reasons'] = $moderation_reasons;
            //Send Notification
            $this->sendNotification("Explicit content. Content has been found to have ".$moderation_reasons);

          }
        }

        ChildLog::where('id',$insLog->id)->update($update);

      }
    }
  }

  /**
  * Display the specified resource.
  *
  * @param  \App\ChildLog  $childLog
  * @return \Illuminate\Http\Response
  */
  public function stats()
  {
    $logs = ChildLog::orderBy('created_at','desc')->get();
    $rejected = ChildLog::where('moderation_status','rejected')->orderBy('created_at','desc')->get();
    return view('home', ['logs'=>$logs,'rejected' => $rejected]);

  }

  protected function sendNotification($message) {
    $content = array(
      "en" => $message
    );
    $hashes_array = array();
    // array_push($hashes_array, array(
    //   "id" => "like-button",
    //   "text" => "Like",
    //   "icon" => "http://i.imgur.com/N8SN8ZS.png",
    //   "url" => "https://yoursite.com"
    // ));
    // array_push($hashes_array, array(
    //   "id" => "like-button-2",
    //   "text" => "Like2",
    //   "icon" => "http://i.imgur.com/N8SN8ZS.png",
    //   "url" => "https://yoursite.com"
    // ));
    $fields = array(
      'app_id' => "a6b0ea95-0ae1-4e0b-b858-98054099b681",
      'included_segments' => array(
        'All'
      ),
      'data' => array(
        "foo" => "bar"
      ),
      'contents' => $content,
      'web_buttons' => $hashes_array
    );

    $fields = json_encode($fields);
    print("\nJSON sent:\n");
    print($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json; charset=utf-8',
      'Authorization: Basic ZWRiMDhhOTEtODM0Ni00ZWRjLThlNjEtZGNmMmJhN2UyZmRh'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
  }

}
