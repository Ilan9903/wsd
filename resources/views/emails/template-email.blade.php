@extends('layouts.emails')

    @section('body')
        @if(isset($greetingClient))
            @include('layouts.notifications.client-created')
        @endif

        @if(isset($greetingUser))
            @include('layouts.notifications.user-created')
        @endif

        @if(isset($greetingGroup))
            @include('layouts.notifications.inform-user-of-group-assignement')
        @endif

        @if(isset($greetingLink))
            @include('layouts.notifications.inform-user-for-link-download')
        @endif

{{--        @if($link === true)--}}
{{--            @include('layouts.notifications.link-created')--}}
{{--        @endif--}}
    @endsection

    @section('footer')

        <style>
            a {
                color: #E94E3C;
                text-decoration: none;
            }
            a:hover {
                color: #b93525;
            }
        </style>

        @if(isset($greetingClient))
            <span>
                © {{ now()->year }} <a href="{{ $lineLoginClient }}">WESEND</a>. All rights reserved.
            </span>
        @endif

        @if(isset($greetingUser))
            <span>
                © {{ now()->year }} <a href="{{ $lineDomainUser }}">WESEND</a>. All rights reserved.
            </span>
        @endif

        @if(isset($greetingGroup))
            <span>
                © {{ now()->year }} <a href="{{ $lineLoginClient }}">WESEND</a>. All rights reserved.
            </span>
        @endif

{{--        @if($link === true)--}}
{{--            <span>--}}
{{--                © {{ now()->year }} <a href="{{ $lineLoginClient }}">WESEND</a>. All rights reserved.--}}
{{--            </span>--}}
{{--        @endif--}}
    @endsection
