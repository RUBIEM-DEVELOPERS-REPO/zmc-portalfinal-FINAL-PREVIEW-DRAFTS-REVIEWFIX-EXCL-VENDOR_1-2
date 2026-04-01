<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'ZMC Staff Portal')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Session Management -->
    @auth
    <meta name="user-authenticated" content="true">
    <meta name="session-lifetime" content="{{ config('session.lifetime') * 60 }}">
    @endauth

    {{-- Bootstrap 5 (if you already load via Vite/app, remove these 2 lines) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Icons (optional) --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root{
            --zmc-green:#388e3c;
            --zmc-green-dark:#2e7d32;
        }
        .zmc-topbar{
            background: url('{{ asset("zmc_building.png") }}') center center / cover no-repeat;
            position: relative;
        }
        .zmc-topbar::before{
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(90deg, rgba(46, 125, 50, 0.82), rgba(27, 94, 32, 0.85));
            z-index: 0;
        }
        .zmc-topbar > *{
            position: relative;
            z-index: 1;
        }
        .zmc-badge{
            background: rgba(255,255,255,.18);
            border: 1px solid rgba(255,255,255,.25);
            color:#fff;
        }
        .zmc-btn-outline{
            border-color: rgba(255,255,255,.5);
            color:#fff;
        }
        .zmc-btn-outline:hover{
            background: rgba(255,255,255,.12);
            border-color:#fff;
            color:#fff;
        }
        body{ 
            background: url('{{ asset("zmc_building.png") }}') no-repeat center center fixed !important;
            background-size: cover !important;
        }
        body::before{
            content: "";
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, rgba(240, 247, 240, 0.88) 0%, rgba(220, 237, 220, 0.92) 100%);
            z-index: -1;
        }
    </style>

    @stack('head')
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark zmc-topbar">
    <div class="container-fluid px-3">
        <span class="navbar-brand fw-bold d-flex align-items-center gap-2">
            <img src="{{ asset('zmc_logo.png') }}" alt="ZMC" style="height:48px">
            ZMC Staff Portal
        </span>

        <div class="d-flex align-items-center gap-2">
            @if(session('active_staff_role'))
                <span class="badge rounded-pill zmc-badge text-uppercase">
                    {{ str_replace('_',' ', session('active_staff_role')) }}
                </span>
            @endif

            <span class="text-white small d-none d-md-inline">
                {{ auth()->user()->name ?? auth()->user()->email }}
            </span>

            {{-- Switch role goes back to /staff landing --}}
            <a href="{{ route('staff.entry') }}" class="btn btn-sm zmc-btn-outline">
                <i class="ri-shuffle-line"></i> Switch Role
            </a>

            {{-- Staff logout --}}
            <form method="POST" action="{{ route('staff.logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-light">
                    <i class="ri-logout-box-r-line"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<main class="container-fluid py-3 px-3">
    @yield('content')
</main>

<script>
(function(){
  var p='_auth_token', u=new URLSearchParams(window.location.search), t=u.get(p);
  if(t) localStorage.setItem(p,t);
  var s=localStorage.getItem(p);
  if(!s) return;
  document.addEventListener('click',function(e){
    var a=e.target.closest('a');
    if(!a||!a.href) return;
    try{
      var l=new URL(a.href);
      if(l.origin===window.location.origin && !l.searchParams.has(p)){
        l.searchParams.set(p,s);
        a.href=l.toString();
      }
    }catch(x){}
  },true);
  document.addEventListener('submit',function(e){
    var f=e.target;
    if(f.tagName==='FORM' && !f.querySelector('input[name="'+p+'"]')){
      var i=document.createElement('input');
      i.type='hidden'; i.name=p; i.value=s;
      f.appendChild(i);
    }
  },true);
  var origFetch=window.fetch;
  window.fetch=function(url,opts){
    if(typeof url==='string'){
      try{
        var u2=new URL(url,window.location.origin);
        if((u2.origin===window.location.origin||url.startsWith('/'))&&!u2.searchParams.has(p)){
          u2.searchParams.set(p,s);
          url=u2.toString();
        }
      }catch(x){
        if(url.startsWith('/')){var sep=url.indexOf('?')>=0?'&':'?';url+=sep+p+'='+s;}
      }
    }
    return origFetch.call(this,url,opts);
  };
  var origOpen=XMLHttpRequest.prototype.open;
  XMLHttpRequest.prototype.open=function(method,url){
    if(typeof url==='string'){
      try{
        var u3=new URL(url,window.location.origin);
        if((u3.origin===window.location.origin||url.startsWith('/'))&&!u3.searchParams.has(p)){
          u3.searchParams.set(p,s);
          url=u3.toString();
        }
      }catch(x){
        if(url.startsWith('/')){var sep=url.indexOf('?')>=0?'&':'?';url+=sep+p+'='+s;}
      }
    }
    var args=Array.prototype.slice.call(arguments);args[1]=url;
      return origOpen.apply(this,args);
  };
})();
</script>

<!-- Session Timeout Management -->
@auth
<script src="{{ asset('js/session-timeout.js') }}"></script>
<script src="{{ asset('js/session-indicator.js') }}"></script>
@endauth

</body>
</html>
