<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Ticket;

class MessageController extends Controller
{

    // List of messages starting with the latest
    // Only assistante FR have messages
    public function getMyMessages(){
        if(Auth::check()){
            if(Auth::user()->type == 'customer'){
                $messages = Message::where('send_to',Auth::user()->id)->orderBy("created_at","asc")->get();
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

    public function finishTicket(Request $request){
        if(Auth::check()){
            if(Auth::user()->type == 'agent'){
                // $validator = Validator::make($request->all(), [
                //     'fichier' => 'mimes:zip|max:5000',
                //     'notes' => 'required|string',
                // ]);

                // if($validator->fails()){
                //     return response(['errors'=>$validator->errors()->all()], 422);
                // }
                if($request->id_ticket){
                    $ticket = Ticket::find($request->id_ticket);
                    if($ticket->user_id != Auth::user()->id){
                        return response()->json(["message"=>"Vous n'avez pas le droit de terminer ce ticket"],403);
                    }
                    if($ticket){
                        $ticket->etat = 'done';
                        $fichier = '';
                        if ($request->hasFile('fichier')) {
                            $name_file = uniqid() . '.' . $request->file("fichier")->getClientOriginalExtension();
                            $request->file("fichier")->storeAs(
                                'public',
                                $name_file
                            );
                            $fichier = $name_file;
                        }
                        $message = Message::create([
                            'id_ticket'=>$ticket->id,
                            'fichier'=>$fichier,
                            'notes'=>$request->notes,
                            'send_to'=>$ticket->createdBy
                        ]);
                        if($ticket->save() && $message){
                            return response()->json(["message"=>"Tâche effectué avec succès. Notification envoyée."],200);
                        }else{
                            return response()->json(["message"=>"Erreur inconnue, veuillez réessayer"],500);
                        }
                    }else{
                        return response()->json([],401);
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
