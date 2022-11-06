<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Moving Out</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
</head>

<body class="antialiased">

<div id="app">
</div>
<div id="search">
    <properties-search properties="{{ json_encode($properties) }}" heading="{{ $heading }}" type="{{ $request->type }}" request="{{ $request }}"></properties-search>
</div>


<script defer src="{{ asset('js/app.js') }}"></script>
</body>

</html>


