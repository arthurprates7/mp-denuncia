<?php

namespace App\Services;


use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;


class ApiService {

    private $baseURLForHandleHttpRequest = "https://api.z-api.io/instances/";

    public function __construct($instanceId = null , $token = null)
    {
        if(is_null($instanceId) and is_null($token)) {
            $instanceId = DB::table('infos')->first()->instanceId;
            $token = DB::table('infos')->first()->token;
        }

        $this->baseURLForHandleHttpRequest = $this->baseURLForHandleHttpRequest . $instanceId .'/token/'.$token.'/';
    }

    public function handleHttpRequest($endpoint, $data, $method = 'GET')
    {
        $response = NULL;

        try {

            $client = new Client();

            $params =  [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ];

            if(!is_null($data)) {
                $queryOrJSON = $method === "GET" ? 'query' : 'json';
                $params[$queryOrJSON] = $data;
            }

            if($method == 'GET'){
                $response = $client->$method($endpoint);
            }else {
                $response = $client->$method($endpoint,$params);
            }

        } catch (\Exception $ex) {

        }

        if(is_null($response))
            return null;

        return json_decode($response->getBody()->getContents());

    }

    //pega o QRCODE para autenticação
    public function getQrCode($token)
    {
        $url = $this->baseURLForHandleHttpRequest.'qr_code?token='.$token;
        $this->handleHttpRequest($url,'');
    }

    //sai do whatsapp
    public function logoutUser($token)
    {
        $url = $this->baseURLForHandleHttpRequest.'logout?token='.$token;

        $this->handleHttpRequest($url, '' );
    }

    //envia uma mensagem para o whatsapp
    public function sendMessage ($message , $number)
    {
        $url = $this->baseURLForHandleHttpRequest.'send-text';

        $data = [
            'message' => $message,
            'phone' => $number,
        ];

        $this->handleHttpRequest($url, $data , 'POST');
    }

    //envia uma imagem para o whatsapp
    public function sendImage ($imagem , $number)
    {
        $url = $this->baseURLForHandleHttpRequest.'send-image';

        $data = [
            'image' => $imagem,
            'phone' => $number,
        ];

        $this->handleHttpRequest($url, $data , 'POST');
    }

    //pega todas as mensagens enviadas
    public function searchMessagesUser($number, $limit = 25)
    {
        $url = $this->baseURLForHandleHttpRequest.'chat-messages/'.$number.'?amount='.$limit;
        return $this->handleHttpRequest($url, [] , 'GET');
    }

    //pega os contatos ativos
    public function getChats($limit = 100)
    {
        $url = $this->baseURLForHandleHttpRequest.'chats?page=0&pagesSize='.$limit;
        return $this->handleHttpRequest($url, [] , 'GET');
    }

    //store log messages
    public function storeLog ($message, $number , $user = null , $send = true, $block_id = null)
    {
        if(is_null($user)) {
            $user = auth()->user()->id;
        }

        Message::create([
            'body' => $message,
            'number' => $number,
            'user_id' => $user,
            'send'  => $send,
            'block_id' => $block_id
        ]);
    }

    public function updateOrCreateCall($number, $status = Call::STATUS_OPEN, $userId = null, $closedAt = null)
    {
        $call = Call::where('number',$number)
            ->where('status',Call::STATUS_OPEN)
            ->orderBy('id', 'desc')->first();

        if ($call) {
            $call->update([
                'status' => $status,
                'user_id' => $userId,
                'closed_at' => $closedAt,
            ]);
        } else {
           Call::create([
                'number' => $number,
                'status' => $status,
                'user_id' => $userId,
                'closed_at' => $closedAt,
            ]);
        }

        return $call;
    }

    public function lastBlock ($number)
    {
        $block = Message::where('number',$number)
            ->where('send',Message::SEND)
            ->orderBy('id', 'desc')->first();

        if($block and $block->block_id) {
            return $block->block_id;
        }
        return null;
    }

    public function blockMain ($user)
    {
        $block = Block::where('master',1)
            ->where('user_id',$user)
            ->orderBy('id', 'desc')->first();

        $options = Option::where('block_id',$block->id)
            ->orderBy('option','asc')->get();

        $message = "*$block->name*";

        foreach($options as $option){
            $message .=
            "
            $option->option - $option->text
            ";
        }

        return $message;
    }

    public function blockMainId ($user)
    {
        $block = Block::where('master', Block::MASTER)
            ->where('user_id',$user)
            ->orderBy('id', 'desc')->first();

        return $block->id;
    }

    public function optionBlock($block_pai,$mensagem)
    {

        $option = Option::where('block_id',$block_pai)
            ->where('option',$mensagem)
            ->orderBy('option','asc')->first();

        $block = Block::where('id',$option->id)
            ->orderBy('id', 'desc')->first();

        $options = Option::where('block_id',$block->id)
            ->orderBy('option','asc')->get();

        $message = "*$block->name*";
        foreach($options as $option){
            $message .=
                "
            $option->option - $option->text
            ";
        }

        return $message;


    }


    public function childrenBlock($block_pai,$mensagem)
    {
        $option = Option::where('block_id',$block_pai)
            ->where('option',$mensagem)
            ->orderBy('option','asc')->first();

        if(isset($option) and !$option->unlock_block_id){
            return $option->response;
        }
        if($option){
            return $option->unlock_block_id;
        }else{
            return null;
        }
    }

    public function closeCall($optionId, $number){
        $option = Option::find($optionId);

        if($option->end) {
            $call = Call::where('number',$number)->orderBy('created_at', 'desc')->first();
            $call->closed_at = Carbon::now();
            $call->status = 'closed';
            $call->save();
        }
    }

    public function optionsChildren($block_filho)
    {
        $block = Block::where('id',$block_filho)
            ->orderBy('id', 'desc')->first();

        if($block){
            $options = Option::where('block_id',$block->id)
                ->orderBy('option','asc')->get();

            $message = "*$block->name*";
            foreach($options as $option){
                $message .=
                    "
            $option->option - $option->text
            ";
            }

            return $message;
        }
        else{
            return $block_filho;
        }


    }

    //send photo to external service
    public function sendImageToExtern($image)
    {
        $parameter = "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"image\"\r\n\r\n$image\r\n-----011000010111000001101001--\r\n";

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.imgbb.com/1/upload?expiration=600&key=c115a546927b627577f6c1fdca0fecdf",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $parameter,
            CURLOPT_HTTPHEADER => [
                "content-type: multipart/form-data; boundary=---011000010111000001101001"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $decode = json_decode($response);
            return $decode->data->url;
        }
    }
}
