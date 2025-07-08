@extends('layouts.mail')
@section('mailContent')
    <div class="">
        <hr>
        <p>Veuillez ignorer ce mail s'il n'est pas opportun.</p>
        {{-- <p>Votre code de reinitialisation est :</p>
        <div style = "font-style : bold; color : green">{{$request->recorvering_password}}</div> --}}
        <a href="{{$request->link}}/{{$request->token}}">
            <button class="btn btn-primary btn-lg">Réinitialiser mon mot de passe</button>
        </a>
        <hr>
    </div>