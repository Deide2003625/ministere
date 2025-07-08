@extends('layouts.mail')
@section('mailContent')
    <div class="">
        <hr>
        <p>Votre demande d'enregistrement a été traitée et acceptée.</p>
        <hr>
        <a href = "http://localhost:8000/storage/{{$request->admin_receipt_path}}" download>Récépissé d'approbation</a>
    </div>
@endsection
