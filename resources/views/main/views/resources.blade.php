@section('title', $arrData->title??config('app.name'))
@section('description', $arrData->description??'')
@section('keywords', $arrData->keywords??'')
@extends('main.layouts.master')

@section('contents')
    <vue-head-content title="{{ $arrData->title??'Ресурсы' }}"></vue-head-content>
    <vue-file-preview></vue-file-preview>
@endsection
