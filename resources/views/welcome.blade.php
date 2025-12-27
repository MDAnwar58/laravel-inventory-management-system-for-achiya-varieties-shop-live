@extends('layouts.app')

@section('content')
<x-userend.hero-section :landing_page="$landing_page" />

<x-userend.welcome.features :landing_page="$landing_page" :features="$features" />

<x-userend.welcome.stats :landing_page="$landing_page" :custmers_count="$custmers_count" />

<x-userend.welcome.contact :landing_page="$landing_page" :contactInfos="$contactInfos" />
@endsection
