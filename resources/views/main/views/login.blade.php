@section('title', $arrData->title??config('app.name'))
@section('description', $arrData->description??'')
@section('keywords', $arrData->keywords??'')
@extends('main.layouts.master')

@section('contents')
    <vue-auth></vue-auth>
@endsection
