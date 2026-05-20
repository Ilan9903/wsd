<style>
    @import url('https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap');

    .wesend-btn {
        font-family: 'Nunito', sans-serif;
        font-weight: bold;
        background-color: #e94e3c;
        border-radius: 5px;
        padding: 8px;
        padding-inline: 20px;
        color: #fff;
        border: none;
        box-shadow: rgba(233, 78, 60, 0.5) 0px 2px 10px,
        rgba(0, 0, 0, 0.25) 0px 2px 4px;
        transform: translateY(0);
        margin-top: 3px;
    }

    .wesend-btn:hover {
        transform: translateY(-1px);
        cursor: pointer;
    }

    .domainLink {
        font-family: 'Nunito', sans-serif;
        font-weight: bold;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<h2>{{ $greetingUser }}</h2>
<p>{{ $intro1User }}</p>
<p>{{ $intro2User }}</p>
<ul>
    <li>{{ $lineEmailUser }}</li>
    <li>{{ $linePhoneUser }}</li>
</ul>
<p>{{ $lineActionDomainUser }}</p>
<a class="domainLink" href="{{ $lineDomainUser }}"><button class="wesend-btn" role="button">Accedez à mon Espace Client</button></a>
<br>
<p>{{ $lineQuestionsUser }}</p>
<p><i>{{ $salutationUser }}</i></p>
