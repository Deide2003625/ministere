@extends('layouts.mail')
@section('mailContent')
    <div class="">
        <hr>
        <p>Votre demande d'enregistrement a été rejetée. Veuillez tenir compte
            des observations ci-dessous pour revoir la soumission sur le site.</p>
        <hr>
        <p>Code : {{$request->request_code}}</p>
        @if (! empty($request->concerned_observations))
            <h5>Observation·s : </h5>
            @foreach ($request->concerned_observations as $o)
                <ul>
                    <li>{{$o}}</li>
                </ul>
            @endforeach
        @endif
        
    </div>
@endsection
