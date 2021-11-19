<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\FinishedEmail;
class MessageController extends Controller
{

    // List of messages starting with the latest
    // Only assistante FR have messages
    public function getMyMessages(){
        if(Auth::check()){
            if(Auth::user()->type == 'customer'){
                $messages = Message::where('send_to',Auth::user()->id)->orderBy("created_at","asc")->get();
                foreach($messages as $msg){
                    $ticket = Ticket::find($msg->id_ticket);
                    $msg->title = $ticket->title;
                    $msg->description = $ticket->description;
                    $agent = User::find($ticket->user_id);
                    $customer = User::find($ticket->createdBy);
                    $msg->agent = $agent;
                    $msg->customer = $customer;
                }
                return response()->json(["messages"=>$messages],200);
            }else{
                return response()->json(["message"=>"Vous n'avez pas de messages"],403);
            }
        }else{
            return response()->json(["message"=>"Vous n'êtes pas connecté"],403);
        }
    }

    // Marquer un ticket comme "Effectué"
    // Ne peut être effectué que par une assistane DZ `agent`
    // Charger un fichier zip contenant les fichiers correspondant à la tâche
    // Ajouter des remarques `notes` optionnel
    // La terminaison d'un ticket veut dire l'envoie d'une notification à l'assistante FR `customer`

    // Fonction
    private function sendFinishedEmail($sendTo, $ticket_id, $username){
        $mailData = [
            'title' => 'Ticket '.$ticket_id.' a été effectué',
            'body' => $username.' a terminé le ticket '.$ticket_id,
            'sub' => 'Vous pouvez dés à présent consulter les notes et les fichiers associés'
        ];

        Mail::to("gf_oukacha@esi.dz")->send(new FinishedEmail($mailData));

        return response()->json([
            "message" => "Email envoyé"
        ]);
    }

    public function finishTicket(Request $request){
        if(Auth::check()){
            if(Auth::user()->type == 'agent'){
                $user = Auth::user();
                if($request->id_ticket){
                    $ticket = Ticket::find($request->id_ticket);
                    if($ticket->user_id != $user->id){
                        return response()->json(["message"=>"Vous n'avez pas le droit de terminer ce ticket"],403);
                    }
                    if($ticket && $ticket->etat=='todo'){
                        $ticket->etat = 'done';
                        $fichier = '';
                        if ($request->fichier) {
                            $name_file = uniqid() . '.' . $request->file("fichier")->getClientOriginalExtension();
                            $request->file("fichier")->storeAs(
                                'public',
                                $name_file
                            );
                            //$path = Storage::putFile($name_file, $request->fichier);
                            $fichier = $name_file;
                        }
                        $message = Message::create([
                            'id_ticket'=>$ticket->id,
                            'fichier'=>$fichier,
                            'notes'=>$request->notes,
                            'send_to'=>$ticket->createdBy
                        ]);
                        if($ticket->save() && $message){
                            $tickets = Ticket::where('user_id',$user->id)->orderBy('created_at','desc')->get();
                            foreach($tickets as $t){
                                $agent = User::find($t->user_id);
                                $customer = User::find($t->createdBy);
                                $t->agent = $agent;
                                $t->customer = $customer;
                            }
                            $username = $user->name.' '.$user->lastname;
                            $this->sendFinishedEmail($ticket->createdBy, $ticket->id, $username);
                            return response()->json([
                                "message"=>"Tâche effectué avec succès. Notification envoyée.",
                                "tickets"=>$tickets,
                                "file"=>$fichier,
                                ],200);
                        }else{
                            return response()->json(["message"=>"Erreur inconnue, veuillez réessayer"],500);
                        }
                    }else{
                        return response()->json(["message"=>"Ticket inexistant ou déjà effectué"],401);
                    }
                }

            }else{
                return response()->json(["message"=>"Vous n'avez pas le droit de terminer un ticket"],403);
            }
        }else{
            return response()->json(["message"=>"Vous n'êtes pas connecté"],403);
        }

    }


    // Marquer un message comme lu

    public function readMessage(Request $request){
        if(Auth::check()){
            if(Auth::user()->type == 'customer'){
                if($request->id_message){
                    $message = Message::find($request->id_message);
                    $message->read = true;
                    if($message->save()){
                        return response()->json(["message"=>"Message lu"],200);
                    }
                }
            }else{
                return response()->json(["message"=>"Vous n'avez pas de messages"],403);
            }
        }else{
            return response()->json(["message"=>"Vous n'êtes pas connecté"],403);
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
    }
}
