@extends('layouts.mail')
@section('mailContent')
    <div class="">
        <h3>Données d'enregistrement</h3>
        <table>
            <tbody>
                <tr>
                    <th> Matricule </th><th> : </th><th> {{$request->matricule}} </th>
                </tr>
                <tr>
                    <th> Nom et Prénom (s) </th><th> : </th><th> {{$request->surname}} {{$request->firstname}} </th>
                </tr>
                <tr>
                    <th> Numéro de Téléphone </th><th> : </th><th> {{$request->u_telephone}} </th>
                </tr>
                <tr>
                    <th> Adresse mail </th><th> : </th><th> {{$request->u_email}} </th>
                </tr>
                <tr>
                    <th> Mot de Passe </th><th> : </th><th> {{$request->password}} </th>
                </tr>
                <tr>
                    <th> Rôle </th><th> : </th><th> {{$request->role}} </th>
                </tr>
            </tbody>
        </table>
        NB : Pour la sécurité de vos opérations professionnelles, veuillez modifier votre mot de passe dès la première connexion.
    </div>
@endsection
