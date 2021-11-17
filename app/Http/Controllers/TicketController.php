<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;
class TicketController extends Controller
{

    // Retourner tout les tickets en les sortant depuis le dernier (latest)
    // La liste des tickets peut être vu par tout les utilisateurs :
    // Admin, assistante DZ, assistante FR

    public function getTickets(){
        if(Auth::check()){
            $tickets = Ticket::orderBy('created_at','desc')->get();
            foreach($tickets as $ticket){
                $agent = User::find($ticket->user_id);
                $customer = User::find($ticket->createdBy);
                $ticket->agent = $agent;
                $ticket->customer = $customer;
            }
            return response()->json(["tickets"=>$tickets],200);
        }else{
            return response()->json(["message"=>"Vous n'êtes pas connecté"],403);
        }
    }

    public function myTickets(){
        if(Auth::check()){
            $tickets = Ticket::where('user_id',Auth::user()->id)->orderBy('created_at','asc')->get();
            return response()->json(["tickets"=>$tickets],200);
        }else{
            return response()->json(["message"=>"Vous n'êtes pas connecté"],403);
        }
    }

    public function finishedTickets(){
        if(Auth::check()){
            $tickets = Ticket::where('user_id',Auth::user()->id)->where('etat','done')->orderBy('created_at','asc')->get();
            return response()->json(["tickets"=>$tickets],200);
        }else{
            return response()->json(["message"=>"Vous n'êtes pas connecté"],403);
        }
    }

    // Création d'un nouveau ticket ne peut être exécutée que par une assistante FR
    // Le titre, la description ainsi que le deadline sont obligatoire
    // L'assistante FR peut ajouter des notes
    // L'état par défaut est "Non effectué" i.e `todo`

    public function create(Request $request){
        if(Auth::check()){
            if(Auth::user()->type == "customer"){
                $validator = Validator::make($request->all(), [
                    'title' => 'required|string|max:300',
                    'description' => 'required|string',
                    'year' => 'required|string',
                    'month' => 'required|string',
                    'day' => 'required|string',
                ]);

                if($validator->fails()){
                    return response(['errors'=>$validator->errors()->all()], 422);
                }
                $request['deadline'] = Carbon::create($request->year, $request->month, $request->day)
                                                ->format('Y/m/d');
                $request['createdBy'] = Auth::user()->id;
                $ticket = Ticket::create($request->toArray());
                if($ticket){
                    $tickets = Ticket::orderBy('created_at','desc')->get();
                    foreach($tickets as $ticket){
                        $agent = User::find($ticket->user_id);
                        $customer = User::find($ticket->createdBy);
                        $ticket->agent = $agent;
                        $ticket->customer = $customer;
                    }
                    return response()->json([
                        "message"=>"Ticket crée avec succès",
                        "tickets"=>$tickets
                    ],200);
                }else{
                    return response()->json([
                        "message"=>"Erreur inconnue, veuillez réessayer"
                    ],500);
                }
            }else{
                return response()->json(["message"=>"Seules les assistantes FR ont le droit de créer un ticket"],403);
            }
        }else{
            return response()->json(["message"=>"Vous n'êtes pas connecté"],403);
        }
    }


    // Prendre un ticket
    // Une opération qui ne peut être faite que par l'assistante DZ connecté
    // i.e assigner un ticket à l'utilisateur connecté

    public function assignTicket(Request $request){
        if(Auth::check()){
            if(Auth::user()->type == 'agent'){
                if($request->id_ticket){
                    $ticket = Ticket::find($request->id_ticket);
					$tickets = Ticket::orderBy('created_at','desc')->get();
					foreach($tickets as $ticket){
                        $agent = User::find($ticket->user_id);
                        $customer = User::find($ticket->createdBy);
                        $ticket->agent = $agent;
                        $ticket->customer = $customer;
                    }
					if($ticket->user_id){
	                    $message = "Ce ticket est déjà pris";
						return response()
									->json([
										"message"=>$message,
										"id_ticket"=>$ticket->id,
										"tickets"=>$tickets
									],200);
					}else{
						$ticket->user_id = Auth::user()->id;
						$message = "Le ticket est maintenat à vous de traiter";
						if($ticket->save()){
							return response()
									->json([
										"message"=>$message,
										"id_ticket"=>$ticket->id,
										"tickets"=>$tickets
									],200);
						}else{
							return response()->json(["message"=>"Erreur inconnue, veuillez réessayer"],500);
						}
					}


                }else{
                    return response()->json(["message"=>"Veuillez choisir un ticket à prendre"],401);
                }

            }else{
                return response()->json(["message"=>"Vous n'êtes pas une assistante DZ"],403);
            }
        }else{
            return response()->json(["message"=>"Vous n'êtes pas connecté"],403);
        }
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
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function show(Ticket $ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}
