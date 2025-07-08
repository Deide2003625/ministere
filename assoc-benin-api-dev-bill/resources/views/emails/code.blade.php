@extends('layouts.mail')
@section('mailContent')
    <div class="">
        <hr>
        @if ($request->identify_route == "create_association")
            <p>Votre demande a été bien enregistrée.</p>
            <p>Le code unique d'identification et de suivi de votre demande est :</p>
            <div>{{$request->request_code}}</div>
        @else
            <p>Votre demande a été bien modifiée.</p>
            <p>Le code unique d'identification et de suivi de votre demande est :</p>
            <div>{{$request->code_requete}}</div>
        @endif
        <hr>
    </div>
@endsection
